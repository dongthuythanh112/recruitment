@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-stone-700">
        {{ $label }}@if ($required) <span class="text-red-700">*</span>@endif
    </label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @required($required)
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 focus:border-teal-700 focus:ring-1 focus:ring-teal-700 focus:outline-none']) }}>
    @if ($hint)
        <p class="mt-1 text-xs text-stone-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
