@extends('admin.layouts.app')

@section('title', 'Quản lý nhà tuyển dụng')

@section('actions')
    <x-admin.button :href="route('admin.recruiters.create')" class="gap-2 rounded-xl px-4 py-2.5 shadow-sm shadow-sky-900/10">
        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/></svg>
        Thêm nhà tuyển dụng
    </x-admin.button>
@endsection

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="max-w-2xl text-sm leading-6 text-slate-600">Quản lý tài khoản tuyển dụng, thông tin công ty và tin đã đăng.</p>
        <span class="inline-flex items-center gap-2 rounded-full border border-sky-100 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm"><span class="size-2 rounded-full bg-sky-600"></span>{{ number_format($recruiters->total()) }} nhà tuyển dụng</span>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_14rem_13rem_auto_auto] sm:items-end sm:p-5">
        <div class="relative min-w-0">
            <label for="recruiter-search" class="mb-1.5 block text-xs font-semibold text-slate-700">Tìm nhà tuyển dụng</label>
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute bottom-3 left-3 size-4 text-slate-400"><path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.42 9.81l3.63 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.63A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
            <input id="recruiter-search" name="q" type="search" value="{{ request('q') }}" placeholder="Tên, email hoặc số điện thoại..." class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100">
        </div>
        <div><x-admin.select name="company_id" label="Công ty" :options="$companies" :value="request('company_id')" placeholder="Tất cả công ty" /></div>
        <div><x-admin.select name="status" label="Trạng thái" :options="\App\Models\User::STATUS_LABELS" :value="request('status')" placeholder="Tất cả trạng thái" /></div>
        <x-admin.button class="min-h-11 rounded-xl px-5">Lọc danh sách</x-admin.button>
        @if (request()->filled('q') || request()->filled('company_id') || request()->filled('status'))
            <x-admin.button variant="secondary" :href="route('admin.recruiters.index')" class="min-h-11 rounded-xl px-4">Xóa lọc</x-admin.button>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.03]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div><h2 class="text-sm font-bold text-slate-900">Danh sách nhà tuyển dụng</h2><p class="mt-0.5 text-xs text-slate-500">{{ $recruiters->firstItem() ?? 0 }}–{{ $recruiters->lastItem() ?? 0 }} trên {{ number_format($recruiters->total()) }} tài khoản</p></div>
            <span class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-800">Trang {{ $recruiters->currentPage() }}</span>
        </div>
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold tracking-[0.08em] text-slate-500 uppercase"><tr><th class="px-4 py-3.5 sm:px-5">Nhà tuyển dụng</th><th class="px-4 py-3.5">Công ty</th><th class="px-4 py-3.5">Chức vụ</th><th class="px-4 py-3.5 text-center">Tin đã đăng</th><th class="px-4 py-3.5">Trạng thái</th><th class="px-4 py-3.5 text-right"><span class="sr-only">Thao tác</span></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recruiters as $recruiter)
                        <tr class="group transition-colors hover:bg-sky-50/50">
                            <td class="whitespace-nowrap px-4 py-3.5 sm:px-5"><div class="flex items-center gap-3">
                                @if ($recruiter->company?->logo)<img src="{{ $recruiter->company->logo }}" alt="" class="size-10 shrink-0 rounded-xl border border-slate-100 bg-white object-contain p-1">
                                @else<span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-50 text-sm font-extrabold text-sky-800 ring-1 ring-inset ring-sky-100">{{ mb_strtoupper(mb_substr($recruiter->user->name, 0, 1)) }}</span>@endif
                                <span class="min-w-0"><a href="{{ route('admin.recruiters.show', $recruiter) }}" class="block max-w-60 truncate font-semibold text-slate-900 transition hover:text-sky-800">{{ $recruiter->user->name }}</a><span class="mt-0.5 block text-xs text-slate-500">{{ $recruiter->user->email }}</span></span>
                            </div></td>
                            <td class="max-w-52 truncate px-4 py-3.5 font-medium text-slate-700">{{ $recruiter->company?->name ?? 'Chưa liên kết công ty' }}</td>
                            <td class="px-4 py-3.5 text-slate-600">{{ $recruiter->position ?: '—' }}</td>
                            <td class="px-4 py-3.5 text-center"><span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-bold tabular-nums text-sky-800">{{ number_format($recruiter->jobs_count) }}</span></td>
                            <td class="whitespace-nowrap px-4 py-3.5"><x-admin.badge :status="$recruiter->user->status" :label="\App\Models\User::STATUS_LABELS[$recruiter->user->status] ?? $recruiter->user->status" /></td>
                            <td class="px-4 py-3.5"><div class="flex justify-end"><a href="{{ route('admin.recruiters.edit', $recruiter) }}" aria-label="Sửa {{ $recruiter->user->name }}" title="Sửa nhà tuyển dụng" class="grid size-9 place-items-center rounded-lg text-slate-500 transition hover:bg-sky-100 hover:text-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500"><svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="m13.59 3.41 3 3a1.5 1.5 0 0 1 0 2.12l-8.3 8.3-4.3.95.95-4.3 8.3-8.3a1.5 1.5 0 0 1 2.12 0Z"/></svg></a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-16 text-center"><p class="font-semibold text-slate-800">Không tìm thấy nhà tuyển dụng</p><p class="mt-1 text-sm text-slate-500">Thử đổi từ khóa, công ty hoặc trạng thái.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($recruiters->hasPages())<div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $recruiters->links('admin.partials.pagination') }}</div>@endif
    </div>
@endsection
