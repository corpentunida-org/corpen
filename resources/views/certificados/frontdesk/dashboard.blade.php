<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }

        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }

        .badge-radicado {
            background-color: #f1f3f5;
            color: #495057;
            font-weight: 500;
            padding: 0.5rem 0.8rem;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
        }

        .btn-pdf {
            transition: all 0.3s ease;
        }
        .btn-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2) !important;
        }
    </style>

    <div class="app-container py-4">

        {{-- Encabezado y Logout --}}
        <div class="d-flex justify-content-between align-items-center mb-5 bg-white p-4 rounded-4 shadow-sm border" style="border-color: #f0f0f0 !important;">
            <div class="d-flex align-items-center">
                <div class="symbol-label bg-pastel-primary me-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 18px;">
                    <i class="fas fa-user text-primary fs-3"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold m-0 text-dark">
                        Bienvenido, <span class="text-primary">{{ $tercero->nom_ter }} {{ $tercero->apl1 }}</span>
                    </h1>
                    <p class="text-muted fs-7 mb-0 mt-1"><i class="fas fa-id-card me-1 opacity-50"></i> NIT: {{ $tercero->cod_ter }} | <i class="fas fa-map-marker-alt ms-2 me-1 opacity-50"></i> {{ $tercero->ciudad ?? 'Ciudad no registrada' }}</p>
                </div>
            </div>
            <div>
                <form action="{{ route('certificados.frontdesk.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light shadow-sm rounded-pill px-4 py-2 fw-bold text-danger transition-all">
                        <i class="fas fa-sign-out-alt me-2"></i> Salir
                    </button>
                </form>
            </div>
        </div>

        {{-- Tarjeta Principal de Operaciones --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-transparent border-0 pt-4 pb-2 px-5">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-folder-open text-primary me-2"></i> Sus Operaciones Radicadas</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 12px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-5 border-0 py-3">No. Radicado / Lote</th>
                            <th class="border-0 py-3">Fecha</th>
                            <th class="border-0 py-3">Estado Actual</th>
                            <th class="border-0 py-3 text-center">Líneas Asociadas</th>
                            <th class="border-0 py-3 text-end pe-5">Certificado</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($operaciones as $operacion)
                            <tr class="bg-white">
                                <td class="ps-5">
                                    <div class="d-flex flex-column gap-1">
                                        <div class="badge-radicado fw-bold" style="width: fit-content;">
                                            <i class="fas fa-hashtag me-2 text-primary opacity-50"></i>
                                            {{ $operacion->numero_radicado }}
                                        </div>
                                        {{-- AQUÍ AGREGAMOS LA INFORMACIÓN DEL LOTE --}}
                                        <div class="text-muted" style="font-size: 0.8rem; margin-left: 5px;">
                                            <i class="fas fa-layer-group me-1 opacity-50"></i> Lote: BLQ-{{ str_pad($operacion->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold small">{{ $operacion->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $ultimoEstado = $operacion->estados->sortByDesc('created_at')->first();
                                        $estadoStr = $ultimoEstado && $ultimoEstado->estado ? $ultimoEstado->estado->nombre : 'En Trámite';

                                        $clasePastel = match($estadoStr) {
                                            'Aprobado', 'Completado' => 'bg-pastel-success',
                                            'En Trámite', 'Pendiente' => 'bg-pastel-secondary',
                                            default => 'bg-pastel-primary'
                                        };
                                    @endphp
                                    <span class="badge {{ $clasePastel }} px-4 py-2 rounded-pill fw-bold fs-8">
                                        {{ strtoupper($estadoStr) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border rounded-circle" style="padding: 0.6rem 0.8rem; font-size: 0.9rem;" title="{{ $operacion->lineas->count() }} líneas de crédito registradas">
                                        {{ $operacion->lineas->count() }}
                                    </span>
                                </td>
                                {{-- AQUÍ AGREGAMOS EL BOTÓN DE VER PDF --}}
                                <td class="text-end pe-5">
                                    <a href="{{ route('certificados.operaciones.pdf_individual', $operacion->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill fw-bold shadow-sm btn-pdf">
                                        <i class="fas fa-file-pdf me-1"></i> Ver PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10">
                                    <div class="text-center px-4">
                                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="opacity-20 mb-4" alt="Sin operaciones">
                                        <h4 class="fw-bold text-muted">Sin operaciones activas</h4>
                                        <p class="text-gray-400 fw-semibold">Actualmente no posee trámites registrados en el sistema.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-base-layout>
