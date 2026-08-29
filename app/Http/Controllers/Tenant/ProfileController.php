<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Identity\UpdateTenantProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTenantProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('tenant.profile', compact('user', 'profile'));
    }

    public function update(UpdateTenantProfileRequest $request, UpdateTenantProfileAction $action): RedirectResponse
    {
        $action->execute(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        return back()->with('success', 'Profil penyewa Anda berhasil diperbarui.');
    }
}
