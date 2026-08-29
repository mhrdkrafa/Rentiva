<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // Allow Tenants and Super Admins
        if ($user->role === UserRole::TENANT || $user->isAdmin()) {
            return $next($request);
        }

        // If Owner tries to access Tenant dashboard, redirect to Owner Dashboard with alert
        if ($user->isOwner()) {
            return redirect()->route('owner.dashboard')
                ->with('info', 'Anda saat ini masuk sebagai Pemilik Kost.');
        }

        abort(403, 'Akses ini khusus untuk akun Penyewa (Tenant).');
    }
}
