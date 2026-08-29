@extends('layouts.app', ['seo' => $seo])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    <!-- Header Banner -->
    <div class="rounded-3xl bg-gradient-to-tr from-slate-900 via-emerald-950 to-teal-900 p-8 sm:p-12 text-white relative overflow-hidden shadow-2xl">
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-bold uppercase tracking-wider">
                🎉 Promo & Voucher Spesial
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                Hemat Lebih Banyak untuk Kamar Kost Impian Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                Gunakan kode voucher promo di bawah ini saat melakukan pembayaran sewa kamar pertama atau perpanjangan sewa Anda di Rentiva.
            </p>
        </div>
    </div>

    <!-- Promotions Grid -->
    @if($promotions->isEmpty())
        <x-card class="p-12 text-center space-y-4 bg-white">
            <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-2xl font-black">
                🎟️
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum Ada Kupon Promo yang Aktif</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Nantikan kampanye diskon dan voucher sewa kamar menarik berikutnya. Pantau terus halaman ini atau daftar akun untuk notifikasi promo terbaru!
            </p>
            <x-button variant="primary" size="md" href="{{ route('properties.index') }}">
                Jelajahi Semua Kost &rarr;
            </x-button>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{ copiedCode: null }">
            @foreach($promotions as $promo)
                <x-card class="p-6 space-y-4 bg-white border border-slate-200/80 hover:border-emerald-500/40 transition-all flex flex-col justify-between" hover>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-black uppercase">
                                {{ $promo->discount_type->label() }}
                            </span>
                            <span class="text-[11px] font-bold text-slate-400">
                                @if($promo->ends_at)
                                    s/d {{ $promo->ends_at->format('d M Y') }}
                                @else
                                    Promo Selamanya
                                @endif
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-slate-900">{{ $promo->name }}</h3>
                            <div class="text-2xl font-black text-emerald-600 mt-1">
                                {{ $promo->formatted_discount_label }}
                            </div>
                        </div>

                        <div class="text-xs text-slate-500 space-y-1 pt-2 border-t border-slate-100">
                            <p>&bull; Minimal Transaksi: <strong>{{ \App\Support\Money::format($promo->min_transaction_amount) }}</strong></p>
                            @if($promo->max_uses)
                                <p>&bull; Sisa Kuota: <strong>{{ max(0, $promo->max_uses - $promo->used_count) }} pemakai</strong></p>
                            @endif
                        </div>
                    </div>

                    <!-- Voucher Code & Copy Button -->
                    <div class="pt-4 border-t border-dashed border-slate-200 flex items-center justify-between gap-3 bg-slate-50 p-3 rounded-2xl">
                        <div class="font-mono font-black text-sm text-slate-900 tracking-wider">
                            {{ $promo->code }}
                        </div>
                        <button
                            type="button"
                            @click="navigator.clipboard.writeText('{{ $promo->code }}'); copiedCode = '{{ $promo->code }}'; setTimeout(() => copiedCode = null, 2500)"
                            class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-emerald-700 hover:bg-emerald-600 hover:text-white transition-colors"
                        >
                            <span x-text="copiedCode === '{{ $promo->code }}' ? '✓ Tersalin!' : 'Salin Kode'"></span>
                        </button>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection
