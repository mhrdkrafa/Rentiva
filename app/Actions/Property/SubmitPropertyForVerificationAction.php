<?php

namespace App\Actions\Property;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use Illuminate\Validation\ValidationException;

class SubmitPropertyForVerificationAction
{
    public function execute(Property $property): Property
    {
        if ($property->units()->count() === 0) {
            throw ValidationException::withMessages([
                'units' => ['Tambahkan setidaknya 1 unit / kamar sebelum mengajukan verifikasi properti.'],
            ]);
        }

        if ($property->images()->count() === 0) {
            throw ValidationException::withMessages([
                'images' => ['Unggah setidaknya 1 foto properti sebelum mengajukan verifikasi.'],
            ]);
        }

        $property->update([
            'verification_status' => VerificationStatus::PENDING,
            'status' => PropertyStatus::PENDING_REVIEW,
            'rejection_reason' => null,
        ]);

        return $property;
    }
}
