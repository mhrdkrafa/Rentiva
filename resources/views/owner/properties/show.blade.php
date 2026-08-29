@extends('layouts.owner', ['title' => $property->name, 'headerTitle' => 'Detail & Manajemen Properti'])

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <!-- Header with Back Link & Status Badges -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('owner.properties.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
                &larr; Kembali ke Daftar Properti
            </a>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $property->name }}</h2>
                <x-badge :variant="$property->verification_status->color()" size="sm">
                    {{ $property->verification_status->label() }}
                </x-badge>
                <x-badge :variant="$property->status->color()" size="sm">
                    {{ $property->status->label() }}
                </x-badge>
            </div>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $property->location->name }} &bull; {{ $property->address }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($property->verification_status === \App\Enums\VerificationStatus::UNVERIFIED || $property->verification_status === \App\Enums\VerificationStatus::REJECTED)
                <form action="{{ route('owner.properties.submit-verification', $property) }}" method="POST">
                    @csrf
                    <x-button type="submit" variant="primary" size="md">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ajukan Verifikasi Listing
                    </x-button>
                </form>
            @endif

            @if($property->isPublished() && $property->isVerified())
                <x-button variant="outline" size="md" href="{{ route('properties.show', $property->slug) }}" target="_blank">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Lihat Listing Publik
                </x-button>
            @endif
        </div>
    </div>

    <!-- Rejection Alert if any -->
    @if($property->verification_status === \App\Enums\VerificationStatus::REJECTED && $property->rejection_reason)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="font-bold">Verifikasi Listing Ditolak:</p>
                <p class="mt-0.5">{{ $property->rejection_reason }}</p>
                <p class="mt-1 text-[11px] text-rose-600">Silakan perbaiki data di atas lalu ajukan kembali verifikasi properti.</p>
            </div>
        </div>
    @endif

    <!-- Units Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Unit & Kamar</h3>
                <p class="text-xs text-slate-500">Kelola nomor kamar, ketersediaan, dan paket harga sewa untuk properti ini.</p>
            </div>

            <x-button variant="primary" size="sm" href="{{ route('owner.units.create', $property) }}">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Unit / Kamar
            </x-button>
        </div>

        @if($property->units->isEmpty())
            <x-card class="p-8 text-center space-y-3 bg-amber-50/40 border-dashed border-amber-200">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <p class="text-sm font-bold text-slate-900">Belum Ada Kamar / Unit</p>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Untuk mengajukan verifikasi listing dan menerima penyewa, Anda harus menambahkan setidaknya 1 unit kamar.</p>
                <div class="pt-1">
                    <x-button variant="primary" size="sm" href="{{ route('owner.units.create', $property) }}">
                        Tambah Kamar Pertama
                    </x-button>
                </div>
            </x-card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($property->units as $unit)
                    <x-card class="p-5 space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $unit->roomType->name }}</span>
                                <h4 class="text-base font-bold text-slate-900">{{ $unit->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $unit->size ?? 'Ukuran standar' }} &bull; Kapasitas {{ $unit->capacity }} orang</p>
                            </div>
                            <x-badge :variant="$unit->status->color()" size="sm">
                                {{ $unit->status->label() }}
                            </x-badge>
                        </div>

                        <!-- Price Plans Summary -->
                        <div class="p-3 bg-slate-50 rounded-xl space-y-1 text-xs">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase">Skema Harga Sewa:</span>
                            @forelse($unit->pricePlans as $plan)
                                <div class="flex items-center justify-between text-slate-700">
                                    <span>{{ $plan->billing_period->label() }}</span>
                                    <span class="font-bold text-emerald-700">{{ $plan->formatted_amount }}</span>
                                </div>
                            @empty
                                <p class="text-slate-400 text-xs italic">Belum ada paket harga</p>
                            @endforelse
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <span class="text-xs text-slate-400">Lantai: {{ $unit->floor ?? '1' }}</span>
                            <form action="{{ route('owner.units.destroy', $unit) }}" method="POST" onsubmit="return confirm('Hapus unit ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                    Hapus Unit
                                </button>
                            </form>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Property Details Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Deskripsi Properti</h3>
                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $property->description }}</p>
            </x-card>

            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Fasilitas Terpasang</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($property->facilities as $fac)
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-medium flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $fac->name }}
                        </span>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada fasilitas dipilih</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Galeri Foto Properti</h3>
                <div class="grid grid-cols-2 gap-2">
                    @forelse($property->images as $img)
                        <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 relative group">
                            <img src="{{ $img->url }}" alt="{{ $property->name }}" class="w-full h-full object-cover" />
                            @if($img->is_cover)
                                <span class="absolute bottom-1 left-1 text-[9px] px-1.5 py-0.5 rounded bg-slate-900/80 text-white font-bold">Cover</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic col-span-2">Belum ada foto</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
