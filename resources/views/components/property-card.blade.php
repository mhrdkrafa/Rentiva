@props(['property'])

<div {{ $attributes->merge(['class' => 'group rounded-3xl bg-white border border-slate-200/80 hover:border-emerald-500/30 overflow-hidden hover:shadow-xl hover:shadow-emerald-950/5 transition-all duration-300 flex flex-col justify-between']) }}>
    <div>
        <!-- Photo & Badges -->
        <a href="{{ route('properties.show', $property->slug) }}" class="block relative aspect-4/3 overflow-hidden bg-slate-100">
            <img
                src="{{ $property->cover_image_url }}"
                alt="{{ $property->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent opacity-60"></div>

            <!-- Top Badges -->
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5 z-10">
                <x-badge :variant="$property->gender_policy->badgeVariant()" size="sm">
                    {{ $property->gender_policy->label() }}
                </x-badge>
                @if($property->featured)
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-lg bg-amber-500 text-white shadow-xs">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        Pilihan Rentiva
                    </span>
                @endif
            </div>

            <!-- Type Badge Bottom Left -->
            <div class="absolute bottom-3 left-3 z-10">
                <span class="text-xs px-2.5 py-1 rounded-xl bg-slate-900/80 backdrop-blur-xs text-white font-semibold">
                    {{ $property->propertyType->name }}
                </span>
            </div>

            <!-- Room Count Bottom Right -->
            <div class="absolute bottom-3 right-3 z-10">
                <span class="text-[11px] px-2 py-0.5 rounded-lg bg-emerald-600/90 text-white font-medium">
                    {{ $property->available_units_count }} kamar siap
                </span>
            </div>
        </a>

        <!-- Content -->
        <div class="p-5 space-y-3">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">{{ $property->location->name }}</p>
                <h3 class="text-base font-bold text-slate-900 line-clamp-1 group-hover:text-emerald-600 transition-colors mt-0.5">
                    <a href="{{ route('properties.show', $property->slug) }}">
                        {{ $property->name }}
                    </a>
                </h3>
                <p class="text-xs text-slate-500 line-clamp-1 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ Str::limit($property->address, 38) }}
                </p>
            </div>

            <!-- Facilities preview chips -->
            <div class="flex flex-wrap gap-1.5">
                @foreach($property->facilities->take(3) as $fac)
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">
                        {{ $fac->name }}
                    </span>
                @endforeach
                @if($property->facilities->count() > 3)
                    <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-400 text-[10px] font-medium">
                        +{{ $property->facilities->count() - 3 }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer with Price & Action -->
    <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
        <div>
            <span class="text-[10px] font-medium text-slate-400 uppercase">Mulai Dari</span>
            <p class="text-base font-extrabold text-slate-900 leading-tight">
                {{ $property->formatted_min_price }}
                <span class="text-[11px] font-normal text-slate-500">/bln</span>
            </p>
        </div>

        <x-button size="sm" variant="outline" href="{{ route('properties.show', $property->slug) }}" class="rounded-xl group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition-colors">
            Lihat Detail
        </x-button>
    </div>
</div>
