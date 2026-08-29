<?php

namespace App\Services\Payment;

use App\Actions\Tenant\CreateRentalTenancyAction;
use App\Contracts\PaymentGatewayInterface;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentWebhookService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected CreateRentalTenancyAction $createRentalAction
    ) {}

    public function handle(string $signature, string $rawPayload): array
    {
        // 1. Verify Cryptographic Signature
        if (! $this->gateway->verifyWebhookSignature($signature, $rawPayload)) {
            throw new InvalidArgumentException('Tanda tangan webhook pembayaran tidak valid.');
        }

        $payload = json_decode($rawPayload, true);
        $parsed = $this->gateway->parseWebhookPayload($payload);

        $orderId = $parsed['order_id'];
        $newStatus = $parsed['status'];

        // 2. Transactional & Idempotent Execution with lockForUpdate
        return DB::transaction(function () use ($orderId, $newStatus, $payload, $parsed) {
            // Find payment by code or reference, or fallback to invoice by code
            $payment = Payment::where('code', $orderId)
                ->orWhere('gateway_reference', $orderId)
                ->lockForUpdate()
                ->first();

            $invoice = null;
            if ($payment) {
                $invoice = Invoice::where('id', $payment->invoice_id)->lockForUpdate()->first();
            } else {
                $invoice = Invoice::where('code', $orderId)->lockForUpdate()->first();
                if ($invoice) {
                    $payment = $invoice->payments()->latest()->lockForUpdate()->first();
                }
            }

            if (! $invoice) {
                throw new InvalidArgumentException('Invoice atau transaksi pembayaran tidak ditemukan.');
            }

            // IDEMPOTENCY CHECK: If already paid/settled, return immediately without re-processing
            if ($invoice->status === InvoiceStatus::PAID && $newStatus === 'settlement') {
                return [
                    'status' => 'already_processed',
                    'message' => 'Invoice sudah berstatus lunas sebelumnya.',
                    'invoice_id' => $invoice->id,
                ];
            }

            // Update Payment Record
            if ($payment) {
                $payment->update([
                    'status' => match ($newStatus) {
                        'settlement' => PaymentStatus::SETTLEMENT,
                        'expired' => PaymentStatus::EXPIRED,
                        'failed' => PaymentStatus::FAILED,
                        'refunded' => PaymentStatus::REFUNDED,
                        default => PaymentStatus::PENDING,
                    },
                    'paid_at' => $newStatus === 'settlement' ? now() : null,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], ['webhook' => $payload]),
                ]);
            }

            // Process Settlement
            if ($newStatus === 'settlement') {
                $invoice->update([
                    'status' => InvoiceStatus::PAID,
                    'paid_at' => now(),
                ]);

                // If invoice is attached to a confirmed/approved booking, create active rental
                if ($invoice->booking_request_id && ! $invoice->rental_id) {
                    $booking = $invoice->bookingRequest;
                    if ($booking) {
                        $rental = $this->createRentalAction->execute($booking);
                        $invoice->update(['rental_id' => $rental->id]);
                    }
                }
            } elseif (in_array($newStatus, ['expired', 'failed'], true)) {
                if ($invoice->status === InvoiceStatus::UNPAID) {
                    $invoice->update([
                        'status' => match ($newStatus) {
                            'expired' => InvoiceStatus::EXPIRED,
                            default => InvoiceStatus::CANCELLED,
                        },
                    ]);
                }
            }

            return [
                'status' => 'success',
                'invoice_status' => $invoice->status->value,
                'invoice_id' => $invoice->id,
            ];
        });
    }
}
