<x-base-layout>
    @section('titlepage', 'Panel de Administración - Sistema RSV')

    {{-- Notificaciones Toast --}}
    @include('rsv.components.alert')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-extrabold mb-0">Panel de Control</h2>
        <a href="{{ route('rsv.reservas.pdf', 1) }}" class="btn btn-dark rounded-pill shadow-sm">
            <i class="bi bi-file-pdf"></i> Reporte Global
        </a>
    </div>

    {{-- Pestañas de Navegación --}}
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-pill shadow-sm" id="adminTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#inmuebles">Inmuebles</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#reservas">Reservas y Logística</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#calendario">Calendario</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#finanzas">Finanzas</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#auditoria">Config & Auditoría</button></li>
    </ul>

    <div class="tab-content" id="adminTabsContent">

        {{-- MÓDULO 1: INMUEBLES --}}
        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel" aria-labelledby="inmuebles-tab">
            @include('rsv.admin.partials.tab-inmuebles')
        </div>

        {{-- MODAL PARA CREAR NUEVO INMUEBLE (Con todos los campos del modelo) --}}
        <div class="modal fade" id="modalNuevoInmueble" tabindex="-1" aria-labelledby="modalNuevoInmuebleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 p-3">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="modalNuevoInmuebleLabel">Registrar Propiedad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <form action="{{ route('rsv.inmuebles.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">Nombre del Inmueble (`name`)</label>
                                <input type="text" class="form-control" name="name" placeholder="Ej. Cabaña de Montaña" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-bold">Precio Base por Noche ($) (`precio_base_noche`)</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_base_noche" placeholder="0.00" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-bold">Capacidad Máxima (Personas) (`capacidad_maxima`)</label>
                                    <input type="number" class="form-control" name="capacidad_maxima" placeholder="Ej. 4">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-bold">Ciudad (`city`)</label>
                                    <input type="text" class="form-control" name="city" placeholder="Ej. Bogotá">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-dark fw-bold">Ubicación / Dirección (`ubicacion`)</label>
                                    <input type="text" class="form-control" name="ubicacion" placeholder="Ej. Calle 100 # 15-20">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold">ID Tipo de Inmueble (`tipo_inmueble_id`)</label>
                                <input type="number" class="form-control" name="tipo_inmueble_id" placeholder="Ej. 1">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="active" value="1" id="activeCheck" checked>
                                <label class="form-check-label text-dark fw-bold" for="activeCheck">Activo (`active`)</label>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Inmueble</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- MÓDULO 2: RESERVAS Y LOGÍSTICA --}}
        <div class="tab-pane fade" id="reservas">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold">Reservas Activas</h5>
                            <p class="text-muted small">Control de titulares (users), reservas (rsv_reservas), huéspedes asociados (rsv_reserva_huespedes) e itinerarios (rsv_itinerarios_eventos).</p>
                            <!-- Lista de reservas globales -->
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold text-danger">Gestión de Endosos</h6>
                            <p class="text-muted small">Aprobación de traslados de titularidad (rsv_historial_endosos).</p>
                            <button class="btn btn-outline-danger btn-sm w-100 rounded-pill">Revisar Solicitudes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MÓDULO 3: CALENDARIO GLOBAL --}}
        <div class="tab-pane fade" id="calendario">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex flex-column" style="min-height: 400px;">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold">Disponibilidad y Bloqueos</h5>
                        <button class="btn btn-warning btn-sm rounded-pill text-dark">Registrar Mantenimiento (Bloqueo)</button>
                    </div>
                    <p class="text-muted small mb-3">Visualización conjunta de reservas aprobadas y bloqueos administrativos (rsv_bloqueos_calendario).</p>
                    <div class="flex-grow-1 border rounded-3 bg-light d-flex align-items-center justify-content-center text-muted">
                        [Vista FullCalendar Global]
                    </div>
                </div>
            </div>
        </div>

        {{-- MÓDULO 4: FINANZAS --}}
        <div class="tab-pane fade" id="finanzas">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold">Control de Recaudos y Pasarelas</h5>
                    <p class="text-muted small">Monitoreo de pagos (rsv_transacciones_financieras) y configuración de métodos de cobro (rsv_pasarelas).</p>
                    <!-- Tabla de transacciones globales -->

                    @include('rsv.components.pagination', ['paginator' => $transacciones ?? null])
                </div>
            </div>
        </div>

        {{-- MÓDULO 5: CONFIGURACIÓN Y AUDITORÍA --}}
        <div class="tab-pane fade" id="auditoria">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white">
                        <div class="card-body">
                            <h5 class="fw-bold text-warning"><i class="bi bi-shield-lock me-2"></i>Bitácora del Sistema (Audit Logs)</h5>
                            <p class="small text-light">Trazabilidad inmutable de todas las acciones de los usuarios (rsv_audit_logs).</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-3 text-center">
                        <h6 class="fw-bold">Catálogos Maestros</h6>
                        <span class="d-block small text-muted mb-1">Orígenes: Web, App (rsv_origen_reservas)</span>
                        <span class="d-block small text-muted mb-1">Tipos de Receptor (rsv_tipo_receptor)</span>
                        <span class="d-block small text-muted mb-3">Estados Permitidos (rsv_statuses / rsv_historial_estados)</span>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill mt-auto">Configurar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA NUEVO INMUEBLE (Integrado con su formulario) --}}
    @include('rsv.components.modal', [
        'id' => 'modalNuevoInmueble',
        'title' => 'Registrar Propiedad',
        'slot' => '
            <form action="' . route('rsv.inmuebles.store') . '" method="POST">
                ' . csrf_field() . '
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Nombre del Inmueble</label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Tarifa Base ($)</label>
                    <input type="number" class="form-control" name="tarifa_base" required>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" @click="open = false">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Inmueble</button>
                </div>
            </form>
        '
    ])

</x-base-layout>
