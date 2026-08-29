@extends('layouts.tenant', ['title' => 'Daftar Tagihan & Pembayaran'])

@section('tenant_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Tagihan & Pembayaran</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola tagihan sewa kost, deposit, dan riwayat pembayaran digital Anda.</p>
        </div>
    </div>

    @if($invoices->isEmpty())
        <x-card class="p-12 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Belum Ada Tagihan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Tagihan sewa akan otomatis dibuat setelah pengajuan booking Anda disetujui pemilik properti.</p>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($invoices as $inv)
                <x-card class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:shadow-md transition-shadow">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-slate-800">{{ $inv->code }}</span>
                            <x-badge :variant="$inv->status->color()" size="sm">
                                {{ $inv->status->label() }}
                            </x-badge>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $inv->bookingRequest?->unit?->property?->name ?? 'Sewa Properti' }} &bull; {{ $inv->bookingRequest?->unit?->name }}
                        </h4>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span>Jatuh Tempo: <strong>{{ $inv->due_date->format('d M Y') }}</strong></span>
                            @if($inv->paid_at)
                                <span>&bull;</span>
                                <span class="text-emerald-600 font-semibold">Dibayar pada {{ $inv->paid_at->format('d M Y H:i') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 gap-2">
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-semibold uppercase">Total Tagihan</p>
                            <p class="text-base font-extrabold text-slate-900">{{ $inv->formatted_total_amount }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-button variant="outline" size="sm" href="{{ route('tenant.invoices.show', $inv) }}">
                                Detail Rincian
                            </x-button>
                            @if($inv->status === \App\Enums\InvoiceStatus::UNPAID)
                                <x-button variant="primary" size="sm" href="{{ route('tenant.invoices.checkout', $inv) }}">
                                    Bayar Sekarang
                                </x-button>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach

            <div>
                {{ $invoices->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
