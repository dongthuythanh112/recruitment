@extends('admin.layouts.app')

@section('title', 'Quản lý thông báo')

@section('actions')
    @if ($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            @method('PATCH')
            <x-admin.button variant="secondary">Đánh dấu tất cả đã đọc</x-admin.button>
        </form>
    @endif
@endsection

@section('content')
    <section class="mb-5 flex flex-wrap items-end justify-between gap-3 rounded-2xl border border-sky-100 bg-gradient-to-r from-sky-50 to-white p-5 sm:p-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Trung tâm quản trị</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Thông báo của bạn</h2>
            <p class="mt-1 text-sm text-slate-600">Xem các sự kiện cần xử lý và trạng thái đã đọc.</p>
        </div>
        <div class="rounded-xl border border-sky-100 bg-white px-4 py-2 text-sm shadow-sm">
            <span class="font-bold text-sky-800">{{ $unreadCount }}</span>
            <span class="text-slate-500">chưa đọc</span>
        </div>
    </section>

    @if ($notifications->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center">
            <span aria-hidden="true" class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-800">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-7"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 13h4" /></svg>
            </span>
            <h2 class="mt-4 font-semibold text-slate-900">Chưa có thông báo</h2>
            <p class="mt-1 text-sm text-slate-500">Thông báo mới sẽ xuất hiện tại đây.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
                <div><h2 class="text-sm font-bold text-slate-900">Danh sách thông báo</h2><p class="mt-0.5 text-xs text-slate-500">{{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} trên {{ number_format($notifications->total()) }} thông báo</p></div>
                <span class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-800">Trang {{ $notifications->currentPage() }}</span>
            </div>
            <ul class="divide-y divide-slate-100">
                @foreach ($notifications as $notification)
                    <li @class([
                        'flex flex-wrap items-start gap-3 p-4 transition sm:items-center sm:gap-4 sm:px-5',
                        'bg-sky-50/70' => ! $notification->read_at,
                        'hover:bg-slate-50' => $notification->read_at,
                    ])>
                        <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold tabular-nums text-slate-500 sm:mt-0" aria-label="STT {{ $notifications->firstItem() + $loop->index }}">{{ $notifications->firstItem() + $loop->index }}</span>
                        <span @class([
                            'mt-0.5 grid size-10 shrink-0 place-items-center rounded-xl sm:mt-0',
                            'bg-sky-100 text-sky-800' => ! $notification->read_at,
                            'bg-slate-100 text-slate-500' => $notification->read_at,
                        ]) aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 13h4" /></svg>
                        </span>
                        <a href="{{ route('admin.notifications.open', $notification) }}" class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-slate-900">{{ $notification->title }}</span>
                                @unless ($notification->read_at)
                                    <span class="size-2 rounded-full bg-sky-600" aria-label="Chưa đọc"></span>
                                @endunless
                            </span>
                            <span class="mt-1 block line-clamp-2 text-sm text-slate-600">{{ $notification->content }}</span>
                            <span class="mt-1.5 block text-xs text-slate-400">{{ $notification->created_at?->format('d/m/Y H:i') }}</span>
                        </a>
                        <div class="ml-[3.25rem] flex items-center gap-3 sm:ml-0">
                            <a href="{{ route('admin.notifications.open', $notification) }}" class="text-xs font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Mở màn hình</a>
                            @unless ($notification->read_at)
                                <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-slate-500 underline underline-offset-4 hover:text-sky-800">Đánh dấu đã đọc</button>
                                </form>
                            @endunless
                        </div>
                    </li>
                @endforeach
            </ul>
            {{ $notifications->links('admin.partials.pagination') }}
        </div>
    @endif
@endsection
