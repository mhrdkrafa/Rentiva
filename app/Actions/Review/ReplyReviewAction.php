<?php

namespace App\Actions\Review;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class ReplyReviewAction
{
    public function execute(User $owner, Review $review, string $replyText): Review
    {
        $propertyOwnerId = $review->property->owner_id;

        if ($owner->id !== $propertyOwnerId && ! $owner->isAdmin()) {
            throw new AuthorizationException('Hanya pemilik properti yang dapat membalas ulasan ini.');
        }

        if (empty(trim($replyText))) {
            throw ValidationException::withMessages([
                'owner_reply' => ['Teks balasan ulasan tidak boleh kosong.'],
            ]);
        }

        $review->update([
            'owner_reply' => trim($replyText),
            'owner_replied_at' => now(),
        ]);

        return $review;
    }
}
