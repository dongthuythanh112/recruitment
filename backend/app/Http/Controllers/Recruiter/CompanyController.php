<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function edit(Request $request): View
    {
        return view('recruiter.company.edit', [
            'company' => $this->company($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $company = $this->company($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'website' => ['nullable', 'url', 'max:500'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'company_size' => ['nullable', 'string', 'max:50'],
            'tax_code' => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($company, $data) {
            if ($company->name !== $data['name']) {
                $data['slug'] = Company::uniqueSlug($data['name'], $company->id);
            }

            $company->update($data);
        });

        return redirect()->route('recruiter.company.edit')->with('success', 'Đã cập nhật thông tin công ty.');
    }

    private function company(Request $request): Company
    {
        $profile = $request->user()->recruiterProfile()->with('company')->first();
        abort_unless($profile?->company, 403, 'Tài khoản chưa được gắn với công ty. Vui lòng liên hệ quản trị viên.');

        return $profile->company;
    }
}
