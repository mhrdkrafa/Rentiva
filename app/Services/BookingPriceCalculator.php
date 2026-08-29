<?php

namespace App\Services;

use App\Models\PricePlan;
use App\Models\Unit;

class BookingPriceCalculator
{
    /**
     * Calculate server-side pricing breakdown in integer minor units (IDR).
     *
     * @return array{base_amount: int, deposit_amount: int, additional_fees_amount: int, total_amount: int, duration_months: int}
     */
    public function calculate(Unit $unit, PricePlan $pricePlan, int $duration = 1): array
    {
        $duration = max(1, $duration);
        $baseAmount = (int) ($pricePlan->amount * $duration);
        $depositAmount = (int) ($pricePlan->deposit_amount ?? 0);

        // Sum active required additional fees (property level and unit level)
        $propertyFees = $unit->property->additionalFees()
            ->where('is_active', true)
            ->where('is_required', true)
            ->sum('amount');

        $unitFees = $unit->additionalFees()
            ->where('is_active', true)
            ->where('is_required', true)
            ->sum('amount');

        $additionalFeesAmount = (int) ($propertyFees + $unitFees);
        $totalAmount = $baseAmount + $depositAmount + $additionalFeesAmount;

        return [
            'base_amount' => $baseAmount,
            'deposit_amount' => $depositAmount,
            'additional_fees_amount' => $additionalFeesAmount,
            'total_amount' => $totalAmount,
            'duration_months' => $duration,
        ];
    }
}
