<?php

namespace App\Actions\Review;

use App\Enums\ReviewModerationStatus;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ModerateReviewAction
{
    public function execute(User $moderator, Review $review, ReviewModerationStatus $status): Review
    {
        if (! $moderator->isAdmin()) {
            throw new AuthorizationException('Hanya administrator yang berwenang memoderasi ulasan.');
        }

        $review->update([
            'moderation_status' => $status,
        ]);

        return $review;
    }
}
