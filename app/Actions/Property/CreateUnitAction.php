<?php

namespace App\Actions\Property;

use App\Enums\UnitStatus;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;

class CreateUnitAction
{
    public function execute(Property $property, array $data, array $pricePlans = [], array $photos = [], array $facilityIds = []): Unit
    {
        return DB::transaction(function () use ($property, $data, $pricePlans, $photos, $facilityIds) {
            $unit = Unit::create([
                'property_id' => $property->id,
                'room_type_id' => $data['room_type_id'],
                'name' => $data['name'],
                'floor' => $data['floor'] ?? null,
                'size' => $data['size'] ?? null,
                'capacity' => $data['capacity'] ?? 1,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? UnitStatus::AVAILABLE,
                'available_from' => $data['available_from'] ?? now()->toDateString(),
            ]);

            if (! empty($facilityIds)) {
                $unit->facilities()->sync($facilityIds);
            }

            // Save price plans (integer amounts)
            foreach ($pricePlans as $plan) {
                if (! empty($plan['amount']) && (int) $plan['amount'] > 0) {
                    PricePlan::create([
                        'unit_id' => $unit->id,
                        'billing_period' => $plan['billing_period'],
                        'amount' => (int) $plan['amount'],
                        'deposit_amount' => (int) ($plan['deposit_amount'] ?? 0),
                        'is_active' => true,
                    ]);
                }
            }

            // Save photos
            foreach ($photos as $index => $photo) {
                $path = MediaStorage::storePublicImage($photo, 'units');
                UnitImage::create([
                    'unit_id' => $unit->id,
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }

            return $unit;
        });
    }
}
