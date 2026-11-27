@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-between gap-3 text-sm" aria-label="Phân trang">
        <p class="text-xs font-medium text-slate-500 sm:text-sm">
            Hiển thị <span class="font-semibold text-slate-700">{{ number_format($paginator->firstItem() ?? 0) }}–{{ number_format($paginator->lastItem() ?? 0) }}</span>
            trên <span class="font-semibold text-slate-700">{{ number_format($paginator->total()) }}</span> kết quả
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex size-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300">
                    <span class="sr-only">Trang trước</span>
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 0 1 0 1.06L8.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Trang trước" class="inline-flex size-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 0 1 0 1.06L8.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06l-5.25-5.25a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                </a>
            @endif

            <span class="px-2 text-xs font-semibold tabular-nums text-slate-600 sm:hidden">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-hidden="true" class="grid size-9 place-items-center text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="grid size-9 place-items-center rounded-lg bg-sky-800 text-xs font-bold text-white shadow-sm shadow-sky-900/20">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Trang {{ $page }}" class="grid size-9 place-items-center rounded-lg border border-transparent text-xs font-semibold text-slate-600 transition hover:border-slate-200 hover:bg-slate-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Trang sau" class="inline-flex size-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </a>
            @else
                <span aria-disabled="true" class="inline-flex size-9 cursor-not-allowed items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-300">
                    <span class="sr-only">Trang sau</span>
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M7.22 4.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L11.94 10 7.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
