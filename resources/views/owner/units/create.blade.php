@extends('layouts.owner', ['title' => 'Tambah Unit / Kamar', 'headerTitle' => 'Tambah Unit Baru'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('owner.properties.show', $property) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Properti ({{ $property->name }})
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Unit / Kamar Baru</h2>
        <p class="text-sm text-slate-500 mt-1">Daftarkan spesifikasi kamar, fasilitas privat, dan skema paket harga sewa.</p>
    </div>

    <form action="{{ route('owner.units.store', $property) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Unit Details Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                </svg>
                Spesifikasi Kamar / Unit
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input
                    name="name"
                    label="Nama / Nomor Kamar"
                    placeholder="Contoh: Kamar 101, Kamar A-2"
                    required
                />

                <x-select
                    name="room_type_id"
                    label="Tipe Kamar"
                    :options="$roomTypes->pluck('name', 'id')->toArray()"
                    placeholder="Pilih tipe kamar"
                    required
                />

                <x-input
                    name="floor"
                    label="Lantai"
                    placeholder="Contoh: Lantai 1, Lantai 2"
                />

                <x-input
                    name="size"
                    label="Ukuran Kamar (Dimensi)"
                    placeholder="Contoh: 3x4 meter"
                />

                <x-input
                    type="number"
                    name="capacity"
                    label="Kapasitas Maksimal (Orang)"
                    value="1"
                    min="1"
                    required
                />

                <x-select
                    name="status"
                    label="Status Ketersediaan"
                    :options="[
                        'available' => 'Tersedia (Siap Huni)',
                        'occupied' => 'Sedang Terisi',
                        'maintenance' => 'Dalam Perbaikan',
                    ]"
                    default="available"
                    required
                />

                <div class="sm:col-span-2">
                    <x-textarea
                        name="description"
                        label="Catatan Khusus Kamar (Opsional)"
                        placeholder="Contoh: Menghadap ke taman belakang, sirkulasi udara sangat bagus..."
                        rows="2"
                    />
                </div>
            </div>
        </x-card>

        <!-- Pricing Plans Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Skema Harga Sewa (Nominal Bulanan Utama)
            </h3>
            <p class="text-xs text-slate-500">Masukkan nominal sewa dalam Rupiah bulat tanpa titik/koma (contoh: 1500000 untuk Rp 1.500.000).</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <input type="hidden" name="price_plans[0][billing_period]" value="monthly" />

                <x-input
                    type="number"
                    name="price_plans[0][amount]"
                    label="Harga Sewa Bulanan (IDR) *"
                    placeholder="1500000"
                    required
                />

                <x-input
                    type="number"
                    name="price_plans[0][deposit_amount]"
                    label="Deposit Jaminan (Opsional)"
                    placeholder="500000"
                />

                <div class="sm:col-span-1 flex items-end pb-1.5">
                    <span class="text-xs text-slate-500">Periode: <strong>Bulanan</strong> (Dapat ditambah paket harian/tahunan nanti)</span>
                </div>
            </div>
        </x-card>

        <!-- Room Facilities Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Fasilitas Khusus Kamar Ini
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($roomFacilities as $facility)
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
                Foto Interior Kamar
            </h3>

            <div class="space-y-2">
                <input
                    type="file"
                    name="photos[]"
                    multiple
                    accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                />
                <p class="text-[11px] text-slate-400">Unggah foto tempat tidur, meja belajar, kamar mandi dalam jika ada.</p>
            </div>
        </x-card>

        <div class="flex justify-end gap-3 pt-2">
            <x-button type="button" variant="ghost" href="{{ route('owner.properties.show', $property) }}">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="lg" class="px-8">
                Simpan Unit / Kamar
            </x-button>
        </div>
    </form>
</div>
@endsection
