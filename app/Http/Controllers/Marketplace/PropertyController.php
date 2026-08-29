<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use App\Services\PropertySearchService;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request, PropertySearchService $searchService): View
    {
        $filters = $request->all();
        $properties = $searchService->search($filters, 12);

        $locations = Location::where('is_active', true)->orderBy('name')->get();
        $propertyTypes = PropertyType::where('is_active', true)->get();
        $facilities = Facility::where('is_active', true)->get();

        $seo = SeoService::propertyList($properties->total());

        return view('marketplace.index', compact('properties', 'locations', 'propertyTypes', 'facilities', 'seo', 'filters'));
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

        // Visibility authorization
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
