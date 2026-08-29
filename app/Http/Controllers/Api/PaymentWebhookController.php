<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentWebhookService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PaymentWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentWebhookService $webhookService): JsonResponse
    {
        $rawPayload = $request->getContent();
        $payload = json_decode($rawPayload, true) ?? [];

        // Midtrans style signature in body or header
        $signature = $request->header('X-Signature') ?? $payload['signature_key'] ?? '';

        try {
            $result = $webhookService->handle($signature, $rawPayload);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error processing webhook: ' . $e->getMessage(),
            ], 500);
        }
    }
}
