<x-base-layout>
    <div class="app-container py-4">
        {{-- HEADER CON ACCIONES --}}
        <header class="main-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold mb-1">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Radicado #{{ $correspondencia->id_radicado }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('correspondencia.correspondencias.index') }}">Correspondencia</a></li>
                        <li class="breadcrumb-item active">Detalle de Gestión</li>
                    </ol>
                </nav>
            </div>
            <div class="header-actions d-flex gap-2">
                <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalSeguimiento">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Gestión
                </button>
                <a href="{{ route('correspondencia.correspondencias.edit', $correspondencia) }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-pencil-square"></i> Editar Base
                </a>
            </div>
        </header>

        <div class="row g-4">
            {{-- COLUMNA IZQUIERDA: INFORMACIÓN Y TRAZABILIDAD --}}
            <div class="col-lg-8">
                {{-- CARD PRINCIPAL --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge bg-light text-primary border border-primary mb-2">Asunto del Documento</span>
                                <h4 class="fw-bold text-dark">{{ $correspondencia->asunto }}</h4>
                            </div>
                            <span class="badge rounded-pill px-3 py-2 {{ Str::slug($correspondencia->estado->nombre ?? 'default') }}" 
                                  style="font-size: 0.9rem;">
                                {{ $correspondencia->estado->nombre ?? 'Estado N/D' }}
                            </span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person me-1"></i>Remitente</label>
                                <span class="fw-bold">{{ $correspondencia->remitente->nom_ter ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-person-badge me-1"></i>Registrado por</label>
                                <span class="fw-bold">{{ $correspondencia->usuario->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block mb-1"><i class="bi bi-calendar-event me-1"></i>Fecha Radicación</label>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($correspondencia->fecha_solicitud)->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small d-block mb-1">Observaciones Iniciales</label>
                                <div class="bg-light p-3 rounded italic text-muted">
                                    {{ $correspondencia->observacion_previa ?? 'Sin observaciones adicionales.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRAZABILIDAD (HISTORIAL) --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Historial de Gestión (Trazabilidad)</h5>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Etapa/Proceso</th>
                                        <th>Estado</th>
                                        <th>Responsable</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Notif.</th>
                                        <th class="pe-4 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($correspondencia->procesos as $registro)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold small text-primary">
                                                {{ $registro->proceso->flujo->nombre ?? 'Gestión Directa' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-info text-info border border-info">
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
                                                {{-- Ver Observación --}}
                                                <button class="btn btn-sm btn-light border-0" title="{{ $registro->observacion }}" data-bs-toggle="tooltip">
                                                    <i class="bi bi-chat-left-text"></i>
                                                </button>
                                                {{-- Ver Adjunto de la Gestión --}}
                                                @if($registro->documento_arc)
                                                    <a href="{{ Storage::disk('s3')->url($registro->documento_arc) }}" target="_blank" class="btn btn-sm btn-light border-0 text-danger" title="Ver Soporte">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No hay gestiones registradas aún.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: INDICADORES --}}
            <div class="col-lg-4">
                {{-- INDICADOR DE TIEMPO --}}
                @php
                    $limite = \Carbon\Carbon::parse($correspondencia->fecha_solicitud)->addDays($correspondencia->trd->tiempo_gestion ?? 0);
                    $dias = now()->diffInDays($limite, false);
                    $colorClase = $dias > 3 ? 'bg-primary' : ($dias <= 0 ? 'bg-danger' : 'bg-warning text-dark');
                @endphp
                
                <div class="card border-0 shadow-sm mb-4 {{ $colorClase }} text-white" style="border-radius: 15px;">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase opacity-75 fw-bold mb-3">Vencimiento TRD</h6>
                        <div class="display-5 fw-bold mb-1">{{ abs((int)$dias) }}</div>
                        <p class="mb-0 fw-light">{{ $dias >= 0 ? 'Días restantes' : 'Días de retraso' }}</p>
                        <hr class="opacity-25">
                        <div class="small">
                            <strong>Serie:</strong> {{ $correspondencia->trd->serie_documental ?? 'N/A' }}<br>
                            <strong>Límite:</strong> {{ $limite->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                {{-- ARCHIVO ORIGINAL DEL RADICADO --}}
                @if($correspondencia->documento_arc)
                <div class="card border-0 shadow-sm p-4 text-center mb-4" style="border-radius: 15px;">
                    <div class="mb-3">
                        <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Documento Original</h6>
                    <p class="text-muted small mb-3">Expediente escaneado</p>
                    <a href="{{ Storage::disk('s3')->url($correspondencia->documento_arc) }}" target="_blank" class="btn btn-dark w-100 rounded-pill">
                        <i class="bi bi-download me-2"></i>Descargar
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL PARA REGISTRAR SEGUIMIENTO --}}
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Seguimiento - Radicado #{{ $correspondencia->id_radicado }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Formulario con enctype para archivos --}}
                <form action="{{ route('correspondencia.correspondencias-procesos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_correspondencia" value="{{ $correspondencia->id }}">
                    <input type="hidden" name="fk_usuario" value="{{ auth()->id() }}">

                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Etapa del Proceso</label>
                                <select name="id_proceso" class="form-select border-0 bg-light" required>
                                    <option value="">--- Seleccione la Etapa del Proceso ---</option>
                                    @foreach($procesos_disponibles as $proceso)
                                        <option value="{{ $proceso->id }}">
                                            {{-- Nombre del Flujo + Detalle Técnico de la etapa --}}
                                            {{ strtoupper($proceso->flujo->nombre ?? 'PROCESO') }} ⮕ {{ $proceso->detalle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nuevo Estado</label>
                                <select name="estado" class="form-select border-0 bg-light" required>
                                    <option value="recibido">Recibido</option>
                                    <option value="en_tramite">En Trámite</option>
                                    <option value="devuelto">Devuelto</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Observaciones de la Gestión</label>
                                <textarea name="observacion" class="form-control border-0 bg-light" rows="3" placeholder="¿Qué acción se realizó?" required></textarea>
                            </div>
                            
                            {{-- NUEVO: Campo para Adjuntar Soporte en la Gestión --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small">Adjuntar Soporte de Gestión (Opcional)</label>
                                <input type="file" name="documento_arc" class="form-control border-0 bg-light">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Fecha de Gestión</label>
                                <input type="datetime-local" name="fecha_gestion" class="form-control border-0 bg-light" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="notificado_email" value="1" id="notifCheckModal">
                                    <label class="form-check-label fw-bold small" for="notifCheckModal">¿Notificar al remitente?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>

<style>
    .recibido { background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
    .en-tramite, .en_tramite { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; }
    .finalizado { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #2e7d32; }
    .devuelto { background-color: #fce4ec; color: #d81b60; border: 1px solid #d81b60; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .main-header { border-bottom: 1px solid #eee; padding-bottom: 1rem; }
    .table thead th { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #666; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>