<article class="group relative flex min-h-44 overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-lg hover:shadow-sky-900/5">
    <a href="{{ route('jobs.show', $job->slug) }}" class="flex min-w-0 flex-1 items-start gap-4 p-4 pr-16 sm:p-5 sm:pr-16">
        <span class="grid size-14 shrink-0 place-items-center overflow-hidden rounded-xl border border-slate-100 bg-slate-50 text-lg font-bold text-sky-800 sm:size-16">
            <img src="{{ $job->company->logo ?: asset('images/default-company-logo.svg') }}" alt="Logo {{ $job->company->name }}" loading="lazy" class="size-full object-contain p-1.5">
        </span>

        <span class="flex min-w-0 flex-1 flex-col self-stretch">
            <span class="line-clamp-2 text-sm font-bold leading-5 text-slate-900 transition group-hover:text-sky-800 sm:text-base">{{ $job->title }}</span>
            <span class="mt-1 truncate text-xs font-medium text-slate-500 sm:text-sm">{{ $job->company->name }}</span>
            <span class="mt-auto grid grid-cols-2 items-center gap-3 pt-4 text-xs text-slate-600">
                <span class="inline-flex min-w-0 items-center gap-1.5 truncate font-semibold text-emerald-700">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4"><rect x="2.25" y="4.25" width="15.5" height="11.5" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 8.1c.35-.4.9-.6 1.65-.6 1.05 0 1.75.48 1.75 1.2 0 1.8-3.5.85-3.5 2.75 0 .75.7 1.3 1.85 1.3.75 0 1.4-.22 1.8-.65M7.75 6.5v7m4.25-5.75h2.5M12 10h2.5m-2.5 2.25h2.5"/></svg>
                    {{ $job->salaryShort() }}
                </span>
                <span class="inline-flex min-w-0 items-center gap-1.5 text-slate-600">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-sky-700"><path fill-rule="evenodd" d="M10 1.75a6.25 6.25 0 0 0-6.25 6.25c0 4.35 5.47 9.56 5.7 9.78a.8.8 0 0 0 1.1 0c.23-.22 5.7-5.43 5.7-9.78A6.25 6.25 0 0 0 10 1.75Zm0 8.5a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd"/></svg>
                    <span class="truncate">{{ $job->location ?: 'Linh hoạt địa điểm' }}</span>
                </span>
            </span>
        </span>
    </a>

    @php($isSaved = in_array((int) $job->id, $savedJobIds, true))
    @if ($candidate)
        <form method="POST" action="{{ route($isSaved ? 'candidate.saved-jobs.destroy' : 'candidate.saved-jobs.store', $job) }}" data-save-job-form data-saved="{{ $isSaved ? 'true' : 'false' }}" data-store-url="{{ route('candidate.saved-jobs.store', $job) }}" data-destroy-url="{{ route('candidate.saved-jobs.destroy', $job) }}" class="absolute right-3 top-3">
            @csrf
            @if ($isSaved) <input type="hidden" name="_method" value="DELETE" data-save-method> @endif
            <button type="submit" data-save-job-button aria-label="{{ $isSaved ? 'Bỏ lưu' : 'Lưu' }} {{ $job->title }}" title="{{ $isSaved ? 'Bỏ lưu việc làm' : 'Lưu việc làm' }}" class="grid size-10 place-items-center rounded-full border {{ $isSaved ? 'border-rose-200 bg-rose-50 text-rose-600' : 'border-slate-200 bg-white text-slate-500' }} shadow-sm transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus:ring-4 focus:ring-rose-100">
                <svg aria-hidden="true" data-save-job-icon viewBox="0 0 24 24" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5a1.5 1.5 0 0 1 1.5 1.5v15l-6.75-4.5-6.75 4.5v-15a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
            </button>
        </form>
    @else
        <a href="{{ route('login', ['role' => 'candidate']) }}" aria-label="Đăng nhập để lưu {{ $job->title }}" title="Đăng nhập để lưu việc làm" class="absolute right-3 top-3 grid size-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5a1.5 1.5 0 0 1 1.5 1.5v15l-6.75-4.5-6.75 4.5v-15a1.5 1.5 0 0 1 1.5-1.5Z"/></svg>
        </a>
    @endif
</article>
