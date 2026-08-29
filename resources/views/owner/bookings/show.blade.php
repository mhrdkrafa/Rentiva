@extends('layouts.owner', ['title' => 'Tinjau Pengajuan ' . $booking->code, 'headerTitle' => 'Tinjau Pengajuan Sewa'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ rejectModalOpen: false }">
    <div>
        <a href="{{ route('owner.bookings') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Permintaan
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan #{{ $booking->code }}</h2>
                    <x-badge :variant="$booking->status->color()" size="md">
                        {{ $booking->status->label() }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">Diterima pada {{ $booking->created_at->format('d M Y, H:i') }} WIB</p>
            </div>

            <!-- Owner Action Buttons -->
            @if($booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="rejectModalOpen = true"
                        class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-xl border border-rose-200 transition-colors"
                    >
                        Tolak Pengajuan
                    </button>

                    <form action="{{ route('owner.bookings.approve', $booking) }}" method="POST" onsubmit="return confirm('Setujui pengajuan sewa ini?')">
                        @csrf
                        <x-button type="submit" variant="primary" size="md" class="shadow-md shadow-emerald-600/20">
                            Setujui Pengajuan Sewa
                        </x-button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Tenant Profile Card -->
    <x-card class="p-6 sm:p-8 space-y-6">
        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Data Calon Penyewa</h3>
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <x-avatar :name="$booking->tenant->name" size="xl" />
                <div class="space-y-1">
                    <h4 class="font-bold text-slate-900 text-lg">{{ $booking->tenant->name }}</h4>
                    <p class="text-xs text-slate-500">{{ $booking->tenant->email }} &bull; {{ $booking->tenant->phone ?? 'Tanpa nomor telepon' }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Pekerjaan / Status: <strong>{{ $booking->tenant->profile?->occupation ?? 'Tidak dicantumkan' }}</strong>
                    </p>
                </div>
            </div>

            @if($booking->tenant->profile?->is_identity_verified)
                <x-badge variant="success" size="md">
                    Identitas KTP Terverifikasi
                </x-badge>
            @endif
        </div>

        @if($booking->tenant->profile?->bio)
            <div class="p-4 bg-slate-50 rounded-2xl text-xs text-slate-700 space-y-1">
                <span class="font-bold text-slate-900">Tentang Penyewa:</span>
                <p>{{ $booking->tenant->profile->bio }}</p>
            </div>
        @endif

        @if($booking->tenant_notes)
            <div class="p-4 bg-emerald-50/60 border border-emerald-100 rounded-2xl text-xs text-emerald-950 space-y-1">
                <span class="font-bold text-emerald-900">Pesan Pengajuan dari Penyewa:</span>
                <p>{{ $booking->tenant_notes }}</p>
            </div>
        @endif
    </x-card>

    <!-- Booking Specs & Price Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card class="p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Unit yang Diajukan</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Properti:</span>
                    <span class="font-bold text-slate-900">{{ $booking->unit->property->name }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Kamar/Unit:</span>
                    <span class="font-bold text-slate-900">{{ $booking->unit->name }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Check-in:</span>
                    <span class="font-bold text-slate-900">{{ $booking->check_in_date->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Check-out:</span>
                    <span class="font-bold text-slate-900">{{ $booking->check_out_date->format('d F Y') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Durasi:</span>
                    <span class="font-bold text-slate-900">{{ $booking->duration_months }} Bulan</span>
                </div>
            </div>
        </x-card>

        <x-card class="p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Rincian Nominal Pembayaran</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Tarif Sewa Pokok:</span>
                    <span class="font-semibold text-slate-900">{{ $booking->formatted_base_amount }}</span>
                </div>
                @if($booking->deposit_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Deposit Jaminan:</span>
                        <span class="font-semibold text-slate-900">{{ $booking->formatted_deposit_amount }}</span>
                    </div>
                @endif
                @if($booking->additional_fees_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Tambahan:</span>
                        <span class="font-semibold text-slate-900">{{ $booking->formatted_additional_fees_amount }}</span>
                    </div>
                @endif
                <div class="pt-3 border-t border-slate-200 flex justify-between">
                    <span class="font-bold text-slate-900">Total Nominal Sewa:</span>
                    <span class="font-black text-emerald-700 text-base">{{ $booking->formatted_total_amount }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Reject Modal -->
    <div
        x-show="rejectModalOpen"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="rejectModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-5 shadow-2xl">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tolak Pengajuan Sewa</h3>
                <p class="text-xs text-slate-500 mt-1">Berikan alasan yang jelas kepada calon penyewa mengapa pengajuan tidak dapat diterima.</p>
            </div>

            <form action="{{ route('owner.bookings.reject', $booking) }}" method="POST" class="space-y-4">
                @csrf
                <x-textarea
                    name="reason"
                    label="Alasan Penolakan *"
                    placeholder="Contoh: Kamar sedang dalam renovasi, tidak memenuhi kriteria aturan kost..."
                    rows="3"
                    required
                />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button type="button" variant="ghost" @click="rejectModalOpen = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="danger">
                        Tolak Pengajuan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
