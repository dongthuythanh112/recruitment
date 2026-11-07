@extends('admin.layouts.app')

@section('title', 'Duyệt tin tuyển dụng')

@section('content')
    <div class="overflow-hidden rounded-lg border border-stone-200 bg-white">
        <div class="relative overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-xs text-stone-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 font-medium">Tin chờ duyệt</th>
                        <th class="px-4 py-3 font-medium">Ngành nghề</th>
                        <th class="px-4 py-3 font-medium">Người đăng</th>
                        <th class="px-4 py-3 font-medium">Ngày gửi</th>
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
                            <td class="px-4 py-3 whitespace-nowrap">{{ $job->created_at?->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <x-admin.button variant="secondary" :href="route('admin.jobs.show', $job)">Xem và từ chối</x-admin.button>
                                    <form method="POST" action="{{ route('admin.jobs.moderate', $job) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <x-admin.button>Duyệt</x-admin.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-stone-500">Không còn tin nào chờ duyệt.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $jobs->links('admin.partials.pagination') }}
    </div>
@endsection
