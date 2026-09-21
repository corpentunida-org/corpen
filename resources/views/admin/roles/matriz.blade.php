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

        <div class="ui-card mb-4" id="cardNuevoPerfil">
            <div class="p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-plus-circle me-2 text-primary"></i>Crear perfil nuevo</h6>
                        <p class="text-muted fs-13 mb-0">Un perfil agrupa permisos. Se crea vacío y aquí mismo le asignas los permisos.</p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevoPerfil">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo perfil
                    </button>
                </div>
                <div class="collapse {{ request()->boolean('nuevo') || $errors->has('namerole') ? 'show' : '' }}" id="formNuevoPerfil">
                    <form method="POST" action="{{ route('admin.roles.store') }}" class="row g-3 align-items-end mt-1">
                        @csrf
                        <div class="col-md-8">
                            <label class="form-label fs-13 fw-semibold">Nombre del perfil <span class="text-danger">*</span></label>
                            <input type="text" name="namerole" value="{{ old('namerole') }}" maxlength="100" required
                                   class="form-control @error('namerole') is-invalid @enderror" placeholder="Ej. Analista Financiero, Soporte...">
                            @error('namerole') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="ui-btn-primary w-100"><i class="bi bi-save"></i> Crear perfil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" id="cardNuevoPermiso">
            <div class="p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="fw-bold text-dark mb-1"><i class="bi bi-key me-2 text-primary"></i>Crear permiso nuevo</h6>
                        <p class="text-muted fs-13 mb-0">Formato <code>modulo.recurso.accion</code> en minúsculas. Queda asignado al perfil que elijas y luego lo marcas en los demás. <a href="{{ route('admin.guia.permisos') }}" target="_blank">Ver guía</a></p>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#formNuevoPermiso">
                        <i class="bi bi-plus-lg me-1"></i> Nuevo permiso
                    </button>
                </div>
                <div class="collapse {{ $errors->has('permisoName') || $errors->has('permisoRol') ? 'show' : '' }}" id="formNuevoPermiso">
                    <form method="POST" action="{{ route('admin.permisos.store') }}" class="row g-3 align-items-end mt-1">
                        @csrf
                        <div class="col-md-5">
                            <label class="form-label fs-13 fw-semibold">Nombre del permiso <span class="text-danger">*</span></label>
                            <input type="text" name="permisoName" value="{{ old('permisoName') }}" maxlength="100" required
                                   class="form-control font-monospace @error('permisoName') is-invalid @enderror" placeholder="cartera.morosos.generarcarta">
                            @error('permisoName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fs-13 fw-semibold">Asignar al perfil <span class="text-danger">*</span></label>
                            <select name="permisoRol" required class="form-select @error('permisoRol') is-invalid @enderror">
                                <option value="" disabled @selected(!old('permisoRol'))>Seleccione...</option>
                                @foreach ($roles as $r)
                                    <option value="{{ $r->id }}" @selected(old('permisoRol') == $r->id)>{{ strtoupper($r->name) }}</option>
                                @endforeach
                            </select>
                            @error('permisoRol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="ui-btn-primary w-100"><i class="bi bi-save"></i> Crear permiso</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="ui-card mb-3 p-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group" style="max-width: 460px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" id="buscarPerfil" class="form-control" placeholder="Buscar perfil o permiso (ej. cartera, seguros.poliza)..." autocomplete="off">
                </div>
                <span class="text-muted fs-13" id="contadorPerfiles">{{ count($roles) }} perfiles</span>
            </div>
            <div class="text-muted fs-12 mt-1">Encuentra un perfil por su nombre, o los perfiles que tienen un permiso (escribe al menos 3 letras del permiso).</div>
        </div>

        <div class="accordion ui-accordion mb-5" id="accordionMatriz">
            @foreach ($roles as $i => $rol)
                @php
                    $idsAsignados = $rol->permissions->pluck('id')->toArray();
                    $totalAsignados = count($idsAsignados);
                    $totalPermisos = $permisosPorModulo->flatten()->count();
                @endphp
                @php $abrir = (int) request('rol') === (int) $rol->id; @endphp
                <div class="accordion-item shadow-sm perfil-item" id="rol-{{ $rol->id }}" data-nombre="{{ strtolower($rol->name) }}" data-permisos="{{ strtolower($rol->permissions->pluck('name')->implode('|')) }}">
                    <h2 class="accordion-header" id="mheading-{{ $i }}">
                        <button class="accordion-button {{ $abrir ? '' : 'collapsed' }} d-flex justify-content-between align-items-center"
                                type="button" data-bs-toggle="collapse" data-bs-target="#mcollapse-{{ $i }}" aria-expanded="{{ $abrir ? 'true' : 'false' }}">
                            <div class="d-flex align-items-center w-100 pe-3">
                                <i class="bi bi-shield-check text-indigo me-3 fs-4" style="color: #6366f1;"></i>
                                <span>{{ strtoupper($rol->name) }}</span>
                                <span class="ms-auto badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1 fw-medium fs-12">
                                    {{ $totalAsignados }} / {{ $totalPermisos }} permisos
                                </span>
                            </div>
                        </button>
                    </h2>

                    <div id="mcollapse-{{ $i }}" class="accordion-collapse collapse {{ $abrir ? 'show' : '' }}" data-bs-parent="#accordionMatriz">
                        <div class="accordion-body p-4 p-md-5 bg-light">
                            <form action="{{ route('admin.roles.update', $rol->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="desde_matriz" value="1">

                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center bg-white p-3 p-md-4 rounded-4 border border-light-subtle shadow-sm mb-3 gap-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Todos los permisos del sistema</h6>
                                        <p class="fs-13 text-muted mb-0">Marca o desmarca los que este rol debe tener, sin importar quién los haya creado.</p>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <div class="input-group input-group-sm w-100" style="max-width: 220px; min-width: 160px;">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" placeholder="Buscar permiso..."
                                                   oninput="filtrarPermisosMatriz({{ $i }}, this.value)">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" onclick="marcarPermisosMatriz({{ $i }}, true)">Marcar todos</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" onclick="marcarPermisosMatriz({{ $i }}, false)">Ninguno</button>
                                        <button type="submit" class="ui-btn-primary text-nowrap shadow-sm">
                                            <i class="bi bi-save me-2"></i> Actualizar
                                        </button>
                                    </div>
                                </div>

                                {{-- La cuadrícula se dibuja al desplegar el perfil (ver construirMatriz): pintar los 25
                                     perfiles x todos los permisos pesaba >5 MB de HTML, inusable en celular. --}}
                                <div id="mmatriz-{{ $i }}" data-asignados="{{ json_encode($idsAsignados) }}"></div>
                            </form>
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
            caja.addEventListener('input', function () {
                const t = caja.value.trim().toLowerCase();
                let visibles = 0;
                items.forEach(function (it) {
                    const coincide = !t || it.dataset.nombre.includes(t) || (t.length >= 3 && it.dataset.permisos.includes(t));
                    it.classList.toggle('d-none', !coincide);
                    if (coincide) visibles++;
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

        document.querySelectorAll('#accordionMatriz .accordion-collapse').forEach(function (panel) {
            const i = panel.id.replace('mcollapse-', '');
            panel.addEventListener('show.bs.collapse', function () { construirMatriz(i); });
            if (panel.classList.contains('show')) construirMatriz(i);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const nuevo = document.querySelector('#formNuevoPerfil.show');
            if (nuevo) { nuevo.scrollIntoView({ block: 'center' }); return; }
            const abierto = document.querySelector('#accordionMatriz .accordion-collapse.show');
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
