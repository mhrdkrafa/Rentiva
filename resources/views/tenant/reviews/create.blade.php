@extends('layouts.tenant', ['title' => 'Tulis Ulasan Kamar'])

@section('tenant_content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('tenant.rentals.show', $rental) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Rincian Sewa
        </a>
        <h1 class="text-xl font-bold text-slate-900">Tulis Ulasan & Penilaian Hunian</h1>
        <p class="text-xs text-slate-500">Bagikan pengalaman nyata Anda tinggal di {{ $rental->unit->property->name }} untuk membantu calon penyewa lainnya.</p>
    </div>

    @if($errors->any())
        <x-alert variant="danger" :message="$errors->first()" />
    @endif

    <x-card class="p-6 sm:p-8 space-y-6 bg-white">
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black shrink-0">
                ⭐
            </div>
            <div>
                <p class="text-xs font-bold">{{ $rental->unit->property->name }} &bull; {{ $rental->unit->name }}</p>
                <p class="text-[11px] text-emerald-700">Masa Sewa: {{ $rental->start_date->format('d M Y') }} s/d {{ $rental->end_date->format('d M Y') }}</p>
            </div>
        </div>

        <form action="{{ route('tenant.reviews.store', $rental) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Overall Rating -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Rating Keseluruhan <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center gap-3" x-data="{ rating: 5 }">
                    <input type="hidden" name="rating" :value="rating" />
                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                        <button
                            type="button"
                            @click="rating = star"
                            class="text-3xl transition-transform hover:scale-110 focus:outline-none"
                            :class="star <= rating ? 'text-amber-400' : 'text-slate-300'"
                        >
                            ★
                        </button>
                    </template>
                    <span class="text-xs font-bold text-slate-700 ml-2" x-text="rating + ' / 5 Bintang'"></span>
                </div>
            </div>

            <!-- Multi-dimensional sub ratings -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Kebersihan Kamar & Properti</label>
                    <select name="cleanliness_rating" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <option value="5">⭐⭐⭐⭐⭐ Sangat Bersih (5)</option>
                        <option value="4">⭐⭐⭐⭐ Bersih (4)</option>
                        <option value="3">⭐⭐⭐ Cukup (3)</option>
                        <option value="2">⭐⭐ Kurang (2)</option>
                        <option value="1">⭐ Buruk (1)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Akurasi Foto & Fasilitas</label>
                    <select name="accuracy_rating" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <option value="5">⭐⭐⭐⭐⭐ Sangat Sesuai (5)</option>
                        <option value="4">⭐⭐⭐⭐ Sesuai (4)</option>
                        <option value="3">⭐⭐⭐ Cukup Sesuai (3)</option>
                        <option value="2">⭐⭐ Kurang Sesuai (2)</option>
                        <option value="1">⭐ Tidak Sesuai (1)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Komunikasi & Respon Pemilik</label>
                    <select name="communication_rating" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <option value="5">⭐⭐⭐⭐⭐ Sangat Ramah & Cepat (5)</option>
                        <option value="4">⭐⭐⭐⭐ Baik (4)</option>
                        <option value="3">⭐⭐⭐ Cukup (3)</option>
                        <option value="2">⭐⭐ Lambat (2)</option>
                        <option value="1">⭐ Tidak Responsif (1)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-700">Kenyamanan Lokasi & Lingkungan</label>
                    <select name="location_rating" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <option value="5">⭐⭐⭐⭐⭐ Sangat Strategis & Aman (5)</option>
                        <option value="4">⭐⭐⭐⭐ Strategis (4)</option>
                        <option value="3">⭐⭐⭐ Cukup (3)</option>
                        <option value="2">⭐⭐ Kurang Nyaman (2)</option>
                        <option value="1">⭐ Tidak Aman (1)</option>
                    </select>
                </div>
            </div>

            <!-- Review comment -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Cerita & Ulasan Pengalaman Tinggal <span class="text-rose-500">*</span>
                </label>
                <textarea
                    name="comment"
                    rows="4"
                    required
                    placeholder="Ceritakan bagaimana kenyamanan kamar, fasilitas bersama, kecepatan internet WiFi, dan keramahan pemilik kost..."
                    class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-xs sm:text-sm focus:border-emerald-500 focus:ring-emerald-500"
                ></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <x-button variant="outline" size="md" href="{{ route('tenant.rentals.show', $rental) }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md">
                    Kirim Ulasan Resmi
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
