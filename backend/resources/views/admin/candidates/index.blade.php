@extends('admin.layouts.app')

@section('title', 'Quản lý ứng viên')

@section('actions')
    <x-admin.button :href="route('admin.candidates.create')">Thêm ứng viên</x-admin.button>
@endsection

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-slate-200 bg-white p-4">
        <div class="min-w-56 flex-1"><x-admin.input name="q" label="Tìm kiếm" :value="request('q')" placeholder="Tên, email hoặc số điện thoại" /></div>
        <div class="w-44"><x-admin.select name="status" label="Trạng thái" :options="\App\Models\User::STATUS_LABELS" :value="request('status')" placeholder="Tất cả" /></div>
        <x-admin.button variant="secondary">Lọc</x-admin.button>
    </form>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Ứng viên</th>
                        <th class="px-4 py-3 font-medium">Vị trí mong muốn</th>
                        <th class="px-4 py-3 font-medium">Kinh nghiệm</th>
                        <th class="px-4 py-3 font-medium">Ứng tuyển</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($candidates as $candidate)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.candidates.show', $candidate) }}" class="font-medium text-sky-800 hover:underline">{{ $candidate->user->name }}</a>
                                <p class="text-xs text-slate-500">{{ $candidate->user->email }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $candidate->desired_position ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{{ $candidate->years_of_experience !== null ? (float) $candidate->years_of_experience.' năm' : '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $candidate->applications_count }}</td>
                            <td class="px-4 py-3"><x-admin.badge :status="$candidate->user->status" :label="\App\Models\User::STATUS_LABELS[$candidate->user->status] ?? $candidate->user->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.candidates.edit', $candidate) }}" class="text-sky-800 hover:underline">Sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">Không tìm thấy ứng viên nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $candidates->links('admin.partials.pagination') }}
    </div>
@endsection
