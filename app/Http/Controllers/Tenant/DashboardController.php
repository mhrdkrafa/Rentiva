<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $activeRental = $user?->rentals()
            ->active()
            ->with(['unit.property.coverImage', 'unit.property.owner', 'unit.roomType'])
            ->latest('start_date')
            ->first();

        $recentBookings = $user?->bookingRequests()
            ->with(['unit.property', 'unit.roomType', 'pricePlan'])
            ->latest()
            ->limit(3)
            ->get() ?? collect();

        $favorites = $user?->favoriteProperties()
            ->with(['propertyType', 'location', 'coverImage', 'units.pricePlans'])
            ->limit(4)
            ->get() ?? collect();

        $pendingIssues = $user?->rentalIssues()
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with('rental.unit.property')
            ->latest()
            ->get() ?? collect();

        $stats = [
            'active_rentals_count' => $user?->rentals()->active()->count() ?? 0,
            'pending_bookings_count' => $user?->bookingRequests()->where('status', 'pending_approval')->count() ?? 0,
            'favorites_count' => $user?->favorites()->count() ?? 0,
            'pending_issues_count' => $pendingIssues->count(),
        ];

        return view('tenant.dashboard', compact('activeRental', 'recentBookings', 'favorites', 'pendingIssues', 'stats'));
    }
}
