@extends('layouts.tenant', ['title' => 'Tiket Keluhan: ' . $issue->title, 'headerTitle' => 'Status Tiket Keluhan'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('tenant.issues.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Keluhan
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $issue->title }}</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">Dilaporkan pada {{ $issue->created_at->format('d M Y, H:i') }} WIB</p>
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

    <!-- Timeline & Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Deskripsi Kerusakan</h3>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $issue->description }}
                </p>
            </x-card>

            @if($issue->owner_notes)
                <x-card class="p-6 space-y-2 bg-emerald-50/60 border-emerald-100">
                    <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Catatan Tindak Lanjut dari Pemilik</h3>
                    <p class="text-xs text-emerald-950 leading-relaxed">{{ $issue->owner_notes }}</p>
                </x-card>
            @endif
        </div>

        <div class="space-y-6">
            <x-card class="p-6 space-y-4 text-xs">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Lokasi & Pemilik</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-slate-400">Properti:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->property->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Kamar:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->name }} ({{ $issue->rental->unit->roomType->name }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Pemilik Kost:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->property->owner->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nomor Telepon:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $issue->rental->unit->property->owner->phone ?? '-' }}</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
