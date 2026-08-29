@props([
    'src' => null,
    'name' => 'User',
    'size' => 'md', // sm, md, lg, xl
    'status' => null, // online, offline, busy, null
])

@php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-14 h-14 text-lg',
        'xl' => 'w-20 h-20 text-2xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $initials = collect(explode(' ', $name))
        ->filter()
        ->map(fn ($segment) => mb_substr($segment, 0, 1))
        ->take(2)
        ->join('');

    if (empty($initials)) {
        $initials = 'R';
    }
@endphp

<div class="relative inline-flex shrink-0">
    @if($src)
        <img
            src="{{ $src }}"
            alt="{{ $name }}"
            {{ $attributes->merge(['class' => 'rounded-full object-cover border border-slate-200 ' . $sizeClass]) }}
        />
    @else
        <div {{ $attributes->merge(['class' => 'rounded-full bg-emerald-100 text-emerald-800 font-semibold flex items-center justify-center border border-emerald-200 uppercase ' . $sizeClass]) }}>
            {{ $initials }}
        </div>
    @endif

    @if($status)
        <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white {{ $status === 'online' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
    @endif
</div>
