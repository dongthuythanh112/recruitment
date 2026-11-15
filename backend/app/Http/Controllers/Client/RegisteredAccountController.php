<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredAccountController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('role') === User::ROLE_RECRUITER
            ? User::ROLE_RECRUITER
            : User::ROLE_CANDIDATE;

        return view('client.auth.register', compact('role'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_CANDIDATE, User::ROLE_RECRUITER])],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'company_name' => [Rule::requiredIf($request->input('role') === User::ROLE_RECRUITER), 'nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'company_name.required' => 'Vui lòng nhập tên công ty.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Mật khẩu xác nhận chưa khớp.',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $data['password'],
                'role' => $data['role'],
                'status' => User::STATUS_ACTIVE,
            ]);

            if ($user->role === User::ROLE_RECRUITER) {
                $company = Company::create([
                    'name' => $data['company_name'],
                    'created_by' => $user->id,
                ]);

                $user->recruiterProfile()->create([
                    'company_id' => $company->id,
                    'position' => $data['position'] ?? null,
                ]);
            } else {
                $user->candidateProfile()->create();
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $destination = $user->role === User::ROLE_RECRUITER
            ? route('recruiter.jobs.index')
            : route('home');

        $message = $user->role === User::ROLE_RECRUITER
            ? 'Tài khoản nhà tuyển dụng đã sẵn sàng. Hãy tạo tin tuyển dụng đầu tiên.'
            : 'Tạo tài khoản thành công. Chào mừng bạn đến với Việc Ngon!';

        return redirect($destination)->with('success', $message);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
