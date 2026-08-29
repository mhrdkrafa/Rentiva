<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Review\ReplyReviewAction;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $reviews = Review::whereHas('property', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })
            ->with(['tenant', 'property', 'unit'])
            ->latest()
            ->paginate(10);

        return view('owner.reviews.index', compact('reviews'));
    }

    public function reply(
        Request $request,
        Review $review,
        ReplyReviewAction $replyReviewAction
    ): RedirectResponse {
        $user = $request->user();
        abort_unless($review->property->owner_id === $user->id, 403);

        $request->validate([
            'owner_reply' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $replyReviewAction->execute($user, $review, $request->owner_reply);

        return redirect()->route('owner.reviews.index')
            ->with('success', 'Balasan ulasan berhasil dikirim.');
    }
}
