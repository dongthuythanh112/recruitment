@php($selectedSkills = array_map('intval', old('skills', $job->exists ? $job->skills->pluck('id')->all() : [])))

<div class="space-y-5">
    @if ($job->status === 'approved')
        <div role="status" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Khi lưu thay đổi, tin sẽ chuyển về trạng thái chờ duyệt và tạm thời không hiển thị công khai.
        </div>
    @elseif ($job->status === 'rejected' && $job->rejection_reason)
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
            <p class="font-semibold">Lý do tin bị từ chối</p>
            <p class="mt-1 whitespace-pre-line">{{ $job->rejection_reason }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-rose-300/30 bg-rose-950/50 px-4 py-3 text-sm text-rose-100">
            <p class="font-semibold">Vui lòng kiểm tra lại thông tin trong biểu mẫu.</p>
        </div>
    @endif

    <section class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
        <div class="mb-5">
            <h2 class="text-lg font-semibold">Thông tin chung</h2>
            <p class="mt-1 text-sm text-ink/55">Tin sẽ được lưu nháp trước khi gửi quản trị viên duyệt.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <p class="mb-1.5 text-sm font-medium">Công ty</p>
                <div class="flex min-h-12 items-center rounded-xl border border-ink/10 bg-ink/5 px-3.5 text-sm font-medium">{{ $company->name }}</div>
            </div>

            <div class="sm:col-span-2">
                <label for="title" class="mb-1.5 block text-sm font-medium">Tiêu đề tin <span class="text-signal">*</span></label>
                <input id="title" name="title" type="text" maxlength="255" value="{{ old('title', $job->title) }}" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Ví dụ: Nhân viên kinh doanh">
                @error('title') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="industry_id" class="mb-1.5 block text-sm font-medium">Ngành nghề <span class="text-signal">*</span></label>
                <select id="industry_id" name="industry_id" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                    <option value="">Chọn ngành nghề</option>
                    @foreach ($industries as $industryId => $industryName)
                        <option value="{{ $industryId }}" @selected((string) old('industry_id', $job->industry_id) === (string) $industryId)>{{ $industryName }}</option>
                    @endforeach
                </select>
                @error('industry_id') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="location" class="mb-1.5 block text-sm font-medium">Địa điểm làm việc</label>
                <input id="location" name="location" type="text" maxlength="500" value="{{ old('location', $job->location) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Quận Cầu Giấy, Hà Nội">
                @error('location') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="employment_type" class="mb-1.5 block text-sm font-medium">Hình thức làm việc</label>
                <select id="employment_type" name="employment_type" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                    <option value="">Chọn hình thức</option>
                    @foreach (\App\Models\Job::EMPLOYMENT_TYPE_LABELS as $type => $label)
                        <option value="{{ $type }}" @selected(old('employment_type', $job->employment_type) === $type)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('employment_type') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="workplace_type" class="mb-1.5 block text-sm font-medium">Nơi làm việc</label>
                <select id="workplace_type" name="workplace_type" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                    <option value="">Chọn nơi làm việc</option>
                    @foreach (\App\Models\Job::WORKPLACE_TYPE_LABELS as $type => $label)
                        <option value="{{ $type }}" @selected(old('workplace_type', $job->workplace_type) === $type)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('workplace_type') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="quantity" class="mb-1.5 block text-sm font-medium">Số lượng tuyển <span class="text-signal">*</span></label>
                <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', $job->quantity ?? 1) }}" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('quantity') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="experience_years" class="mb-1.5 block text-sm font-medium">Số năm kinh nghiệm</label>
                <input id="experience_years" name="experience_years" type="number" min="0" max="99" step="0.5" value="{{ old('experience_years', $job->experience_years) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('experience_years') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="salary_min" class="mb-1.5 block text-sm font-medium">Lương tối thiểu</label>
                <input id="salary_min" name="salary_min" type="number" min="0" step="100000" value="{{ old('salary_min', $job->salary_min) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('salary_min') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="salary_max" class="mb-1.5 block text-sm font-medium">Lương tối đa</label>
                <input id="salary_max" name="salary_max" type="number" min="0" step="100000" value="{{ old('salary_max', $job->salary_max) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('salary_max') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="salary_currency" class="mb-1.5 block text-sm font-medium">Đơn vị tiền</label>
                <input id="salary_currency" name="salary_currency" type="text" maxlength="10" value="{{ old('salary_currency', $job->salary_currency ?? 'VND') }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('salary_currency') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="deadline" class="mb-1.5 block text-sm font-medium">Hạn nhận hồ sơ</label>
                <input id="deadline" name="deadline" type="date" value="{{ old('deadline', $job->deadline?->format('Y-m-d')) }}" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">
                @error('deadline') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="space-y-4 rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
        <div>
            <h2 class="text-lg font-semibold">Nội dung công việc</h2>
            <p class="mt-1 text-sm text-ink/55">Mô tả rõ ràng giúp ứng viên hiểu vị trí và yêu cầu.</p>
        </div>
        <div>
            <label for="description" class="mb-1.5 block text-sm font-medium">Mô tả công việc <span class="text-signal">*</span></label>
            <textarea id="description" name="description" rows="6" required class="w-full rounded-xl border border-ink/15 bg-white px-3.5 py-3 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">{{ old('description', $job->description) }}</textarea>
            @error('description') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="requirements" class="mb-1.5 block text-sm font-medium">Yêu cầu ứng viên</label>
            <textarea id="requirements" name="requirements" rows="4" class="w-full rounded-xl border border-ink/15 bg-white px-3.5 py-3 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">{{ old('requirements', $job->requirements) }}</textarea>
            @error('requirements') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="benefits" class="mb-1.5 block text-sm font-medium">Quyền lợi</label>
            <textarea id="benefits" name="benefits" rows="4" class="w-full rounded-xl border border-ink/15 bg-white px-3.5 py-3 text-sm outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10">{{ old('benefits', $job->benefits) }}</textarea>
            @error('benefits') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
        </div>
    </section>

    <fieldset class="rounded-2xl border border-ink/10 bg-paper p-5 text-ink sm:p-7">
        <legend class="px-2 text-lg font-semibold">Kỹ năng yêu cầu</legend>
        @if ($errors->has('skills')) <p class="mb-3 text-xs text-rose-700">{{ $errors->first('skills') }}</p> @endif
        <div class="flex flex-wrap gap-x-5 gap-y-3">
            @forelse ($skills as $skillId => $skillName)
                <label class="flex cursor-pointer items-center gap-2 text-sm text-ink/75">
                    <input type="checkbox" name="skills[]" value="{{ $skillId }}" class="size-4 rounded border-ink/25 accent-teal-700" @checked(in_array((int) $skillId, $selectedSkills, true))>
                    {{ $skillName }}
                </label>
            @empty
                <p class="text-sm text-ink/55">Chưa có kỹ năng trong danh mục.</p>
            @endforelse
        </div>
        @if ($errors->has('skills.*')) <p class="mt-2 text-xs text-rose-700">{{ $errors->first('skills.*') }}</p> @endif
    </fieldset>

    <div class="flex flex-wrap gap-3">
        <button type="submit" class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-flap px-5 text-sm font-semibold text-ink transition hover:bg-white">Lưu nháp</button>
        <a href="{{ route('recruiter.jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/25 px-5 text-sm font-medium text-paper/80 transition hover:border-white hover:text-white">Hủy</a>
    </div>
</div>
