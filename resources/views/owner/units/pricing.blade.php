@extends('layouts.owner', ['title' => 'Atur Tarif Kamar ' . $unit->name, 'headerTitle' => 'Manajemen Tarif & Skema Harga'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('owner.properties.show', $unit->property_id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Detail Properti
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Atur Skema Tarif: {{ $unit->name }}</h2>
        <p class="text-xs text-slate-500 mt-1">{{ $unit->property->name }} &bull; Tipe: {{ $unit->roomType->name }}</p>
    </div>

    @php
        $monthlyPlan = $unit->pricePlans->firstWhere('billing_period', \App\Enums\BillingPeriod::MONTHLY);
        $dailyPlan = $unit->pricePlans->firstWhere('billing_period', \App\Enums\BillingPeriod::DAILY);
        $weeklyPlan = $unit->pricePlans->firstWhere('billing_period', \App\Enums\BillingPeriod::WEEKLY);
        $yearlyPlan = $unit->pricePlans->firstWhere('billing_period', \App\Enums\BillingPeriod::YEARLY);
    @endphp

    <x-card class="p-6 sm:p-8">
        <form action="{{ route('owner.units.pricing.update', $unit) }}" method="POST" class="space-y-6">
            @csrf

            <div class="p-4 bg-emerald-50/70 border border-emerald-100 rounded-2xl text-xs text-emerald-950 space-y-1">
                <span class="font-bold text-emerald-900">Ketentuan Harga:</span>
                <p>Masukkan nominal harga dalam bilangan bulat Rupiah (contoh: 1500000 untuk Rp 1.500.000). Sistem akan secara otomatis mengamankan transaksi berbasis integer Rupiah murni tanpa resiko floating-point.</p>
            </div>

            <!-- Primary Monthly Price (Required) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    type="number"
                    name="monthly_amount"
                    label="Tarif Sewa Bulanan (Wajib, IDR) *"
                    placeholder="Contoh: 1500000"
                    :value="old('monthly_amount', $monthlyPlan?->amount)"
                    required
                />

                <x-input
                    type="number"
                    name="deposit_amount"
                    label="Deposit Jaminan (Opsional, IDR)"
                    placeholder="Contoh: 500000"
                    :value="old('deposit_amount', $monthlyPlan?->deposit_amount ?? 0)"
                />
            </div>

            <!-- Optional Flexible Pricing Plans -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Skema Durasi Lainnya (Opsional)</h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-input
                        type="number"
                        name="daily_amount"
                        label="Tarif Harian (IDR)"
                        placeholder="Contoh: 150000"
                        :value="old('daily_amount', $dailyPlan?->amount)"
                    />

                    <x-input
                        type="number"
                        name="weekly_amount"
                        label="Tarif Mingguan (IDR)"
                        placeholder="Contoh: 600000"
                        :value="old('weekly_amount', $weeklyPlan?->amount)"
                    />

                    <x-input
                        type="number"
                        name="yearly_amount"
                        label="Tarif Tahunan (IDR)"
                        placeholder="Contoh: 16000000"
                        :value="old('yearly_amount', $yearlyPlan?->amount)"
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <x-button variant="ghost" href="{{ route('owner.properties.show', $unit->property_id) }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" class="shadow-md shadow-emerald-600/20">
                    Simpan Perubahan Tarif
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
