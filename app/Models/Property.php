<?php

namespace App\Models;

use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Support\MediaStorage;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'property_type_id',
        'location_id',
        'name',
        'slug',
        'description',
        'address',
        'gender_policy',
        'latitude',
        'longitude',
        'public_location_precision',
        'verification_status',
        'status',
        'featured',
        'published_at',
        'verified_at',
        'rejection_reason',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'gender_policy' => GenderPolicy::class,
            'verification_status' => VerificationStatus::class,
            'status' => PropertyStatus::class,
            'featured' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'published_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Property $property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->name) . '-' . Str::lower(Str::random(6));
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_cover', true);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'facility_property');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function availableUnits(): HasMany
    {
        return $this->hasMany(Unit::class)->where('status', UnitStatus::AVAILABLE);
    }

    public function additionalFees(): HasMany
    {
        return $this->hasMany(AdditionalFee::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::PUBLISHED);
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', VerificationStatus::VERIFIED);
    }

    public function scopeMarketplaceVisible(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::PUBLISHED)
                     ->where('verification_status', VerificationStatus::VERIFIED);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeForOwner(Builder $query, int $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === VerificationStatus::VERIFIED;
    }

    public function isPublished(): bool
    {
        return $this->status === PropertyStatus::PUBLISHED;
    }

    public function getCoverImageUrlAttribute(): string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();
        if ($cover) {
            return MediaStorage::publicUrl($cover->path);
        }

        return asset('images/placeholders/property.jpg');
    }

    public function getMinMonthlyPriceAttribute(): ?int
    {
        $min = null;
        foreach ($this->units as $unit) {
            $price = $unit->monthly_price;
            if ($price !== null && ($min === null || $price < $min)) {
                $min = $price;
            }
        }
        return $min;
    }

    public function getFormattedMinPriceAttribute(): string
    {
        return Money::format($this->min_monthly_price);
    }

    public function getTotalUnitsCountAttribute(): int
    {
        return $this->units->count();
    }

    public function getAvailableUnitsCountAttribute(): int
    {
        return $this->units->where('status', UnitStatus::AVAILABLE)->count();
    }
}
