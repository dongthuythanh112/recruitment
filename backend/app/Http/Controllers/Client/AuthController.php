<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('role') === User::ROLE_RECRUITER
            ? User::ROLE_RECRUITER
            : User::ROLE_CANDIDATE;

        return view('client.auth.login', compact('role'));
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_CANDIDATE, User::ROLE_RECRUITER])],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $role = $credentials['role'];
        unset($credentials['role']);

        if (! Auth::attempt([
            ...$credentials,
            'role' => $role,
            'status' => User::STATUS_ACTIVE,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản không thuộc nhóm này.',
            ]);
        }

        $request->session()->regenerate();

        $destination = $role === User::ROLE_RECRUITER
            ? route('recruiter.jobs.index')
            : route('home');

        return redirect()->intended($destination);
    }
}
