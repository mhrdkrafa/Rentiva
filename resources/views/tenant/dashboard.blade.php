@extends('layouts.tenant', ['title' => 'Ringkasan Penyewa', 'headerTitle' => 'Selamat Datang di Rentiva'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg shadow-emerald-700/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold backdrop-blur-xs">
                Portal Hunian Terpadu
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight">Halo, {{ auth()->user()->name ?? 'Penyewa' }} 👋</h2>
            <p class="text-sm text-emerald-100 max-w-xl">
                Semua informasi seputar sewa kamar, tanggal jatuh tempo tagihan, dan chat pemilik properti dapat Anda pantau di sini secara transparan.
            </p>
        </div>
        <x-button variant="outline" size="md" href="{{ url('/search') }}" class="bg-white text-emerald-800 hover:bg-emerald-50 border-transparent shadow-xs shrink-0">
            Jelajahi Properti Lain
        </x-button>
    </div>

    <!-- Overview Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card class="p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sewa Aktif</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">0</p>
            <p class="text-xs text-slate-500 mt-1">Belum ada sewa aktif berjalan</p>
        </x-card>

        <x-card class="p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pengajuan Booking</span>
                <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">0</p>
            <p class="text-xs text-slate-500 mt-1">Menunggu konfirmasi pemilik</p>
        </x-card>

        <x-card class="p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tagihan Jatuh Tempo</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">Rp 0</p>
            <p class="text-xs text-emerald-600 font-medium mt-1">Tidak ada tagihan tertunggak</p>
        </x-card>

        <x-card class="p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Favorit Tersimpan</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-2">0</p>
            <p class="text-xs text-slate-500 mt-1">Daftar properti yang Anda simpan</p>
        </x-card>
    </div>

    <!-- Active Rental Empty State / Summary -->
    <x-card class="p-8 text-center bg-white border border-dashed border-slate-300">
        <div class="max-w-md mx-auto space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Sewa Properti yang Berjalan</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Cari kamar kost impian Anda, ajukan sewa dengan tanggal check-in yang fleksibel, dan nikmati kemudahan sewa di Rentiva.
                </p>
            </div>
            <x-button variant="primary" size="md" href="{{ url('/search') }}">
                Mulai Cari Hunian
            </x-button>
        </div>
    </x-card>
</div>
@endsection
