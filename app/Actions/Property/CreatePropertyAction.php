<?php

namespace App\Actions\Property;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Support\MediaStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePropertyAction
{
    public function execute(User $owner, array $data, array $photos = [], array $facilityIds = []): Property
    {
        return DB::transaction(function () use ($owner, $data, $photos, $facilityIds) {
            $slug = Str::slug($data['name']) . '-' . Str::lower(Str::random(6));

            $property = Property::create([
                'owner_id' => $owner->id,
                'property_type_id' => $data['property_type_id'],
                'location_id' => $data['location_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'address' => $data['address'],
                'gender_policy' => $data['gender_policy'] ?? 'all',
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'public_location_precision' => $data['public_location_precision'] ?? 'approximate',
                'verification_status' => VerificationStatus::UNVERIFIED,
                'status' => PropertyStatus::DRAFT,
                'featured' => false,
                'seo_title' => $data['seo_title'] ?? $data['name'] . ' — Sewa di Rentiva',
                'seo_description' => $data['seo_description'] ?? Str::limit(strip_tags($data['description']), 160),
            ]);

            if (! empty($facilityIds)) {
                $property->facilities()->sync($facilityIds);
            }

            // Save uploaded photos
            foreach ($photos as $index => $photo) {
                $path = MediaStorage::storePublicImage($photo, 'properties');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $path,
                    'sort_order' => $index,
                    'is_cover' => $index === 0,
                ]);
            }

            return $property;
        });
    }
}
