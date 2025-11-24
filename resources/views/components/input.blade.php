@php
    $label = $label ?? ucfirst($name ?? '');
    $name = $name ?? '';
    $type = $type ?? 'text';
    $value = $value ?? '';
    $placeholder = $placeholder ?? '';
    $required = $required ?? false;
    $autofocus = $autofocus ?? false;
    $error = $error ?? null;
    $helper = $helper ?? null;
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            class="block w-full rounded-lg border {{ $error ? 'border-rose-300' : 'border-slate-300' }} shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-slate-900">{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if ($required) required @endif
            @if ($autofocus) autofocus @endif
            class="block w-full rounded-lg border {{ $error ? 'border-rose-300' : 'border-slate-300' }} shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm text-slate-900" />
    @endif

    @if ($helper)
        <p class="text-xs text-slate-500">{{ $helper }}</p>
    @endif

    @if ($error)
        <p class="text-xs text-rose-600">{{ $error }}</p>
    @endif
</div>

