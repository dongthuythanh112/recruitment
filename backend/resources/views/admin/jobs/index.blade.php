@extends('admin.layouts.app')

@section('title', 'Quản lý tin tuyển dụng')

@section('actions')
    <x-admin.button :href="route('admin.jobs.create')">Tạo tin</x-admin.button>
@endsection

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-stone-200 bg-white p-4">
        @if (request('created_by'))
            <input type="hidden" name="created_by" value="{{ request('created_by') }}">
        @endif
        <div class="min-w-56 flex-1"><x-admin.input name="q" label="Tìm theo tiêu đề" :value="request('q')" /></div>
        <div class="w-44"><x-admin.select name="status" label="Trạng thái" :options="\App\Models\Job::STATUS_LABELS" :value="request('status')" placeholder="Tất cả" /></div>
        <div class="w-52"><x-admin.select name="industry_id" label="Ngành nghề" :options="$industries" :value="request('industry_id')" placeholder="Tất cả" /></div>
        <div class="w-52"><x-admin.select name="company_id" label="Công ty" :options="$companies" :value="request('company_id')" placeholder="Tất cả" /></div>
        <x-admin.button variant="secondary">Lọc</x-admin.button>
    </form>

    <div class="overflow-hidden rounded-lg border border-stone-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-xs text-stone-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Tin</th>
                        <th class="px-4 py-3 font-medium">Ngành nghề</th>
                        <th class="px-4 py-3 font-medium">Người đăng</th>
                        <th class="px-4 py-3 font-medium">Ứng tuyển</th>
                        <th class="px-4 py-3 font-medium">Hạn nộp</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($jobs as $job)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="font-medium text-teal-800 hover:underline">{{ $job->title }}</a>
                                <p class="text-xs text-stone-500">{{ $job->company->name }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $job->industry->name }}</td>
                            <td class="px-4 py-3">{{ $job->creator->name }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $job->applications_count }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $job->deadline?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="text-teal-800 hover:underline">Sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-stone-500">Không tìm thấy tin tuyển dụng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jobs->links('admin.partials.pagination') }}
    </div>
@endsection
