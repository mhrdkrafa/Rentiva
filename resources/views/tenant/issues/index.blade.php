@extends('layouts.tenant', ['title' => 'Laporan Keluhan & Perbaikan', 'headerTitle' => 'Pusat Bantuan & Keluhan'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Keluhan & Permintaan Perbaikan</h2>
            <p class="text-sm text-slate-500 mt-1">Sampaikan masalah fasilitas kamar (AC, listrik, air, kebocoran) langsung ke pemilik kost.</p>
        </div>

        <x-button variant="primary" size="md" href="{{ route('tenant.issues.create') }}">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Laporan Baru
        </x-button>
    </div>

    @if($issues->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Tidak Ada Keluhan Perbaikan</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Semua fasilitas kamar Anda dalam kondisi baik. Jika ada kendala, laporkan melalui tombol di atas.</p>
            </div>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($issues as $issue)
                <x-card class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <x-badge :variant="$issue->priority->color()" size="sm">
                                {{ $issue->priority->label() }}
                            </x-badge>
                            <x-badge :variant="$issue->status->color()" size="sm">
                                {{ $issue->status->label() }}
                            </x-badge>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">
                            <a href="{{ route('tenant.issues.show', $issue) }}" class="hover:text-emerald-600 transition-colors">
                                {{ $issue->title }}
                            </a>
                        </h3>
                        <p class="text-xs text-slate-500">
                            Properti: <strong>{{ $issue->rental->unit->property->name }} ({{ $issue->rental->unit->name }})</strong> &bull; Dilaporkan {{ $issue->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <x-button variant="outline" size="sm" href="{{ route('tenant.issues.show', $issue) }}">
                            Pantau Progres Tiket &rarr;
                        </x-button>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $issues->links() }}
        </div>
    @endif
</div>
@endsection
