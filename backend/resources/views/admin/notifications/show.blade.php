@extends('admin.layouts.app')

@section('title', 'Chi tiết thông báo')

@section('actions')
    <x-admin.button :href="route('admin.notifications.index')" variant="secondary">Tất cả thông báo</x-admin.button>
@endsection

@section('content')
    <article class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header @class([
            'border-b px-5 py-5 sm:px-7',
            'border-sky-100 bg-sky-50/70' => ! $notification->read_at,
            'border-slate-100 bg-slate-50' => $notification->read_at,
        ])>
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[11px] font-semibold text-sky-900">{{ $notification->type === 'job.pending_review' ? 'Tin cần duyệt' : 'Thông báo hệ thống' }}</span>
                @if ($notification->read_at)
                    <span class="rounded-full bg-slate-200 px-2.5 py-1 text-[11px] font-medium text-slate-600">Đã đọc</span>
                @else
                    <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-sky-800 ring-1 ring-inset ring-sky-200">Chưa đọc</span>
                @endif
            </div>
            <h2 class="mt-3 text-xl font-bold text-slate-900 sm:text-2xl">{{ $notification->title }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $notification->created_at?->format('d/m/Y lúc H:i') }}</p>
        </header>

        <div class="space-y-5 p-5 sm:p-7">
            <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $notification->content ?: 'Không có nội dung chi tiết.' }}</p>

            @if ($job)
                <div class="rounded-xl border border-sky-100 bg-sky-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-800">Tin tuyển dụng liên quan</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $job->title }}</p>
                    <a href="{{ route('admin.jobs.show', $job) }}" class="mt-2 inline-flex text-sm font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Mở trang kiểm duyệt</a>
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('admin.notifications.index') }}" class="text-sm font-medium text-slate-600 underline underline-offset-4 hover:text-slate-900">Quay lại danh sách</a>
                @unless ($notification->read_at)
                    <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                        @csrf
                        @method('PATCH')
                        <x-admin.button>Đánh dấu đã đọc</x-admin.button>
                    </form>
                @endunless
            </div>
        </div>
    </article>
@endsection
