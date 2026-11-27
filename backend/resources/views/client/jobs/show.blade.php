@extends('client.layouts.app')

@section('title', $job->title)
@section('description', \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $job->description), 155))

@section('content')
    @php
        $company = $job->company;
        $sections = array_filter([
            'Mô tả công việc' => $job->description,
            'Yêu cầu ứng viên' => $job->requirements,
            'Quyền lợi' => $job->benefits,
        ]);
        $facts = array_filter([
            ['Địa điểm làm việc', $job->location, 'location'],
            ['Ngành nghề', $job->industry?->name, 'industry'],
            ['Hình thức làm việc', \App\Models\Job::EMPLOYMENT_TYPE_LABELS[$job->employment_type] ?? null, 'work'],
            ['Nơi làm việc', \App\Models\Job::WORKPLACE_TYPE_LABELS[$job->workplace_type] ?? null, 'place'],
            ['Kinh nghiệm', $job->experience_years !== null ? 'Từ '.(float) $job->experience_years.' năm' : null, 'experience'],
            ['Số lượng tuyển', $job->quantity ? $job->quantity.' người' : null, 'people'],
            ['Hạn nộp hồ sơ', $job->deadline?->format('d/m/Y'), 'date'],
        ], fn ($fact) => filled($fact[1]));
        $websiteUrl = $company->website && preg_match('#^https?://#i', $company->website) ? $company->website : null;
    @endphp

    <div class="min-h-screen bg-gradient-to-b from-sky-50 via-white to-white pb-16">
        <div class="mx-auto max-w-6xl px-4 pt-6 sm:pt-9">
            <a href="{{ route('home') }}#viec-lam" class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-semibold text-sky-800 transition hover:bg-sky-100 hover:text-sky-950">
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 0 1 0 1.06L8.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                Tất cả việc làm
            </a>

            <header class="relative mt-4 overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-xl shadow-sky-900/[0.06]" data-enter>
                <div aria-hidden="true" class="absolute -right-20 -top-28 size-80 rounded-full bg-sky-100/70 blur-3xl"></div>
                <div class="relative p-5 sm:p-8 lg:p-10">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-sky-50 px-3 py-1.5 text-[11px] font-bold tracking-wide text-sky-800">Mã tin #{{ str_pad((string) $job->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-800">Đang nhận hồ sơ</span>
                        @if ($job->isClosingSoon())
                            <span class="rounded-full bg-amber-50 px-3 py-1.5 text-[11px] font-bold text-amber-800">Sắp hết hạn</span>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center sm:gap-6">
                        <div class="grid size-20 shrink-0 place-items-center overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 shadow-sm sm:size-24">
                            @if ($company->logo)
                                <img src="{{ $company->logo }}" alt="Logo {{ $company->name }}" class="size-full object-contain p-2">
                            @else
                                <img src="{{ asset('images/default-company-logo.svg') }}" alt="Logo mặc định {{ $company->name }}" class="size-full object-contain p-2">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-sky-800">{{ $company->name }}</p>
                            <h1 class="mt-2 text-3xl leading-tight font-extrabold tracking-tight text-slate-950 text-balance sm:text-4xl lg:text-5xl">{{ $job->title }}</h1>
                            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-600">
                                @if ($job->location)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-sky-700"><path fill-rule="evenodd" d="M10 1.75a6.25 6.25 0 0 0-6.25 6.25c0 4.35 5.47 9.56 5.7 9.78a.8.8 0 0 0 1.1 0c.23-.22 5.7-5.43 5.7-9.78A6.25 6.25 0 0 0 10 1.75Zm0 8.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                                        {{ $job->location }}
                                    </span>
                                @endif
                                @if ($job->industry)<span>{{ $job->industry->name }}</span>@endif
                                @if ($job->published_at)<span>Đăng {{ $job->published_at->diffForHumans() }}</span>@endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-8 lg:px-10">
                    <p class="text-sm text-slate-600">Cơ hội nghề nghiệp tại <span class="font-semibold text-slate-900">{{ $company->name }}</span></p>
                    @if ($websiteUrl)
                        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener nofollow noreferrer" class="inline-flex items-center gap-1.5 text-sm font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Tìm hiểu công ty <span aria-hidden="true">↗</span></a>
                    @endif
                </div>
            </header>

            <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-8">
                <main class="min-w-0 space-y-5">
                    <section class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm shadow-slate-900/[0.025] sm:p-7" aria-labelledby="job-overview">
                        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                            <span class="grid size-10 place-items-center rounded-xl bg-sky-100 text-sky-800">
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                            </span>
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-sky-800">Thông tin nhanh</p>
                                <h2 id="job-overview" class="mt-0.5 text-lg font-bold text-slate-900">Tổng quan công việc</h2>
                            </div>
                        </div>
                        <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-gradient-to-br from-sky-50 to-cyan-50 p-4 sm:col-span-2">
                                <dt class="text-xs font-semibold text-slate-500">Mức lương</dt>
                                <dd class="mt-1 text-2xl font-extrabold tracking-tight text-sky-900 sm:text-3xl">{{ $job->salaryShort() }}</dd>
                            </div>
                            @foreach ($facts as [$label, $value, $icon])
                                <div class="min-w-0 rounded-xl border border-slate-100 p-3.5">
                                    <dt class="text-xs font-medium text-slate-500">{{ $label }}</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    @foreach ($sections as $heading => $body)
                        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm shadow-slate-900/[0.025] sm:p-7" aria-label="{{ $heading }}">
                            <div class="flex items-start gap-3">
                                <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-xl bg-sky-100 text-sm font-extrabold text-sky-800">{{ $loop->iteration }}</span>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-xl font-bold tracking-tight text-slate-900">{{ $heading }}</h2>
                                    <p class="mt-4 max-w-prose whitespace-pre-line text-sm leading-7 text-slate-700">{{ $body }}</p>
                                </div>
                            </div>
                        </section>
                    @endforeach

                    @if ($job->skills->isNotEmpty())
                        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm shadow-slate-900/[0.025] sm:p-7" aria-labelledby="job-skills">
                            <h2 id="job-skills" class="text-xl font-bold tracking-tight text-slate-900">Kỹ năng cần có</h2>
                            <ul class="mt-4 flex flex-wrap gap-2">
                                @foreach ($job->skills as $skill)
                                    <li class="rounded-full border border-sky-100 bg-sky-50 px-3.5 py-2 text-sm font-semibold text-sky-900">{{ $skill->name }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm shadow-slate-900/[0.025] sm:p-7" aria-labelledby="about-company">
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-xl border border-slate-100 bg-slate-50">
                                @if ($company->logo)
                                    <img src="{{ $company->logo }}" alt="" class="size-full object-contain p-1">
                                @else
                                    <img src="{{ asset('images/default-company-logo.svg') }}" alt="" class="size-full object-contain p-1">
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sky-800">Nhà tuyển dụng</p>
                                <h2 id="about-company" class="mt-0.5 truncate text-lg font-bold text-slate-900">{{ $company->name }}</h2>
                            </div>
                        </div>
                        @if ($company->description)
                            <p class="mt-4 max-w-prose whitespace-pre-line text-sm leading-7 text-slate-600">{{ $company->description }}</p>
                        @endif
                        <ul class="mt-5 grid gap-4 border-t border-slate-100 pt-5 text-sm text-slate-600 sm:grid-cols-2">
                            @if ($company->address)<li><span class="block text-xs font-medium text-slate-400">Địa chỉ</span><span class="mt-1 block">{{ $company->address }}</span></li>@endif
                            @if ($company->company_size)<li><span class="block text-xs font-medium text-slate-400">Quy mô</span><span class="mt-1 block">{{ $company->company_size }} nhân sự</span></li>@endif
                            @if ($company->email)<li><span class="block text-xs font-medium text-slate-400">Email tuyển dụng</span><a href="mailto:{{ $company->email }}" class="mt-1 block break-all font-medium text-sky-800 underline underline-offset-4">{{ $company->email }}</a></li>@endif
                            @if ($websiteUrl)<li><span class="block text-xs font-medium text-slate-400">Website</span><a href="{{ $websiteUrl }}" target="_blank" rel="noopener nofollow noreferrer" class="mt-1 block break-all font-medium text-sky-800 underline underline-offset-4">{{ $websiteUrl }}</a></li>@endif
                        </ul>
                        <a href="{{ route('home', ['q' => $company->name]) }}#viec-lam" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-sky-800 hover:text-sky-950">Xem các vị trí khác <span aria-hidden="true">→</span></a>
                    </section>
                </main>

                <aside class="lg:sticky lg:top-20" aria-label="Ứng tuyển công việc">
                    <div class="overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-lg shadow-sky-900/[0.06]">
                        <div class="bg-gradient-to-br from-sky-800 to-sky-700 p-5 text-white sm:p-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-sky-100">Sẵn sàng ứng tuyển?</p>
                            <h2 class="mt-1 text-2xl font-extrabold tracking-tight">Bắt đầu ngay</h2>
                            <p class="mt-2 text-sm leading-6 text-sky-100">Gửi hồ sơ cho nhà tuyển dụng hoặc lưu tin để xem lại sau.</p>
                        </div>
                        <div class="p-5 sm:p-6">
                            @auth
                                @if (auth()->user()->role === \App\Models\User::ROLE_CANDIDATE)
                                    @if ($application && $application->status !== 'withdrawn')
                                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                                            <p class="text-sm font-bold text-emerald-900">Bạn đã ứng tuyển vị trí này</p>
                                            <p class="mt-1 text-xs text-emerald-800">Trạng thái: {{ \App\Models\Application::STATUS_LABELS[$application->status] ?? $application->status }}</p>
                                            <a href="{{ route('candidate.applications.index') }}" class="mt-2 inline-flex text-xs font-bold text-emerald-900 underline underline-offset-4">Theo dõi hồ sơ</a>
                                        </div>
                                    @else
                                        <a href="{{ route('candidate.applications.create', $job) }}" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-sky-800 px-4 text-sm font-bold text-white transition hover:bg-sky-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700">
                                            Ứng tuyển ngay <span aria-hidden="true">→</span>
                                        </a>
                                    @endif
                                @else
                                    <p class="rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">Đăng nhập bằng tài khoản ứng viên để ứng tuyển vị trí này.</p>
                                @endif
                            @else
                                <a href="{{ route('login', ['role' => 'candidate']) }}" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-sky-800 px-4 text-sm font-bold text-white transition hover:bg-sky-900">Đăng nhập để ứng tuyển</a>
                            @endauth

                            @if (auth()->check() && auth()->user()->role === \App\Models\User::ROLE_CANDIDATE)
                                @if ($isSaved)
                                    <form method="POST" action="{{ route('candidate.saved-jobs.destroy', $job) }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700"><span aria-hidden="true">♥</span> Đã lưu · Bỏ lưu</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('candidate.saved-jobs.store', $job) }}" class="mt-3">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-800"><span aria-hidden="true">♡</span> Lưu việc làm</button>
                                    </form>
                                @endif
                            @elseif (!auth()->check())
                                <a href="{{ route('login') }}" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-800">Đăng nhập để lưu tin</a>
                            @endif

                            <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4 text-emerald-600"><path fill-rule="evenodd" d="M10 1.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Zm3.12 6.1a.75.75 0 0 0-1.1-1.02l-3.04 3.28-1.3-1.2a.75.75 0 0 0-1.02 1.1l1.85 1.72a.75.75 0 0 0 1.06-.04l3.55-3.84Z" clip-rule="evenodd"/></svg>
                                Tin tuyển dụng đã được kiểm duyệt
                            </div>
                        </div>
                    </div>

                    <section class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="sidebar-company-heading">
                        <div class="flex items-center gap-3">
                            <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-xl border border-slate-100 bg-slate-50">
                                @if ($company->logo)
                                    <img src="{{ $company->logo }}" alt="Logo {{ $company->name }}" class="size-full object-contain p-1">
                                @else
                                    <img src="{{ asset('images/default-company-logo.svg') }}" alt="Logo mặc định {{ $company->name }}" class="size-full object-contain p-1">
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wider text-sky-800">Nhà tuyển dụng</p>
                                <h2 id="sidebar-company-heading" class="mt-0.5 truncate font-bold text-slate-900">{{ $company->name }}</h2>
                            </div>
                        </div>
                        <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-sm">
                            @if ($company->address)
                                <div>
                                    <dt class="text-xs font-medium text-slate-400">Địa chỉ</dt>
                                    <dd class="mt-1 leading-5 text-slate-700">{{ $company->address }}</dd>
                                </div>
                            @endif
                            @if ($company->email)
                                <div>
                                    <dt class="text-xs font-medium text-slate-400">Email công ty</dt>
                                    <dd class="mt-1"><a href="mailto:{{ $company->email }}" class="break-all font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">{{ $company->email }}</a></dd>
                                </div>
                            @endif
                            @if (! $company->address && ! $company->email)
                                <dd class="text-sm text-slate-500">Thông tin liên hệ chưa được cập nhật.</dd>
                            @endif
                        </dl>
                    </section>
                </aside>
            </div>

            @if ($relatedJobs->isNotEmpty())
                <section class="mt-14" aria-labelledby="related-jobs-heading">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Có thể bạn quan tâm</p>
                            <h2 id="related-jobs-heading" class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Việc làm cùng ngành</h2>
                        </div>
                        <a href="{{ route('home', ['industry_id' => $job->industry_id]) }}#viec-lam" class="hidden text-sm font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950 sm:inline">Xem tất cả</a>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($relatedJobs as $relatedJob)
                            <a href="{{ route('jobs.show', $relatedJob->slug) }}" class="group flex min-h-52 flex-col rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:border-sky-300 hover:shadow-lg hover:shadow-sky-900/[0.06] sm:p-5">
                                <div class="flex items-start gap-3">
                                    <span class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-xl border border-slate-100 bg-slate-50">
                                        @if ($relatedJob->company->logo)
                                            <img src="{{ $relatedJob->company->logo }}" alt="Logo {{ $relatedJob->company->name }}" loading="lazy" class="size-full object-contain p-1.5">
                                        @else
                                            <img src="{{ asset('images/default-company-logo.svg') }}" alt="Logo mặc định {{ $relatedJob->company->name }}" loading="lazy" class="size-full object-contain p-1.5">
                                        @endif
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="line-clamp-2 block min-h-10 text-sm font-bold leading-5 text-slate-900 transition group-hover:text-sky-800">{{ $relatedJob->title }}</span>
                                        <span class="mt-1 block truncate text-xs font-medium text-slate-500">{{ $relatedJob->company->name }}</span>
                                    </span>
                                </div>
                                <span class="mt-5 inline-flex items-center gap-1.5 text-sm font-bold text-emerald-700">
                                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4"><rect x="2.25" y="4.25" width="15.5" height="11.5" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 8.1c.35-.4.9-.6 1.65-.6 1.05 0 1.75.48 1.75 1.2 0 1.8-3.5.85-3.5 2.75 0 .75.7 1.3 1.85 1.3.75 0 1.4-.22 1.8-.65M7.75 6.5v7m4.25-5.75h2.5M12 10h2.5m-2.5 2.25h2.5"/></svg>
                                    {{ $relatedJob->salaryShort() }}
                                </span>
                                <span class="mt-auto flex items-center justify-between gap-3 border-t border-slate-100 pt-4 text-xs text-slate-500">
                                    <span class="inline-flex min-w-0 items-center gap-1.5">
                                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-sky-700"><path fill-rule="evenodd" d="M10 1.75a6.25 6.25 0 0 0-6.25 6.25c0 4.35 5.47 9.56 5.7 9.78a.8.8 0 0 0 1.1 0c.23-.22 5.7-5.43 5.7-9.78A6.25 6.25 0 0 0 10 1.75Zm0 8.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                                        <span class="truncate">{{ $relatedJob->location ?: 'Linh hoạt địa điểm' }}</span>
                                    </span>
                                    <span aria-hidden="true" class="shrink-0 font-bold text-sky-700 transition group-hover:translate-x-0.5">→</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
