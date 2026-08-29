<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Property\CreateUnitAction;
use App\Actions\Property\UpdateUnitAction;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function create(Property $property): View
    {
        $this->authorizeOwnerOrManager($property);

        $roomTypes = RoomType::all();
        $roomFacilities = Facility::whereIn('type', ['room', 'general'])->where('is_active', true)->get();

        return view('owner.units.create', compact('property', 'roomTypes', 'roomFacilities'));
    }

    public function store(Request $request, Property $property, CreateUnitAction $action): RedirectResponse
    {
        $this->authorizeOwnerOrManager($property);

        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'floor' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:available,reserved,occupied,maintenance,unavailable'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'price_plans' => ['required', 'array'],
            'price_plans.*.billing_period' => ['required', 'in:daily,weekly,monthly,quarterly,semi_annually,yearly'],
            'price_plans.*.amount' => ['required', 'integer', 'min:1'],
            'price_plans.*.deposit_amount' => ['nullable', 'integer', 'min:0'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $action->execute(
            $property,
            $validated,
            $validated['price_plans'] ?? [],
            $request->file('photos', []),
            $request->input('facilities', [])
        );

        return redirect()->route('owner.properties.show', $property)
            ->with('success', 'Unit/kamar berhasil ditambahkan!');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $property = $unit->property;
        $this->authorizeOwnerOrManager($property);

        $unit->delete();

        return back()->with('success', 'Unit/kamar berhasil dihapus.');
    }

    protected function authorizeOwnerOrManager(Property $property): void
    {
        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->id === $property->owner_id || $user->canManageOwnerProperty($property->owner_id))) {
            return;
        }

        abort(403, 'Anda tidak memiliki hak kelola atas properti ini.');
    }
}
