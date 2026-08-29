@extends('layouts.tenant', ['title' => 'Pembayaran Tagihan ' . $invoice->code])

@section('tenant_content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <a href="{{ route('tenant.invoices.show', $invoice) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Rincian Faktur
        </a>
        <h1 class="text-xl font-bold text-slate-900">Pembayaran Tagihan Sewa</h1>
        <p class="text-xs text-slate-500">Pilih metode pembayaran aman untuk melunasi tagihan sewa kamar Anda.</p>
    </div>

    @if(session('success'))
        <x-alert variant="success" :message="session('success')" />
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Order Summary Card -->
        <div class="md:col-span-1 space-y-4">
            <x-card class="p-5 space-y-4 bg-slate-50 border-slate-200">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ringkasan Tagihan</h3>
                <div class="space-y-1">
                    <p class="font-mono text-xs font-bold text-slate-700">{{ $invoice->code }}</p>
                    <p class="text-xs text-slate-600 font-semibold">{{ $invoice->bookingRequest?->unit?->property?->name }}</p>
                    <p class="text-[11px] text-slate-400">Unit: {{ $invoice->bookingRequest?->unit?->name }}</p>
                </div>
                <div class="pt-3 border-t border-slate-200">
                    <p class="text-[10px] text-slate-400 font-semibold uppercase">Total Pembayaran</p>
                    <p class="text-lg font-extrabold text-emerald-600">{{ $invoice->formatted_total_amount }}</p>
                </div>
            </x-card>
        </div>

        <!-- Payment Action Area -->
        <div class="md:col-span-2 space-y-6">
            @if($latestPayment && $latestPayment->status === \App\Enums\PaymentStatus::PENDING)
                <!-- Active Payment Intent Instructions -->
                <x-card class="p-6 space-y-6 border-emerald-300 ring-2 ring-emerald-500/10">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Instruksi Pembayaran Aktif</span>
                            <h3 class="text-base font-bold text-slate-900">{{ $latestPayment->payment_method->label() }}</h3>
                        </div>
                        <x-badge variant="warning" size="sm">Menunggu Pembayaran</x-badge>
                    </div>

                    @php
                        $instructions = $latestPayment->gateway_payload['instructions'] ?? [];
                    @endphp

                    @if(!empty($instructions['va_number']))
                        <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-2">
                            <p class="text-[11px] text-slate-400 font-semibold uppercase">Nomor Virtual Account ({{ $instructions['bank'] ?? 'Bank' }})</p>
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-xl font-black text-emerald-400 tracking-wider">{{ $instructions['va_number'] }}</span>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $instructions['va_number'] }}'); alert('Nomor VA disalin!')" class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-semibold text-white transition-colors">
                                    Salin
                                </button>
                            </div>
                        </div>
                    @elseif(!empty($instructions['qr_string']))
                        <div class="text-center space-y-3 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                            <div class="w-48 h-48 mx-auto bg-white p-3 rounded-xl border border-slate-200 flex items-center justify-center shadow-xs">
                                <div class="text-center font-mono text-[10px] text-slate-400 p-4 border border-dashed border-slate-300 rounded-lg">
                                    [QRIS CODE AKTIF]<br>
                                    Scan via GoPay / OVO / Dana / BCA
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 font-semibold">Scan QRIS di atas dengan aplikasi e-wallet atau mobile banking favorit Anda.</p>
                        </div>
                    @endif

                    <div class="text-xs text-slate-500 space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="font-bold text-slate-700">Panduan Pembayaran:</p>
                        <ol class="list-decimal list-inside space-y-1 text-[11px]">
                            <li>Gunakan nominal pas: <strong>{{ $invoice->formatted_total_amount }}</strong></li>
                            <li>Transaksi diverifikasi secara otomatis dalam hitungan detik.</li>
                            <li>Kuitansi resmi dan status sewa akan langsung aktif setelah pembayaran diterima.</li>
                        </ol>
                    </div>
                </x-card>
            @endif

            <!-- Method Selection Form -->
            <x-card class="p-6 space-y-6">
                <h3 class="text-sm font-bold text-slate-900">
                    {{ $latestPayment ? 'Ubah atau Pilih Metode Pembayaran Lain' : 'Pilih Metode Pembayaran' }}
                </h3>

                <form action="{{ route('tenant.invoices.process-checkout', $invoice) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="bank_transfer" class="text-emerald-600 focus:ring-emerald-500" checked />
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-900">Virtual Account Bank (BCA, Mandiri, BNI, BRI)</p>
                                <p class="text-[11px] text-slate-400">Verifikasi instan tanpa perlu unggah bukti transfer.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="qris" class="text-emerald-600 focus:ring-emerald-500" />
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-900">QRIS (GoPay, OVO, Dana, ShopeePay, LinkAja)</p>
                                <p class="text-[11px] text-slate-400">Scan kode QR langsung dari smartphone Anda.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="credit_card" class="text-emerald-600 focus:ring-emerald-500" />
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-900">Kartu Kredit / Debit Online (Visa, Mastercard, JCB)</p>
                                <p class="text-[11px] text-slate-400">Pembayaran dengan enkripsi 3D-Secure 256-bit.</p>
                            </div>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <x-button type="submit" variant="primary" size="md">
                            Dapatkan Nomor Pembayaran
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
