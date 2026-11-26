@extends('client.layouts.app')

@section('title', 'Hồ sơ cá nhân')
@section('description', 'Cập nhật thông tin, học vấn, kinh nghiệm và kỹ năng ứng viên.')

@section('content')
    @php($field = 'min-h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10')
    @php($label = 'mb-1.5 block text-sm font-medium text-slate-800')

    <section class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
        <header class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-sky-800 via-sky-700 to-cyan-600 px-5 py-7 text-white shadow-xl shadow-sky-900/15 sm:px-8 sm:py-9">
            <div aria-hidden="true" class="absolute -right-12 -top-24 -z-10 size-72 rounded-full border-[36px] border-white/10 sm:size-96"></div>
            <div class="flex flex-wrap items-center gap-5">
                <div class="grid size-20 shrink-0 place-items-center overflow-hidden rounded-2xl border border-white/25 bg-white/15 text-3xl font-bold !text-white shadow-lg">
                    @if ($candidate->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($candidate->avatar) }}" alt="Ảnh đại diện {{ $account->name }}" class="size-full object-cover">
                    @else
                        {{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] !text-white/75">Hồ sơ ứng viên</p>
                    <h1 class="mt-1 font-signage text-4xl font-extrabold tracking-wide uppercase !text-white sm:text-5xl">{{ $account->name }}</h1>
                    <p class="mt-1 text-sm !text-white/85">{{ $candidate->desired_position ?: 'Hoàn thiện hồ sơ để nhà tuyển dụng hiểu hơn về bạn' }}</p>
                </div>
                <a href="{{ route('candidate.resumes.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-white/30 bg-white/10 px-4 text-sm font-semibold !text-white transition hover:bg-white/20">Quản lý CV</a>
            </div>
        </header>

        @if ($errors->any())
            <div role="alert" class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold">Vui lòng kiểm tra lại thông tin.</p>
                <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <div class="space-y-6">
                <section id="thong-tin" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Thông tin cơ bản</p>
                        <h2 class="mt-1 font-signage text-2xl font-extrabold tracking-wide uppercase">Thông tin cá nhân</h2>
                    </div>

                    <form method="POST" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="name" class="{{ $label }}">Họ và tên <span class="text-rose-600">*</span></label>
                                <input id="name" name="name" required maxlength="255" value="{{ old('name', $account->name) }}" autocomplete="name" class="{{ $field }}">
                                @error('name')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="{{ $label }}">Email <span class="text-rose-600">*</span></label>
                                <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email', $account->email) }}" autocomplete="email" class="{{ $field }}">
                                @error('email')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="phone" class="{{ $label }}">Số điện thoại</label>
                                <input id="phone" name="phone" type="tel" maxlength="20" value="{{ old('phone', $account->phone) }}" autocomplete="tel" class="{{ $field }}">
                                @error('phone')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="date_of_birth" class="{{ $label }}">Ngày sinh</label>
                                <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', $candidate->date_of_birth?->format('Y-m-d')) }}" class="{{ $field }}">
                                @error('date_of_birth')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="gender" class="{{ $label }}">Giới tính</label>
                                <select id="gender" name="gender" class="{{ $field }}">
                                    <option value="">Chưa chọn</option>
                                    @foreach (\App\Models\CandidateProfile::GENDER_LABELS as $value => $genderLabel)
                                        <option value="{{ $value }}" @selected(old('gender', $candidate->gender) === $value)>{{ $genderLabel }}</option>
                                    @endforeach
                                </select>
                                @error('gender')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="avatar" class="{{ $label }}">Ảnh đại diện <span class="font-normal text-slate-500">(tối đa 2 MB)</span></label>
                                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="block min-h-11 w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-3 file:h-11 file:border-0 file:bg-sky-100 file:px-3 file:font-semibold file:text-sky-900 hover:file:bg-sky-200">
                                @error('avatar')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="address" class="{{ $label }}">Địa chỉ</label>
                            <input id="address" name="address" maxlength="500" value="{{ old('address', $candidate->address) }}" autocomplete="street-address" class="{{ $field }}">
                            @error('address')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="summary" class="{{ $label }}">Giới thiệu bản thân</label>
                            <textarea id="summary" name="summary" rows="4" maxlength="5000" placeholder="Chia sẻ ngắn về điểm mạnh, kinh nghiệm và mục tiêu nghề nghiệp..." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10">{{ old('summary', $candidate->summary) }}</textarea>
                            @error('summary')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex justify-end border-t border-slate-100 pt-4">
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-sky-700 px-5 text-sm font-semibold text-white transition hover:bg-sky-800">Lưu thông tin</button>
                        </div>
                    </form>
                </section>

                <section id="hoc-van" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Định hướng nghề nghiệp</p>
                        <h2 class="mt-1 font-signage text-2xl font-extrabold tracking-wide uppercase">Công việc mong muốn</h2>
                    </div>
                    <form method="POST" action="{{ route('candidate.profile.update') }}" class="grid gap-4 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $account->name }}">
                        <input type="hidden" name="email" value="{{ $account->email }}">
                        <input type="hidden" name="phone" value="{{ $account->phone }}">
                        <input type="hidden" name="date_of_birth" value="{{ $candidate->date_of_birth?->format('Y-m-d') }}">
                        <input type="hidden" name="gender" value="{{ $candidate->gender }}">
                        <input type="hidden" name="address" value="{{ $candidate->address }}">
                        <input type="hidden" name="summary" value="{{ $candidate->summary }}">
                        <div class="sm:col-span-2">
                            <label for="desired_position" class="{{ $label }}">Vị trí mong muốn</label>
                            <input id="desired_position" name="desired_position" maxlength="255" value="{{ old('desired_position', $candidate->desired_position) }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="desired_salary_min" class="{{ $label }}">Lương tối thiểu (VND/tháng)</label>
                            <input id="desired_salary_min" name="desired_salary_min" type="number" min="0" step="100000" value="{{ old('desired_salary_min', $candidate->desired_salary_min) }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="desired_salary_max" class="{{ $label }}">Lương tối đa (VND/tháng)</label>
                            <input id="desired_salary_max" name="desired_salary_max" type="number" min="0" step="100000" value="{{ old('desired_salary_max', $candidate->desired_salary_max) }}" class="{{ $field }}">
                            @error('desired_salary_max')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="years_of_experience" class="{{ $label }}">Số năm kinh nghiệm</label>
                            <input id="years_of_experience" name="years_of_experience" type="number" min="0" max="80" step="0.5" value="{{ old('years_of_experience', $candidate->years_of_experience) }}" class="{{ $field }}">
                        </div>
                        <div class="flex items-end justify-end">
                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-sky-700 px-5 text-sm font-semibold text-white transition hover:bg-sky-800 sm:w-auto">Lưu định hướng</button>
                        </div>
                    </form>
                </section>

                <section id="kinh-nghiem" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Quá trình học tập</p>
                            <h2 class="mt-1 font-signage text-2xl font-extrabold tracking-wide uppercase">Học vấn <span class="font-client text-sm font-medium text-slate-400">({{ $candidate->educations->count() }})</span></h2>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($candidate->educations as $education)
                            <details class="group rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
                                    <span class="min-w-0"><span class="block truncate font-semibold text-slate-900">{{ $education->school_name }}</span><span class="mt-1 block text-xs text-slate-500">{{ $education->degree }}{{ $education->major ? ' · '.$education->major : '' }}</span></span>
                                    <span class="shrink-0 text-xs font-semibold text-sky-800 group-open:hidden">Chỉnh sửa</span><span class="hidden shrink-0 text-xs font-semibold text-slate-500 group-open:inline">Thu gọn</span>
                                </summary>
                                <form method="POST" action="{{ route('candidate.profile.education.update', $education) }}" class="mt-4 grid gap-3 border-t border-slate-200 pt-4 sm:grid-cols-2">
                                    @csrf @method('PUT')
                                    <div><label class="{{ $label }}">Trường học <span class="text-rose-600">*</span></label><input name="school_name" required maxlength="255" value="{{ $education->school_name }}" class="{{ $field }}"></div>
                                    <div><label class="{{ $label }}">Bằng cấp</label><input name="degree" maxlength="255" value="{{ $education->degree }}" class="{{ $field }}"></div>
                                    <div><label class="{{ $label }}">Chuyên ngành</label><input name="major" maxlength="255" value="{{ $education->major }}" class="{{ $field }}"></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label class="{{ $label }}">Bắt đầu</label><input name="start_date" type="date" value="{{ $education->start_date?->format('Y-m-d') }}" class="{{ $field }}"></div><div><label class="{{ $label }}">Kết thúc</label><input name="end_date" type="date" value="{{ $education->end_date?->format('Y-m-d') }}" class="{{ $field }}"></div></div>
                                    <div class="sm:col-span-2"><label class="{{ $label }}">Mô tả</label><textarea name="description" rows="2" maxlength="3000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-sky-600 focus:outline-none">{{ $education->description }}</textarea></div>
                                    <div class="flex items-center justify-between gap-3 sm:col-span-2"><button class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-4 text-xs font-semibold text-white hover:bg-sky-800">Lưu học vấn</button></form><form method="POST" action="{{ route('candidate.profile.education.destroy', $education) }}" onsubmit="return confirm('Xóa mục học vấn này?')">@csrf @method('DELETE')<button class="text-xs font-medium text-rose-700 underline underline-offset-4">Xóa</button></form></div>
                            </details>
                        @empty
                            <p class="rounded-xl bg-slate-50 px-4 py-5 text-sm text-slate-500">Chưa có thông tin học vấn. Thêm thông tin bên dưới nhé.</p>
                        @endforelse
                    </div>
                    <details class="mt-4 rounded-xl border border-dashed border-sky-300 bg-sky-50/60 p-4">
                        <summary class="cursor-pointer text-sm font-semibold text-sky-900">+ Thêm học vấn</summary>
                        <form method="POST" action="{{ route('candidate.profile.education.store') }}" class="mt-4 grid gap-3 border-t border-sky-100 pt-4 sm:grid-cols-2">
                            @csrf
                            <div><label class="{{ $label }}">Trường học <span class="text-rose-600">*</span></label><input name="school_name" required maxlength="255" class="{{ $field }}"></div>
                            <div><label class="{{ $label }}">Bằng cấp</label><input name="degree" maxlength="255" class="{{ $field }}"></div>
                            <div><label class="{{ $label }}">Chuyên ngành</label><input name="major" maxlength="255" class="{{ $field }}"></div>
                            <div class="grid grid-cols-2 gap-3"><div><label class="{{ $label }}">Bắt đầu</label><input name="start_date" type="date" class="{{ $field }}"></div><div><label class="{{ $label }}">Kết thúc</label><input name="end_date" type="date" class="{{ $field }}"></div></div>
                            <div class="sm:col-span-2"><label class="{{ $label }}">Mô tả</label><textarea name="description" rows="2" maxlength="3000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-sky-600 focus:outline-none"></textarea></div>
                            <div class="sm:col-span-2"><button class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-4 text-xs font-semibold text-white hover:bg-sky-800">Thêm học vấn</button></div>
                        </form>
                    </details>
                </section>

                <section id="ky-nang" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Lịch sử công việc</p>
                        <h2 class="mt-1 font-signage text-2xl font-extrabold tracking-wide uppercase">Kinh nghiệm <span class="font-client text-sm font-medium text-slate-400">({{ $candidate->experiences->count() }})</span></h2>
                    </div>
                    <div class="space-y-3">
                        @forelse ($candidate->experiences as $experience)
                            <details class="group rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 [&::-webkit-details-marker]:hidden">
                                    <span class="min-w-0"><span class="block truncate font-semibold text-slate-900">{{ $experience->position }}</span><span class="mt-1 block text-xs text-slate-500">{{ $experience->company_name }} · {{ $experience->is_current ? 'Đang làm việc' : ($experience->end_date?->format('m/Y') ?? 'Chưa rõ thời gian') }}</span></span>
                                    <span class="shrink-0 text-xs font-semibold text-sky-800 group-open:hidden">Chỉnh sửa</span><span class="hidden shrink-0 text-xs font-semibold text-slate-500 group-open:inline">Thu gọn</span>
                                </summary>
                                <form method="POST" action="{{ route('candidate.profile.experience.update', $experience) }}" class="mt-4 grid gap-3 border-t border-slate-200 pt-4 sm:grid-cols-2">
                                    @csrf @method('PUT')
                                    <div><label class="{{ $label }}">Chức danh <span class="text-rose-600">*</span></label><input name="position" required maxlength="255" value="{{ $experience->position }}" class="{{ $field }}"></div>
                                    <div><label class="{{ $label }}">Công ty <span class="text-rose-600">*</span></label><input name="company_name" required maxlength="255" value="{{ $experience->company_name }}" class="{{ $field }}"></div>
                                    <div class="grid grid-cols-2 gap-3"><div><label class="{{ $label }}">Bắt đầu</label><input name="start_date" type="date" value="{{ $experience->start_date?->format('Y-m-d') }}" class="{{ $field }}"></div><div><label class="{{ $label }}">Kết thúc</label><input name="end_date" type="date" value="{{ $experience->end_date?->format('Y-m-d') }}" @disabled($experience->is_current) class="{{ $field }}"></div></div>
                                    <label class="flex min-h-11 items-center gap-2 self-end text-sm text-slate-700"><input name="is_current" value="1" type="checkbox" @checked($experience->is_current) class="size-4 rounded border-slate-300 text-sky-700 focus:ring-sky-600"> Tôi đang làm ở đây</label>
                                    <div class="sm:col-span-2"><label class="{{ $label }}">Mô tả</label><textarea name="description" rows="2" maxlength="3000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-sky-600 focus:outline-none">{{ $experience->description }}</textarea></div>
                                    <div class="flex items-center justify-between gap-3 sm:col-span-2"><button class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-4 text-xs font-semibold text-white hover:bg-sky-800">Lưu kinh nghiệm</button></form><form method="POST" action="{{ route('candidate.profile.experience.destroy', $experience) }}" onsubmit="return confirm('Xóa mục kinh nghiệm này?')">@csrf @method('DELETE')<button class="text-xs font-medium text-rose-700 underline underline-offset-4">Xóa</button></form></div>
                            </details>
                        @empty
                            <p class="rounded-xl bg-slate-50 px-4 py-5 text-sm text-slate-500">Chưa có kinh nghiệm làm việc. Bạn có thể thêm thông tin bên dưới.</p>
                        @endforelse
                    </div>
                    <details class="mt-4 rounded-xl border border-dashed border-sky-300 bg-sky-50/60 p-4">
                        <summary class="cursor-pointer text-sm font-semibold text-sky-900">+ Thêm kinh nghiệm</summary>
                        <form method="POST" action="{{ route('candidate.profile.experience.store') }}" class="mt-4 grid gap-3 border-t border-sky-100 pt-4 sm:grid-cols-2">
                            @csrf
                            <div><label class="{{ $label }}">Chức danh <span class="text-rose-600">*</span></label><input name="position" required maxlength="255" class="{{ $field }}"></div>
                            <div><label class="{{ $label }}">Công ty <span class="text-rose-600">*</span></label><input name="company_name" required maxlength="255" class="{{ $field }}"></div>
                            <div class="grid grid-cols-2 gap-3"><div><label class="{{ $label }}">Bắt đầu</label><input name="start_date" type="date" class="{{ $field }}"></div><div><label class="{{ $label }}">Kết thúc</label><input name="end_date" type="date" class="{{ $field }}"></div></div>
                            <label class="flex min-h-11 items-center gap-2 self-end text-sm text-slate-700"><input name="is_current" value="1" type="checkbox" class="size-4 rounded border-slate-300 text-sky-700 focus:ring-sky-600"> Tôi đang làm ở đây</label>
                            <div class="sm:col-span-2"><label class="{{ $label }}">Mô tả</label><textarea name="description" rows="2" maxlength="3000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-sky-600 focus:outline-none"></textarea></div>
                            <div class="sm:col-span-2"><button class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-4 text-xs font-semibold text-white hover:bg-sky-800">Thêm kinh nghiệm</button></div>
                        </form>
                    </details>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Năng lực chuyên môn</p>
                        <h2 class="mt-1 font-signage text-2xl font-extrabold tracking-wide uppercase">Kỹ năng</h2>
                    </div>
                    <form method="POST" action="{{ route('candidate.profile.skills') }}" class="space-y-3">
                        @csrf @method('PUT')
                        <label for="skills_text" class="{{ $label }}">Các kỹ năng của bạn</label>
                        <textarea id="skills_text" name="skills_text" rows="3" maxlength="3000" placeholder="Ví dụ: PHP, Laravel, Giao tiếp, Quản lý dự án" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10">{{ old('skills_text', $skillsText) }}</textarea>
                        <p class="text-xs text-slate-500">Phân cách kỹ năng bằng dấu phẩy hoặc xuống dòng, tối đa 30 kỹ năng.</p>
                        @error('skills_text')<p class="text-xs text-rose-700">{{ $message }}</p>@enderror
                        <div class="flex justify-end border-t border-slate-100 pt-4"><button class="inline-flex min-h-11 items-center rounded-lg bg-sky-700 px-5 text-sm font-semibold text-white transition hover:bg-sky-800">Lưu kỹ năng</button></div>
                    </form>
                </section>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-24">
                <div class="rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-800">Hoàn thiện hồ sơ</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Thông tin rõ ràng giúp nhà tuyển dụng hiểu năng lực và định hướng của bạn.</p>
                    <div class="mt-5 space-y-3 text-sm">
                        <a href="#thong-tin" class="flex items-center gap-2 text-slate-700 hover:text-sky-800"><span class="text-sky-700">✓</span> Thông tin cá nhân</a>
                        <a href="#hoc-van" class="flex items-center gap-2 text-slate-700 hover:text-sky-800"><span class="text-sky-700">✓</span> Học vấn</a>
                        <a href="#kinh-nghiem" class="flex items-center gap-2 text-slate-700 hover:text-sky-800"><span class="text-sky-700">✓</span> Kinh nghiệm</a>
                        <a href="#ky-nang" class="flex items-center gap-2 text-slate-700 hover:text-sky-800"><span class="text-sky-700">✓</span> Kỹ năng</a>
                    </div>
                </div>
                <a href="{{ route('candidate.resumes.index') }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-sky-300 hover:text-sky-800">
                    <span><span class="block">CV ứng tuyển</span><span class="mt-1 block text-xs font-normal text-slate-500">Quản lý các phiên bản CV đã tải lên</span></span><span aria-hidden="true" class="text-xl text-sky-700">→</span>
                </a>
            </aside>
        </div>
    </section>
@endsection
