@props([
    'name',
    'label' => null,
    'checked' => false,
    'value' => '1',
    'disabled' => false,
    'description' => null,
])

@php
    $isChecked = old($name, $checked);
@endphp

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            {{ $isChecked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'h-4 w-4 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer disabled:opacity-50'
            ]) }}
        />
    </div>
    @if($label || $description)
        <div class="ml-3 text-sm">
            @if($label)
                <label for="{{ $name }}" class="font-medium text-slate-700 cursor-pointer select-none">
                    {{ $label }}
                </label>
            @endif
            @if($description)
                <p class="text-xs text-slate-500">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
