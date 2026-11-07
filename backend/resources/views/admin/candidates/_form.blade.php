@php($user = $candidate->user)
<div class="space-y-6">
    <section class="rounded-lg border border-stone-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold">Tài khoản</h2>
        @include('admin.partials.account-fields', ['user' => $user])
    </section>

    <section class="rounded-lg border border-stone-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold">Hồ sơ ứng viên</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.input name="date_of_birth" label="Ngày sinh" type="date" :value="$candidate->date_of_birth?->format('Y-m-d')" />
            <x-admin.select name="gender" label="Giới tính" :options="\App\Models\CandidateProfile::GENDER_LABELS" :value="$candidate->gender" placeholder="Chưa chọn" />
            <x-admin.input name="desired_position" label="Vị trí mong muốn" :value="$candidate->desired_position" />
            <x-admin.input name="years_of_experience" label="Số năm kinh nghiệm" type="number" step="0.5" min="0" max="99" :value="$candidate->years_of_experience" />
            <x-admin.input name="desired_salary_min" label="Lương mong muốn tối thiểu (VND)" type="number" min="0" :value="$candidate->desired_salary_min" />
            <x-admin.input name="desired_salary_max" label="Lương mong muốn tối đa (VND)" type="number" min="0" :value="$candidate->desired_salary_max" />
            <div class="sm:col-span-2"><x-admin.input name="address" label="Địa chỉ" :value="$candidate->address" /></div>
            <div class="sm:col-span-2"><x-admin.textarea name="summary" label="Giới thiệu" :value="$candidate->summary" /></div>
        </div>
    </section>

    <div class="flex gap-2">
        <x-admin.button>Lưu</x-admin.button>
        <x-admin.button variant="secondary" :href="route('admin.candidates.index')">Hủy</x-admin.button>
    </div>
</div>
