<?php

namespace App\Models;

use App\Enums\ReviewModerationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'property_id',
        'unit_id',
        'tenant_id',
        'rating',
        'cleanliness_rating',
        'accuracy_rating',
        'communication_rating',
        'location_rating',
        'value_rating',
        'comment',
        'moderation_status',
        'owner_reply',
        'owner_replied_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'accuracy_rating' => 'integer',
            'communication_rating' => 'integer',
            'location_rating' => 'integer',
            'value_rating' => 'integer',
            'moderation_status' => ReviewModerationStatus::class,
            'owner_replied_at' => 'datetime',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', ReviewModerationStatus::APPROVED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('moderation_status', ReviewModerationStatus::PENDING);
    }
}
