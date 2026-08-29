<?php

namespace App\Http\Controllers\Owner;

use App\Actions\Owner\UpdateUnitPricingAction;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function edit(Unit $unit): View
    {
        $this->authorizeOwnerOrManager($unit);

        $unit->load(['property', 'roomType', 'pricePlans', 'additionalFees']);

        return view('owner.units.pricing', compact('unit'));
    }

    public function update(Request $request, Unit $unit, UpdateUnitPricingAction $action): RedirectResponse
    {
        $this->authorizeOwnerOrManager($unit);

        $validated = $request->validate([
            'monthly_amount' => ['required', 'numeric', 'min:10000'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'daily_amount' => ['nullable', 'numeric', 'min:0'],
            'weekly_amount' => ['nullable', 'numeric', 'min:0'],
            'yearly_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $action->execute(
            $request->user(),
            $unit,
            (int) $validated['monthly_amount'],
            (int) ($validated['deposit_amount'] ?? 0),
            ! empty($validated['daily_amount']) ? (int) $validated['daily_amount'] : null,
            ! empty($validated['weekly_amount']) ? (int) $validated['weekly_amount'] : null,
            ! empty($validated['yearly_amount']) ? (int) $validated['yearly_amount'] : null
        );

        return redirect()->route('owner.properties.show', $unit->property_id)
            ->with('success', 'Skema tarif harga kamar berhasil diperbarui.');
    }

    protected function authorizeOwnerOrManager(Unit $unit): void
    {
        $user = auth()->user();
        $ownerId = $unit->property->owner_id;

        if ($user && ($user->isAdmin() || $user->id === $ownerId || $user->canManageOwnerProperty($ownerId))) {
            return;
        }

        abort(403, 'Akses tidak diizinkan.');
    }
}
