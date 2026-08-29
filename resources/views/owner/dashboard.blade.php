@extends('layouts.owner', ['title' => 'Ringkasan Pemilik', 'headerTitle' => 'Dashboard Manajemen Properti'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    <!-- Welcome Header & Quick Action -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang, {{ auth()->user()->name ?? 'Pemilik' }}!</h2>
            <p class="text-sm text-slate-500 mt-0.5">Berikut ikhtisar performa dan aktivitas properti sewa Anda hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" href="{{ url('/owner/properties/create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Listing Baru
            </x-button>
        </div>
    </div>

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-card class="p-5" hover>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Properti</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">0</p>
            <p class="text-xs text-slate-500 mt-1">Listing terdaftar di marketplace</p>
        </x-card>

        <x-card class="p-5" hover>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tingkat Okupansi</span>
                <span class="p-2 rounded-xl bg-teal-50 text-teal-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">0%</p>
            <p class="text-xs text-slate-500 mt-1">0 dari 0 kamar terisi</p>
        </x-card>

        <x-card class="p-5" hover>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Permintaan Masuk</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">0</p>
            <p class="text-xs text-slate-500 mt-1">Permintaan sewa baru</p>
        </x-card>

        <x-card class="p-5" hover>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Estimasi Pendapatan</span>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">Rp 0</p>
            <p class="text-xs text-slate-500 mt-1">Bulan ini</p>
        </x-card>
    </div>

    <!-- Empty State Guide -->
    <x-card class="p-8 bg-white border border-slate-200">
        <div class="max-w-2xl mx-auto text-center space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Belum Ada Listing Properti yang Didaftarkan</h3>
            <p class="text-sm text-slate-600 leading-relaxed">
                Mulai daftarkan kost, apartemen, atau rumah sewa Anda untuk menjangkau calon penyewa berkualitas. Tambahkan foto menarik, fasilitas, tipe kamar, dan tentukan skema harga sewa.
            </p>
            <div class="pt-2">
                <x-button variant="primary" size="lg" href="{{ url('/owner/properties/create') }}">
                    Daftarkan Properti Pertama
                </x-button>
            </div>
        </div>
    </x-card>
</div>
@endsection
