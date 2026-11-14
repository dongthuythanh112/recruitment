@extends('client.layouts.app')

@section('title', 'Tạo tài khoản')
@section('description', 'Tạo tài khoản ứng viên hoặc nhà tuyển dụng trên Việc Ngon.')

@section('content')
    <section class="relative isolate overflow-hidden bg-board px-4 py-10 text-paper sm:py-14 lg:py-20">
        <div aria-hidden="true" class="pointer-events-none absolute -top-36 right-[-5rem] -z-10 size-[28rem] rounded-full border border-flap/15 sm:right-[-2rem]"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -top-20 right-8 -z-10 size-[20rem] rounded-full border border-flap/10"></div>

        <div class="mx-auto grid max-w-6xl overflow-hidden rounded-2xl border border-white/10 bg-paper text-ink shadow-2xl shadow-black/30 lg:grid-cols-[0.85fr_1.15fr]">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-flap p-8 text-ink sm:flex lg:p-10">
                <div aria-hidden="true" class="absolute -right-20 -bottom-24 size-72 rounded-full border-[28px] border-ink/5"></div>
                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-ink/15 bg-ink/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.14em]">
                        <span class="size-1.5 rounded-full bg-signal"></span>
                        Kết nối đúng cơ hội
                    </span>
                    <h1 class="mt-8 font-signage text-5xl leading-[1.03] font-extrabold tracking-wide uppercase lg:text-6xl">
                        Ga tiếp theo<br>là của bạn.
                    </h1>
                    <p class="mt-5 max-w-sm text-sm leading-6 text-ink/75">
                        Tạo tài khoản miễn phí để tìm việc phù hợp hoặc đưa cơ hội nghề nghiệp đến gần ứng viên hơn.
                    </p>
                </div>

                <div class="relative mt-10 border-t border-ink/15 pt-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.15em] text-ink/60">Việc Ngon</p>
                    <p class="mt-1 text-sm text-ink/75">Bảng việc làm cho người tìm việc và nơi cần người.</p>
                </div>
            </aside>

            <div class="p-5 sm:p-8 lg:p-10">
                <div class="mb-7">
                    <p class="text-sm font-semibold text-signal">Bắt đầu miễn phí</p>
                    <h2 class="mt-1 font-signage text-4xl font-extrabold tracking-wide uppercase sm:text-5xl">Tạo tài khoản</h2>
                    <p class="mt-2 text-sm text-ink/65">Chọn loại tài khoản để tiếp tục đăng ký.</p>
                </div>

                <div class="grid grid-cols-2 rounded-xl bg-ink/5 p-1" aria-label="Chọn loại tài khoản">
                    <a href="{{ route('register', ['role' => 'candidate']) }}" @class([
                        'flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold transition',
                        'bg-white text-ink shadow-sm' => $role === 'candidate',
                        'text-ink/60 hover:text-ink' => $role !== 'candidate',
                    ]) @if ($role === 'candidate') aria-current="page" @endif>
                        Ứng viên
                    </a>
                    <a href="{{ route('register', ['role' => 'recruiter']) }}" @class([
                        'flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-semibold transition',
                        'bg-white text-ink shadow-sm' => $role === 'recruiter',
                        'text-ink/60 hover:text-ink' => $role !== 'recruiter',
                    ]) @if ($role === 'recruiter') aria-current="page" @endif>
                        Nhà tuyển dụng
                    </a>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <input type="hidden" name="role" value="{{ $role }}">

                    @if ($errors->any())
                        <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <p class="font-semibold">Vui lòng kiểm tra lại thông tin:</p>
                            <ul class="mt-1 list-inside list-disc space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($role === 'recruiter')
                        <div class="rounded-xl border border-teal-900/10 bg-teal-900/[0.04] p-4">
                            <p class="text-sm font-semibold">Thông tin nhà tuyển dụng</p>
                            <p class="mt-1 text-xs leading-5 text-ink/60">Tài khoản công ty và hồ sơ liên hệ sẽ được tạo cùng lúc.</p>
                        </div>
                    @endif

                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium">{{ $role === 'recruiter' ? 'Họ và tên người liên hệ' : 'Họ và tên' }} <span class="text-signal">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Nguyễn Văn An">
                        @error('name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Email <span class="text-signal">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="ban@email.com">
                        @error('email') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium">Số điện thoại <span class="text-xs font-normal text-ink/50">(không bắt buộc)</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="0912 345 678">
                            @error('phone') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        @if ($role === 'recruiter')
                            <div>
                                <label for="position" class="mb-1.5 block text-sm font-medium">Chức vụ <span class="text-xs font-normal text-ink/50">(không bắt buộc)</span></label>
                                <input id="position" name="position" type="text" value="{{ old('position') }}" autocomplete="organization-title" class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Chuyên viên nhân sự">
                                @error('position') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>

                    @if ($role === 'recruiter')
                        <div>
                            <label for="company_name" class="mb-1.5 block text-sm font-medium">Tên công ty <span class="text-signal">*</span></label>
                            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" autocomplete="organization" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition placeholder:text-ink/35 focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Công ty của bạn">
                            @error('company_name') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium">Mật khẩu <span class="text-signal">*</span></label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Tối thiểu 8 ký tự">
                            @error('password') <p class="mt-1 text-xs text-rose-700">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Nhập lại mật khẩu <span class="text-signal">*</span></label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-ink/15 bg-white px-3.5 text-sm text-ink outline-none transition focus:border-teal-700 focus:ring-4 focus:ring-teal-700/10" placeholder="Nhập lại mật khẩu">
                        </div>
                    </div>

                    <button type="submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-ink px-5 text-sm font-semibold text-paper transition hover:bg-signal focus:outline-none focus:ring-4 focus:ring-signal/20">
                        {{ $role === 'recruiter' ? 'Tạo tài khoản nhà tuyển dụng' : 'Tạo tài khoản ứng viên' }}
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="block size-[18px] shrink-0" aria-hidden="true"><path d="M5 12h14m-7-7 7 7-7 7" /></svg>
                    </button>
                    <p class="text-center text-xs leading-5 text-ink/55">Khi tạo tài khoản, bạn đồng ý cung cấp thông tin chính xác để sử dụng Việc Ngon.</p>
                </form>
            </div>
        </div>
    </section>
@endsection
