@extends('client.layouts.app')

@section('title', 'Thông báo')
@section('description', 'Theo dõi các cập nhật mới về hồ sơ ứng tuyển của bạn.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-sky-800">Trung tâm cập nhật</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Thông báo</h1>
                    <p class="mt-2 text-slate-600">Bạn có <span class="font-bold text-sky-800">{{ $unreadCount }}</span> thông báo chưa đọc.</p>
                </div>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('candidate.notifications.read-all') }}">
                        @csrf
                        @method('PATCH')
                        <button class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-800 shadow-sm transition hover:bg-sky-50">
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.312a1 1 0 0 1-1.42.006l-3.75-3.688a1 1 0 1 1 1.403-1.426l3.04 2.987 6.557-6.611a1 1 0 0 1 1.414.006Z" clip-rule="evenodd" /></svg>
                            Đánh dấu tất cả đã xem
                        </button>
                    </form>
                @endif
            </div>

            <div class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @forelse ($notifications as $notification)
                    <div class="flex gap-4 border-b border-slate-100 p-4 last:border-0 sm:p-5 {{ $notification->read_at ? '' : 'bg-sky-50/70' }}">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-sky-100 text-sky-800' }}">
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-900">{{ $notification->title }}</h2>
                                @unless ($notification->read_at)
                                    <span class="rounded-full bg-sky-800 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-white">Mới</span>
                                @endunless
                            </div>
                            <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->content }}</p>
                            <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at?->format('H:i · d/m/Y') }}</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <a href="{{ route('candidate.notifications.open', $notification) }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-sky-800 px-3 text-xs font-bold text-white transition hover:bg-sky-900">
                                    Xem nội dung
                                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-3.5"><path fill-rule="evenodd" d="M3.25 10a.75.75 0 0 1 .75-.75h10.19l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                                </a>
                                @unless ($notification->read_at)
                                    <form method="POST" action="{{ route('candidate.notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="min-h-9 rounded-lg px-2 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-800">Đánh dấu đã xem</button>
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
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" class="size-7"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                        </span>
                        <h2 class="mt-4 font-bold text-slate-900">Bạn chưa có thông báo nào</h2>
                        <p class="mt-1 text-sm text-slate-500">Cập nhật về đơn ứng tuyển sẽ xuất hiện tại đây.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-5">{{ $notifications->links() }}</div>
        </div>
    </section>
@endsection
