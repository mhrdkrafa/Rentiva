<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Property\CreatePropertyAction;
use App\Actions\Property\SubmitPropertyForVerificationAction;
use App\Actions\Property\UpdatePropertyAction;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $properties = Property::with(['propertyType', 'location', 'coverImage', 'units'])
            ->where(function ($query) use ($user) {
                if ($user->isAdmin()) {
                    return;
                }
                $query->where('owner_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('owner.properties.index', compact('properties'));
    }

    public function create(): View
    {
        $propertyTypes = PropertyType::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $facilities = Facility::where('is_active', true)->get();

        return view('owner.properties.create', compact('propertyTypes', 'locations', 'facilities'));
    }

    public function store(Request $request, CreatePropertyAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'address' => ['required', 'string'],
            'gender_policy' => ['required', 'in:all,male_only,female_only,married_couples'],
            'description' => ['required', 'string'],
            'public_location_precision' => ['nullable', 'in:exact,approximate,area_only'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $property = $action->execute(
            $request->user(),
            $validated,
            $request->file('photos', []),
            $request->input('facilities', [])
        );

        return redirect()->route('owner.properties.show', $property)
            ->with('success', 'Properti berhasil dibuat sebagai draf! Silakan tambahkan unit/kamar.');
    }

    public function show(Property $property): View
    {
        $this->authorizeOwnerOrManager($property);

        $property->load(['propertyType', 'location', 'facilities', 'images', 'units.roomType', 'units.pricePlans', 'units.images']);

        return view('owner.properties.show', compact('property'));
    }

    public function submitVerification(Property $property, SubmitPropertyForVerificationAction $action): RedirectResponse
    {
        $this->authorizeOwnerOrManager($property);

        $action->execute($property);

        return back()->with('success', 'Pengajuan verifikasi berhasil dikirimkan! Tim kurator kami akan meninjau properti Anda.');
    }

    protected function authorizeOwnerOrManager(Property $property): void
    {
        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->id === $property->owner_id || $user->canManageOwnerProperty($property->owner_id))) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke properti ini.');
    }
}
