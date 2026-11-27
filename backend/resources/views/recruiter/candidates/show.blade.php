@extends('client.layouts.app')

@section('title', 'Hồ sơ '.$candidate->user->name)
@section('description', 'Thông tin hồ sơ ứng viên đã cho phép nhà tuyển dụng xem.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:py-14">
            <a href="{{ route('recruiter.candidates.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950">
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M16.75 10a.75.75 0 0 1-.75.75H5.81l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 1 1 1.06 1.06l-3.22 3.22H16a.75.75 0 0 1 .75.75Z" clip-rule="evenodd" /></svg>
                Danh sách hồ sơ
            </a>

            <header class="mt-5 rounded-3xl bg-gradient-to-br from-sky-900 via-sky-800 to-cyan-700 p-6 text-white shadow-xl shadow-sky-900/15 sm:p-8">
                <div class="flex flex-wrap items-start gap-4">
                    @if ($candidate->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($candidate->avatar) }}" alt="" class="size-16 rounded-2xl object-cover ring-2 ring-white/30">
                    @else
                        <span class="grid size-16 place-items-center rounded-2xl bg-white/15 text-2xl font-bold text-white">{{ mb_strtoupper(mb_substr($candidate->user->name, 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Ứng viên cho phép xem hồ sơ</p>
                        <h1 class="mt-1 text-2xl font-extrabold sm:text-3xl">{{ $candidate->user->name }}</h1>
                        <p class="mt-1 text-sm text-white/85">{{ $candidate->desired_position ?: 'Đang tìm kiếm cơ hội phù hợp' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs font-semibold text-white">
                        @if ($candidate->years_of_experience !== null)<span class="rounded-full bg-white/15 px-3 py-1.5">{{ number_format((float) $candidate->years_of_experience, 1, ',', '') }} năm kinh nghiệm</span>@endif
                        @if ($candidate->address)<span class="rounded-full bg-white/15 px-3 py-1.5">{{ $candidate->address }}</span>@endif
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap gap-x-6 gap-y-2 border-t border-white/15 pt-4 text-sm text-white/90">
                    <a href="mailto:{{ $candidate->user->email }}" class="hover:underline">{{ $candidate->user->email }}</a>
                    @if ($candidate->user->phone)<a href="tel:{{ $candidate->user->phone }}" class="hover:underline">{{ $candidate->user->phone }}</a>@endif
                </div>
            </header>

            @if ($candidate->summary)
                <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-bold text-slate-900">Giới thiệu</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $candidate->summary }}</p>
                </section>
            @endif

            <div class="mt-5 grid gap-5 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-bold text-slate-900">Kỹ năng</h2>
                    @if ($candidate->skills->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($candidate->skills as $skill)
                                <span class="rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800">{{ $skill->name }}{{ $skill->pivot->level ? ' · '.$skill->pivot->level : '' }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-sm text-slate-500">Ứng viên chưa thêm kỹ năng.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-bold text-slate-900">CV</h2>
                    @if ($candidate->resumes->isNotEmpty())
                        <ul class="mt-3 space-y-3">
                            @foreach ($candidate->resumes as $resume)
                                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-slate-800">{{ $resume->title }}</span>
                                        <span class="mt-0.5 block truncate text-xs text-slate-500">{{ $resume->original_filename }}</span>
                                    </span>
                                    <span class="flex shrink-0 gap-2">
                                        <a href="{{ route('recruiter.candidates.resumes.view', [$candidate, $resume]) }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-sky-800 hover:border-sky-300">Xem</a>
                                        <a href="{{ route('recruiter.candidates.resumes.download', [$candidate, $resume]) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-sky-800 hover:border-sky-300">Tải</a>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-slate-500">Ứng viên chưa tải CV lên.</p>
                    @endif
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-bold text-slate-900">Kinh nghiệm làm việc</h2>
                    <div class="mt-3 space-y-4">
                        @forelse ($candidate->experiences as $experience)
                            <article class="border-l-2 border-sky-200 pl-4">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $experience->position }}</h3>
                                <p class="mt-0.5 text-xs text-slate-600">{{ $experience->company_name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $experience->start_date?->format('m/Y') }} – {{ $experience->is_current ? 'Hiện tại' : $experience->end_date?->format('m/Y') }}</p>
                                @if ($experience->description)<p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $experience->description }}</p>@endif
                            </article>
                        @empty
                            <p class="text-sm text-slate-500">Ứng viên chưa thêm kinh nghiệm.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="font-bold text-slate-900">Học vấn</h2>
                    <div class="mt-3 space-y-4">
                        @forelse ($candidate->educations as $education)
                            <article class="border-l-2 border-sky-200 pl-4">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $education->school_name }}</h3>
                                <p class="mt-0.5 text-xs text-slate-600">{{ $education->degree }}{{ $education->major ? ' · '.$education->major : '' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $education->start_date?->format('Y') }} – {{ $education->end_date?->format('Y') ?: 'Hiện tại' }}</p>
                            </article>
                        @empty
                            <p class="text-sm text-slate-500">Ứng viên chưa thêm học vấn.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
