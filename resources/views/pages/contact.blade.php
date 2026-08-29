@extends('layouts.app', ['seo' => $seo])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
    <div class="text-center space-y-3">
        <span class="px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold uppercase tracking-wider">
            Hubungi Kami
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
            Pusat Bantuan & Layanan Pengguna
        </h1>
        <p class="text-sm text-slate-500 max-w-xl mx-auto">
            Tim layanan pelanggan Rentiva siap membantu Anda setiap hari untuk kebutuhan informasi sewa dan kemitraan pemilik kost.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="p-6 text-center space-y-3 bg-white" hover>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-xl">
                💬
            </div>
            <h4 class="font-bold text-slate-900 text-sm">WhatsApp Support</h4>
            <p class="text-xs text-slate-500">Respon cepat setiap hari 08:00 - 22:00 WIB</p>
            <a href="https://wa.me/6281234567890" target="_blank" class="inline-block text-xs font-bold text-emerald-600 hover:text-emerald-700">
                Chat via WhatsApp &rarr;
            </a>
        </x-card>

        <x-card class="p-6 text-center space-y-3 bg-white" hover>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-xl">
                ✉️
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Email Resmi</h4>
            <p class="text-xs text-slate-500">Untuk pertanyaan kemitraan & bantuan teknis</p>
            <a href="mailto:support@rentiva.id" class="inline-block text-xs font-bold text-teal-600 hover:text-teal-700">
                support@rentiva.id &rarr;
            </a>
        </x-card>

        <x-card class="p-6 text-center space-y-3 bg-white" hover>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-xl">
                🏢
            </div>
            <h4 class="font-bold text-slate-900 text-sm">Kantor Operasional</h4>
            <p class="text-xs text-slate-500">DI Yogyakarta & Jakarta, Indonesia</p>
            <span class="inline-block text-xs font-bold text-indigo-600">
                Senin - Jumat 09:00 - 17:00
            </span>
        </x-card>
    </div>
</div>
@endsection
