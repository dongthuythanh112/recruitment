@extends('client.layouts.app')

@section('title', 'Gợi ý ứng viên')
@section('description', 'Ứng viên đã bật tìm kiếm, được lọc và sắp xếp theo yêu cầu tuyển dụng của bạn.')

@section('content')
    <section class="bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:py-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-800">Tuyển dụng phù hợp hơn</p>
                    <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Gợi ý ứng viên</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Chỉ hiển thị hồ sơ ứng viên đã bật chế độ tìm kiếm. Hệ thống xếp hạng theo vị trí mong muốn và kinh nghiệm làm việc, địa điểm, số năm kinh nghiệm, mức lương mong muốn và kỹ năng phù hợp.</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">{{ $candidates->total() }} ứng viên phù hợp</span>
            </div>

            <form method="GET" action="{{ route('recruiter.candidates.index') }}" class="mt-6 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="font-bold text-slate-900">Bộ lọc tiêu chí</h2>
                        <p class="mt-0.5 text-xs leading-5 text-slate-500">Chọn một tin để đối chiếu. Nếu chưa chọn bộ lọc, hệ thống tự dùng tin đã duyệt mới nhất của bạn; bạn cũng có thể nhập tiêu chí riêng.</p>
                    </div>
                    @if ($hasCriteria)
                        <a href="{{ route('recruiter.candidates.index') }}" class="text-xs font-semibold text-sky-800 hover:text-sky-950">Xóa bộ lọc</a>
                    @endif
                </div>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="job_id" class="mb-1 block text-xs font-semibold text-slate-700">Dùng yêu cầu từ tin tuyển dụng</label>
                        <select id="job_id" name="job_id" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                            <option value="">Tự nhập tiêu chí</option>
                            @foreach ($jobs as $jobOption)
                                <option value="{{ $jobOption->id }}" @selected((string) $filters['job_id'] === (string) $jobOption->id)>{{ $jobOption->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="position" class="mb-1 block text-xs font-semibold text-slate-700">Vị trí / kỹ năng</label>
                        <input id="position" name="position" value="{{ $filters['position'] }}" maxlength="150" placeholder="Ví dụ: Kế toán, Laravel" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                    </div>
                    <div>
                        <label for="location" class="mb-1 block text-xs font-semibold text-slate-700">Địa điểm</label>
                        <input id="location" name="location" value="{{ $filters['location'] }}" maxlength="150" placeholder="Ví dụ: Hà Nội" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                    </div>
                    <div>
                        <label for="experience_min" class="mb-1 block text-xs font-semibold text-slate-700">Kinh nghiệm tối thiểu (năm)</label>
                        <input id="experience_min" name="experience_min" type="number" min="0" max="80" step="0.5" value="{{ $filters['experience_min'] }}" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                    </div>
                    <div>
                        <label for="salary_min" class="mb-1 block text-xs font-semibold text-slate-700">Lương tối thiểu (VND/tháng)</label>
                        <input id="salary_min" name="salary_min" type="number" min="0" step="100000" value="{{ $filters['salary_min'] }}" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                    </div>
                    <div>
                        <label for="salary_max" class="mb-1 block text-xs font-semibold text-slate-700">Lương tối đa (VND/tháng)</label>
                        <input id="salary_max" name="salary_max" type="number" min="0" step="100000" value="{{ $filters['salary_max'] }}" class="min-h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 focus:border-sky-600 focus:outline-none focus:ring-4 focus:ring-sky-600/10">
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
                    <p class="text-xs text-slate-500">Hồ sơ chưa nhập lương hoặc kinh nghiệm vẫn có thể xuất hiện, nhưng sẽ không được cộng điểm ở tiêu chí đó.</p>
                    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-sky-800 px-5 text-sm font-bold text-white transition hover:bg-sky-900">Lọc ứng viên phù hợp</button>
                </div>
            </form>

            <p class="mt-5 text-sm font-semibold text-slate-700">
                {{ $hasCriteria ? 'Tìm thấy '.$candidates->total().' ứng viên theo tiêu chí đã chọn' : 'Chọn tin tuyển dụng hoặc nhập tiêu chí để xếp hạng ứng viên' }}
            </p>

            @if ($candidates->isEmpty())
                <div class="mt-7 rounded-2xl border border-dashed border-sky-200 bg-white px-5 py-14 text-center">
                    <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-100 text-sky-800">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-7"><circle cx="9" cy="8" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 20v-1.5a6.5 6.5 0 0 1 13 0V20m2-11a3.5 3.5 0 1 0 0-7m1 10a5.5 5.5 0 0 1 3 5v1"/></svg>
                    </span>
                    <h2 class="mt-4 font-bold text-slate-900">{{ $hasCriteria ? 'Chưa có ứng viên khớp bộ lọc' : 'Chưa có hồ sơ đang bật tìm kiếm' }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $hasCriteria ? 'Hãy thử nới lỏng tiêu chí hoặc chọn tin tuyển dụng khác.' : 'Ứng viên sẽ xuất hiện tại đây khi cho phép nhà tuyển dụng xem hồ sơ.' }}</p>
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
                                <div class="min-w-0 flex-1">
                                    <h2 class="truncate font-bold text-slate-900">{{ $candidate->user->name }}</h2>
                                    <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $candidate->desired_position ?: 'Đang tìm kiếm cơ hội phù hợp' }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">{{ $hasCriteria ? count($candidate->recommendation_reasons).' tiêu chí khớp' : 'Đang tìm việc' }}</span>
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
                            @if ($candidate->recommendation_reasons)
                                <p class="mt-3 text-[11px] leading-5 text-slate-500">{{ implode(' · ', $candidate->recommendation_reasons) }}</p>
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
