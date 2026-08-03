@if (isset($paginator) && $paginator instanceof \Illuminate\Contracts\Pagination\Paginator && $paginator->hasPages())
    <nav class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4 gap-3 text-sm">
        <div class="text-muted">
            Mostrando
            <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> a
            <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> de
            <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> resultados
        </div>

        <ul class="pagination pagination-sm mb-0 shadow-sm rounded-pill overflow-hidden">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-light text-muted px-3 py-2">Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 px-3 py-2" href="{{ $paginator->previousPageUrl() }}">Anterior</a>
                </li>
            @endif

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 px-3 py-2" href="{{ $paginator->nextPageUrl() }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-light text-muted px-3 py-2">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
