<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Booking\CreateAvailabilityBlockAction;
use App\Actions\Booking\DeleteAvailabilityBlockAction;
use App\Http\Controllers\Controller;
use App\Models\AvailabilityBlock;
use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();

        $properties = Property::with(['units.availabilityBlocks', 'units.bookingRequests' => fn ($b) => $b->active()])
            ->where(function ($q) use ($user) {
                if ($user->isAdmin()) {
                    return;
                }
                $q->where('owner_id', $user->id);
            })
            ->get();

        return view('owner.availability.index', compact('properties'));
    }

    public function store(Request $request, CreateAvailabilityBlockAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'unit_id' => ['required', 'exists:units,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'reason' => ['required', 'in:maintenance,reserved,manual_hold'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $unit = Unit::findOrFail($validated['unit_id']);

        $action->execute(
            $request->user(),
            $unit,
            $validated['start_date'],
            $validated['end_date'],
            $validated['reason'],
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Jadwal blok ketersediaan berhasil ditambahkan.');
    }

    public function destroy(AvailabilityBlock $block, DeleteAvailabilityBlockAction $action): RedirectResponse
    {
        $action->execute(auth()->user(), $block);

        return back()->with('success', 'Blok ketersediaan berhasil dihapus.');
    }
}
