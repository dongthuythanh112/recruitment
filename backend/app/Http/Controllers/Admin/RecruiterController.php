<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\RecruiterProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RecruiterController extends Controller
{
    public function index(Request $request): View
    {
        $recruiters = RecruiterProfile::with(['user', 'company'])
            ->withCount('jobs')
            ->whereHas('user', function ($query) use ($request) {
                $query->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
                    ->when($request->query('q'), fn ($q, $keyword) => $q->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    }));
            })
            ->when($request->query('company_id'), fn ($query, $companyId) => $query->where('company_id', $companyId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.recruiters.index', [
            'recruiters' => $recruiters,
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * <<include>> Quản lý tin tuyển dụng: trang chi tiết kèm các tin của nhà tuyển dụng.
     */
    public function show(RecruiterProfile $recruiter): View
    {
        $recruiter->load(['user', 'company']);

        return view('admin.recruiters.show', [
            'recruiter' => $recruiter,
            'jobs' => $recruiter->jobs()->with('industry')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.recruiters.create', [
            'recruiter' => new RecruiterProfile,
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$account, $profile, $newCompany] = $this->validated($request);

        $recruiter = DB::transaction(function () use ($request, $account, $profile, $newCompany) {
            $user = User::create([...$account, 'role' => User::ROLE_RECRUITER]);

            return $user->recruiterProfile()->create($this->withCompany($profile, $newCompany, $request->user()));
        });

        return redirect()->route('admin.recruiters.show', $recruiter)->with('success', 'Đã thêm nhà tuyển dụng.');
    }

    public function edit(RecruiterProfile $recruiter): View
    {
        $recruiter->load('user');

        return view('admin.recruiters.edit', [
            'recruiter' => $recruiter,
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, RecruiterProfile $recruiter): RedirectResponse
    {
        [$account, $profile, $newCompany] = $this->validated($request, $recruiter->user);

        DB::transaction(function () use ($request, $recruiter, $account, $profile, $newCompany) {
            $recruiter->user->update($account);
            $recruiter->update($this->withCompany($profile, $newCompany, $request->user()));
        });

        return redirect()->route('admin.recruiters.show', $recruiter)->with('success', 'Đã cập nhật nhà tuyển dụng.');
    }

    public function toggleStatus(RecruiterProfile $recruiter): RedirectResponse
    {
        $user = $recruiter->user;
        $user->update([
            'status' => $user->status === User::STATUS_ACTIVE ? User::STATUS_LOCKED : User::STATUS_ACTIVE,
        ]);

        return back()->with('success', $user->status === User::STATUS_LOCKED ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }

    public function destroy(RecruiterProfile $recruiter): RedirectResponse
    {
        if ($recruiter->jobs()->exists()) {
            return back()->with('error', 'Nhà tuyển dụng đã có tin tuyển dụng nên không thể xóa. Hãy khóa tài khoản thay vì xóa.');
        }

        $recruiter->user->delete();

        return redirect()->route('admin.recruiters.index')->with('success', 'Đã xóa nhà tuyển dụng.');
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>, 2: ?string} tài khoản, hồ sơ, tên công ty mới
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $user ? ['nullable', 'confirmed', Password::min(8)] : ['prohibited'],
            'status' => ['required', Rule::in(array_keys(User::STATUS_LABELS))],
            'position' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'new_company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $account = collect($data)->only(['name', 'email', 'phone', 'status', 'password'])
            ->filter(fn ($value, $key) => $key !== 'password' || filled($value))
            ->all();
        if (! $user) {
            $account['password'] = '12345678';
        }

        return [
            $account,
            ['position' => $data['position'] ?? null, 'company_id' => $data['company_id'] ?? null],
            $data['new_company_name'] ?? null,
        ];
    }

    /**
     * Nếu nhập tên công ty mới thì tạo công ty và gán cho hồ sơ.
     *
     * @param  array<string, mixed>  $profile
     * @return array<string, mixed>
     */
    private function withCompany(array $profile, ?string $newCompanyName, User $admin): array
    {
        if (filled($newCompanyName)) {
            $profile['company_id'] = Company::create(['name' => $newCompanyName, 'created_by' => $admin->id])->id;
        }

        return $profile;
    }
}
