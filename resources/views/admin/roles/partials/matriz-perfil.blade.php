{{-- Requiere: $rol, $i (índice único del perfil), $parent (acordeón padre o null), $permisosPorModulo. --}}
                @php
                    $idsAsignados = $rol->permissions->pluck('id')->toArray();
                    $totalAsignados = count($idsAsignados);
                    $totalPermisos = $permisosPorModulo->flatten()->count();
                @endphp
                @php $abrir = (int) request('rol') === (int) $rol->id; @endphp
                <div class="accordion-item shadow-sm perfil-item" id="rol-{{ $rol->id }}" data-nombre="{{ strtolower($rol->name) }}">
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

                    <div id="mcollapse-{{ $i }}" class="accordion-collapse collapse {{ $abrir ? 'show' : '' }}" @if ($parent) data-bs-parent="{{ $parent }}" @endif>
                        <div class="accordion-body p-4 p-md-5 bg-light">
                            @php
                                $nUsuarios = (int) ($usuariosPorRol[$rol->id] ?? 0);
                                $protegido = (int) $rol->id === 13 || strtolower($rol->name) === 'asociado';
                            @endphp
                            <div class="d-flex flex-wrap align-items-end gap-3 mb-3 pb-3 border-bottom">
                                <form action="{{ route('admin.roles.nombre', $rol->id) }}" method="POST" class="d-flex flex-wrap align-items-end gap-2">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="form-label fs-12 fw-semibold mb-1">Nombre del perfil</label>
                                        <input type="text" name="nombre" value="{{ $rol->name }}" maxlength="100" required class="form-control form-control-sm" style="min-width: 220px;">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i> Guardar nombre</button>
                                </form>
                                <div class="ms-auto d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="alternarUsuariosPerfil(this)" data-url="{{ route('admin.roles.usuarios', $rol->id) }}" aria-expanded="false" @disabled($nUsuarios === 0)>
                                        <i class="bi bi-people me-1"></i> Ver usuarios ({{ $nUsuarios }})
                                    </button>
                                    @if ($protegido)
                                        <button type="button" class="btn btn-sm btn-outline-danger" disabled title="Lo usa el registro de asociados"><i class="bi bi-trash me-1"></i> Eliminar perfil</button>
                                    @elseif ($nUsuarios > 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger" disabled title="Asigna otro perfil a esos usuarios primero"><i class="bi bi-trash me-1"></i> Eliminar perfil</button>
                                    @else
                                        <form action="{{ route('admin.roles.eliminar', $rol->id) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar el perfil {{ strtoupper($rol->name) }}? Ningún usuario lo tiene asignado. Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i> Eliminar perfil</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="panel-usuarios d-none bg-white border rounded-3 p-3 mb-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div class="fw-semibold fs-13"><i class="bi bi-people me-1 text-primary"></i>Usuarios con el perfil {{ strtoupper($rol->name) }} <span class="text-muted fw-normal total-usuarios"></span></div>
                                    <div class="input-group input-group-sm" style="max-width: 300px;">
                                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                        <input type="search" class="form-control buscar-usuarios" placeholder="Buscar nombre, cédula o correo..." autocomplete="off"
                                               onkeydown="if (event.key === 'Enter') event.preventDefault()">
                                    </div>
                                </div>
                                <div style="max-height: 320px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle mb-0 fs-13">
                                        <thead><tr><th>Nombre</th><th>Cédula</th><th>Correo</th><th>Tipo</th></tr></thead>
                                        <tbody class="lista-usuarios"></tbody>
                                    </table>
                                </div>
                                <div class="text-muted fs-13 py-2 d-none mensaje-usuarios"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-none mas-usuarios">Cargar más</button>
                            </div>
                            <form action="{{ route('admin.roles.area', $rol->id) }}" method="POST" class="d-flex flex-wrap align-items-end gap-2 mb-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="form-label fs-12 fw-semibold mb-1">Área de este perfil (opcional)</label>
                                    <select name="area" class="form-select form-select-sm" style="min-width: 220px;">
                                        <option value="" @selected(!$rol->area)>(sin área)</option>
                                        @foreach ($areas as $a) <option value="{{ $a }}" @selected(strtolower((string) $rol->area) === $a)>{{ strtoupper($a) }}</option> @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-diagram-3 me-1"></i> Guardar área</button>
                                <span class="text-muted fs-12">Mueve el perfil a otra área; no cambia permisos.</span>
                            </form>
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
