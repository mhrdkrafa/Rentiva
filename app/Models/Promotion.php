<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_transaction_amount',
        'starts_at',
        'ends_at',
        'max_uses',
        'used_count',
        'is_active',
        'property_id',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'integer',
            'max_discount_amount' => 'integer',
            'min_transaction_amount' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'max_uses' => 'integer',
            'used_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function isValidForAmount(int $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($amount < $this->min_transaction_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(int $amount): int
    {
        if (! $this->isValidForAmount($amount)) {
            return 0;
        }

        if ($this->discount_type === DiscountType::PERCENTAGE) {
            $discount = (int) round(($amount * $this->discount_value) / 100);
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }

            return $discount;
        }

        // Fixed discount
        return min($amount, $this->discount_value);
    }

    public function getFormattedDiscountLabelAttribute(): string
    {
        if ($this->discount_type === DiscountType::PERCENTAGE) {
            $label = $this->discount_value . '%';
            if ($this->max_discount_amount) {
                $label .= ' (Maks. ' . Money::format($this->max_discount_amount) . ')';
            }

            return $label;
        }

        return Money::format($this->discount_value);
    }
}
