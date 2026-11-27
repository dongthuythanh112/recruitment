@props(['name', 'label', 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null, 'hint' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">
        {{ $label }}@if ($required) <span class="text-red-700">*</span>@endif
    </label>
    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-sky-700 focus:ring-1 focus:ring-sky-700 focus:outline-none']) }}>
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $value) === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
    @enderror
</div>
