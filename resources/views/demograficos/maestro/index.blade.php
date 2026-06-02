<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag"><i class="fas fa-table me-1"></i> Módulo de Demografía</span>
                <h1 class="main-title">Directorio Maestro Geográfico</h1>
                <p class="main-subtitle">Gestión manual de registros demográficos (Países, Regiones, Ciudades).</p>
            </div>
            <div class="header-actions">
                <button class="btn-ghost-corporate me-2" onclick="window.print()">
                    <i class="fas fa-print"></i> <span>Imprimir</span>
                </button>
                <a href="{{ route('demograficos.maestro.create') }}" class="btn-corporate-black">
                    <i class="fas fa-plus"></i> <span>Nuevo Registro</span>
                </a>
            </div>
        </header>

        <div class="card shadow-sm border-0 mb-4 rounded-3">
            <div class="card-body p-3 bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3 align-items-center w-50">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Buscar por Código ISO o Nombre..." aria-label="Buscar">
                    </div>
                </div>
            </div>
        </div>

        <div class="show-card-corp shadow-sm">
            <div class="show-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0 ecm-table">
                        <thead class="table-light text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <tr>
                                <th class="ps-4" style="width: 15%;">Código ISO</th>
                                <th style="width: 40%;">Nombre del País</th>
                                <th style="width: 30%;">Estadísticas</th>
                                <th class="text-center pe-4" style="width: 15%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paises as $pais)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="radicado-icon me-2 bg-light-primary text-primary">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <span class="fw-bold text-dark d-block">{{ $pais->codigo_iso }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $pais->nombre }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('demograficos.maestro.show', $pais->codigo_iso) }}" class="text-decoration-none" title="Ver listado de regiones">
                                        <span class="badge bg-light text-primary border border-primary p-2 me-1 custom-hover-badge" style="transition: all 0.2s;">
                                            {{ $pais->regiones_count ?? 0 }} Regiones <i class="fas fa-external-link-alt ms-1 opacity-50"></i>
                                        </span>
                                    </a>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border rounded-circle" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('demograficos.maestro.show', $pais->codigo_iso) }}">
                                                    <i class="fas fa-eye text-primary me-2"></i> Ver Detalles
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('demograficos.maestro.edit', $pais->codigo_iso) }}">
                                                    <i class="fas fa-pen text-warning me-2"></i> Editar
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('demograficos.maestro.destroy', $pais->codigo_iso) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger border-0 bg-transparent w-100 text-start">
                                                        <i class="fas fa-trash me-2"></i> Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-globe-americas mb-3" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mb-0">No hay países registrados en el sistema.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Paginación de Laravel --}}
            @if(isset($paises) && $paises->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                    {{ $paises->links() }}
                </div>
            @endif
            
        </div>
    </div>
    
    @includeIf('asociados.partials.styles')

    <style>
        .custom-hover-badge:hover {
            background-color: #0d6efd !important;
            color: #ffffff !important;
            cursor: pointer;
        }
    </style>
</x-base-layout>