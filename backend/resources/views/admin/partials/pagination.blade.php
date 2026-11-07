@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3 border-t border-stone-200 px-4 py-3 text-sm" aria-label="Phân trang">
        <span class="text-stone-500">Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} · {{ $paginator->total() }} kết quả</span>
        <span class="flex gap-2">
            @if ($paginator->onFirstPage())
                <span class="rounded-md px-3 py-1.5 text-stone-400 ring-1 ring-inset ring-stone-200">Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-md px-3 py-1.5 text-stone-700 ring-1 ring-inset ring-stone-300 hover:bg-stone-50">Trước</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-md px-3 py-1.5 text-stone-700 ring-1 ring-inset ring-stone-300 hover:bg-stone-50">Sau</a>
            @else
                <span class="rounded-md px-3 py-1.5 text-stone-400 ring-1 ring-inset ring-stone-200">Sau</span>
            @endif
        </span>
    </nav>
@endif
