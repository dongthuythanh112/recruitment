@props(['name', 'label', 'value' => null, 'required' => false, 'rows' => 4])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-stone-700">
        {{ $label }}@if ($required) <span class="text-red-700">*</span>@endif
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 focus:border-teal-700 focus:ring-1 focus:ring-teal-700 focus:outline-none']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
