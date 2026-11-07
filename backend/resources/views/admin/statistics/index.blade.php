@extends('admin.layouts.app')

@section('title', 'Thống kê hệ thống')

@section('content')
    <section aria-label="Số liệu tổng quan" class="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        @foreach ($cards as $card)
            @php($tag = $card['url'] ? 'a' : 'div')
            <{{ $tag }} @if ($card['url']) href="{{ $card['url'] }}" @endif
                class="rounded-lg border border-stone-200 bg-white p-4 {{ $card['url'] ? 'hover:border-teal-700' : '' }}">
                <p class="text-sm text-stone-500">{{ $card['label'] }}</p>
                <p class="mt-1 text-3xl font-semibold tracking-tight tabular-nums">{{ number_format($card['value'], 0, ',', '.') }}</p>
            </{{ $tag }}>
        @endforeach
    </section>

    <div class="grid gap-4 lg:grid-cols-2">
        @include('admin.statistics._columns', ['title' => 'Tài khoản mới theo tháng', 'rows' => $newUsersByMonth])
        @include('admin.statistics._columns', ['title' => 'Tin tuyển dụng mới theo tháng', 'rows' => $newJobsByMonth])
        @include('admin.statistics._bars', ['title' => 'Tin tuyển dụng theo trạng thái', 'rows' => $jobsByStatus])
        @include('admin.statistics._bars', ['title' => 'Ứng tuyển theo trạng thái', 'rows' => $applicationsByStatus])
        @include('admin.statistics._bars', ['title' => 'Ngành nghề có nhiều tin nhất', 'rows' => $topIndustries])
    </div>
@endsection
