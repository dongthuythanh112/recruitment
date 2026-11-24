<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('recruiter.profile.edit', [
            'account' => $request->user()->load('recruiterProfile'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $account = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$account->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        $account->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
        $account->recruiterProfile()->updateOrCreate([], [
            'position' => $data['position'] ?? null,
        ]);

        return redirect()->route('recruiter.profile.edit')->with('success', 'Đã cập nhật hồ sơ nhà tuyển dụng.');
    }
}
