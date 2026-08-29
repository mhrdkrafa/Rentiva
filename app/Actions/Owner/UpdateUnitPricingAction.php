<?php

namespace App\Actions\Owner;

use App\Enums\BillingPeriod;
use App\Models\PricePlan;
use App\Models\Unit;
use App\Models\User;
use App\Support\Money;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class UpdateUnitPricingAction
{
    /**
     * Update unit price plan and optional fees in integer IDR units.
     */
    public function execute(
        User $owner,
        Unit $unit,
        int $monthlyAmount,
        int $depositAmount = 0,
        ?int $dailyAmount = null,
        ?int $weeklyAmount = null,
        ?int $yearlyAmount = null
    ): Unit {
        $ownerId = $unit->property->owner_id;
        if ($owner->id !== $ownerId && ! $owner->canManageOwnerProperty($ownerId, 'manage_pricing') && ! $owner->isAdmin()) {
            throw new AuthorizationException('Anda tidak berhak mengatur harga untuk kamar ini.');
        }

        return DB::transaction(function () use ($unit, $monthlyAmount, $depositAmount, $dailyAmount, $weeklyAmount, $yearlyAmount) {
            // Update / Create Monthly Price Plan
            PricePlan::updateOrCreate(
                [
                    'unit_id' => $unit->id,
                    'billing_period' => BillingPeriod::MONTHLY,
                ],
                [
                    'amount' => $monthlyAmount,
                    'deposit_amount' => $depositAmount,
                    'is_active' => true,
                ]
            );

            // Optional Daily
            if ($dailyAmount !== null && $dailyAmount > 0) {
                PricePlan::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'billing_period' => BillingPeriod::DAILY,
                    ],
                    [
                        'amount' => $dailyAmount,
                        'deposit_amount' => 0,
                        'is_active' => true,
                    ]
                );
            }

            // Optional Weekly
            if ($weeklyAmount !== null && $weeklyAmount > 0) {
                PricePlan::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'billing_period' => BillingPeriod::WEEKLY,
                    ],
                    [
                        'amount' => $weeklyAmount,
                        'deposit_amount' => 0,
                        'is_active' => true,
                    ]
                );
            }

            // Optional Yearly
            if ($yearlyAmount !== null && $yearlyAmount > 0) {
                PricePlan::updateOrCreate(
                    [
                        'unit_id' => $unit->id,
                        'billing_period' => BillingPeriod::YEARLY,
                    ],
                    [
                        'amount' => $yearlyAmount,
                        'deposit_amount' => $depositAmount,
                        'is_active' => true,
                    ]
                );
            }

            return $unit->fresh(['pricePlans']);
        });
    }
}
