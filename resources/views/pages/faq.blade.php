@extends('layouts.app', ['seo' => $seo])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    <div class="text-center space-y-3">
        <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
            Pusat Bantuan
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Pertanyaan yang Sering Diajukan (FAQ)
        </h1>
        <p class="text-sm text-slate-500 max-w-xl mx-auto">
            Temukan jawaban cepat atas pertanyaan seputar pemesanan kamar, verifikasi kost, metode pembayaran, dan tata tertib hunian.
        </p>
    </div>

    @if($faqs->isEmpty())
        <x-card class="p-8 text-center bg-white space-y-2">
            <h4 class="font-bold text-slate-900">Belum ada FAQ terpublikasi</h4>
            <p class="text-xs text-slate-500">Silakan hubungi tim bantuan kami jika ada pertanyaan.</p>
        </x-card>
    @else
        <div class="space-y-4" x-data="{ active: null }">
            @foreach($faqs as $idx => $faq)
                <x-card class="overflow-hidden bg-white border border-slate-200/80">
                    <button
                        type="button"
                        @click="active = (active === {{ $idx }} ? null : {{ $idx }})"
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-slate-900 text-sm sm:text-base hover:text-emerald-600 transition-colors"
                    >
                        <span>{{ $faq->question }}</span>
                        <svg class="w-5 h-5 shrink-0 transition-transform duration-200" :class="active === {{ $idx }} ? 'rotate-180 text-emerald-600' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="active === {{ $idx }}" x-collapse class="px-5 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        {{ $faq->answer }}
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection
