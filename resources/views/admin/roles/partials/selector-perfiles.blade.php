{{-- Lista desplegable (clic) con buscador de perfiles SIN área, con casillas (perfiles[]). Requiere:
     $perfiles (colección de roles sin área). Debe ir dentro de un <form>. Usa las funciones
     alternarSelectorPerfiles / filtrarSelectorPerfiles / actualizarConteoSelector de la Matriz. --}}
<div class="selector-perfiles">
    <button type="button" class="btn btn-outline-secondary btn-sm w-100 d-flex justify-content-between align-items-center bg-white"
            onclick="alternarSelectorPerfiles(this)" aria-expanded="false">
        <span class="etiqueta-selector text-start">Seleccionar perfiles sin área… ({{ count($perfiles) }} disponibles)</span>
        <i class="bi bi-chevron-down"></i>
    </button>
    <div class="panel-selector d-none mt-2">
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
            <input type="search" class="form-control" placeholder="Buscar perfil sin área..." autocomplete="off"
                   oninput="filtrarSelectorPerfiles(this)" onkeydown="if (event.key === 'Enter') event.preventDefault()">
        </div>
        <div class="border rounded-3 bg-white p-2" style="max-height: 190px; overflow-y: auto;">
            @forelse ($perfiles as $p)
                <label class="d-flex align-items-center gap-2 py-1 px-2 rounded item-sin-area" data-nombre="{{ strtolower($p->name) }}" style="cursor: pointer;">
                    <input type="checkbox" name="perfiles[]" value="{{ $p->id }}" class="form-check-input m-0" onchange="actualizarConteoSelector(this)">
                    <span class="fs-13">{{ strtoupper($p->name) }}</span>
                </label>
            @empty
                <div class="text-muted fs-13 p-2">No hay perfiles sin área.</div>
            @endforelse
            <div class="text-muted fs-13 p-2 d-none sin-resultados">Ningún perfil coincide con la búsqueda.</div>
        </div>
    </div>
</div>
