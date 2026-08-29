<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'tenant_id',
        'unit_id',
        'price_plan_id',
        'check_in_date',
        'check_out_date',
        'duration_months',
        'duration_unit',
        'base_amount',
        'deposit_amount',
        'additional_fees_amount',
        'total_amount',
        'status',
        'tenant_notes',
        'owner_rejection_reason',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'expired_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'duration_months' => 'integer',
            'base_amount' => 'integer',
            'deposit_amount' => 'integer',
            'additional_fees_amount' => 'integer',
            'total_amount' => 'integer',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'expired_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pricePlan(): BelongsTo
    {
        return $this->belongsTo(PricePlan::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::PENDING_APPROVAL,
            BookingStatus::APPROVED,
            BookingStatus::PAYMENT_PENDING,
            BookingStatus::CONFIRMED,
        ]);
    }

    public function scopeBlocksDates(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::APPROVED,
            BookingStatus::PAYMENT_PENDING,
            BookingStatus::CONFIRMED,
        ]);
    }

    public function getFormattedBaseAmountAttribute(): string
    {
        return Money::format($this->base_amount);
    }

    public function getFormattedDepositAmountAttribute(): string
    {
        return Money::format($this->deposit_amount);
    }

    public function getFormattedAdditionalFeesAmountAttribute(): string
    {
        return Money::format($this->additional_fees_amount);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return Money::format($this->total_amount);
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING_APPROVAL;
    }

    public function isApproved(): bool
    {
        return $this->status === BookingStatus::APPROVED;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }
}
