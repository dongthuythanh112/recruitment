@extends('admin.layouts.app')

@section('title', 'Chi tiết tin tuyển dụng')

@section('actions')
    <x-admin.button variant="secondary" :href="route('admin.jobs.edit', $job)">Chỉnh sửa tin</x-admin.button>
    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Xóa tin tuyển dụng này?')">
        @csrf
        @method('DELETE')
        <x-admin.button variant="danger">Xóa tin</x-admin.button>
    </form>
@endsection

@section('content')
    @php
        $sections = array_filter([
            'Mô tả công việc' => $job->description,
            'Yêu cầu ứng viên' => $job->requirements,
            'Quyền lợi' => $job->benefits,
        ]);
        $publiclyAvailable = $job->status === \App\Models\Job::STATUS_APPROVED
            && (! $job->deadline || $job->deadline->isToday() || $job->deadline->isFuture());
    @endphp

    <div class="space-y-5">
        <header class="relative isolate overflow-hidden rounded-3xl border border-sky-100 bg-gradient-to-br from-white via-sky-50 to-cyan-50 p-5 shadow-sm sm:p-7">
            <div aria-hidden="true" class="absolute -right-12 -top-24 -z-10 size-72 rounded-full border-[32px] border-sky-100/70"></div>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-sky-100 px-3 py-1 font-mono text-[11px] font-semibold text-sky-900">TIN #{{ str_pad((string) $job->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" />
                    </div>
                    <h2 class="mt-4 max-w-4xl text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $job->title }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $job->company->name }} <span class="mx-1 text-slate-300">·</span> {{ $job->industry->name }}</p>
                    <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-500">
                        @if ($job->location)<span class="inline-flex items-center gap-1.5"><span class="text-sky-700">⌖</span>{{ $job->location }}</span>@endif
                        <span>Khởi tạo {{ $job->created_at?->format('d/m/Y H:i') }}</span>
                        @if ($job->published_at)<span>Đăng công khai {{ $job->published_at->format('d/m/Y H:i') }}</span>@endif
                    </div>
                </div>
                @if ($publiclyAvailable)
                    <x-admin.button variant="secondary" :href="route('jobs.show', $job->slug)">Xem trên trang việc làm ↗</x-admin.button>
                @endif
            </div>
        </header>

        <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_21rem]">
            <main class="min-w-0 space-y-5">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-5 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <span class="grid size-9 place-items-center rounded-xl bg-sky-100 text-sky-800" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M7 3h10a2 2 0 0 1 2 2v16H5V5a2 2 0 0 1 2-2Zm1 5h8m-8 4h8m-8 4h5" /></svg>
                        </span>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-sky-800">Nội dung đăng tuyển</p>
                            <h3 class="font-semibold text-slate-900">Mô tả vị trí</h3>
                        </div>
                    </div>

                    @forelse ($sections as $heading => $body)
                        <section class="{{ $loop->first ? '' : 'mt-6 border-t border-slate-100 pt-5' }}">
                            <h4 class="text-sm font-semibold text-slate-800">{{ $heading }}</h4>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $body }}</p>
                        </section>
                    @empty
                        <p class="text-sm text-slate-500">Tin chưa có nội dung mô tả.</p>
                    @endforelse

                    @if ($job->skills->isNotEmpty())
                        <div class="mt-6 border-t border-slate-100 pt-5">
                            <h4 class="text-sm font-semibold text-slate-800">Kỹ năng yêu cầu</h4>
                            <ul class="mt-3 flex flex-wrap gap-2">
                                @foreach ($job->skills as $skill)
                                    <li class="rounded-full border border-sky-100 bg-sky-50 px-3 py-1.5 text-xs font-medium text-sky-900">{{ $skill->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-sky-800">Nhật ký quản trị</p>
                            <h3 class="mt-1 font-semibold text-slate-900">Lịch sử kiểm duyệt</h3>
                        </div>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $job->moderationHistories->count() }} lần</span>
                    </div>
                    <ol class="space-y-0">
                        @forelse ($job->moderationHistories as $history)
                            <li class="relative border-l border-sky-200 pb-5 pl-5 last:border-transparent last:pb-0">
                                <span class="absolute -left-[5px] top-1 size-2.5 rounded-full bg-sky-600 ring-4 ring-white" aria-hidden="true"></span>
                                <p class="text-sm font-semibold text-slate-800">{{ \App\Models\JobModerationHistory::ACTION_LABELS[$history->action] ?? $history->action }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $history->admin?->name ?? 'Tài khoản đã xóa' }} <span class="mx-1">·</span> {{ $history->created_at?->format('d/m/Y H:i') }}</p>
                                @if ($history->note)<p class="mt-2 whitespace-pre-line rounded-xl bg-slate-50 p-3 text-sm leading-6 text-slate-600">{{ $history->note }}</p>@endif
                            </li>
                        @empty
                            <li class="rounded-xl bg-slate-50 px-4 py-5 text-sm text-slate-500">Tin chưa có thao tác kiểm duyệt nào.</li>
                        @endforelse
                    </ol>
                </section>
            </main>

            <aside class="space-y-5 xl:sticky xl:top-6">
                <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-sky-900 to-sky-700 text-white shadow-lg shadow-sky-900/10">
                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] !text-sky-100">Mức lương</p>
                        <p class="mt-1 font-signage text-3xl font-extrabold tracking-wide !text-white">{{ $job->salaryRange() }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-3 border-t border-white/15 pt-4 text-sm">
                            <div><p class="text-xs !text-sky-100">Ứng viên</p><p class="mt-1 font-semibold !text-white">{{ number_format($job->applications_count) }}</p></div>
                            <div><p class="text-xs !text-sky-100">Số lượng tuyển</p><p class="mt-1 font-semibold !text-white">{{ $job->quantity }}</p></div>
                        </div>
                    </div>
                    @if ($job->deadline)
                        <div class="border-t border-white/15 bg-white/10 px-5 py-3 text-xs !text-sky-50 sm:px-6">Hạn nộp hồ sơ: {{ $job->deadline->format('d/m/Y') }}</div>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-sky-800">Trạng thái hiện tại</p>
                            <h3 class="mt-1 font-semibold text-slate-900">Kiểm duyệt tin</h3>
                        </div>
                        <x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" />
                    </div>

                    @if ($job->rejection_reason)
                        <div class="mb-4 rounded-xl border border-rose-100 bg-rose-50 p-3 text-sm text-rose-900">
                            <p class="font-semibold">Lý do từ chối</p>
                            <p class="mt-1 leading-5">{{ $job->rejection_reason }}</p>
                        </div>
                    @endif

                    @if ($job->status === \App\Models\Job::STATUS_PENDING)
                        <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <x-admin.button class="min-h-11 w-full rounded-xl">Duyệt và đăng tin</x-admin.button>
                        </form>
                        <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <x-admin.textarea name="note" label="Lý do từ chối" rows="3" required />
                            <x-admin.button variant="danger" class="min-h-11 w-full rounded-xl">Từ chối tin</x-admin.button>
                        </form>
                    @elseif ($job->status === \App\Models\Job::STATUS_APPROVED)
                        <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="action" value="hide">
                            <x-admin.textarea name="note" label="Ghi chú khi ẩn (không bắt buộc)" rows="2" />
                            <x-admin.button variant="secondary" class="min-h-11 w-full rounded-xl">Ẩn tin khỏi trang tuyển dụng</x-admin.button>
                        </form>
                    @elseif ($job->status === \App\Models\Job::STATUS_HIDDEN)
                        <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">
                            @csrf
                            <input type="hidden" name="action" value="restore">
                            <x-admin.button variant="secondary" class="min-h-11 w-full rounded-xl">Khôi phục tin</x-admin.button>
                        </form>
                    @else
                        <p class="rounded-xl bg-slate-50 p-3 text-sm leading-5 text-slate-500">Trạng thái này hiện không có thao tác kiểm duyệt.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4">
                        <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-sky-800">Thông tin tổng quan</p>
                        <h3 class="mt-1 font-semibold text-slate-900">Chi tiết tin đăng</h3>
                    </div>
                    <dl class="divide-y divide-slate-100">
                        @foreach ([
                            'Công ty' => $job->company->name,
                            'Ngành nghề' => $job->industry->name,
                            'Người đăng' => $job->creator->name,
                            'Địa điểm' => $job->location,
                            'Hình thức' => \App\Models\Job::EMPLOYMENT_TYPE_LABELS[$job->employment_type] ?? null,
                            'Nơi làm việc' => \App\Models\Job::WORKPLACE_TYPE_LABELS[$job->workplace_type] ?? null,
                            'Kinh nghiệm' => $job->experience_years !== null ? (float) $job->experience_years.' năm' : null,
                            'Người duyệt' => $job->reviewer?->name,
                        ] as $label => $value)
                            <div class="flex items-start justify-between gap-4 py-2.5 text-sm first:pt-0 last:pb-0">
                                <dt class="shrink-0 text-slate-500">{{ $label }}</dt>
                                <dd class="text-right font-medium text-slate-800">{{ $value ?? '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            </aside>
        </div>
    </div>
@endsection
