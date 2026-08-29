@props([
    'align' => 'right', // left, right
    'width' => '48', // 48, 56, 64
    'contentClasses' => 'py-1 bg-white',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        'right' => 'origin-top-right right-0',
        default => 'origin-top-right right-0',
    };

    $widthClasses = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClasses }} rounded-2xl shadow-xl {{ $alignmentClasses }} border border-slate-100"
        style="display: none;"
        @click="open = false"
    >
        <div class="rounded-2xl ring-1 ring-black/5 overflow-hidden {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
