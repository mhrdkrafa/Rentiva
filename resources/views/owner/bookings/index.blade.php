@extends('layouts.owner', ['title' => 'Permintaan Sewa Masuk', 'headerTitle' => 'Permintaan Sewa'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Permintaan Sewa Masuk</h2>
            <p class="text-sm text-slate-500 mt-1">Tinjau calon penyewa dan konfirmasi ketersediaan kamar kost Anda.</p>
        </div>

        <!-- Filter Status Tabs -->
        <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
            <a href="{{ route('owner.bookings') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Semua
            </a>
            <a href="{{ route('owner.bookings', ['status' => 'pending_approval']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'pending_approval' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('owner.bookings', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'approved' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Disetujui
            </a>
        </div>
    </div>

    @if($bookings->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Belum Ada Permintaan Sewa</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Permintaan sewa dari calon penyewa baru akan muncul di sini.</p>
            </div>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                <x-card class="p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <x-avatar :name="$booking->tenant->name" size="lg" />
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-emerald-700 font-mono">{{ $booking->code }}</span>
                                <x-badge :variant="$booking->status->color()" size="sm">
                                    {{ $booking->status->label() }}
                                </x-badge>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">
                                {{ $booking->tenant->name }} &bull; <span class="text-slate-600 font-medium">{{ $booking->unit->property->name }} ({{ $booking->unit->name }})</span>
                            </h3>
                            <p class="text-xs text-slate-500">
                                Mulai Sewa: <strong>{{ $booking->check_in_date->format('d M Y') }}</strong> (Durasi {{ $booking->duration_months }} Bulan)
                            </p>
                            @if($booking->tenant_notes)
                                <p class="text-xs text-slate-600 italic bg-slate-50 p-2 rounded-lg mt-2 max-w-lg">
                                    "{{ $booking->tenant_notes }}"
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-3 w-full sm:w-auto border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400">Total Sewa:</span>
                            <p class="text-base font-extrabold text-slate-900 leading-none">
                                {{ $booking->formatted_total_amount }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-button variant="primary" size="sm" href="{{ route('owner.bookings.show', $booking) }}">
                                Tinjau & Konfirmasi &rarr;
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
