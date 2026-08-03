<x-base-layout>
    @section('titlepage', 'Mi Portal - Sistema RSV')

    {{-- Notificaciones Toast --}}
    @include('rsv.components.alert')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-extrabold mb-1">Hola, {{ auth()->user()->name ?? 'Huésped' }}</h2>
            <p class="text-muted">Gestiona tus próximas estadías y pagos desde aquí.</p>
        </div>
    </div>

    {{-- Pestañas de Navegación Cliente --}}
    <ul class="nav nav-pills mb-4 bg-white p-2 rounded-pill shadow-sm" id="clienteTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#mis-reservas">Mis Reservas</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#mis-pagos">Pagos y Comprobantes</button></li>
        <li class="nav-item"><button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#soporte-reviews">Soporte y Reseñas</button></li>
    </ul>

    <div class="tab-content" id="clienteTabsContent">

        {{-- MÓDULO 1: MIS RESERVAS --}}
        <div class="tab-pane fade show active" id="mis-reservas">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-3">
                        <div class="card-body">
                            <h5 class="fw-bold text-primary">Mis Próximos Viajes</h5>
                            <!-- Iteración de reservas del cliente (rsv_reservas) -->
                            <div class="border rounded-3 p-3 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1">Cabaña del Lago (rsv_catalogo_inmueble)</h6>
                                    <small class="text-muted">15 Nov - 18 Nov | 4 Huéspedes (rsv_reserva_huespedes)</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalItinerario">
                                    Ver Itinerario
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-light">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history fs-1 text-secondary mb-2"></i>
                            <h6 class="fw-bold">Historial Pasado</h6>
                            <p class="small text-muted">Revisa tus reservas completadas o canceladas.</p>
                            <button class="btn btn-secondary btn-sm rounded-pill w-100">Ver Historial</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MÓDULO 2: PAGOS Y COMPROBANTES --}}
        <div class="tab-pane fade" id="mis-pagos">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="fw-bold">Mis Transacciones</h5>
                    <p class="text-muted small">Tus comprobantes de pago (rsv_transacciones_financieras) a través de pasarelas autorizadas (rsv_pasarelas).</p>
                    <div class="table-responsive mt-3">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr><th>Fecha</th><th>Reserva</th><th>Monto</th><th>Estado</th><th>Descarga</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>12 Oct 2023</td>
                                    <td>#RSV-0012</td>
                                    <td>$450.00</td>
                                    <td><span class="badge bg-success">Aprobado</span></td>
                                    <td>
                                        <a href="{{ route('rsv.reservas.pdf', 12) }}" class="btn btn-sm btn-danger rounded-pill">
                                            <i class="bi bi-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Paginación fluida --}}
                        @include('rsv.components.pagination')
                    </div>
                </div>
            </div>
        </div>

        {{-- MÓDULO 3: SOPORTE Y RESEÑAS --}}
        <div class="tab-pane fade" id="soporte-reviews">
            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white pt-4 pb-2 border-0">
                            <h5 class="fw-bold"><i class="bi bi-chat-dots text-primary me-2"></i>Centro de Ayuda</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="flex-grow-1 bg-light rounded-3 p-3 mb-3" style="min-height: 200px;">
                                <p class="text-muted text-center mt-4 small">Historial de mensajes con el administrador (rsv_mensajes)</p>
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control rounded-start-pill" placeholder="Escribe tu consulta...">
                                <button class="btn btn-primary rounded-end-pill px-4">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <h5 class="fw-bold"><i class="bi bi-star-fill text-warning me-2"></i>Mis Reseñas</h5>
                            <p class="text-muted small">Califica tus estadías recientes (rsv_reviews).</p>

                            <div class="border rounded-3 p-3 mb-2">
                                <h6 class="small fw-bold mb-1">Cabaña del Bosque</h6>
                                <div class="text-warning mb-2">★★★★★</div>
                                <p class="small text-muted mb-0">"Excelente servicio y vista inmejorable."</p>
                            </div>

                            <button class="btn btn-outline-warning text-dark btn-sm rounded-pill w-100 mt-2">Dejar nueva reseña</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal para formulario usando @include y pasando parámetros --}}
    @include('rsv.components.modal', ['id' => 'modalItinerario', 'title' => 'Detalle del Itinerario y Huéspedes'])

</x-base-layout>
