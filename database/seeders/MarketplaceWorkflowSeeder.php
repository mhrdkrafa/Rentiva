<?php

namespace Database\Seeders;

use App\Actions\Finance\CreateInvoiceFromBookingAction;
use App\Enums\BookingStatus;
use App\Enums\InvoiceStatus;
use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RentalStatus;
use App\Enums\ReviewModerationStatus;
use App\Enums\UnitStatus;
use App\Models\BookingRequest;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Message;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Rental;
use App\Models\RentalIssue;
use App\Models\Review;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketplaceWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = User::where('email', 'tenant@rentiva.test')->first();
        $tenant2 = User::where('email', 'tenant2@rentiva.test')->first();
        $owner = User::where('email', 'owner@rentiva.test')->first();

        if (! $tenant1 || ! $owner) {
            return;
        }

        $property = Property::where('owner_id', $owner->id)->first() ?? Property::first();
        if (! $property) {
            return;
        }

        $unit1 = $property->units()->first();
        $unit2 = $property->units()->skip(1)->first() ?? $unit1;
        $pricePlan1 = $unit1?->pricePlans()->first();
        $pricePlan2 = $unit2?->pricePlans()->first() ?? $pricePlan1;

        if (! $unit1 || ! $pricePlan1) {
            return;
        }

        // 1. Seed Active Rental Tenancy for Tenant 1 (Mahardika Rafa)
        $booking1 = BookingRequest::updateOrCreate(
            ['code' => 'BOOK-2026-001'],
            [
                'unit_id' => $unit1->id,
                'tenant_id' => $tenant1->id,
                'price_plan_id' => $pricePlan1->id,
                'check_in_date' => now()->subMonths(2)->toDateString(),
                'check_out_date' => now()->addMonths(4)->toDateString(),
                'duration_months' => 6,
                'base_amount' => $pricePlan1->amount,
                'deposit_amount' => 500000,
                'additional_fees_amount' => 0,
                'total_amount' => $pricePlan1->amount + 500000,
                'status' => BookingStatus::APPROVED,
                'approved_at' => now()->subMonths(2),
            ]
        );

        $unit1->update(['status' => UnitStatus::OCCUPIED]);

        $invoice1 = Invoice::updateOrCreate(
            ['code' => 'INV-20260701-0001'],
            [
                'booking_request_id' => $booking1->id,
                'tenant_id' => $tenant1->id,
                'owner_id' => $owner->id,
                'subtotal_amount' => $pricePlan1->amount,
                'deposit_amount' => 500000,
                'additional_fees_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $pricePlan1->amount + 500000,
                'status' => InvoiceStatus::PAID,
                'due_date' => now()->subMonths(2)->addDays(2)->toDateString(),
                'paid_at' => now()->subMonths(2),
            ]
        );

        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice1->id, 'item_type' => 'rent'],
            [
                'description' => 'Sewa ' . $unit1->name . ' - ' . $property->name . ' (Bulan ke-1)',
                'unit_price' => $pricePlan1->amount,
                'quantity' => 1,
                'total_amount' => $pricePlan1->amount,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['invoice_id' => $invoice1->id, 'item_type' => 'deposit'],
            [
                'description' => 'Uang Jaminan / Deposit Kamar (Dapat Dikembalikan)',
                'unit_price' => 500000,
                'quantity' => 1,
                'total_amount' => 500000,
            ]
        );

        Payment::updateOrCreate(
            ['invoice_id' => $invoice1->id],
            [
                'code' => 'PAY-20260701-0001',
                'tenant_id' => $tenant1->id,
                'gateway_reference' => 'MOCK-BCA-VA-987654321',
                'payment_method' => PaymentMethod::BANK_TRANSFER,
                'payment_channel' => 'bca_va',
                'amount' => $pricePlan1->amount + 500000,
                'status' => PaymentStatus::SETTLEMENT,
                'paid_at' => now()->subMonths(2),
                'gateway_payload' => ['channel' => 'bca_va', 'va_number' => '123456789012'],
            ]
        );

        $rental1 = Rental::updateOrCreate(
            ['code' => 'RNT-2026-0001'],
            [
                'booking_request_id' => $booking1->id,
                'tenant_id' => $tenant1->id,
                'unit_id' => $unit1->id,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(4)->toDateString(),
                'monthly_rent' => $pricePlan1->amount,
                'deposit_held' => 500000,
                'status' => RentalStatus::ACTIVE,
            ]
        );

        // 2. Seed Maintenance Issue Ticket
        RentalIssue::updateOrCreate(
            ['rental_id' => $rental1->id, 'title' => 'Kran Wastafel Kamar Mandi Menetes'],
            [
                'tenant_id' => $tenant1->id,
                'description' => 'Kran wastafel di dalam kamar mandi sedikit menetes airnya saat ditutup rapat. Mohon bantuan teknisi untuk diperbaiki.',
                'priority' => IssuePriority::MEDIUM,
                'status' => IssueStatus::IN_PROGRESS,
                'owner_notes' => 'Teknisi dijadwalkan datang besok pukul 10:00 WIB.',
            ]
        );

        // 3. Seed Verified Tenant Review & Owner Reply
        Review::updateOrCreate(
            ['rental_id' => $rental1->id],
            [
                'property_id' => $property->id,
                'unit_id' => $unit1->id,
                'tenant_id' => $tenant1->id,
                'rating' => 5,
                'cleanliness_rating' => 5,
                'accuracy_rating' => 5,
                'communication_rating' => 5,
                'location_rating' => 5,
                'value_rating' => 5,
                'comment' => 'Kost sangat nyaman, bersih, dan lingkungan sekitarnya tenang untuk belajar. Ibu dan bapak kost sangat ramah serta cepat tanggap jika ada kendala fasilitas. Dekat sekali dengan kampus UGM!',
                'moderation_status' => ReviewModerationStatus::APPROVED,
                'owner_reply' => 'Terima kasih banyak Mas Rafa atas ulasannya! Senang Mas Rafa merasa nyaman tinggal di Griya Asri. Semoga lancar studinya di UGM!',
                'owner_replied_at' => now()->subMonths(1),
            ]
        );

        // 4. Seed Pending Booking Request for Tenant 2 (Anisa)
        if ($tenant2 && $unit2) {
            $booking2 = BookingRequest::updateOrCreate(
                ['code' => 'BOOK-2026-002'],
                [
                    'unit_id' => $unit2->id,
                    'tenant_id' => $tenant2->id,
                    'price_plan_id' => $pricePlan2->id,
                    'check_in_date' => now()->addDays(3)->toDateString(),
                    'check_out_date' => now()->addDays(3)->addMonths(3)->toDateString(),
                    'duration_months' => 3,
                    'base_amount' => $pricePlan2->amount,
                    'deposit_amount' => 500000,
                    'additional_fees_amount' => 0,
                    'total_amount' => $pricePlan2->amount + 500000,
                    'status' => BookingStatus::PENDING_APPROVAL,
                ]
            );
        }

        // 5. Seed In-Platform Chat Conversation
        $conversation = Conversation::firstOrCreate([
            'property_id' => $property->id,
            'booking_request_id' => $booking1->id,
        ], [
            'last_message_at' => now()->subHours(2),
        ]);

        $conversation->participants()->syncWithoutDetaching([$tenant1->id, $owner->id]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'sender_id' => $tenant1->id,
            'body' => 'Halo Pak Bambang, selamat siang. Saya calon penyewa kamar 101, apakah ada jam malam untuk tamu kost?',
        ], [
            'created_at' => now()->subDays(2),
        ]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'sender_id' => $owner->id,
            'body' => 'Halo Mas Rafa, siang. Untuk gerbang utama kami sediakan kunci kartu akses 24 jam. Namun untuk tamu lawan jenis dilarang masuk ke dalam kamar, bisa menggunakan ruang tamu bersama di lantai 1.',
        ], [
            'created_at' => now()->subDays(2)->addHours(1),
        ]);

        Message::firstOrCreate([
            'conversation_id' => $conversation->id,
            'sender_id' => $tenant1->id,
            'body' => 'Baik Pak Bambang, sangat jelas. Terima kasih banyak informasinya!',
        ], [
            'created_at' => now()->subHours(2),
        ]);
    }
}
