@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-3 border-t border-slate-200 px-4 py-3 text-sm" aria-label="Phân trang">
        <span class="text-slate-500">Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} · {{ $paginator->total() }} kết quả</span>
        <span class="flex gap-2">
            @if ($paginator->onFirstPage())
                <span class="rounded-md px-3 py-1.5 text-slate-400 ring-1 ring-inset ring-slate-200">Trước</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-md px-3 py-1.5 text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Trước</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-md px-3 py-1.5 text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Sau</a>
            @else
                <span class="rounded-md px-3 py-1.5 text-slate-400 ring-1 ring-inset ring-slate-200">Sau</span>
            @endif
        </span>
    </nav>
@endif
