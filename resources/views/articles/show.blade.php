@extends('layouts.app', [
    'title' => $article->meta_title ?? $article->title,
    'description' => $article->meta_description ?? $article->excerpt
])

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Breadcrumb -->
    <div>
        <a href="{{ route('articles.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-4">
            &larr; Kembali ke Semua Artikel
        </a>
        <div class="space-y-3">
            <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold uppercase">
                {{ $article->category }}
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                {{ $article->title }}
            </h1>
            <div class="flex items-center gap-4 text-xs text-slate-400 pt-2 border-b border-slate-100 pb-4">
                <span>Ditulis oleh <strong>{{ $article->author->name ?? 'Tim Editorial Rentiva' }}</strong></span>
                <span>&bull;</span>
                <span>{{ $article->published_at?->format('d M Y') ?? $article->created_at->format('d M Y') }}</span>
                <span>&bull;</span>
                <span>{{ $article->estimated_reading_time }} menit baca</span>
            </div>
        </div>
    </div>

    <!-- Featured Cover Image -->
    <div class="rounded-3xl overflow-hidden shadow-md max-h-96 w-full bg-slate-100">
        <img src="{{ $article->cover_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover" />
    </div>

    <!-- Article Body -->
    <div class="prose prose-slate max-w-none text-slate-800 text-sm sm:text-base leading-relaxed whitespace-pre-line">
        {!! $article->body !!}
    </div>

    <!-- Related Articles -->
    @if($relatedArticles->isNotEmpty())
        <div class="pt-12 border-t border-slate-200 space-y-6">
            <h3 class="text-xl font-bold text-slate-900">Artikel Terkait Lainnya</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $rel)
                    <x-card class="p-5 space-y-2 hover:shadow-md transition-shadow">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">{{ $rel->category }}</span>
                        <h4 class="text-sm font-bold text-slate-900 line-clamp-2">
                            <a href="{{ route('articles.show', $rel->slug) }}" class="hover:text-emerald-600">
                                {{ $rel->title }}
                            </a>
                        </h4>
                    </x-card>
                @endforeach
            </div>
        </div>
    @endif
</article>
@endsection
