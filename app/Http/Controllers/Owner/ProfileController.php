<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Identity\UpdateOwnerProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOwnerProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $ownerProfile = $user->ownerProfile;

        return view('owner.profile', compact('user', 'ownerProfile'));
    }

    public function update(UpdateOwnerProfileRequest $request, UpdateOwnerProfileAction $action): RedirectResponse
    {
        $action->execute(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        return back()->with('success', 'Profil pemilik properti dan data rekening berhasil diperbarui.');
    }
}
