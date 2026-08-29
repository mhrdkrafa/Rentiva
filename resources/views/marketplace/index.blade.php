@extends('layouts.app', ['title' => 'Cari & Sewa Kost, Kamar, dan Apartemen — Rentiva', 'seo' => $seo ?? null])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ mobileFilterOpen: false }">
    <!-- Top Search Header & Summary Bar -->
    <div class="space-y-4 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    @if(!empty($filters['q']))
                        Hasil Pencarian: "{{ $filters['q'] }}"
                    @elseif(!empty($filters['location_id']) && $locations->firstWhere('id', $filters['location_id']))
                        Kost & Properti di {{ $locations->firstWhere('id', $filters['location_id'])->name }}
                    @else
                        Katalog Kost & Hunian Sewa
                    @endif
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Ditemukan <span class="font-bold text-emerald-700">{{ $properties->total() }}</span> hunian terverifikasi siap huni
                </p>
            </div>

            <!-- Sort and Mobile Filter Toggle -->
            <div class="flex items-center gap-3">
                <button
                    @click="mobileFilterOpen = true"
                    type="button"
                    class="lg:hidden inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 shadow-xs"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter Pencarian
                </button>

                <!-- Sorting Dropdown Form -->
                <form action="{{ route('properties.index') }}" method="GET" class="flex items-center gap-2">
                    @foreach($filters as $k => $v)
                        @if($k !== 'sort' && !empty($v))
                            @if(is_array($v))
                                @foreach($v as $subV)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}" />
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
                            @endif
                        @endif
                    @endforeach

                    <label class="hidden sm:inline text-xs font-semibold text-slate-500 whitespace-nowrap">Urutkan:</label>
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        class="text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl px-3 py-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs cursor-pointer"
                    >
                        <option value="recommended" {{ ($filters['sort'] ?? '') === 'recommended' ? 'selected' : '' }}>Rekomendasi Rentiva</option>
                        <option value="price_low" {{ ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' }}>Harga Termurah</option>
                        <option value="price_high" {{ ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' }}>Harga Termahal</option>
                        <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Listing Terbaru</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Active Filters Pills -->
        @php
            $hasActiveFilters = !empty($filters['q']) || !empty($filters['location_id']) || !empty($filters['types']) || !empty($filters['type_id']) || (!empty($filters['gender']) && $filters['gender'] !== 'all') || !empty($filters['min_price']) || !empty($filters['max_price']) || !empty($filters['facilities']) || !empty($filters['available_only']);
        @endphp

        @if($hasActiveFilters)
            <div class="flex flex-wrap items-center gap-2 pt-2">
                <span class="text-xs font-semibold text-slate-400">Filter Aktif:</span>

                @if(!empty($filters['q']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        Kata kunci: {{ $filters['q'] }}
                    </span>
                @endif

                @if(!empty($filters['location_id']) && $loc = $locations->firstWhere('id', $filters['location_id']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        Kota: {{ $loc->name }}
                    </span>
                @endif

                @if(!empty($filters['gender']) && $filters['gender'] !== 'all')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        {{ \App\Enums\GenderPolicy::tryFrom($filters['gender'])?->label() ?? $filters['gender'] }}
                    </span>
                @endif

                @if(!empty($filters['min_price']) || !empty($filters['max_price']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        Harga: {{ !empty($filters['min_price']) ? \App\Support\Money::format((int)$filters['min_price']) : 'Rp 0' }} - {{ !empty($filters['max_price']) ? \App\Support\Money::format((int)$filters['max_price']) : 'Max' }}
                    </span>
                @endif

                @if(!empty($filters['available_only']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-medium">
                        Hanya Kamar Siap Huni
                    </span>
                @endif

                <a href="{{ route('properties.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 underline ml-2">
                    Reset Semua Filter
                </a>
            </div>
        @endif
    </div>

    <!-- Main Content Layout (Sidebar + Results Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Desktop Sidebar Filter Form -->
        <aside class="hidden lg:block space-y-6">
            <form action="{{ route('properties.index') }}" method="GET" class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <!-- Preserve sort -->
                <input type="hidden" name="sort" value="{{ $filters['sort'] ?? 'recommended' }}" />

                <!-- Keyword Search -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Cari Kata Kunci</label>
                    <x-input
                        name="q"
                        placeholder="Nama kost, jalan..."
                        :value="$filters['q'] ?? ''"
                    />
                </div>

                <!-- Location Filter -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Kota / Kawasan</label>
                    <x-select
                        name="location_id"
                        :options="$locations->pluck('name', 'id')->toArray()"
                        placeholder="Semua Kota & Kampus"
                        :selected="$filters['location_id'] ?? ''"
                    />
                </div>

                <!-- Price Range Filter -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Rentang Harga Sewa (IDR)</label>
                    <div class="space-y-2">
                        <x-input
                            type="number"
                            name="min_price"
                            placeholder="Harga Minimum (cth: 500000)"
                            :value="$filters['min_price'] ?? ''"
                        />
                        <x-input
                            type="number"
                            name="max_price"
                            placeholder="Harga Maksimum (cth: 3000000)"
                            :value="$filters['max_price'] ?? ''"
                        />
                    </div>
                </div>

                <!-- Property Types Filter -->
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Tipe Properti</label>
                    <div class="space-y-2">
                        @foreach($propertyTypes as $pt)
                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="types[]"
                                    value="{{ $pt->id }}"
                                    class="rounded text-emerald-600 focus:ring-emerald-500"
                                    {{ in_array((string)$pt->id, (array)($filters['types'] ?? []), true) ? 'checked' : '' }}
                                />
                                <span>{{ $pt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Gender Rules Filter -->
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Aturan Penghuni</label>
                    <div class="space-y-2">
                        @foreach([
                            'all' => 'Semua Kalangan',
                            'female_only' => 'Khusus Putri (Wanita)',
                            'male_only' => 'Khusus Putra (Pria)',
                            'married_couples' => 'Khusus Pasutri / Keluarga',
                        ] as $val => $label)
                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                <input
                                    type="radio"
                                    name="gender"
                                    value="{{ $val }}"
                                    class="text-emerald-600 focus:ring-emerald-500"
                                    {{ ($filters['gender'] ?? 'all') === $val ? 'checked' : '' }}
                                />
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Availability Toggle -->
                <div class="pt-2 border-t border-slate-100">
                    <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-800 cursor-pointer">
                        <input
                            type="checkbox"
                            name="available_only"
                            value="1"
                            class="rounded text-emerald-600 focus:ring-emerald-500"
                            {{ !empty($filters['available_only']) ? 'checked' : '' }}
                        />
                        <span>Hanya Kamar Siap Huni</span>
                    </label>
                </div>

                <!-- Facilities Filter -->
                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">Fasilitas Populer</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                        @foreach($facilities as $fac)
                            <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="facilities[]"
                                    value="{{ $fac->id }}"
                                    class="rounded text-emerald-600 focus:ring-emerald-500"
                                    {{ in_array((string)$fac->id, (array)($filters['facilities'] ?? []), true) ? 'checked' : '' }}
                                />
                                <span>{{ $fac->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <x-button type="submit" variant="primary" size="md" class="w-full justify-center shadow-md shadow-emerald-600/10">
                        Terapkan Filter
                    </x-button>
                </div>
            </form>
        </aside>

        <!-- Properties Results Grid -->
        <main class="lg:col-span-3 space-y-6">
            @if($properties->isEmpty())
                <x-card class="p-12 text-center space-y-4 bg-slate-50 border-dashed border-slate-300">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-bold text-slate-900">Tidak Ada Properti yang Sesuai</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Coba kurangi filter yang dipilih atau gunakan kata kunci pencarian yang lebih umum.
                        </p>
                    </div>
                    <div class="pt-2">
                        <x-button variant="primary" size="sm" href="{{ route('properties.index') }}">
                            Tampilkan Semua Properti
                        </x-button>
                    </div>
                </x-card>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($properties as $property)
                        <x-property-card :property="$property" />
                    @endforeach
                </div>

                <div class="pt-6">
                    {{ $properties->links() }}
                </div>
            @endif
        </main>
    </div>

    <!-- Mobile Filter Drawer Modal -->
    <div
        x-show="mobileFilterOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs lg:hidden"
        style="display: none;"
    >
        <div
            @click.away="mobileFilterOpen = false"
            class="fixed inset-y-0 right-0 w-full max-w-sm bg-white p-6 overflow-y-auto shadow-2xl flex flex-col justify-between"
        >
            <div class="space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-slate-900">Filter Pencarian</h3>
                    <button @click="mobileFilterOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('properties.index') }}" method="GET" class="space-y-6">
                    <!-- Keyword Search -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-900 uppercase">Kata Kunci</label>
                        <x-input name="q" placeholder="Nama kost..." :value="$filters['q'] ?? ''" />
                    </div>

                    <!-- Location -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-900 uppercase">Kota</label>
                        <x-select
                            name="location_id"
                            :options="$locations->pluck('name', 'id')->toArray()"
                            placeholder="Semua Lokasi"
                            :selected="$filters['location_id'] ?? ''"
                        />
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-900 uppercase">Rentang Harga (IDR)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <x-input type="number" name="min_price" placeholder="Min" :value="$filters['min_price'] ?? ''" />
                            <x-input type="number" name="max_price" placeholder="Max" :value="$filters['max_price'] ?? ''" />
                        </div>
                    </div>

                    <!-- Gender Rules -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-900 uppercase">Aturan Penghuni</label>
                        <x-select
                            name="gender"
                            :options="[
                                'all' => 'Semua Kalangan',
                                'female_only' => 'Khusus Putri',
                                'male_only' => 'Khusus Putra',
                                'married_couples' => 'Khusus Pasutri',
                            ]"
                            :selected="$filters['gender'] ?? 'all'"
                        />
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex gap-3">
                        <x-button type="button" variant="ghost" @click="mobileFilterOpen = false" class="w-1/2 justify-center">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" class="w-1/2 justify-center">
                            Terapkan
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
