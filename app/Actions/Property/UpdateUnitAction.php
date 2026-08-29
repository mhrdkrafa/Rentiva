<?php

namespace App\Actions\Property;

use App\Models\PricePlan;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;

class UpdateUnitAction
{
    public function execute(Unit $unit, array $data, ?array $pricePlans = null, array $newPhotos = [], ?array $facilityIds = null): Unit
    {
        return DB::transaction(function () use ($unit, $data, $pricePlans, $newPhotos, $facilityIds) {
            $unit->update([
                'room_type_id' => $data['room_type_id'] ?? $unit->room_type_id,
                'name' => $data['name'] ?? $unit->name,
                'floor' => $data['floor'] ?? $unit->floor,
                'size' => $data['size'] ?? $unit->size,
                'capacity' => $data['capacity'] ?? $unit->capacity,
                'description' => $data['description'] ?? $unit->description,
                'status' => $data['status'] ?? $unit->status,
                'available_from' => $data['available_from'] ?? $unit->available_from,
            ]);

            if ($facilityIds !== null) {
                $unit->facilities()->sync($facilityIds);
            }

            if ($pricePlans !== null) {
                // Update / recreate price plans
                $unit->pricePlans()->delete();
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
            }

            if (! empty($newPhotos)) {
                $lastSort = $unit->images()->max('sort_order') ?? -1;
                foreach ($newPhotos as $photo) {
                    $lastSort++;
                    $path = MediaStorage::storePublicImage($photo, 'units');
                    UnitImage::create([
                        'unit_id' => $unit->id,
                        'path' => $path,
                        'sort_order' => $lastSort,
                    ]);
                }
            }

            return $unit;
        });
    }
}
