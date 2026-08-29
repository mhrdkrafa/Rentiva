@extends('layouts.tenant', ['title' => 'Kontrak Sewa ' . $rental->code, 'headerTitle' => 'Rincian Perjanjian Sewa'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <a href="{{ route('tenant.rentals.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 mb-2">
            &larr; Kembali ke Daftar Sewa
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Perjanjian Sewa #{{ $rental->code }}</h2>
                    <x-badge :variant="$rental->status->color()" size="md">
                        {{ $rental->status->label() }}
                    </x-badge>
                </div>
                <p class="text-xs text-slate-500 mt-1">Diterbitkan pada {{ $rental->created_at->format('d M Y') }}</p>
            </div>

            <div class="flex items-center gap-3">
                <x-button variant="secondary" size="md" href="{{ route('tenant.rentals.receipt', $rental) }}">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Lihat Kuitansi / Invoice
                </x-button>
                @if(! $rental->hasReviewed())
                    <x-button variant="outline" size="md" href="{{ route('tenant.reviews.create', $rental) }}">
                        ⭐ Tulis Ulasan
                    </x-button>
                @endif
                <x-button variant="primary" size="md" href="{{ route('tenant.issues.create', ['rental_id' => $rental->id]) }}">
                    Lapor Keluhan Kamar
                </x-button>
            </div>
        </div>
    </div>

    <!-- Rental Info Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Kamar & Lokasi</h3>
                <div class="flex items-start gap-4">
                    <img src="{{ $rental->unit->cover_image_url }}" alt="{{ $rental->unit->name }}" class="w-24 h-24 rounded-2xl object-cover bg-slate-100 shrink-0" />
                    <div class="space-y-1">
                        <h4 class="font-bold text-slate-900 text-base">{{ $rental->unit->property->name }}</h4>
                        <p class="text-xs text-slate-600 font-semibold">{{ $rental->unit->name }} ({{ $rental->unit->roomType->name }})</p>
                        <p class="text-xs text-slate-500">{{ $rental->unit->property->address }}, {{ $rental->unit->property->location->name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3 border-t border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400">Masa Awal Sewa:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $rental->start_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Masa Akhir Sewa:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $rental->end_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Pemilik Kost:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $rental->unit->property->owner->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nomor Kontak Pemilik:</span>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $rental->unit->property->owner->phone ?? '-' }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Issues History on this Tenancy -->
            <x-card class="p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="text-sm font-bold text-slate-900">Keluhan & Perbaikan pada Kamar Ini</h3>
                    <a href="{{ route('tenant.issues.create', ['rental_id' => $rental->id]) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">+ Buat Tiket Baru</a>
                </div>

                @if($rental->issues->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada keluhan yang dilaporkan untuk sewa ini.</p>
                @else
                    <div class="space-y-2.5">
                        @foreach($rental->issues as $issue)
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 text-xs">
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $issue->title }}</h4>
                                    <p class="text-slate-500 text-[11px]">Dilaporkan {{ $issue->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-badge :variant="$issue->status->color()" size="sm">
                                        {{ $issue->status->label() }}
                                    </x-badge>
                                    <x-button variant="ghost" size="sm" href="{{ route('tenant.issues.show', $issue) }}">
                                        Detail &rarr;
                                    </x-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card class="p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Ketentuan Finansial</h3>
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Tarif Sewa Bulanan:</span>
                        <span class="font-bold text-slate-900">{{ $rental->formatted_monthly_rent }}</span>
                    </div>
                    @if($rental->deposit_held > 0)
                        <div class="flex justify-between text-slate-600">
                            <span>Deposit Jaminan Tertahan:</span>
                            <span class="font-semibold text-slate-900">{{ $rental->formatted_deposit_held }}</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
