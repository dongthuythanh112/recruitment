@extends('client.layouts.app')

@section('title', 'Gợi ý việc làm')
@section('description', 'Danh sách việc làm phù hợp với hồ sơ và định hướng nghề nghiệp của bạn.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Dành riêng cho bạn</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Gợi ý việc làm</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Danh sách được chọn theo vị trí mong muốn, địa chỉ, mức lương, kinh nghiệm và kỹ năng hiện có trong hồ sơ.</p>
                </div>
                <a href="{{ route('candidate.profile.edit') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-800 shadow-sm transition hover:bg-sky-50">
                    Cập nhật hồ sơ
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M3.25 10a.75.75 0 0 1 .75-.75h10.19l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                </a>
            </div>

            @if ($recommendedJobs->isNotEmpty())
                <div class="mt-8">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="grid size-10 place-items-center rounded-xl bg-sky-100 text-sky-800">
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2L12 3Z" /><path stroke-linecap="round" d="m19 15 .8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z" /></svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Phù hợp với hồ sơ của bạn</h2>
                            <p class="text-xs text-slate-500">{{ $recommendedJobs->count() }} vị trí được gợi ý theo mức độ phù hợp</p>
                        </div>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($recommendedJobs as $suggestion)
                            @php($job = $suggestion['job'])
                            <article class="flex min-h-56 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="line-clamp-2 text-base font-bold leading-6 text-slate-900">{{ $job->title }}</h3>
                                        <p class="mt-1 truncate text-sm text-slate-600">{{ $job->company->name }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Phù hợp</span>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
                                    <span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ $job->location ?: 'Linh hoạt địa điểm' }}</span>
                                    <span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ $job->salaryShort() }}</span>
                                    @if ($job->experience_years !== null)
                                        <span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ number_format((float) $job->experience_years, 1, ',', '') }}+ năm kinh nghiệm</span>
                                    @endif
                                </div>
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($suggestion['reasons'] as $reason)
                                        <span class="rounded-full border border-sky-100 bg-sky-50 px-2 py-1 text-[10px] font-medium text-sky-800">{{ $reason }}</span>
                                    @endforeach
                                </div>
                                <a href="{{ route('jobs.show', $job->slug) }}" class="mt-auto inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-sky-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-sky-900">
                                    Xem tin tuyển dụng
                                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M3.25 10a.75.75 0 0 1 .75-.75h10.19l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H4a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($featuredJobs->isNotEmpty())
                <div class="mt-9 {{ $recommendedJobs->isNotEmpty() ? 'border-t border-slate-200 pt-8' : '' }}">
                    <div class="mb-4">
                        <h2 class="text-lg font-bold text-slate-900">Việc làm nổi bật đang tuyển</h2>
                        <p class="mt-1 text-sm text-slate-500">Những vị trí đang mở được ứng viên quan tâm nhiều trên hệ thống.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($featuredJobs as $job)
                            <a href="{{ route('jobs.show', $job->slug) }}" class="group flex min-h-24 items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-300 hover:shadow-md">
                                <span class="min-w-0">
                                    <span class="block truncate font-bold text-slate-900 group-hover:text-sky-800">{{ $job->title }}</span>
                                    <span class="mt-1 block truncate text-xs text-slate-500">{{ $job->company->name }} · {{ $job->location ?: 'Linh hoạt địa điểm' }}</span>
                                    <span class="mt-2 block text-xs font-semibold text-sky-800">{{ $job->salaryShort() }}</span>
                                </span>
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0 text-slate-400 transition group-hover:translate-x-0.5 group-hover:text-sky-700"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06L11.69 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($recommendedJobs->isEmpty() && $featuredJobs->isEmpty())
                <div class="mt-8 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-12 text-center">
                    <h2 class="font-bold text-slate-900">Chưa có tin tuyển dụng đang mở</h2>
                    <p class="mt-1 text-sm text-slate-500">Các gợi ý việc làm sẽ xuất hiện ở đây khi có tin được duyệt.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
