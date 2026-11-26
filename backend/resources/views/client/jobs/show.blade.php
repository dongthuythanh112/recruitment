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
            ['Nơi làm việc', $job->location, '⌖'],
            ['Ngành nghề', $job->industry->name, '▤'],
            ['Hình thức làm việc', \App\Models\Job::WORKPLACE_TYPE_LABELS[$job->workplace_type] ?? null, '◷'],
            ['Loại hình công việc', \App\Models\Job::EMPLOYMENT_TYPE_LABELS[$job->employment_type] ?? null, '▣'],
            ['Kinh nghiệm', $job->experience_years !== null ? 'Từ '.(float) $job->experience_years.' năm' : null, '✦'],
            ['Số lượng tuyển', $job->quantity ? $job->quantity.' người' : null, '＋'],
            ['Hạn nộp hồ sơ', $job->deadline?->format('d/m/Y'), '◷'],
        ], fn ($fact) => filled($fact[1]));
        $websiteUrl = $company->website && preg_match('#^https?://#i', $company->website) ? $company->website : null;
    @endphp

    <div class="mx-auto max-w-6xl px-4 pb-16 pt-6 sm:pt-8">
        <a href="{{ route('home') }}#viec-lam" class="inline-flex items-center gap-2 text-sm text-paper/65 transition hover:text-flap">
            <span aria-hidden="true">←</span> Tất cả việc làm
        </a>

        <header class="relative mt-5 overflow-hidden rounded-2xl bg-paper text-ink shadow-2xl shadow-black/20" data-enter>
            <div aria-hidden="true" class="absolute -right-10 -top-20 size-72 rounded-full border-[32px] border-ink/[0.035] sm:size-96"></div>
            <div class="relative p-5 sm:p-8 lg:p-10">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wider">
                    <span class="rounded-full bg-ink px-3 py-1.5 font-board text-[10px] text-flap">Mã tin {{ str_pad((string) $job->id, 6, '0', STR_PAD_LEFT) }}</span>
                    @include('client.partials.job-status')
                </div>

                <div class="mt-6 flex flex-col gap-6 sm:flex-row sm:items-center">
                    <div class="grid size-20 shrink-0 place-items-center overflow-hidden rounded-2xl border border-ink/10 bg-white shadow-sm sm:size-24">
                        @if ($company->logo)
                            <img src="{{ $company->logo }}" alt="Logo {{ $company->name }}" class="size-full object-contain p-2">
                        @else
                            <span aria-hidden="true" class="font-signage text-5xl font-extrabold text-ink/65">{{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-ink/60">{{ $company->name }}</p>
                        <h1 class="mt-1 font-signage text-4xl leading-[1.02] font-extrabold tracking-wide text-balance uppercase sm:text-5xl lg:text-6xl">{{ $job->title }}</h1>
                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-ink/65">
                            @if ($job->location)<span class="inline-flex items-center gap-1.5"><span aria-hidden="true">⌖</span>{{ $job->location }}</span>@endif
                            @if ($job->industry)<span>{{ $job->industry->name }}</span>@endif
                            @if ($job->published_at)<span>Đăng {{ $job->published_at->diffForHumans() }}</span>@endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex flex-wrap items-center justify-between gap-3 border-t border-ink/10 bg-white/55 px-5 py-4 sm:px-8 lg:px-10">
                <p class="text-xs text-ink/55">Cơ hội nghề nghiệp tại <span class="font-semibold text-ink">{{ $company->name }}</span></p>
                @if ($websiteUrl)
                    <a href="{{ $websiteUrl }}" target="_blank" rel="noopener nofollow noreferrer" class="text-xs font-semibold text-teal-800 underline underline-offset-4 hover:text-signal">Tìm hiểu công ty <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </header>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_21rem] lg:gap-8">
            <main class="min-w-0 space-y-6">
                <section class="rounded-2xl border border-white/10 bg-white/[0.045] p-5 sm:p-7" aria-labelledby="job-overview">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-flap">Thông tin nhanh</p>
                            <h2 id="job-overview" class="mt-1 font-signage text-2xl font-bold tracking-wide uppercase">Tổng quan công việc</h2>
                        </div>
                        <span aria-hidden="true" class="hidden text-3xl text-flap/80 sm:block">✳</span>
                    </div>
                    <dl class="mt-5 grid gap-x-6 gap-y-5 sm:grid-cols-2">
                        <div class="rounded-xl bg-black/20 p-4 sm:col-span-2">
                            <dt class="text-xs text-paper/55">Mức lương</dt>
                            <dd class="mt-1 font-signage text-3xl font-extrabold tracking-wide text-flap sm:text-4xl">{{ $job->salaryShort() }}</dd>
                        </div>
                        @foreach ($facts as [$label, $value, $icon])
                            <div class="flex min-w-0 gap-3">
                                <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-lg bg-flap/10 text-lg text-flap">{{ $icon }}</span>
                                <div class="min-w-0">
                                    <dt class="text-xs text-paper/50">{{ $label }}</dt>
                                    <dd class="mt-0.5 break-words text-sm font-medium text-paper">{{ $value }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </section>

                @foreach ($sections as $heading => $body)
                    <section class="rounded-2xl bg-paper p-5 text-ink sm:p-7" aria-label="{{ $heading }}">
                        <div class="flex items-start gap-3">
                            <span aria-hidden="true" class="mt-0.5 grid size-9 shrink-0 place-items-center rounded-lg bg-flap text-lg font-bold">✳</span>
                            <div class="min-w-0">
                                <h2 class="font-signage text-2xl font-extrabold tracking-wide uppercase sm:text-3xl">{{ $heading }}</h2>
                                <p class="mt-4 max-w-prose leading-7 whitespace-pre-line text-ink/80">{{ $body }}</p>
                            </div>
                        </div>
                    </section>
                @endforeach

                @if ($job->skills->isNotEmpty())
                    <section class="rounded-2xl bg-paper p-5 text-ink sm:p-7" aria-labelledby="job-skills">
                        <div class="flex items-center gap-3">
                            <span aria-hidden="true" class="grid size-9 place-items-center rounded-lg bg-flap text-lg">✦</span>
                            <h2 id="job-skills" class="font-signage text-2xl font-extrabold tracking-wide uppercase sm:text-3xl">Kỹ năng cần có</h2>
                        </div>
                        <ul class="mt-5 flex flex-wrap gap-2">
                            @foreach ($job->skills as $skill)
                                <li class="rounded-full border border-teal-900/15 bg-teal-900/[0.06] px-3.5 py-2 text-sm font-medium text-teal-950">{{ $skill->name }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="rounded-2xl border border-white/10 bg-white/[0.045] p-5 sm:p-7" aria-labelledby="about-company">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-flap">Nhà tuyển dụng</p>
                    <h2 id="about-company" class="mt-1 font-signage text-2xl font-bold tracking-wide uppercase sm:text-3xl">Về {{ $company->name }}</h2>
                    @if ($company->description)
                        <p class="mt-4 max-w-prose leading-7 whitespace-pre-line text-paper/75">{{ $company->description }}</p>
                    @endif
                    <ul class="mt-5 grid gap-3 border-t border-white/10 pt-5 text-sm text-paper/75 sm:grid-cols-2">
                        @if ($company->address)<li><span class="text-paper/45">Địa chỉ</span><br>{{ $company->address }}</li>@endif
                        @if ($company->company_size)<li><span class="text-paper/45">Quy mô</span><br>{{ $company->company_size }} nhân sự</li>@endif
                        @if ($company->email)<li><span class="text-paper/45">Email tuyển dụng</span><br><a href="mailto:{{ $company->email }}" class="break-all text-flap underline underline-offset-4">{{ $company->email }}</a></li>@endif
                        @if ($websiteUrl)<li><span class="text-paper/45">Website</span><br><a href="{{ $websiteUrl }}" target="_blank" rel="noopener nofollow noreferrer" class="break-all text-flap underline underline-offset-4">{{ $websiteUrl }}</a></li>@endif
                    </ul>
                    <a href="{{ route('home', ['q' => $company->name]) }}#viec-lam" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-flap underline underline-offset-4 hover:text-white">Xem các vị trí khác <span aria-hidden="true">→</span></a>
                </section>
            </main>

            <aside class="lg:sticky lg:top-20" aria-label="Ứng tuyển công việc">
                <div class="overflow-hidden rounded-2xl bg-flap text-ink shadow-xl shadow-black/20">
                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink/65">Sẵn sàng ứng tuyển?</p>
                        <p class="mt-1 font-signage text-3xl font-extrabold tracking-wide uppercase">Bắt đầu ngay</p>
                        <p class="mt-2 text-sm leading-6 text-ink/70">Gửi hồ sơ cho nhà tuyển dụng hoặc lưu tin để xem lại sau.</p>
                        @auth
                            @if (auth()->user()->role === \App\Models\User::ROLE_CANDIDATE)
                                @if ($application && $application->status !== 'withdrawn')
                                    <div class="mt-5 rounded-xl border border-ink/15 bg-white/40 p-4">
                                        <p class="text-sm font-bold">Bạn đã ứng tuyển vị trí này</p>
                                        <p class="mt-1 text-xs text-ink/65">Trạng thái: {{ \App\Models\Application::STATUS_LABELS[$application->status] ?? $application->status }}</p>
                                        <a href="{{ route('candidate.applications.index') }}" class="mt-2 inline-flex text-xs font-semibold underline underline-offset-4">Theo dõi hồ sơ</a>
                                    </div>
                                @else
                                    <a href="{{ route('candidate.applications.create', $job) }}" class="mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-sky-800 px-4 text-sm font-bold text-white transition hover:bg-sky-900">
                                        Ứng tuyển ngay <span aria-hidden="true">→</span>
                                    </a>
                                @endif
                            @else
                                <p class="mt-5 rounded-xl bg-white/40 p-4 text-sm">Đăng nhập bằng tài khoản ứng viên để ứng tuyển vị trí này.</p>
                            @endif
                        @else
                            <a href="{{ route('login', ['role' => 'candidate']) }}" class="mt-5 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-lg bg-sky-800 px-4 text-sm font-bold text-white transition hover:bg-sky-900">Đăng nhập để ứng tuyển</a>
                        @endauth
                        @if (auth()->check() && auth()->user()->role === \App\Models\User::ROLE_CANDIDATE)
                            @if ($isSaved)
                                <form method="POST" action="{{ route('candidate.saved-jobs.destroy', $job) }}" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-ink/25 px-4 text-sm font-semibold transition hover:bg-ink/5"><span aria-hidden="true">♥</span> Đã lưu · Bỏ lưu</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('candidate.saved-jobs.store', $job) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-ink/25 px-4 text-sm font-semibold transition hover:bg-ink/5"><span aria-hidden="true">♡</span> Lưu việc làm</button>
                                </form>
                            @endif
                        @elseif (!auth()->check())
                            <a href="{{ route('login') }}" class="mt-3 inline-flex min-h-11 w-full items-center justify-center rounded-lg border border-ink/25 px-4 text-sm font-semibold transition hover:bg-ink/5">Đăng nhập để lưu tin</a>
                        @endif
                    </div>
                    <div class="border-t border-ink/10 bg-white/25 px-5 py-3 font-board text-[10px] tracking-[0.18em] text-ink/55 sm:px-6">
                        VIỆC NGON <span class="float-right">#{{ str_pad((string) $job->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
            </aside>
        </div>

        @if ($relatedJobs->isNotEmpty())
            <section class="mt-14">
                <div class="mb-4 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-flap">Có thể bạn quan tâm</p>
                        <h2 class="mt-1 font-signage text-3xl font-extrabold tracking-wide uppercase">Việc làm cùng ngành</h2>
                    </div>
                    <a href="{{ route('home', ['industry_id' => $job->industry_id]) }}#viec-lam" class="hidden text-sm text-paper/60 underline underline-offset-4 hover:text-flap sm:inline">Xem tất cả</a>
                </div>
                <div class="overflow-hidden rounded-xl border border-white/15 bg-black/30">
                    @foreach ($relatedJobs as $job)
                        @include('client.partials.job-row')
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
