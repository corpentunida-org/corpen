<x-base-layout>
    <style>
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-secondary { background-color: #f5f5f5 !important; color: #616161 !important; border: none; }
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        
        .nav-tabs-custom .nav-link {
            border: none; color: #616161; font-weight: 600; padding: 1rem 1.5rem; border-bottom: 3px solid transparent; transition: all 0.3s;
        }
        .nav-tabs-custom .nav-link.active { color: #0052cc; background: transparent; border-bottom: 3px solid #0052cc; }
        
        .json-viewer {
            background-color: #282c34;
            color: #abb2bf;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.8rem;
            border-radius: 10px;
            padding: 10px;
            max-height: 150px;
            overflow-y: auto;
        }
    </style>

    <div class="app-container py-4">
        
        {{-- Encabezado --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="symbol-label bg-pastel-primary me-4 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 18px;">
                    <i class="fas fa-shield-alt text-primary fs-3"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Bitácora de Auditoría</h1>
                    <p class="text-muted mt-1 mb-0">Trazabilidad Absoluta del Motor SIA</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light shadow-sm rounded-pill px-4 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalEvento">
                    <i class="fas fa-plus me-1"></i> Tipo Evento
                </button>
                <button type="button" class="btn btn-pastel-primary shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalOrigen">
                    <i class="fas fa-plus me-1"></i> Nuevo Origen
                </button>
            </div>
        </div>

        @if(session('success')) <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4">{{ session('error') }}</div> @endif

        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white pt-3 pb-0 border-bottom px-4" style="border-radius: 20px 20px 0 0;">
                <ul class="nav nav-tabs nav-tabs-custom" id="auditTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-logs"><i class="fas fa-list-alt me-2"></i> Historial Transaccional</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-catalogos"><i class="fas fa-cogs me-2"></i> Orígenes y Eventos (IDs)</button></li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    
                    {{-- TAB 1: LOGS --}}
                    <div class="tab-pane fade show active" id="tab-logs">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="text-muted small text-uppercase bg-light">
                                    <tr>
                                        <th class="ps-4 border-0 py-3">Fecha y Hora</th>
                                        <th class="border-0 py-3">Actor / IP</th>
                                        <th class="border-0 py-3">Evento & Origen</th>
                                        <th class="border-0 py-3">Bloque Afectado</th>
                                        <th class="border-0 pe-4 py-3">Detalle (Payload)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr class="border-bottom">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark fs-7">{{ $log->created_at->format('d/m/Y') }}</div>
                                            <div class="text-muted fs-8">{{ $log->created_at->format('H:i:s.v') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-primary fs-7"><i class="fas fa-user-circle me-1"></i> {{ $log->usuario->name ?? 'Sistema Automático' }}</div>
                                            <div class="text-muted fs-8"><i class="fas fa-network-wired me-1"></i> {{ $log->ip ?? '127.0.0.1' }}</div>
                                        </td>
                                        <td>
                                            <div class="badge bg-pastel-secondary text-dark rounded-pill px-3 py-1 mb-1">{{ $log->eventoAuditoria->nombre ?? 'ID: '.$log->id_car_sia_eventos_auditoria }}</div>
                                            <div class="fs-8 text-muted"><i class="fas fa-sign-in-alt me-1"></i> Vía: {{ $log->origenEvento->nombre ?? 'Desconocido' }}</div>
                                        </td>
                                        <td><code class="text-dark fw-bold">{{ $log->numero_bloque }}</code></td>
                                        <td class="pe-4">
                                            @if($log->detalles_ejecucion)
                                                <div class="json-viewer">{{ json_encode($log->detalles_ejecucion, JSON_PRETTY_PRINT) }}</div>
                                            @else
                                                <span class="text-muted fs-8">Sin payload adicional</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No existen registros de auditoría aún.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($logs->hasPages())
                            <div class="mt-4">{{ $logs->links() }}</div>
                        @endif
                    </div>

                    {{-- TAB 2: CATÁLOGOS TÉCNICOS --}}
                    <div class="tab-pane fade" id="tab-catalogos">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3 text-uppercase fs-8"><i class="fas fa-project-diagram me-2"></i> Orígenes Autorizados</h6>
                                <ul class="list-group list-group-flush rounded-4 border">
                                    @foreach($origenes as $origen)
                                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark">{{ $origen->nombre }}</span>
                                            <span class="badge bg-light text-muted">ID: {{ $origen->id }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted mb-3 text-uppercase fs-8"><i class="fas fa-bolt me-2"></i> Eventos Auditables</h6>
                                <ul class="list-group list-group-flush rounded-4 border">
                                    @foreach($eventos as $evento)
                                        <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                            <span class="fw-semibold text-dark">{{ $evento->nombre }}</span>
                                            <span class="badge bg-light text-muted">ID: {{ $evento->id }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Origen --}}
    <div class="modal fade" id="modalOrigen" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.auditoria.store_origen') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-project-diagram text-primary fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Origen</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-3 text-center" placeholder="Ej. Web, API, Cron" required>
                    <button type="submit" class="btn btn-pastel-primary w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Evento --}}
    <div class="modal fade" id="modalEvento" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form action="{{ route('certificados.auditoria.store_evento') }}" method="POST" class="modal-content border-0 shadow card-custom">
                @csrf
                <div class="modal-body p-4 text-center">
                    <i class="fas fa-bolt text-dark fs-1 mb-3"></i>
                    <h5 class="fw-bold mb-3">Crear Evento</h5>
                    <input type="text" name="nombre" class="form-control bg-light border-0 mb-3 text-center" placeholder="Ej. Inyección Masiva" required>
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-base-layout>