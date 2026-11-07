{{-- Trường tài khoản dùng chung cho ứng viên và nhà tuyển dụng. $user là null khi tạo mới. --}}
<div class="grid gap-4 sm:grid-cols-2">
    <x-admin.input name="name" label="Họ và tên" :value="$user?->name" required />
    <x-admin.input name="email" label="Email" type="email" :value="$user?->email" required />
    <x-admin.input name="phone" label="Số điện thoại" :value="$user?->phone" />
    <x-admin.select name="status" label="Trạng thái tài khoản" :options="\App\Models\User::STATUS_LABELS" :value="$user?->status ?? 'active'" required />
    <x-admin.input name="password" label="Mật khẩu" type="password" autocomplete="new-password" :required="! $user"
        :hint="$user ? 'Để trống nếu không đổi mật khẩu.' : 'Ít nhất 8 ký tự.'" />
    <x-admin.input name="password_confirmation" label="Nhập lại mật khẩu" type="password" autocomplete="new-password" :required="! $user" />
</div>
