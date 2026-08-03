@if ($paginator->hasPages())
    <nav class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm">
        <div class="text-gray-600">
            Mostrando <span class="font-semibold text-gray-800">{{ $paginator->firstItem() }}</span> a <span class="font-semibold text-gray-800">{{ $paginator->lastItem() }}</span> de <span class="font-semibold text-gray-800">{{ $paginator->total() }}</span> resultados
        </div>
        <div class="inline-flex rounded-md shadow-sm space-x-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed font-medium">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">Siguiente</a>
            @else
                <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed font-medium">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
