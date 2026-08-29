<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerOrManager
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

        // Allow Owners, Property Managers, and Super Admins
        if ($user->isOwner() || $user->isAdmin() || $user->managerAssignmentsAsManager()->exists()) {
            return $next($request);
        }

        // If Tenant tries to access Owner dashboard, redirect to Tenant Dashboard with alert
        if ($user->isTenant()) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'Akses ditolak. Halaman tersebut khusus untuk Mitra Pemilik Kost (Owner).');
        }

        abort(403, 'Akses ini khusus untuk Mitra Pemilik Kost (Owner).');
    }
}
