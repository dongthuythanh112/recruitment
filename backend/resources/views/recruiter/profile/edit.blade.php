@extends('client.layouts.app')

@section('title', 'Hồ sơ nhà tuyển dụng')
@section('description', 'Cập nhật thông tin liên hệ và chức danh của nhà tuyển dụng.')

@section('content')
    <section class="bg-board px-4 pb-14 text-paper">
        <div class="mx-auto max-w-3xl">
            <div class="border-b border-white/15 py-8 sm:py-10">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-flap">Tài khoản nhà tuyển dụng</p>
                <h1 class="mt-2 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Hồ sơ cá nhân</h1>
                <p class="mt-2 text-sm text-paper/65">Cập nhật thông tin người liên hệ dùng để quản lý tuyển dụng.</p>
            </div>

            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-xl border border-rose-300/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-100">
                    <p class="font-semibold">Vui lòng kiểm tra lại thông tin.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('recruiter.profile.update') }}" class="mt-6 space-y-5 rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4 border-b border-ink/10 pb-5">
                    <span aria-hidden="true" class="grid size-14 shrink-0 place-items-center rounded-full bg-flap text-xl font-bold text-ink">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                    <div class="min-w-0">
                        <p class="truncate font-semibold">{{ $account->name }}</p>
                        <p class="text-sm text-ink/55">Nhà tuyển dụng</p>
                    </div>
                </div>

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium">Họ và tên <span class="text-signal">*</span></label>
                    <input id="name" name="name" type="text" maxlength="255" value="{{ old('name', $account->name) }}" required autocomplete="name" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                    @error('name')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-signal">*</span></label>
                        <input id="email" name="email" type="email" maxlength="255" value="{{ old('email', $account->email) }}" required autocomplete="email" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        @error('email')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium">Số điện thoại</label>
                        <input id="phone" name="phone" type="tel" maxlength="20" value="{{ old('phone', $account->phone) }}" autocomplete="tel" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                        @error('phone')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="position" class="mb-1.5 block text-sm font-medium">Chức danh</label>
                    <input id="position" name="position" type="text" maxlength="255" value="{{ old('position', $account->recruiterProfile?->position) }}" placeholder="Ví dụ: Chuyên viên tuyển dụng" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                    @error('position')<p class="mt-1 text-xs text-rose-700">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap gap-3 border-t border-ink/10 pt-5">
                    <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-ink px-5 text-sm font-semibold text-paper transition hover:bg-teal-800">Lưu hồ sơ</button>
                    <a href="{{ route('recruiter.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-ink/15 px-5 text-sm font-medium text-ink/70 transition hover:border-ink/40 hover:text-ink">Quay lại quản lý tin</a>
                </div>
            </form>
        </div>
    </section>
@endsection
