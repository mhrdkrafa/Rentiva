@props([
    'name',
    'label' => null,
    'placeholder' => '',
    'rows' => 4,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'helper' => null,
])

@php
    $hasError = $errors->has($name);
    $inputValue = old($name, $value);
@endphp

<div class="w-full space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl text-sm transition-colors duration-200 border px-3.5 py-2.5 ' .
                ($hasError 
                    ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500' 
                    : 'border-slate-200 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500') .
                ($disabled ? ' bg-slate-50 text-slate-500 cursor-not-allowed' : ' bg-white')
        ]) }}
    >{{ $inputValue }}</textarea>

    @if($hasError)
        <p class="text-xs text-rose-600 font-medium">{{ $errors->first($name) }}</p>
    @elseif($helper)
        <p class="text-xs text-slate-500">{{ $helper }}</p>
    @endif
</div>
