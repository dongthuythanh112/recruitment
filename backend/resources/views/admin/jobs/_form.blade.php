@php($selectedSkills = array_map('intval', old('skills', $job->exists ? $job->skills->pluck('id')->all() : [])))
<div class="space-y-6">
    <section class="rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold">Thông tin chung</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2"><x-admin.input name="title" label="Tiêu đề" :value="$job->title" required /></div>
            <x-admin.select name="company_id" label="Công ty" :options="$companies" :value="$job->company_id" placeholder="Chọn công ty" required />
            <x-admin.select name="industry_id" label="Ngành nghề" :options="$industries" :value="$job->industry_id" placeholder="Chọn ngành nghề" required />
            <x-admin.select name="employment_type" label="Hình thức làm việc" :options="\App\Models\Job::EMPLOYMENT_TYPE_LABELS" :value="$job->employment_type" placeholder="Chưa chọn" />
            <x-admin.select name="workplace_type" label="Nơi làm việc" :options="\App\Models\Job::WORKPLACE_TYPE_LABELS" :value="$job->workplace_type" placeholder="Chưa chọn" />
            <div class="sm:col-span-2"><x-admin.input name="location" label="Địa điểm" :value="$job->location" /></div>
            <x-admin.input name="quantity" label="Số lượng tuyển" type="number" min="1" :value="$job->quantity ?? 1" required />
            <x-admin.input name="experience_years" label="Số năm kinh nghiệm" type="number" step="0.5" min="0" max="99" :value="$job->experience_years" />
            <x-admin.input name="salary_min" label="Lương tối thiểu" type="number" min="0" :value="$job->salary_min" />
            <x-admin.input name="salary_max" label="Lương tối đa" type="number" min="0" :value="$job->salary_max" />
            <x-admin.input name="salary_currency" label="Đơn vị tiền" :value="$job->salary_currency ?? 'VND'" />
            <x-admin.input name="deadline" label="Hạn nộp" type="date" :value="$job->deadline?->format('Y-m-d')" />
        </div>
    </section>

    <section class="space-y-4 rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="text-sm font-semibold">Nội dung</h2>
        <x-admin.textarea name="description" label="Mô tả công việc" :value="$job->description" rows="6" required />
        <x-admin.textarea name="requirements" label="Yêu cầu" :value="$job->requirements" />
        <x-admin.textarea name="benefits" label="Quyền lợi" :value="$job->benefits" />
    </section>

    @if ($job->exists)
        <fieldset class="rounded-lg border border-slate-200 bg-white p-5">
            <legend class="px-1 text-sm font-semibold">Kỹ năng yêu cầu</legend>
            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm">
                @forelse ($skills as $skillId => $skillName)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="skills[]" value="{{ $skillId }}" class="accent-sky-700" @checked(in_array($skillId, $selectedSkills, true))>
                        {{ $skillName }}
                    </label>
                @empty
                    <p class="text-slate-500">Chưa có kỹ năng trong danh mục.</p>
                @endforelse
            </div>
            @error('skills')
                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </fieldset>
    @endif

    <div class="flex gap-2">
        <x-admin.button>Lưu</x-admin.button>
        <x-admin.button variant="secondary" :href="$job->exists ? route('admin.jobs.show', $job) : route('admin.jobs.index')">Hủy</x-admin.button>
    </div>
</div>
