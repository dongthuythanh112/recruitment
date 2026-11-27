@extends('client.layouts.app')

@section('title', 'Quản lý tin tuyển dụng')
@section('description', 'Quản lý tin tuyển dụng của công ty trên Việc Ngon.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-white/15 py-8 sm:py-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Khu vực nhà tuyển dụng</p>
                    <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Tin tuyển dụng</h1>
                    <p class="mt-2 text-sm text-paper/65">{{ $company->name }} · {{ $jobs->total() }} tin</p>
                </div>
                <a href="{{ route('recruiter.jobs.create') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg bg-flap px-5 text-sm font-semibold text-ink transition hover:bg-white">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="block size-[18px]" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
                    Tạo tin mới
                </a>
            </div>

            @if (session('error'))
                <div role="alert" class="mt-5 rounded-xl border border-rose-300/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-100">{{ session('error') }}</div>
            @endif

            <nav aria-label="Lọc trạng thái tin" class="mt-6 flex gap-2 overflow-x-auto pb-2">
                <a href="{{ route('recruiter.jobs.index') }}" @class([
                    'inline-flex min-h-9 shrink-0 items-center gap-2 rounded-full px-3.5 text-sm font-medium transition',
                    'bg-flap text-ink' => $status === null,
                    'bg-white/8 text-paper/75 hover:bg-flap hover:text-ink' => $status !== null,
                ])>
                    Tất cả <span class="text-xs opacity-75">{{ $counts->sum() }}</span>
                </a>
                @foreach ($statuses as $key => $label)
                    <a href="{{ route('recruiter.jobs.index', ['status' => $key]) }}" @class([
                        'inline-flex min-h-9 shrink-0 items-center gap-2 rounded-full px-3.5 text-sm font-medium transition',
                        'bg-flap text-ink' => $status === $key,
                        'bg-white/8 text-paper/75 hover:bg-flap hover:text-ink' => $status !== $key,
                    ])>
                        {{ $label }} <span class="text-xs opacity-75">{{ $counts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-4 space-y-3">
                @forelse ($jobs as $job)
                    @php
                        $statusStyle = match ($job->status) {
                            'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                            'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
                            'rejected' => 'border-rose-200 bg-rose-50 text-rose-800',
                            'hidden' => 'border-slate-200 bg-slate-100 text-slate-700',
                            'closed' => 'border-stone-200 bg-stone-100 text-stone-700',
                            default => 'border-sky-200 bg-sky-50 text-sky-800',
                        };
                    @endphp
                    <article class="rounded-2xl border border-white/10 bg-paper p-5 text-ink shadow-lg shadow-black/10 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="break-words text-lg font-semibold sm:text-xl">{{ $job->title }}</h2>
                                <p class="mt-1 text-sm text-ink/60">
                                    {{ $job->industry?->name ?? 'Chưa phân loại' }}
                                    @if ($job->location) <span class="mx-1">·</span>{{ $job->location }} @endif
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 rounded-full border px-3 py-1 text-xs font-semibold {{ $statusStyle }}">{{ $statuses[$job->status] ?? $job->status }}</span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-ink/70">
                            <span>{{ $job->salaryShort() }}</span>
                            <span>{{ $job->applications_count }} hồ sơ ứng tuyển</span>
                            <span>Cập nhật {{ $job->updated_at->format('d/m/Y') }}</span>
                            @if ($job->deadline) <span>Hạn nộp {{ $job->deadline->format('d/m/Y') }}</span> @endif
                        </div>

                        @if ($job->status === 'rejected' && $job->rejection_reason)
                            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                                <p class="font-semibold">Lý do từ chối</p>
                                <p class="mt-1 whitespace-pre-line">{{ $job->rejection_reason }}</p>
                            </div>
                        @endif

                        <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-ink/10 pt-4">
                            <a href="{{ route('recruiter.jobs.edit', $job) }}" class="inline-flex min-h-9 items-center rounded-lg border border-ink/15 px-3 text-sm font-medium transition hover:border-teal-700 hover:text-teal-800">Chỉnh sửa</a>

                            @if (in_array($job->status, ['draft', 'rejected', 'hidden', 'closed'], true))
                                <form method="POST" action="{{ route('recruiter.jobs.submit', $job) }}" data-confirm-form data-confirm-title="Gửi tin tuyển dụng để duyệt?" data-confirm-message="Tin sẽ được chuyển đến quản trị viên. Sau khi gửi, bạn có thể theo dõi trạng thái trong danh sách tin.">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg bg-teal-700 px-3 text-sm font-semibold text-white transition hover:bg-teal-800">Gửi duyệt</button>
                                </form>
                            @endif

                            @if (in_array($job->status, ['pending', 'approved'], true))
                                <form method="POST" action="{{ route('recruiter.jobs.close', $job) }}" onsubmit="return confirm('Đóng tin tuyển dụng này? Tin sẽ không còn hiển thị công khai.')">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg px-3 text-sm font-medium text-ink/70 transition hover:bg-amber-50 hover:text-amber-800">Đóng tin</button>
                                </form>
                            @endif

                            @if ($job->status === 'approved')
                                <form method="POST" action="{{ route('recruiter.jobs.hide', $job) }}" onsubmit="return confirm('Ẩn tin khỏi trang việc làm?')">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg px-3 text-sm font-medium text-ink/70 transition hover:bg-slate-100 hover:text-slate-900">Ẩn tin</button>
                                </form>
                            @endif

                            @if ($job->applications_count === 0)
                                <form method="POST" action="{{ route('recruiter.jobs.destroy', $job) }}" data-confirm-form data-confirm-title="Xóa tin tuyển dụng này?" data-confirm-message="Tin sẽ bị xóa vĩnh viễn và không thể khôi phục." class="ml-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-9 cursor-pointer items-center rounded-lg px-3 text-sm font-medium text-rose-700 transition hover:bg-rose-50">Xóa</button>
                                </form>
                            @else
                                <span class="ml-auto text-xs text-ink/45">Đã có hồ sơ, không thể xóa</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/20 px-5 py-14 text-center">
                        <p class="font-signage text-3xl font-bold uppercase">Chưa có tin tuyển dụng</p>
                        <p class="mt-2 text-sm text-paper/65">Tạo tin đầu tiên, lưu nháp rồi gửi quản trị viên duyệt.</p>
                        <a href="{{ route('recruiter.jobs.create') }}" class="mt-5 inline-flex min-h-10 items-center rounded-lg bg-flap px-4 text-sm font-semibold text-ink transition hover:bg-white">Tạo tin tuyển dụng</a>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $jobs->links() }}</div>
        </div>
    </section>

    <dialog id="job-action-confirmation" style="position:fixed; inset:50% auto auto 50%; transform:translate(-50%, -50%); margin:0;" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border-0 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-950/50">
        <div class="p-6 sm:p-7">
            <span id="job-confirm-icon" class="grid size-12 place-items-center rounded-2xl bg-sky-50 text-sky-700">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-6"><path d="M12 8v4m0 4h.01"/><circle cx="12" cy="12" r="9"/></svg>
            </span>
            <h2 id="job-confirm-title" class="mt-4 text-lg font-bold">Xác nhận thao tác</h2>
            <p id="job-confirm-message" class="mt-2 text-sm leading-6 text-slate-600"></p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" id="job-confirm-cancel" class="min-h-10 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Hủy</button>
                <button type="button" id="job-confirm-accept" class="min-h-10 rounded-xl bg-sky-700 px-4 text-sm font-semibold text-white transition hover:bg-sky-800">Xác nhận</button>
            </div>
        </div>
    </dialog>
    <script>
        (() => {
            const dialog = document.getElementById('job-action-confirmation');
            if (!dialog) return;

            let pendingForm = null;
            const title = document.getElementById('job-confirm-title');
            const message = document.getElementById('job-confirm-message');
            const accept = document.getElementById('job-confirm-accept');
            const cancel = document.getElementById('job-confirm-cancel');
            const icon = document.getElementById('job-confirm-icon');

            document.querySelectorAll('[data-confirm-form]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.confirmed === 'true') {
                        delete form.dataset.confirmed;
                        return;
                    }

                    event.preventDefault();
                    pendingForm = form;
                    title.textContent = form.dataset.confirmTitle || 'Xác nhận thao tác';
                    message.textContent = form.dataset.confirmMessage || 'Bạn có chắc muốn tiếp tục?';
                    const isDelete = form.querySelector('input[name="_method"][value="DELETE"]');
                    accept.className = `min-h-10 rounded-xl px-4 text-sm font-semibold text-white transition ${isDelete ? 'bg-rose-600 hover:bg-rose-700' : 'bg-sky-700 hover:bg-sky-800'}`;
                    accept.textContent = isDelete ? 'Xóa tin' : 'Gửi duyệt';
                    icon.className = `grid size-12 place-items-center rounded-2xl ${isDelete ? 'bg-rose-50 text-rose-700' : 'bg-sky-50 text-sky-700'}`;
                    dialog.showModal();
                });
            });

            cancel.addEventListener('click', () => dialog.close());
            dialog.addEventListener('close', () => { pendingForm = null; });
            accept.addEventListener('click', () => {
                if (!pendingForm) return;
                const form = pendingForm;
                form.dataset.confirmed = 'true';
                dialog.close();
                form.requestSubmit();
            });
            dialog.addEventListener('click', (event) => {
                if (event.target === dialog) dialog.close();
            });
        })();
    </script>
@endsection
