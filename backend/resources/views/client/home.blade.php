@extends('client.layouts.app')

@section('title', 'Bảng việc làm')
@section('description', 'Bảng việc làm cho người tìm việc và nơi cần người. Mọi tin đều được kiểm duyệt trước khi lên bảng.')

@section('content')
    @php
        $mailto = 'mailto:'.config('app.contact_email').'?subject='.rawurlencode('Đăng tin tuyển dụng trên Ga Việc');
    @endphp

    <section class="relative z-20 isolate w-full overflow-x-clip bg-gradient-to-br from-sky-950 via-sky-800 to-cyan-700 py-11 text-white sm:py-14 lg:py-16" data-enter>
        <div aria-hidden="true" class="absolute -left-24 -top-28 size-80 rounded-full bg-cyan-300/15 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -bottom-40 right-[-4rem] size-[28rem] rounded-full border-[58px] border-white/5"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-5xl text-center">
                <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight text-balance text-white sm:text-5xl lg:text-6xl">
                    Một tấm bảng cho <span class="text-cyan-200">người tìm việc</span><br class="hidden sm:block"> và <span class="text-cyan-200">nơi cần người</span>
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-sky-100 sm:text-base">
                    Tìm kiếm cơ hội phù hợp với bạn. Mọi vị trí đều được kiểm duyệt trước khi hiển thị.
                </p>
            </div>

            <form method="GET" action="{{ route('home') }}#ket-qua-tim-kiem" role="search" class="mx-auto mt-8 grid max-w-6xl grid-cols-1 gap-3 rounded-2xl border border-white/70 bg-white p-4 text-left shadow-2xl shadow-sky-950/25 sm:grid-cols-3 sm:p-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)_minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <label for="q" class="mb-1.5 block text-xs font-semibold text-slate-700">Vị trí hoặc công ty</label>
                    <input id="q" name="q" type="search" maxlength="100" value="{{ request()->query('q') }}" placeholder="Ví dụ: kế toán, marketing..." class="board-field">
                </div>
                <div>
                    <label for="location" class="mb-1.5 block text-xs font-semibold text-slate-700">Địa điểm</label>
                    <div class="relative">
                        <select id="location" name="location" class="min-h-11 w-full appearance-none rounded-lg border border-slate-300 bg-white px-3.5 pr-10 text-sm text-slate-800 shadow-sm transition hover:border-sky-400 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-100">
                            <option value="" @selected(!request()->query('location'))>Tất cả tỉnh/thành</option>
                            @foreach ($provinceOptions as $province)
                                <option value="{{ $province }}" @selected(request()->query('location') === $province)>{{ $province }}</option>
                            @endforeach
                        </select>
                        <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-sky-700"><path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
                <div>
                    <label for="industry_id" class="mb-1.5 block text-xs font-semibold text-slate-700">Ngành nghề</label>
                    <select id="industry_id" name="industry_id" class="board-field">
                        <option value="">Tất cả ngành nghề</option>
                        @foreach ($industryOptions as $id => $name)
                            <option value="{{ $id }}" @selected((string) request()->query('industry_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-sky-800 px-6 text-sm font-bold text-white transition hover:bg-sky-950 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:col-span-3 lg:col-span-1">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M8.5 3a5.5 5.5 0 1 0 3.42 9.81l3.63 3.64a.75.75 0 1 0 1.06-1.06l-3.64-3.63A5.5 5.5 0 0 0 8.5 3ZM4.5 8.5a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" /></svg>
                    Tìm việc
                </button>
            </form>
            <div class="mt-4 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-medium text-sky-100/85">
                <span class="inline-flex items-center gap-2"><span class="grid size-5 place-items-center rounded-full bg-white/15 text-white">✓</span> Tin được kiểm duyệt</span>
                <a href="#nha-tuyen-dung" class="underline decoration-white/40 underline-offset-4 transition hover:text-white">Bạn đang tuyển dụng? Đăng tin tuyển dụng</a>
            </div>
        </div>
    </section>
    @if ($isFiltered)
        <section id="ket-qua-tim-kiem" class="scroll-mt-14 border-y border-sky-100 bg-sky-50/80">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Kết quả theo bộ lọc</p>
                        <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">Kết quả tìm kiếm</h2>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Tối đa 8 việc làm phù hợp với tiêu chí bạn đã chọn.</p>
                    </div>
                    <a href="{{ route('home') }}#ket-qua-tim-kiem" class="inline-flex min-h-10 items-center rounded-lg border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-800 shadow-sm transition hover:border-sky-400 hover:bg-sky-50">Xóa bộ lọc</a>
                </div>
                @if ($searchJobs->isEmpty())
                    <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-12 text-center">
                        <p class="font-semibold text-slate-800">Không tìm thấy việc làm phù hợp.</p>
                        <p class="mt-1 text-sm text-slate-500">Thử thay đổi từ khóa, địa điểm hoặc ngành nghề.</p>
                    </div>
                @else
                    <p class="mt-4 text-sm font-medium text-slate-600">{{ $searchJobs->count() }} việc làm phù hợp</p>
                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($searchJobs as $job)
                            @include('client.partials.home-job-card', ['job' => $job])
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
    @if ($homeSuggestions->isNotEmpty())
        <section id="goi-y-viec-lam" class="scroll-mt-14 border-y border-sky-100 bg-sky-50/80">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Gợi ý dành cho bạn</p>
                        <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">Gợi ý việc làm</h2>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">{{ $candidate ? 'Các cơ hội việc làm có thể phù hợp với bạn.' : 'Những cơ hội mới đăng gần đây trên hệ thống.' }}</p>
                    </div>
                    @if ($candidate || auth()->guest())
                        <a href="{{ route('candidate.recommendations.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-sky-200 bg-white px-4 text-sm font-semibold text-sky-800 shadow-sm transition hover:border-sky-400 hover:bg-sky-50">
                            Xem thêm <span aria-hidden="true">→</span>
                        </a>
                    @endif
                </div>
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($homeSuggestions as $suggestion)
                        @php($job = $suggestion['job'])
                        @include('client.partials.home-job-card', ['job' => $job])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <section id="viec-lam" class="scroll-mt-14 border-y border-sky-100 bg-sky-50/80">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Khám phá cơ hội</p>
                    <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">Việc làm nổi bật</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Những vị trí đang tuyển được cập nhật mới nhất.</p>
                </div>
            </div>

            @if ($featuredJobs->isEmpty())
                <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-12 text-center">
                    <p class="font-semibold text-slate-800">Hiện chưa có việc làm nào đang tuyển.</p>
                </div>
            @else
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featuredJobs as $job)
                        @include('client.partials.home-job-card', ['job' => $job])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($industries->isNotEmpty())
        @php($industryPages = $industries->chunk(8))
        <section id="top-nganh-nghe" class="bg-white">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Khám phá theo lĩnh vực</p>
                        <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">Top ngành nghề nổi bật</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Các ngành có nhiều tin tuyển dụng nhất hiện nay.</p>
                    </div>
                    @if ($industryPages->count() > 1)
                        <div class="flex items-center gap-2">
                            <button type="button" aria-label="Trang ngành nghề trước" data-industry-prev class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 0 1 0 1.06L8.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </button>
                            <span class="min-w-12 text-center text-xs font-semibold tabular-nums text-slate-500"><span data-industry-current>1</span> / {{ $industryPages->count() }}</span>
                            <button type="button" aria-label="Trang ngành nghề tiếp" data-industry-next class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mt-5" data-industry-carousel data-interval="5500">
                    @foreach ($industryPages as $page)
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4 {{ $loop->first ? '' : 'hidden' }}" data-industry-page aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                            @foreach ($page as $industry)
                                <a href="{{ route('home', ['industry_id' => $industry->id]) }}#viec-lam" class="group flex min-h-40 flex-col items-center rounded-2xl border border-sky-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md sm:min-h-44 sm:p-5">
                                    <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-sky-50 to-cyan-100 text-sky-800 transition group-hover:from-sky-100 group-hover:to-cyan-200 sm:size-14">
                                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-6 sm:size-7">
                                            @switch($industry->home_icon)
                                                @case('technology') <rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4M7 8h4m-4 4h8"/> @break
                                                @case('finance') <rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18m-13 4h3m-3 3h6M7 3h10"/> @break
                                                @case('marketing') <path d="M4 13V9a2 2 0 0 1 2-2h3l9-4v16l-9-4H6a2 2 0 0 1-2-2Zm5 2 2 6h4l-3-7m9-5 2-2m-2 10 2 2"/> @break
                                                @case('design') <path d="M12 3a9 9 0 1 0 0 18h1.2a2 2 0 0 0 1.4-3.4 1.5 1.5 0 0 1 1.1-2.6H18a3 3 0 0 0 3-3c0-5-4-9-9-9Z"/><path d="M7.5 10h.01M11 7h.01M15.5 9h.01"/> @break
                                                @case('health') <path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11Z"/><path d="M12 8v6m-3-3h6"/> @break
                                                @case('education') <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H12v17H5.5A2.5 2.5 0 0 0 3 22V5.5Zm18 0A2.5 2.5 0 0 0 18.5 3H12v17h6.5A2.5 2.5 0 0 1 21 22V5.5Z"/> @break
                                                @case('logistics') <path d="M3 6h11v11H3zM14 10h4l3 4v3h-7z"/><circle cx="7.5" cy="18" r="2"/><circle cx="18" cy="18" r="2"/> @break
                                                @case('hospitality') <path d="M4 3v7m4-7v7M4 7h4m-2 3v11m10-18v18m0-18c-3 2-3 6 0 8"/> @break
                                                @case('people') <circle cx="9" cy="8" r="3"/><path d="M3 20v-1a6 6 0 0 1 12 0v1m3-15a3 3 0 0 1 0 6m3 9v-1a6 6 0 0 0-4-5.65"/> @break
                                                @case('legal') <path d="M12 3v18m-7-15h14M5 6 2 13h6L5 6Zm14 0-3 7h6l-3-7ZM7 21h10"/> @break
                                                @case('building') <path d="M4 21V5a2 2 0 0 1 2-2h8v18M14 9h4a2 2 0 0 1 2 2v10M2 21h20M8 7h2m-2 4h2m-2 4h2m8-2h.01m-0.01 4h.01"/> @break
                                                @case('engineering') <path d="M14.7 6.3a5 5 0 0 0-6.4 6.4L3 18l3 3 5.3-5.3a5 5 0 0 0 6.4-6.4L15 12l-3-3 2.7-2.7Z"/> @break
                                                @case('sales') <path d="M4 19V5m0 14h17M7 15l4-4 3 2 6-7m-4 0h4v4"/> @break
                                                @case('science') <path d="M9 3h6m-5 0v6l-5.5 9A2 2 0 0 0 6.2 21h11.6a2 2 0 0 0 1.7-3L14 9V3M7 16h10"/> @break
                                                @default <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m-13 5h18m-11 0v2h4v-2"/>
                                            @endswitch
                                        </svg>
                                    </span>
                                    <span class="mt-3 line-clamp-2 min-h-10 text-sm font-bold leading-5 text-slate-800 transition group-hover:text-sky-800">{{ $industry->name }}</span>
                                    <span class="mt-auto pt-2 text-xs font-medium text-slate-500">{{ number_format($industry->open_jobs_count) }} tin tuyển dụng</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                @if ($industryPages->count() > 1)
                    <div class="mt-5 flex justify-center gap-2" aria-label="Chọn trang ngành nghề">
                        @foreach ($industryPages as $page)
                            <button type="button" aria-label="Trang ngành nghề {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" data-industry-dot="{{ $loop->index }}" class="h-2.5 rounded-full transition-all {{ $loop->first ? 'w-7 bg-sky-700' : 'w-2.5 bg-sky-200 hover:bg-sky-400' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
    @if ($companies->isNotEmpty())
        @php($companyPages = $companies->chunk(5))
        <section id="tuyen" class="scroll-mt-14 border-y border-sky-100 bg-sky-50/80">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Cơ hội từ các doanh nghiệp hàng đầu</p>
                        <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">Nhà tuyển dụng nổi bật</h2>
                    </div>
                    @if ($companyPages->count() > 1)
                        <div class="flex items-center gap-2">
                            <button type="button" aria-label="Nhà tuyển dụng trước" data-company-prev class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 0 1 0 1.06L8.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                            </button>
                            <span class="min-w-12 text-center text-xs font-semibold tabular-nums text-slate-500"><span data-company-current>1</span> / {{ $companyPages->count() }}</span>
                            <button type="button" aria-label="Nhà tuyển dụng tiếp" data-company-next class="grid size-9 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <div class="mt-5" data-company-carousel data-interval="5500">
                    @foreach ($companyPages as $page)
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 sm:gap-4 {{ $loop->first ? '' : 'hidden' }}" data-company-page aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                            @foreach ($page as $company)
                                <a href="{{ route('home', ['q' => $company->name]) }}#viec-lam" aria-label="Xem việc làm tại {{ $company->name }}" title="{{ $company->name }}" class="group flex min-h-32 flex-col items-center justify-center gap-3 rounded-2xl border border-sky-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md sm:min-h-36 sm:p-6">
                                    <img src="{{ $company->logo ?: asset('images/default-company-logo.svg') }}" alt="Logo {{ $company->name }}" loading="lazy" class="h-14 w-full object-contain transition duration-300 group-hover:scale-105 sm:h-16">
                                    <span class="line-clamp-2 text-sm font-semibold leading-5 text-slate-800 transition group-hover:text-sky-800">{{ $company->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                @if ($companyPages->count() > 1)
                    <div class="mt-5 flex justify-center gap-2" aria-label="Chọn trang nhà tuyển dụng">
                        @foreach ($companyPages as $page)
                            <button type="button" aria-label="Trang nhà tuyển dụng {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" data-company-dot="{{ $loop->index }}" class="h-2.5 rounded-full transition-all {{ $loop->first ? 'w-7 bg-sky-700' : 'w-2.5 bg-sky-200 hover:bg-sky-400' }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif    <section id="nha-tuyen-dung" class="scroll-mt-14 bg-flap text-ink">
        <div class="mx-auto max-w-6xl px-4 py-14">
            <h2 class="max-w-3xl font-signage text-4xl leading-[1.1] font-extrabold tracking-wide uppercase sm:text-5xl">Đưa vị trí của bạn lên bảng qua ba ga</h2>
            <ol class="route mt-8 grid gap-8 md:grid-cols-3">
                <li class="route-stop">
                    <h3 class="font-semibold">Gửi thông tin vị trí</h3>
                    <p class="mt-1 text-sm">Mô tả công việc, mức lương, địa điểm và hạn nộp. Tài khoản nhà tuyển dụng do quản trị viên cấp.</p>
                </li>
                <li class="route-stop">
                    <h3 class="font-semibold">Quản trị viên duyệt tin</h3>
                    <p class="mt-1 text-sm">Tin chưa đạt được trả lại kèm lý do để bạn sửa.</p>
                </li>
                <li class="route-stop">
                    <h3 class="font-semibold">Tin lên bảng</h3>
                    <p class="mt-1 text-sm">{{ number_format($candidatesCount, 0, ',', '.') }} ứng viên đang có hồ sơ trên hệ thống. Tin ở trên bảng đến hết hạn nộp.</p>
                </li>
            </ol>
            <a href="{{ $mailto }}" class="btn-ink mt-8">Gửi yêu cầu đăng tin</a>
        </div>
    </section>
@endsection
