@extends('layouts.app', ['title' => $property->name . ' — Rentiva', 'seo' => $seo ?? null])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8" x-data="{ 
    bookingModalOpen: false, 
    selectedUnitId: null, 
    selectedUnitName: '', 
    selectedPricePlanId: null, 
    selectedPriceAmount: 0,
    durationMonths: 1,
    checkInDate: '{{ now()->addDay()->toDateString() }}',
    notes: '',

    openBooking(unitId, unitName, pricePlanId, priceAmount) {
        @auth
            this.selectedUnitId = unitId;
            this.selectedUnitName = unitName;
            this.selectedPricePlanId = pricePlanId;
            this.selectedPriceAmount = priceAmount;
            this.bookingModalOpen = true;
        @else
            window.location.href = '{{ url('/admin/login') }}';
        @endauth
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-slate-800">Beranda</a>
        <span>/</span>
        <a href="{{ route('properties.index') }}" class="hover:text-slate-800">Katalog Properti</a>
        <span>/</span>
        <a href="{{ route('properties.index', ['location_id' => $property->location_id]) }}" class="hover:text-slate-800">{{ $property->location->name }}</a>
        <span>/</span>
        <span class="text-slate-900 font-semibold truncate max-w-xs">{{ $property->name }}</span>
    </nav>

    <!-- Photo Gallery Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 rounded-3xl overflow-hidden aspect-2/1 md:aspect-5/2 bg-slate-900">
        <div class="md:col-span-2 relative h-full">
            <img src="{{ $property->cover_image_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover" />
            <div class="absolute top-4 left-4 flex gap-2">
                <x-badge :variant="$property->gender_policy->badgeVariant()" size="md">
                    {{ $property->gender_policy->label() }}
                </x-badge>
                <x-badge :variant="$property->verification_status->color()" size="md">
                    {{ $property->verification_status->label() }}
                </x-badge>
            </div>
        </div>

        <div class="hidden md:grid md:col-span-2 grid-cols-2 gap-3">
            @forelse($property->images->slice(1, 4) as $img)
                <div class="relative h-full overflow-hidden bg-slate-800">
                    <img src="{{ $img->url }}" alt="{{ $property->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                </div>
            @empty
                <div class="col-span-2 flex items-center justify-center bg-slate-800 text-slate-400 text-xs">
                    Foto interior lainnya segera tersedia
                </div>
            @endforelse
        </div>
    </div>

    <!-- Main Content & Booking Sticky Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Left 2 Cols: Details, Amenities, Units -->
        <div class="lg:col-span-2 space-y-10">
            <!-- Header Info -->
            <div class="space-y-3 pb-6 border-b border-slate-200">
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 uppercase tracking-wider">
                    <span>{{ $property->propertyType->name }}</span>
                    <span>&bull;</span>
                    <span>{{ $property->location->name }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $property->name }}
                </h1>

                <p class="text-sm text-slate-600 flex items-start gap-2">
                    <svg class="w-5 h-5 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ $property->address }}</span>
                </p>
            </div>

            <!-- Description -->
            <div class="space-y-3">
                <h2 class="text-lg font-bold text-slate-900">Tentang Properti Ini</h2>
                <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $property->description }}
                </div>
            </div>

            <!-- Facilities & Amenities -->
            <div class="space-y-4 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Fasilitas Properti & Bersama</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @forelse($property->facilities as $facility)
                        <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-800">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $facility->name }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic col-span-3">Fasilitas belum dicantumkan</p>
                    @endforelse
                </div>
            </div>

            <!-- Room Units List -->
            <div class="space-y-6 pt-6 border-t border-slate-200" id="units-section">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Pilihan Tipe Kamar & Unit Tersedia</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih unit kamar yang sesuai dengan kebutuhan dan anggaran Anda.</p>
                </div>

                <div class="space-y-4">
                    @forelse($property->units as $unit)
                        <x-card class="p-6 space-y-4 hover:border-emerald-500/40 transition-colors">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div>
                                    <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">{{ $unit->roomType->name }}</span>
                                    <h3 class="text-lg font-bold text-slate-900">{{ $unit->name }}</h3>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                        <span>Dimensi: <strong>{{ $unit->size ?? '3x4 m' }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Kapasitas: <strong>{{ $unit->capacity }} orang</strong></span>
                                        <span>&bull;</span>
                                        <span>Lantai: <strong>{{ $unit->floor ?? '1' }}</strong></span>
                                    </div>
                                </div>

                                <x-badge :variant="$unit->status->color()" size="md">
                                    {{ $unit->status->label() }}
                                </x-badge>
                            </div>

                            @if($unit->description)
                                <p class="text-xs text-slate-600">{{ $unit->description }}</p>
                            @endif

                            <!-- Unit Facilities -->
                            @if($unit->facilities->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5 pt-2">
                                    @foreach($unit->facilities as $uf)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium">
                                            {{ $uf->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Price Plans & Booking CTA -->
                            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <span class="text-[11px] font-semibold text-slate-400 uppercase">Tarif Sewa:</span>
                                    <div class="flex flex-wrap items-baseline gap-3">
                                        @foreach($unit->pricePlans as $plan)
                                            <div class="text-xs font-semibold text-slate-700">
                                                <span>{{ $plan->billing_period->label() }}:</span>
                                                <span class="text-sm font-extrabold text-emerald-700">{{ $plan->formatted_amount }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if($unit->status === \App\Enums\UnitStatus::AVAILABLE && $unit->activeMonthlyPricePlan)
                                    <x-button
                                        type="button"
                                        variant="primary"
                                        size="md"
                                        @click="openBooking({{ $unit->id }}, '{{ addslashes($unit->name) }}', {{ $unit->activeMonthlyPricePlan->id }}, {{ $unit->activeMonthlyPricePlan->amount }})"
                                    >
                                        Ajukan Sewa Kamar Ini
                                    </x-button>
                                @elseif($unit->status === \App\Enums\UnitStatus::AVAILABLE && $unit->pricePlans->first())
                                    <x-button
                                        type="button"
                                        variant="primary"
                                        size="md"
                                        @click="openBooking({{ $unit->id }}, '{{ addslashes($unit->name) }}', {{ $unit->pricePlans->first()->id }}, {{ $unit->pricePlans->first()->amount }})"
                                    >
                                        Ajukan Sewa Kamar Ini
                                    </x-button>
                                @else
                                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-3 py-2 rounded-xl">
                                        Unit Sedang Terisi
                                    </span>
                                @endif
                            </div>
                        </x-card>
                    @empty
                        <div class="p-8 text-center bg-slate-50 rounded-2xl text-xs text-slate-500">
                            Belum ada unit yang aktif saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Owner Info Card -->
            <div class="space-y-4 pt-6 border-t border-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Dikelola Oleh</h2>
                <x-card class="p-6 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <x-avatar :name="$property->owner->name" size="lg" />
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $property->owner->name }}</h3>
                            <p class="text-xs text-slate-500">Mitra Pemilik Kost Terverifikasi di Rentiva</p>
                            <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">Respon Cepat &bull; Terdaftar sejak {{ $property->owner->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Reviews & Ratings Section -->
            <div class="space-y-6 pt-6 border-t border-slate-200" id="reviews">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Ulasan & Penilaian Penghuni</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pengalaman tinggal nyata dari penyewa yang telah menempati properti ini.</p>
                    </div>
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-2xl">
                        <span class="text-amber-500 text-lg">★</span>
                        <span class="text-sm font-black text-slate-900">{{ number_format($property->average_rating, 1) }}</span>
                        <span class="text-[11px] text-slate-500">({{ $property->reviews_count }} ulasan)</span>
                    </div>
                </div>

                @if($property->approvedReviews->isEmpty())
                    <div class="p-8 text-center bg-slate-50 rounded-3xl border border-slate-100 text-xs text-slate-400 space-y-1">
                        <p class="font-bold text-slate-600">Belum Ada Ulasan</p>
                        <p>Jadilah penyewa pertama yang memberikan ulasan untuk properti ini!</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($property->approvedReviews as $review)
                            <x-card class="p-6 space-y-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :name="$review->tenant->name" size="md" />
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-900">{{ $review->tenant->name }}</h4>
                                            <p class="text-[11px] text-slate-400">Penyewa kamar {{ $review->unit->name }} &bull; {{ $review->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-0.5 text-amber-400 text-xs">
                                        {{ str_repeat('★', $review->rating) }}
                                    </div>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                    "{{ $review->comment }}"
                                </p>

                                @if($review->owner_reply)
                                    <div class="pl-4 border-l-2 border-emerald-500 bg-emerald-50/60 p-3 rounded-r-2xl space-y-1 text-xs">
                                        <div class="flex items-center justify-between text-[11px] font-bold text-emerald-800">
                                            <span>Tanggapan Pemilik ({{ $property->owner->name }}):</span>
                                            <span class="text-slate-400 font-normal">{{ $review->owner_replied_at?->format('d M Y') }}</span>
                                        </div>
                                        <p class="text-slate-700">{{ $review->owner_reply }}</p>
                                    </div>
                                @endif
                            </x-card>
                        @endforeach
                    </div>
                @endif
            </div>

        <!-- Right Col: Booking Sticky Sidebar -->
        <div class="space-y-6">
            <div class="sticky top-28 space-y-6">
                <x-card class="p-6 space-y-6 shadow-xl shadow-emerald-950/5 border-emerald-500/20">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mulai Dari</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-3xl font-black text-slate-900">{{ $property->formatted_min_price }}</span>
                            <span class="text-xs text-slate-500 font-medium">/bulan</span>
                        </div>
                    </div>

                    <div class="space-y-3 p-4 bg-slate-50 rounded-2xl text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Status Kamar:</span>
                            <span class="font-bold text-emerald-700">{{ $property->available_units_count }} Kamar Siap Huni</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Aturan Penghuni:</span>
                            <span class="font-semibold text-slate-900">{{ $property->gender_policy->label() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Verifikasi:</span>
                            <span class="font-semibold text-emerald-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Listing Asli & Terverifikasi
                            </span>
                        </div>
                    </div>

                    <x-button variant="primary" size="lg" href="#units-section" class="w-full justify-center text-sm font-bold shadow-lg shadow-emerald-600/20">
                        Pilih Kamar & Ajukan Sewa
                    </x-button>

                    @auth
                        @if(auth()->id() !== $property->owner_id)
                            <form action="{{ route('messages.start') }}" method="POST">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $property->id }}" />
                                <x-button type="submit" variant="outline" size="md" class="w-full justify-center text-xs font-bold text-slate-700">
                                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Tanya Pemilik Kost
                                </x-button>
                            </form>
                        @endif
                    @else
                        <x-button variant="outline" size="md" href="{{ url('/admin/login') }}" class="w-full justify-center text-xs font-bold text-slate-700">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Tanya Pemilik Kost
                        </x-button>
                    @endauth

                    <p class="text-center text-[11px] text-slate-400">
                        Tanpa biaya perantara liar &bull; Pembayaran aman via Rentiva
                    </p>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Booking Modal (Alpine.js) -->
    <div
        x-show="bookingModalOpen"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="bookingModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Ajukan Sewa Kamar</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $property->name }} &bull; <span x-text="selectedUnitName" class="font-semibold text-emerald-700"></span></p>
                </div>
                <button @click="bookingModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('tenant.bookings.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="unit_id" :value="selectedUnitId" />
                <input type="hidden" name="price_plan_id" :value="selectedPricePlanId" />

                <div>
                    <x-input
                        type="date"
                        name="check_in_date"
                        label="Tanggal Mulai Sewa (Check-in) *"
                        x-model="checkInDate"
                        min="{{ now()->toDateString() }}"
                        required
                    />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase">Durasi Sewa *</label>
                    <select name="duration_months" x-model="durationMonths" class="w-full text-xs font-medium border border-slate-300 rounded-xl p-2.5" required>
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan (1 Tahun)</option>
                    </select>
                </div>

                <x-textarea
                    name="tenant_notes"
                    label="Catatan untuk Pemilik (Opsional)"
                    placeholder="Contoh: Rencana masuk tgl 1, bawa motor 1..."
                    rows="2"
                />

                <div class="p-4 bg-emerald-50/70 border border-emerald-100 rounded-2xl space-y-2 text-xs">
                    <div class="flex justify-between font-semibold text-slate-700">
                        <span>Perkiraan Total Biaya Sewa:</span>
                        <span class="text-sm font-black text-emerald-800" x-text="'Rp ' + (selectedPriceAmount * durationMonths).toLocaleString('id-ID')"></span>
                    </div>
                    <p class="text-[11px] text-slate-500">Nominal pasti termasuk deposit & biaya layanan akan diverifikasi sistem saat pengajuan.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <x-button type="button" variant="ghost" @click="bookingModalOpen = false">
                        Batal
                    </x-button>
                    <x-button type="submit" variant="primary" class="shadow-md shadow-emerald-600/20">
                        Kirim Pengajuan Sewa
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
