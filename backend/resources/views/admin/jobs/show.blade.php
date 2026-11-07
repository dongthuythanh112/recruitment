@extends('admin.layouts.app')

@section('title', $job->title)

@section('actions')
    <x-admin.button variant="secondary" :href="route('admin.jobs.edit', $job)">Sửa</x-admin.button>
    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" onsubmit="return confirm('Xóa tin tuyển dụng này?')">
        @csrf
        @method('DELETE')
        <x-admin.button variant="danger">Xóa</x-admin.button>
    </form>
@endsection

@section('content')
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <section class="rounded-lg border border-stone-200 bg-white p-5">
                <h2 class="mb-2 text-sm font-semibold">Mô tả công việc</h2>
                <p class="text-sm whitespace-pre-line">{{ $job->description }}</p>
                @if ($job->requirements)
                    <h2 class="mt-5 mb-2 text-sm font-semibold">Yêu cầu</h2>
                    <p class="text-sm whitespace-pre-line">{{ $job->requirements }}</p>
                @endif
                @if ($job->benefits)
                    <h2 class="mt-5 mb-2 text-sm font-semibold">Quyền lợi</h2>
                    <p class="text-sm whitespace-pre-line">{{ $job->benefits }}</p>
                @endif
                @if ($job->skills->isNotEmpty())
                    <h2 class="mt-5 mb-2 text-sm font-semibold">Kỹ năng yêu cầu</h2>
                    <div class="flex flex-wrap gap-2 text-sm">
                        @foreach ($job->skills as $skill)
                            <span class="rounded-full bg-stone-100 px-3 py-1">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="rounded-lg border border-stone-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">Lịch sử kiểm duyệt</h2>
                <ul class="divide-y divide-stone-100 text-sm">
                    @forelse ($job->moderationHistories as $history)
                        <li class="py-2">
                            <p>
                                <span class="font-medium">{{ \App\Models\JobModerationHistory::ACTION_LABELS[$history->action] ?? $history->action }}</span>
                                <span class="text-stone-500">· {{ $history->admin->name }} · {{ $history->created_at?->format('d/m/Y H:i') }}</span>
                            </p>
                            @if ($history->note)
                                <p class="mt-1 whitespace-pre-line text-stone-700">{{ $history->note }}</p>
                            @endif
                        </li>
                    @empty
                        <li class="text-stone-500">Chưa có thao tác kiểm duyệt nào.</li>
                    @endforelse
                </ul>
            </section>
        </div>

        <div class="space-y-4">
            <section class="rounded-lg border border-stone-200 bg-white p-5">
                <div class="mb-4 flex items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold">Kiểm duyệt</h2>
                    <x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" />
                </div>

                @if ($job->rejection_reason)
                    <p class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-900"><span class="font-medium">Lý do từ chối:</span> {{ $job->rejection_reason }}</p>
                @endif

                @if ($job->status === \App\Models\Job::STATUS_PENDING)
                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="mb-4">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <x-admin.button class="w-full">Duyệt tin</x-admin.button>
                    </form>
                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="space-y-2 border-t border-stone-100 pt-4">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <x-admin.textarea name="note" label="Lý do từ chối" rows="3" required />
                        <x-admin.button variant="danger" class="w-full">Từ chối</x-admin.button>
                    </form>
                @elseif ($job->status === \App\Models\Job::STATUS_APPROVED)
                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="action" value="hide">
                        <x-admin.textarea name="note" label="Ghi chú (không bắt buộc)" rows="2" />
                        <x-admin.button variant="secondary" class="w-full">Ẩn tin</x-admin.button>
                    </form>
                @elseif ($job->status === \App\Models\Job::STATUS_HIDDEN)
                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">
                        @csrf
                        <input type="hidden" name="action" value="restore">
                        <x-admin.button variant="secondary" class="w-full">Khôi phục tin</x-admin.button>
                    </form>
                @else
                    <p class="text-sm text-stone-500">Trạng thái này không có thao tác kiểm duyệt.</p>
                @endif
            </section>

            <section class="rounded-lg border border-stone-200 bg-white p-5">
                <h2 class="mb-4 text-sm font-semibold">Thông tin tin đăng</h2>
                <dl class="space-y-3 text-sm">
                    @foreach ([
                        'Công ty' => $job->company->name,
                        'Ngành nghề' => $job->industry->name,
                        'Người đăng' => $job->creator->name,
                        'Lương' => $job->salaryRange(),
                        'Địa điểm' => $job->location,
                        'Hình thức' => \App\Models\Job::EMPLOYMENT_TYPE_LABELS[$job->employment_type] ?? null,
                        'Nơi làm việc' => \App\Models\Job::WORKPLACE_TYPE_LABELS[$job->workplace_type] ?? null,
                        'Kinh nghiệm' => $job->experience_years !== null ? (float) $job->experience_years.' năm' : null,
                        'Số lượng tuyển' => $job->quantity,
                        'Hạn nộp' => $job->deadline?->format('d/m/Y'),
                        'Lượt ứng tuyển' => $job->applications_count,
                        'Ngày đăng công khai' => $job->published_at?->format('d/m/Y H:i'),
                        'Người duyệt' => $job->reviewer?->name,
                    ] as $label => $value)
                        <div>
                            <dt class="text-stone-500">{{ $label }}</dt>
                            <dd class="break-words">{{ $value ?? '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>
    </div>
@endsection
