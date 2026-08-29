@extends('layouts.app')

@section('content')
<div class="space-y-16 py-8 md:py-12">
    <!-- Hero & Search Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 p-8 sm:p-12 lg:p-16 text-white overflow-hidden shadow-2xl">
            <!-- Decorative Glow -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 text-emerald-300 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Solusi Sewa Kost & Properti Modern di Indonesia
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15]">
                    Temukan Hunian Nyaman <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">Tanpa Ribet & Terpercaya</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                    Jelajahi ribuan pilihan kost eksklusif, kamar mahasiswa, apartemen, dan kontrakan dengan jaminan ketersediaan real-time dan kemudahan transaksi.
                </p>

                <!-- Search Box Bar -->
                <div class="pt-4">
                    <form action="{{ route('properties.index') }}" method="GET" class="bg-white p-2.5 sm:p-3 rounded-2xl shadow-xl flex flex-col md:flex-row items-center gap-3 text-slate-800">
                        <div class="flex-1 w-full flex items-center gap-3 px-3 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                name="q"
                                placeholder="Mau cari di kota, area, atau kampus mana? (contoh: Pogung, Dago, UI)"
                                class="w-full bg-transparent text-sm focus:outline-none placeholder-slate-400 font-medium"
                            />
                        </div>

                        <div class="w-full md:w-48 px-3 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                            <select name="gender" class="w-full bg-transparent text-sm focus:outline-none text-slate-700 font-medium cursor-pointer">
                                <option value="all">Semua Tipe Kost</option>
                                <option value="female_only">Kost Putri</option>
                                <option value="male_only">Kost Putra</option>
                                <option value="married_couples">Pasutri / Keluarga</option>
                            </select>
                        </div>

                        <x-button type="submit" variant="primary" size="md" class="w-full md:w-auto px-8 shrink-0">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Cari Sekarang
                        </x-button>
                    </form>
                </div>

                <!-- Quick Search Chips -->
                <div class="flex flex-wrap items-center gap-2 pt-2 text-xs text-slate-300">
                    <span class="text-slate-400 font-medium">Pencarian Cepat:</span>
                    <a href="{{ route('properties.index', ['q' => 'UGM']) }}" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 transition-colors">Dekat UGM Jogja</a>
                    <a href="{{ route('properties.index', ['q' => 'ITB']) }}" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 transition-colors">Dekat ITB Bandung</a>
                    <a href="{{ route('properties.index', ['q' => 'UI']) }}" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 transition-colors">Dekat UI Depok</a>
                    <a href="{{ route('properties.index', ['gender' => 'female_only']) }}" class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Kost Putri</a>
                    <a href="{{ route('properties.index', ['available_only' => '1']) }}" class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-400/30">Kamar Siap Huni</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Benefits & Trust Signals -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card class="bg-white p-6" hover>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Foto & Data Terverifikasi</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Setiap kamar dan fasilitas diinspeksi untuk memastikan kesesuaian antara foto listing dengan kondisi nyata.
                </p>
            </x-card>

            <x-card class="bg-white p-6" hover>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Ketersediaan Real-Time</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Sistem pemesanan otomatis mencegah double booking dengan kalender ketersediaan kamar yang selalu terbarui.
                </p>
            </x-card>

            <x-card class="bg-white p-6" hover>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Harga Transparan & Jelas</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Tanpa biaya tersembunyi. Rincian sewa, deposit, dan tagihan listrik/air terpampang transparan sebelum sewa.
                </p>
            </x-card>
        </div>
    </section>

    <!-- Call To Action for Owners -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-emerald-50 border border-emerald-100 p-8 sm:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-3 max-w-xl">
                <x-badge variant="primary">Untuk Pemilik Kost & Properti</x-badge>
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
                    Maksimalkan Okupansi Properti Anda Bersama Rentiva
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Kelola ketersediaan kamar, filter calon penyewa, dan terima pembayaran sewa secara teratur dengan sistem manajemen pemilik yang intuitif.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto shrink-0">
                <x-button variant="secondary" size="lg" href="{{ url('/owner/dashboard') }}">
                    Buka Dashboard Owner
                </x-button>
                <x-button variant="outline" size="lg" href="{{ url('/register') }}">
                    Daftar Sebagai Mitra
                </x-button>
            </div>
        </div>
    </section>
</div>
@endsection
