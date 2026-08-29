<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Booking\CancelBookingRequestAction;
use App\Actions\Booking\CreateBookingRequestAction;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\PricePlan;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $bookings = auth()->user()->bookingRequests()
            ->with(['unit.property.coverImage', 'unit.roomType', 'pricePlan'])
            ->latest()
            ->paginate(10);

        return view('tenant.bookings.index', compact('bookings'));
    }

    public function store(Request $request, CreateBookingRequestAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'price_plan_id' => ['required', 'exists:price_plans,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'tenant_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);
        $pricePlan = PricePlan::findOrFail($validated['price_plan_id']);

        $booking = $action->execute(
            $request->user(),
            $unit,
            $pricePlan,
            $validated['check_in_date'],
            $validated['duration_months'],
            $validated['tenant_notes'] ?? null
        );

        return redirect()->route('tenant.bookings.show', $booking)
            ->with('success', 'Pengajuan sewa Anda berhasil dikirim! Menunggu konfirmasi pemilik kost.');
    }

    public function show(BookingRequest $booking): View
    {
        if (auth()->id() !== $booking->tenant_id && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $booking->load(['unit.property.owner', 'unit.property.coverImage', 'unit.roomType', 'pricePlan']);

        return view('tenant.bookings.show', compact('booking'));
    }

    public function cancel(BookingRequest $booking, CancelBookingRequestAction $action): RedirectResponse
    {
        $action->execute(auth()->user(), $booking);

        return back()->with('success', 'Pengajuan sewa berhasil dibatalkan.');
    }
}
