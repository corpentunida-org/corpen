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
