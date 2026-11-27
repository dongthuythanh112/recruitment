@extends('client.layouts.app')

@section('title', 'Thông báo nhà tuyển dụng')
@section('description', 'Theo dõi thông báo về đơn ứng tuyển và hoạt động tuyển dụng.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Trung tâm cập nhật</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Thông báo</h1>
                    <p class="mt-2 text-sm text-slate-600">Thông báo trong 3 tháng gần nhất · <strong class="text-sky-800">{{ $unreadCount }} chưa đọc</strong></p>
                </div>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('recruiter.notifications.read-all') }}">
                        @csrf
                        @method('PATCH')
                        <button class="inline-flex min-h-10 items-center rounded-xl border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-800 shadow-sm transition hover:bg-sky-50">Đánh dấu tất cả đã xem</button>
                    </form>
                @endif
            </div>

            <div class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @forelse ($notifications as $notification)
                    <div class="flex gap-4 border-b border-slate-100 p-4 last:border-0 sm:p-5 {{ $notification->read_at ? '' : 'bg-sky-50/70' }}">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-sky-100 text-sky-800' }}">
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-900">{{ $notification->title }}</h2>
                                @unless ($notification->read_at)<span class="rounded-full bg-sky-800 px-2 py-0.5 text-[10px] font-extrabold text-white">Mới</span>@endunless
                            </div>
                            <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->content }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->format('H:i · d/m/Y') }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <a href="{{ route('recruiter.notifications.open', $notification) }}" class="inline-flex min-h-9 items-center rounded-lg bg-sky-800 px-3 text-xs font-bold text-white transition hover:bg-sky-900">Mở đơn ứng tuyển</a>
                                @unless ($notification->read_at)
                                    <form method="POST" action="{{ route('recruiter.notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="min-h-9 rounded-lg px-2 text-xs font-semibold text-slate-500 hover:bg-slate-100">Đánh dấu đã xem</button>
                                    </form>
                                @else
                                    <span class="text-xs font-medium text-emerald-700">Đã xem</span>
                                @endunless
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-14 text-center">
                        <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-800">
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                        </span>
                        <h2 class="mt-4 font-bold text-slate-900">Chưa có thông báo trong 3 tháng gần đây</h2>
                        <p class="mt-1 text-sm text-slate-500">Thông báo ứng viên nộp hoặc rút hồ sơ sẽ hiển thị tại đây.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $notifications->links() }}</div>
        </div>
    </section>
@endsection
