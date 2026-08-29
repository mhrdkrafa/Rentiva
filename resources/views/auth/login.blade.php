@extends('layouts.app', ['title' => 'Masuk ke Akun Anda — Rentiva'])

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 mx-auto font-black text-xl">
                R
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-xs sm:text-sm text-slate-500">Masuk untuk mengelola sewa, pembayaran, atau properti kost Anda.</p>
        </div>

        @if($errors->any())
            <x-alert variant="danger" :message="$errors->first()" />
        @endif

        @if(session('status'))
            <x-alert variant="success" :message="session('status')" />
        @endif

        <x-card class="p-6 sm:p-8 bg-white space-y-6 shadow-xl shadow-slate-200/50">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <x-input
                        type="email"
                        name="email"
                        label="Alamat Email *"
                        value="{{ old('email') }}"
                        placeholder="contoh: budi@gmail.com"
                        required
                        autofocus
                    />
                </div>

                <div>
                    <x-input
                        type="password"
                        name="password"
                        label="Kata Sandi *"
                        placeholder="••••••••"
                        required
                    />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500" />
                        <span>Ingat saya di perangkat ini</span>
                    </label>

                    <a href="{{ url('/admin/login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700">
                        Masuk Admin CMS &rarr;
                    </a>
                </div>

                <x-button type="submit" variant="primary" size="lg" class="w-full justify-center text-sm font-bold shadow-lg shadow-emerald-600/20">
                    Masuk Sekarang
                </x-button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                Belum memiliki akun?
                <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-700 ml-1">
                    Daftar Akun Baru
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
