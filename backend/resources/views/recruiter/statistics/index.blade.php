@extends('client.layouts.app')

@section('title', 'Thống kê tuyển dụng')
@section('description', 'Theo dõi hiệu quả tin tuyển dụng và hồ sơ ứng tuyển của công ty.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-6xl">
            <div class="border-b border-white/15 py-8 sm:py-10">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Khu vực nhà tuyển dụng</p>
                <h1 class="mt-2 font-signage text-4xl font-extrabold uppercase tracking-wide sm:text-5xl">Thống kê tuyển dụng</h1>
                <p class="mt-2 text-sm text-paper/65">Tổng quan hoạt động tuyển dụng của {{ $company->name }}.</p>
            </div>

            @php
                $cards = [
                    ['label' => 'Tổng tin tuyển dụng', 'value' => $totalJobs, 'note' => 'Tất cả trạng thái', 'icon' => '▤', 'tone' => 'bg-sky-100 text-sky-800'],
                    ['label' => 'Tin đang tuyển', 'value' => $publishedJobs, 'note' => 'Đã được duyệt', 'icon' => '↗', 'tone' => 'bg-emerald-100 text-emerald-800'],
                    ['label' => 'Chờ duyệt', 'value' => $pendingJobs, 'note' => 'Đang chờ quản trị viên', 'icon' => '◷', 'tone' => 'bg-amber-100 text-amber-800'],
                    ['label' => 'Tổng hồ sơ ứng tuyển', 'value' => $totalApplications, 'note' => $hiredApplications.' ứng viên đã tuyển', 'icon' => '♙', 'tone' => 'bg-indigo-100 text-indigo-800'],
                ];
            @endphp
            <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($cards as $card)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                                <p class="mt-3 text-3xl font-extrabold tracking-tight">{{ number_format($card['value']) }}</p>
                            </div>
                            <span class="grid size-11 place-items-center rounded-xl text-xl font-bold {{ $card['tone'] }}" aria-hidden="true">{{ $card['icon'] }}</span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ $card['note'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-5 grid gap-5 lg:grid-cols-[1.2fr_.8fr]">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 sm:p-6">
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold">Hồ sơ ứng tuyển theo tháng</h2>
                            <p class="mt-1 text-sm text-slate-500">Sáu tháng gần nhất</p>
                        </div>
                        <span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-800">{{ $monthlyApplications->sum('total') }} hồ sơ</span>
                    </div>
                    <div class="mt-7 grid h-52 grid-cols-6 items-end gap-3 sm:gap-5" role="img" aria-label="Biểu đồ hồ sơ ứng tuyển trong sáu tháng gần nhất">
                        @foreach ($monthlyApplications as $month)
                            @php $height = $month['total'] > 0 ? max(10, (int) round(($month['total'] / $maxMonthlyApplications) * 100)) : 3; @endphp
                            <div class="flex h-full flex-col items-center justify-end gap-2">
                                <span class="text-xs font-semibold text-slate-600">{{ $month['total'] }}</span>
                                <div class="flex h-36 w-full items-end overflow-hidden rounded-t-lg bg-sky-50">
                                    <div class="w-full rounded-t-lg bg-gradient-to-t from-sky-700 to-sky-400 transition-all" style="height: {{ $height }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-500">{{ $month['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 sm:p-6">
                    <div>
                        <h2 class="text-lg font-bold">Trạng thái ứng tuyển</h2>
                        <p class="mt-1 text-sm text-slate-500">Phân bổ hồ sơ hiện tại</p>
                    </div>
                    <div class="mt-5 space-y-4">
                        @forelse ($statuses as $key => $label)
                            @php $count = (int) ($applicationsByStatus[$key] ?? 0); $percent = $totalApplications > 0 ? (int) round($count / $totalApplications * 100) : 0; @endphp
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                                    <span class="font-medium text-slate-700">{{ $label }}</span>
                                    <span class="text-slate-500">{{ $count }} <span class="text-xs">({{ $percent }}%)</span></span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-sky-600" style="width: {{ $percent }}%"></div></div>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-500">Chưa có dữ liệu ứng tuyển.</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-lg shadow-black/10">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold">Tin thu hút nhiều ứng viên</h2>
                        <p class="mt-1 text-sm text-slate-500">Tối đa 5 tin có nhiều hồ sơ nhất</p>
                    </div>
                    <a href="{{ route('recruiter.jobs.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-sky-800 transition hover:bg-sky-50">Quản lý tin <span aria-hidden="true">→</span></a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($topJobs as $job)
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 sm:px-6">
                            <div class="min-w-0">
                                <p class="truncate font-semibold">{{ $job->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-sky-50 px-3 py-1.5 text-sm font-bold text-sky-800">{{ $job->applications_count }} hồ sơ</span>
                        </div>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-slate-500">Bạn chưa đăng tin tuyển dụng nào.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
@endsection
