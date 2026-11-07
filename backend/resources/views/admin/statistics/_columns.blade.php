{{-- Cột theo thời gian, một chuỗi một biểu đồ (không dùng hai trục). --}}
@php($max = max(1, collect($rows)->max('value') ?? 0))
<section class="rounded-lg border border-stone-200 bg-white p-5">
    <h2 class="mb-4 text-sm font-semibold">{{ $title }}</h2>
    <ul class="flex h-44 gap-2 border-b border-stone-200">
        @foreach ($rows as $row)
            <li class="flex h-full min-w-0 flex-1 flex-col items-center justify-end rounded-t hover:bg-stone-50" title="{{ $row['label'] }}: {{ $row['value'] }}">
                <span class="mb-1 text-xs font-medium text-stone-700 tabular-nums">{{ $row['value'] }}</span>
                @if ($row['value'] > 0)
                    <span class="block w-full max-w-8 rounded-t bg-[#2a78d6]" style="height: {{ round($row['value'] / $max * 75, 1) }}%"></span>
                @endif
            </li>
        @endforeach
    </ul>
    <div class="mt-2 flex gap-2">
        @foreach ($rows as $row)
            <span class="min-w-0 flex-1 truncate text-center text-xs text-stone-500">{{ $row['label'] }}</span>
        @endforeach
    </div>
</section>
