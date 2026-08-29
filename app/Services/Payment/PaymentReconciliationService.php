<?php

namespace App\Services\Payment;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use Illuminate\Support\Collection;

class PaymentReconciliationService
{
    /**
     * Audit single invoice for integrity (items sum == total, and paid status has valid settlement payment).
     */
    public function auditInvoice(Invoice $invoice): array
    {
        $itemsSum = (int) $invoice->items()->sum('total_amount');
        $expectedTotal = (int) $invoice->total_amount;

        $itemsMatch = ($itemsSum === $expectedTotal);

        $settledPaymentsSum = (int) $invoice->payments()
            ->where('status', PaymentStatus::SETTLEMENT)
            ->sum('amount');

        $isPaid = $invoice->status === InvoiceStatus::PAID;
        $paymentStatusConsistent = (! $isPaid || $settledPaymentsSum >= $expectedTotal);

        return [
            'invoice_id' => $invoice->id,
            'invoice_code' => $invoice->code,
            'total_amount' => $expectedTotal,
            'items_sum' => $itemsSum,
            'settled_payments_sum' => $settledPaymentsSum,
            'is_items_consistent' => $itemsMatch,
            'is_payment_consistent' => $paymentStatusConsistent,
            'has_discrepancy' => (! $itemsMatch || ! $paymentStatusConsistent),
        ];
    }

    /**
     * Audit all invoices and flag discrepancies.
     */
    public function auditAll(): Collection
    {
        return Invoice::with(['items', 'payments'])
            ->get()
            ->map(fn ($invoice) => $this->auditInvoice($invoice))
            ->filter(fn ($res) => $res['has_discrepancy']);
    }
}
