@extends('layouts.tenant', ['title' => 'Dashboard Penyewa — Rentiva', 'headerTitle' => 'Dashboard Penyewa'])

@section('content')
<div class="space-y-8">
    <!-- Welcome Header & Stats Grid -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Halo, {{ auth()->user()->name ?? 'Penyewa' }}! 👋
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola status sewa kamar kost aktif, pantau pengajuan baru, dan sampaikan keluhan perbaikan dengan mudah.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" href="{{ route('properties.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari Kost Baru
            </x-button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card class="p-5 flex items-center gap-4 bg-gradient-to-br from-emerald-50 to-white border-emerald-100">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Sewa Aktif</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['active_rentals_count'] }}</p>
            </div>
        </x-card>

        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Pengajuan Booking</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['pending_bookings_count'] }}</p>
            </div>
        </x-card>

        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Kost Favorit</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['favorites_count'] }}</p>
            </div>
        </x-card>

        <x-card class="p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400">Keluhan Berjalan</span>
                <p class="text-2xl font-black text-slate-900 leading-tight">{{ $stats['pending_issues_count'] }}</p>
            </div>
        </x-card>
    </div>

    <!-- Active Tenancy Showcase Card -->
    @if($activeRental)
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900">Hunian Kost Anda Saat Ini</h2>
            <x-card class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 to-emerald-950 text-white rounded-3xl overflow-hidden relative shadow-xl">
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-start gap-5">
                        <img
                            src="{{ $activeRental->unit->cover_image_url }}"
                            alt="{{ $activeRental->unit->name }}"
                            class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover ring-2 ring-emerald-400/40 shrink-0"
                        />
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <x-badge variant="success" size="sm">
                                    {{ $activeRental->status->label() }}
                                </x-badge>
                                <span class="text-xs font-mono text-emerald-300">#{{ $activeRental->code }}</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight">{{ $activeRental->unit->property->name }}</h3>
                            <p class="text-xs sm:text-sm text-slate-300 font-medium">
                                {{ $activeRental->unit->name }} ({{ $activeRental->unit->roomType->name }}) &bull; {{ $activeRental->unit->property->address }}
                            </p>
                            <p class="text-xs text-emerald-300 pt-1">
                                Masa Sewa: <strong>{{ $activeRental->start_date->format('d M Y') }}</strong> s/d <strong>{{ $activeRental->end_date->format('d M Y') }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-3 w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0 border-white/10 shrink-0">
                        <div class="text-left sm:text-right">
                            <span class="text-xs text-slate-400">Tarif Sewa:</span>
                            <p class="text-2xl font-black text-emerald-400">{{ $activeRental->formatted_monthly_rent }}<span class="text-xs text-slate-300 font-normal">/bln</span></p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-button variant="secondary" size="sm" href="{{ route('tenant.rentals.receipt', $activeRental) }}">
                                Kuitansi Sewa
                            </x-button>
                            <x-button variant="outline" size="sm" href="{{ route('tenant.issues.create', ['rental_id' => $activeRental->id]) }}" class="text-white border-white/30 hover:bg-white/10">
                                Lapor Masalah Kamar
                            </x-button>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    @endif

    <!-- Recent Bookings & Unresolved Issues -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Bookings -->
        <x-card class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Pengajuan Booking Terakhir</h3>
                <a href="{{ route('tenant.bookings.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>

            @if($recentBookings->isEmpty())
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada riwayat pengajuan booking.</p>
            @else
                <div class="space-y-3">
                    @foreach($recentBookings as $booking)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900">{{ $booking->unit->property->name }} ({{ $booking->unit->name }})</span>
                                <p class="text-slate-500 text-[11px]">Check-in: {{ $booking->check_in_date->format('d M Y') }}</p>
                            </div>
                            <x-badge :variant="$booking->status->color()" size="sm">
                                {{ $booking->status->label() }}
                            </x-badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        <!-- Unresolved Issues -->
        <x-card class="p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Tiket Keluhan / Perbaikan</h3>
                <a href="{{ route('tenant.issues.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Semua Tiket &rarr;</a>
            </div>

            @if($pendingIssues->isEmpty())
                <p class="text-xs text-slate-400 py-4 text-center">Tidak ada keluhan perbaikan yang sedang berjalan. Kamar aman!</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingIssues as $issue)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50/60 border border-amber-100 text-xs">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-900">{{ $issue->title }}</span>
                                <p class="text-slate-500 text-[11px]">{{ $issue->rental->unit->property->name }} &bull; {{ $issue->created_at->diffForHumans() }}</p>
                            </div>
                            <x-badge :variant="$issue->status->color()" size="sm">
                                {{ $issue->status->label() }}
                            </x-badge>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection
