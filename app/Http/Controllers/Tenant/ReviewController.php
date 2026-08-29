<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Review\SubmitReviewAction;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request, Rental $rental): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->id === $rental->tenant_id, 403);

        if ($rental->hasReviewed()) {
            return redirect()->route('tenant.rentals.show', $rental)
                ->with('info', 'Anda sudah memberikan ulasan untuk masa sewa kamar ini.');
        }

        $rental->load(['unit.property']);

        return view('tenant.reviews.create', compact('rental'));
    }

    public function store(
        Request $request,
        Rental $rental,
        SubmitReviewAction $submitReviewAction
    ): RedirectResponse {
        $user = $request->user();
        abort_unless($user->id === $rental->tenant_id, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'cleanliness_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'accuracy_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'communication_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'location_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'value_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $submitReviewAction->execute($user, $rental, $validated);

        return redirect()->route('tenant.rentals.show', $rental)
            ->with('success', 'Terima kasih! Ulasan dan penilaian Anda berhasil disimpan.');
    }
}
