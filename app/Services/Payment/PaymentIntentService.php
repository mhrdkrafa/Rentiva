<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentIntentService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway
    ) {}

    public function createIntent(Invoice $invoice, PaymentMethod $method, array $params = []): Payment
    {
        $gatewayResult = $this->gateway->createPaymentIntent($invoice, $method, $params);

        $code = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        return Payment::create([
            'code' => $code,
            'invoice_id' => $invoice->id,
            'tenant_id' => $invoice->tenant_id,
            'amount' => $invoice->total_amount,
            'payment_method' => $method,
            'payment_channel' => $gatewayResult['payment_channel'] ?? null,
            'status' => PaymentStatus::PENDING,
            'gateway_reference' => $gatewayResult['gateway_reference'] ?? null,
            'gateway_payload' => $gatewayResult,
        ]);
    }
}
