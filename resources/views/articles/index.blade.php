@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
            Pusat Edukasi & Inspirasi Hunian
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">Artikel, Tips & Panduan Sewa Kost</h1>
        <p class="text-sm sm:text-base text-slate-500">Kumpulan artikel bermanfaat untuk membantu penyewa menemukan kamar idaman dan panduan bagi pemilik properti.</p>
    </div>

    <!-- Filter Categories -->
    <div class="flex flex-wrap items-center justify-center gap-2">
        <a href="{{ route('articles.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ !request('category') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Semua Topik
        </a>
        @foreach($categories as $catKey => $catLabel)
            <a href="{{ route('articles.index', ['category' => $catKey]) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ request('category') === $catKey ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $catLabel }}
            </a>
        @endforeach
    </div>

    <!-- Articles Grid -->
    @if($articles->isEmpty())
        <x-card class="p-12 text-center space-y-3">
            <p class="text-sm font-bold text-slate-700">Belum Ada Artikel</p>
            <p class="text-xs text-slate-400">Artikel pada kategori ini belum tersedia.</p>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($articles as $article)
                <x-card class="overflow-hidden flex flex-col group hover:shadow-xl transition-all" hover>
                    <div class="relative h-52 bg-slate-100 overflow-hidden">
                        <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-xs text-[10px] font-bold text-white uppercase">
                            {{ $categories[$article->category] ?? $article->category }}
                        </span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <span class="text-[11px] text-slate-400 font-semibold">{{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }} &bull; {{ $article->estimated_reading_time }} min baca</span>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors mt-1.5 leading-snug">
                                <a href="{{ route('articles.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                                {{ $article->excerpt }}
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-600 group-hover:text-emerald-700 flex items-center gap-1">
                                Baca Artikel &rarr;
                            </span>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
