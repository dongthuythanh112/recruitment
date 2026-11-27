@extends('admin.layouts.app')

@section('title', $candidate->user->name)

@section('actions')
    <x-admin.button variant="secondary" :href="route('admin.candidates.edit', $candidate)">Sửa</x-admin.button>
    <form method="POST" action="{{ route('admin.candidates.toggle-status', $candidate) }}">
        @csrf
        @method('PATCH')
        <x-admin.button variant="secondary">{{ $candidate->user->status === 'active' ? 'Khóa tài khoản' : 'Mở khóa' }}</x-admin.button>
    </form>
    <form method="POST" action="{{ route('admin.candidates.destroy', $candidate) }}" onsubmit="return confirm('Xóa ứng viên này cùng toàn bộ hồ sơ?')">
        @csrf
        @method('DELETE')
        <x-admin.button variant="danger">Xóa</x-admin.button>
    </form>
@endsection

@section('content')
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-5 lg:col-span-1">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold">Thông tin</h2>
                <x-admin.badge :status="$candidate->user->status" :label="\App\Models\User::STATUS_LABELS[$candidate->user->status] ?? $candidate->user->status" />
            </div>
            <dl class="space-y-3 text-sm">
                @foreach ([
                    'Email' => $candidate->user->email,
                    'Điện thoại' => $candidate->user->phone,
                    'Ngày sinh' => $candidate->date_of_birth?->format('d/m/Y'),
                    'Giới tính' => \App\Models\CandidateProfile::GENDER_LABELS[$candidate->gender] ?? null,
                    'Địa chỉ' => $candidate->address,
                    'Vị trí mong muốn' => $candidate->desired_position,
                    'Kinh nghiệm' => $candidate->years_of_experience !== null ? (float) $candidate->years_of_experience.' năm' : null,
                    'Lương mong muốn' => $candidate->desired_salary_min || $candidate->desired_salary_max
                        ? number_format((float) $candidate->desired_salary_min, 0, ',', '.').' - '.number_format((float) $candidate->desired_salary_max, 0, ',', '.').' VND'
                        : null,
                    'Ngày tạo' => $candidate->created_at?->format('d/m/Y'),
                ] as $label => $value)
                    <div>
                        <dt class="text-slate-500">{{ $label }}</dt>
                        <dd class="break-words">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <div class="space-y-4 lg:col-span-2">
            @if ($candidate->summary)
                <section class="rounded-lg border border-slate-200 bg-white p-5">
                    <h2 class="mb-2 text-sm font-semibold">Giới thiệu</h2>
                    <p class="text-sm whitespace-pre-line">{{ $candidate->summary }}</p>
                </section>
            @endif

            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">Kỹ năng</h2>
                <div class="flex flex-wrap gap-2 text-sm">
                    @forelse ($candidate->skills as $skill)
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $skill->name }}@if ($skill->pivot->level) · {{ $skill->pivot->level }}@endif</span>
                    @empty
                        <p class="text-slate-500">Chưa có kỹ năng.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">Kinh nghiệm làm việc</h2>
                <ul class="space-y-3 text-sm">
                    @forelse ($candidate->experiences as $experience)
                        <li>
                            <p class="font-medium">{{ $experience->position }} · {{ $experience->company_name }}</p>
                            <p class="text-xs text-slate-500">{{ $experience->start_date?->format('m/Y') ?? '?' }} - {{ $experience->is_current ? 'Hiện tại' : ($experience->end_date?->format('m/Y') ?? '?') }}</p>
                        </li>
                    @empty
                        <li class="text-slate-500">Chưa có kinh nghiệm.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">Học vấn</h2>
                <ul class="space-y-3 text-sm">
                    @forelse ($candidate->educations as $education)
                        <li>
                            <p class="font-medium">{{ $education->school_name }}</p>
                            <p class="text-xs text-slate-500">{{ collect([$education->degree, $education->major])->filter()->join(' · ') }}</p>
                        </li>
                    @empty
                        <li class="text-slate-500">Chưa có học vấn.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">CV ({{ $candidate->resumes->count() }})</h2>
                <ul class="space-y-2 text-sm">
                    @forelse ($candidate->resumes as $resume)
                        <li>{{ $resume->title }}@if ($resume->is_default) <span class="text-xs text-slate-500">(mặc định)</span>@endif</li>
                    @empty
                        <li class="text-slate-500">Chưa có CV.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5">
                <h2 class="mb-3 text-sm font-semibold">Lịch sử ứng tuyển ({{ $candidate->applications->count() }})</h2>
                <ul class="divide-y divide-slate-100 text-sm">
                    @forelse ($candidate->applications as $application)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-2">
                            <span>
                                <a href="{{ route('admin.jobs.show', $application->job) }}" class="font-medium text-sky-800 hover:underline">{{ $application->job->title }}</a>
                                <span class="text-xs text-slate-500">· {{ $application->job->company->name }} · {{ $application->applied_at?->format('d/m/Y') }}</span>
                            </span>
                            <x-admin.badge :status="$application->status" :label="\App\Models\Application::STATUS_LABELS[$application->status] ?? $application->status" />
                        </li>
                    @empty
                        <li class="text-slate-500">Chưa ứng tuyển tin nào.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
@endsection
