<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi Sewa #{{ $rental->code }} — Rentiva</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased p-4 sm:p-8">
    <div class="max-w-3xl mx-auto space-y-4">
        <!-- Top Toolbar (Hidden on print) -->
        <div class="no-print flex items-center justify-between">
            <a href="{{ route('tenant.rentals.show', $rental) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Kembali ke Rincian Sewa
            </a>

            <button
                onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md transition-colors"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Simpan PDF
            </button>
        </div>

        <!-- Official Receipt Document -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl print-shadow-none space-y-8 border border-slate-200">
            <!-- Header Brand & Invoice Code -->
            <div class="flex items-start justify-between border-b border-slate-100 pb-6">
                <div class="space-y-1">
                    <span class="text-2xl font-black tracking-tight text-emerald-700">Rentiva</span>
                    <p class="text-xs text-slate-500">Platform Sewa Kost & Manajemen Properti Digital</p>
                </div>
                <div class="text-right space-y-0.5">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Bukti Kuitansi Sewa</span>
                    <p class="text-lg font-mono font-black text-slate-900">#{{ $rental->code }}</p>
                    <p class="text-[11px] text-slate-400">Tanggal Terbit: {{ $rental->created_at->format('d F Y') }}</p>
                </div>
            </div>

            <!-- Landlord and Tenant Billed Details -->
            <div class="grid grid-cols-2 gap-8 text-xs">
                <div class="space-y-1">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Diterbitkan Oleh (Pengelola):</span>
                    <h4 class="font-bold text-slate-900 text-sm">{{ $rental->unit->property->owner->name }}</h4>
                    <p class="text-slate-600">{{ $rental->unit->property->name }}</p>
                    <p class="text-slate-500">{{ $rental->unit->property->address }}</p>
                    <p class="text-slate-500">{{ $rental->unit->property->location->name }}</p>
                </div>

                <div class="space-y-1 text-right">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Diberikan Kepada (Penyewa):</span>
                    <h4 class="font-bold text-slate-900 text-sm">{{ $rental->tenant->name }}</h4>
                    <p class="text-slate-600">{{ $rental->tenant->email }}</p>
                    <p class="text-slate-500">{{ $rental->tenant->phone ?? '-' }}</p>
                </div>
            </div>

            <!-- Rental Specs Table -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-[10px]">
                        <tr>
                            <th class="p-3.5">Deskripsi Kamar / Unit</th>
                            <th class="p-3.5">Periode Sewa</th>
                            <th class="p-3.5 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        <tr>
                            <td class="p-3.5">
                                <span class="font-bold text-slate-900">{{ $rental->unit->name }}</span>
                                <p class="text-[11px] text-slate-500">Tipe: {{ $rental->unit->roomType->name }} &bull; {{ $rental->unit->property->name }}</p>
                            </td>
                            <td class="p-3.5">
                                {{ $rental->start_date->format('d/m/Y') }} — {{ $rental->end_date->format('d/m/Y') }}
                            </td>
                            <td class="p-3.5 text-right font-semibold">
                                {{ $rental->formatted_monthly_rent }}
                            </td>
                        </tr>

                        @if($rental->deposit_held > 0)
                            <tr>
                                <td class="p-3.5 text-slate-600">
                                    Deposit Jaminan (Akan dikembalikan saat check-out)
                                </td>
                                <td class="p-3.5 text-slate-400">-</td>
                                <td class="p-3.5 text-right font-semibold">
                                    {{ $rental->formatted_deposit_held }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                        <tr>
                            <td colspan="2" class="p-3.5 text-right text-slate-900">Total Biaya:</td>
                            <td class="p-3.5 text-right text-emerald-700 text-sm font-extrabold">
                                {{ \App\Support\Money::format($rental->monthly_rent + $rental->deposit_held) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes & Status Seal -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs text-slate-500">
                <div class="space-y-1">
                    <p class="font-semibold text-slate-700">Kuitansi Resmi Sah Diterbitkan Sistem Rentiva</p>
                    <p class="text-[11px]">Pembayaran telah diverifikasi secara terpusat dan tercatat aman.</p>
                </div>
                <div class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 text-center">
                    LUNAS / TERVERIFIKASI
                </div>
            </div>
        </div>
    </div>
</body>
</html>
