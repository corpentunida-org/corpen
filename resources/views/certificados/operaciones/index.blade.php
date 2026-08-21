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
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h1 class="h2 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Motor de Operaciones</h1>
                <p class="text-muted mt-1 mb-0">Gestión y matriz principal de cartera SIA</p>
            </div>
            <a href="{{ route('certificados.operaciones.index') }}" class="btn btn-light shadow-sm rounded-pill px-4 py-2 fw-bold text-muted">
                <i class="fas fa-sync-alt me-2"></i> Actualizar
            </a>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabla Principal --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-5 border-0 py-3">Radicado & Bloque</th>
                            <th class="border-0 py-3">Cliente (Tercero)</th>
                            <th class="border-0 py-3">Ref. Factura</th>
                            <th class="border-0 py-3">Estado Actual</th>
                            <th class="border-0 py-3">Fecha Creación</th>
                            <th class="border-0 text-end pe-5 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($operaciones as $operacion)
                        <tr class="bg-white">
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-3">
                                        <div class="symbol-label bg-pastel-primary" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                                            <i class="fas fa-file-invoice text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-gray-800 fs-6">{{ $operacion->numero_radicado ?? 'N/A' }}</div>
                                        <div class="text-muted small fw-semibold">
                                            <i class="fas fa-cube me-1 opacity-50"></i> {{ $operacion->numero_bloque }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($operacion->tercero)
                                    <div class="fw-bold text-dark small">{{ $operacion->tercero->nom_ter }} {{ $operacion->tercero->apl1 }}</div>
                                    <div class="text-muted fs-9">NIT: {{ $operacion->tercero->cod_ter }}</div>
                                @else
                                    <span class="badge bg-pastel-warning px-3 py-2 rounded-pill"><i class="fas fa-exclamation-triangle me-1"></i> Sin Tercero</span>
                                @endif
                            </td>
                            <td>
                                @if($operacion->factura)
                                    <div class="badge-radicado">
                                        <i class="fas fa-hashtag me-1 text-primary opacity-50"></i>
                                        <span>{{ $operacion->factura->id }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fs-8">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $ultimoEstado = $operacion->estados->sortByDesc('created_at')->first();
                                    $estadoNombre = $ultimoEstado && $ultimoEstado->estado ? $ultimoEstado->estado->nombre : 'Pendiente';
                                    
                                    // Colores dinámicos de ejemplo
                                    $clasePastel = match($estadoNombre) {
                                        'Aprobado', 'Completado' => 'bg-pastel-success',
                                        'Rechazado' => 'bg-pastel-warning',
                                        'Pendiente' => 'bg-pastel-secondary',
                                        default => 'bg-pastel-primary'
                                    };
                                @endphp
                                <span class="badge {{ $clasePastel }} rounded-pill px-4 py-2 fw-bold fs-8">
                                    <i class="fas fa-info-circle me-1"></i> {{ strtoupper($estadoNombre) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-gray-800 fw-bold small">{{ $operacion->created_at->format('d/m/Y') }}</span>
                                    <span class="text-muted fs-9">{{ $operacion->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                            <td class="text-end pe-5">
                                <a href="{{ route('certificados.operaciones.show', $operacion->id) }}" 
                                   class="btn btn-icon btn-light-primary btn-sm rounded-circle shadow-sm" 
                                   title="Ver Detalle y Trazabilidad">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <div class="text-center px-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="opacity-20 mb-4" alt="Sin datos">
                                    <h4 class="fw-bold text-muted">No se encontraron operaciones</h4>
                                    <p class="text-gray-400 fw-semibold">El motor de SIA Cartera está vacío actualmente.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($operaciones->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-5">
                    {{ $operaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>