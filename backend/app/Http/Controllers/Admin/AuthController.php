<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attempted = Auth::attempt(
            [...$credentials, 'role' => User::ROLE_ADMIN, 'status' => User::STATUS_ACTIVE],
            $request->boolean('remember'),
        );

        if (! $attempted) {
            // Cùng một thông báo cho mọi trường hợp để không lộ email nào tồn tại.
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản không có quyền quản trị.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.statistics.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
