@php
    $type = $type ?? 'info';
    $message = $message ?? null;
    $visible = filled($message);

    $styles = match ($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-rose-200 bg-rose-50 text-rose-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-slate-200 bg-slate-50 text-slate-700',
    };
@endphp

@if ($visible)
    <div class="rounded-lg border px-4 py-3 text-sm {{ $styles }}">
        {!! nl2br(e($message)) !!}
    </div>
@endif

