<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Quản trị tuyển dụng</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-stone-100 font-admin text-stone-900 antialiased">
@php
    $menu = [
        ['label' => 'Thống kê', 'route' => 'admin.statistics.index', 'active' => 'admin.statistics.*'],
        ['label' => 'Ứng viên', 'route' => 'admin.candidates.index', 'active' => 'admin.candidates.*'],
        ['label' => 'Nhà tuyển dụng', 'route' => 'admin.recruiters.index', 'active' => 'admin.recruiters.*'],
        ['label' => 'Tin tuyển dụng', 'route' => 'admin.jobs.index', 'active' => 'admin.jobs.*'],
        ['label' => 'Ngành nghề', 'route' => 'admin.industries.index', 'active' => 'admin.industries.*'],
    ];
@endphp
<div class="min-h-screen lg:flex">
    <aside class="bg-stone-900 text-stone-200 lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-60 lg:shrink-0 lg:flex-col">
        <div class="px-5 py-4 text-base font-semibold text-white">Quản trị tuyển dụng</div>
        <nav class="flex gap-1 overflow-x-auto px-3 pb-3 lg:flex-1 lg:flex-col lg:overflow-visible">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}" @class([
                    'rounded-md px-3 py-2 text-sm whitespace-nowrap',
                    'bg-teal-700 font-medium text-white' => request()->routeIs($item['active']),
                    'hover:bg-stone-800' => ! request()->routeIs($item['active']),
                ])>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="hidden border-t border-stone-800 px-5 py-4 lg:block">
            <p class="truncate text-sm text-white">{{ auth()->user()->name }}</p>
            <p class="truncate text-xs text-stone-400">{{ auth()->user()->email }}</p>
            <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="cursor-pointer text-sm text-stone-300 underline hover:text-white">Đăng xuất</button>
            </form>
        </div>
    </aside>

    <main class="min-w-0 flex-1 px-4 py-6 lg:px-8">
        @php
            $account = auth()->user();
            $avatar = $account->candidateProfile?->avatar;
        @endphp
        <div class="mb-5 flex justify-end">
            <div class="flex items-center gap-3">
                @if ($avatar)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($avatar) }}" alt="Ảnh đại diện của {{ $account->name }}" class="size-9 rounded-full border border-stone-300 object-cover">
                @else
                    <span aria-hidden="true" class="grid size-9 place-items-center rounded-full bg-teal-700 text-sm font-semibold text-white">{{ mb_strtoupper(mb_substr($account->name, 0, 1)) }}</span>
                @endif
                <div class="min-w-0">
                    <p class="max-w-48 truncate text-sm font-semibold text-stone-900" title="{{ $account->name }}">{{ $account->name }}</p>
                    <p class="max-w-48 truncate text-xs text-stone-500">{{ $account->email }}</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="cursor-pointer text-sm text-stone-600 underline hover:text-stone-900">Đăng xuất</button>
                </form>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-semibold tracking-tight">@yield('title')</h1>
            <div class="flex flex-wrap gap-2">@yield('actions')</div>
        </div>

        @include('admin.partials.flash')
        @yield('content')
    </main>
</div>
</body>
</html>
