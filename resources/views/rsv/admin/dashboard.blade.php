<x-base-layout>
    @section('titlepage', 'Panel de Administración - Sistema RSV')

    {{-- Importamos Chart.js si se requiere para gráficos analíticos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-success />

    <div class="main-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-extrabold mb-1 pl-2">Panel de Administración General</h1>

        <div class="header-actions d-flex">
            <a href="{{ route('reserva.dashboard.pdf') }}" class="btn btn-danger shadow-sm rounded-pill px-4 fw-bold me-2">
                <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
            </a>

            <a href="{{ route('reserva.dashboard.exportar') }}" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-file-earmark-excel me-1"></i> Generar Informe
            </a>
        </div>
    </div>

    {{-- Pestañas de Navegación del Panel Admin --}}
    <ul class="nav nav-pills mb-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold me-2 shadow-sm" id="inmuebles-tab" data-bs-toggle="pill" data-bs-target="#inmuebles" type="button" role="tab">Inmuebles</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold me-2 shadow-sm" id="reservas-tab" data-bs-toggle="pill" data-bs-target="#reservas" type="button" role="tab">Reservas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold me-2 shadow-sm" id="calendario-tab" data-bs-toggle="pill" data-bs-target="#calendario" type="button" role="tab">Calendario Global</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold me-2 shadow-sm" id="finanzas-tab" data-bs-toggle="pill" data-bs-target="#finanzas" type="button" role="tab">Finanzas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold shadow-sm" id="auditoria-tab" data-bs-toggle="pill" data-bs-target="#auditoria" type="button" role="tab">Auditoría</button>
        </li>
    </ul>

    <div class="tab-content" id="adminTabsContent">
        {{-- Pestaña Inmuebles --}}
        <div class="tab-pane fade show active" id="inmuebles" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-buildings me-2 text-primary"></i> Gestión de Catálogo de Inmuebles</h4>
                <p class="text-muted">Administra las propiedades, tarifas y temporadas conectadas al motor de reservas.</p>
            </div>
        </div>

        {{-- Pestaña Reservas --}}
        <div class="tab-pane fade" id="reservas" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-calendar-check me-2 text-success"></i> Control Global de Reservas</h4>
                <p class="text-muted">Supervisa el ciclo completo de reservas, huéspedes principales y control de estados operativos.</p>
            </div>
        </div>

        {{-- Pestaña Calendario Global --}}
        <div class="tab-pane fade" id="calendario" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-calendar3 me-2 text-info"></i> Calendario Global de Ocupación</h4>
                <p class="text-muted">Visualización en tiempo real de la ocupación y disponibilidad por propiedad.</p>
            </div>
        </div>

        {{-- Pestaña Finanzas --}}
        <div class="tab-pane fade" id="finanzas" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-wallet2 me-2 text-warning"></i> Transacciones Financieras y Pasarelas</h4>
                <p class="text-muted">Monitoreo de pasarelas de pago, estados de transacciones y conciliación de ingresos.</p>
            </div>
        </div>

        {{-- Pestaña Auditoría --}}
        <div class="tab-pane fade" id="auditoria" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h4 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-danger"></i> Auditoría, Historial de Estados y Orígenes</h4>
                <p class="text-muted">Trazabilidad completa de modificaciones, cambios de estado y canales de origen de reservas.</p>
            </div>
        </div>
    </div>
</x-base-layout>
