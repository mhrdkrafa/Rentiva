@extends('layouts.app', ['title' => 'Cari Kost & Hunian Sewa Terbaik — Rentiva', 'seo' => $seo ?? null])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header & Search Bar -->
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Kost & Hunian Sewa</h1>
            <p class="text-sm text-slate-500 mt-1">Temukan ribuan kost terverifikasi dengan informasi harga transparan dan fasilitas lengkap.</p>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('properties.index') }}" method="GET" class="p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <x-input
                    name="q"
                    placeholder="Cari nama kost, jalan, atau daerah..."
                    :value="request('q')"
                />

                <x-select
                    name="location_id"
                    :options="$locations->pluck('name', 'id')->toArray()"
                    placeholder="Semua Kota / Area"
                    :selected="request('location_id')"
                />

                <x-select
                    name="type_id"
                    :options="$propertyTypes->pluck('name', 'id')->toArray()"
                    placeholder="Semua Tipe Properti"
                    :selected="request('type_id')"
                />

                <x-select
                    name="gender"
                    :options="[
                        'all' => 'Semua Aturan Penghuni',
                        'female_only' => 'Khusus Putri (Wanita)',
                        'male_only' => 'Khusus Putra (Pria)',
                        'married_couples' => 'Khusus Pasutri / Keluarga',
                    ]"
                    :selected="request('gender')"
                />
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-xs text-slate-500 font-medium">
                    Menampilkan <strong>{{ $properties->total() }}</strong> properti terverifikasi
                </span>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['q', 'location_id', 'type_id', 'gender']))
                        <x-button variant="ghost" size="sm" href="{{ route('properties.index') }}" class="text-slate-500">
                            Reset Filter
                        </x-button>
                    @endif
                    <x-button type="submit" variant="primary" size="sm">
                        Terapkan Filter
                    </x-button>
                </div>
            </div>
        </form>
    </div>

    <!-- Properties Grid -->
    @if($properties->isEmpty())
        <div class="py-16 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-slate-900">Tidak Ditemukan Properti yang Cocok</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau sesuaikan filter lokasi dan tipe properti Anda.</p>
            </div>
            <x-button variant="primary" size="sm" href="{{ route('properties.index') }}">
                Tampilkan Semua Listing
            </x-button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($properties as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>

        <div class="pt-6">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
