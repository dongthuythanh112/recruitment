@extends('client.layouts.app')

@section('title', $job->title)
@section('description', \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $job->description), 155))

@section('content')
    @php
        $company = $job->company;
        $sections = array_filter(['Mô tả công việc' => $job->description, 'Yêu cầu' => $job->requirements, 'Quyền lợi' => $job->benefits]);
        $ticket = array_filter([
            'Nơi làm' => $job->location,
            'Ngành nghề' => $job->industry->name,
            'Hình thức' => \App\Models\Job::WORKPLACE_TYPE_LABELS[$job->workplace_type] ?? null,
            'Loại hình' => \App\Models\Job::EMPLOYMENT_TYPE_LABELS[$job->employment_type] ?? null,
            'Kinh nghiệm' => $job->experience_years !== null ? 'Từ '.(float) $job->experience_years.' năm' : null,
            'Số lượng' => $job->quantity.' người',
            'Hạn nộp' => $job->deadline?->format('d/m/Y'),
        ]);
        $applyMail = $company->email
            ? 'mailto:'.$company->email.'?subject='.rawurlencode('Ứng tuyển: '.$job->title)
            : null;
        // Website do người dùng nhập: chỉ tạo link với http(s) để tránh javascript:.
        $websiteUrl = $company->website && preg_match('#^https?://#i', $company->website) ? $company->website : null;
    @endphp

    <div class="mx-auto max-w-6xl px-4 pt-6 pb-14">
        <a href="{{ route('home') }}#viec-lam" class="board-link -ml-1.5 text-sm text-paper/80">← Về bảng việc làm</a>

        <div class="mt-4 grid items-start gap-x-10 gap-y-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <header class="min-w-0" data-enter>
                <h1 class="font-signage text-5xl leading-[1.08] font-extrabold tracking-wide text-balance uppercase sm:text-6xl">{{ $job->title }}</h1>
                <p class="mt-3 text-lg text-paper/85">{{ $company->name }}</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    @include('client.partials.job-status')
                </div>
            </header>

            <aside class="ticket self-start lg:sticky lg:top-20 lg:col-start-2 lg:row-span-2 lg:row-start-1" aria-label="Thông tin tuyển dụng">
                <div class="p-5">
                    <p class="text-xs text-ink/70">Mức lương</p>
                    <p class="mt-1 font-signage text-4xl leading-tight font-extrabold tracking-wide uppercase">{{ $job->salaryShort() }}</p>
                    <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        @foreach ($ticket as $label => $value)
                            <div>
                                <dt class="text-xs text-ink/70">{{ $label }}</dt>
                                <dd class="font-medium">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div class="ticket-tear p-5">
                    @if ($applyMail)
                        <a href="{{ $applyMail }}" class="btn-ink w-full">Gửi hồ sơ qua email</a>
                    @else
                        <p class="text-sm">Công ty chưa để lại email nhận hồ sơ.</p>
                    @endif
                    <div aria-hidden="true" class="ticket-barcode mt-4"></div>
                    <p class="mt-1 font-board text-xs tracking-widest">MÃ TIN {{ str_pad((string) $job->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </aside>

            <div class="min-w-0 space-y-8">
                @foreach ($sections as $heading => $body)
                    <section>
                        <h2 class="mb-2 font-signage text-2xl font-bold tracking-wide text-flap uppercase">{{ $heading }}</h2>
                        <p class="max-w-prose leading-relaxed whitespace-pre-line text-paper/90">{{ $body }}</p>
                    </section>
                @endforeach

                @if ($job->skills->isNotEmpty())
                    <section>
                        <h2 class="mb-3 font-signage text-2xl font-bold tracking-wide text-flap uppercase">Kỹ năng cần có</h2>
                        <ul class="flex flex-wrap gap-2 text-sm">
                            @foreach ($job->skills as $skill)
                                <li class="rounded bg-flap px-3 py-1 font-medium text-ink">{{ $skill->name }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="border-t border-white/15 pt-8">
                    <h2 class="mb-2 font-signage text-2xl font-bold tracking-wide text-flap uppercase">Về {{ $company->name }}</h2>
                    @if ($company->description)
                        <p class="max-w-prose leading-relaxed whitespace-pre-line text-paper/90">{{ $company->description }}</p>
                    @endif
                    <ul class="mt-3 space-y-1 text-sm text-paper/80">
                        @if ($company->address)
                            <li>Địa chỉ: {{ $company->address }}</li>
                        @endif
                        @if ($company->company_size)
                            <li>Quy mô: {{ $company->company_size }} nhân sự</li>
                        @endif
                        @if ($websiteUrl)
                            <li><a href="{{ $websiteUrl }}" target="_blank" rel="noopener nofollow" class="text-flap underline underline-offset-4">Website công ty</a></li>
                        @endif
                    </ul>
                    <a href="{{ route('home', ['q' => $company->name]) }}#viec-lam" class="board-link -ml-1.5 mt-2 text-sm text-flap underline underline-offset-4">Xem mọi vị trí của công ty này</a>
                </section>
            </div>
        </div>

        @if ($relatedJobs->isNotEmpty())
            <section class="mt-14">
                <h2 class="mb-4 font-signage text-3xl font-bold tracking-wide uppercase">Việc khác cùng ngành</h2>
                <div class="overflow-hidden rounded-lg border border-white/15 bg-black/30">
                    @foreach ($relatedJobs as $job)
                        @include('client.partials.job-row')
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
