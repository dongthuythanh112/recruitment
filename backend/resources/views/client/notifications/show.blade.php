@extends('client.layouts.app')

@section('title', 'Chi tiết thông báo')
@section('description', 'Chi tiết thông báo trong tài khoản ứng viên.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
            <a href="{{ route('candidate.notifications.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950">
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M16.75 10a.75.75 0 0 1-.75.75H5.81l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22H16a.75.75 0 0 1 .75.75Z" clip-rule="evenodd" /></svg>
                Tất cả thông báo
            </a>
            <article class="mt-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
                <div class="flex size-14 items-center justify-center rounded-2xl bg-sky-100 text-sky-800">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                </div>
                <p class="mt-6 text-xs font-bold uppercase tracking-[0.16em] text-sky-800">{{ $notification->created_at?->format('H:i · d/m/Y') }}</p>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">{{ $notification->title }}</h1>
                <p class="mt-4 whitespace-pre-line text-base leading-7 text-slate-600">{{ $notification->content }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('candidate.notifications.open', $notification) }}" class="inline-flex min-h-11 items-center rounded-xl bg-sky-800 px-5 text-sm font-bold text-white transition hover:bg-sky-900">Đi tới nội dung được thông báo</a>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('candidate.notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <button class="min-h-11 rounded-xl border border-slate-200 px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Đánh dấu đã xem</button>
                        </form>
                    @else
                        <span class="inline-flex min-h-11 items-center rounded-xl bg-emerald-50 px-4 text-sm font-semibold text-emerald-700">Đã xem</span>
                    @endif
                </div>
            </article>
        </div>
    </section>
@endsection
