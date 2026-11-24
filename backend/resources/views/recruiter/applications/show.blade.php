@extends('client.layouts.app')

@section('title', 'Hồ sơ ứng viên')
@section('description', 'Chi tiết hồ sơ ứng tuyển và quá trình xử lý.')

@section('content')
    @php
        $candidate = $application->candidate;
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

    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-6xl">
            <div class="border-b border-white/15 py-7 sm:py-9">
                <a href="{{ route('recruiter.applications.index') }}" class="text-sm text-paper/60 underline underline-offset-4 hover:text-flap">← Quay lại danh sách ứng viên</a>
                <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
                    <div class="flex min-w-0 items-center gap-4">
                        <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-flap text-xl font-bold text-ink">{{ mb_strtoupper(mb_substr($candidate->user->name, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <h1 class="break-words font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">{{ $candidate->user->name }}</h1>
                            <p class="mt-1 text-sm text-paper/65">Ứng tuyển vị trí <span class="font-medium text-white">{{ $application->job->title }}</span></p>
                        </div>
                    </div>
                    <span class="inline-flex shrink-0 rounded-full border px-3.5 py-1.5 text-sm font-semibold {{ $statusStyle }}">{{ $statusLabels[$application->status] ?? $application->status }}</span>
                </div>
            </div>

            <div class="mt-6 grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_21rem]">
                <div class="space-y-5">
                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                        <h2 class="text-lg font-semibold">Thông tin ứng viên</h2>
                        <dl class="mt-4 grid gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                            @foreach ([
                                'Email' => $candidate->user->email,
                                'Điện thoại' => $candidate->user->phone,
                                'Ngày sinh' => $candidate->date_of_birth?->format('d/m/Y'),
                                'Giới tính' => \App\Models\CandidateProfile::GENDER_LABELS[$candidate->gender] ?? null,
                                'Địa chỉ' => $candidate->address,
                                'Vị trí mong muốn' => $candidate->desired_position,
                                'Kinh nghiệm' => $candidate->years_of_experience !== null ? (float) $candidate->years_of_experience.' năm' : null,
                                'Mức lương mong muốn' => $candidate->desired_salary_min || $candidate->desired_salary_max
                                    ? number_format((float) $candidate->desired_salary_min, 0, ',', '.').' - '.number_format((float) $candidate->desired_salary_max, 0, ',', '.').' VND'
                                    : null,
                            ] as $label => $value)
                                <div class="min-w-0">
                                    <dt class="text-xs font-medium text-ink/50">{{ $label }}</dt>
                                    <dd class="mt-1 break-words">{{ $value ?: '—' }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    @if ($candidate->summary)
                        <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                            <h2 class="text-lg font-semibold">Giới thiệu</h2>
                            <p class="mt-3 text-sm leading-6 whitespace-pre-line text-ink/75">{{ $candidate->summary }}</p>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                        <h2 class="text-lg font-semibold">Thư ứng tuyển</h2>
                        @if ($application->cover_letter)
                            <p class="mt-3 text-sm leading-6 whitespace-pre-line text-ink/75">{{ $application->cover_letter }}</p>
                        @else
                            <p class="mt-3 text-sm text-ink/50">Ứng viên không gửi lời nhắn kèm theo.</p>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                        <h2 class="text-lg font-semibold">Kỹ năng</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse ($candidate->skills as $skill)
                                <span class="rounded-full bg-teal-50 px-3 py-1.5 text-sm font-medium text-teal-900">{{ $skill->name }}@if ($skill->pivot->level) <span class="text-teal-800/60">· {{ $skill->pivot->level }}</span>@endif</span>
                            @empty
                                <p class="text-sm text-ink/50">Ứng viên chưa thêm kỹ năng.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                        <h2 class="text-lg font-semibold">Kinh nghiệm làm việc</h2>
                        <ul class="mt-4 space-y-4">
                            @forelse ($candidate->experiences as $experience)
                                <li class="border-l-2 border-teal-200 pl-4">
                                    <p class="font-semibold">{{ $experience->position }}</p>
                                    <p class="mt-0.5 text-sm text-ink/70">{{ $experience->company_name }}</p>
                                    <p class="mt-1 text-xs text-ink/50">{{ $experience->start_date?->format('m/Y') ?? '—' }} – {{ $experience->is_current ? 'Hiện tại' : ($experience->end_date?->format('m/Y') ?? '—') }}</p>
                                    @if ($experience->description)
                                        <p class="mt-2 text-sm leading-6 whitespace-pre-line text-ink/65">{{ $experience->description }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-ink/50">Ứng viên chưa thêm kinh nghiệm làm việc.</li>
                            @endforelse
                        </ul>
                    </section>

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                        <h2 class="text-lg font-semibold">Học vấn</h2>
                        <ul class="mt-4 space-y-4">
                            @forelse ($candidate->educations as $education)
                                <li class="border-l-2 border-amber-200 pl-4">
                                    <p class="font-semibold">{{ $education->school_name }}</p>
                                    <p class="mt-0.5 text-sm text-ink/70">{{ collect([$education->degree, $education->major])->filter()->join(' · ') ?: 'Chưa cập nhật ngành học' }}</p>
                                    <p class="mt-1 text-xs text-ink/50">{{ $education->start_date?->format('Y') ?? '—' }} – {{ $education->end_date?->format('Y') ?? '—' }}</p>
                                    @if ($education->description)
                                        <p class="mt-2 text-sm leading-6 whitespace-pre-line text-ink/65">{{ $education->description }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-ink/50">Ứng viên chưa thêm học vấn.</li>
                            @endforelse
                        </ul>
                    </section>
                </div>

                <aside class="space-y-5 lg:sticky lg:top-20">
                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.15em] text-signal">Tin ứng tuyển</p>
                        <h2 class="mt-2 text-lg font-semibold">{{ $application->job->title }}</h2>
                        <p class="mt-1 text-sm text-ink/60">{{ $application->job->company->name }}</p>
                        <dl class="mt-4 space-y-2 border-t border-ink/10 pt-4 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-ink/50">Ngày nhận hồ sơ</dt><dd class="text-right">{{ $application->applied_at?->format('d/m/Y H:i') }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-ink/50">Trạng thái</dt><dd class="text-right font-medium">{{ $statusLabels[$application->status] ?? $application->status }}</dd></div>
                        </dl>
                    </section>

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-6">
                        <h2 class="text-lg font-semibold">CV ứng viên</h2>
                        @if ($application->resume)
                            <p class="mt-1 text-xs text-teal-800">CV này được đính kèm trong hồ sơ ứng tuyển.</p>
                        @endif
                        <ul class="mt-4 space-y-3">
                            @forelse ($candidate->resumes as $resume)
                                @php($isPdf = strtolower(pathinfo($resume->original_filename ?: $resume->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                <li class="rounded-xl border {{ $application->resume_id === $resume->id ? 'border-teal-300 bg-teal-50/70' : 'border-ink/10 bg-white' }} p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="break-words text-sm font-medium">{{ $resume->title }}</p>
                                            <p class="mt-1 break-all text-xs text-ink/50">{{ $resume->original_filename ?: $resume->file_path }}</p>
                                        </div>
                                        @if ($resume->is_default)
                                            <span class="shrink-0 rounded-full bg-ink/5 px-2 py-1 text-[10px] font-semibold text-ink/60">Mặc định</span>
                                        @endif
                                    </div>
                                    @if ($resumeAvailability->get($resume->id, false))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @if ($isPdf)
                                                <a href="{{ route('recruiter.applications.resumes.view', [$application, $resume]) }}" target="_blank" rel="noopener" class="inline-flex min-h-8 items-center rounded-lg border border-ink/15 px-3 text-xs font-medium transition hover:border-teal-700 hover:text-teal-800">Xem CV</a>
                                            @endif
                                            <a href="{{ route('recruiter.applications.resumes.download', [$application, $resume]) }}" class="inline-flex min-h-8 items-center rounded-lg bg-ink px-3 text-xs font-semibold text-white transition hover:bg-teal-800">Tải xuống</a>
                                        </div>
                                    @else
                                        <p class="mt-3 text-xs text-amber-800">Tệp CV hiện không có trên hệ thống.</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-ink/50">Ứng viên chưa có CV.</li>
                            @endforelse
                        </ul>
                    </section>

                    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-6">
                        <h2 class="text-lg font-semibold">Cập nhật trạng thái</h2>
                        @if ($application->status === 'withdrawn')
                            <p class="mt-2 text-sm text-ink/60">Ứng viên đã rút hồ sơ; nhà tuyển dụng không thể thay đổi trạng thái này.</p>
                        @else
                            <form method="POST" action="{{ route('recruiter.applications.status', $application) }}" class="mt-4 space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="application_status" class="mb-1.5 block text-sm font-medium">Trạng thái mới</label>
                                    <select id="application_status" name="status" required class="min-h-11 w-full rounded-lg border border-ink/15 bg-white px-3 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10">
                                        @foreach ($editableStatuses as $key => $label)
                                            <option value="{{ $key }}" @selected(old('status', $application->status) === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="note" class="mb-1.5 block text-sm font-medium">Ghi chú lịch sử <span class="text-xs font-normal text-ink/50">(không bắt buộc)</span></label>
                                    <textarea id="note" name="note" rows="3" maxlength="2000" class="w-full rounded-lg border border-ink/15 bg-white px-3 py-2.5 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/10" placeholder="Ví dụ: Đã liên hệ ứng viên qua điện thoại.">{{ old('note') }}</textarea>
                                    @error('note') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                                </div>
                                <button type="submit" class="inline-flex min-h-10 w-full cursor-pointer items-center justify-center rounded-lg bg-ink px-4 text-sm font-semibold text-paper transition hover:bg-teal-800">Lưu trạng thái</button>
                            </form>
                        @endif
                    </section>

                    <section class="rounded-2xl border border-white/10 bg-white/5 p-5 text-paper sm:p-6">
                        <h2 class="text-lg font-semibold">Lịch sử trạng thái</h2>
                        <ol class="mt-4 space-y-4">
                            @forelse ($application->statusHistories as $history)
                                <li class="relative border-l border-white/20 pl-4">
                                    <span class="absolute -left-1 top-1 size-2 rounded-full bg-flap"></span>
                                    <p class="text-sm font-medium">
                                        @if ($history->old_status)
                                            {{ $statusLabels[$history->old_status] ?? $history->old_status }} →
                                        @else
                                            Hồ sơ được tạo ở trạng thái
                                        @endif
                                        {{ $statusLabels[$history->new_status] ?? $history->new_status }}
                                    </p>
                                    <p class="mt-1 text-xs text-paper/55">{{ $history->created_at?->format('d/m/Y H:i') }} · {{ $history->changer?->name ?? 'Hệ thống' }}</p>
                                    @if ($history->note)
                                        <p class="mt-2 text-sm leading-5 text-paper/75">{{ $history->note }}</p>
                                    @endif
                                </li>
                            @empty
                                <li class="text-sm text-paper/55">Chưa có lần cập nhật trạng thái nào được ghi nhận.</li>
                            @endforelse
                        </ol>
                    </section>
                </aside>
            </div>
        </div>
    </section>
@endsection
