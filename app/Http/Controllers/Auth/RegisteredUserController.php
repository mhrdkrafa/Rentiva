<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\OwnerProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $defaultRole = $request->query('role', 'tenant');

        return view('auth.register', compact('defaultRole'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:tenant,owner'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $role = $request->role === 'owner' ? UserRole::OWNER : UserRole::TENANT;

        $user = DB::transaction(function () use ($request, $role) {
            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $role,
                'status' => UserStatus::ACTIVE,
                'password' => Hash::make($request->password),
            ]);

            // Create default profile
            UserProfile::create([
                'user_id' => $newUser->id,
            ]);

            if ($role === UserRole::OWNER) {
                OwnerProfile::create([
                    'user_id' => $newUser->id,
                    'company_name' => $request->name . ' Properties',
                ]);
            }

            return $newUser;
        });

        event(new Registered($user));

        Auth::login($user);

        if ($user->isOwner()) {
            return redirect()->route('owner.dashboard')->with('success', 'Selamat datang di Rentiva! Mulai daftarkan properti kost pertama Anda.');
        }

        return redirect()->route('tenant.dashboard')->with('success', 'Selamat datang di Rentiva! Akun penyewa Anda telah aktif.');
    }
}
