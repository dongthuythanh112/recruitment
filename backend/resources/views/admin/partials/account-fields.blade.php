{{-- Trường tài khoản dùng chung cho ứng viên và nhà tuyển dụng. $user là null khi tạo mới. --}}
@if (! $user)
    <p class="mb-4 rounded-xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm leading-6 text-sky-900">
        Tài khoản được admin tạo mới có mật khẩu mặc định là <strong>12345678</strong>.
    </p>
@endif
<div class="grid gap-4 sm:grid-cols-2">
    <x-admin.input name="name" label="Họ và tên" :value="$user?->name" required />
    <x-admin.input name="email" label="Email" type="email" :value="$user?->email" required />
    <x-admin.input name="phone" label="Số điện thoại" :value="$user?->phone" />
    <x-admin.select name="status" label="Trạng thái tài khoản" :options="\App\Models\User::STATUS_LABELS" :value="$user?->status ?? 'active'" required />
    @if ($user)
        <x-admin.input name="password" label="Mật khẩu mới" type="password" autocomplete="new-password" hint="Để trống nếu không đổi mật khẩu." />
        <x-admin.input name="password_confirmation" label="Nhập lại mật khẩu mới" type="password" autocomplete="new-password" />
    @endif
</div>
