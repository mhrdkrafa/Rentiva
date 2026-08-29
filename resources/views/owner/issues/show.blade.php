@extends('layouts.owner', ['title' => 'Tiket Keluhan: ' . $issue->title, 'headerTitle' => 'Tindak Lanjut Keluhan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('owner.issues.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Keluhan
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $issue->title }}</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">Dilaporkan oleh {{ $issue->tenant->name }} pada {{ $issue->created_at->format('d M Y, H:i') }} WIB</p>
            </div>

            <div class="flex items-center gap-2">
                <x-badge :variant="$issue->priority->color()" size="md">
                    {{ $issue->priority->label() }}
                </x-badge>
                <x-badge :variant="$issue->status->color()" size="md">
                    {{ $issue->status->label() }}
                </x-badge>
            </div>
        </div>
    </div>

    <!-- Issue Details & Resolution Form -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <!-- Problem Description -->
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Deskripsi Masalah dari Penyewa</h3>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $issue->description }}
                </p>
            </x-card>

            <!-- Update Status & Notes Form -->
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Update Progres Tindak Lanjut</h3>

                <form action="{{ route('owner.issues.update', $issue) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <x-select
                        name="status"
                        label="Status Tiket Saat Ini *"
                        :options="[
                            'reported' => 'Laporan Diterima',
                            'in_review' => 'Sedang Ditinjau Pemilik',
                            'in_progress' => 'Teknisi Sedang Menangani / Dikerjakan',
                            'resolved' => 'Perbaikan Selesai (Solved)',
                            'closed' => 'Tutup Tiket',
                        ]"
                        :selected="$issue->status->value"
                        required
                    />

                    <x-textarea
                        name="owner_notes"
                        label="Catatan Tindak Lanjut / Keterangan Teknisi (Opsional)"
                        placeholder="Contoh: Teknisi AC dijadwalkan datang besok pukul 10:00 pagi. Freon sudah diisi ulang dan AC berfungsi normal."
                        rows="3"
                        :value="$issue->owner_notes"
                    />

                    <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                        <x-button type="submit" variant="primary">
                            Simpan Perubahan Status
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="space-y-6">
            <!-- Room & Tenant Info Card -->
            <x-card class="p-6 space-y-4 text-xs">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Kamar & Penghuni</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-slate-400">Properti:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->property->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Kamar:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->name }} ({{ $issue->rental->unit->roomType->name }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nama Penyewa:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->tenant->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Kontak Telepon:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->tenant->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Email:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->tenant->email }}</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
