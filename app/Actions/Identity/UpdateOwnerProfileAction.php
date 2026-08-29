<?php

namespace App\Actions\Identity;

use App\Models\OwnerProfile;
use App\Models\User;
use App\Models\UserProfile;
use App\Support\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateOwnerProfileAction
{
    public function execute(User $user, array $data, ?UploadedFile $avatar = null): OwnerProfile
    {
        return DB::transaction(function () use ($user, $data, $avatar) {
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'phone' => $data['phone'] ?? $user->phone,
            ]);

            if ($avatar) {
                $userProfile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
                if ($userProfile->avatar_path) {
                    MediaStorage::delete($userProfile->avatar_path);
                }
                $userProfile->avatar_path = MediaStorage::storePublicImage($avatar, 'avatars');
                $userProfile->save();
            }

            $ownerProfile = $user->ownerProfile ?? new OwnerProfile(['user_id' => $user->id]);
            $ownerProfile->company_name = $data['company_name'] ?? $ownerProfile->company_name;
            $ownerProfile->tax_number = $data['tax_number'] ?? $ownerProfile->tax_number;
            $ownerProfile->bank_name = $data['bank_name'] ?? $ownerProfile->bank_name;
            $ownerProfile->bank_account_number = $data['bank_account_number'] ?? $ownerProfile->bank_account_number;
            $ownerProfile->bank_account_holder = $data['bank_account_holder'] ?? $ownerProfile->bank_account_holder;

            $ownerProfile->save();

            return $ownerProfile;
        });
    }
}
