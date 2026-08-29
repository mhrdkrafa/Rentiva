<?php

use App\Actions\Booking\ApproveBookingRequestAction;
use App\Actions\Finance\CreateInvoiceFromBookingAction;
use App\Actions\Finance\ProcessRefundAction;
use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PropertyStatus;
use App\Enums\RefundStatus;
use App\Enums\RentalStatus;
use App\Enums\UnitStatus;
use App\Enums\VerificationStatus;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PricePlan;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\RoomType;
use App\Models\Unit;
use App\Models\User;
use App\Services\Payment\MockPaymentGateway;
use App\Services\Payment\PaymentIntentService;
use App\Services\Payment\PaymentReconciliationService;
use App\Services\Payment\PaymentWebhookService;

beforeEach(function () {
    $this->owner = User::factory()->owner()->create();
    $this->tenant = User::factory()->tenant()->create();
    $this->otherTenant = User::factory()->tenant()->create();

    $this->location = Location::create(['name' => 'Sleman, Yogyakarta', 'slug' => 'sleman-finance']);
    $this->propertyType = PropertyType::create(['name' => 'Kost Eksklusif', 'slug' => 'kost-eksklusif-finance']);
    $this->roomType = RoomType::create(['name' => 'Deluxe Room', 'slug' => 'deluxe-room-finance']);

    $this->property = Property::create([
        'owner_id' => $this->owner->id,
        'property_type_id' => $this->propertyType->id,
        'location_id' => $this->location->id,
        'name' => 'Kost Eksklusif Kaliurang',
        'slug' => 'kost-eksklusif-kaliurang',
        'description' => 'Kost nyaman dekat kampus',
        'address' => 'Jl. Kaliurang KM 5',
        'verification_status' => VerificationStatus::VERIFIED,
        'status' => PropertyStatus::PUBLISHED,
        'published_at' => now(),
    ]);

    $this->unit = Unit::create([
        'property_id' => $this->property->id,
        'room_type_id' => $this->roomType->id,
        'name' => 'Kamar Deluxe 01',
        'status' => UnitStatus::AVAILABLE,
    ]);

    $this->pricePlan = PricePlan::create([
        'unit_id' => $this->unit->id,
        'duration_type' => 'monthly',
        'amount' => 1500000, // Rp 1.500.000 / month
        'deposit_amount' => 500000,
        'is_active' => true,
    ]);

    $this->booking = BookingRequest::create([
        'code' => 'BOOK-TEST-001',
        'unit_id' => $this->unit->id,
        'tenant_id' => $this->tenant->id,
        'price_plan_id' => $this->pricePlan->id,
        'check_in_date' => now()->addDays(2)->toDateString(),
        'check_out_date' => now()->addDays(2)->addMonths(3)->toDateString(),
        'duration_months' => 3,
        'base_amount' => 4500000,
        'deposit_amount' => 500000,
        'additional_fees_amount' => 50000,
        'total_amount' => 5050000,
        'status' => BookingStatus::PENDING_APPROVAL,
    ]);
});

test('approving a booking automatically generates itemized invoice with integer money', function () {
    $approveAction = app(ApproveBookingRequestAction::class);
    $approvedBooking = $approveAction->execute($this->owner, $this->booking);

    expect($approvedBooking->status)->toBe(BookingStatus::APPROVED);

    $invoice = Invoice::where('booking_request_id', $this->booking->id)->first();

    expect($invoice)->not->toBeNull()
        ->and($invoice->status)->toBe(InvoiceStatus::UNPAID)
        ->and($invoice->total_amount)->toBe(5050000)
        ->and($invoice->subtotal_amount)->toBe(4500000)
        ->and($invoice->deposit_amount)->toBe(500000)
        ->and($invoice->additional_fees_amount)->toBe(50000)
        ->and($invoice->items()->count())->toBe(3);

    // Sum of items equals invoice total
    expect((int) $invoice->items()->sum('total_amount'))->toBe($invoice->total_amount);
});

test('payment intent service creates pending payment with gateway instructions', function () {
    $createInvoiceAction = app(CreateInvoiceFromBookingAction::class);
    $invoice = $createInvoiceAction->execute($this->booking);

    $intentService = app(PaymentIntentService::class);
    $payment = $intentService->createIntent($invoice, PaymentMethod::BANK_TRANSFER, ['channel' => 'bca_va']);

    expect($payment->status)->toBe(PaymentStatus::PENDING)
        ->and($payment->amount)->toBe($invoice->total_amount)
        ->and($payment->payment_method)->toBe(PaymentMethod::BANK_TRANSFER)
        ->and($payment->gateway_reference)->not->toBeNull()
        ->and($payment->gateway_payload)->toHaveKey('instructions');
});

test('payment webhook verifies signature and settles payment idempotently', function () {
    $createInvoiceAction = app(CreateInvoiceFromBookingAction::class);
    $invoice = $createInvoiceAction->execute($this->booking);

    $intentService = app(PaymentIntentService::class);
    $payment = $intentService->createIntent($invoice, PaymentMethod::BANK_TRANSFER);

    $gateway = app(MockPaymentGateway::class);
    $webhookService = app(PaymentWebhookService::class);

    // 1. Invalid signature should throw exception
    $invalidPayload = json_encode([
        'order_id' => $payment->code,
        'status_code' => '200',
        'gross_amount' => (string) $payment->amount,
        'transaction_status' => 'settlement',
    ]);

    expect(fn () => $webhookService->handle('invalid_signature', $invalidPayload))
        ->toThrow(InvalidArgumentException::class);

    // 2. Valid signature settlement
    $validSignature = $gateway->generateWebhookSignature($payment->code, '200', $payment->amount);
    $validPayload = json_encode([
        'order_id' => $payment->code,
        'status_code' => '200',
        'gross_amount' => (string) $payment->amount,
        'transaction_status' => 'settlement',
    ]);

    $result = $webhookService->handle($validSignature, $validPayload);

    expect($result['status'])->toBe('success');

    $invoice->refresh();
    $payment->refresh();
    $this->unit->refresh();

    expect($invoice->status)->toBe(InvoiceStatus::PAID)
        ->and($payment->status)->toBe(PaymentStatus::SETTLEMENT)
        ->and($payment->paid_at)->not->toBeNull()
        ->and($invoice->rental_id)->not->toBeNull()
        ->and($this->unit->status)->toBe(UnitStatus::OCCUPIED);

    // 3. Idempotency test: Re-sending identical webhook returns already_processed without duplicating rental
    $duplicateResult = $webhookService->handle($validSignature, $validPayload);
    expect($duplicateResult['status'])->toBe('already_processed');
});

test('refund processing updates payment and invoice to refunded status', function () {
    $createInvoiceAction = app(CreateInvoiceFromBookingAction::class);
    $invoice = $createInvoiceAction->execute($this->booking);

    $intentService = app(PaymentIntentService::class);
    $payment = $intentService->createIntent($invoice, PaymentMethod::BANK_TRANSFER);

    // Mock settlement
    $payment->update(['status' => PaymentStatus::SETTLEMENT, 'paid_at' => now()]);
    $invoice->update(['status' => InvoiceStatus::PAID, 'paid_at' => now()]);

    $refundAction = app(ProcessRefundAction::class);
    $refund = $refundAction->execute($this->owner, $payment, $payment->amount, 'Kamar batal dihuni karena force majeure');

    expect($refund->status)->toBe(RefundStatus::COMPLETED)
        ->and($refund->amount)->toBe($payment->amount);

    $payment->refresh();
    $invoice->refresh();

    expect($payment->status)->toBe(PaymentStatus::REFUNDED)
        ->and($invoice->status)->toBe(InvoiceStatus::REFUNDED);
});

test('payment reconciliation service detects ledger discrepancies', function () {
    $createInvoiceAction = app(CreateInvoiceFromBookingAction::class);
    $invoice = $createInvoiceAction->execute($this->booking);

    $reconciliationService = app(PaymentReconciliationService::class);
    $audit = $reconciliationService->auditInvoice($invoice);

    // Consistent when unpaid and items sum matches
    expect($audit['has_discrepancy'])->toBeFalse();

    // Intentionally create discrepancy
    $invoice->items()->first()->update(['total_amount' => 100]);
    $discrepancyAudit = $reconciliationService->auditInvoice($invoice);

    expect($discrepancyAudit['has_discrepancy'])->toBeTrue()
        ->and($discrepancyAudit['is_items_consistent'])->toBeFalse();
});

test('tenant can only access own invoices', function () {
    $createInvoiceAction = app(CreateInvoiceFromBookingAction::class);
    $invoice = $createInvoiceAction->execute($this->booking);

    // Owner and tenant can view
    $this->actingAs($this->tenant)
        ->get(route('tenant.invoices.show', $invoice))
        ->assertOk()
        ->assertSee($invoice->code);

    // Other tenant cannot view
    $this->actingAs($this->otherTenant)
        ->get(route('tenant.invoices.show', $invoice))
        ->assertForbidden();
});
