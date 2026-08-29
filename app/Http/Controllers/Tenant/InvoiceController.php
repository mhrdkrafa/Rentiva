<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Payment\PaymentIntentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Invoice::where('tenant_id', $user->id)
            ->with(['items', 'bookingRequest.unit.property', 'payments'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10);

        return view('tenant.invoices.index', compact('invoices'));
    }

    public function show(Request $request, Invoice $invoice): View
    {
        $user = $request->user();
        abort_unless($user->id === $invoice->tenant_id || $user->isAdmin(), 403);

        $invoice->load(['items', 'bookingRequest.unit.property.owner', 'payments', 'rental']);

        return view('tenant.invoices.show', compact('invoice'));
    }

    public function checkout(Request $request, Invoice $invoice): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->id === $invoice->tenant_id, 403);

        if ($invoice->status === InvoiceStatus::PAID) {
            return redirect()->route('tenant.invoices.show', $invoice)
                ->with('info', 'Tagihan ini sudah lunas.');
        }

        $latestPayment = $invoice->payments()->latest()->first();

        return view('tenant.invoices.checkout', compact('invoice', 'latestPayment'));
    }

    public function processCheckout(
        Request $request,
        Invoice $invoice,
        PaymentIntentService $paymentIntentService
    ): RedirectResponse {
        $user = $request->user();
        abort_unless($user->id === $invoice->tenant_id, 403);

        $request->validate([
            'payment_method' => ['required', 'string', 'in:bank_transfer,qris,credit_card,e_wallet'],
            'channel' => ['nullable', 'string'],
        ]);

        $method = PaymentMethod::from($request->payment_method);

        $payment = $paymentIntentService->createIntent($invoice, $method, [
            'channel' => $request->channel,
        ]);

        return redirect()->route('tenant.invoices.checkout', $invoice)
            ->with('success', 'Instruksi pembayaran berhasil dibuat.');
    }
}
