@extends('layouts.tenant', ['title' => 'Riwayat Sewa Kost', 'headerTitle' => 'Sewa Kost Saya'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Perjanjian & Riwayat Sewa</h2>
        <p class="text-sm text-slate-500 mt-1">Daftar kontrak sewa aktif dan riwayat masa tinggal kost Anda.</p>
    </div>

    @if($rentals->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Belum Ada Riwayat Sewa Aktif</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Setelah pengajuan booking disetujui dan dikonfirmasi, kontrak sewa Anda akan tampil di sini.</p>
            </div>
            <x-button variant="primary" href="{{ route('properties.index') }}">
                Cari Kost Sekarang
            </x-button>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($rentals as $rental)
                <x-card class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <img
                            src="{{ $rental->unit->cover_image_url }}"
                            alt="{{ $rental->unit->name }}"
                            class="w-20 h-20 rounded-2xl object-cover bg-slate-100 shrink-0"
                        />
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-emerald-700 font-mono">{{ $rental->code }}</span>
                                <x-badge :variant="$rental->status->color()" size="sm">
                                    {{ $rental->status->label() }}
                                </x-badge>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">
                                {{ $rental->unit->property->name }} — {{ $rental->unit->name }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                Periode: <strong>{{ $rental->start_date->format('d M Y') }}</strong> s/d <strong>{{ $rental->end_date->format('d M Y') }}</strong>
                            </p>
                            <p class="text-xs text-slate-600">
                                Pemilik Kost: <strong>{{ $rental->unit->property->owner->name }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-2 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                        <span class="text-xs text-slate-400">Tarif Bulanan:</span>
                        <p class="text-base font-extrabold text-slate-900 leading-none">
                            {{ $rental->formatted_monthly_rent }}<span class="text-xs text-slate-400 font-normal">/bln</span>
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <x-button variant="outline" size="sm" href="{{ route('tenant.rentals.show', $rental) }}">
                                Detail Kontrak
                            </x-button>
                            <x-button variant="secondary" size="sm" href="{{ route('tenant.rentals.receipt', $rental) }}">
                                Kuitansi
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $rentals->links() }}
        </div>
    @endif
</div>
@endsection
