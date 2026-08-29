<?php

namespace App\Http\Controllers\Tenant;

use App\Actions\Tenant\ToggleFavoriteAction;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = $request->user()->favoriteProperties()
            ->with(['propertyType', 'location', 'coverImage', 'units.pricePlans'])
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('tenant.favorites.index', compact('favorites'));
    }

    public function toggle(Property $property, ToggleFavoriteAction $action): RedirectResponse
    {
        $added = $action->execute(auth()->user(), $property);
        $message = $added 
            ? 'Properti ditambahkan ke daftar favorit.' 
            : 'Properti dihapus dari daftar favorit.';

        return back()->with('success', $message);
    }
}
