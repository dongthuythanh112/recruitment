@extends('client.layouts.app')

@section('title', 'Ứng tuyển '.$job->title)
@section('description', 'Gửi hồ sơ ứng tuyển vào vị trí '.$job->title.'.')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-8 sm:py-12">
        <a href="{{ route('jobs.show', $job->slug) }}" class="inline-flex items-center gap-2 text-sm text-slate-600 transition hover:text-sky-800"><span aria-hidden="true">←</span> Quay lại tin tuyển dụng</a>

        <div class="mt-5 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_19rem]">
            <main>
                <header class="rounded-3xl bg-gradient-to-br from-sky-800 via-sky-700 to-cyan-600 p-5 text-white shadow-xl shadow-sky-900/15 sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] !text-white/75">Đơn ứng tuyển</p>
                    <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase !text-white sm:text-5xl">{{ $job->title }}</h1>
                    <p class="mt-2 text-sm !text-white/85">{{ $job->company->name }}{{ $job->location ? ' · '.$job->location : '' }}</p>
                </header>

                @if ($errors->any())
                    <div role="alert" class="mt-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <p class="font-semibold">Vui lòng kiểm tra lại hồ sơ.</p>
                        <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                @if ($resumes->isEmpty())
                    <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-950 sm:p-6">
                        <h2 class="font-semibold">Bạn cần có CV trước khi ứng tuyển</h2>
                        <p class="mt-1 text-sm text-amber-900/75">Tải lên CV PDF, sau đó quay lại đây để gửi hồ sơ.</p>
                        <a href="{{ route('candidate.resumes.index') }}" class="mt-4 inline-flex min-h-10 items-center rounded-lg bg-sky-800 px-4 text-sm font-semibold text-white transition hover:bg-sky-900">Quản lý CV</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('candidate.applications.store', $job) }}" class="mt-5 space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                        @csrf
                        <fieldset>
                            <legend class="text-base font-semibold text-slate-900">Chọn CV gửi nhà tuyển dụng <span class="text-rose-600">*</span></legend>
                            <p class="mt-1 text-sm text-slate-500">Chọn phiên bản phù hợp nhất với vị trí này.</p>
                            <div class="mt-4 space-y-3">
                                @foreach ($resumes as $resume)
                                    <label @class([
                                        'flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition',
                                        'border-sky-400 bg-sky-50 ring-2 ring-sky-100' => (string) old('resume_id', $defaultResumeId) === (string) $resume->id,
                                        'border-slate-200 hover:border-sky-300' => (string) old('resume_id', $defaultResumeId) !== (string) $resume->id,
                                    ])>
                                        <input type="radio" name="resume_id" value="{{ $resume->id }}" required @checked((string) old('resume_id', $defaultResumeId) === (string) $resume->id) class="mt-1 size-4 border-slate-300 text-sky-700 focus:ring-sky-600">
                                        <span class="min-w-0 flex-1">
                                            <span class="flex flex-wrap items-center gap-2 font-semibold text-slate-900">{{ $resume->title }} @if ($resume->is_default)<span class="rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-semibold text-sky-900">Mặc định</span>@endif</span>
                                            <span class="mt-1 block break-all text-xs text-slate-500">{{ $resume->original_filename }} · {{ $resume->created_at?->format('d/m/Y') }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('resume_id')<p class="mt-2 text-sm text-rose-700">{{ $message }}</p>@enderror
                            <a href="{{ route('candidate.resumes.index') }}" class="mt-3 inline-flex text-xs font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Thêm hoặc quản lý CV</a>
                        </fieldset>

                        <div>
                            <label for="cover_letter" class="block text-base font-semibold text-slate-900">Lời nhắn cho nhà tuyển dụng <span class="font-normal text-slate-500">(không bắt buộc)</span></label>
                            <p class="mt-1 text-sm text-slate-500">Giới thiệu ngắn gọn lý do bạn phù hợp với vị trí này.</p>
                            <textarea id="cover_letter" name="cover_letter" rows="6" maxlength="5000" class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10" placeholder="Kính gửi nhà tuyển dụng...">{{ old('cover_letter') }}</textarea>
                            @error('cover_letter')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                            <a href="{{ route('jobs.show', $job->slug) }}" class="text-sm font-medium text-slate-500 underline underline-offset-4 hover:text-slate-800">Hủy</a>
                            <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-sky-800 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-900 focus:outline-none focus:ring-4 focus:ring-sky-700/20">Gửi hồ sơ ứng tuyển <span aria-hidden="true">→</span></button>
                        </div>
                    </form>
                @endif
            </main>

            <aside class="rounded-2xl border border-sky-200 bg-sky-50 p-5 sm:p-6 lg:sticky lg:top-24">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-900">Trước khi gửi</p>
                <ul class="mt-4 space-y-3 text-sm leading-5 text-slate-700">
                    <li class="flex gap-2"><span class="font-bold text-sky-700">✓</span><span>Kiểm tra CV đã chọn là phiên bản mới nhất.</span></li>
                    <li class="flex gap-2"><span class="font-bold text-sky-700">✓</span><span>Thông tin hồ sơ cá nhân nên được cập nhật đầy đủ.</span></li>
                    <li class="flex gap-2"><span class="font-bold text-sky-700">✓</span><span>Bạn có thể theo dõi trạng thái trong mục đơn ứng tuyển.</span></li>
                </ul>
                <a href="{{ route('candidate.profile.edit') }}" class="mt-5 inline-flex text-sm font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Cập nhật hồ sơ cá nhân</a>
            </aside>
        </div>
    </section>
@endsection
