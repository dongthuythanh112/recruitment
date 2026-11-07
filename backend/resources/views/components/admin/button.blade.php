@props(['variant' => 'primary', 'href' => null])

@php
    $tone = match ($variant) {
        'primary' => 'bg-teal-700 text-white hover:bg-teal-800',
        'danger' => 'bg-white text-red-700 ring-1 ring-inset ring-red-300 hover:bg-red-50',
        default => 'bg-white text-stone-700 ring-1 ring-inset ring-stone-300 hover:bg-stone-50',
    };
    $classes = "inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium whitespace-nowrap cursor-pointer {$tone}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
