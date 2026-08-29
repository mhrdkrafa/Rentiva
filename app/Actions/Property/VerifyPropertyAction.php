<?php

namespace App\Actions\Property;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;

class VerifyPropertyAction
{
    public function approve(Property $property): Property
    {
        $property->update([
            'verification_status' => VerificationStatus::VERIFIED,
            'status' => PropertyStatus::PUBLISHED,
            'verified_at' => now(),
            'published_at' => $property->published_at ?? now(),
            'rejection_reason' => null,
        ]);

        return $property;
    }

    public function reject(Property $property, string $reason): Property
    {
        $property->update([
            'verification_status' => VerificationStatus::REJECTED,
            'status' => PropertyStatus::DRAFT,
            'rejection_reason' => $reason,
        ]);

        return $property;
    }
}
