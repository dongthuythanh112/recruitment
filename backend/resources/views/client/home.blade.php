@extends('client.layouts.app')

@section('title', 'Bảng việc làm')
@section('description', 'Bảng việc làm cho người tìm việc và nơi cần người. Mọi tin đều được kiểm duyệt trước khi lên bảng.')

@section('content')
    @php
        // Màu tuyến cố định theo thứ tự, như sơ đồ tàu điện.
        $lineColors = ['#d63a1f', '#0b6e4f', '#1f5fbf', '#c77700', '#0e7c86', '#8a3b1e', '#3d4a5c', '#b5245f'];
        $mailto = 'mailto:'.config('app.contact_email').'?subject='.rawurlencode('Đăng tin tuyển dụng trên Ga Việc');
    @endphp

    <section class="mx-auto max-w-6xl px-4 pt-10 pb-8 lg:pt-14" data-enter>
        <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr] lg:items-end">
            <h1 class="font-signage text-5xl leading-[1.08] font-extrabold tracking-wide text-balance uppercase sm:text-6xl lg:text-7xl">
                Một tấm bảng cho <span class="text-flap">người tìm việc</span> và <span class="text-flap">nơi cần người</span>
            </h1>
            <div>
                <p class="text-paper/85">
                    Đang mở {{ number_format($openJobsCount, 0, ',', '.') }} vị trí từ {{ number_format($hiringCompaniesCount, 0, ',', '.') }} công ty.
                    Tin nào cũng được kiểm duyệt trước khi lên bảng, hết hạn thì tự xuống.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="#viec-lam" class="btn-flap">Xem bảng việc làm</a>
                    <a href="#nha-tuyen-dung" class="btn-outline">Đăng tin tuyển dụng</a>
                </div>
            </div>
        </div>
    </section>

    @if ($candidate)
        <section class="border-y border-sky-100 bg-sky-50/80">
            <div class="mx-auto max-w-6xl px-4 py-10 sm:py-12">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Gợi ý dành cho bạn</p>
                        <h2 class="mt-2 font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">
                            {{ $recommendedJobs->isNotEmpty() ? 'Việc làm phù hợp' : 'Việc làm nổi bật' }}
                        </h2>
                        <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
                            {{ $recommendedJobs->isNotEmpty() ? 'Sắp xếp theo mức độ phù hợp với vị trí mong muốn, địa điểm, lương, kinh nghiệm và kỹ năng trong hồ sơ.' : 'Các vị trí đang tuyển được ứng viên quan tâm nhiều trên hệ thống.' }}
                        </p>
                    </div>
                    <a href="{{ route('candidate.profile.edit') }}" class="text-sm font-semibold text-sky-800 hover:text-sky-950">{{ $recommendedJobs->isNotEmpty() ? 'Cập nhật hồ sơ để nhận gợi ý sát hơn' : 'Thêm tiêu chí để nhận gợi ý phù hợp' }} <span aria-hidden="true">→</span></a>
                </div>

                @if ($recommendedJobs->isNotEmpty())
                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($recommendedJobs as $suggestion)
                            @php($job = $suggestion['job'])
                            <a href="{{ route('jobs.show', $job->slug) }}" class="group flex min-h-48 flex-col rounded-2xl border border-sky-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="line-clamp-2 font-bold leading-6 text-slate-900 transition group-hover:text-sky-800">{{ $job->title }}</h3>
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
                                <div class="mt-auto flex flex-wrap gap-1.5 pt-4">
                                    @foreach ($suggestion['reasons'] as $reason)
                                        <span class="rounded-full border border-sky-100 bg-sky-50 px-2 py-1 text-[10px] font-medium text-sky-800">{{ $reason }}</span>
                                    @endforeach
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($featuredJobs->isNotEmpty())
                    <div class="{{ $recommendedJobs->isNotEmpty() ? 'mt-8 border-t border-sky-200 pt-7' : 'mt-5' }}">
                        @if ($recommendedJobs->isNotEmpty())
                            <h3 class="font-signage text-xl font-bold tracking-wide text-slate-800 uppercase">Việc đang tuyển nổi bật</h3>
                        @endif
                        <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($featuredJobs as $job)
                                <a href="{{ route('jobs.show', $job->slug) }}" class="group flex items-center justify-between gap-4 rounded-xl border border-sky-100 bg-white px-4 py-3 shadow-sm transition hover:border-sky-300 hover:shadow-md">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-bold text-slate-900 group-hover:text-sky-800">{{ $job->title }}</span>
                                        <span class="mt-1 block truncate text-xs text-slate-500">{{ $job->company->name }} · {{ $job->location ?: 'Linh hoạt' }}</span>
                                    </span>
                                    <span class="shrink-0 text-xs font-semibold text-sky-800">{{ $job->salaryShort() }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($recommendedJobs->isEmpty() && $featuredJobs->isEmpty())
                    <div class="mt-5 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-8 text-center">
                        <p class="font-semibold text-slate-800">Chưa có vị trí phù hợp để gợi ý</p>
                        <p class="mt-1 text-sm text-slate-500">Tin tuyển dụng mới sẽ xuất hiện tại đây sau khi được duyệt.</p>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section id="viec-lam" class="mx-auto max-w-6xl scroll-mt-14 px-4 pb-14">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <h2 class="font-signage text-3xl font-bold tracking-wide uppercase">
                    {{ $isFiltered ? 'Kết quả tìm kiếm ('.$jobs->total().')' : 'Việc đang tuyển' }}
                </h2>
                <div class="flex items-center gap-4">
                    @if ($isFiltered)
                        <a href="{{ route('home') }}#viec-lam" class="board-link text-flap">Xóa bộ lọc</a>
                    @endif
                    <time class="flap" data-clock>{{ now()->format('H:i') }}</time>
                </div>
            </div>

            <form method="GET" action="{{ route('home') }}#viec-lam" role="search" class="grid gap-3 border-b border-slate-200 bg-sky-50/70 px-4 py-4 md:grid-cols-[1.4fr_1fr_1fr_auto] md:items-end">
                <div>
                    <label for="q" class="mb-1 block text-xs text-paper/70">Vị trí hoặc công ty</label>
                    <input id="q" name="q" type="search" maxlength="100" value="{{ request()->query('q') }}" class="board-field">
                </div>
                <div>
                    <label for="location" class="mb-1 block text-xs text-paper/70">Địa điểm</label>
                    <input id="location" name="location" type="text" maxlength="100" value="{{ request()->query('location') }}" class="board-field">
                </div>
                <div>
                    <label for="industry_id" class="mb-1 block text-xs text-paper/70">Ngành nghề</label>
                    <select id="industry_id" name="industry_id" class="board-field">
                        <option value="">Tất cả ngành nghề</option>
                        @foreach ($industryOptions as $id => $name)
                            <option value="{{ $id }}" @selected((string) request()->query('industry_id') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-flap cursor-pointer">Tìm việc</button>
            </form>

            @if ($jobs->isEmpty())
                <p class="px-4 py-14 text-center text-paper/70">
                    {{ $isFiltered ? 'Không có việc làm nào khớp với tìm kiếm của bạn. Hãy thử từ khóa khác.' : 'Hiện chưa có việc làm nào đang tuyển.' }}
                </p>
            @else
                <div class="hidden grid-cols-[minmax(0,1fr)_11rem_12rem_11.5rem_1.25rem] gap-x-6 border-b border-slate-200 bg-slate-50 px-4 py-2 text-xs tracking-wider text-paper/60 uppercase lg:grid" aria-hidden="true">
                    <span>Vị trí</span><span>Nơi làm</span><span>Lương</span><span>Trạng thái</span>
                </div>
                @foreach ($jobs as $job)
                    @include('client.partials.job-row')
                @endforeach
                {{ $jobs->links('client.partials.pagination') }}
            @endif
        </div>
    </section>

    @if ($industries->isNotEmpty() || $companies->isNotEmpty())
        <section id="tuyen" class="scroll-mt-14 bg-paper text-ink">
            <div class="mx-auto grid max-w-6xl gap-12 px-4 py-14 lg:grid-cols-[1.4fr_1fr]">
                <div>
                    <h2 class="font-signage text-4xl font-extrabold tracking-wide uppercase">Sơ đồ tuyến ngành nghề</h2>
                    <p class="mt-2 text-ink/75">Mỗi ga trên tuyến là một vị trí đang mở. Chọn một tuyến để lọc bảng.</p>
                    <ul class="mt-6 space-y-2">
                        @foreach ($industries as $industry)
                            <li>
                                <a href="{{ route('home', ['industry_id' => $industry->id]) }}#viec-lam" class="transit-line" style="--line: {{ $lineColors[$loop->index % count($lineColors)] }}">
                                    <span class="flex items-baseline justify-between gap-3">
                                        <span class="font-semibold">{{ $industry->name }}</span>
                                        <span class="text-sm whitespace-nowrap text-ink/75">{{ $industry->open_jobs_count }} ga</span>
                                    </span>
                                    <span aria-hidden="true" class="transit-track">
                                        @foreach (range(1, min($industry->open_jobs_count, 16)) as $stop)
                                            <span class="transit-stop"></span>
                                        @endforeach
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h2 class="font-signage text-4xl font-extrabold tracking-wide uppercase">Công ty đang tuyển</h2>
                    <ul class="mt-6 divide-y divide-ink/15 border-y border-ink/15">
                        @foreach ($companies as $company)
                            <li>
                                <a href="{{ route('home', ['q' => $company->name]) }}#viec-lam" class="flex min-h-14 items-center justify-between gap-4 py-2 hover:text-signal">
                                    <span class="font-medium">{{ $company->name }}</span>
                                    <span class="text-sm whitespace-nowrap text-ink/75">{{ $company->open_jobs_count }} vị trí</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    <section id="nha-tuyen-dung" class="scroll-mt-14 bg-flap text-ink">
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
