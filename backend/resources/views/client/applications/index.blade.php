@extends('client.layouts.app')

@section('title', 'Đơn ứng tuyển')
@section('description', 'Theo dõi trạng thái các hồ sơ việc làm bạn đã gửi.')

@section('content')
    @php
        $statusStyles = [
            'pending' => 'bg-amber-100 text-amber-900 ring-amber-200',
            'reviewing' => 'bg-sky-100 text-sky-900 ring-sky-200',
            'shortlisted' => 'bg-indigo-100 text-indigo-900 ring-indigo-200',
            'interview' => 'bg-violet-100 text-violet-900 ring-violet-200',
            'offered' => 'bg-emerald-100 text-emerald-900 ring-emerald-200',
            'hired' => 'bg-green-100 text-green-900 ring-green-200',
            'rejected' => 'bg-rose-100 text-rose-900 ring-rose-200',
            'withdrawn' => 'bg-slate-100 text-slate-700 ring-slate-200',
        ];
        $canWithdraw = ['pending', 'reviewing', 'shortlisted', 'interview', 'offered'];
    @endphp

    <section class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
        <header class="flex flex-wrap items-end justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-800">Tài khoản ứng viên</p>
                <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Đơn ứng tuyển</h1>
                <p class="mt-2 text-sm text-slate-600">Theo dõi tiến trình xử lý hồ sơ của bạn.</p>
            </div>
            <a href="{{ route('home') }}#viec-lam" class="inline-flex min-h-10 items-center rounded-lg bg-sky-100 px-4 text-sm font-semibold text-sky-900 transition hover:bg-sky-200">Tìm việc làm <span class="ml-2" aria-hidden="true">→</span></a>
        </header>

        @if ($applications->isEmpty())
            <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center">
                <span aria-hidden="true" class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-100 text-2xl text-sky-800">↗</span>
                <h2 class="mt-4 font-semibold text-slate-900">Bạn chưa gửi hồ sơ ứng tuyển nào</h2>
                <p class="mt-1 text-sm text-slate-500">Tìm một công việc phù hợp và gửi CV trực tiếp đến nhà tuyển dụng.</p>
                <a href="{{ route('home') }}#viec-lam" class="mt-5 inline-flex min-h-11 items-center rounded-lg bg-sky-800 px-5 text-sm font-semibold text-white transition hover:bg-sky-900">Khám phá việc làm</a>
            </div>
        @else
            <div class="mt-6 space-y-4">
                @foreach ($applications as $application)
                    @php
                        $job = $application->job;
                        $jobIsOpen = $job->status === \App\Models\Job::STATUS_APPROVED
                            && (! $job->deadline || $job->deadline->isToday() || $job->deadline->isFuture());
                    @endphp
                    <article id="application-{{ $application->id }}" class="scroll-mt-24 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                        <div class="flex flex-wrap items-start justify-between gap-4 p-5 sm:p-6">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span @class(['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset', $statusStyles[$application->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200'])>
                                        {{ \App\Models\Application::STATUS_LABELS[$application->status] ?? $application->status }}
                                    </span>
                                    <span class="text-xs text-slate-400">Gửi ngày {{ $application->applied_at?->format('d/m/Y H:i') }}</span>
                                </div>
                                <h2 class="mt-3 truncate text-lg font-bold text-slate-900 sm:text-xl">
                                    @if ($jobIsOpen)
                                        <a href="{{ route('jobs.show', $job->slug) }}" class="transition hover:text-sky-800">{{ $job->title }}</a>
                                    @else
                                        {{ $job->title }}
                                    @endif
                                </h2>
                                <p class="mt-1 text-sm text-slate-600">{{ $job->company->name }}{{ $job->location ? ' · '.$job->location : '' }}</p>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-500">
                                    <span>CV: {{ $application->resume->title }}</span>
                                    @if ($application->cover_letter)<span>Có lời nhắn kèm theo</span>@endif
                                </div>
                            </div>
                            @if ($application->status === 'withdrawn' && $jobIsOpen)
                                <a href="{{ route('candidate.applications.create', $job) }}" class="inline-flex min-h-10 items-center rounded-lg border border-sky-200 px-4 text-sm font-semibold text-sky-800 transition hover:bg-sky-50">Ứng tuyển lại</a>
                            @elseif (in_array($application->status, $canWithdraw, true))
                                <form method="POST" action="{{ route('candidate.applications.withdraw', $application) }}" onsubmit="return confirm('Bạn muốn rút hồ sơ ứng tuyển này?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-slate-200 px-4 text-sm font-medium text-slate-600 transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-800">Rút hồ sơ</button>
                                </form>
                            @endif
                        </div>
                        @if ($application->cover_letter)
                            <details class="border-t border-slate-100 px-5 py-3 sm:px-6">
                                <summary class="cursor-pointer text-xs font-semibold text-sky-800">Xem lời nhắn đã gửi</summary>
                                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $application->cover_letter }}</p>
                            </details>
                        @endif
                        <details class="border-t border-slate-100 px-5 py-3 sm:px-6">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 text-xs font-semibold text-sky-800 [&::-webkit-details-marker]:hidden">
                                <span>Xem lịch sử xử lý</span>
                                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] text-sky-900">{{ $application->statusHistories->count() }} mốc</span>
                            </summary>
                            @if ($application->statusHistories->isNotEmpty())
                                <ol class="ml-1 mt-4 space-y-0 border-l border-sky-200">
                                    @foreach ($application->statusHistories as $history)
                                        <li class="relative pb-4 pl-5 last:pb-0">
                                            <span @class([
                                                'absolute -left-[5px] top-1 size-2.5 rounded-full ring-4 ring-white',
                                                'bg-sky-600' => $loop->last,
                                                'bg-slate-300' => ! $loop->last,
                                            ])></span>
                                            <p class="text-sm font-medium text-slate-800">
                                                @if ($history->old_status)
                                                    {{ \App\Models\Application::STATUS_LABELS[$history->old_status] ?? $history->old_status }}
                                                    <span class="mx-1 text-slate-400" aria-hidden="true">→</span>
                                                @else
                                                    Hồ sơ được gửi
                                                    <span class="mx-1 text-slate-400" aria-hidden="true">→</span>
                                                @endif
                                                {{ \App\Models\Application::STATUS_LABELS[$history->new_status] ?? $history->new_status }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $history->created_at?->format('d/m/Y H:i') }}
                                                <span class="mx-1">·</span>
                                                {{ $history->changer?->id === auth()->id() ? 'Bạn' : ($history->changer?->name ?? 'Hệ thống') }}
                                            </p>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="mt-3 text-xs text-slate-500">Chưa có dữ liệu lịch sử cho hồ sơ này.</p>
                            @endif
                        </details>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $applications->links('client.partials.pagination') }}</div>
        @endif
    </section>
@endsection
