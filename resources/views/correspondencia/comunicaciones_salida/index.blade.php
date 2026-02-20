<x-base-layout>
    <style>
        /* Paleta de Colores Pasteles Soft UI */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        
        .table-hover tbody tr:hover {
            background-color: #fcfdfe !important;
            transition: all 0.2s ease;
        }

        .card-custom {
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #f0f0f0;
        }

        .btn-pastel-primary {
            background-color: #4a90e2;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-pastel-primary:hover {
            background-color: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
            color: white;
        }

        .search-minimal {
            background-color: #f8f9fa;
            border: 1px solid #ececec;
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .search-minimal:focus {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #4a90e2;
        }

        .badge-radicado {
            background-color: #f1f3f5;
            color: #495057;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
        }
    </style>

    <div class="app-container py-4">
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-dot fw-semibold fs-7 my-1">
                        <li class="breadcrumb-item text-muted">Correspondencia</li>
                        <li class="breadcrumb-item text-primary">Salidas</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Comunicaciones de Salida</h1>
            </div>
            <a href="{{ route('correspondencia.comunicaciones-salida.create') }}" class="btn btn-pastel-primary shadow-sm rounded-pill px-5 py-3 fw-bold">
                <i class="fas fa-paper-plane me-2"></i> Nuevo Oficio
            </a>
        </div>

        {{-- Barra de Filtros Minimalista --}}
        <div class="card card-custom mb-4 border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('correspondencia.comunicaciones-salida.index') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-0 pe-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control search-minimal" placeholder="Buscar por oficio, radicado o asunto...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="estado" class="form-select search-minimal">
                            <option value="">Todos los estados</option>
                            <option value="Generado" {{ request('estado') == 'Generado' ? 'selected' : '' }}>Generado</option>
                            <option value="Enviado por Email" {{ request('estado') == 'Enviado por Email' ? 'selected' : '' }}>Enviado por Email</option>
                            <option value="Notificado Físicamente" {{ request('estado') == 'Notificado Físicamente' ? 'selected' : '' }}>Notificado Físicamente</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">Filtrar Resultados</button>
                        
                        @if(request()->filled('search') || request()->filled('estado'))
                            <a href="{{ route('correspondencia.comunicaciones-salida.index') }}" class="btn btn-light-danger btn-icon rounded-circle" title="Limpiar Filtros">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabla Principal --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-5 border-0 py-3">Oficio & Fecha</th>
                            <th class="border-0 py-3">Radicado Origen</th>
                            <th class="border-0 py-3">Estado Actual</th>
                            <th class="border-0 py-3">Firmado por</th>
                            <th class="border-0 text-end pe-5 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($comunicaciones as $com)
                        <tr class="bg-white">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-3">
                                        <div class="symbol-label bg-pastel-primary">
                                            <i class="fas fa-file-signature text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">{{ $com->nro_oficio_salida }}</div>
                                        <div class="text-muted small fw-semibold">
                                            {{ $com->fecha_generacion ? $com->fecha_generacion->format('d/m/Y') : 'Fecha no definida' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{-- Lógica corregida para Radicado Origen --}}
                                @if($com->correspondencia)
                                    <div class="badge-radicado">
                                        <i class="fas fa-link me-2 text-primary opacity-50"></i>
                                        <span>{{ $com->correspondencia->nro_radicado ?? $com->correspondencia->id_radicado }}</span>
                                    </div>
                                @else
                                    <div class="badge bg-pastel-warning px-3 py-2 rounded-pill">
                                        <i class="fas fa-exclamation-triangle me-1"></i> ID: {{ $com->id_correspondencia }} (No vinculado)
                                    </div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $pastelClass = [
                                        'Generado' => 'bg-pastel-primary',
                                        'Enviado por Email' => 'bg-pastel-info',
                                        'Notificado Físicamente' => 'bg-pastel-success'
                                    ][$com->estado_envio] ?? 'bg-pastel-secondary';
                                @endphp
                                <span class="badge {{ $pastelClass }} rounded-pill px-4 py-2 fw-bold fs-8">
                                    <i class="fas fa-check-circle me-1"></i> {{ strtoupper($com->estado_envio) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold small">{{ $com->usuario->name ?? 'Sistema' }}</span>
                                    <span class="text-muted fs-9">Responsable</span>
                                </div>
                            </td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('correspondencia.comunicaciones-salida.descargarPdf', $com->id_respuesta) }}" 
                                       target="_blank" 
                                       class="btn btn-icon btn-light-danger btn-sm rounded-circle shadow-sm" 
                                       title="Descargar PDF Oficial">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.comunicaciones-salida.show', $com) }}" 
                                       class="btn btn-icon btn-light-primary btn-sm rounded-circle shadow-sm" 
                                       title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('correspondencia.comunicaciones-salida.edit', $com) }}" 
                                       class="btn btn-icon btn-light-warning btn-sm rounded-circle shadow-sm" 
                                       title="Editar Registro">
                                        <i class="fas fa-pen fs-8"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10">
                                <div class="text-center px-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="opacity-20 mb-4">
                                    <h4 class="fw-bold text-muted">No se encontraron resultados</h4>
                                    <p class="text-gray-400 fw-semibold">Intente ajustar los filtros o cree una nueva salida oficial.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($comunicaciones->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-6 px-10">
                    {{ $comunicaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>