@props(['status', 'label'])

@php
    $tone = match ($status) {
        'active', 'approved' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'pending' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'locked', 'rejected' => 'bg-red-50 text-red-800 ring-red-200',
        default => 'bg-stone-100 text-stone-700 ring-stone-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap ring-1 ring-inset {$tone}"]) }}>{{ $label }}</span>
