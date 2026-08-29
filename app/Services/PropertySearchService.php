<?php

namespace App\Services;

use App\Enums\GenderPolicy;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Support\Money;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PropertySearchService
{
    /**
     * Search and filter properties based on user criteria.
     */
    public function search(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Property::query()
            ->where('status', PropertyStatus::PUBLISHED)
            ->where('verification_status', VerificationStatus::VERIFIED)
            ->with(['propertyType', 'location', 'coverImage', 'facilities', 'units.pricePlans']);

        // 1. Keyword Search
        if (! empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%")
                  ->orWhereHas('location', fn ($l) => $l->where('name', 'like', "%{$keyword}%"));
            });
        }

        // 2. Location Filter
        if (! empty($filters['location_id'])) {
            $locationId = (int) $filters['location_id'];
            $query->where(function (Builder $q) use ($locationId) {
                $q->where('location_id', $locationId)
                  ->orWhereHas('location', fn ($l) => $l->where('parent_id', $locationId));
            });
        }

        // 3. Property Type Filter
        if (! empty($filters['types']) && is_array($filters['types'])) {
            $query->whereIn('property_type_id', $filters['types']);
        } elseif (! empty($filters['type_id'])) {
            $query->where('property_type_id', (int) $filters['type_id']);
        }

        // 4. Gender Policy Filter
        if (! empty($filters['gender']) && $filters['gender'] !== 'all') {
            $gender = $filters['gender'];
            $query->where(function (Builder $q) use ($gender) {
                $q->where('gender_policy', $gender)
                  ->orWhere('gender_policy', GenderPolicy::ALL);
            });
        }

        // 5. Price Range Filter (query active unit price plans)
        $minPrice = ! empty($filters['min_price']) ? Money::parse($filters['min_price']) : null;
        $maxPrice = ! empty($filters['max_price']) ? Money::parse($filters['max_price']) : null;

        if ($minPrice !== null && $minPrice > 0) {
            $query->whereHas('units.pricePlans', function (Builder $p) use ($minPrice) {
                $p->where('is_active', true)->where('amount', '>=', $minPrice);
            });
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $query->whereHas('units.pricePlans', function (Builder $p) use ($maxPrice) {
                $p->where('is_active', true)->where('amount', '<=', $maxPrice);
            });
        }

        // 6. Facilities Filter (properties must have all or any requested facilities)
        if (! empty($filters['facilities']) && is_array($filters['facilities'])) {
            $facilityIds = array_filter(array_map('intval', $filters['facilities']));
            if (! empty($facilityIds)) {
                foreach ($facilityIds as $facilityId) {
                    $query->where(function (Builder $q) use ($facilityId) {
                        $q->whereHas('facilities', fn ($f) => $f->where('facilities.id', $facilityId))
                          ->orWhereHas('units.facilities', fn ($uf) => $uf->where('facilities.id', $facilityId));
                    });
                }
            }
        }

        // 7. Availability Filter (only show properties that have ready-to-rent units)
        if (! empty($filters['available_only'])) {
            $query->whereHas('units', function (Builder $u) {
                $u->where('status', UnitStatus::AVAILABLE);
            });
        }

        // 8. Sorting
        $sort = $filters['sort'] ?? 'recommended';
        match ($sort) {
            'price_low' => $query->addSelect(['min_price' => \App\Models\PricePlan::select('amount')
                ->join('units', 'units.id', '=', 'price_plans.unit_id')
                ->whereColumn('units.property_id', 'properties.id')
                ->where('price_plans.is_active', true)
                ->orderBy('amount', 'asc')
                ->limit(1)
            ])->orderBy('min_price', 'asc'),

            'price_high' => $query->addSelect(['max_price' => \App\Models\PricePlan::select('amount')
                ->join('units', 'units.id', '=', 'price_plans.unit_id')
                ->whereColumn('units.property_id', 'properties.id')
                ->where('price_plans.is_active', true)
                ->orderBy('amount', 'desc')
                ->limit(1)
            ])->orderBy('max_price', 'desc'),

            'latest' => $query->latest('published_at')->latest('id'),

            default => $query->orderByDesc('featured')->latest('published_at')->latest('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }
}
