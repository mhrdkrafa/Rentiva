@props([
    'hover' => false,
    'padding' => 'p-6',
    'as' => 'div',
])

@php
    $classes = 'bg-white rounded-2xl border border-slate-200/80 shadow-xs transition-all duration-200 overflow-hidden';
    if ($hover) {
        $classes .= ' hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5';
    }
@endphp

<{{ $as }} {{ $attributes->merge(['class' => $classes]) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $footer }}
        </div>
    @endif
</{{ $as }}>
