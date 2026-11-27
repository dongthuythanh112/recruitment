@extends('client.layouts.app')

@section('title', 'Hồ sơ ứng viên')
@section('description', 'Tìm kiếm hồ sơ ứng viên đã bật cho phép nhà tuyển dụng xem.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Ứng viên đang tìm cơ hội</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Hồ sơ ứng viên</h1>
                    <p class="mt-2 text-sm text-slate-600">Chỉ hiển thị ứng viên đã bật cho phép nhà tuyển dụng tìm hồ sơ.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $candidates->total() }} hồ sơ công khai</span>
            </div>

            @if ($candidates->isEmpty())
                <div class="mt-7 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-14 text-center">
                    <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-800">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7"><circle cx="9" cy="8" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20m2-11a3.5 3.5 0 1 0 0-7m1 10a5.5 5.5 0 0 1 3 5v1"/></svg>
                    </span>
                    <h2 class="mt-4 font-bold text-slate-900">Chưa có hồ sơ đang bật tìm kiếm</h2>
                    <p class="mt-1 text-sm text-slate-500">Ứng viên sẽ xuất hiện tại đây khi cho phép nhà tuyển dụng xem hồ sơ.</p>
                </div>
            @else
                <div class="mt-7 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($candidates as $candidate)
                        <article class="flex min-h-56 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-sky-300 hover:shadow-md">
                            <div class="flex items-start gap-3">
                                @if ($candidate->avatar)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($candidate->avatar) }}" alt="" class="size-12 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
                                @else
                                    <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-sky-100 text-lg font-bold text-sky-800">{{ mb_strtoupper(mb_substr($candidate->user->name, 0, 1)) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <h2 class="truncate font-bold text-slate-900">{{ $candidate->user->name }}</h2>
                                    <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $candidate->desired_position ?: 'Đang tìm kiếm cơ hội phù hợp' }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
                                @if ($candidate->address)<span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ $candidate->address }}</span>@endif
                                @if ($candidate->years_of_experience !== null)<span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ number_format((float) $candidate->years_of_experience, 1, ',', '') }} năm kinh nghiệm</span>@endif
                                <span class="rounded-lg bg-slate-50 px-2.5 py-1.5">{{ $candidate->skills_count }} kỹ năng</span>
                            </div>
                            @if ($candidate->skills->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach ($candidate->skills->take(4) as $skill)
                                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-semibold text-sky-800">{{ $skill->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('recruiter.candidates.show', $candidate) }}" class="mt-auto inline-flex min-h-10 items-center justify-center rounded-xl bg-sky-800 px-4 py-2 text-sm font-bold text-white transition hover:bg-sky-900">Xem hồ sơ</a>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">{{ $candidates->links() }}</div>
            @endif
        </div>
    </section>
@endsection
