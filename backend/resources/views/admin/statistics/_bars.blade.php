{{-- Thanh ngang xếp hạng, một màu; tên và số luôn hiển thị bằng chữ nên không phụ thuộc vào màu. --}}
@php($max = max(1, collect($rows)->max('value') ?? 0))
<section class="rounded-lg border border-slate-200 bg-white p-5">
    <h2 class="mb-4 text-sm font-semibold">{{ $title }}</h2>
    @if (collect($rows)->sum('value') === 0)
        <p class="py-6 text-center text-sm text-slate-500">Chưa có dữ liệu</p>
    @else
        <ul class="space-y-1">
            @foreach ($rows as $row)
                <li class="grid grid-cols-[9rem_1fr_2.5rem] items-center gap-3 rounded px-1 py-1 text-sm hover:bg-slate-50" title="{{ $row['label'] }}: {{ $row['value'] }}">
                    <span class="truncate text-slate-600">{{ $row['label'] }}</span>
                    <span class="h-2 rounded-r bg-slate-100">
                        @if ($row['value'] > 0)
                            <span class="block h-2 rounded-r bg-sky-700" style="width: {{ round($row['value'] / $max * 100, 1) }}%"></span>
                        @endif
                    </span>
                    <span class="text-right font-medium tabular-nums">{{ $row['value'] }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</section>
