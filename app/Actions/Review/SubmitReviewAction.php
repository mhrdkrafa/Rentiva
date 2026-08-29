<?php

namespace App\Actions\Review;

use App\Enums\ReviewModerationStatus;
use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubmitReviewAction
{
    public function execute(User $tenant, Rental $rental, array $data): Review
    {
        return DB::transaction(function () use ($tenant, $rental, $data) {
            // Authorization: Tenant must be the owner of the rental tenancy
            if ($tenant->id !== $rental->tenant_id && ! $tenant->isAdmin()) {
                throw new AuthorizationException('Hanya penyewa resmi kamar ini yang dapat memberikan ulasan.');
            }

            // Check if already reviewed
            if ($rental->hasReviewed()) {
                throw ValidationException::withMessages([
                    'rental' => ['Anda sudah memberikan ulasan untuk masa sewa kamar ini.'],
                ]);
            }

            $overallRating = (int) ($data['rating'] ?? 5);

            return Review::create([
                'rental_id' => $rental->id,
                'property_id' => $rental->unit->property_id,
                'unit_id' => $rental->unit_id,
                'tenant_id' => $tenant->id,
                'rating' => max(1, min(5, $overallRating)),
                'cleanliness_rating' => max(1, min(5, (int) ($data['cleanliness_rating'] ?? $overallRating))),
                'accuracy_rating' => max(1, min(5, (int) ($data['accuracy_rating'] ?? $overallRating))),
                'communication_rating' => max(1, min(5, (int) ($data['communication_rating'] ?? $overallRating))),
                'location_rating' => max(1, min(5, (int) ($data['location_rating'] ?? $overallRating))),
                'value_rating' => max(1, min(5, (int) ($data['value_rating'] ?? $overallRating))),
                'comment' => $data['comment'],
                'moderation_status' => ReviewModerationStatus::APPROVED, // Default approved or pending moderation
            ]);
        });
    }
}
