@php
    $type = $type ?? 'button';
    $variant = $variant ?? 'primary';
    $label = $label ?? 'Submit';
    $disabled = $disabled ?? false;
    $block = $block ?? false;
    $class = $class ?? '';

    $variantClasses = match ($variant) {
        'secondary' => 'text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 focus-visible:outline-slate-400',
        'danger' => 'text-white bg-rose-600 hover:bg-rose-500 focus-visible:outline-rose-500',
        'raw' => '',
        default => 'text-white bg-blue-600 hover:bg-blue-500 focus-visible:outline-blue-500',
    };
@endphp

<button type="{{ $type }}"
    @if ($disabled) disabled @endif
    class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold shadow-sm transition {{ $variantClasses }} {{ $block ? 'w-full' : '' }} {{ $disabled ? 'opacity-60 cursor-not-allowed' : '' }} {{ $class }}">
    <span class="inline-flex items-center gap-2">
        <svg data-spinner class="w-4 h-4 animate-spin text-white hidden" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        <span>{{ $label }}</span>
    </span>
</button>

