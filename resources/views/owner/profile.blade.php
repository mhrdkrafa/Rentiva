@extends('layouts.owner', ['title' => 'Profil Pemilik & Rekening', 'headerTitle' => 'Pengaturan Profil Pemilik'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Profil Mitra Pemilik Properti</h2>
        <p class="text-sm text-slate-500 mt-1">Lengkapi data usaha dan nomor rekening bank untuk pencairan hasil sewa dari penyewa.</p>
    </div>

    <form action="{{ route('owner.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Basic Owner Info -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Identitas Pemilik & Usaha
            </h3>

            <div class="flex flex-col sm:flex-row items-center gap-6 pb-2">
                <x-avatar :src="$user->avatar_url" :name="$user->name" size="xl" />
                <div class="space-y-1.5 text-center sm:text-left">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unggah Foto / Logo Usaha</label>
                    <input
                        type="file"
                        name="avatar"
                        accept="image/jpeg,image/png,image/webp"
                        class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400">Format PNG, JPG, atau WEBP (Maksimal 5MB).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input
                    name="name"
                    label="Nama Lengkap Penanggung Jawab"
                    :value="$user->name"
                    required
                />

                <x-input
                    name="email"
                    label="Alamat Email"
                    :value="$user->email"
                    disabled
                />

                <x-input
                    name="phone"
                    label="Nomor WhatsApp Pengelola"
                    :value="$user->phone"
                    placeholder="0812xxxxxxxx"
                />

                <x-input
                    name="company_name"
                    label="Nama Usaha / Manajemen Properti (Opsional)"
                    :value="$ownerProfile?->company_name"
                    placeholder="Contoh: Kost Griya Melati Group"
                />

                <x-input
                    name="tax_number"
                    label="NPWP / Nomor Pokok Wajib Pajak (Opsional)"
                    :value="$ownerProfile?->tax_number"
                    placeholder="Nomor NPWP jika ada"
                />
            </div>
        </x-card>

        <!-- Payout Bank Account Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Rekening Pencairan Dana Sewa
            </h3>
            <p class="text-xs text-slate-500">Pastikan nama pemegang rekening sesuai dengan nama pemilik untuk memperlancar transfer hasil sewa.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <x-select
                    name="bank_name"
                    label="Nama Bank"
                    :selected="$ownerProfile?->bank_name"
                    :options="[
                        'BCA' => 'Bank Central Asia (BCA)',
                        'Mandiri' => 'Bank Mandiri',
                        'BRI' => 'Bank Rakyat Indonesia (BRI)',
                        'BNI' => 'Bank Negara Indonesia (BNI)',
                        'BSI' => 'Bank Syariah Indonesia (BSI)',
                        'CIMB' => 'CIMB Niaga',
                    ]"
                    placeholder="Pilih Bank"
                />

                <x-input
                    name="bank_account_number"
                    label="Nomor Rekening"
                    :value="$ownerProfile?->bank_account_number"
                    placeholder="Contoh: 1234567890"
                />

                <x-input
                    name="bank_account_holder"
                    label="Nama Pemilik Rekening"
                    :value="$ownerProfile?->bank_account_holder"
                    placeholder="Nama tertera di buku tabungan"
                />
            </div>
        </x-card>

        <div class="flex justify-end gap-3 pt-2">
            <x-button type="submit" variant="primary" size="lg" class="px-8">
                Simpan Profil Pemilik
            </x-button>
        </div>
    </form>
</div>
@endsection
