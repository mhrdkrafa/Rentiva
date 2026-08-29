@extends('layouts.owner', ['title' => 'Tambah Properti Baru', 'headerTitle' => 'Pendaftaran Properti'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('owner.properties.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Properti
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Formulir Pendaftaran Properti Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Lengkapi informasi dasar properti. Setelah properti tersimpan, Anda dapat menambahkan kamar/unit dan paket harga.</p>
    </div>

    <form action="{{ route('owner.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Main Info Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Informasi Pokok
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <x-input
                        name="name"
                        label="Nama Properti / Bangunan Kost"
                        placeholder="Contoh: Kost Griya Asri Sleman"
                        required
                    />
                </div>

                <x-select
                    name="property_type_id"
                    label="Tipe Properti"
                    :options="$propertyTypes->pluck('name', 'id')->toArray()"
                    placeholder="Pilih tipe properti"
                    required
                />

                <x-select
                    name="location_id"
                    label="Kota / Lokasi"
                    :options="$locations->pluck('name', 'id')->toArray()"
                    placeholder="Pilih lokasi kota"
                    required
                />

                <x-select
                    name="gender_policy"
                    label="Kebijakan Penghuni"
                    :options="[
                        'all' => 'Campur / Semua Kalangan',
                        'male_only' => 'Khusus Putra (Pria)',
                        'female_only' => 'Khusus Putri (Wanita)',
                        'married_couples' => 'Khusus Pasutri / Keluarga',
                    ]"
                    placeholder="Pilih aturan penghuni"
                    required
                />

                <x-select
                    name="public_location_precision"
                    label="Tampilan Presisi Lokasi Publik"
                    :options="[
                        'approximate' => 'Perkiraan Area (Rekomendasi)',
                        'exact' => 'Alamat Tepat',
                        'area_only' => 'Hanya Kelurahan/Kecamatan',
                    ]"
                    default="approximate"
                />

                <div class="sm:col-span-2">
                    <x-textarea
                        name="address"
                        label="Alamat Lengkap Properti"
                        placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan terdekat..."
                        rows="2"
                        required
                    />
                </div>

                <div class="sm:col-span-2">
                    <x-textarea
                        name="description"
                        label="Deskripsi Properti"
                        placeholder="Jelaskan keunggulan properti, lingkungan sekitar, akses kampus/jalan utama, dan aturan umum..."
                        rows="4"
                        required
                    />
                </div>
            </div>
        </x-card>

        <!-- Facilities Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Fasilitas Gedung / Properti
            </h3>
            <p class="text-xs text-slate-500">Pilih fasilitas yang tersedia bersama di lingkungan properti.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($facilities as $facility)
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-200 cursor-pointer transition-colors text-xs font-medium text-slate-700">
                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}" class="rounded text-emerald-600 focus:ring-emerald-500" />
                        <span>{{ $facility->name }}</span>
                    </label>
                @endforeach
            </div>
        </x-card>

        <!-- Photos Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Foto Bangunan / Tampak Depan
            </h3>

            <div class="space-y-2">
                <input
                    type="file"
                    name="photos[]"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                />
                <p class="text-[11px] text-slate-400">Pilih beberapa foto (tampak depan, area parkir, ruang tamu/bersama). Foto pertama akan menjadi sampul utama.</p>
            </div>
        </x-card>

        <div class="flex justify-end gap-3 pt-2">
            <x-button type="button" variant="ghost" href="{{ route('owner.properties.index') }}">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="lg" class="px-8">
                Simpan & Lanjutkan Tambah Unit
            </x-button>
        </div>
    </form>
</div>
@endsection
