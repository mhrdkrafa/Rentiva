@extends('layouts.tenant', ['title' => 'Rincian Tagihan ' . $invoice->code])

@section('tenant_content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            &larr; Kembali ke Daftar Tagihan
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-700 flex items-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Faktur / Kuitansi
            </button>
            @if($invoice->status === \App\Enums\InvoiceStatus::UNPAID)
                <x-button variant="primary" size="sm" href="{{ route('tenant.invoices.checkout', $invoice) }}">
                    Lanjut ke Pembayaran
                </x-button>
            @endif
        </div>
    </div>

    <!-- Official Invoice Document Container -->
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-8 print:border-none print:shadow-none print:p-0">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-slate-100 pb-8">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-black text-sm">
                        R
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">Rentiva</span>
                </div>
                <p class="text-xs text-slate-400">Faktur Sewa Resmi Marketplace & Manajemen Kost</p>
            </div>

            <div class="sm:text-right space-y-1">
                <p class="font-mono text-sm font-black text-slate-900">{{ $invoice->code }}</p>
                <div class="inline-block">
                    <x-badge :variant="$invoice->status->color()" size="sm">
                        {{ $invoice->status->label() }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-400 pt-1">Tanggal Terbit: {{ $invoice->created_at->format('d M Y') }}</p>
                <p class="text-xs text-slate-400">Jatuh Tempo: {{ $invoice->due_date->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Parties Involved -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs text-slate-600 border-b border-slate-100 pb-8">
            <div class="space-y-1.5">
                <p class="font-bold text-slate-900 uppercase tracking-wider text-[10px]">Ditagihkan Kepada (Penyewa):</p>
                <p class="font-bold text-slate-800 text-sm">{{ $invoice->tenant->name }}</p>
                <p>{{ $invoice->tenant->email }}</p>
                <p>{{ $invoice->tenant->phone ?? '-' }}</p>
            </div>

            <div class="space-y-1.5 sm:text-right">
                <p class="font-bold text-slate-900 uppercase tracking-wider text-[10px]">Pemberi Sewa / Pemilik Properti:</p>
                <p class="font-bold text-slate-800 text-sm">{{ $invoice->bookingRequest?->unit?->property?->name ?? 'Pemilik Kost' }}</p>
                <p>Pemilik: {{ $invoice->owner->name }}</p>
                <p>{{ $invoice->bookingRequest?->unit?->property?->address ?? '-' }}</p>
            </div>
        </div>

        <!-- Itemized Table -->
        <div class="space-y-4">
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rincian Item Tagihan</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 font-semibold uppercase">
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3 text-center">Tipe</th>
                            <th class="py-3 text-right">Harga Satuan</th>
                            <th class="py-3 text-center">Jml</th>
                            <th class="py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="py-3 font-semibold text-slate-800">{{ $item->description }}</td>
                                <td class="py-3 text-center text-slate-500 capitalize">{{ str_replace('_', ' ', $item->item_type) }}</td>
                                <td class="py-3 text-right font-mono text-slate-600">{{ $item->formatted_unit_price }}</td>
                                <td class="py-3 text-center text-slate-600">{{ $item->quantity }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900">{{ $item->formatted_total_amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Calculation Summary -->
        <div class="border-t border-slate-200 pt-6 flex justify-end">
            <div class="w-full sm:w-80 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal Sewa</span>
                    <span class="font-mono font-medium">{{ $invoice->formatted_subtotal_amount }}</span>
                </div>
                @if($invoice->deposit_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Deposit Jaminan</span>
                        <span class="font-mono font-medium">{{ $invoice->formatted_deposit_amount }}</span>
                    </div>
                @endif
                @if($invoice->additional_fees_amount > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Tambahan</span>
                        <span class="font-mono font-medium">{{ $invoice->formatted_additional_fees_amount }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-slate-900 font-extrabold text-base border-t border-slate-200 pt-3">
                    <span>Total Pembayaran</span>
                    <span class="font-mono text-emerald-600">{{ $invoice->formatted_total_amount }}</span>
                </div>
            </div>
        </div>

        @if($invoice->status === \App\Enums\InvoiceStatus::PAID)
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 text-xs flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold">Lunas Terbayar</span> pada {{ $invoice->paid_at?->format('d M Y H:i:s') }}. Kontrak sewa digital dan kamar kost Anda telah aktif secara resmi.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
