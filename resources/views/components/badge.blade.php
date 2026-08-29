@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger, verified, accent
    'size' => 'md', // sm, md
    'dot' => false,
])

@php
    $base = 'inline-flex items-center font-medium rounded-full';

    $variants = [
        'neutral' => 'bg-slate-100 text-slate-700 border border-slate-200/80',
        'primary' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/80',
        'success' => 'bg-green-50 text-green-700 border border-green-200/80',
        'warning' => 'bg-amber-50 text-amber-700 border border-amber-200/80',
        'danger' => 'bg-rose-50 text-rose-700 border border-rose-200/80',
        'accent' => 'bg-indigo-50 text-indigo-700 border border-indigo-200/80',
        'verified' => 'bg-emerald-500 text-white shadow-xs',
    ];

    $dotColors = [
        'neutral' => 'bg-slate-400',
        'primary' => 'bg-emerald-500',
        'success' => 'bg-green-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
        'accent' => 'bg-indigo-500',
        'verified' => 'bg-white',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['neutral']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$variant] ?? 'bg-current' }}"></span>
    @endif
    {{ $slot }}
</span>
