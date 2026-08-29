@extends('layouts.tenant', ['title' => 'Riwayat Pengajuan Sewa', 'headerTitle' => 'Pengajuan Sewa Saya'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan Sewa & Booking</h2>
        <p class="text-sm text-slate-500 mt-1">Pantau status konfirmasi dari pemilik kost dan riwayat pengajuan sewa kamar Anda.</p>
    </div>

    @if($bookings->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Pengajuan Sewa</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">Cari kamar kost idaman Anda di katalog Rentiva dan ajukan sewa dengan mudah.</p>
            </div>
            <x-button variant="primary" href="{{ route('properties.index') }}">
                Jelajahi Kost Sekarang
            </x-button>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <x-card class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <img
                            src="{{ $booking->unit->cover_image_url }}"
                            alt="{{ $booking->unit->name }}"
                            class="w-20 h-20 rounded-2xl object-cover bg-slate-100 shrink-0"
                        />
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-emerald-700 font-mono">{{ $booking->code }}</span>
                                <x-badge :variant="$booking->status->color()" size="sm">
                                    {{ $booking->status->label() }}
                                </x-badge>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">
                                <a href="{{ route('tenant.bookings.show', $booking) }}" class="hover:text-emerald-600 transition-colors">
                                    {{ $booking->unit->property->name }} — {{ $booking->unit->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500">
                                Check-in: <strong>{{ $booking->check_in_date->format('d M Y') }}</strong> ({{ $booking->duration_months }} Bulan)
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-2 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                        <span class="text-xs text-slate-400">Total Nominal:</span>
                        <p class="text-base font-extrabold text-slate-900 leading-none">
                            {{ $booking->formatted_total_amount }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <x-button variant="outline" size="sm" href="{{ route('tenant.bookings.show', $booking) }}">
                                Lihat Detail & Rincian
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
