@extends('client.layouts.app')

@section('title', 'Hồ sơ ứng viên')
@section('description', 'Xem và xử lý hồ sơ ứng tuyển vào các tin của công ty.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-white/15 py-8 sm:py-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Khu vực nhà tuyển dụng</p>
                    <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Hồ sơ ứng viên</h1>
                    <p class="mt-2 text-sm text-paper/65">Danh sách người đã ứng tuyển vào tin tuyển dụng của bạn.</p>
                </div>
                <a href="{{ route('recruiter.jobs.index') }}" class="text-sm text-paper/65 underline underline-offset-4 transition hover:text-flap">Quản lý tin tuyển dụng</a>
            </div>

            <form method="GET" action="{{ route('recruiter.applications.index') }}" class="mt-6 grid gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-end">
                <div>
                    <label for="job_id" class="mb-1.5 block text-xs font-medium text-paper/75">Tin tuyển dụng</label>
                    <select id="job_id" name="job_id" class="min-h-11 w-full rounded-lg border border-white/15 bg-board px-3 text-sm text-paper outline-none focus:border-flap focus:ring-2 focus:ring-flap/20">
                        <option value="">Tất cả tin tuyển dụng</option>
                        @foreach ($jobs as $id => $title)
                            <option value="{{ $id }}" @selected((string) $jobId === (string) $id)>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-xs font-medium text-paper/75">Trạng thái hồ sơ</label>
                    <select id="status" name="status" class="min-h-11 w-full rounded-lg border border-white/15 bg-board px-3 text-sm text-paper outline-none focus:border-flap focus:ring-2 focus:ring-flap/20">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-flap px-5 text-sm font-semibold text-ink transition hover:bg-white">Lọc hồ sơ</button>
                    @if ($status || $jobId)
                        <a href="{{ route('recruiter.applications.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/20 px-4 text-sm text-paper/75 transition hover:border-white hover:text-white">Xóa lọc</a>
                    @endif
                </div>
            </form>

            <nav aria-label="Lọc nhanh theo trạng thái" class="mt-5 flex gap-2 overflow-x-auto pb-2">
                <a href="{{ route('recruiter.applications.index', array_filter(['job_id' => $jobId])) }}" @class([
                    'inline-flex min-h-9 shrink-0 items-center gap-2 rounded-full px-3.5 text-sm font-medium transition',
                    'bg-flap text-ink' => $status === null,
                    'bg-white/8 text-paper/75 hover:bg-white/15 hover:text-white' => $status !== null,
                ])>Tất cả <span class="text-xs opacity-75">{{ $counts->sum() }}</span></a>
                @foreach ($statuses as $key => $label)
                    <a href="{{ route('recruiter.applications.index', array_filter(['job_id' => $jobId, 'status' => $key])) }}" @class([
                        'inline-flex min-h-9 shrink-0 items-center gap-2 rounded-full px-3.5 text-sm font-medium transition',
                        'bg-flap text-ink' => $status === $key,
                        'bg-white/8 text-paper/75 hover:bg-white/15 hover:text-white' => $status !== $key,
                    ])>{{ $label }} <span class="text-xs opacity-75">{{ $counts[$key] ?? 0 }}</span></a>
                @endforeach
            </nav>

            <div class="mt-4 space-y-3">
                @forelse ($applications as $application)
                    @php
                        $statusStyle = match ($application->status) {
                            'reviewing' => 'border-sky-200 bg-sky-50 text-sky-800',
                            'shortlisted' => 'border-violet-200 bg-violet-50 text-violet-800',
                            'interview' => 'border-indigo-200 bg-indigo-50 text-indigo-800',
                            'offered' => 'border-amber-200 bg-amber-50 text-amber-800',
                            'hired' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                            'rejected' => 'border-rose-200 bg-rose-50 text-rose-800',
                            'withdrawn' => 'border-stone-200 bg-stone-100 text-stone-700',
                            default => 'border-slate-200 bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <article class="rounded-2xl border border-white/10 bg-paper p-5 text-ink shadow-lg shadow-black/10 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-teal-100 text-sm font-bold text-teal-800">{{ mb_strtoupper(mb_substr($application->candidate->user->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <h2 class="truncate text-lg font-semibold">{{ $application->candidate->user->name }}</h2>
                                    <p class="truncate text-sm text-ink/60">{{ $application->candidate->user->email }}@if ($application->candidate->user->phone) <span class="mx-1">·</span>{{ $application->candidate->user->phone }} @endif</p>
                                </div>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusStyle }}">{{ $statuses[$application->status] ?? $application->status }}</span>
                        </div>

                        <div class="mt-4 grid gap-2 border-t border-ink/10 pt-4 text-sm sm:grid-cols-2">
                            <p><span class="text-ink/50">Tin ứng tuyển:</span> <span class="font-medium">{{ $application->job->title }}</span></p>
                            <p><span class="text-ink/50">Ngày ứng tuyển:</span> {{ $application->applied_at?->format('d/m/Y H:i') }}</p>
                            <p><span class="text-ink/50">Vị trí mong muốn:</span> {{ $application->candidate->desired_position ?: '—' }}</p>
                            <p><span class="text-ink/50">CV đính kèm:</span> {{ $application->resume?->title ?? 'Chưa có CV' }}</p>
                        </div>

                        @if ($application->cover_letter)
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-ink/65">{{ $application->cover_letter }}</p>
                        @endif

                        <div class="mt-4 flex justify-end border-t border-ink/10 pt-4">
                            <a href="{{ route('recruiter.applications.show', $application) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-ink px-4 text-sm font-semibold text-paper transition hover:bg-teal-800">Xem hồ sơ</a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/20 px-5 py-14 text-center">
                        <p class="font-signage text-3xl font-bold uppercase">Chưa có hồ sơ ứng tuyển</p>
                        <p class="mt-2 text-sm text-paper/65">Hồ sơ sẽ xuất hiện tại đây khi ứng viên nộp đơn vào tin tuyển dụng của bạn.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $applications->links() }}</div>
        </div>
    </section>
@endsection
