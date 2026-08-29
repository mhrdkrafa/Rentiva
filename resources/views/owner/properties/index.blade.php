@extends('layouts.owner', ['title' => 'Daftar Properti Saya', 'headerTitle' => 'Inventaris Properti'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Properti & Hunian</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola listing kost, apartemen, atau kontrakan yang Anda daftarkan di Rentiva.</p>
        </div>

        <x-button variant="primary" size="md" href="{{ route('owner.properties.create') }}">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Properti Baru
        </x-button>
    </div>

    @if($properties->isEmpty())
        <x-card class="p-12 text-center space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto shadow-inner">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-bold text-slate-900">Belum Ada Properti Terdaftar</h3>
                <p class="text-sm text-slate-500 max-w-md mx-auto">Mulai daftarkan kost atau properti sewa Anda sekarang untuk menjangkau calon penyewa berkualitas.</p>
            </div>
            <div class="pt-2">
                <x-button variant="primary" href="{{ route('owner.properties.create') }}">
                    Daftarkan Properti Pertama
                </x-button>
            </div>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($properties as $property)
                <x-card class="overflow-hidden p-0 flex flex-col justify-between group hover:shadow-md transition-shadow">
                    <div>
                        <!-- Cover Image & Status Badges -->
                        <div class="relative aspect-video bg-slate-100 overflow-hidden">
                            <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <x-badge :variant="$property->gender_policy->badgeVariant()" size="sm">
                                    {{ $property->gender_policy->label() }}
                                </x-badge>
                                <x-badge :variant="$property->verification_status->color()" size="sm">
                                    {{ $property->verification_status->label() }}
                                </x-badge>
                            </div>
                            <div class="absolute bottom-3 right-3">
                                <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-900/80 backdrop-blur-xs text-white font-medium">
                                    {{ $property->propertyType->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 space-y-3">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base line-clamp-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $property->name }}
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $property->location->name }} &bull; {{ Str::limit($property->address, 35) }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100">
                                <span>Unit: <strong>{{ $property->available_units_count }}</strong> / {{ $property->total_units_count }} siap</span>
                                <span class="text-emerald-700 font-bold">{{ $property->formatted_min_price }}<span class="text-[10px] font-normal text-slate-500">/bln</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('owner.properties.show', $property) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            Kelola Unit & Pengaturan &rarr;
                        </a>
                        <x-badge :variant="$property->status->color()" size="sm">
                            {{ $property->status->label() }}
                        </x-badge>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
