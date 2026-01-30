<x-base-layout>
    @push('styles')
        <style>
            /* Transiciones suaves y efectos de elevación */
            .hover-lift { transition: all 0.2s ease-in-out; }
            .hover-lift:hover { 
                background-color: #f8fafc !important; 
                transform: translateY(-1px);
                box-shadow: inset 0 0 0 1px #e2e8f0;
            }
            
            /* Contenedor de filtros con elevación sutil */
            .filter-bar {
                background-color: #ffffff;
                border-bottom: 1px solid #f1f5f9;
                padding: 1.25rem 2rem;
            }

            /* Inputs modernos con foco suave */
            .input-group-custom {
                background-color: #f1f5f9;
                border: 1px solid transparent;
                border-radius: 10px;
                transition: all 0.3s;
            }

            .input-group-custom:focus-within {
                border-color: #6366f1;
                background-color: #fff;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.08);
            }

            .form-select-custom {
                border: none;
                background-color: transparent;
                font-size: 0.875rem;
                font-weight: 500;
                color: #334155;
                cursor: pointer;
            }

            /* Badges estilo "Pill" corporativo */
            .badge-category-fixed {
                background-color: #e0e7ff !important;
                color: #4338ca !important;
                border: 1px solid #c7d2fe !important;
                padding: 0.4rem 0.75rem;
                font-weight: 600;
                font-size: 0.7rem;
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                border-radius: 6px; /* Diseño más corporativo que circular */
                text-transform: uppercase;
                letter-spacing: 0.025em;
            }

            .badge-empty {
                background-color: #f8fafc;
                color: #94a3b8;
                border: 1px solid #e2e8f0;
                padding: 0.4rem 0.75rem;
                font-size: 0.7rem;
                font-weight: 500;
                border-radius: 6px;
            }

            /* Cabeceras de tabla minimalistas */
            .table-modern thead th {
                background-color: #f8fafc;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                color: #64748b;
                border-top: none;
                border-bottom: 1px solid #e2e8f0;
                padding: 1rem;
            }

            /* Grupo de botones minimalista */
            .action-pills {
                display: inline-flex;
                gap: 0.5rem;
                background: #f1f5f9;
                padding: 0.25rem;
                border-radius: 8px;
            }
            
            .btn-pill {
                padding: 0.4rem;
                border-radius: 6px;
                line-height: 1;
                color: #64748b;
                transition: all 0.2s;
            }
            
            .btn-pill:hover { background: white; color: #4f46e5; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        </style>
    @endpush

    <div class="container-fluid py-5 px-lg-5">
        {{-- Header con jerarquía clara --}}
        <div class="d-flex align-items-end justify-content-between mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item small"><a href="#" class="text-decoration-none text-muted">Archivo</a></li>
                        <li class="breadcrumb-item small active">Configuración</li>
                    </ol>
                </nav>
                <h2 class="fw-bold text-slate-900 m-0" style="letter-spacing: -1.5px;">Tipos de Documento</h2>
                <p class="text-muted m-0 small">Arquitectura y organización de la base documental.</p>
            </div>
            <a href="{{ route('archivo.gdotipodocumento.create') }}" class="btn btn-dark btn-lg rounded-3 px-4 shadow-sm fw-bold">
                <i class="bi bi-plus-lg me-2"></i> Crear Nuevo
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            {{-- SECCIÓN DE FILTROS INTEGRADOS --}}
            <div class="filter-bar">
                <form method="GET" action="{{ route('archivo.gdotipodocumento.index') }}" id="filterForm">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-4">
                            <div class="input-group input-group-custom px-3">
                                <span class="input-group-text bg-transparent border-0 text-muted p-0 me-2">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" name="search" class="form-control bg-transparent border-0 py-2 ps-0" 
                                       placeholder="Buscar documento..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="input-group input-group-custom px-3">
                                <span class="input-group-text bg-transparent border-0 text-muted p-0 me-2">
                                    <i class="bi bi-tag"></i>
                                </span>
                                <select name="categoria_id" class="form-select form-select-custom py-2 ps-0" onchange="this.form.submit()">
                                    <option value="">Todas las Categorías</option>
                                    @foreach($todasCategorias as $cat)
                                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if(request('search') || request('categoria_id'))
                            <div class="col-lg-auto">
                                <a href="{{ route('archivo.gdotipodocumento.index') }}" class="btn btn-link text-danger text-decoration-none small fw-bold p-0">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Resetear
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla Modernizada --}}
            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Documento</th>
                            <th>Estructura / Categoría</th>
                            <th class="text-end pe-4">Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                            <tr class="hover-lift">
                                <td class="ps-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-indigo-soft text-indigo p-2 rounded-2 me-3 d-flex align-items-center justify-content-center" style="background: #eef2ff; width: 42px; height: 42px;">
                                            <i class="bi bi-file-earmark-text text-indigo fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block mb-0 h6">{{ $tipo->nombre }}</span>
                                            <code class="text-muted" style="font-size: 0.65rem;">REF: {{ str_pad($tipo->id, 5, '0', STR_PAD_LEFT) }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($tipo->categoria)
                                        <div class="badge-category-fixed">
                                            <i class="bi bi-layers-half"></i>
                                            {{ $tipo->categoria->nombre }}
                                        </div>
                                    @else
                                        <span class="badge-empty">
                                            <i class="bi bi-dash-circle me-1"></i> Sin Asignar
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="action-pills">
                                        <a href="{{ route('archivo.gdotipodocumento.show', $tipo->id) }}" class="btn btn-pill" title="Ver detalle">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('archivo.gdotipodocumento.edit', $tipo->id) }}" class="btn btn-pill" title="Modificar">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="bi bi-inbox text-light display-1"></i>
                                        <p class="text-muted mt-3 mb-0">No se encontraron registros activos.</p>
                                        <small class="text-muted">Intenta cambiar los términos de búsqueda.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($tipos->hasPages())
                <div class="p-4 border-top bg-white d-flex justify-content-center">
                    {{ $tipos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>