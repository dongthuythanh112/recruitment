@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3 px-4 py-4 text-sm text-paper/70" aria-label="Phân trang">
        <span>Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        <span class="flex gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-11 items-center rounded border border-white/10 px-4 text-paper/40">Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center rounded border border-white/30 px-4 text-paper hover:border-flap hover:text-flap">Trước</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center rounded border border-white/30 px-4 text-paper hover:border-flap hover:text-flap">Sau</a>
            @else
                <span class="inline-flex min-h-11 items-center rounded border border-white/10 px-4 text-paper/40">Sau</span>
            @endif
        </span>
    </nav>
@endif
