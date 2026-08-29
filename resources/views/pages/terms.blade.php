@extends('layouts.app', ['seo' => $seo])

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div>
        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Ketentuan Penggunaan</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-1">Syarat & Ketentuan Layanan</h1>
        <p class="text-xs text-slate-400 mt-1">Terakhir diperbarui: 29 Agustus 2026</p>
    </div>

    <x-card class="p-6 sm:p-10 bg-white prose prose-slate max-w-none text-xs sm:text-sm leading-relaxed space-y-6">
        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">1. Ketentuan Umum</h3>
            <p class="text-slate-600">
                Selamat datang di Rentiva. Dengan mendaftar, mengakses, atau menggunakan layanan platform sewa Rentiva, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat secara hukum dengan Syarat & Ketentuan ini.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">2. Akun Pengguna & Kewajiban</h3>
            <p class="text-slate-600">
                Setiap pengguna bertanggung jawab penuh atas kerahasiaan kredensial akun mereka. Pengguna dilarang memberikan informasi identitas palsu, menyewakan kamar secara ilegal, atau melakukan aktivitas yang melanggar hukum di Indonesia.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">3. Pemesanan, Pembayaran & Deposit</h3>
            <p class="text-slate-600">
                Seluruh transaksi pembayaran sewa dan uang jaminan (deposit held) wajib diproses melalui gerbang pembayaran resmi Rentiva. Biaya deposit akan ditahan secara aman selama masa sewa aktif dan dikembalikan setelah penyewa menyelesaikan check-out tanpa adanya kerusakan unit.
            </p>
        </section>

        <section class="space-y-2">
            <h3 class="text-base font-bold text-slate-900">4. Pembatalan & Pengembalian Dana (Refund)</h3>
            <p class="text-slate-600">
                Ketentuan pembatalan tunduk pada kebijakan pembatalan properti yang dipilih saat pengajuan sewa. Pengembalian dana akan diproses kembali ke rekening atau metode pembayaran asal penyewa sesuai alur finansial Rentiva.
            </p>
        </section>
    </x-card>
</div>
@endsection
