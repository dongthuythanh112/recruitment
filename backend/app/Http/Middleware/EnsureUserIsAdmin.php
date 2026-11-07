<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Chỉ cho tài khoản admin đang hoạt động vào khu quản trị.
     * Admin bị khóa giữa chừng cũng bị đăng xuất ngay ở request tiếp theo.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isActiveAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->guest(route('admin.login'));
        }

        return $next($request);
    }
}
