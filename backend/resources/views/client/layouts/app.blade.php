<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Ga Việc</title>
    <meta name="description" content="@yield('description')">
    <meta property="og:title" content="@yield('title') - Ga Việc">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#16140f">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600|big-shoulders-display:700,800|ibm-plex-mono:500,600&display=swap">
    @vite(['resources/css/app.css', 'resources/js/client-board.js'])
</head>
<body class="bg-board font-client text-paper antialiased">
    <a href="#noi-dung" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-20 focus:rounded focus:bg-flap focus:px-3 focus:py-2 focus:text-ink">Đến nội dung chính</a>

    <header class="sticky top-0 z-10 border-b border-white/10 bg-board">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2">
            <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-2 font-signage text-2xl font-extrabold tracking-wide whitespace-nowrap uppercase">
                <span aria-hidden="true" class="grid size-8 place-items-center rounded-full bg-flap text-lg text-ink">G</span>
                Ga Việc
            </a>
            <nav class="flex items-center gap-1 text-xs font-medium whitespace-nowrap sm:gap-4 sm:text-sm" aria-label="Điều hướng chính">
                <a href="{{ route('home') }}#viec-lam" class="board-link hidden sm:inline-flex">Bảng việc làm</a>
                <a href="{{ route('home') }}#tuyen" class="board-link">Tuyến ngành</a>
                <a href="{{ route('home') }}#nha-tuyen-dung" class="board-link text-flap">Nhà tuyển dụng</a>
            </nav>
        </div>
    </header>

    <main id="noi-dung">@yield('content')</main>

    <footer class="border-t border-white/10 text-sm text-paper/70">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-6">
            <span class="font-signage text-lg font-bold tracking-wide text-paper uppercase">Ga Việc</span>
            <span>© {{ date('Y') }} Bảng việc làm cho người tìm việc và nơi cần người.</span>
        </div>
    </footer>
</body>
</html>
