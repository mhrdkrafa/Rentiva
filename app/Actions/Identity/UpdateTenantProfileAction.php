<?php

namespace App\Actions\Identity;

use App\Models\User;
use App\Models\UserProfile;
use App\Support\MediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateTenantProfileAction
{
    public function execute(User $user, array $data, ?UploadedFile $avatar = null): UserProfile
    {
        return DB::transaction(function () use ($user, $data, $avatar) {
            // Update base user name & phone
            $user->update([
                'name' => $data['name'] ?? $user->name,
                'phone' => $data['phone'] ?? $user->phone,
            ]);

            $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);

            if ($avatar) {
                if ($profile->avatar_path) {
                    MediaStorage::delete($profile->avatar_path);
                }
                $profile->avatar_path = MediaStorage::storePublicImage($avatar, 'avatars');
            }

            $profile->bio = $data['bio'] ?? $profile->bio;
            $profile->gender = $data['gender'] ?? $profile->gender;
            $profile->date_of_birth = $data['date_of_birth'] ?? $profile->date_of_birth;
            $profile->occupation = $data['occupation'] ?? $profile->occupation;
            $profile->emergency_contact_name = $data['emergency_contact_name'] ?? $profile->emergency_contact_name;
            $profile->emergency_contact_phone = $data['emergency_contact_phone'] ?? $profile->emergency_contact_phone;
            $profile->emergency_contact_relation = $data['emergency_contact_relation'] ?? $profile->emergency_contact_relation;

            $profile->save();

            return $profile;
        });
    }
}
