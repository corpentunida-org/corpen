<div class="dropdown nxl-h-item">
    <div class="nxl-head-link me-3" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
        <i class="feather-bell"></i>
        <span class="badge bg-danger nxl-h-badge" id="contadorNotificaciones"></span>
    </div>
    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
        <div class="d-flex justify-content-between align-items-center notifications-head">
            <h6 class="fw-bold text-dark mb-0">Notificaciones de Soportes</h6>
        </div>
        <div id="listaNotificaciones">
            {{-- <div class="notifications-item">
                <div class="notifications-desc">
                    <div class="d-flex align-items-center">
                        <a class="single-task-list-link" data-bs-toggle="offcanvas"
                            data-bs-target="#tasksDetailsOffcanvas">
                            <div class="fs-13 fw-bold text-truncate-1-line">Malanie Hanvey <span
                                    class="ms-2 badge bg-soft-danger text-danger">ALTA</span></div>
                            <div class="fs-12 fw-normal text-muted">No me permite visualizar el archivo subido</div>
                        </a>
                    </div>
                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">hace 3 dias
                        <span class="ms-2 badge bg-gray-200 text-dark mb-2">PENDIENTE</span>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="text-center notifications-footer">
            <a href="{{route('soportes.soportes.index')}}" class="fs-13 fw-semibold text-dark">VER TODOS SOPORTES</a>
        </div>
    </div>
</div>

{{-- <div class="nxl-h-item dropdown" style="position: relative;">
    <a href="javascript:void(0);" class="nxl-head-link position-relative" id="notificacionesBtn">
        <i class="bi bi-bell fs-5"></i>
        <span id="contadorNotificaciones"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm">
            0
        </span>
    </a>
    <!-- Panel flotante de notificaciones -->
    <div id="panelNotificaciones" class="shadow-lg border rounded-3 bg-white"
        style="m
                                        display: none;
                                        position: absolute;
                                        top: 120%; /* bajamos un poco para que no tape la campana */
                                        right: 0;
                                        width: 400px;
                                        max-height: 550px;
                                        overflow-y: auto;
                                        z-index: 99999;
                                        transform: translateY(10px);
                                        opacity: 0;
                                        transition: all 0.25s ease;
                                    ">
        <!-- Cabecera -->
        <div
            class="bg-primary text-white px-3 py-2 rounded-top fw-semibold d-flex justify-content-between align-items-center">
            <span>📋 Mis Soportes</span>
        </div>

        <!-- Contadores -->
        <div class="px-3 py-2 border-bottom bg-light d-flex justify-content-around flex-wrap gap-2">
            <span class="badge bg-primary" title="Creados por mí">Creados: <span id="numCreados">0</span></span>
            <span class="badge bg-success" title="Asignados a mí">Asignados: <span id="numAsignados">0</span></span>
            <span class="badge bg-warning text-dark" title="Pendientes por cerrar">Pendientes:
                <span id="numPendientes">0</span></span>
        </div>
        <!-- Lista de notificaciones -->
        <div id="listaNotificaciones" class="list-group list-group-flush small"
            style="max-height: 450px; overflow-y: auto;">
            <div class="text-center text-muted p-3">Cargando notificaciones...</div>
        </div>
    </div>
</div> --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('notificacionesBtn');
        const panel = document.getElementById('panelNotificaciones');
        const contador = document.getElementById('contadorNotificaciones');

        /*btn.addEventListener('click', () => {
            panel.classList.toggle('mostrar');
        });

        document.addEventListener('click', (e) => {
            if (!btn.contains(e.target) && !panel.contains(e.target)) {
                panel.classList.remove('mostrar');
            }
        });*/

        actualizarNotificacionesDetalladas();
        setInterval(actualizarNotificacionesDetalladas, 60000);

        function actualizarNotificacionesDetalladas() {
            fetch('{{ route('soportes.notificaciones.detalladas') }}')
                .then(res => res.json())
                .then(data => {
                    // --- Contadores ---
                    /*document.getElementById('numCreados').textContent = data.creados ?? 0;
                    document.getElementById('numAsignados').textContent = data.asignados ?? 0;
                    document.getElementById('numPendientes').textContent = data.pendientes ?? 0;*/
                    contador.textContent = data.total ?? 0;

                    // --- Detalles ---
                    const lista = document.getElementById('listaNotificaciones');
                    lista.innerHTML = '';

                    if (data.detalles && data.detalles.length > 0) {                        
                        data.detalles.forEach(item => {
                            /*lista.innerHTML += `
                                <div class="list-group-item" onclick="window.location='/soportes/soportes/${item.id}'">
                                    <div class="fw-semibold">${item.detalles_soporte}</div>
                                    <small class="text-muted d-block mt-1">
                                        Estado: <span style="color:${item.estado_color};">${item.estado}</span><br>
                                        ${item.fecha_creacion}
                                    </small>
                                </div>`;*/
                            lista.innerHTML += `
                                <div class="notifications-item">
                                    <div class="notifications-desc">
                                        <div class="d-flex align-items-center">
                                            <a class="single-task-list-link">
                                                <div class="fs-13 fw-bold text-truncate-1-line">${item.usuario_nombre}
                                                    <span class="ms-2 badge bg-soft-${item.prioridad_color} text-${item.prioridad_color}">${item.prioridad}</span>
                                                </div>
                                                <div class="fs-12 fw-normal text-muted">${item.detalles_soporte}</div>
                                            </a>
                                        </div>
                                        <div class="notifications-date text-muted">${item.fecha_creacion}
                                            <span class="ms-2 badge bg-gray-200 text-dark">${item.estado}</span>
                                        </div>
                                    </div>
                                </div>`;
                        });
                    } else {
                        lista.innerHTML =
                            '<div class="text-center text-muted p-3">No hay notificaciones recientes.</div>';
                    }
                }).catch(err => console.error('Error al obtener notificaciones:', err));
        }
    });
</script>
