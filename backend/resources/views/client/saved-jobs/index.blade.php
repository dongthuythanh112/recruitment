@extends('client.layouts.app')

@section('title', 'Việc làm đã lưu')
@section('description', 'Danh sách việc làm bạn đã lưu trên Việc Ngon.')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-10 sm:py-14">
        <div class="flex flex-wrap items-end justify-between gap-4 border-b border-white/15 pb-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Tài khoản ứng viên</p>
                <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Việc làm đã lưu</h1>
                <p class="mt-2 text-sm text-paper/65">Các tin đang tuyển mà bạn đã đánh dấu để xem lại.</p>
            </div>
            <a href="{{ route('home') }}#viec-lam" class="board-link text-flap underline underline-offset-4">Tìm việc làm</a>
        </div>

        @if ($jobs->isEmpty())
            <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 px-5 py-12 text-center">
                <p class="font-signage text-2xl font-bold tracking-wide uppercase">Bạn chưa lưu việc làm nào</p>
                <p class="mt-2 text-sm text-paper/65">Mở một tin tuyển dụng rồi chọn “Lưu việc làm” để thêm vào danh sách.</p>
                <a href="{{ route('home') }}#viec-lam" class="btn-flap mt-5">Khám phá việc làm</a>
            </div>
        @else
            <div class="mt-6 space-y-3">
                @foreach ($jobs as $job)
                    <article class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-white/10 bg-white/5 p-4 sm:p-5">
                        <a href="{{ route('jobs.show', $job->slug) }}" class="min-w-0 flex-1">
                            <h2 class="truncate text-lg font-semibold text-white transition hover:text-flap">{{ $job->title }}</h2>
                            <p class="mt-1 truncate text-sm text-paper/70">{{ $job->company->name }} · {{ $job->location ?? 'Chưa rõ địa điểm' }}</p>
                            <p class="mt-2 text-sm text-flap">{{ $job->salaryShort() }} <span class="text-paper/50">· {{ $job->availabilityLabel() }}</span></p>
                        </a>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('jobs.show', $job->slug) }}" class="inline-flex min-h-10 items-center rounded-lg bg-flap px-4 text-sm font-semibold text-ink transition hover:bg-white">Xem tin</a>
                            <form method="POST" action="{{ route('candidate.saved-jobs.destroy', $job) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 items-center rounded-lg border border-white/20 px-3 text-sm text-paper/75 transition hover:border-rose-300 hover:text-rose-200">Bỏ lưu</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-6">{{ $jobs->links('client.partials.pagination') }}</div>
        @endif
    </section>
@endsection
