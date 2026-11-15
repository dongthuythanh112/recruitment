@extends('client.layouts.app')

@section('title', 'Đăng nhập')
@section('description', 'Đăng nhập tài khoản ứng viên hoặc nhà tuyển dụng trên Việc Ngon.')

@section('content')
    @php($role = old('role', $role))

    <section class="relative isolate overflow-hidden bg-board px-4 py-10 text-paper sm:py-14 lg:py-20">
        <div aria-hidden="true" class="pointer-events-none absolute -top-36 right-[-5rem] -z-10 size-[28rem] rounded-full border border-flap/15 sm:right-[-2rem]"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -top-20 right-8 -z-10 size-[20rem] rounded-full border border-flap/10"></div>

        <div class="mx-auto grid max-w-6xl overflow-hidden rounded-2xl border border-white/10 bg-paper text-ink shadow-2xl shadow-black/30 lg:grid-cols-[0.85fr_1.15fr]">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-flap p-8 text-ink sm:flex lg:p-10">
                <div aria-hidden="true" class="absolute -right-20 -bottom-24 size-72 rounded-full border-[28px] border-ink/5"></div>
                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-ink/15 bg-ink/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em]">
                        <span class="size-1.5 rounded-full bg-signal"></span>
                        Việc Ngon
                    </span>
                    <h1 class="mt-8 font-signage text-5xl leading-[1.03] font-extrabold tracking-wide uppercase lg:text-6xl">
                        Ga tiếp theo<br>là của bạn.
                    </h1>
                    <p class="mt-5 max-w-sm text-sm leading-6 text-ink/75">
                        Đăng nhập để tiếp tục hành trình tìm kiếm cơ hội hoặc đưa cơ hội nghề nghiệp đến gần ứng viên hơn.
                    </p>
                </div>

                <div class="relative mt-10 border-t border-ink/15 pt-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-ink/60">Mỗi cơ hội bắt đầu từ một kết nối.</p>
                    <p class="mt-1 text-sm text-ink/75">Bảng việc làm dành cho người tìm việc và nhà tuyển dụng.</p>
                </div>
            </aside>

            <div class="p-5 sm:p-8 lg:p-10">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-signal">Chào mừng trở lại</p>
                    <h2 class="mt-1 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Đăng nhập</h2>
                    <p class="mt-2 text-sm text-ink/65">Chọn loại tài khoản để tiếp tục.</p>
                </div>

                <div class="grid grid-cols-2 rounded-xl bg-ink/5 p-1" aria-label="Chọn loại tài khoản">
                    <a href="{{ route('login', ['role' => 'candidate']) }}" @class([
                        'flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold transition',
                        'bg-white text-ink shadow-sm' => $role === 'candidate',
                        'text-ink/60 hover:text-ink' => $role !== 'candidate',
                    ]) @if ($role === 'candidate') aria-current="page" @endif>
                        Ứng viên
                    </a>
                    <a href="{{ route('login', ['role' => 'recruiter']) }}" @class([
                        'flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold transition',
                        'bg-white text-ink shadow-sm' => $role === 'recruiter',
                        'text-ink/60 hover:text-ink' => $role !== 'recruiter',
                    ]) @if ($role === 'recruiter') aria-current="page" @endif>
                        Nhà tuyển dụng
                    </a>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    @if ($errors->any())
                        <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <p class="font-semibold">Không thể đăng nhập:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-signal">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="ban@email.com">
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium">Mật khẩu <span class="text-signal">*</span></label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Nhập mật khẩu">
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-sm text-ink/70">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-ink/25 accent-teal-700">
                        Ghi nhớ đăng nhập
                    </label>

                    <button type="submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-ink px-5 text-sm font-semibold text-paper transition hover:bg-signal focus:outline-none focus:ring-4 focus:ring-signal/20">
                        {{ $role === 'recruiter' ? 'Đăng nhập nhà tuyển dụng' : 'Đăng nhập ứng viên' }}
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="block size-[18px] shrink-0" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7" /></svg>
                    </button>
                    <p class="text-center text-xs leading-5 text-ink/55">Chưa có tài khoản?
                        <a href="{{ route('register', ['role' => $role]) }}" class="font-semibold text-signal underline underline-offset-4 hover:text-ink">Đăng ký ngay</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection
