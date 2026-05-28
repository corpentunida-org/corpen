<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">ECM - Auditoría</span>
                <h1 class="main-title">Expediente Completo</h1>
                <p class="main-subtitle">Radicado Físico: <strong class="text-dark">{{ $maeAsociado->radicado ?? 'NO ASIGNADO' }}</strong></p>
            </div>
            <div class="header-actions">
                <a href="{{ route('asociados.maestro.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Volver</span>
                </a>
                <a href="{{ route('asociados.maestro.edit', $maeAsociado->id) }}" class="btn-corporate-black">
                    <i class="fas fa-pen-nib"></i> <span>Actualizar Datos</span>
                </a>
            </div>
        </header>

        <div class="show-card-corp mb-4">
            <div class="show-header">
                <div class="status-badge-large {{ $maeAsociado->estado == 'Activo' ? 'st-completed' : 'st-danger' }}">
                    <i class="fas fa-circle"></i> EXPEDIENTE {{ strtoupper($maeAsociado->estado ?? 'ACTIVO') }}
                </div>
                <h2>{{ $maeAsociado->nombre_completo }}</h2>
            </div>
            
            <div class="show-body">
                <h4 class="mt-2 mb-3 text-secondary border-bottom pb-2">1. Identidad y Contacto</h4>
                <div class="grid-3 mb-4">
                    <div class="info-block">
                        <h4>Cédula y Expedición</h4>
                        <p class="text-primary fw-bold">{{ $maeAsociado->cedula }} <br> <small class="text-muted fw-normal">{{ $maeAsociado->lugar_expedicion_cedula }} ({{ $maeAsociado->fecha_expedicion ? $maeAsociado->fecha_expedicion->format('d/m/Y') : 'N/A' }})</small></p>
                    </div>
                    <div class="info-block">
                        <h4>Nacimiento y Est. Civil</h4>
                        <p>{{ $maeAsociado->fecha_nacimiento ? $maeAsociado->fecha_nacimiento->format('d/m/Y') : 'N/A' }} <br> <small class="text-muted">{{ $maeAsociado->estado_civil ?? 'N/A' }}</small></p>
                    </div>
                    <div class="info-block">
                        <h4>Datos de Contacto</h4>
                        <p><i class="fas fa-envelope me-1"></i> {{ $maeAsociado->correo_pastor ?? 'N/A' }} <br> <i class="fas fa-phone me-1"></i> {{ $maeAsociado->celular_pastor ?? 'N/A' }} (WA: {{ $maeAsociado->whatsapp ?? 'N/A' }})</p>
                    </div>
                </div>

                <h4 class="mt-4 mb-3 text-secondary border-bottom pb-2">2. Núcleo Familiar (Cónyuge)</h4>
                <div class="grid-3 mb-4">
                    <div class="info-block">
                        <h4>Nombre Esposa</h4>
                        <p>{{ $maeAsociado->nombre_esposa ?? 'N/A' }}</p>
                    </div>
                    <div class="info-block">
                        <h4>Cédula Esposa</h4>
                        <p>{{ $maeAsociado->cedula_esposa ?? 'N/A' }}</p>
                    </div>
                    <div class="info-block">
                        <h4>Contacto Esposa</h4>
                        <p>{{ $maeAsociado->celular_esposa ?? 'N/A' }} <br> <small class="text-muted">{{ $maeAsociado->correo_esposa ?? '' }}</small></p>
                    </div>
                </div>

                <h4 class="mt-4 mb-3 text-secondary border-bottom pb-2">3. Asignación Ministerial</h4>
                <div class="grid-3 mb-4">
                    <div class="detail-item">
                        <i class="fas fa-church icon-muted"></i>
                        <div>
                            <span class="label">Distrito / Iglesia</span>
                            <strong>{{ $maeAsociado->distrito_actual ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">{{ $maeAsociado->iglesia_actual ?? '' }} ({{ $maeAsociado->ciudad_distrito ?? '' }})</small>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-id-badge icon-muted"></i>
                        <div>
                            <span class="label">Rol y Licencia</span>
                            <strong>{{ $maeAsociado->especificacion ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">Licencia: {{ $maeAsociado->licencia ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-user-check icon-muted"></i>
                        <div>
                            <span class="label">Estado Pastoral</span>
                            <strong>{{ $maeAsociado->estado_pastor ?? 'N/A' }}</strong>
                            <small class="d-block text-muted">Afiliado: {{ $maeAsociado->fecha_afiliacion ? $maeAsociado->fecha_afiliacion->format('d/m/Y') : 'N/A' }}</small>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4 mb-3 text-secondary border-bottom pb-2">4. Trazabilidad Documental y Archivo</h4>
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold mb-3">Archivo Físico</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Ubicación:</strong> Carpeta {{ $maeAsociado->ubicacion_carpeta ?? 'N/A' }} | Caja {{ $maeAsociado->numero_caja ?? 'N/A' }}</li>
                            <li class="mb-2"><strong>Volumen:</strong> {{ $maeAsociado->cantidad_folios ?? '0' }} Folios (Estado: {{ $maeAsociado->estado_conservacion ?? 'N/A' }})</li>
                            <li class="mb-2"><strong>Custodia:</strong> {{ $maeAsociado->custodia_actual ?? 'N/A' }} desde el {{ $maeAsociado->fecha_ingreso_archivo ? $maeAsociado->fecha_ingreso_archivo->format('d/m/Y') : 'N/A' }}</li>
                            <li class="mb-2 text-muted fst-italic">Obs: {{ $maeAsociado->observaciones_archivo ?? 'Ninguna' }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Digitalización (ECM)</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                {!! $maeAsociado->escaneado ? '<i class="fas fa-check-circle text-success"></i> Escaneado' : '<i class="fas fa-times-circle text-danger"></i> No Escaneado' !!}
                            </li>
                            <li class="mb-2">
                                {!! $maeAsociado->cargado_ecm ? '<i class="fas fa-check-circle text-success"></i> Cargado en Plataforma ECM' : '<i class="fas fa-times-circle text-danger"></i> Pendiente Carga ECM' !!}
                            </li>
                            <li class="mb-2">
                                {!! $maeAsociado->validado_archivo ? '<i class="fas fa-check-circle text-success"></i> Validado por Auditoría' : '<i class="fas fa-times-circle text-danger"></i> Sin Validar' !!}
                            </li>
                            @if($maeAsociado->ubicacion_ecm_link)
                                <li class="mt-3">
                                    <a href="{{ $maeAsociado->ubicacion_ecm_link }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt me-1"></i> Ver en Plataforma ECM</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <h6 class="fw-bold mt-4 mb-3">Checklist de Soportes Exigidos</h6>
                <div class="table-responsive bg-light p-3 rounded">
                    <table class="table table-sm table-borderless m-0" style="font-size: 0.85rem;">
                        <tr>
                            <td><strong>Form. Afiliación:</strong> {{ $maeAsociado->doc_formulario_afiliacion ?? 'Pendiente' }}</td>
                            <td><strong>Habeas Data:</strong> {{ $maeAsociado->doc_autorizacion_datos ?? 'Pendiente' }}</td>
                            <td><strong>CC Pastor:</strong> {{ $maeAsociado->doc_cedula_pastor ?? 'Pendiente' }}</td>
                            <td><strong>CC Esposa:</strong> {{ $maeAsociado->doc_cedula_esposa ?? 'Pendiente' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Licencia:</strong> {{ $maeAsociado->doc_licencia_pastoral ?? 'Pendiente' }}</td>
                            <td><strong>Reg. Matrimonio:</strong> {{ $maeAsociado->doc_registro_matrimonio ?? 'Pendiente' }}</td>
                            <td><strong>ID Hijos:</strong> {{ $maeAsociado->doc_id_hijos ?? 'Pendiente' }}</td>
                            <td></td>
                        </tr>
                    </table>
                </div>

                @if($maeAsociado->observaciones_generales)
                <div class="mt-4 p-3 border border-warning rounded bg-white">
                    <h6 class="fw-bold text-warning"><i class="fas fa-exclamation-triangle"></i> Observaciones Generales</h6>
                    <p class="mb-0 text-dark">{{ $maeAsociado->observaciones_generales }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('asociados.partials.styles')
</x-base-layout>