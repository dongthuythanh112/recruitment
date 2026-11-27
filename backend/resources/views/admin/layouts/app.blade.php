<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Quản trị tuyển dụng</title>
    <meta name="theme-color" content="#f3f8fc">
    @vite(['resources/css/app.css', 'resources/js/client-board.js'])
</head>
<body class="bg-board font-client text-ink antialiased">
@php
    $menu = [
        ['label' => 'Thống kê', 'route' => 'admin.statistics.index', 'active' => 'admin.statistics.*', 'icon' => 'chart'],
        ['label' => 'Thông báo', 'route' => 'admin.notifications.index', 'active' => 'admin.notifications.*', 'icon' => 'bell'],
        ['label' => 'Ứng viên', 'route' => 'admin.candidates.index', 'active' => 'admin.candidates.*', 'icon' => 'users'],
        ['label' => 'Nhà tuyển dụng', 'route' => 'admin.recruiters.index', 'active' => 'admin.recruiters.*', 'icon' => 'briefcase'],
        ['label' => 'Tin tuyển dụng', 'route' => 'admin.jobs.index', 'active' => 'admin.jobs.*', 'icon' => 'document'],
        ['label' => 'Ngành nghề', 'route' => 'admin.industries.index', 'active' => 'admin.industries.*', 'icon' => 'grid'],
    ];
    $account = auth()->user();
    $unreadNotificationsCount = $account->userNotifications()->whereNull('read_at')->count();
@endphp

<div class="min-h-screen lg:flex">
    <aside class="border-b border-slate-200 bg-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-64 lg:shrink-0 lg:flex-col lg:border-r lg:border-b-0">
        <a href="{{ route('admin.statistics.index') }}" class="group flex min-h-[4.5rem] items-center gap-3 border-b border-slate-100 px-4 py-3 sm:px-5">
            <span aria-hidden="true" class="grid size-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-sky-700 to-cyan-500 font-signage text-2xl font-extrabold !text-white shadow-sm shadow-sky-800/20">V</span>
            <span class="min-w-0">
                <span class="block truncate text-sm font-bold tracking-tight text-slate-900">Việc Ngon</span>
                <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">Quản trị hệ thống</span>
            </span>
        </a>

        <nav class="flex gap-1.5 overflow-x-auto px-3 py-3 lg:flex-1 lg:flex-col lg:gap-1 lg:overflow-y-auto lg:overflow-x-visible lg:px-3 lg:py-6" aria-label="Điều hướng quản trị">
            <p class="hidden px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400 lg:block">Không gian làm việc</p>
            @foreach ($menu as $item)
                @php($isActive = request()->routeIs($item['active']))
                <a href="{{ route($item['route']) }}" @class([
                    'group relative flex min-h-11 shrink-0 items-center gap-3 rounded-xl px-2.5 py-2 text-sm font-medium transition sm:px-3 lg:w-full',
                    'bg-sky-50 text-sky-900 ring-1 ring-inset ring-sky-100' => $isActive,
                    'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => ! $isActive,
                ]) @if ($isActive) aria-current="page" @endif>
                    <span @class([
                        'grid size-8 shrink-0 place-items-center rounded-lg transition',
                        'bg-white text-sky-800 shadow-sm ring-1 ring-sky-100' => $isActive,
                        'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:text-sky-700' => ! $isActive,
                    ])>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-[18px]" aria-hidden="true">
                            @switch($item['icon'])
                                @case('chart')<path d="M4 19V5m0 14h16M8 15v-4m4 4V7m4 8v-6m4 6V4" />@break
                                @case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2m6-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm10 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />@break
                                @case('briefcase')<rect x="3" y="7" width="18" height="13" rx="2" /><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m-13 5h18m-11 0v2h4v-2" />@break
                                @case('document')<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Zm0 0v6h6M8 13h8m-8 4h8" />@break
                                @case('grid')<rect x="3" y="3" width="8" height="8" rx="1.5" /><rect x="13" y="3" width="8" height="8" rx="1.5" /><rect x="3" y="13" width="8" height="8" rx="1.5" /><rect x="13" y="13" width="8" height="8" rx="1.5" />@break
                                @case('bell')<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 13h4" />@break
                            @endswitch
                        </svg>
                    </span>
                    <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                    @if ($item['icon'] === 'bell')
                        <span @class([
                            'ml-auto min-w-5 rounded-full px-2 py-0.5 text-center text-[10px] font-extrabold tabular-nums !text-white shadow-sm',
                            'bg-sky-900 text-white ring-2 ring-sky-100' => $unreadNotificationsCount > 0,
                            'bg-slate-600' => $unreadNotificationsCount === 0,
                        ]) aria-label="{{ $unreadNotificationsCount }} thông báo chưa đọc">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                    @endif
                    @if ($isActive)
                        @unless ($item['icon'] === 'bell')
                            <span class="ml-auto hidden h-5 w-1 rounded-full bg-sky-600 lg:block" aria-hidden="true"></span>
                        @endunless
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="hidden p-3 lg:block">
            <div class="flex items-center gap-3 rounded-2xl bg-gradient-to-br from-sky-50 to-cyan-50 p-3 ring-1 ring-inset ring-sky-100">
                <span aria-hidden="true" class="grid size-10 shrink-0 place-items-center rounded-full bg-sky-700 text-sm font-bold text-white">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-xs font-semibold text-slate-900">{{ $account->name }}</span>
                    <span class="mt-0.5 block truncate text-[11px] text-slate-500">Quản trị viên</span>
                </span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" aria-label="Đăng xuất" title="Đăng xuất" class="grid size-8 place-items-center rounded-lg text-slate-500 transition hover:bg-white hover:text-sky-800">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-[18px]" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="min-w-0 flex-1 px-4 py-5 sm:px-6 lg:px-8 lg:py-7">
        <div class="mb-6 flex justify-end">
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                <span aria-hidden="true" class="grid size-9 place-items-center rounded-full bg-sky-100 text-sm font-semibold text-sky-900">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="max-w-48 truncate text-sm font-semibold text-slate-900" title="{{ $account->name }}">{{ $account->name }}</p>
                    <p class="max-w-48 truncate text-xs text-slate-500">{{ $account->email }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">
                    @csrf
                    <button type="submit" class="cursor-pointer text-xs font-semibold text-sky-800 underline underline-offset-4">Thoát</button>
                </form>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 pb-4">
            <h1 class="font-signage text-3xl font-extrabold tracking-wide text-slate-900 uppercase sm:text-4xl">@yield('title')</h1>
            <div class="flex flex-wrap gap-2">@yield('actions')</div>
        </div>

        @include('admin.partials.flash')
        @yield('content')
    </main>
</div>
</body>
</html>
