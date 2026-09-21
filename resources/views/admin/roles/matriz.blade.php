<x-base-layout>
    @section('titlepage', 'Matriz de Permisos')

    <x-success />
    <x-error />

    <style>
        .ui-card {
            background: #ffffff; border: 1px solid #eaedf1; border-radius: 16px;
            box-shadow: 0 4px 24px rgba(17, 24, 39, 0.04); overflow: hidden;
        }
        .ui-banner {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            border-radius: 16px; padding: 2.5rem 2rem; position: relative; overflow: hidden;
        }
        .ui-icon-box {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;
        }
        .ui-pastel-blue { background: #e0f2fe; color: #0284c7; }
        .ui-btn-primary {
            background: #4f46e5; color: #fff; border: none; border-radius: 8px; padding: 0.85rem 2rem;
            font-weight: 600; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .ui-btn-primary:hover { background: #4338ca; color: #fff; }
        .ui-accordion .accordion-item {
            border: 1px solid #e2e8f0; border-radius: 12px !important; margin-bottom: 1rem;
            overflow: hidden; background: #ffffff;
        }
        .ui-accordion .accordion-button {
            background: #ffffff; font-weight: 600; color: #1e293b; padding: 1.25rem 1.5rem; box-shadow: none !important;
        }
        .ui-accordion .accordion-button:not(.collapsed) {
            background: #f8fafc; color: #6366f1; border-bottom: 1px solid #e2e8f0;
        }
        .modulo-titulo {
            font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;
            letter-spacing: 0.05em; margin: 1.5rem 0 0.75rem; padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .ui-switch .form-check-input {
            height: 1.5rem; width: 2.75rem; border-radius: 2rem; cursor: pointer;
            border-color: #cbd5e1; background-color: #e2e8f0;
        }
        .ui-switch .form-check-input:checked { background-color: #6366f1; border-color: #6366f1; }
        .ui-switch .form-check-label {
            padding-top: 0.2rem; padding-left: 0.5rem; font-size: 0.85rem; font-weight: 500;
            color: #334155; cursor: pointer; font-family: monospace;
        }
    </style>

    <div class="container-fluid px-0 pb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <span></span>
            <a href="{{ route('admin.guia.permisos') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-signpost-split me-1"></i> Guía paso a paso
            </a>
        </div>

        <div class="ui-banner mb-4 text-white">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                </div>
                <h2 class="fw-bolder mb-0 fs-2">Matriz de Permisos</h2>
            </div>
            <p class="mb-0 fs-15 opacity-75 fw-medium ms-1">
                Aquí se definen los permisos de cada perfil (área/grupo). <strong>Cada persona tiene un solo perfil y sus permisos
                y menús (<code>menu.*</code>) provienen únicamente de él</strong>: al guardar un rol, el cambio se aplica de inmediato a
                todos los usuarios que lo tienen. Cada rol muestra <strong>todos</strong> los permisos del
                sistema (agrupados por módulo), no solo los creados para ese rol.
            </p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-5">
                <div class="ui-card h-100" id="cardNuevaArea">
                    <div class="p-3 p-md-4">
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Crear área nueva</h6>
                        <p class="text-muted fs-13">Una área agrupa los perfiles de un mismo equipo (ej. Cartera, Asociado). Se crea vacía y luego le agregas perfiles.</p>
                        <form method="POST" action="{{ route('admin.roles.areas.crear') }}" class="d-flex flex-wrap gap-2">
                            @csrf
                            <div class="flex-grow-1">
                                <input type="text" name="area" value="{{ old('area') }}" maxlength="60" required
                                       class="form-control @error('area') is-invalid @enderror" placeholder="Nombre del área">
                                @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="ui-btn-primary"><i class="bi bi-plus-lg"></i> Crear área</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ui-card h-100" id="cardNuevoPerfil">
                    <div class="p-3 p-md-4">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <h6 class="fw-bold text-dark mb-1"><i class="bi bi-plus-circle me-2 text-primary"></i>Crear perfil nuevo</h6>
                            <a href="{{ route('admin.guia.permisos') }}" target="_blank" class="fs-13"><i class="bi bi-signpost-split me-1"></i>Guía: cómo crear un perfil</a>
                        </div>
                        <p class="text-muted fs-13">Un perfil agrupa permisos y es lo único que se asigna a una persona. Se crea vacío y aquí mismo le marcas los permisos.</p>
                        <form method="POST" action="{{ route('admin.roles.store') }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label fs-12 fw-semibold mb-1">Nombre del perfil <span class="text-danger">*</span></label>
                                <input type="text" name="namerole" value="{{ old('namerole') }}" maxlength="100" required
                                       class="form-control @error('namerole') is-invalid @enderror" placeholder="Ej. analista financiero">
                                @error('namerole') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-semibold mb-1">Dentro del área</label>
                                <select name="area" class="form-select">
                                    <option value="">(una área propia con su nombre)</option>
                                    @foreach ($areas as $a) <option value="{{ $a }}" @selected(old('area', request('nuevaen')) === $a)>{{ strtoupper($a) }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="ui-btn-primary w-100"><i class="bi bi-save"></i> Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui-card mb-3 p-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group" style="max-width: 460px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" id="buscarPerfil" class="form-control" placeholder="Buscar área o perfil (ej. asociado, cartera)..." autocomplete="off">
                </div>
                <span class="text-muted fs-13" id="contadorPerfiles">{{ count($roles) }} perfiles</span>
            </div>
            <div class="text-muted fs-12 mt-1">Busca por área o por perfil. Al desplegar un perfil, ahí mismo tiene su propio buscador de permisos.</div>
        </div>

        <div class="accordion ui-accordion mb-5" id="accordionMatriz">
            @foreach ($grupos as $g)
                @php
                    $ga = $loop->index;
                    $abrirArea = request('area') === $g['area'] || $g['roles']->contains(fn ($r) => (int) request('rol') === (int) $r->id);
                @endphp
                <div class="accordion-item shadow-sm area-item" data-nombre="{{ $g['area'] }}" id="area-{{ $ga }}">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $abrirArea ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                data-bs-target="#acollapse-{{ $ga }}" aria-expanded="{{ $abrirArea ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center w-100 pe-3">
                                <i class="bi bi-diagram-3 me-3 fs-4" style="color: #0ea5e9;"></i>
                                <span>ÁREA {{ strtoupper($g['area']) }}</span>
                                <span class="ms-auto badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1 fw-medium fs-12">
                                    {{ count($g['roles']) }} {{ count($g['roles']) === 1 ? 'perfil' : 'perfiles' }}@if (count($g['roles'])): {{ $g['roles']->map(fn ($r) => strtoupper($r->name))->implode(', ') }}@endif
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="acollapse-{{ $ga }}" class="accordion-collapse collapse {{ $abrirArea ? 'show' : '' }}">
                        <div class="accordion-body p-3 p-md-4 bg-light">
                            <div class="d-flex flex-wrap align-items-end gap-3 mb-3 pb-3 border-bottom">
                                @if ($g['id'])
                                    <form action="{{ route('admin.roles.areas.renombrar', $g['id']) }}" method="POST" class="d-flex flex-wrap align-items-end gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="form-label fs-12 fw-semibold mb-1">Nombre del área</label>
                                            <input type="text" name="area" value="{{ $g['area'] }}" maxlength="60" required class="form-control form-control-sm" style="min-width: 200px;">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i> Guardar nombre</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.roles.store') }}" method="POST" class="d-flex flex-wrap align-items-end gap-2">
                                    @csrf
                                    <input type="hidden" name="area" value="{{ $g['area'] }}">
                                    <div>
                                        <label class="form-label fs-12 fw-semibold mb-1">Agregar un perfil a esta área</label>
                                        <input type="text" name="namerole" maxlength="100" required placeholder="Nombre del perfil nuevo" class="form-control form-control-sm" style="min-width: 220px;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i> Agregar perfil</button>
                                </form>
                                @if ($g['id'] && count($g['roles']) === 0)
                                    <form action="{{ route('admin.roles.areas.eliminar', $g['id']) }}" method="POST" class="ms-auto"
                                          onsubmit="return confirm('¿Eliminar el área {{ strtoupper($g['area']) }}? Está vacía.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Eliminar área vacía</button>
                                    </form>
                                @endif
                            </div>
                            @if (count($g['roles']))
                                <div class="accordion ui-accordion">
                                    @foreach ($g['roles'] as $rol)
                                        @include('admin.roles.partials.matriz-perfil', ['rol' => $rol, 'i' => $indice[$rol->id], 'parent' => null])
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted fs-13 mb-0">Esta área aún no tiene perfiles. Agrega el primero arriba.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @php
        $catalogoJson = json_encode(
            $permisosPorModulo->map(fn ($ps, $modulo) => [$modulo, $ps->map(fn ($p) => [$p->id, $p->name])->values()])->values(),
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
        );
    @endphp
    <script>
        // Catálogo único de permisos por módulo: [[modulo, [[id, nombre], ...]], ...]
        const CATALOGO_PERMISOS = {!! $catalogoJson !!};

        // Buscador de perfiles: por nombre del perfil o por permisos que tiene asignados.
        (function () {
            const caja = document.getElementById('buscarPerfil');
            const contador = document.getElementById('contadorPerfiles');
            if (!caja) return;
            const items = Array.from(document.querySelectorAll('#accordionMatriz .perfil-item'));
            const areas = Array.from(document.querySelectorAll('#accordionMatriz .area-item'));
            caja.addEventListener('input', function () {
                const t = caja.value.trim().toLowerCase();
                let visibles = 0;
                items.forEach(function (it) {
                    const area = it.closest('.area-item');
                    const coincide = !t || (area && area.dataset.nombre.includes(t)) || it.dataset.nombre.includes(t);
                    it.classList.toggle('d-none', !coincide);
                    if (coincide) visibles++;
                });
                // Un área se muestra si alguno de sus perfiles coincide, y se despliega para verlos.
                areas.forEach(function (a) {
                    const vacia = a.querySelector('.perfil-item') === null;
                    const hay = vacia ? (!t || a.dataset.nombre.includes(t)) : a.querySelector('.perfil-item:not(.d-none)') !== null;
                    a.classList.toggle('d-none', !hay);
                    if (t && hay) {
                        a.querySelector(':scope > .accordion-collapse').classList.add('show');
                        a.querySelector(':scope > .accordion-header .accordion-button').classList.remove('collapsed');
                    }
                });
                contador.textContent = t ? visibles + ' de ' + items.length + ' perfiles' : items.length + ' perfiles';
            });
        })();

        function escaparHtml(t) {
            return String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        // Dibuja (una sola vez) la cuadrícula de permisos del perfil i con sus interruptores.
        function construirMatriz(i) {
            const cont = document.getElementById('mmatriz-' + i);
            if (!cont || cont.dataset.construida) return;
            const asignados = new Set(JSON.parse(cont.dataset.asignados || '[]'));
            let html = '';
            CATALOGO_PERMISOS.forEach(function ([modulo, permisos]) {
                const mod = escaparHtml(modulo), modLower = escaparHtml(String(modulo).toLowerCase());
                html += '<div class="modulo-titulo modulo-item-' + i + '" data-nombre="' + modLower + '">' + mod + '</div>'
                     + '<div class="row g-3 modulo-grupo-' + i + '" data-modulo="' + modLower + '">';
                permisos.forEach(function ([id, nombre]) {
                    const n = escaparHtml(nombre);
                    html += '<div class="col-lg-6 col-xl-4 permiso-item-' + i + '" data-nombre="' + escaparHtml(nombre.toLowerCase()) + '">'
                         + '<div class="ui-card p-3 h-100 border-0"><div class="form-check form-switch ui-switch d-flex align-items-center justify-content-between w-100 m-0 p-0">'
                         + '<label class="form-check-label user-select-none text-truncate pe-3" for="mchk-' + i + '-' + id + '">' + n + '</label>'
                         + '<input type="checkbox" name="permissions[]" value="' + id + '" class="form-check-input flex-shrink-0 m-0" id="mchk-' + i + '-' + id + '"'
                         + (asignados.has(id) ? ' checked' : '') + '></div></div></div>';
                });
                html += '</div>';
            });
            cont.innerHTML = html;
            cont.dataset.construida = '1';
        }

        document.querySelectorAll('#accordionMatriz [id^="mcollapse-"]').forEach(function (panel) {
            const i = panel.id.replace('mcollapse-', '');
            panel.addEventListener('show.bs.collapse', function () { construirMatriz(i); });
            if (panel.classList.contains('show')) construirMatriz(i);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const nuevo = document.querySelector('#formNuevoPerfil.show');
            if (nuevo) { nuevo.scrollIntoView({ block: 'center' }); return; }
            const abierto = document.querySelector('#accordionMatriz [id^="mcollapse-"].show');
            if (abierto) abierto.closest('.accordion-item').scrollIntoView({ block: 'start' });
        });

        // Filtra por nombre de permiso O de módulo dentro de la matriz de UN rol — el módulo
        // solo se oculta si ninguno de sus permisos coincide con la búsqueda.
        function filtrarPermisosMatriz(i, texto) {
            const termino = texto.trim().toLowerCase();
            document.querySelectorAll('.modulo-grupo-' + i).forEach(function (grupo) {
                let visibles = 0;
                grupo.querySelectorAll('.permiso-item-' + i).forEach(function (item) {
                    const coincide = item.dataset.nombre.includes(termino) || grupo.dataset.modulo.includes(termino);
                    item.classList.toggle('d-none', !coincide);
                    if (coincide) visibles++;
                });
                grupo.classList.toggle('d-none', visibles === 0);
                const titulo = grupo.previousElementSibling;
                if (titulo && titulo.classList.contains('modulo-item-' + i)) {
                    titulo.classList.toggle('d-none', visibles === 0);
                }
            });
        }

        // Marca/desmarca solo los permisos actualmente visibles (respeta el filtro de búsqueda),
        // así "Marcar todos" después de buscar "reservas" no toca el resto de módulos.
        function marcarPermisosMatriz(i, estado) {
            document.querySelectorAll('#mmatriz-' + i + ' .permiso-item-' + i).forEach(function (item) {
                if (!item.classList.contains('d-none')) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    if (checkbox) checkbox.checked = estado;
                }
            });
        }
    </script>
</x-base-layout>
