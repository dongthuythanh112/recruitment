@extends('client.layouts.app')

@section('title', 'Thông tin công ty')
@section('description', 'Quản lý hồ sơ công ty của bạn trên Việc Ngon.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-4xl">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-white/15 py-8 sm:py-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Khu vực nhà tuyển dụng</p>
                    <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Thông tin công ty</h1>
                    <p class="mt-2 text-sm text-paper/65">Cập nhật hồ sơ hiển thị cùng tin tuyển dụng của công ty.</p>
                </div>
                <a href="{{ route('recruiter.jobs.index') }}" class="text-sm text-paper/65 underline underline-offset-4 transition hover:text-flap">Quản lý tin tuyển dụng</a>
            </div>

            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-xl border border-rose-300/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-100">
                    <p class="font-semibold">Vui lòng kiểm tra lại thông tin.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
                <aside class="h-fit rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-6">
                    <div class="grid size-16 place-items-center overflow-hidden rounded-2xl bg-flap text-2xl font-bold text-ink">
                        @if ($company->logo)
                            <img src="{{ $company->logo }}" alt="Logo {{ $company->name }}" class="size-full object-contain">
                        @else
                            {{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}
                        @endif
                    </div>
                    <h2 class="mt-4 break-words text-xl font-semibold text-white">{{ $company->name }}</h2>
                    @if ($company->website)
                        <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="mt-1 block break-all text-sm text-flap underline underline-offset-4">{{ $company->website }}</a>
                    @endif
                    <p class="mt-4 text-xs leading-5 text-paper/55">Thông tin này được gắn với các tin tuyển dụng do công ty đăng.</p>
                </aside>

                <form method="POST" action="{{ route('recruiter.company.update') }}" class="space-y-5 rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium">Tên công ty <span class="text-signal">*</span></label>
                        <input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $company->name) }}" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        @error('name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="logo" class="mb-1.5 block text-sm font-medium">Đường dẫn logo</label>
                        <input id="logo" name="logo" type="url" maxlength="500" value="{{ old('logo', $company->logo) }}" placeholder="https://example.com/logo.png" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        <p class="mt-1 text-xs text-ink/50">Nhập URL ảnh logo công khai. Để trống nếu muốn bỏ logo.</p>
                        @error('logo') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-1.5 block text-sm font-medium">Giới thiệu công ty</label>
                        <textarea id="description" name="description" rows="5" maxlength="10000" class="w-full rounded-xl border border-ink/15 bg-white px-3.5 py-3 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Chia sẻ về lĩnh vực, văn hóa và môi trường làm việc...">{{ old('description', $company->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="website" class="mb-1.5 block text-sm font-medium">Website</label>
                            <input id="website" name="website" type="url" maxlength="500" value="{{ old('website', $company->website) }}" placeholder="https://congty.com" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                            @error('website') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium">Email công ty</label>
                            <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $company->email) }}" placeholder="hr@congty.com" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                            @error('email') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium">Điện thoại công ty</label>
                            <input id="phone" name="phone" type="tel" maxlength="20" value="{{ old('phone', $company->phone) }}" placeholder="024 1234 5678" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                            @error('phone') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_size" class="mb-1.5 block text-sm font-medium">Quy mô nhân sự</label>
                            <input id="company_size" name="company_size" type="text" maxlength="50" value="{{ old('company_size', $company->company_size) }}" placeholder="Ví dụ: 50-100" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                            @error('company_size') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="mb-1.5 block text-sm font-medium">Địa chỉ</label>
                        <input id="address" name="address" type="text" maxlength="500" value="{{ old('address', $company->address) }}" placeholder="Địa chỉ trụ sở công ty" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        @error('address') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tax_code" class="mb-1.5 block text-sm font-medium">Mã số thuế</label>
                        <input id="tax_code" name="tax_code" type="text" maxlength="50" value="{{ old('tax_code', $company->tax_code) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        @error('tax_code') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-wrap gap-3 border-t border-ink/10 pt-5">
                        <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-ink px-5 text-sm font-semibold text-paper transition hover:bg-teal-800">Lưu thông tin</button>
                        <a href="{{ route('recruiter.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-ink/15 px-5 text-sm font-medium text-ink/70 transition hover:border-ink/40 hover:text-ink">Quay lại tin tuyển dụng</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
