<?php

namespace App\Http\Controllers\Owner;

use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Rental;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $propertiesQuery = Property::with(['units.pricePlans', 'units.rentals' => fn ($r) => $r->where('status', RentalStatus::ACTIVE)]);
        if (! $user?->isAdmin() && $user) {
            $propertiesQuery->where('owner_id', $user->id);
        }
        $properties = $propertiesQuery->get();

        $propertyBreakdowns = $properties->map(function (Property $property) {
            $totalUnits = $property->units->count();
            $occupiedUnits = $property->units->where('status', UnitStatus::OCCUPIED)->count();
            $occupancy = $totalUnits > 0 ? (int) round(($occupiedUnits / $totalUnits) * 100) : 0;

            $activeRevenue = 0;
            foreach ($property->units as $u) {
                $activeRental = $u->rentals->first();
                if ($activeRental) {
                    $activeRevenue += $activeRental->monthly_rent;
                }
            }

            return [
                'property' => $property,
                'total_units' => $totalUnits,
                'occupied_units' => $occupiedUnits,
                'available_units' => $property->units->where('status', UnitStatus::AVAILABLE)->count(),
                'occupancy_rate' => $occupancy,
                'monthly_revenue' => $activeRevenue,
                'formatted_monthly_revenue' => Money::format($activeRevenue),
            ];
        });

        $totalRevenue = $propertyBreakdowns->sum('monthly_revenue');
        $totalUnits = $propertyBreakdowns->sum('total_units');
        $totalOccupied = $propertyBreakdowns->sum('occupied_units');
        $overallOccupancy = $totalUnits > 0 ? (int) round(($totalOccupied / $totalUnits) * 100) : 0;

        return view('owner.statistics.index', compact('propertyBreakdowns', 'totalRevenue', 'totalUnits', 'totalOccupied', 'overallOccupancy'));
    }
}
