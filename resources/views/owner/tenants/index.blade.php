@extends('layouts.owner', ['title' => 'Direktori Penyewa Aktif', 'headerTitle' => 'Direktori Penyewa'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{ checkoutModalOpen: false, selectedRentalId: null, selectedTenantName: '' }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Penghuni & Kontrak Sewa</h2>
            <p class="text-sm text-slate-500 mt-1">Data lengkap penyewa aktif di properti Anda, masa sewa, dan kontak darurat.</p>
        </div>

        <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-semibold">
            <a href="{{ route('owner.tenants') }}" class="px-3 py-1.5 rounded-lg {{ !request('status') || request('status') === 'active' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Penghuni Aktif
            </a>
            <a href="{{ route('owner.tenants', ['status' => 'completed']) }}" class="px-3 py-1.5 rounded-lg {{ request('status') === 'completed' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                Riwayat Selesai
            </a>
        </div>
    </div>

    @if($tenants->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Belum Ada Penyewa Tercatat</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Penyewa yang telah dikonfirmasi pemesanannya akan otomatis muncul di direktori ini.</p>
            </div>
        </x-card>
    @else
        <div class="space-y-4">
            @foreach($tenants as $rental)
                <x-card class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <x-avatar :name="$rental->tenant->name" size="xl" />
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold text-emerald-700 font-mono">{{ $rental->code }}</span>
                                <x-badge :variant="$rental->status->color()" size="sm">
                                    {{ $rental->status->label() }}
                                </x-badge>
                            </div>
                            <h3 class="text-base font-bold text-slate-900">
                                {{ $rental->tenant->name }} &bull; <span class="text-slate-600 font-medium">{{ $rental->unit->property->name }} ({{ $rental->unit->name }})</span>
                            </h3>
                            <p class="text-xs text-slate-500">
                                Kontak: <strong>{{ $rental->tenant->email }}</strong> &bull; <strong>{{ $rental->tenant->phone ?? '-' }}</strong>
                            </p>
                            <p class="text-xs text-slate-600">
                                Masa Sewa: <strong>{{ $rental->start_date->format('d M Y') }}</strong> s/d <strong>{{ $rental->end_date->format('d M Y') }}</strong>
                            </p>
                            @if($rental->tenant->profile?->emergency_contact_name)
                                <p class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-lg mt-1 inline-block">
                                    Kontak Darurat: <strong>{{ $rental->tenant->profile->emergency_contact_name }}</strong> ({{ $rental->tenant->profile->emergency_contact_relation }} - {{ $rental->tenant->profile->emergency_contact_phone }})
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:items-end gap-3 w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0 border-slate-100">
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400">Tarif Bulanan:</span>
                            <p class="text-base font-extrabold text-slate-900 leading-none">
                                {{ $rental->formatted_monthly_rent }}
                            </p>
                        </div>

                        @if($rental->status === \App\Enums\RentalStatus::ACTIVE)
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="selectedRentalId = {{ $rental->id }}; selectedTenantName = '{{ addslashes($rental->tenant->name) }}'; checkoutModalOpen = true"
                                    class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors"
                                >
                                    Selesaikan Sewa (Check-out)
                                </button>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tenants->links() }}
        </div>
    @endif

    <!-- Check-out Modal -->
    <div
        x-show="checkoutModalOpen"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="checkoutModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-5 shadow-2xl">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Selesaikan Masa Sewa (Check-out)</h3>
                <p class="text-xs text-slate-500 mt-1">Penyewa <strong x-text="selectedTenantName"></strong> akan ditandai selesai dan status kamar akan kembali <strong>Siap Huni</strong>.</p>
            </div>

            <form :action="'/owner/tenants/' + selectedRentalId + '/complete'" method="POST" class="space-y-4">
                @csrf
                <x-textarea
                    name="check_out_notes"
                    label="Catatan Akhir Sewa / Kondisi Kamar (Opsional)"
                    placeholder="Contoh: Kunci kamar sudah dikembalikan, kamar dalam kondisi rapi, deposit jaminan telah diselesaikan..."
                    rows="3"
                />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button type="button" variant="ghost" @click="checkoutModalOpen = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Konfirmasi Selesai Sewa
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
