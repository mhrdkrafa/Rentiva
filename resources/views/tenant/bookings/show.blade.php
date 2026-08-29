@extends('layouts.tenant', ['title' => 'Detail Pengajuan ' . $booking->code, 'headerTitle' => 'Rincian Pengajuan Sewa'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('tenant.bookings.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Pengajuan
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan #{{ $booking->code }}</h2>
                    <x-badge :variant="$booking->status->color()" size="md">
                        {{ $booking->status->label() }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">Diajukan pada {{ $booking->created_at->format('d M Y, H:i') }} WIB</p>
            </div>

            @if(in_array($booking->status, [\App\Enums\BookingStatus::PENDING_APPROVAL, \App\Enums\BookingStatus::APPROVED]))
                <form action="{{ route('tenant.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan sewa ini?')">
                    @csrf
                    <x-button type="submit" variant="danger" size="sm">
                        Batalkan Pengajuan
                    </x-button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Alerts -->
    @if($booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-bold">Menunggu Konfirmasi Pemilik Kost</p>
                <p class="mt-0.5">Pemilik kost memiliki waktu maksimal 24 jam untuk meninjau dan menyetujui pengajuan Anda. Anda akan diberitahu setelah disetujui.</p>
            </div>
        </div>
    @elseif($booking->status === \App\Enums\BookingStatus::APPROVED)
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs flex items-start justify-between gap-4">
            <div class="space-y-1">
                <p class="font-bold text-emerald-900 text-sm">Selamat! Pengajuan Sewa Anda Disetujui Pemilik</p>
                <p class="text-emerald-800">Silakan lanjutkan ke proses pembayaran sebelum batas waktu berakhir pada <strong>{{ $booking->expires_at?->format('d M Y, H:i') }} WIB</strong>.</p>
            </div>
            <x-button variant="primary" size="md" class="shrink-0 shadow-md shadow-emerald-600/20">
                Lanjut ke Pembayaran
            </x-button>
        </div>
    @elseif($booking->status === \App\Enums\BookingStatus::REJECTED)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1">
            <p class="font-bold text-rose-950">Pengajuan Sewa Ditolak Pemilik</p>
            <p class="text-rose-800">Alasan: {{ $booking->owner_rejection_reason ?? 'Tidak ada alasan khusus dicantumkan.' }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left 2 Cols: Unit & Booking Information -->
        <div class="md:col-span-2 space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Kamar & Properti</h3>
                
                <div class="flex items-start gap-4">
                    <img src="{{ $booking->unit->cover_image_url }}" alt="{{ $booking->unit->name }}" class="w-24 h-24 rounded-2xl object-cover bg-slate-100 shrink-0" />
                    <div class="space-y-1">
                        <h4 class="font-bold text-slate-900 text-base">{{ $booking->unit->property->name }}</h4>
                        <p class="text-xs text-slate-600 font-semibold">{{ $booking->unit->name }} ({{ $booking->unit->roomType->name }})</p>
                        <p class="text-xs text-slate-500">{{ $booking->unit->property->address }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400">Tanggal Mulai Sewa (Check-in):</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $booking->check_in_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Tanggal Selesai (Check-out):</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $booking->check_out_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Durasi Sewa:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $booking->duration_months }} Bulan</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Pemilik Properti:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $booking->unit->property->owner->name }}</p>
                    </div>
                </div>
            </x-card>

            @if($booking->tenant_notes)
                <x-card class="p-6 space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Catatan Pengajuan Anda</h3>
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $booking->tenant_notes }}</p>
                </x-card>
            @endif
        </div>

        <!-- Right Col: Price Breakdown Receipt -->
        <div class="space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Rincian Pembayaran</h3>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya Sewa ({{ $booking->duration_months }} bln):</span>
                        <span class="font-semibold text-slate-900">{{ $booking->formatted_base_amount }}</span>
                    </div>

                    @if($booking->deposit_amount > 0)
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Deposit Jaminan (Dikembalikan):</span>
                            <span class="font-semibold text-slate-900">{{ $booking->formatted_deposit_amount }}</span>
                        </div>
                    @endif

                    @if($booking->additional_fees_amount > 0)
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Biaya Layanan & Kebersihan:</span>
                            <span class="font-semibold text-slate-900">{{ $booking->formatted_additional_fees_amount }}</span>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900">Total Tagihan:</span>
                        <span class="text-lg font-black text-emerald-700">{{ $booking->formatted_total_amount }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
