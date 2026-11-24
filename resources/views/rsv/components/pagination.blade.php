@php
    $pag = $paginator ?? null;
    if ($pag && method_exists($pag, 'hasPages') && $pag->hasPages()) {
        if (!isset($elements)) {
            $window = \Illuminate\Pagination\UrlWindow::make($pag);
            $elements = array_filter([
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ]);
        }
    }
@endphp

@if ($pag && method_exists($pag, 'hasPages') && $pag->hasPages())
    <nav aria-label="Paginación">
        <ul class="pagination pagination-sm justify-content-center mb-0" style="font-size: 8.5px;">
            @if ($pag->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $pag->previousPageUrl() }}" rel="prev">&lsaquo;</a></li>
            @endif

            @foreach ($elements ?? [] as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $pag->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($pag->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $pag->nextPageUrl() }}" rel="next">&rsaquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>
            @endif
        </ul>
    </nav>
@endif
