<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập - Quản trị tuyển dụng</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-stone-100 px-4 font-admin text-stone-900 antialiased">
    <main class="w-full max-w-sm rounded-lg border border-stone-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-semibold tracking-tight">Đăng nhập quản trị</h1>
        <p class="mt-1 mb-6 text-sm text-stone-500">Hệ thống hỗ trợ tuyển dụng và tìm kiếm việc làm</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="email" label="Email" type="email" autocomplete="username" autofocus required />
            <x-admin.input name="password" label="Mật khẩu" type="password" autocomplete="current-password" required />
            <label class="flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="remember" value="1" class="rounded border-stone-300 accent-teal-700">
                Ghi nhớ đăng nhập
            </label>
            <x-admin.button class="w-full">Đăng nhập</x-admin.button>
        </form>
    </main>
</body>
</html>
