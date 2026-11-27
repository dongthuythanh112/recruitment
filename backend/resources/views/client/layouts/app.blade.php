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

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm shadow-slate-900/5 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2">
            <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-2 font-signage text-2xl font-extrabold tracking-wide whitespace-nowrap uppercase">
                <span aria-hidden="true" class="grid size-8 place-items-center rounded-full bg-flap text-lg text-ink">V</span>
                Việc Ngon
            </a>
            <nav class="flex min-w-0 items-center gap-1 text-xs font-medium whitespace-nowrap sm:gap-4 sm:text-sm" aria-label="Điều hướng chính">
                @auth
                    @if (auth()->user()->role === \App\Models\User::ROLE_RECRUITER)
                        <a href="{{ route('home') }}#viec-lam" @if (request()->routeIs('home')) aria-current="page" @endif @class(['board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950', 'bg-sky-50 !text-sky-950' => request()->routeIs('home')])>Bảng việc làm</a>
                        <a href="{{ route('recruiter.jobs.index') }}" @if (request()->routeIs('recruiter.jobs.*')) aria-current="page" @endif @class(['board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950', 'bg-sky-50 !text-sky-950' => request()->routeIs('recruiter.jobs.*')])>Quản lý tin</a>
                        <a href="{{ route('recruiter.applications.index') }}" @if (request()->routeIs('recruiter.applications.*')) aria-current="page" @endif @class(['board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950', 'bg-sky-50 !text-sky-950' => request()->routeIs('recruiter.applications.*')])>Ứng viên</a>
                        <a href="{{ route('recruiter.candidates.index') }}" @if (request()->routeIs('recruiter.candidates.*')) aria-current="page" @endif @class(['board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950', 'bg-sky-50 !text-sky-950' => request()->routeIs('recruiter.candidates.*')])>Gợi ý ứng viên</a>
                        <a href="{{ route('recruiter.company.edit') }}" @if (request()->routeIs('recruiter.company.*')) aria-current="page" @endif @class(['board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950', 'bg-sky-50 !text-sky-950' => request()->routeIs('recruiter.company.*')])>Công ty</a>
                    @else
                        <a href="{{ route('home') }}#viec-lam" class="board-link hidden rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950 sm:inline-flex">Bảng việc làm</a>
                        <a href="{{ route('home') }}#tuyen" class="board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950">Tuyến ngành</a>
                        <a href="{{ route('home') }}#nha-tuyen-dung" class="board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950">Nhà tuyển dụng</a>
                    @endif
                    @php
                        $account = auth()->user();
                        $avatar = $account->role === \App\Models\User::ROLE_CANDIDATE ? $account->candidateProfile?->avatar : null;
                        $isCandidateAccount = $account->role === \App\Models\User::ROLE_CANDIDATE;
                        $hasNotificationCenter = in_array($account->role, [\App\Models\User::ROLE_CANDIDATE, \App\Models\User::ROLE_RECRUITER], true);
                        $notificationPrefix = $isCandidateAccount ? 'candidate' : 'recruiter';
                        $accountNotifications = $hasNotificationCenter
                            ? $account->userNotifications()->where('created_at', '>=', now()->subMonths(3))->latest('created_at')->get()
                            : collect();
                        $unreadNotificationsCount = $hasNotificationCenter
                            ? $account->userNotifications()->where('created_at', '>=', now()->subMonths(3))->whereNull('read_at')->count()
                            : 0;
                    @endphp
                    @if ($account->role === \App\Models\User::ROLE_CANDIDATE)
                        <a href="{{ route('candidate.recommendations.index') }}" @if (request()->routeIs('candidate.recommendations.*')) aria-current="page" @endif class="board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950 {{ request()->routeIs('candidate.recommendations.*') ? 'bg-sky-50 !text-sky-950' : '' }}">Gợi ý việc làm</a>
                    @endif
                    @if ($hasNotificationCenter)
                        <details class="group/notifications relative shrink-0">
                            <summary aria-label="Thông báo{{ $unreadNotificationsCount ? ', '.$unreadNotificationsCount.' chưa đọc' : '' }}" title="Thông báo" class="relative grid size-11 cursor-pointer list-none place-items-center rounded-xl text-sky-800 transition hover:bg-sky-50 hover:text-sky-950 group-open/notifications:bg-sky-50 group-open/notifications:text-sky-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 [&::-webkit-details-marker]:hidden">
                                <span class="relative grid size-5 place-items-center transition group-open/notifications:scale-105">
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M10 21h4" /></svg>
                                    <span class="absolute -right-1 -top-3 z-10 min-w-3 text-center text-[10px] font-black leading-none text-red-600 drop-shadow-[0_1px_1px_rgba(255,255,255,0.95)]" style="color:#dc2626">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                                </span>
                            </summary>
                            <div class="absolute right-0 top-full z-30 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white text-left text-slate-900 shadow-xl shadow-slate-900/15">
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
                                    <div>
                                        <h2 class="text-sm font-bold">Thông báo</h2>
                                        <p class="mt-0.5 text-xs text-slate-500">3 tháng gần nhất · {{ $unreadNotificationsCount }} chưa đọc</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route($notificationPrefix.'.notifications.index') }}" class="whitespace-nowrap rounded-lg px-2 py-1.5 text-[11px] font-bold text-sky-800 hover:bg-sky-50">Xem tất cả</a>
                                        @if ($unreadNotificationsCount > 0)
                                            <form method="POST" action="{{ route($notificationPrefix.'.notifications.read-all') }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="whitespace-nowrap rounded-lg px-2 py-1.5 text-[11px] font-bold text-sky-800 hover:bg-sky-50">Đánh dấu đã xem</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="max-h-[min(65vh,28rem)] overflow-y-auto overscroll-contain">
                                    @forelse ($accountNotifications as $notification)
                                        <div class="flex gap-3 border-b border-slate-100 px-3 py-3 last:border-0 {{ $notification->read_at ? '' : 'bg-sky-50/80' }}">
                                            <span class="mt-0.5 grid size-8 shrink-0 place-items-center rounded-full {{ $notification->read_at ? 'bg-slate-100 text-slate-500' : 'bg-sky-100 text-sky-800' }}">
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-4"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <a href="{{ route($notificationPrefix.'.notifications.open', $notification) }}" class="block rounded-md focus-visible:outline-2 focus-visible:outline-sky-600">
                                                    <span class="flex items-start gap-2 text-xs font-bold leading-5 text-slate-900">
                                                        <span class="min-w-0 flex-1">{{ $notification->title }}</span>
                                                        @unless ($notification->read_at)<span class="mt-1 size-2 shrink-0 rounded-full bg-sky-600" aria-label="Chưa đọc"></span>@endunless
                                                    </span>
                                                    <span class="mt-0.5 line-clamp-2 block text-xs leading-5 text-slate-600">{{ $notification->content }}</span>
                                                    <span class="mt-1 block text-[10px] text-slate-400">{{ $notification->created_at?->format('H:i · d/m/Y') }}</span>
                                                </a>
                                                @unless ($notification->read_at)
                                                    <form method="POST" action="{{ route($notificationPrefix.'.notifications.read', $notification) }}" class="mt-1">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="text-[10px] font-semibold text-sky-800 hover:underline">Đánh dấu đã xem</button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <span class="mx-auto grid size-10 place-items-center rounded-full bg-sky-50 text-sky-700">
                                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                                            </span>
                                            <p class="mt-2 text-xs font-semibold text-slate-700">Chưa có thông báo trong 3 tháng gần đây</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </details>
                    @endif
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
                                <a href="{{ route('candidate.profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Hồ sơ cá nhân</a>
                                <a href="{{ route('candidate.resumes.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Quản lý CV</a>
                                <a href="{{ route('candidate.applications.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Việc đã ứng tuyển</a>
                                <a href="{{ route('candidate.saved-jobs.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-ink hover:bg-sky-50">Việc làm đã lưu</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-sky-800 hover:bg-sky-50">Đăng xuất</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('home') }}#viec-lam" class="board-link hidden rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950 sm:inline-flex">Bảng việc làm</a>
                    <a href="{{ route('home') }}#tuyen" class="board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950">Tuyến ngành</a>
                    <a href="{{ route('home') }}#nha-tuyen-dung" class="board-link rounded-lg text-sky-800 transition-colors hover:bg-sky-50 hover:text-sky-950">Nhà tuyển dụng</a>
                    <a href="{{ route('login') }}" class="board-link">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-9 items-center rounded bg-flap px-3 font-semibold text-ink transition hover:bg-white">Đăng ký</a>
                @endauth
            </nav>
        </div>
    </header>

    <main id="noi-dung">
        @if (session('success'))
            <div data-toast class="fixed inset-x-4 top-4 z-50 mx-auto flex max-w-md items-start gap-3 rounded-2xl border border-emerald-200 bg-white p-4 text-left text-emerald-950 shadow-xl shadow-slate-900/15 sm:inset-x-auto sm:right-5" role="status" aria-live="polite">
                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700" aria-hidden="true">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.312a1 1 0 0 1-1.42.006l-3.75-3.688a1 1 0 1 1 1.403-1.426l3.04 2.987 6.557-6.611a1 1 0 0 1 1.414.006Z" clip-rule="evenodd" /></svg>
                </span>
                <div class="min-w-0 flex-1 pt-1">
                    <p class="text-sm font-semibold">Thành công</p>
                    <p class="mt-0.5 text-sm leading-5 text-slate-600">{{ session('success') }}</p>
                </div>
                <button type="button" data-toast-close aria-label="Đóng thông báo" class="grid size-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 1 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" /></svg>
                </button>
            </div>
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
