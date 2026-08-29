<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Booking\ApproveBookingRequestAction;
use App\Actions\Booking\RejectBookingRequestAction;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $query = BookingRequest::with(['tenant.profile', 'unit.property', 'pricePlan'])
            ->whereHas('unit.property', function ($p) use ($user) {
                if ($user->isAdmin()) {
                    return;
                }
                $p->where('owner_id', $user->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('owner.bookings.index', compact('bookings'));
    }

    public function show(BookingRequest $booking): View
    {
        $this->authorizeOwnerOrManager($booking);

        $booking->load(['tenant.profile', 'unit.property', 'unit.roomType', 'pricePlan']);

        return view('owner.bookings.show', compact('booking'));
    }

    public function approve(BookingRequest $booking, ApproveBookingRequestAction $action): RedirectResponse
    {
        $action->execute(auth()->user(), $booking);

        return back()->with('success', 'Pengajuan sewa berhasil disetujui! Menunggu pembayaran dari penyewa.');
    }

    public function reject(Request $request, BookingRequest $booking, RejectBookingRequestAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $action->execute(auth()->user(), $booking, $validated['reason']);

        return back()->with('success', 'Pengajuan sewa telah ditolak.');
    }

    protected function authorizeOwnerOrManager(BookingRequest $booking): void
    {
        $user = auth()->user();
        $ownerId = $booking->unit->property->owner_id;

        if ($user && ($user->isAdmin() || $user->id === $ownerId || $user->canManageOwnerProperty($ownerId))) {
            return;
        }

        abort(403, 'Akses tidak diizinkan.');
    }
}
