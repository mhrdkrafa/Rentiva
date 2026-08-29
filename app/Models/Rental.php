<?php

namespace App\Models;

use App\Enums\RentalStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'tenant_id',
        'unit_id',
        'booking_request_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'deposit_held',
        'status',
        'check_in_notes',
        'check_out_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => RentalStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'monthly_rent' => 'integer',
            'deposit_held' => 'integer',
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

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(RentalIssue::class)->latest();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [RentalStatus::PENDING_MOVE_IN, RentalStatus::ACTIVE]);
    }

    public function getFormattedMonthlyRentAttribute(): string
    {
        return Money::format($this->monthly_rent);
    }

    public function getFormattedDepositHeldAttribute(): string
    {
        return Money::format($this->deposit_held);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
