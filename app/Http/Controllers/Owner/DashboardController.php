<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Models\BookingRequest;
use App\Models\Property;
use App\Models\Rental;
use App\Models\RentalIssue;
use App\Models\Unit;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $propertiesQuery = Property::query();
        if (! $user?->isAdmin() && $user) {
            $propertiesQuery->where('owner_id', $user->id);
        }
        $properties = $propertiesQuery->with(['units', 'coverImage'])->get();

        $propertyIds = $properties->pluck('id')->toArray();
        $totalProperties = $properties->count();

        $unitsQuery = Unit::whereIn('property_id', $propertyIds);
        $totalUnits = $unitsQuery->count();
        $occupiedUnits = (clone $unitsQuery)->where('status', UnitStatus::OCCUPIED)->count();
        $availableUnits = (clone $unitsQuery)->where('status', UnitStatus::AVAILABLE)->count();
        $occupancyRate = $totalUnits > 0 ? (int) round(($occupiedUnits / $totalUnits) * 100) : 0;

        // Active Rentals and Monthly Estimated Revenue
        $activeRentals = Rental::whereHas('unit', fn ($u) => $u->whereIn('property_id', $propertyIds))
            ->where('status', RentalStatus::ACTIVE)
            ->with(['tenant.profile', 'unit.property', 'unit.roomType'])
            ->get();

        $monthlyEstimatedRevenue = (int) $activeRentals->sum('monthly_rent');

        // Pending Bookings requiring owner attention
        $pendingBookings = BookingRequest::whereHas('unit', fn ($u) => $u->whereIn('property_id', $propertyIds))
            ->where('status', BookingStatus::PENDING_APPROVAL)
            ->with(['tenant.profile', 'unit.property', 'unit.roomType', 'pricePlan'])
            ->latest()
            ->limit(5)
            ->get();

        // Active Maintenance Issues
        $pendingIssues = RentalIssue::whereHas('rental.unit', fn ($u) => $u->whereIn('property_id', $propertyIds))
            ->whereNotIn('status', ['resolved', 'closed'])
            ->with(['rental.unit.property', 'tenant'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_properties' => $totalProperties,
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits,
            'available_units' => $availableUnits,
            'occupancy_rate' => $occupancyRate,
            'monthly_revenue' => $monthlyEstimatedRevenue,
            'formatted_monthly_revenue' => Money::format($monthlyEstimatedRevenue),
            'pending_bookings_count' => $pendingBookings->count(),
            'pending_issues_count' => $pendingIssues->count(),
        ];

        return view('owner.dashboard', compact('properties', 'activeRentals', 'pendingBookings', 'pendingIssues', 'stats'));
    }
}
