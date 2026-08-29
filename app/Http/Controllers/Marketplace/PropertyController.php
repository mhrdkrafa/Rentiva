<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::query()
            ->where('status', PropertyStatus::PUBLISHED)
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->with(['propertyType', 'location', 'coverImage', 'facilities', 'units.pricePlans']);

        // Filter by location / search query
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        } elseif ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$q}%"));
            });
        }

        // Filter by property type
        if ($request->filled('type_id')) {
            $query->where('property_type_id', $request->type_id);
        }

        // Filter by gender policy
        if ($request->filled('gender') && $request->gender !== 'all') {
            $query->where(function ($g) use ($request) {
                $g->where('gender_policy', $request->gender)
                  ->orWhere('gender_policy', GenderPolicy::ALL);
            });
        }

        // Sort order
        match ($request->get('sort', 'latest')) {
            'price_low' => $query->orderBy('id', 'asc'), // will be enhanced with raw min price query
            'featured' => $query->orderByDesc('featured')->latest(),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $propertyTypes = PropertyType::where('is_active', true)->get();
        $facilities = Facility::where('is_active', true)->get();

        $seo = SeoService::propertyList($properties->total());

        return view('marketplace.index', compact('properties', 'locations', 'propertyTypes', 'facilities', 'seo'));
    }

    public function show(string $slug): View
    {
        $property = Property::where('slug', $slug)
            ->with([
                'owner.profile',
                'propertyType',
                'location',
                'images',
                'facilities',
                'units' => fn ($u) => $u->whereNull('deleted_at'),
                'units.roomType',
                'units.facilities',
                'units.images',
                'units.pricePlans',
                'additionalFees' => fn ($f) => $f->where('is_active', true),
            ])
            ->firstOrFail();

        // Check visibility authorization if not published and verified
        if (! $property->isPublished() || ! $property->isVerified()) {
            $user = auth()->user();
            if (! $user || (! $user->isAdmin() && $user->id !== $property->owner_id && ! $user->canManageOwnerProperty($property->owner_id))) {
                abort(404, 'Properti belum dipublikasikan.');
            }
        }

        $seo = SeoService::propertyDetail(
            title: $property->name . ' — ' . $property->location->name,
            description: $property->description,
            imageUrl: $property->cover_image_url,
            url: route('properties.show', $property->slug),
            minPrice: $property->min_monthly_price ?? 0,
            city: $property->location->name,
            address: $property->public_location_precision === 'exact' ? $property->address : $property->location->name
        );

        $similarProperties = Property::where('status', PropertyStatus::PUBLISHED)
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->where('id', '!=', $property->id)
            ->where('location_id', $property->location_id)
            ->with(['propertyType', 'location', 'coverImage', 'units.pricePlans'])
            ->limit(3)
            ->get();

        return view('marketplace.show', compact('property', 'seo', 'similarProperties'));
    }
}
