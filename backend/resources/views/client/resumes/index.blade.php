@extends('client.layouts.app')

@section('title', 'Quản lý CV')
@section('description', 'Tải lên và quản lý các CV ứng tuyển của bạn.')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
        <header class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-sky-800 via-sky-700 to-cyan-600 px-5 py-7 text-white shadow-xl shadow-sky-900/15 sm:px-8 sm:py-9">
            <div aria-hidden="true" class="absolute -right-12 -top-24 -z-10 size-72 rounded-full border-[36px] border-white/10 sm:size-96"></div>
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-2xl">
                    <p class="!text-white/75 text-xs font-semibold uppercase tracking-[0.18em]">Tài khoản ứng viên</p>
                    <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase !text-white sm:text-5xl">Quản lý CV</h1>
                    <p class="mt-2 text-sm leading-6 !text-white/85">Lưu các phiên bản CV của bạn tại một nơi, sẵn sàng cho cơ hội tiếp theo.</p>
                </div>
                <div class="flex gap-3">
                    <div class="min-w-24 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold !text-white">{{ $resumes->count() }}</p>
                        <p class="mt-0.5 text-[11px] !text-white/75">CV đã lưu</p>
                    </div>
                    <div class="min-w-24 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-center backdrop-blur-sm">
                        <p class="text-2xl font-bold !text-white">{{ $resumes->where('is_default', true)->count() }}</p>
                        <p class="mt-0.5 text-[11px] !text-white/75">CV mặc định</p>
                    </div>
                </div>
            </div>
        </header>

        @if ($errors->any())
            <div role="alert" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold">Không thể tải CV lên.</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_19rem]">
            <div class="space-y-4">
                @forelse ($resumes as $resume)
                    <article @class([
                        'rounded-2xl border bg-white p-5 shadow-sm transition-shadow hover:shadow-md sm:p-6',
                        'border-sky-300 ring-2 ring-sky-100' => $resume->is_default,
                        'border-slate-200' => ! $resume->is_default,
                    ])>
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-sky-100 to-cyan-50 text-sm font-extrabold tracking-wide text-sky-800 ring-1 ring-sky-200/70">PDF</span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="break-words font-semibold text-slate-900">{{ $resume->title }}</h2>
                                        @if ($resume->is_default)
                                            <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[11px] font-semibold text-sky-900">CV mặc định</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 break-all text-xs text-slate-500">{{ $resume->original_filename }} · Tải lên {{ $resume->created_at?->format('d/m/Y') }}</p>
                                    @if ($resume->applications_count > 0)
                                        <p class="mt-2 text-xs text-slate-500">Đã dùng trong {{ $resume->applications_count }} hồ sơ ứng tuyển</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('candidate.resumes.view', $resume) }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-700 transition hover:border-sky-500 hover:text-sky-800">Xem CV</a>
                                <a href="{{ route('candidate.resumes.download', $resume) }}" class="inline-flex min-h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-700 transition hover:border-sky-500 hover:text-sky-800">Tải xuống</a>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-slate-100 pt-4">
                            @unless ($resume->is_default)
                                <form method="POST" action="{{ route('candidate.resumes.default', $resume) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Đặt làm CV mặc định</button>
                                </form>
                            @endunless
                            @if ($resume->applications_count === 0)
                                <form method="POST" action="{{ route('candidate.resumes.destroy', $resume) }}" onsubmit="return confirm('Bạn có chắc muốn xóa CV này?')" class="ml-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-700 underline underline-offset-4 hover:text-rose-900">Xóa CV</button>
                                </form>
                            @else
                                <span class="ml-auto text-xs text-slate-400">Không thể xóa CV đã dùng để ứng tuyển</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
                        <span aria-hidden="true" class="mx-auto grid size-14 place-items-center rounded-2xl bg-sky-50 text-2xl text-sky-800">▤</span>
                        <h2 class="mt-4 font-semibold text-slate-900">Bạn chưa có CV nào</h2>
                        <p class="mt-1 text-sm text-slate-500">Tải lên CV PDF để sẵn sàng ứng tuyển.</p>
                    </div>
                @endforelse
            </div>

            <aside class="rounded-2xl border border-sky-200 bg-gradient-to-b from-sky-50 to-white p-5 shadow-sm sm:sticky sm:top-24 sm:p-6">
                <span class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-sky-900"><span aria-hidden="true">＋</span> Thêm hồ sơ</span>
                <h2 class="mt-3 font-signage text-2xl font-extrabold tracking-wide uppercase">Tải CV lên</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Hỗ trợ PDF, dung lượng tối đa 10 MB. CV đầu tiên sẽ được đặt làm mặc định.</p>

                <form method="POST" action="{{ route('candidate.resumes.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="title" class="mb-1.5 block text-sm font-medium text-slate-800">Tên CV <span class="font-normal text-slate-500">(không bắt buộc)</span></label>
                        <input id="title" name="title" type="text" maxlength="255" value="{{ old('title') }}" placeholder="Ví dụ: CV Marketing 2026" class="min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10">
                        @error('title')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="file" class="mb-1.5 block text-sm font-medium text-slate-800">Chọn tệp PDF <span class="text-rose-600">*</span></label>
                        <input id="file" name="file" type="file" accept="application/pdf,.pdf" required class="block min-h-11 w-full cursor-pointer rounded-xl border border-dashed border-sky-300 bg-white text-sm text-slate-600 transition hover:border-sky-500 file:mr-3 file:h-11 file:border-0 file:bg-sky-100 file:px-3 file:font-semibold file:text-sky-900 hover:file:bg-sky-200">
                        @error('file')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg bg-sky-700 px-4 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-700/20">Tải CV lên <span aria-hidden="true">↑</span></button>
                </form>
            </aside>
        </div>
    </section>
@endsection
