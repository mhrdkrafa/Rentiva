<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar_path',
        'bio',
        'gender',
        'date_of_birth',
        'occupation',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'id_card_number',
        'id_card_path',
        'is_identity_verified',
        'identity_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_identity_verified' => 'boolean',
            'identity_verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
