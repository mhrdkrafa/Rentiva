@extends('layouts.owner', ['title' => 'Keuangan & Pendapatan'])

@section('owner_content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Keuangan & Pembukuan Sewa</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau seluruh arus kas masuk, deposit jaminan, dan status tagihan sewa properti Anda.</p>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <x-card class="p-6 space-y-2 bg-white">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan Sewa (Lunas)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ \App\Support\Money::format($totalRevenue) }}</p>
            <p class="text-[11px] text-emerald-600 font-semibold">Tercatat dari faktur sewa lunas</p>
        </x-card>

        <x-card class="p-6 space-y-2 bg-white">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Deposit Jaminan Ditahan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ \App\Support\Money::format($totalDepositHeld) }}</p>
            <p class="text-[11px] text-slate-400 font-semibold">Akan dikembalikan saat masa sewa berakhir</p>
        </x-card>

        <x-card class="p-6 space-y-2 bg-white">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tagihan Menunggu Pembayaran</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-black text-slate-900">{{ \App\Support\Money::format($totalPendingReceivables) }}</p>
            <p class="text-[11px] text-amber-600 font-semibold">Menunggu pelunasan dari calon penyewa</p>
        </x-card>
    </div>

    <!-- Invoices Ledger Table -->
    <x-card class="p-6 space-y-4 bg-white">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Buku Besar Tagihan & Transaksi</h3>
        </div>

        @if($invoices->isEmpty())
            <div class="p-8 text-center text-xs text-slate-400">
                Belum ada catatan transaksi sewa.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 font-semibold uppercase">
                            <th class="py-3">Faktur</th>
                            <th class="py-3">Penyewa</th>
                            <th class="py-3">Properti / Unit</th>
                            <th class="py-3 text-right">Total Tagihan</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoices as $inv)
                            <tr>
                                <td class="py-3 font-mono font-bold text-slate-900">{{ $inv->code }}</td>
                                <td class="py-3 font-semibold text-slate-800">{{ $inv->tenant->name }}</td>
                                <td class="py-3 text-slate-600">{{ $inv->bookingRequest?->unit?->property?->name ?? 'Properti' }} &bull; {{ $inv->bookingRequest?->unit?->name }}</td>
                                <td class="py-3 text-right font-mono font-bold text-slate-900">{{ $inv->formatted_total_amount }}</td>
                                <td class="py-3 text-center">
                                    <x-badge :variant="$inv->status->color()" size="sm">
                                        {{ $inv->status->label() }}
                                    </x-badge>
                                </td>
                                <td class="py-3 text-right text-slate-400">{{ $inv->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
