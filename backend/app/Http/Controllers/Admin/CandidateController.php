<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(Request $request): View
    {
        $candidates = CandidateProfile::with('user')
            ->withCount('applications')
            ->whereHas('user', function ($query) use ($request) {
                $query->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
                    ->when($request->query('q'), fn ($q, $keyword) => $q->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    }));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.candidates.index', compact('candidates'));
    }

    public function show(CandidateProfile $candidate): View
    {
        $candidate->load([
            'user',
            'resumes',
            'educations' => fn ($query) => $query->orderByDesc('start_date'),
            'experiences' => fn ($query) => $query->orderByDesc('start_date'),
            'skills',
            'applications' => fn ($query) => $query->with('job.company')->latest('applied_at'),
        ]);

        return view('admin.candidates.show', compact('candidate'));
    }

    public function create(): View
    {
        return view('admin.candidates.create', ['candidate' => new CandidateProfile]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$account, $profile] = $this->validated($request);

        $candidate = DB::transaction(function () use ($account, $profile) {
            $user = User::create([...$account, 'role' => User::ROLE_CANDIDATE]);

            return $user->candidateProfile()->create($profile);
        });

        return redirect()->route('admin.candidates.show', $candidate)->with('success', 'Đã thêm ứng viên.');
    }

    public function edit(CandidateProfile $candidate): View
    {
        $candidate->load('user');

        return view('admin.candidates.edit', compact('candidate'));
    }

    public function update(Request $request, CandidateProfile $candidate): RedirectResponse
    {
        [$account, $profile] = $this->validated($request, $candidate->user);

        DB::transaction(function () use ($candidate, $account, $profile) {
            $candidate->user->update($account);
            $candidate->update($profile);
        });

        return redirect()->route('admin.candidates.show', $candidate)->with('success', 'Đã cập nhật ứng viên.');
    }

    public function toggleStatus(CandidateProfile $candidate): RedirectResponse
    {
        $user = $candidate->user;
        $user->update([
            'status' => $user->status === User::STATUS_ACTIVE ? User::STATUS_LOCKED : User::STATUS_ACTIVE,
        ]);

        return back()->with('success', $user->status === User::STATUS_LOCKED ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }

    public function destroy(CandidateProfile $candidate): RedirectResponse
    {
        if ($candidate->applications()->exists()) {
            return back()->with('error', 'Ứng viên đã có hồ sơ ứng tuyển nên không thể xóa. Hãy khóa tài khoản thay vì xóa.');
        }

        // Hồ sơ, CV, học vấn, kinh nghiệm... bị xóa theo nhờ khóa ngoại cascade.
        $candidate->user->delete();

        return redirect()->route('admin.candidates.index')->with('success', 'Đã xóa ứng viên.');
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>} thông tin tài khoản và thông tin hồ sơ
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => $user ? ['nullable', 'confirmed', Password::min(8)] : ['prohibited'],
            'status' => ['required', Rule::in(array_keys(User::STATUS_LABELS))],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(array_keys(CandidateProfile::GENDER_LABELS))],
            'address' => ['nullable', 'string', 'max:500'],
            'summary' => ['nullable', 'string'],
            'desired_position' => ['nullable', 'string', 'max:255'],
            'desired_salary_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999999'],
            'desired_salary_max' => ['nullable', 'numeric', 'min:0', 'max:9999999999999', Rule::when($request->filled('desired_salary_min'), 'gte:desired_salary_min')],
            'years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:99'],
        ]);

        $account = collect($data)->only(['name', 'email', 'phone', 'status', 'password'])
            ->filter(fn ($value, $key) => $key !== 'password' || filled($value))
            ->all();
        if (! $user) {
            $account['password'] = '12345678';
        }

        return [$account, collect($data)->except(['name', 'email', 'phone', 'status', 'password'])->all()];
    }
}
