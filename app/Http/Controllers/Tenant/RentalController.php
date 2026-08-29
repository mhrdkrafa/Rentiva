<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function index(Request $request): View
    {
        $rentals = $request->user()->rentals()
            ->with(['unit.property.coverImage', 'unit.property.owner', 'unit.roomType'])
            ->latest('start_date')
            ->paginate(10);

        return view('tenant.rentals.index', compact('rentals'));
    }

    public function show(Rental $rental): View
    {
        if (auth()->id() !== $rental->tenant_id && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $rental->load(['unit.property.owner', 'unit.property.location', 'unit.roomType', 'bookingRequest', 'issues']);

        return view('tenant.rentals.show', compact('rental'));
    }

    public function receipt(Rental $rental): View
    {
        if (auth()->id() !== $rental->tenant_id && ! auth()->user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $rental->load(['tenant.profile', 'unit.property.owner.ownerProfile', 'unit.property.location', 'unit.roomType', 'bookingRequest']);

        return view('tenant.rentals.receipt', compact('rental'));
    }
}
