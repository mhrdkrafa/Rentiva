<?php

namespace App\Actions\Finance;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProcessRefundAction
{
    public function __construct(
        protected PaymentGatewayInterface $gateway
    ) {}

    public function execute(User $initiator, Payment $payment, int $amount, string $reason, ?string $notes = null): Refund
    {
        return DB::transaction(function () use ($initiator, $payment, $amount, $reason, $notes) {
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->firstOrFail();
            $invoice = $lockedPayment->invoice;

            // Authorization: Initiator must be super admin/admin or property owner
            if ($initiator->id !== $invoice->owner_id && ! $initiator->isAdmin()) {
                throw new AuthorizationException('Hanya pemilik properti atau admin yang berhak memproses pengembalian dana.');
            }

            if ($lockedPayment->status !== PaymentStatus::SETTLEMENT) {
                throw ValidationException::withMessages([
                    'payment' => ['Pengembalian dana hanya dapat dilakukan untuk transaksi pembayaran yang telah berhasil (settlement).'],
                ]);
            }

            if ($amount <= 0 || $amount > $lockedPayment->amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Nominal pengembalian dana tidak boleh melebihi jumlah transaksi pembayaran.'],
                ]);
            }

            // Call gateway refund
            $gatewayResult = $this->gateway->processRefund($lockedPayment, $amount, $reason);

            $refundCode = 'REF-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $refund = Refund::create([
                'code' => $refundCode,
                'payment_id' => $lockedPayment->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'reason' => $reason,
                'status' => $gatewayResult['status'] === 'completed' ? RefundStatus::COMPLETED : RefundStatus::PENDING,
                'notes' => $notes,
                'processed_at' => now(),
            ]);

            $lockedPayment->update(['status' => PaymentStatus::REFUNDED]);
            $invoice->update(['status' => InvoiceStatus::REFUNDED]);

            return $refund;
        });
    }
}
