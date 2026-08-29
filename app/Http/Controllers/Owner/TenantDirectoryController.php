<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Owner\CompleteRentalTenancyAction;
use App\Enums\RentalStatus;
use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantDirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Rental::whereHas('unit.property', function ($p) use ($user) {
            if ($user->isAdmin()) {
                return;
            }
            $p->where('owner_id', $user->id);
        })->with(['tenant.profile', 'unit.property', 'unit.roomType', 'bookingRequest']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', [RentalStatus::ACTIVE, RentalStatus::PENDING_MOVE_IN]);
        }

        $tenants = $query->latest('start_date')->paginate(10)->withQueryString();

        return view('owner.tenants.index', compact('tenants'));
    }

    public function complete(Request $request, Rental $rental, CompleteRentalTenancyAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'check_out_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $action->execute($request->user(), $rental, $validated['check_out_notes'] ?? null);

        return back()->with('success', 'Masa sewa penyewa berhasil diselesaikan. Kamar kini berstatus Siap Huni.');
    }
}
