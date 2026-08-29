<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'booking_request_id',
        'rental_id',
        'tenant_id',
        'owner_id',
        'subtotal_amount',
        'deposit_amount',
        'additional_fees_amount',
        'discount_amount',
        'total_amount',
        'status',
        'due_date',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'subtotal_amount' => 'integer',
            'deposit_amount' => 'integer',
            'additional_fees_amount' => 'integer',
            'discount_amount' => 'integer',
            'total_amount' => 'integer',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::UNPAID);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return Money::format($this->total_amount);
    }

    public function getFormattedSubtotalAmountAttribute(): string
    {
        return Money::format($this->subtotal_amount);
    }

    public function getFormattedDepositAmountAttribute(): string
    {
        return Money::format($this->deposit_amount);
    }

    public function getFormattedAdditionalFeesAmountAttribute(): string
    {
        return Money::format($this->additional_fees_amount);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return Money::format($this->discount_amount);
    }
}
