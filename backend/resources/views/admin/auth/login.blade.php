<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập quản trị - Việc Ngon</title>
    <meta name="theme-color" content="#f3f8fc">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-board px-4 py-10 font-client text-ink antialiased sm:py-14">
    <main class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-sky-950/10 lg:grid-cols-[0.9fr_1.1fr]">
        <aside class="relative isolate hidden min-h-[34rem] flex-col justify-between overflow-hidden bg-gradient-to-br from-sky-900 via-sky-800 to-cyan-700 p-8 text-white sm:flex lg:p-10">
            <div aria-hidden="true" class="absolute -right-24 -top-20 -z-10 size-80 rounded-full border-[36px] border-white/10"></div>
            <div aria-hidden="true" class="absolute -bottom-32 -left-20 -z-10 size-72 rounded-full border-[30px] border-white/10"></div>
            <a href="{{ route('home') }}" class="inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-sm font-semibold !text-white backdrop-blur-sm transition hover:bg-white/15">
                <span class="grid size-7 place-items-center rounded-full bg-sky-100 font-signage text-lg font-extrabold !text-sky-900">V</span>
                Việc Ngon
            </a>
            <div class="relative">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] !text-sky-100">Khu vực quản trị</p>
                <h1 class="mt-3 font-signage text-5xl leading-[1.02] font-extrabold tracking-wide uppercase !text-white lg:text-6xl">Điều hành<br>tuyển dụng.</h1>
                <p class="mt-5 max-w-sm text-sm leading-6 !text-white/80">Quản lý tin tuyển dụng, tài khoản và hoạt động trên nền tảng Việc Ngon.</p>
            </div>
            <p class="relative border-t border-white/20 pt-5 text-xs !text-white/70">Bảng việc làm dành cho người tìm việc và nhà tuyển dụng.</p>
        </aside>

        <section class="flex items-center p-5 sm:p-8 lg:p-12">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-semibold text-sky-800 hover:text-sky-950 sm:hidden">
                    <span class="grid size-8 place-items-center rounded-full bg-sky-100 font-signage text-lg font-extrabold">V</span> Việc Ngon
                </a>
                <div class="mb-7">
                    <p class="text-sm font-semibold text-sky-800">Chào mừng trở lại</p>
                    <h2 class="mt-1 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Đăng nhập</h2>
                    <p class="mt-2 text-sm text-slate-500">Đăng nhập vào trang quản trị Việc Ngon.</p>
                </div>

                @if ($errors->any())
                    <div role="alert" class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <p class="font-semibold">Không thể đăng nhập.</p>
                        <ul class="mt-1 list-inside list-disc space-y-0.5">
                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email <span class="text-sky-700">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" autofocus required placeholder="admin@example.com" class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10">
                        @error('email')<p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">Mật khẩu <span class="text-sky-700">*</span></label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Nhập mật khẩu" class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-600/10">
                        @error('password')<p class="mt-1.5 text-xs text-rose-700">{{ $message }}</p>@enderror
                    </div>

                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 accent-sky-700 focus:ring-sky-600">
                        Ghi nhớ đăng nhập
                    </label>

                    <button type="submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-sky-800 px-5 text-sm font-semibold !text-white shadow-sm transition hover:bg-sky-900 focus:outline-none focus:ring-4 focus:ring-sky-700/20">
                        Đăng nhập quản trị
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-[18px]" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7" /></svg>
                    </button>
                </form>

                <p class="mt-7 border-t border-slate-100 pt-5 text-center text-xs text-slate-500">
                    <a href="{{ route('home') }}" class="font-semibold text-sky-800 underline underline-offset-4 hover:text-sky-950">Quay về trang chủ</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
