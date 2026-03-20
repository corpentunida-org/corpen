{{-- @if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            //Previous Page Link
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            //Pagination Elements
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                // Array Of Links 
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            //Next Page Link
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif --}}
@if ($paginator->hasPages())
    <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li class="disabled">
                <i class="bi bi-arrow-left"></i>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </li>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span>{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li>
                        <a href="{{ $url }}" class="{{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}">
                    <i class="bi bi-arrow-right"></i>
                </a>
            </li>
        @else
            <li class="disabled">
                <i class="bi bi-arrow-right"></i>
            </li>
        @endif

    </ul>
@endif