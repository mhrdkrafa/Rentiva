@extends('layouts.owner', ['title' => 'Dashboard Pemilik Properti — Rentiva', 'headerTitle' => 'Dashboard Manajemen Properti'])

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Selamat Datang, {{ auth()->user()->name ?? 'Mitra Pemilik' }}! 🏢
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau tingkat okupansi hunian, estimasi pendapatan bulanan, dan kelola operasional properti Anda secara terpusat (Total Properti: {{ $stats['total_properties'] }}).
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" href="{{ route('owner.properties.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Properti Baru
            </x-button>
        </div>
    </div>

    <!-- Financial & Occupancy KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Estimated Monthly Revenue -->
        <x-card class="p-5 flex items-center gap-4 bg-gradient-to-br from-emerald-50 to-white border-emerald-100">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Estimasi Omzet / Bln</span>
                <p class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">{{ $stats['formatted_monthly_revenue'] }}</p>
            </div>
        </x-card>

        <!-- Occupancy Rate -->
        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-500/10 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Tingkat Okupansi</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['occupancy_rate'] }}%</p>
                <span class="text-[11px] text-slate-500">{{ $stats['occupied_units'] }}/{{ $stats['total_units'] }} Unit Terisi</span>
            </div>
        </x-card>

        <!-- Pending Bookings -->
        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Permintaan Masuk</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['pending_bookings_count'] }}</p>
                <a href="{{ route('owner.bookings') }}" class="text-[11px] text-emerald-600 font-semibold hover:underline">Tinjau Sekarang &rarr;</a>
            </div>
        </x-card>

        <!-- Pending Issues -->
        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Keluhan Kamar</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['pending_issues_count'] }}</p>
                <a href="{{ route('owner.issues.index') }}" class="text-[11px] text-emerald-600 font-semibold hover:underline">Lihat Tiket &rarr;</a>
            </div>
        </x-card>
    </div>

    <!-- Occupancy Progress Bar -->
    <x-card class="p-6 space-y-3">
        <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-900">Kapasitas Okupansi Seluruh Kamar</span>
            <span class="font-black text-emerald-700">{{ $stats['occupied_units'] }} Terisi &bull; {{ $stats['available_units'] }} Siap Huni (Total {{ $stats['total_units'] }} Unit)</span>
        </div>
        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden flex">
            <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $stats['occupancy_rate'] }}%;"></div>
        </div>
    </x-card>

    <!-- Two Columns: Pending Bookings & Maintenance Issues -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Incoming Booking Requests -->
        <x-card class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Permintaan Sewa Menunggu Konfirmasi</h3>
                    <p class="text-xs text-slate-500">Konfirmasi sebelum batas waktu 24 jam berakhir</p>
                </div>
                <a href="{{ route('owner.bookings') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Semua Permintaan &rarr;</a>
            </div>

            @if($pendingBookings->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Tidak ada pengajuan sewa baru yang menunggu konfirmasi.</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingBookings as $booking)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-amber-50/60 border border-amber-100 text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900">{{ $booking->tenant->name }} &bull; {{ $booking->unit->name }}</span>
                                <p class="text-slate-600 text-[11px]">{{ $booking->unit->property->name }} ({{ $booking->duration_months }} Bulan)</p>
                                <p class="text-emerald-700 font-bold text-[11px]">{{ $booking->formatted_total_amount }}</p>
                            </div>
                            <x-button variant="primary" size="sm" href="{{ route('owner.bookings.show', $booking) }}">
                                Tinjau
                            </x-button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <!-- Active Maintenance Issues -->
        <x-card class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Laporan Keluhan Penyewa</h3>
                    <p class="text-xs text-slate-500">Perbaikan dan kendala fasilitas yang dilaporkan</p>
                </div>
                <a href="{{ route('owner.issues.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Semua Keluhan &rarr;</a>
            </div>

            @if($pendingIssues->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Tidak ada keluhan perbaikan yang aktif. Semua fasilitas normal!</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingIssues as $issue)
                        <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 text-xs">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-1.5">
                                    <x-badge :variant="$issue->priority->color()" size="sm">
                                        {{ $issue->priority->label() }}
                                    </x-badge>
                                    <span class="font-bold text-slate-900">{{ $issue->title }}</span>
                                </div>
                                <p class="text-slate-500 text-[11px]">{{ $issue->rental->unit->property->name }} ({{ $issue->rental->unit->name }})</p>
                            </div>
                            <x-button variant="outline" size="sm" href="{{ route('owner.issues.show', $issue) }}">
                                Tindak Lanjut
                            </x-button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <!-- Properties Showcase Overview -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Properti Kost Anda</h3>
            <a href="{{ route('owner.properties.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Kelola Semua Properti &rarr;</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $prop)
                <x-card class="p-5 space-y-3 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-slate-900 text-base">
                                <a href="{{ route('owner.properties.show', $prop) }}" class="hover:text-emerald-600">
                                    {{ $prop->name }}
                                </a>
                            </h4>
                            <p class="text-xs text-slate-500">{{ $prop->location?->name }} &bull; {{ $prop->units->count() }} Kamar</p>
                        </div>
                        <x-badge :variant="$prop->verification_status->color()" size="sm">
                            {{ $prop->verification_status->label() }}
                        </x-badge>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Tarif Mulai:</span>
                        <span class="font-bold text-slate-900">{{ $prop->formatted_min_price }}</span>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</div>
@endsection
