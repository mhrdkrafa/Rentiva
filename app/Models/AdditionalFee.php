<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'unit_id',
        'name',
        'type',
        'amount',
        'frequency',
        'is_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return Money::format($this->amount);
    }
}
