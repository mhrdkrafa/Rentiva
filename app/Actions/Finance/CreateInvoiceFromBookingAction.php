<?php

namespace App\Actions\Finance;

use App\Enums\InvoiceStatus;
use App\Models\BookingRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateInvoiceFromBookingAction
{
    public function execute(BookingRequest $booking): Invoice
    {
        return DB::transaction(function () use ($booking) {
            // If booking already has an unpaid or paid invoice, return it
            $existing = Invoice::where('booking_request_id', $booking->id)
                ->whereIn('status', [InvoiceStatus::UNPAID, InvoiceStatus::PAID])
                ->first();

            if ($existing) {
                return $existing;
            }

            $code = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            $ownerId = $booking->unit->property->owner_id;

            $invoice = Invoice::create([
                'code' => $code,
                'booking_request_id' => $booking->id,
                'tenant_id' => $booking->tenant_id,
                'owner_id' => $ownerId,
                'subtotal_amount' => $booking->base_amount,
                'deposit_amount' => $booking->deposit_amount,
                'additional_fees_amount' => $booking->additional_fees_amount,
                'discount_amount' => 0,
                'total_amount' => $booking->total_amount,
                'status' => InvoiceStatus::UNPAID,
                'due_date' => now()->addHours(24)->toDateString(),
            ]);

            // 1. Base Rent Item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Sewa Kamar ' . $booking->unit->name . ' (' . $booking->duration_months . ' Bulan)',
                'item_type' => 'rent',
                'unit_price' => $booking->pricePlan->amount,
                'quantity' => $booking->duration_months,
                'total_amount' => $booking->base_amount,
            ]);

            // 2. Deposit Item (if applicable)
            if ($booking->deposit_amount > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Deposit Jaminan Hunian (Dapat Dikembalikan)',
                    'item_type' => 'deposit',
                    'unit_price' => $booking->deposit_amount,
                    'quantity' => 1,
                    'total_amount' => $booking->deposit_amount,
                ]);
            }

            // 3. Additional Fees Item (if applicable)
            if ($booking->additional_fees_amount > 0) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Biaya Tambahan & Layanan Hunian Wajib',
                    'item_type' => 'additional_fee',
                    'unit_price' => $booking->additional_fees_amount,
                    'quantity' => 1,
                    'total_amount' => $booking->additional_fees_amount,
                ]);
            }

            return $invoice->fresh(['items', 'bookingRequest']);
        });
    }
}
