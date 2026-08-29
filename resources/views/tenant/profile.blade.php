@extends('layouts.tenant', ['title' => 'Profil Saya', 'headerTitle' => 'Pengaturan Profil Penyewa'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Informasi Profil Penyewa</h2>
        <p class="text-sm text-slate-500 mt-1">Lengkapi data diri dan kontak darurat Anda untuk mempermudah proses verifikasi dan sewa hunian.</p>
    </div>

    <form action="{{ route('tenant.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Avatar & Basic Details Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Foto Profil & Data Pribadi
            </h3>

            <div class="flex flex-col sm:flex-row items-center gap-6 pb-2">
                <x-avatar :src="$user->avatar_url" :name="$user->name" size="xl" />
                <div class="space-y-1.5 text-center sm:text-left">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unggah Foto Baru</label>
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
                    label="Nama Lengkap"
                    :value="$user->name"
                    required
                />

                <x-input
                    name="email"
                    label="Alamat Email"
                    :value="$user->email"
                    disabled
                    helper="Email akun tidak dapat diubah sembarangan."
                />

                <x-input
                    name="phone"
                    label="Nomor WhatsApp / Telepon"
                    :value="$user->phone"
                    placeholder="Contoh: 081234567890"
                />

                <x-select
                    name="gender"
                    label="Jenis Kelamin"
                    :selected="$profile?->gender"
                    :options="['male' => 'Laki-laki', 'female' => 'Perempuan']"
                    placeholder="Pilih jenis kelamin"
                />

                <x-input
                    type="date"
                    name="date_of_birth"
                    label="Tanggal Lahir"
                    :value="$profile?->date_of_birth?->format('Y-m-d')"
                />

                <x-input
                    name="occupation"
                    label="Pekerjaan / Status"
                    :value="$profile?->occupation"
                    placeholder="Contoh: Mahasiswa, Karyawan Swasta"
                />
            </div>

            <x-textarea
                name="bio"
                label="Tentang Saya (Bio Singkat)"
                :value="$profile?->bio"
                placeholder="Ceritakan sedikit tentang kebiasaan atau preferensi hunian Anda..."
                rows="3"
            />
        </x-card>

        <!-- Emergency Contact Card -->
        <x-card class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Kontak Darurat
            </h3>
            <p class="text-xs text-slate-500">Informasi ini bersifat privat dan hanya digunakan oleh pengelola sewa pada situasi darurat.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <x-input
                    name="emergency_contact_name"
                    label="Nama Kontak Darurat"
                    :value="$profile?->emergency_contact_name"
                    placeholder="Nama orang tua/wali"
                />

                <x-input
                    name="emergency_contact_phone"
                    label="Nomor Telepon Darurat"
                    :value="$profile?->emergency_contact_phone"
                    placeholder="0812xxxxxxxx"
                />

                <x-input
                    name="emergency_contact_relation"
                    label="Hubungan"
                    :value="$profile?->emergency_contact_relation"
                    placeholder="Contoh: Orang Tua, Saudara"
                />
            </div>
        </x-card>

        <div class="flex justify-end gap-3 pt-2">
            <x-button type="submit" variant="primary" size="lg" class="px-8">
                Simpan Perubahan Profil
            </x-button>
        </div>
    </form>
</div>
@endsection
