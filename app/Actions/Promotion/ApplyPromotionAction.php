<?php

namespace App\Actions\Promotion;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\User;
use App\Services\Promotion\PromotionDiscountCalculator;
use Illuminate\Support\Facades\DB;

class ApplyPromotionAction
{
    public function __construct(
        protected PromotionDiscountCalculator $calculator
    ) {}

    public function execute(Invoice $invoice, string $promoCode, User $user): Invoice
    {
        return DB::transaction(function () use ($invoice, $promoCode, $user) {
            $lockedInvoice = Invoice::where('id', $invoice->id)->lockForUpdate()->firstOrFail();

            $calc = $this->calculator->validateAndCalculate($promoCode, $lockedInvoice->subtotal_amount, $user);
            /** @var Promotion $promotion */
            $promotion = $calc['promotion'];
            $discountAmount = $calc['discount_amount'];

            // Update Invoice discount
            $newTotal = max(0, ($lockedInvoice->subtotal_amount - $discountAmount) + $lockedInvoice->deposit_amount + $lockedInvoice->additional_fees_amount);

            $lockedInvoice->update([
                'discount_amount' => $discountAmount,
                'total_amount' => $newTotal,
            ]);

            // Add or update discount line item
            InvoiceItem::updateOrCreate(
                [
                    'invoice_id' => $lockedInvoice->id,
                    'item_type' => 'discount',
                ],
                [
                    'description' => 'Potongan Promo Voucher (' . $promotion->code . ')',
                    'unit_price' => $discountAmount,
                    'quantity' => 1,
                    'total_amount' => $discountAmount,
                ]
            );

            // Record Promotion Usage
            PromotionUsage::create([
                'promotion_id' => $promotion->id,
                'user_id' => $user->id,
                'invoice_id' => $lockedInvoice->id,
                'booking_request_id' => $lockedInvoice->booking_request_id,
                'discount_amount' => $discountAmount,
                'used_at' => now(),
            ]);

            $promotion->increment('used_count');

            return $lockedInvoice->fresh(['items']);
        });
    }
}
