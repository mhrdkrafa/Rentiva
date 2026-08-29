@extends('layouts.owner', ['title' => 'Statistik & Analitik Kinerja Properti', 'headerTitle' => 'Statistik & Kinerja'])

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Statistik & Kinerja Properti</h2>
        <p class="text-sm text-slate-500 mt-1">Analisis tingkat hunian, rincian omzet bulanan, dan performa setiap aset kost Anda.</p>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card class="p-5">
            <span class="text-xs font-semibold text-slate-400">Total Portofolio Properti</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $propertyBreakdowns->count() }} Lokasi</p>
        </x-card>

        <x-card class="p-5">
            <span class="text-xs font-semibold text-slate-400">Total Kapasitas Kamar</span>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalUnits }} Kamar</p>
        </x-card>

        <x-card class="p-5 bg-gradient-to-br from-emerald-50 to-white border-emerald-100">
            <span class="text-xs font-semibold text-slate-400">Rata-rata Okupansi</span>
            <p class="text-2xl font-black text-emerald-700 mt-1">{{ $overallOccupancy }}%</p>
            <span class="text-[11px] text-slate-500">{{ $totalOccupied }} dari {{ $totalUnits }} kamar terisi</span>
        </x-card>

        <x-card class="p-5 bg-gradient-to-br from-emerald-50 to-white border-emerald-100">
            <span class="text-xs font-semibold text-slate-400">Total Omzet Aktif / Bln</span>
            <p class="text-xl sm:text-2xl font-black text-emerald-700 mt-1">{{ \App\Support\Money::format($totalRevenue) }}</p>
        </x-card>
    </div>

    <!-- Per-Property Performance Breakdown Table -->
    <x-card class="p-6 space-y-4">
        <h3 class="text-base font-bold text-slate-900">Rincian Performa per Properti</h3>

        @if($propertyBreakdowns->isEmpty())
            <p class="text-xs text-slate-400 py-6 text-center">Belum ada properti terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-semibold border-b border-slate-100">
                        <tr>
                            <th class="p-3.5">Nama Properti & Lokasi</th>
                            <th class="p-3.5">Total Kamar</th>
                            <th class="p-3.5">Terisi / Siap Huni</th>
                            <th class="p-3.5">Tingkat Okupansi</th>
                            <th class="p-3.5 text-right">Omzet Berjalan</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-800">
                        @foreach($propertyBreakdowns as $item)
                            <tr>
                                <td class="p-3.5">
                                    <span class="font-bold text-slate-900">{{ $item['property']->name }}</span>
                                    <p class="text-[11px] text-slate-500">{{ $item['property']->location->name }}</p>
                                </td>
                                <td class="p-3.5 font-semibold">
                                    {{ $item['total_units'] }} Unit
                                </td>
                                <td class="p-3.5">
                                    <span class="text-emerald-700 font-bold">{{ $item['occupied_units'] }} Terisi</span> &bull; 
                                    <span class="text-slate-500">{{ $item['available_units'] }} Kosong</span>
                                </td>
                                <td class="p-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $item['occupancy_rate'] }}%;"></div>
                                        </div>
                                        <span class="font-bold text-slate-900">{{ $item['occupancy_rate'] }}%</span>
                                    </div>
                                </td>
                                <td class="p-3.5 text-right font-extrabold text-slate-900">
                                    {{ $item['formatted_monthly_revenue'] }}
                                </td>
                                <td class="p-3.5 text-right">
                                    <x-button variant="ghost" size="sm" href="{{ route('owner.properties.show', $item['property']) }}">
                                        Kelola &rarr;
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</div>
@endsection
