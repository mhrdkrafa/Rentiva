@props([
    'variant' => 'primary', // primary, secondary, outline, danger, ghost, accent
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
])

@php
    $baseStyles = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none cursor-pointer rounded-xl';
    
    $variants = [
        'primary' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm hover:shadow shadow-emerald-600/20 focus:ring-emerald-500 border border-transparent',
        'secondary' => 'bg-slate-900 hover:bg-slate-800 text-white shadow-sm focus:ring-slate-900 border border-transparent',
        'accent' => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm focus:ring-indigo-500 border border-transparent',
        'outline' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm focus:ring-emerald-500',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm focus:ring-rose-500 border border-transparent',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-700 focus:ring-slate-400 border border-transparent',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2.5 text-sm gap-2',
        'lg' => 'px-6 py-3.5 text-base gap-2.5',
    ];

    $classes = $baseStyles . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <span class="inline-flex shrink-0">{!! $icon !!}</span>
        @endif
    </button>
@endif
