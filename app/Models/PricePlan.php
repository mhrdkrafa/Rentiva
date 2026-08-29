<?php

namespace App\Models;

use App\Enums\BillingPeriod;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'billing_period',
        'amount',
        'deposit_amount',
        'active_from',
        'active_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'billing_period' => BillingPeriod::class,
            'amount' => 'integer',
            'deposit_amount' => 'integer',
            'active_from' => 'date',
            'active_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return Money::format($this->amount);
    }

    public function getFormattedDepositAttribute(): string
    {
        return Money::format($this->deposit_amount);
    }
}
