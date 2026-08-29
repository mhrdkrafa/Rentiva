@extends('layouts.owner', ['title' => 'Laporan Keluhan Penyewa', 'headerTitle' => 'Pusat Keluhan & Perbaikan'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Keluhan & Permintaan Perbaikan Kamar</h2>
            <p class="text-sm text-slate-500 mt-1">Pantau dan tindak lanjuti laporan kerusakan dari penyewa kost Anda.</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
            <a href="{{ route('owner.issues.index') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Semua
            </a>
            <a href="{{ route('owner.issues.index', ['status' => 'reported']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'reported' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Baru Dilaporkan
            </a>
            <a href="{{ route('owner.issues.index', ['status' => 'in_progress']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'in_progress' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Sedang Dikerjakan
            </a>
            <a href="{{ route('owner.issues.index', ['status' => 'resolved']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'resolved' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Selesai
            </a>
        </div>
    </div>

    @if($issues->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Tidak Ada Keluhan Aktif</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Semua fasilitas hunian penyewa Anda berada dalam kondisi prima.</p>
            </div>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($issues as $issue)
                <x-card class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-md transition-shadow">
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
                            {{ $issue->title }}
                        </h3>
                        <p class="text-xs text-slate-500">
                            Pelapor: <strong>{{ $issue->tenant->name }}</strong> &bull; {{ $issue->rental->unit->property->name }} ({{ $issue->rental->unit->name }}) &bull; {{ $issue->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <x-button variant="primary" size="sm" href="{{ route('owner.issues.show', $issue) }}">
                            Tindak Lanjut & Detail &rarr;
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
