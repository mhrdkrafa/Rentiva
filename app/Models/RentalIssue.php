<?php

namespace App\Models;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'tenant_id',
        'title',
        'description',
        'priority',
        'status',
        'photos',
        'owner_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => IssuePriority::class,
            'status' => IssueStatus::class,
            'photos' => 'array',
            'resolved_at' => 'datetime',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }
}
