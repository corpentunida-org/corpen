<x-base-layout>
    @push('styles')
        <style>
            .hover-lift:hover { background-color: #fbfcfd !important; transition: all 0.2s ease; }
            .file-icon-bg { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: #f1f5f9; color: #475569; }
            .search-container { background-color: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 1.5rem 2rem; }
            .input-search-custom { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.3s; }
            .input-search-custom:focus-within { border-color: #6366f1; background-color: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
            .btn-action-pill { border: 1px solid #e2e8f0; border-radius: 50px; padding: 0.35rem 0.8rem; background: #fff; color: #64748b; font-size: 0.85rem; transition: all 0.2s; }
            .btn-action-pill:hover { background: #f8fafc; color: #4f46e5; border-color: #4f46e5; }
        </style>
    @endpush

    <div class="container-fluid py-4 px-lg-5">
        {{-- Encabezado --}}
        <div class="d-flex align-items-center justify-content-between mb-5">
            <div>
                <h2 class="fw-bold text-dark m-0" style="letter-spacing: -1px;">Expedientes de Empleados</h2>
                <p class="text-muted small m-0">Repositorio central de documentos y soportes del personal.</p>
            </div>
            <a href="{{ route('archivo.gdodocsempleados.create') }}" class="btn btn-dark rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-cloud-upload me-2"></i> Subir Documento
            </a>
        </div>

        {{-- Notificaciones --}}
        @if (session('success'))
            <div class="alert alert-soft-success border-0 mb-4 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            {{-- Buscador --}}
            <div class="search-container">
                <form method="GET" action="{{ route('archivo.gdodocsempleados.index') }}">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="input-group input-search-custom px-3">
                                <span class="input-group-text bg-transparent border-0 text-muted">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" name="search" class="form-control bg-transparent border-0 py-2" 
                                       placeholder="Buscar por fecha, observaciones o ruta..." value="{{ request('search') }}">
                            </div>
                        </div>
                        @if(request('search'))
                            <div class="col-auto">
                                <a href="{{ route('archivo.gdodocsempleados.index') }}" class="btn btn-link text-danger fw-bold text-decoration-none small pt-2">
                                    <i class="bi bi-x-circle-fill"></i> Limpiar
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Empleado</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Tipo / Documento</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase text-center">Fecha Subida</th>
                            <th class="py-3 text-muted small fw-bold text-uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gdodocsempleados as $doc)
                            <tr class="hover-lift">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-soft rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 35px; height: 35px; background: #eef2ff;">
                                            {{ substr($doc->empleado->apellido1 ?? 'E', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block">
                                                {{ $doc->empleado->nombre1 ?? 'N/A' }} {{ $doc->empleado->apellido1 ?? '' }}
                                            </span>
                                            <small class="text-muted">ID: {{ $doc->empleado->cedula ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-bg me-3">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </div>
                                        <div>
                                            <span class="fw-medium text-dark d-block">{{ $doc->tipoDocumento->nombre ?? 'Sin Tipo' }}</span>
                                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">{{ $doc->observaciones }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="small fw-medium text-slate-600">
                                        {{ \Carbon\Carbon::parse($doc->fecha_subida)->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="py-3 pe-4 text-end">
                                    <div class="btn-group gap-2">
                                        {{-- Ver Archivo (Nueva pestaña) --}}
                                        <a href="{{ route('archivo.gdodocsempleados.verArchivo', $doc->id) }}" target="_blank" class="btn-action-pill text-decoration-none">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        {{-- Descargar --}}
                                        <a href="{{ route('archivo.gdodocsempleados.download', $doc->id) }}" class="btn-action-pill text-decoration-none">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        {{-- Menú de opciones --}}
                                        <div class="dropdown">
                                            <button class="btn-action-pill" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                                <li><a class="dropdown-item py-2 small" href="{{ route('archivo.gdodocsempleados.show', $doc->id) }}"><i class="bi bi-info-circle me-2"></i> Detalles</a></li>
                                                <li><a class="dropdown-item py-2 small" href="{{ route('archivo.gdodocsempleados.edit', $doc->id) }}"><i class="bi bi-pencil me-2"></i> Editar</a></li>
                                                <li><hr class="dropdown-divider opacity-50"></li>
                                                <li>
                                                    <form action="{{ route('archivo.gdodocsempleados.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item py-2 small text-danger"><i class="bi bi-trash3 me-2"></i> Eliminar</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-folder2-open display-1 text-light"></i>
                                        <p class="text-muted mt-3 mb-0">No se encontraron documentos registrados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($gdodocsempleados->hasPages())
                <div class="p-4 border-top bg-light bg-opacity-50">
                    {{ $gdodocsempleados->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>