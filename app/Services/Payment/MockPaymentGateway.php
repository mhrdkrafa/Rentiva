<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGatewayInterface
{
    protected string $serverKey;

    public function __construct(?string $serverKey = null)
    {
        $this->serverKey = $serverKey ?? config('app.key', 'rentiva-secure-payment-key');
    }

    public function createPaymentIntent(Invoice $invoice, PaymentMethod $method, array $params = []): array
    {
        $reference = 'GW-' . strtoupper(Str::random(12));
        $channel = $params['channel'] ?? match ($method) {
            PaymentMethod::BANK_TRANSFER => 'bca_va',
            PaymentMethod::QRIS => 'qris_gopay',
            PaymentMethod::CREDIT_CARD => 'visa_mastercard',
            PaymentMethod::E_WALLET => 'gopay',
        };

        $instructions = match ($method) {
            PaymentMethod::BANK_TRANSFER => [
                'va_number' => '88012' . str_pad((string) $invoice->id, 8, '0', STR_PAD_LEFT),
                'bank' => strtoupper(explode('_', $channel)[0] ?? 'BCA'),
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
            PaymentMethod::QRIS => [
                'qr_string' => '00020101021226580014ID.LINKAJA.WWW01189360091100222000025204581253033605802ID5910RENTIVA ID6006SLEMAN62070703A016304' . strtoupper(Str::random(4)),
                'expires_at' => now()->addMinutes(30)->toIso8601String(),
            ],
            default => [
                'redirect_url' => url('/payments/mock-checkout/' . $reference),
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        };

        return [
            'gateway' => 'mock_midtrans',
            'gateway_reference' => $reference,
            'amount' => $invoice->total_amount,
            'payment_method' => $method->value,
            'payment_channel' => $channel,
            'status' => 'pending',
            'instructions' => $instructions,
            'signature' => $this->generateSignature($reference, $invoice->total_amount, 'pending'),
        ];
    }

    public function verifyWebhookSignature(string $signature, string $payload): bool
    {
        $data = json_decode($payload, true);
        if (! $data || empty($data['order_id']) || empty($data['gross_amount']) || empty($data['status_code'])) {
            return false;
        }

        $expectedSignature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $this->serverKey);

        return hash_equals($expectedSignature, $signature);
    }

    public function generateWebhookSignature(string $orderId, string $statusCode, string|int $grossAmount): string
    {
        return hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
    }

    public function parseWebhookPayload(array $payload): array
    {
        return [
            'order_id' => $payload['order_id'] ?? null, // e.g. Payment code or Invoice code
            'transaction_id' => $payload['transaction_id'] ?? $payload['order_id'] ?? null,
            'status' => match ($payload['transaction_status'] ?? null) {
                'capture', 'settlement' => 'settlement',
                'pending' => 'pending',
                'deny', 'cancel', 'expire' => 'expired',
                'refund' => 'refunded',
                default => 'failed',
            },
            'amount' => (int) ($payload['gross_amount'] ?? 0),
            'payment_type' => $payload['payment_type'] ?? 'bank_transfer',
            'raw' => $payload,
        ];
    }

    public function processRefund(Payment $payment, int $amount, string $reason): array
    {
        return [
            'success' => true,
            'refund_reference' => 'REF-GW-' . strtoupper(Str::random(10)),
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'completed',
        ];
    }

    protected function generateSignature(string $reference, int $amount, string $status): string
    {
        return hash('sha512', $reference . $status . $amount . $this->serverKey);
    }
}
