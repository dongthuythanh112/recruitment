@extends('admin.layouts.app')

@section('title', 'Quản lý ngành nghề')

@section('actions')
    <x-admin.button :href="route('admin.industries.create')" class="gap-2 rounded-xl px-4 py-2.5 shadow-sm shadow-sky-900/10">
        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v5.5h5.5a.75.75 0 0 1 0 1.5h-5.5v5.5a.75.75 0 0 1-1.5 0v-5.5h-5.5a.75.75 0 0 1 0-1.5h5.5v-5.5A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/></svg>
        Thêm ngành nghề
    </x-admin.button>
@endsection

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="max-w-2xl text-sm leading-6 text-slate-600">Sắp xếp hệ thống ngành nghề theo cấp bậc, theo dõi tin tuyển dụng và trạng thái sử dụng.</p>
        <span class="inline-flex items-center gap-2 rounded-full border border-sky-100 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm">
            <span class="size-2 rounded-full bg-sky-600"></span>
            {{ number_format($industries->total()) }} ngành nghề
        </span>
    </div>

    <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[minmax(0,1fr)_13rem_auto_auto] sm:items-end sm:p-5">
        <div class="relative min-w-0">
            <label for="industry-search" class="mb-1.5 block text-xs font-semibold text-slate-700">Tìm ngành nghề</label>
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute bottom-3 left-3 size-4 text-slate-400"><path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.42 9.81l3.63 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.63A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd"/></svg>
            <input id="industry-search" name="q" type="search" value="{{ request('q') }}" placeholder="Nhập tên ngành nghề..." class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100">
        </div>
        <div>
            <x-admin.select name="status" label="Trạng thái" :options="\App\Models\Industry::STATUS_LABELS" :value="request('status')" placeholder="Tất cả trạng thái" />
        </div>
        <x-admin.button class="min-h-11 rounded-xl px-5">Lọc danh sách</x-admin.button>
        @if (request()->filled('q') || request()->filled('status'))
            <x-admin.button variant="secondary" :href="route('admin.industries.index')" class="min-h-11 rounded-xl px-4">Xóa lọc</x-admin.button>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/[0.03]">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Danh sách ngành nghề</h2>
                <p class="mt-0.5 text-xs text-slate-500">{{ $industries->firstItem() ?? 0 }}–{{ $industries->lastItem() ?? 0 }} trên {{ number_format($industries->total()) }} ngành nghề</p>
            </div>
            <span class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-800">Trang {{ $industries->currentPage() }}</span>
        </div>

        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold tracking-[0.08em] text-slate-500 uppercase">
                    <tr>
                        <th scope="col" class="w-16 px-4 py-3.5 text-center sm:px-5">STT</th>
                        <th scope="col" class="px-4 py-3.5 sm:px-5">Ngành nghề</th>
                        <th scope="col" class="px-4 py-3.5">Ngành cha</th>
                        <th scope="col" class="px-4 py-3.5 text-center">Tin tuyển dụng</th>
                        <th scope="col" class="px-4 py-3.5 text-center">Ngành con</th>
                        <th scope="col" class="px-4 py-3.5">Trạng thái</th>
                        <th scope="col" class="px-4 py-3.5 text-right"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($industries as $industry)
                        <tr class="group transition-colors hover:bg-sky-50/50">
                            <td class="px-4 py-3.5 text-center text-xs font-bold tabular-nums text-slate-500 sm:px-5">{{ $industries->firstItem() + $loop->index }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 sm:px-5">
                                <div class="flex items-center gap-3">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-50 text-sm font-extrabold text-sky-800 ring-1 ring-inset ring-sky-100">
                                        {{ mb_strtoupper(mb_substr($industry->name, 0, 1)) }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block max-w-64 truncate font-semibold text-slate-900">{{ $industry->name }}</span>
                                        <span class="mt-0.5 block text-xs text-slate-400">/{{ $industry->slug }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5">
                                @if ($industry->parent)
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-200">
                                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-3.5 text-slate-400"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                                        {{ $industry->parent->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400"><span class="size-1.5 rounded-full bg-sky-400"></span>Ngành gốc</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-bold tabular-nums text-sky-800">{{ number_format($industry->jobs_count) }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex min-w-10 items-center justify-center rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold tabular-nums text-slate-600">{{ number_format($industry->children_count) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5"><x-admin.badge :status="$industry->status" :label="\App\Models\Industry::STATUS_LABELS[$industry->status] ?? $industry->status" /></td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.industries.edit', $industry) }}" aria-label="Sửa {{ $industry->name }}" title="Sửa ngành nghề" class="grid size-9 place-items-center rounded-lg text-slate-500 transition hover:bg-sky-100 hover:text-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="m13.59 3.41 3 3a1.5 1.5 0 0 1 0 2.12l-8.3 8.3-4.3.95.95-4.3 8.3-8.3a1.5 1.5 0 0 1 2.12 0ZM4.84 14.4l.76.76 1.52-.34-1.94-1.94-.34 1.52Z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.industries.destroy', $industry) }}" onsubmit="return confirm('Xóa ngành nghề này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" aria-label="Xóa {{ $industry->name }}" title="Xóa ngành nghề" class="grid size-9 place-items-center rounded-lg text-slate-400 transition hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500">
                                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M3.5 5.5h13m-11.5 0 .8 11h9.4l.8-11M7.5 5.5V3.75h5V5.5m-4 3v5m3-5v5"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <span class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" class="size-6"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/></svg>
                                </span>
                                <p class="mt-3 font-semibold text-slate-800">Không tìm thấy ngành nghề</p>
                                <p class="mt-1 text-sm text-slate-500">Thử đổi từ khóa hoặc trạng thái, hoặc tạo ngành nghề mới.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($industries->hasPages())
            <div class="border-t border-slate-100 px-4 py-3 sm:px-5">{{ $industries->links('admin.partials.pagination') }}</div>
        @endif
    </div>
@endsection
