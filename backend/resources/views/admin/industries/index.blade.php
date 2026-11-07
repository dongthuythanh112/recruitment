@extends('admin.layouts.app')

@section('title', 'Quản lý ngành nghề')

@section('actions')
    <x-admin.button :href="route('admin.industries.create')">Thêm ngành nghề</x-admin.button>
@endsection

@section('content')
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-3 rounded-lg border border-stone-200 bg-white p-4">
        <div class="min-w-56 flex-1"><x-admin.input name="q" label="Tìm theo tên" :value="request('q')" /></div>
        <div class="w-44"><x-admin.select name="status" label="Trạng thái" :options="\App\Models\Industry::STATUS_LABELS" :value="request('status')" placeholder="Tất cả" /></div>
        <x-admin.button variant="secondary">Lọc</x-admin.button>
    </form>

    <div class="overflow-hidden rounded-lg border border-stone-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-xs text-stone-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Ngành nghề</th>
                        <th class="px-4 py-3 font-medium">Ngành cha</th>
                        <th class="px-4 py-3 font-medium">Số tin</th>
                        <th class="px-4 py-3 font-medium">Ngành con</th>
                        <th class="px-4 py-3 font-medium">Trạng thái</th>
                        <th class="px-4 py-3"><span class="sr-only">Thao tác</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($industries as $industry)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-medium">{{ $industry->name }}</span>
                                <p class="text-xs text-stone-500">{{ $industry->slug }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $industry->parent?->name ?? '—' }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $industry->jobs_count }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $industry->children_count }}</td>
                            <td class="px-4 py-3"><x-admin.badge :status="$industry->status" :label="\App\Models\Industry::STATUS_LABELS[$industry->status] ?? $industry->status" /></td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3 whitespace-nowrap">
                                    <a href="{{ route('admin.industries.edit', $industry) }}" class="text-teal-800 hover:underline">Sửa</a>
                                    <form method="POST" action="{{ route('admin.industries.destroy', $industry) }}" onsubmit="return confirm('Xóa ngành nghề này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer text-red-700 hover:underline">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-stone-500">Không tìm thấy ngành nghề nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $industries->links('admin.partials.pagination') }}
    </div>
@endsection
