@extends('admin.layouts.app')

@section('title', $recruiter->user->name)

@section('actions')
    <x-admin.button variant="secondary" :href="route('admin.recruiters.edit', $recruiter)">Sửa</x-admin.button>
    <form method="POST" action="{{ route('admin.recruiters.toggle-status', $recruiter) }}">
        @csrf
        @method('PATCH')
        <x-admin.button variant="secondary">{{ $recruiter->user->status === 'active' ? 'Khóa tài khoản' : 'Mở khóa' }}</x-admin.button>
    </form>
    <form method="POST" action="{{ route('admin.recruiters.destroy', $recruiter) }}" onsubmit="return confirm('Xóa nhà tuyển dụng này?')">
        @csrf
        @method('DELETE')
        <x-admin.button variant="danger">Xóa</x-admin.button>
    </form>
@endsection

@section('content')
    <div class="grid gap-4 lg:grid-cols-3">
        <section class="rounded-lg border border-stone-200 bg-white p-5">
            <div class="mb-4 flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold">Thông tin</h2>
                <x-admin.badge :status="$recruiter->user->status" :label="\App\Models\User::STATUS_LABELS[$recruiter->user->status] ?? $recruiter->user->status" />
            </div>
            <dl class="space-y-3 text-sm">
                @foreach ([
                    'Email' => $recruiter->user->email,
                    'Điện thoại' => $recruiter->user->phone,
                    'Công ty' => $recruiter->company?->name,
                    'Chức vụ' => $recruiter->position,
                    'Ngày tạo' => $recruiter->created_at?->format('d/m/Y'),
                ] as $label => $value)
                    <div>
                        <dt class="text-stone-500">{{ $label }}</dt>
                        <dd class="break-words">{{ $value ?? '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- <<include>> Quản lý tin tuyển dụng --}}
        <section class="overflow-hidden rounded-lg border border-stone-200 bg-white lg:col-span-2">
            <div class="flex items-center justify-between gap-2 p-5 pb-3">
                <h2 class="text-sm font-semibold">Tin tuyển dụng ({{ $jobs->total() }})</h2>
                <a href="{{ route('admin.jobs.index', ['created_by' => $recruiter->user_id]) }}" class="text-sm text-teal-800 hover:underline">Lọc trong trang tin</a>
            </div>
            <ul class="divide-y divide-stone-100 text-sm">
                @forelse ($jobs as $job)
                    <li class="flex flex-wrap items-center justify-between gap-2 px-5 py-3">
                        <span>
                            <a href="{{ route('admin.jobs.show', $job) }}" class="font-medium text-teal-800 hover:underline">{{ $job->title }}</a>
                            <span class="block text-xs text-stone-500">{{ $job->industry->name }} · {{ $job->created_at?->format('d/m/Y') }}</span>
                        </span>
                        <x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" />
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-stone-500">Chưa có tin tuyển dụng nào.</li>
                @endforelse
            </ul>
            {{ $jobs->links('admin.partials.pagination') }}
        </section>
    </div>
@endsection
