<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Enums\UnitStatus;
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

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'room_type_id',
        'name',
        'floor',
        'size',
        'capacity',
        'description',
        'status',
        'available_from',
    ];

    protected function casts(): array
    {
        return [
            'status' => UnitStatus::class,
            'capacity' => 'integer',
            'available_from' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UnitImage::class)->orderBy('sort_order');
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'unit_facility');
    }

    public function pricePlans(): HasMany
    {
        return $this->hasMany(PricePlan::class);
    }

    public function activePricePlans(): HasMany
    {
        return $this->hasMany(PricePlan::class)->where('is_active', true);
    }

    public function activeMonthlyPricePlan(): HasOne
    {
        return $this->hasOne(PricePlan::class)
            ->where('is_active', true)
            ->where('billing_period', BillingPeriod::MONTHLY);
    }

    public function additionalFees(): HasMany
    {
        return $this->hasMany(AdditionalFee::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', UnitStatus::AVAILABLE);
    }

    public function getCoverImageUrlAttribute(): string
    {
        $firstImage = $this->images->first();
        if ($firstImage) {
            return MediaStorage::publicUrl($firstImage->path);
        }

        return $this->property?->cover_image_url ?? asset('images/placeholders/room.jpg');
    }

    public function getMonthlyPriceAttribute(): ?int
    {
        $plan = $this->pricePlans->where('is_active', true)->where('billing_period', BillingPeriod::MONTHLY)->first();
        if ($plan) {
            return $plan->amount;
        }

        return $this->pricePlans->where('is_active', true)->first()?->amount;
    }

    public function getFormattedMonthlyPriceAttribute(): string
    {
        return Money::format($this->monthly_price);
    }
}
