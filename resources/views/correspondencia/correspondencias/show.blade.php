<x-base-layout>
    <div class="app-container py-4">
        {{-- HEADER CON ACCIONES --}}
        <header class="main-header d-flex justify-content-between align-items-center mb-4 pb-3">
            <div>
                <h1 class="page-title h3 fw-bold mb-1">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Radicado #{{ $correspondencia->id_radicado }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.correspondencias.index') }}" class="text-decoration-none text-muted">Correspondencia</a></li>
                        <li class="breadcrumb-item active fw-medium">Detalle de Gestión</li>
                    </ol>
                </nav>
            </div>
            <div class="header-actions d-flex gap-2">
                <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalSeguimiento">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Gestión
                </button>
                <a href="{{ route('correspondencia.correspondencias.edit', $correspondencia) }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-pencil-square"></i> Editar Base
                </a>
            </div>
        </header>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: INFORMACIÓN Y TRAZABILIDAD --}}
            <div class="col-lg-8">
                {{-- CARD PRINCIPAL --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge bg-soft-primary text-primary mb-2 px-3 py-2 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; text-transform: uppercase;">Asunto del Documento</span>
                                <h4 class="fw-bold text-dark mb-0">{{ $correspondencia->asunto }}</h4>
                            </div>
                            <span class="badge rounded-pill px-4 py-2 {{ Str::slug($correspondencia->estado->nombre ?? 'default') }} shadow-sm">
                                {{ $correspondencia->estado->nombre ?? 'Estado N/D' }}
                            </span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person me-1"></i>Remitente</label>
                                <span class="fw-bold text-dark">{{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person-badge me-1"></i>Registrado por</label>
                                <span class="fw-bold text-dark">{{ $correspondencia->usuario->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Fecha Radicación</label>
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($correspondencia->fecha_solicitud)->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small d-block mb-1">Observaciones Iniciales</label>
                                <div class="p-3 rounded-4 bg-light italic text-muted border-start border-4 border-primary-subtle">
                                    {{ $correspondencia->observacion_previa ?? 'Sin observaciones adicionales.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRAZABILIDAD (HISTORIAL) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0 text-dark">Historial de Gestión</h5>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Etapa/Proceso</th>
                                        <th class="border-0">Estado</th>
                                        <th class="border-0">Responsable</th>
                                        <th class="border-0">Fecha</th>
                                        <th class="text-center border-0">Notif.</th>
                                        <th class="pe-4 text-end border-0">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($correspondencia->procesos as $registro)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold small text-primary">
                                                {{ $registro->proceso->nombre ?? 'Gestión Directa' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $registro->proceso->flujo->nombre ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info rounded-pill px-3 border-0">
                                                {{ ucfirst(str_replace('_', ' ', $registro->estado)) }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $registro->usuario->name ?? 'N/D' }}</td>
                                        <td class="small text-muted">
                                            {{ \Carbon\Carbon::parse($registro->fecha_gestion)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <i class="bi {{ $registro->notificado_email ? 'bi-envelope-check text-success' : 'bi-envelope text-muted opacity-50' }}"></i>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-light border-0 rounded-circle me-1" title="{{ $registro->observacion }}" data-bs-toggle="tooltip">
                                                    <i class="bi bi-chat-left-text"></i>
                                                </button>
                                                @if($registro->documento_arc)
                                                    <a href="{{ $registro->getFile($registro->documento_arc) }}" target="_blank" class="btn btn-sm btn-soft-danger border-0 rounded-circle" title="Ver Soporte">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">No hay gestiones registradas aún.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: INDICADORES Y FLUJO --}}
            <div class="col-lg-4">
                {{-- INDICADOR DE TIEMPO --}}
                @php
                    $limite = \Carbon\Carbon::parse($correspondencia->fecha_solicitud)->addDays($correspondencia->trd->tiempo_gestion ?? 0);
                    $dias = now()->diffInDays($limite, false);
                    
                    if ($dias > 3) {
                        $bgColor = '#e7f3ff'; $textColor = '#3a7abd'; $borderColor = '#cfe5ff';
                    } elseif ($dias <= 0) {
                        $bgColor = '#ffe5e5'; $textColor = '#bd3a3a'; $borderColor = '#ffcfcf';
                    } else {
                        $bgColor = '#fff8e1'; $textColor = '#8d6e1d'; $borderColor = '#ffecb3';
                    }
                @endphp
                
                <div class="card border-0 shadow-sm mb-4" 
                     style="border-radius: 20px; background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }} !important;">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase fw-bold mb-3" style="color: {{ $textColor }}; opacity: 0.8; font-size: 0.75rem; letter-spacing: 1px;">
                            Vencimiento TRD
                        </h6>
                        <div class="d-flex justify-content-center align-items-baseline mb-1">
                            <span class="display-4 fw-bold" style="color: {{ $textColor }};">{{ abs((int)$dias) }}</span>
                        </div>
                        <p class="mb-3 fw-medium" style="color: {{ $textColor }}; opacity: 0.9;">
                            {{ $dias >= 0 ? 'Días restantes' : 'Días de retraso' }}
                        </p>
                        <div class="py-3 px-3 rounded-4" style="background-color: rgba(255,255,255,0.4); border: 1px dashed {{ $borderColor }};">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small opacity-75 fw-bold" style="color: {{ $textColor }};">Serie:</span>
                                <span class="small fw-bold" style="color: {{ $textColor }};">{{ $correspondencia->trd->serie_documental ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small opacity-75 fw-bold" style="color: {{ $textColor }};">Límite:</span>
                                <span class="small fw-bold" style="color: {{ $textColor }};">{{ $limite->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RUTA DEL PROCESO + PARTICIPANTES --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i>Ruta y Participantes</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline-steps">
                            @php $procesosCompletados = $correspondencia->procesos->pluck('id_proceso')->toArray(); @endphp
                            @forelse($procesos_disponibles as $index => $paso)
                                <div class="d-flex mb-4 position-relative">
                                    {{-- Indicador Visual --}}
                                    <div class="me-3 position-relative" style="z-index: 2;">
                                        @if(in_array($paso->id, $procesosCompletados))
                                            <span class="badge rounded-circle bg-success p-2 shadow-sm">
                                                <i class="bi bi-check2 text-white"></i>
                                            </span>
                                        @else
                                            <span class="badge rounded-circle bg-light text-muted border p-2">
                                                {{ $index + 1 }}
                                            </span>
                                        @endif
                                        @if(!$loop->last)
                                            <div class="position-absolute start-50 translate-middle-x" style="width: 2px; height: 55px; top: 30px; z-index: -1; background-color: #f0f2f5;"></div>
                                        @endif
                                    </div>
                                    
                                    {{-- Información del Proceso --}}
                                    <div class="w-100">
                                        <div class="fw-bold small {{ in_array($paso->id, $procesosCompletados) ? 'text-success' : 'text-dark' }}">
                                            {{ $paso->nombre }}
                                        </div>
                                        
                                        {{-- AVATARES DE PARTICIPANTES --}}
                                        <div class="d-flex align-items-center mt-2">
                                            @if($paso->usuariosAsignados && $paso->usuariosAsignados->count() > 0)
                                                <div class="avatar-group d-flex">
                                                    @foreach($paso->usuariosAsignados as $asignacion)
                                                        <div class="avatar-circle-sm" 
                                                             title="{{ $asignacion->usuario->name }} ({{ $asignacion->detalle ?? 'Responsable' }})" 
                                                             data-bs-toggle="tooltip">
                                                            {{ strtoupper(substr($asignacion->usuario->name, 0, 1)) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <span class="ms-2 text-muted" style="font-size: 0.65rem;">Asignados</span>
                                            @else
                                                <span class="text-muted italic" style="font-size: 0.65rem;">Sin responsables fijos</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted small text-center">Sin flujo definido.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ARCHIVO ORIGINAL --}}
                @if($correspondencia->documento_arc)
                <div class="card border-0 shadow-sm p-4 text-center" style="border-radius: 20px; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                    <div class="mb-3">
                        <div class="bg-soft-danger d-inline-block p-3 rounded-circle">
                            <i class="bi bi-file-earmark-pdf text-danger h1 mb-0"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Documento Original</h6>
                    <p class="text-muted small mb-3">Expediente digitalizado</p>
                    <a href="{{ Storage::disk('s3')->url($correspondencia->documento_arc) }}" target="_blank" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                        <i class="bi bi-eye me-2"></i>Visualizar PDF
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL REESTILIZADO --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bi bi-plus-circle-fill text-primary me-2"></i>Registrar Gestión
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" value="{{ $correspondencia->id_radicado }}">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Etapa del Proceso</label>
                                <select name="id_proceso" class="form-select border-0 bg-light rounded-3 py-2" required>
                                    <option value="">Seleccione etapa...</option>
                                    @foreach($procesos_disponibles as $proceso)
                                        <option value="{{ $proceso->id }}">{{ $proceso->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Nuevo Estado</label>
                                <select name="estado" class="form-select border-0 bg-light rounded-3 py-2" required>
                                    <option value="recibido">Recibido</option>
                                    <option value="en_tramite">En Trámite</option>
                                    <option value="devuelto">Devuelto</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Observaciones</label>
                                <textarea name="observacion" class="form-control border-0 bg-light rounded-3" rows="3" placeholder="Detalles de la gestión..." required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Soporte (PDF/Img)</label>
                                <input type="file" name="documento_arc" class="form-control border-0 bg-light rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Fecha</label>
                                <input type="datetime-local" name="fecha_gestion" class="form-control border-0 bg-light rounded-3" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow fw-bold">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>

<style>
    body { background-color: #f4f7f9; }
    .bg-soft-primary { background-color: #eef2ff; }
    .bg-soft-info { background-color: #e0f7fa; }
    .bg-soft-danger { background-color: #fff1f0; }
    .btn-soft-danger { background-color: #fff1f0; color: #ff4d4f; }
    .btn-soft-danger:hover { background-color: #ff4d4f; color: white; }
    
    /* Estados Pastel */
    .recibido { background-color: #e3f2fd; color: #1976d2; border: 0; font-weight: 600; }
    .en-tramite, .en_tramite { background-color: #fff3e0; color: #f57c00; border: 0; font-weight: 600; }
    .finalizado { background-color: #e8f5e9; color: #388e3c; border: 0; font-weight: 600; }
    .devuelto { background-color: #fce4ec; color: #c2185b; border: 0; font-weight: 600; }

    .main-header { border-bottom: 1px solid #dee2e6; }
    .table thead th { font-size: 0.7rem; letter-spacing: 0.5px; font-weight: 700; color: #8898aa; }
    
    /* Estilos de Avatares */
    .avatar-group { display: flex; padding-left: 0; }
    .avatar-circle-sm {
        width: 24px; height: 24px; background-color: #e9ecef; color: #495057;
        border: 2px solid #fff; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 0.65rem;
        font-weight: 700; margin-left: -8px; transition: all 0.2s ease; cursor: default;
    }
    .avatar-circle-sm:first-child { margin-left: 0; }
    .avatar-circle-sm:hover { transform: translateY(-3px); z-index: 10; background-color: #3b82f6; color: white; }

    .form-select:focus, .form-control:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05); border: 1px solid #dee2e6; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>