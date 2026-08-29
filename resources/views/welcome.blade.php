@extends('layouts.app')

@section('content')
<div class="space-y-20 py-8 md:py-12">
    <!-- 1. Hero & Search Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative rounded-3xl bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 p-8 sm:p-12 lg:p-16 text-white overflow-hidden shadow-2xl">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 text-emerald-300 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ \App\Models\WebsiteSetting::get('site_tagline', 'Sewa Kost & Kamar Praktis, Aman & Terpercaya') }}
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
                                placeholder="Cari di kota, area, atau dekat kampus mana? (contoh: Pogung, UGM, Sleman)"
                                class="w-full bg-transparent text-sm focus:outline-none placeholder-slate-400 font-medium"
                            />
                        </div>

                        <div class="w-full md:w-48 px-3 py-2 border-b md:border-b-0 md:border-r border-slate-200">
                            <select name="gender" class="w-full bg-transparent text-sm focus:outline-none text-slate-700 font-medium cursor-pointer">
                                <option value="all">Semua Tipe Kost</option>
                                <option value="female_only">Kost Putri</option>
                                <option value="male_only">Kost Putra</option>
                                <option value="married_couples">Pasutri / Campur</option>
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
                    <a href="{{ route('properties.index', ['q' => 'UNY']) }}" class="px-3 py-1 rounded-full bg-white/10 hover:bg-white/20 transition-colors">Dekat UNY</a>
                    <a href="{{ route('properties.index', ['gender' => 'female_only']) }}" class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Kost Putri</a>
                    <a href="{{ route('properties.index', ['available_only' => '1']) }}" class="px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-400/30">Kamar Siap Huni</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Value Proposition Stats & Trust Signals -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-card class="bg-white p-6" hover>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Foto & Data Terverifikasi</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
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
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Sistem pemesanan otomatis mencegah double booking dengan kalender ketersediaan kamar yang selalu terbarui.
                </p>
            </x-card>

            <x-card class="bg-white p-6" hover>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Kuitansi Digital & Bantuan Cepat</h3>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Bukti sewa resmi tersimpan rapi, dan fitur keluhan fasilitas memastikan kamar Anda selalu dalam kondisi terbaik.
                </p>
            </x-card>
        </div>
    </section>

    <!-- 3. Featured Properties -->
    @if($featuredProperties->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kost Pilihan Rekomendasi</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Paling diminati dengan fasilitas lengkap dan lokasi terbaik.</p>
                </div>
                <a href="{{ route('properties.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Lihat Semua Kost &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredProperties as $prop)
                    <x-property-card :property="$prop" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- 4. Testimonials Section -->
    @if($testimonials->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Cerita Bahagia Pengguna Rentiva</h2>
                <p class="text-xs sm:text-sm text-slate-500">Pengalaman nyata dari penyewa dan pemilik kost yang menggunakan platform kami.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $t)
                    <x-card class="p-6 space-y-4 bg-white" hover>
                        <div class="flex items-center gap-1 text-amber-400 text-sm">
                            {{ str_repeat('⭐', $t->rating) }}
                        </div>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed italic">
                            "{{ $t->content }}"
                        </p>
                        <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                            <x-avatar :name="$t->name" size="md" />
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $t->name }}</h4>
                                <p class="text-[11px] text-slate-400">{{ $t->role }}</p>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 5. FAQ Accordion Section -->
    @if($faqs->isNotEmpty())
        <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" id="faq">
            <div class="text-center space-y-2 mb-10">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                <p class="text-xs sm:text-sm text-slate-500">Jawaban lengkap seputar cara sewa, pembayaran aman, dan tata tertib hunian.</p>
            </div>

            <div class="space-y-4" x-data="{ openFaq: null }">
                @foreach($faqs as $index => $faq)
                    <x-card class="overflow-hidden border border-slate-200/80">
                        <button
                            type="button"
                            @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                            class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm sm:text-base hover:text-emerald-600 transition-colors"
                        >
                            <span>{{ $faq->question }}</span>
                            <svg class="w-5 h-5 shrink-0 transition-transform duration-200" :class="openFaq === {{ $index }} ? 'rotate-180 text-emerald-600' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === {{ $index }}" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                            {{ $faq->answer }}
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 6. Educational Articles Section -->
    @if($latestArticles->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Artikel & Panduan Edukasi</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Tips memilih kost, gaya hidup, dan strategi mengelola properti.</p>
                </div>
                <a href="{{ route('articles.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Semua Artikel &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($latestArticles as $article)
                    <x-card class="overflow-hidden flex flex-col group hover:shadow-lg transition-all" hover>
                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-xs text-[10px] font-bold text-white uppercase">
                                {{ $article->category }}
                            </span>
                        </div>
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }} &bull; {{ $article->estimated_reading_time }} min baca</span>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-emerald-600 transition-colors mt-1 line-clamp-2">
                                    <a href="{{ route('articles.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                    {{ $article->excerpt }}
                                </p>
                            </div>
                            <div class="pt-3 border-t border-slate-100">
                                <a href="{{ route('articles.show', $article->slug) }}" class="text-xs font-bold text-emerald-600 group-hover:text-emerald-700 flex items-center gap-1">
                                    Baca Selengkapnya &rarr;
                                </a>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 7. Owner CTA Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-emerald-600 p-8 sm:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-8 shadow-xl shadow-emerald-600/20">
            <div class="space-y-2 max-w-xl text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Punya Properti atau Kamar Kost Kosong?</h2>
                <p class="text-emerald-100 text-xs sm:text-sm leading-relaxed">
                    Daftarkan properti Anda sekarang, dapatkan verifikasi gratis, dan raih potensi okupansi maksimal dengan manajemen digital bebas repot.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <x-button variant="white" size="lg" href="{{ route('owner.properties.create') }}" class="font-bold text-emerald-800 shadow-md">
                    Daftarkan Properti Saya
                </x-button>
            </div>
        </div>
    </section>
</div>
@endsection
