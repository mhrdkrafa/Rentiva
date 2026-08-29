<?php

namespace App\Contracts;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent with the gateway.
     */
    public function createPaymentIntent(Invoice $invoice, PaymentMethod $method, array $params = []): array;

    /**
     * Cryptographically verify the incoming webhook signature.
     */
    public function verifyWebhookSignature(string $signature, string $payload): bool;

    /**
     * Process gateway webhook payload into normalized array.
     */
    public function parseWebhookPayload(array $payload): array;

    /**
     * Process refund with gateway.
     */
    public function processRefund(Payment $payment, int $amount, string $reason): array;
}
