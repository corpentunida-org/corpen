@if ($paginator->hasPages())
    <nav aria-label="Paginación" class="mt-4">
        <ul class="pagination justify-content-center gap-1 mb-0">

            {{-- Página anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill px-3 py-2 text-muted shadow-sm" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link rounded-pill px-3 py-2 shadow-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">
                        &laquo;
                    </a>
                </li>
            @endif

            {{-- Páginas intermedias --}}
            @foreach ($elements as $element)
                {{-- Separador (...) --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill px-3 py-2 text-muted shadow-sm">{{ $element }}</span>
                    </li>
                @endif

                {{-- Links numéricos --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link rounded-pill px-3 py-2 shadow-sm fw-bold bg-primary text-white border-0">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-pill px-3 py-2 shadow-sm text-dark bg-light border-0" href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Página siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill px-3 py-2 shadow-sm" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">
                        &raquo;
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link rounded-pill px-3 py-2 text-muted shadow-sm" aria-hidden="true">&raquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
