<?php

namespace App\Actions\Property;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;

class UpdatePropertyAction
{
    public function execute(Property $property, array $data, array $newPhotos = [], ?array $facilityIds = null): Property
    {
        return DB::transaction(function () use ($property, $data, $newPhotos, $facilityIds) {
            $property->update([
                'property_type_id' => $data['property_type_id'] ?? $property->property_type_id,
                'location_id' => $data['location_id'] ?? $property->location_id,
                'name' => $data['name'] ?? $property->name,
                'description' => $data['description'] ?? $property->description,
                'address' => $data['address'] ?? $property->address,
                'gender_policy' => $data['gender_policy'] ?? $property->gender_policy,
                'latitude' => $data['latitude'] ?? $property->latitude,
                'longitude' => $data['longitude'] ?? $property->longitude,
                'public_location_precision' => $data['public_location_precision'] ?? $property->public_location_precision,
                'seo_title' => $data['seo_title'] ?? $property->seo_title,
                'seo_description' => $data['seo_description'] ?? $property->seo_description,
            ]);

            if ($facilityIds !== null) {
                $property->facilities()->sync($facilityIds);
            }

            if (! empty($newPhotos)) {
                $lastSort = $property->images()->max('sort_order') ?? -1;
                foreach ($newPhotos as $photo) {
                    $lastSort++;
                    $path = MediaStorage::storePublicImage($photo, 'properties');
                    PropertyImage::create([
                        'property_id' => $property->id,
                        'path' => $path,
                        'sort_order' => $lastSort,
                        'is_cover' => $property->images()->count() === 0 && $lastSort === 0,
                    ]);
                }
            }

            return $property;
        });
    }
}
