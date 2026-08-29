@extends('layouts.app', ['title' => 'Daftar Akun Baru — Rentiva'])

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8" x-data="{ selectedRole: '{{ old('role', $defaultRole ?? 'tenant') }}' }">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 mx-auto font-black text-xl">
                R
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Buat Akun Rentiva</h1>
            <p class="text-xs sm:text-sm text-slate-500">Mulai sewa kamar kost idaman atau kelola properti Anda dengan mudah.</p>
        </div>

        @if($errors->any())
            <x-alert variant="danger" :message="$errors->first()" />
        @endif

        <x-card class="p-6 sm:p-8 bg-white space-y-6 shadow-xl shadow-slate-200/50">
            <!-- Role Selection Pill Tabs -->
            <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 rounded-2xl text-xs font-bold">
                <button
                    type="button"
                    @click="selectedRole = 'tenant'"
                    class="py-2.5 rounded-xl transition-all text-center flex items-center justify-center gap-1.5"
                    :class="selectedRole === 'tenant' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                >
                    <span>🏠</span> Penyewa (Tenant)
                </button>
                <button
                    type="button"
                    @click="selectedRole = 'owner'"
                    class="py-2.5 rounded-xl transition-all text-center flex items-center justify-center gap-1.5"
                    :class="selectedRole === 'owner' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-900'"
                >
                    <span>🏢</span> Pemilik Kost (Owner)
                </button>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" :value="selectedRole" />

                <div>
                    <x-input
                        type="text"
                        name="name"
                        label="Nama Lengkap *"
                        value="{{ old('name') }}"
                        placeholder="contoh: Mahardika Rafa"
                        required
                        autofocus
                    />
                </div>

                <div>
                    <x-input
                        type="email"
                        name="email"
                        label="Alamat Email *"
                        value="{{ old('email') }}"
                        placeholder="contoh: rafa@gmail.com"
                        required
                    />
                </div>

                <div>
                    <x-input
                        type="text"
                        name="phone"
                        label="Nomor WhatsApp / HP *"
                        value="{{ old('phone') }}"
                        placeholder="contoh: 081234567890"
                        required
                    />
                </div>

                <div>
                    <x-input
                        type="password"
                        name="password"
                        label="Kata Sandi *"
                        placeholder="Minimal 8 karakter"
                        required
                    />
                </div>

                <div>
                    <x-input
                        type="password"
                        name="password_confirmation"
                        label="Ulangi Kata Sandi *"
                        placeholder="Ketik ulang kata sandi"
                        required
                    />
                </div>

                <div class="pt-2">
                    <x-button type="submit" variant="primary" size="lg" class="w-full justify-center text-sm font-bold shadow-lg shadow-emerald-600/20">
                        <span x-text="selectedRole === 'owner' ? 'Daftar sebagai Pemilik Kost' : 'Daftar sebagai Penyewa'"></span>
                    </x-button>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1">
                    Masuk Sekarang
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
