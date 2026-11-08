<a href="{{ route('jobs.show', $job->slug) }}" title="{{ $job->title }}" class="group grid gap-x-6 gap-y-2 border-b border-white/10 px-4 py-3 transition-colors hover:bg-white/5 lg:grid-cols-[minmax(0,1fr)_11rem_12rem_11.5rem_1.25rem] lg:items-center" data-board-row>
    <div class="min-w-0">
        <h3 class="font-normal"><span class="flap lg:block" data-flap data-flap-title>{{ \Illuminate\Support\Str::limit($job->title, 29, '…') }}</span></h3>
        <p class="mt-0.5 truncate text-sm text-paper/70">{{ $job->company->name }}</p>
    </div>
    <div class="flex flex-wrap gap-x-3 gap-y-2 lg:contents">
        <span class="flap" data-flap>{{ \Illuminate\Support\Str::limit($job->location ?? 'Chưa rõ', 13, '…') }}</span>
        <span class="flap" data-flap>{{ \Illuminate\Support\Str::limit($job->salaryShort(), 15, '…') }}</span>
        @include('client.partials.job-status')
    </div>
    <svg aria-hidden="true" viewBox="0 0 20 20" class="hidden size-5 text-paper/60 transition-transform group-hover:translate-x-0.5 group-hover:text-flap lg:block"><path d="M7 5l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2"/></svg>
</a>
