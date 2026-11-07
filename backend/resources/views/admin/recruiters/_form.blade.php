@php($user = $recruiter->user)
<div class="space-y-6">
    <section class="rounded-lg border border-stone-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold">Tài khoản</h2>
        @include('admin.partials.account-fields', ['user' => $user])
    </section>

    <section class="rounded-lg border border-stone-200 bg-white p-5">
        <h2 class="mb-4 text-sm font-semibold">Hồ sơ nhà tuyển dụng</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-admin.select name="company_id" label="Công ty" :options="$companies" :value="$recruiter->company_id" placeholder="Chưa gán công ty" />
            <x-admin.input name="new_company_name" label="Hoặc tạo công ty mới" hint="Nếu nhập tên ở đây, công ty mới sẽ được tạo và gán cho nhà tuyển dụng." />
            <x-admin.input name="position" label="Chức vụ" :value="$recruiter->position" />
        </div>
    </section>

    <div class="flex gap-2">
        <x-admin.button>Lưu</x-admin.button>
        <x-admin.button variant="secondary" :href="route('admin.recruiters.index')">Hủy</x-admin.button>
    </div>
</div>
