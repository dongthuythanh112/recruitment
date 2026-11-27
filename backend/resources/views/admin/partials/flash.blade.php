@php
    $adminFlashMessages = collect();

    if (session('success')) {
        $adminFlashMessages->push(['type' => 'success', 'message' => session('success')]);
    }

    if (session('error')) {
        $adminFlashMessages->push(['type' => 'error', 'message' => session('error')]);
    }

    if ($errors->has('action')) {
        $adminFlashMessages->push(['type' => 'error', 'message' => $errors->first('action')]);
    }
@endphp

@if ($adminFlashMessages->isNotEmpty())
    <div class="fixed inset-x-4 top-4 z-[100] mx-auto flex max-w-md flex-col gap-3 sm:inset-x-auto sm:right-5" aria-live="polite">
        @foreach ($adminFlashMessages as $flash)
            <div data-toast role="{{ $flash['type'] === 'success' ? 'status' : 'alert' }}" class="flex items-start gap-3 rounded-2xl border bg-white p-4 text-left shadow-xl shadow-slate-900/15 {{ $flash['type'] === 'success' ? 'border-emerald-200 text-emerald-950' : 'border-rose-200 text-rose-950' }}">
                <span class="grid size-9 shrink-0 place-items-center rounded-full {{ $flash['type'] === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}" aria-hidden="true">
                    @if ($flash['type'] === 'success')
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.312a1 1 0 0 1-1.42.006l-3.75-3.688a1 1 0 0 1 1.403-1.426l3.04 2.987 6.557-6.611a1 1 0 0 1 1.414.006Z" clip-rule="evenodd"/></svg>
                    @else
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm-.75 4a.75.75 0 0 1 1.5 0v4a.75.75 0 0 1-1.5 0V6Zm.75 7a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z" clip-rule="evenodd"/></svg>
                    @endif
                </span>
                <div class="min-w-0 flex-1 pt-1">
                    <p class="text-sm font-semibold">{{ $flash['type'] === 'success' ? 'Thành công' : 'Có lỗi xảy ra' }}</p>
                    <p class="mt-0.5 text-sm leading-5 text-slate-600">{{ $flash['message'] }}</p>
                </div>
                <button type="button" data-toast-close aria-label="Đóng thông báo" class="grid size-8 shrink-0 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4"><path d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 0 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z"/></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
