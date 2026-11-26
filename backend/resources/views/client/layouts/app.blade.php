<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Việc Ngon</title>
    <meta name="description" content="@yield('description')">
    <meta property="og:title" content="@yield('title') - Việc Ngon">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#f3f8fc">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600|big-shoulders-display:700,800|ibm-plex-mono:500,600&display=swap">
    @vite(['resources/css/app.css', 'resources/js/client-board.js'])
</head>
<body class="bg-board font-client text-paper antialiased">
    <a href="#noi-dung" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-20 focus:rounded focus:bg-flap focus:px-3 focus:py-2 focus:text-ink">Đến nội dung chính</a>

    <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/95 shadow-sm shadow-slate-900/5 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2">
            <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-2 font-signage text-2xl font-extrabold tracking-wide whitespace-nowrap uppercase">
                <span aria-hidden="true" class="grid size-8 place-items-center rounded-full bg-flap text-lg text-ink">V</span>
                Việc Ngon
            </a>
            <nav class="flex min-w-0 items-center gap-1 text-xs font-medium whitespace-nowrap sm:gap-4 sm:text-sm" aria-label="Điều hướng chính">
                <a href="{{ route('home') }}#viec-lam" class="board-link hidden sm:inline-flex">Bảng việc làm</a>
                <a href="{{ route('home') }}#tuyen" class="board-link">Tuyến ngành</a>
                <a href="{{ route('home') }}#nha-tuyen-dung" class="board-link text-flap">Nhà tuyển dụng</a>
                @auth
                    @if (auth()->user()->role === \App\Models\User::ROLE_RECRUITER)
                        <a href="{{ route('recruiter.jobs.index') }}" class="board-link text-flap">Quản lý tin</a>
                        <a href="{{ route('recruiter.applications.index') }}" class="board-link">Ứng viên</a>
                        <a href="{{ route('recruiter.company.edit') }}" class="board-link">Công ty</a>
                    @endif
                    @php
                        $account = auth()->user();
                        $avatar = $account->role === \App\Models\User::ROLE_CANDIDATE ? $account->candidateProfile?->avatar : null;
                    @endphp
                    <details class="group relative ml-1 shrink-0 border-l border-white/15 pl-2 sm:ml-2 sm:pl-4">
                        <summary class="flex min-h-10 cursor-pointer list-none items-center gap-2 rounded px-1 py-1 hover:bg-white/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-flap [&::-webkit-details-marker]:hidden">
                            @if ($avatar)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($avatar) }}" alt="" class="size-8 rounded-full border border-white/20 object-cover">
                            @else
                                <span aria-hidden="true" class="grid size-8 shrink-0 place-items-center rounded-full bg-flap font-semibold text-ink">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                            @endif
                            <span class="max-w-20 truncate text-[11px] text-paper sm:max-w-28 sm:text-sm" title="{{ $account->name }}">{{ $account->name }}</span>
                            <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-3 shrink-0 text-paper/70 transition group-open:rotate-180"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                        </summary>
                        <div class="absolute right-0 top-full z-20 mt-2 hidden min-w-48 rounded-xl border border-slate-200 bg-white p-2 text-ink shadow-xl shadow-slate-900/10 group-open:block">
                            @if ($account->role === \App\Models\User::ROLE_RECRUITER)
                                <a href="{{ route('recruiter.profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Hồ sơ nhà tuyển dụng</a>
                            @elseif ($account->role === \App\Models\User::ROLE_CANDIDATE)
                                <a href="{{ route('candidate.resumes.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Quản lý CV</a>
                                <a href="{{ route('candidate.saved-jobs.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Việc làm đã lưu</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-sky-800 hover:bg-sky-50">Đăng xuất</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="board-link">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-9 items-center rounded bg-flap px-3 font-semibold text-ink transition hover:bg-white">Đăng ký</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="noi-dung">
        @if (session('success'))
            <div role="status" class="border-b border-emerald-900/15 bg-emerald-100 px-4 py-3 text-center text-sm font-medium text-emerald-950">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div role="alert" class="border-b border-rose-900/15 bg-rose-100 px-4 py-3 text-center text-sm font-medium text-rose-950">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white text-sm text-paper/70">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-6">
            <span class="font-signage text-lg font-bold tracking-wide text-paper uppercase">Việc Ngon</span>
            <span>© {{ date('Y') }} Bảng việc làm cho người tìm việc và nơi cần người.</span>
        </div>
    </footer>
</body>
</html>
