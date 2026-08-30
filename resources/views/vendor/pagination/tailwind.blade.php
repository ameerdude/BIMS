@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <p style="font-size:0.8125rem;color:var(--text-secondary);">
                @if ($paginator->firstItem())
                    <span style="font-weight:600;color:var(--text-primary);">{{ $paginator->firstItem() }}</span>
                    –
                    <span style="font-weight:600;color:var(--text-primary);">{{ $paginator->lastItem() }}</span>
                    of
                @else
                    {{ $paginator->count() }} of
                @endif
                <span style="font-weight:600;color:var(--text-primary);">{{ $paginator->total() }}</span>
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:4px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-btn" style="opacity:0.4;cursor:not-allowed;display:inline-flex;align-items:center;gap:4px;padding:6px 10px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-btn" style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Prev
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-btn" style="cursor:default;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn" style="text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-btn" style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;text-decoration:none;">
                    Next
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span class="pagination-btn" style="opacity:0.4;cursor:not-allowed;display:inline-flex;align-items:center;gap:4px;padding:6px 10px;">
                    Next
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>
    </nav>
@endif
