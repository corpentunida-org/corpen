<x-base-layout>
    @section('titlepage', 'Portal de Huésped - Sistema RSV')

    <x-success />

    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Portal de Huésped</h1>

        <div class="header-actions d-flex">
            <a href="#" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-search me-1"></i> Explorar Inmuebles
            </a>
        </div>
    </div>

    {{-- Pestañas de Navegación del Portal Cliente --}}
    <ul class="nav nav-pills mb-4" id="clientTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold me-2 shadow-sm" id="activas-tab" data-bs-toggle="pill" data-bs-target="#activas" type="button" role="tab">Reservas Activas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold me-2 shadow-sm" id="historial-tab" data-bs-toggle="pill" data-bs-target="#historial" type="button" role="tab">Historial de Estancias</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold me-2 shadow-sm" id="pagos-tab" data-bs-toggle="pill" data-bs-target="#pagos" type="button" role="tab">Pagos y Transacciones</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold shadow-sm" id="soporte-tab" data-bs-toggle="pill" data-bs-target="#soporte" type="button" role="tab">Soporte y Chat</button>
        </li>
    </ul>

    <div class="tab-content" id="clientTabsContent">
        {{-- Reservas Activas --}}
        <div class="tab-pane fade show active" id="activas" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i> Mis Reservas Vigentes</h4>
                <div class="alert alert-info border-0 rounded-3 mb-0 text-dark">
                    No tienes reservas activas en este momento. ¡Realiza una nueva reserva para comenzar tu estancia!
                </div>
            </div>
        </div>

        {{-- Historial --}}
        <div class="tab-pane fade" id="historial" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-journal-text me-2 text-success"></i> Historial de Estancias y Reseñas</h4>
                <p class="text-muted">Consulta tus estancias pasadas y califica tu experiencia a través de nuestro sistema de reseñas.</p>
            </div>
        </div>

        {{-- Pagos --}}
        <div class="tab-pane fade" id="pagos" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-credit-card me-2 text-warning"></i> Estado de Pagos y Pasarelas</h4>
                <p class="text-muted">Historial financiero asociado a tus transacciones de pago y comprobantes descargables.</p>
            </div>
        </div>

        {{-- Soporte --}}
        <div class="tab-pane fade" id="soporte" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-chat-dots me-2 text-info"></i> Chat de Soporte y Asistencia</h4>
                <div class="border rounded-4 p-3 bg-light mb-3" style="height: 250px; overflow-y: auto;">
                    <div class="bg-white p-3 rounded-3 shadow-sm mb-2 w-75">
                        <small class="text-muted fw-bold d-block mb-1">Soporte RSV</small>
                        ¡Hola! ¿En qué podemos ayudarte con tu reserva?
                    </div>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control rounded-pill px-4" placeholder="Escribe tu mensaje...">
                    <button class="btn btn-primary rounded-pill px-4 ms-2 fw-bold" type="button">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
