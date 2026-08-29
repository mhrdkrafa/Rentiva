@extends('layouts.app', ['seo' => $seo])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div>
        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Perlindungan Data</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-1">Kebijakan Privasi</h1>
        <p class="text-xs text-slate-400 mt-1">Terakhir diperbarui: 29 Agustus 2026</p>
    </div>

    <x-card class="p-6 sm:p-10 bg-white prose prose-slate max-w-none text-xs sm:text-sm leading-relaxed space-y-6">
        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">1. Data yang Kami Kumpulkan</h3>
            <p class="text-slate-600">
                Kami mengumpulkan data pribadi yang Anda berikan saat membuat akun (nama, alamat email, nomor telepon WhatsApp), data identitas untuk verifikasi penyewa, serta riwayat transaksi sewa dan pembayaran.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">2. Penggunaan Data</h3>
            <p class="text-slate-600">
                Informasi Anda digunakan semata-mata untuk memfasilitasi proses sewa hunian, verifikasi keamanan pemesanan, penerbitan kontrak sewa resmi, dan pemrosesan pembayaran yang aman. Kami tidak akan pernah menjual data pribadi Anda kepada pihak ketiga.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">3. Keamanan Informasi</h3>
            <p class="text-slate-600">
                Seluruh data sensitif, kata sandi, dan dokumen identifikasi disimpan secara terenkripsi dengan standar keamanan tinggi untuk mencegah akses tidak sah.
            </p>
        </section>
    </x-card>
</div>
@endsection
