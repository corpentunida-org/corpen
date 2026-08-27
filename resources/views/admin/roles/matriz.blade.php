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
            <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i> Volver a Gestión de Roles
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
                A diferencia de "Gestión de Roles", aquí cada rol muestra <strong>todos</strong> los permisos del
                sistema (agrupados por módulo), no solo los que fueron creados directamente para ese rol.
            </p>
        </div>

        <div class="accordion ui-accordion mb-5" id="accordionMatriz">
            @foreach ($roles as $i => $rol)
                @php
                    $idsAsignados = $rol->permissions->pluck('id')->toArray();
                    $totalAsignados = count($idsAsignados);
                    $totalPermisos = $permisosPorModulo->flatten()->count();
                @endphp
                <div class="accordion-item shadow-sm">
                    <h2 class="accordion-header" id="mheading-{{ $i }}">
                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                                type="button" data-bs-toggle="collapse" data-bs-target="#mcollapse-{{ $i }}" aria-expanded="false">
                            <div class="d-flex align-items-center w-100 pe-3">
                                <i class="bi bi-shield-check text-indigo me-3 fs-4" style="color: #6366f1;"></i>
                                <span>{{ strtoupper($rol->name) }}</span>
                                <span class="ms-auto badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-1 fw-medium fs-12">
                                    {{ $totalAsignados }} / {{ $totalPermisos }} permisos
                                </span>
                            </div>
                        </button>
                    </h2>

                    <div id="mcollapse-{{ $i }}" class="accordion-collapse collapse" data-bs-parent="#accordionMatriz">
                        <div class="accordion-body p-4 p-md-5 bg-light">
                            <form action="{{ route('admin.roles.update', $rol->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center bg-white p-4 rounded-4 border border-light-subtle shadow-sm mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">Todos los permisos del sistema</h6>
                                        <p class="fs-13 text-muted mb-0">Marca o desmarca los que este rol debe tener, sin importar quién los haya creado.</p>
                                    </div>
                                    <button type="submit" class="ui-btn-primary mt-3 mt-sm-0 shadow-sm">
                                        <i class="bi bi-save me-2"></i> Actualizar Permisos
                                    </button>
                                </div>

                                @foreach ($permisosPorModulo as $modulo => $permisosDelModulo)
                                    <div class="modulo-titulo">{{ $modulo }}</div>
                                    <div class="row g-3">
                                        @foreach ($permisosDelModulo as $permiso)
                                            <div class="col-lg-6 col-xl-4">
                                                <div class="ui-card p-3 h-100 border-0">
                                                    <div class="form-check form-switch ui-switch d-flex align-items-center justify-content-between w-100 m-0 p-0">
                                                        <label class="form-check-label user-select-none text-truncate pe-3" for="mchk-{{ $i }}-{{ $permiso->id }}">
                                                            {{ $permiso->name }}
                                                        </label>
                                                        <input type="checkbox" name="permissions[]" value="{{ $permiso->id }}"
                                                            class="form-check-input flex-shrink-0 m-0" id="mchk-{{ $i }}-{{ $permiso->id }}"
                                                            @if (in_array($permiso->id, $idsAsignados)) checked @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-base-layout>
