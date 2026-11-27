@extends('admin.layouts.app')

@section('title', 'Quản lý tin tuyển dụng')

@section('actions')
    <x-admin.button :href="route('admin.jobs.create')">Tạo tin</x-admin.button>
@endsection

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-4">
        @if (request('created_by'))
            <input type="hidden" name="created_by" value="{{ request('created_by') }}">
        @endif
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div class="min-w-56 flex-1"><x-admin.input name="q" label="Tìm theo tiêu đề" :value="request('q')" /></div>
        <div class="w-52"><x-admin.select name="industry_id" label="Ngành nghề" :options="$industries" :value="request('industry_id')" placeholder="Tất cả" /></div>
        <div class="w-52"><x-admin.select name="company_id" label="Công ty" :options="$companies" :value="request('company_id')" placeholder="Tất cả" /></div>
        <x-admin.button variant="secondary">Lọc</x-admin.button>
    </form>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <nav aria-label="Lọc theo trạng thái" class="flex gap-1 overflow-x-auto border-b border-slate-200 px-2">
            @foreach ($tabs as $key => $tab)
                <a href="{{ $tab['url'] }}" @if ($tab['active']) aria-current="page" @endif @class([
                    'flex items-center gap-2 border-b-2 px-3 py-3 text-sm whitespace-nowrap',
                    'border-sky-700 font-medium text-sky-800' => $tab['active'],
                    'border-transparent text-slate-600 hover:text-slate-900' => ! $tab['active'],
                ])>
                    {{ $tab['label'] }}
                    <span @class([
                        'rounded-full px-2 text-xs font-medium tabular-nums',
                        'bg-amber-200 text-amber-900' => $key === \App\Models\Job::STATUS_PENDING && $tab['count'] > 0,
                        'bg-slate-100 text-slate-600' => ! ($key === \App\Models\Job::STATUS_PENDING && $tab['count'] > 0),
                    ])>{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Tin</th>
                        <th class="px-4 py-3 font-medium">Ngành nghề</th>
                        <th class="px-4 py-3 font-medium">Người đăng</th>
                        <th class="px-4 py-3 font-medium">Ứng tuyển</th>
                        <th class="px-4 py-3 font-medium">Hạn nộp</th>
                        @unless ($status)
                            <th class="px-4 py-3 font-medium">Trạng thái</th>
                        @endunless
                        <th class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jobs as $job)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="font-medium text-sky-800 hover:underline">{{ $job->title }}</a>
                                <p class="text-xs text-slate-500">{{ $job->company->name }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $job->industry->name }}</td>
                            <td class="px-4 py-3">{{ $job->creator->name }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $job->applications_count }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $job->deadline?->format('d/m/Y') ?? '—' }}</td>
                            @unless ($status)
                                <td class="px-4 py-3"><x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" /></td>
                            @endunless
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="text-sm text-sky-800 hover:underline">Sửa</a>
                                    @if ($job->status === \App\Models\Job::STATUS_PENDING)
                                        <a href="{{ route('admin.jobs.show', $job) }}" class="text-sm text-red-700 hover:underline">Từ chối</a>
                                        <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <x-admin.button>Duyệt</x-admin.button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $status ? 6 : 7 }}" class="px-4 py-10 text-center text-slate-500">Không tìm thấy tin tuyển dụng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jobs->links('admin.partials.pagination') }}
    </div>
@endsection
