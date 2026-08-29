@extends('layouts.owner', ['title' => 'Kalender Ketersediaan Kamar', 'headerTitle' => 'Kalender Ketersediaan'])

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ addBlockModalOpen: false, selectedUnitId: '' }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Kalender & Blok Ketersediaan Kamar</h2>
            <p class="text-sm text-slate-500 mt-1">Kunci jadwal kamar untuk perbaikan/renovasi atau reservasi manual di luar aplikasi.</p>
        </div>

        <x-button variant="primary" size="md" @click="addBlockModalOpen = true">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Blok Jadwal
        </x-button>
    </div>

    <!-- Properties & Units Availability Grid -->
    <div class="space-y-6">
        @forelse($properties as $property)
            <x-card class="p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $property->name }}</h3>
                        <p class="text-xs text-slate-500">{{ $property->location->name }} &bull; {{ $property->units->count() }} Unit Terdaftar</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($property->units as $unit)
                        <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $unit->name }}</h4>
                                    <span class="text-[11px] text-slate-500">{{ $unit->roomType->name }}</span>
                                </div>
                                <x-badge :variant="$unit->status->color()" size="sm">
                                    {{ $unit->status->label() }}
                                </x-badge>
                            </div>

                            <!-- Active Blocks List -->
                            <div class="space-y-1.5 pt-2 border-t border-slate-200 text-xs">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Jadwal Terblokir / Terisi:</span>
                                @if($unit->availabilityBlocks->isEmpty() && $unit->bookingRequests->isEmpty())
                                    <p class="text-emerald-700 text-[11px] font-medium">Kamar bebas jadwal / Siap huni</p>
                                @else
                                    @foreach($unit->availabilityBlocks as $block)
                                        <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50 text-amber-900 text-[11px]">
                                            <span>{{ $block->start_date->format('d/m/y') }} - {{ $block->end_date->format('d/m/y') }} ({{ ucfirst($block->reason) }})</span>
                                            <form action="{{ route('owner.availability.destroy', $block) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    @endforeach

                                    @foreach($unit->bookingRequests as $booking)
                                        <div class="p-2 rounded-xl bg-emerald-50 text-emerald-900 text-[11px] flex justify-between">
                                            <span>Sewa #{{ $booking->code }}: {{ $booking->check_in_date->format('d/m/y') }} - {{ $booking->check_out_date->format('d/m/y') }}</span>
                                            <span class="font-bold">{{ $booking->status->label() }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @empty
            <x-card class="p-12 text-center text-xs text-slate-500">
                Belum ada properti terdaftar.
            </x-card>
        @endforelse
    </div>

    <!-- Modal Add Availability Block -->
    <div
        x-show="addBlockModalOpen"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="addBlockModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-5 shadow-2xl">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Tambah Blok Ketersediaan</h3>
                <p class="text-xs text-slate-500 mt-1">Kunci jadwal kamar agar tidak dapat diajukan sewa oleh penyewa pada tanggal tersebut.</p>
            </div>

            <form action="{{ route('owner.availability.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase">Pilih Kamar / Unit *</label>
                    <select name="unit_id" class="w-full text-xs font-medium border border-slate-300 rounded-xl p-2.5" required>
                        <option value="">Pilih Unit...</option>
                        @foreach($properties as $prop)
                            <optgroup label="{{ $prop->name }}">
                                @foreach($prop->units as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->roomType->name }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <x-input type="date" name="start_date" label="Tanggal Mulai *" required />
                    <x-input type="date" name="end_date" label="Tanggal Selesai *" required />
                </div>

                <x-select
                    name="reason"
                    label="Alasan Blok *"
                    :options="[
                        'maintenance' => 'Perbaikan / Renovasi Kamar',
                        'reserved' => 'Reservasi Manual (Tamu Langsung)',
                        'manual_hold' => 'Hold Sementara oleh Pemilik',
                    ]"
                    default="maintenance"
                    required
                />

                <x-input name="notes" label="Catatan Tambahan (Opsional)" placeholder="Catatan internal pengelola..." />

                <div class="flex justify-end gap-3 pt-2">
                    <x-button type="button" variant="ghost" @click="addBlockModalOpen = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Simpan Blok Jadwal
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
