@extends('admin.layouts.app')

@section('title', 'Quản lý nhà tuyển dụng')

@section('actions')
    <x-admin.button :href="route('admin.recruiters.create')">Thêm nhà tuyển dụng</x-admin.button>
@endsection

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-4">
        <div class="min-w-56 flex-1"><x-admin.input name="q" label="Tìm kiếm" :value="request('q')" placeholder="Tên, email hoặc số điện thoại" /></div>
        <div class="w-56"><x-admin.select name="company_id" label="Công ty" :options="$companies" :value="request('company_id')" placeholder="Tất cả" /></div>
        <div class="w-44"><x-admin.select name="status" label="Trạng thái" :options="\App\Models\User::STATUS_LABELS" :value="request('status')" placeholder="Tất cả" /></div>
        <x-admin.button variant="secondary">Lọc</x-admin.button>
    </form>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nhà tuyển dụng</th>
                        <th class="px-4 py-3 font-medium">Công ty</th>
                        <th class="px-4 py-3 font-medium">Chức vụ</th>
                        <th class="px-4 py-3 font-medium">Số tin</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recruiters as $recruiter)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.recruiters.show', $recruiter) }}" class="font-medium text-sky-800 hover:underline">{{ $recruiter->user->name }}</a>
                                <p class="text-xs text-slate-500">{{ $recruiter->user->email }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $recruiter->company?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $recruiter->position ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $recruiter->jobs_count }}</td>
                            <td class="px-4 py-3"><x-admin.badge :status="$recruiter->user->status" :label="\App\Models\User::STATUS_LABELS[$recruiter->user->status] ?? $recruiter->user->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.recruiters.edit', $recruiter) }}" class="text-sky-800 hover:underline">Sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Không tìm thấy nhà tuyển dụng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $recruiters->links('admin.partials.pagination') }}
    </div>
@endsection
