<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-danger { background-color: #ffebee !important; color: #c62828 !important; border: none; }
        
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }
    </style>

    <div class="app-container py-4">
        
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="symbol-label bg-pastel-warning me-4 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 18px;">
                    <i class="fas fa-database text-warning fs-3"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Ingesta ERP (Staging)</h1>
                    <p class="text-muted mt-1 mb-0">Recepción de Lotes Crudos e Inyección al Motor</p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                {{-- Botón para procesar los pendientes --}}
                <form action="{{ route('certificados.ingesta.inyectar') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-pastel-primary shadow-sm rounded-pill px-4 py-2 fw-bold" 
                            onclick="return confirm('¿Confirma que desea inyectar todos los lotes pendientes al motor?');">
                        <i class="fas fa-play me-2"></i> Procesar Lotes ({{ $totalPendientes }})
                    </button>
                </form>
            </div>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
        @endif

        {{-- Tabla de Staging --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-2 border-bottom-0">
                <h6 class="fw-bold text-muted mb-0"><i class="fas fa-list me-2"></i> Historial de Lotes Recibidos</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 10px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-4 border-0 py-3">ID Factura</th>
                            <th class="border-0 py-3">Cliente / Tercero</th>
                            <th class="border-0 py-3 text-end">Valor Neto</th>
                            <th class="border-0 py-3">Estado ETL</th>
                            <th class="border-0 py-3">Fecha Recepción</th>
                            <th class="border-0 text-end pe-4 py-3">Excluir</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($lotesCrudos as $lote)
                        <tr class="bg-white">
                            <td class="ps-4 fw-bold text-dark">
                                <i class="fas fa-hashtag text-muted me-1 opacity-50"></i> {{ $lote->id_factura ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark fs-7">{{ $lote->nombre_tercero ?? 'Desconocido' }}</div>
                                <div class="text-muted fs-8">NIT: {{ $lote->tercero ?? 'N/A' }}</div>
                            </td>
                            <td class="text-end fw-bold text-gray-800">
                                ${{ number_format((float)$lote->valor, 2) }}
                            </td>
                            <td>
                                @if($lote->anular == 1)
                                    <span class="badge bg-pastel-danger rounded-pill px-3 py-2"><i class="fas fa-ban me-1"></i> Anulado</span>
                                @elseif($lote->estado == 'PROCESADO')
                                    <span class="badge bg-pastel-success rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i> Procesado</span>
                                @else
                                    <span class="badge bg-pastel-warning rounded-pill px-3 py-2 text-dark"><i class="fas fa-clock me-1"></i> Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <div class="fs-8 text-muted">{{ $lote->fecha_ad ?? $lote->created_at }}</div>
                            </td>
                            <td class="text-end pe-4">
                                @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                    <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-icon btn-light-danger btn-sm rounded-circle shadow-sm" title="Anular Lote Defectuoso" onclick="return confirm('¿Seguro que desea excluir este registro de la inyección?');">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-icon btn-light btn-sm rounded-circle" disabled><i class="fas fa-lock text-muted"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay lotes en la tabla de staging.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($lotesCrudos->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-5">
                    {{ $lotesCrudos->links() }}
                </div>
            @endif
        </div>

    </div>
</x-base-layout>