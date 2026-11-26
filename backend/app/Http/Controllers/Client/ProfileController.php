<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $candidate = $this->candidate($request)->load(['educations' => fn ($query) => $query->orderByDesc('start_date'), 'experiences' => fn ($query) => $query->orderByDesc('start_date'), 'skills']);

        return view('client.profile.edit', [
            'candidate' => $candidate,
            'account' => $request->user(),
            'skillsText' => $candidate->skills->pluck('name')->implode(', '),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $account = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($account->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(array_keys(CandidateProfile::GENDER_LABELS))],
            'address' => ['nullable', 'string', 'max:500'],
            'summary' => ['nullable', 'string', 'max:5000'],
            'desired_position' => ['nullable', 'string', 'max:255'],
            'desired_salary_min' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'desired_salary_max' => ['nullable', 'numeric', 'min:0', 'max:999999999999', 'gte:desired_salary_min'],
            'years_of_experience' => ['nullable', 'numeric', 'min:0', 'max:80'],
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'date_of_birth.before' => 'Ngày sinh phải nhỏ hơn ngày hôm nay.',
            'desired_salary_max.gte' => 'Mức lương tối đa phải lớn hơn hoặc bằng mức lương tối thiểu.',
            'avatar.image' => 'Ảnh đại diện phải là tệp hình ảnh hợp lệ.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2 MB.',
        ]);

        $newAvatar = $request->file('avatar')?->store('candidate-avatars/'.$candidate->id, 'public');
        $oldAvatar = $candidate->avatar;

        try {
            DB::transaction(function () use ($account, $candidate, $data, $newAvatar): void {
                $account->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                ]);

                $profileFields = [
                    'date_of_birth', 'gender', 'address', 'summary', 'desired_position',
                    'desired_salary_min', 'desired_salary_max', 'years_of_experience',
                ];
                $profileData = array_intersect_key($data, array_flip($profileFields));
                if ($newAvatar) {
                    $profileData['avatar'] = $newAvatar;
                }

                $candidate->update($profileData);
            });
        } catch (\Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')->delete($newAvatar);
            }

            throw $exception;
        }

        if ($newAvatar && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return back()->with('success', 'Đã cập nhật hồ sơ cá nhân.');
    }

    public function updateSkills(Request $request): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $data = $request->validate([
            'skills_text' => ['nullable', 'string', 'max:3000'],
        ]);

        $names = collect(preg_split('/[,\n]+/', $data['skills_text'] ?? ''))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->map(fn (string $name) => Str::limit($name, 100, ''))
            ->unique(fn (string $name) => mb_strtolower($name))
            ->take(30)
            ->values();

        DB::transaction(function () use ($candidate, $names): void {
            $existing = $candidate->skills()->get()->keyBy(fn (Skill $skill) => mb_strtolower($skill->name));
            $sync = [];

            foreach ($names as $name) {
                $skill = Skill::firstOrCreate(['name' => $name]);
                $oldSkill = $existing->get(mb_strtolower($name));
                $sync[$skill->id] = [
                    'level' => $oldSkill?->pivot->level,
                    'created_at' => $oldSkill?->pivot->created_at ?? now(),
                ];
            }

            $candidate->skills()->sync($sync);
        });

        return back()->with('success', 'Đã cập nhật kỹ năng.');
    }

    public function storeEducation(Request $request): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $candidate->educations()->create($this->validatedEducation($request));

        return back()->with('success', 'Đã thêm học vấn.');
    }

    public function updateEducation(Request $request, Education $education): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $education->candidate_id);
        $education->update($this->validatedEducation($request));

        return back()->with('success', 'Đã cập nhật học vấn.');
    }

    public function destroyEducation(Request $request, Education $education): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $education->candidate_id);
        $education->delete();

        return back()->with('success', 'Đã xóa mục học vấn.');
    }

    public function storeExperience(Request $request): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $candidate->experiences()->create($this->validatedExperience($request));

        return back()->with('success', 'Đã thêm kinh nghiệm làm việc.');
    }

    public function updateExperience(Request $request, Experience $experience): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $experience->candidate_id);
        $experience->update($this->validatedExperience($request));

        return back()->with('success', 'Đã cập nhật kinh nghiệm làm việc.');
    }

    public function destroyExperience(Request $request, Experience $experience): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $this->assertOwned($candidate, $experience->candidate_id);
        $experience->delete();

        return back()->with('success', 'Đã xóa mục kinh nghiệm.');
    }

    private function validatedEducation(Request $request): array
    {
        return $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'degree' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required_with:end_date', 'nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);
    }

    private function validatedExperience(Request $request): array
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required_with:end_date', 'nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'before_or_equal:today'],
            'is_current' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);

        $data['is_current'] = (bool) ($data['is_current'] ?? false);
        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        return $data;
    }

    private function candidate(Request $request): CandidateProfile
    {
        abort_unless(
            $request->user()?->role === User::ROLE_CANDIDATE
                && $request->user()?->status === User::STATUS_ACTIVE,
            403,
            'Chức năng này chỉ dành cho ứng viên đang hoạt động.',
        );

        return $request->user()->candidateProfile()->firstOrCreate();
    }

    private function assertOwned(CandidateProfile $candidate, int $candidateId): void
    {
        abort_unless((int) $candidate->id === $candidateId, 404);
    }
}
