@extends('layouts.tenant', ['title' => 'Laporkan Keluhan / Perbaikan', 'headerTitle' => 'Buat Laporan Keluhan'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('tenant.issues.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Keluhan
        </a>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Laporkan Masalah / Kerusakan Kamar</h2>
        <p class="text-xs text-slate-500 mt-1">Laporan ini akan diteruskan ke pemilik properti dan teknisi untuk segera ditindaklanjuti.</p>
    </div>

    @if($rentals->isEmpty())
        <x-card class="p-8 text-center space-y-3">
            <p class="text-xs text-slate-500">Anda tidak memiliki masa sewa aktif saat ini untuk membuat laporan keluhan.</p>
            <x-button variant="primary" size="sm" href="{{ route('tenant.dashboard') }}">
                Kembali ke Dashboard
            </x-button>
        </x-card>
    @else
        <x-card class="p-6 sm:p-8">
            <form action="{{ route('tenant.issues.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Select Rental -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase">Pilih Hunian / Kamar yang Mengalami Kendala *</label>
                    <select name="rental_id" class="w-full text-xs font-medium border border-slate-300 rounded-xl p-3 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @foreach($rentals as $r)
                            <option value="{{ $r->id }}" {{ (string)$r->id === (string)$selectedRentalId ? 'selected' : '' }}>
                                {{ $r->unit->property->name }} — {{ $r->unit->name }} (Kontrak: #{{ $r->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-input
                    name="title"
                    label="Judul Keluhan / Masalah *"
                    placeholder="Contoh: AC tidak dingin / bocor menetes, Lampu kamar mandi mati"
                    required
                />

                <x-select
                    name="priority"
                    label="Tingkat Urgensi *"
                    :options="[
                        'low' => 'Rendah (Kosmetik / Tidak mengganggu aktivitas)',
                        'medium' => 'Sedang (Perlu diperbaiki dalam 1-2 hari)',
                        'high' => 'Tinggi (Mengganggu kenyamanan tinggal)',
                        'urgent' => 'Darurat / Sangat Mendesak (Air mati, korsleting listrik)',
                    ]"
                    default="medium"
                    required
                />

                <x-textarea
                    name="description"
                    label="Penjelasan Rinci Kerusakan *"
                    placeholder="Jelaskan secara detail bagian yang rusak, kapan mulai terjadi, dan waktu luang Anda untuk menerima teknisi..."
                    rows="4"
                    required
                />

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                    <x-button variant="ghost" href="{{ route('tenant.issues.index') }}">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" class="shadow-md shadow-emerald-600/20">
                        Kirim Laporan Keluhan
                    </x-button>
                </div>
            </form>
        </x-card>
    @endif
</div>
@endsection
