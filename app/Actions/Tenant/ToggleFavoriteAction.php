<?php

namespace App\Actions\Tenant;

use App\Models\Favorite;
use App\Models\Property;
use App\Models\User;

class ToggleFavoriteAction
{
    /**
     * Toggle property favorite status for user.
     * Returns true if added, false if removed.
     */
    public function execute(User $user, Property $property): bool
    {
        $existing = Favorite::where('user_id', $user->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return false;
        }

        Favorite::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);

        return true;
    }
}
