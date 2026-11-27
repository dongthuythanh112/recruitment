@extends('admin.layouts.app')

@section('title', 'Quản lý tin tuyển dụng')

@section('actions')
    <x-admin.button :href="route('admin.jobs.create')" class="gap-2 rounded-xl px-4 py-2.5 shadow-sm shadow-sky-900/10">
        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/></svg>
        Tạo tin
    </x-admin.button>
@endsection

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="max-w-2xl text-sm leading-6 text-slate-600">Duyệt, theo dõi và quản lý các tin tuyển dụng trên hệ thống.</p>
        <span class="inline-flex items-center gap-2 rounded-full border border-sky-100 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm"><span class="size-2 rounded-full bg-sky-600"></span>{{ number_format($jobs->total()) }} tin trong danh sách</span>
    </div>

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_14rem_14rem_auto_auto] sm:items-end sm:p-5">
        @if (request('created_by'))<input type="hidden" name="created_by" value="{{ request('created_by') }}">@endif
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <div class="relative min-w-0">
            <label for="job-search" class="mb-1.5 block text-xs font-semibold text-slate-700">Tìm tin tuyển dụng</label>
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute bottom-3 left-3 size-4 text-slate-400"><path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.42 9.81l3.63 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.63A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
            <input id="job-search" name="q" type="search" value="{{ request('q') }}" placeholder="Nhập tiêu đề tin..." class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100">
        </div>
        <div><x-admin.select name="industry_id" label="Ngành nghề" :options="$industries" :value="request('industry_id')" placeholder="Tất cả ngành nghề" /></div>
        <div><x-admin.select name="company_id" label="Công ty" :options="$companies" :value="request('company_id')" placeholder="Tất cả công ty" /></div>
        <x-admin.button class="min-h-11 rounded-xl px-5">Lọc danh sách</x-admin.button>
        @if (request()->filled('q') || request()->filled('industry_id') || request()->filled('company_id'))
            <x-admin.button variant="secondary" :href="route('admin.jobs.index', array_filter(['status' => $status, 'created_by' => request('created_by')]))" class="min-h-11 rounded-xl px-4">Xóa lọc</x-admin.button>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.03]">
        <nav aria-label="Lọc theo trạng thái" class="flex gap-2 overflow-x-auto border-b border-slate-100 bg-slate-50/60 px-4 py-3 sm:px-5">
            @foreach ($tabs as $key => $tab)
                <a href="{{ $tab['url'] }}" @if ($tab['active']) aria-current="page" @endif @class([
                    'inline-flex min-h-9 items-center gap-2 rounded-xl px-3 text-xs font-semibold whitespace-nowrap transition sm:text-sm',
                    'bg-sky-800 text-white shadow-sm shadow-sky-900/15' => $tab['active'],
                    'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-sky-50 hover:text-sky-800' => ! $tab['active'],
                ])>
                    {{ $tab['label'] }}
                    <span @class([
                        'rounded-full px-2 py-0.5 text-[10px] font-bold tabular-nums',
                        'bg-white/15 text-white' => $tab['active'],
                        'bg-slate-100 text-slate-600' => ! $tab['active'],
                    ])>{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div><h2 class="text-sm font-bold text-slate-900">Danh sách tin tuyển dụng</h2><p class="mt-0.5 text-xs text-slate-500">{{ $jobs->firstItem() ?? 0 }}–{{ $jobs->lastItem() ?? 0 }} trên {{ number_format($jobs->total()) }} tin</p></div>
            <span class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-800">Trang {{ $jobs->currentPage() }}</span>
        </div>

        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold tracking-[0.08em] text-slate-500 uppercase"><tr><th class="w-16 px-4 py-3.5 text-center sm:px-5">STT</th><th class="px-4 py-3.5 sm:px-5">Tin tuyển dụng</th><th class="px-4 py-3.5">Ngành nghề</th><th class="px-4 py-3.5">Người đăng</th><th class="px-4 py-3.5 text-center">Ứng tuyển</th><th class="px-4 py-3.5">Hạn nộp</th>@unless ($status)<th class="px-4 py-3.5">Trạng thái</th>@endunless<th class="px-4 py-3.5 text-right"><span class="sr-only">Thao tác</span></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($jobs as $job)
                        <tr class="group transition-colors hover:bg-sky-50/50">
                            <td class="px-4 py-3.5 text-center text-xs font-bold tabular-nums text-slate-500 sm:px-5">{{ $jobs->firstItem() + $loop->index }}</td>
                            <td class="min-w-64 px-4 py-3.5 sm:px-5"><div class="flex items-center gap-3"><span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-50 text-sm font-extrabold text-sky-800 ring-1 ring-inset ring-sky-100">{{ mb_strtoupper(mb_substr($job->company->name, 0, 1)) }}</span><span class="min-w-0"><a href="{{ route('admin.jobs.show', $job) }}" class="block max-w-72 truncate font-semibold text-slate-900 transition hover:text-sky-800">{{ $job->title }}</a><span class="mt-0.5 block truncate text-xs text-slate-500">{{ $job->company->name }}</span></span></div></td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-slate-600">{{ $job->industry->name }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-slate-600">{{ $job->creator->name }}</td>
                            <td class="px-4 py-3.5 text-center"><span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-bold tabular-nums text-sky-800">{{ number_format($job->applications_count) }}</span></td>
                            <td class="whitespace-nowrap px-4 py-3.5 text-slate-600">{{ $job->deadline?->format('d/m/Y') ?? '—' }}</td>
                            @unless ($status)<td class="whitespace-nowrap px-4 py-3.5"><x-admin.badge :status="$job->status" :label="\App\Models\Job::STATUS_LABELS[$job->status] ?? $job->status" /></td>@endunless
                            <td class="px-4 py-3.5"><div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.jobs.edit', $job) }}" aria-label="Sửa {{ $job->title }}" title="Sửa tin" class="grid size-9 place-items-center rounded-lg text-slate-500 transition hover:bg-sky-100 hover:text-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500"><svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="m13.59 3.41 3 3a1.5 1.5 0 0 1 0 2.12l-8.3 8.3-4.3.95.95-4.3 8.3-8.3a1.5 1.5 0 0 1 2.12 0Z"/></svg></a>
                                @if ($job->status === \App\Models\Job::STATUS_PENDING)
                                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">@csrf<input type="hidden" name="action" value="approve"><button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-emerald-50 px-3 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">Duyệt</button></form>
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="inline-flex min-h-9 items-center rounded-lg bg-rose-50 px-3 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Xem xét</a>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $status ? 7 : 8 }}" class="px-5 py-16 text-center"><p class="font-semibold text-slate-800">Không tìm thấy tin tuyển dụng</p><p class="mt-1 text-sm text-slate-500">Thử đổi bộ lọc hoặc trạng thái tin.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($jobs->hasPages())<div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $jobs->links('admin.partials.pagination') }}</div>@endif
    </div>
@endsection
