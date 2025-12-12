<div class="dropdown nxl-h-item">
    <div class="nxl-head-link me-3 position-relative" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
        <i class="feather-bell notification-bell"></i>
        <span class="badge bg-danger nxl-h-badge pulse-animation" id="contadorNotificaciones"></span>
        <div class="notification-indicator" id="notificationIndicator"></div>
    </div>
    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu notification-panel">
        <div class="notifications-header">
            <div class="d-flex justify-content-between align-items-center notifications-head">
                <h6 class="fw-bold text-dark mb-0">Centro de Soportes</h6>
                <button class="btn btn-sm btn-icon refresh-btn" id="refreshBtn" title="Actualizar">
                    <i class="feather-refresh-cw"></i>
                </button>
            </div>
            
            <!-- Pestañas de categorías con iconos minimalistas -->
            <div class="notification-tabs">
                <div class="tab-item active" data-category="sinAsignar">
                    <i class="feather-user-x tab-icon"></i>
                    <span class="tab-label">Sin Asignar</span>
                    <span class="tab-count" id="countSinAsignar">0</span>
                </div>
                <div class="tab-item" data-category="enProceso">
                    <i class="feather-loader tab-icon"></i>
                    <span class="tab-label">En Proceso</span>
                    <span class="tab-count" id="countEnProceso">0</span>
                </div>
                <div class="tab-item" data-category="revision">
                    <i class="feather-eye tab-icon"></i>
                    <span class="tab-label">Revisión</span>
                    <span class="tab-count" id="countRevision">0</span>
                </div>
                <div class="tab-item" data-category="cerrados">
                    <i class="feather-check-circle tab-icon"></i>
                    <span class="tab-label">Cerrados</span>
                    <span class="tab-count" id="countCerrados">0</span>
                </div>
            </div>
        </div>
        
        <div id="listaNotificaciones" class="notifications-list">
            <!-- Las notificaciones se cargarán aquí -->
        </div>
        
        <div class="text-center notifications-footer">
            <a href="{{route('soportes.soportes.index')}}" class="fs-13 fw-semibold text-dark view-all-link">
                VER TODOS SOPORTES
                <i class="feather-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- ============================================
   ESTILOS CSS - DISEÑO CORPORATIVO MINIMALISTA
   ============================================ -->
<style>
/* Paleta de colores pastel corporativa */
:root {
    --pastel-yellow: #FFF3CD;
    --pastel-blue: #CFE2FF;
    --pastel-purple: #E2D9F3;
    --pastel-green: #D1E7DD;
    --pastel-pink: #F8D7DA;
    --pastel-gray: #F8F9FA;
    --text-primary: #212529;
    --text-secondary: #6C757D;
    --border-light: #E9ECEF;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Campana de notificaciones */
.notification-bell {
    transition: var(--transition);
}

.notification-bell:hover {
    transform: scale(1.05);
    color: #073B4C;
}

/* Badge de contador */
.badge.bg-danger {
    background: linear-gradient(135deg, #FFD166, #F77F00) !important;
    animation: pulse 2s infinite;
}

/* Indicador de nuevas notificaciones */
.notification-indicator {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 8px;
    height: 8px;
    background: linear-gradient(135deg, #06D6A0, #118AB2);
    border-radius: 50%;
    opacity: 0;
    transform: scale(0);
    transition: var(--transition);
}

.notification-indicator.show {
    opacity: 1;
    transform: scale(1);
    animation: blink 1.5s infinite;
}

/* Panel de notificaciones */
.notification-panel {
    width: 380px;
    max-height: 500px;
    border-radius: 12px;
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 209, 102, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(255, 209, 102, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 209, 102, 0); }
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Cabecera */
.notifications-header {
    padding: 15px;
    background: linear-gradient(135deg, var(--pastel-gray) 0%, white 100%);
    border-bottom: 1px solid var(--border-light);
}

.notifications-head h6 {
    font-size: 1rem;
    color: var(--text-primary);
}

.btn-icon {
    background: none;
    border: none;
    padding: 4px;
    border-radius: 6px;
    color: var(--text-secondary);
    transition: var(--transition);
}

.btn-icon:hover {
    background: var(--pastel-gray);
    color: var(--text-primary);
}

.btn-icon.spinning i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Pestañas de categorías */
.notification-tabs {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    overflow-x: auto;
    padding-bottom: 4px;
}

.notification-tabs::-webkit-scrollbar {
    height: 3px;
}

.notification-tabs::-webkit-scrollbar-track {
    background: var(--border-light);
    border-radius: 3px;
}

.notification-tabs::-webkit-scrollbar-thumb {
    background: #CED4DA;
    border-radius: 3px;
}

.tab-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 10px;
    border-radius: 10px;
    background: white;
    border: 1px solid var(--border-light);
    cursor: pointer;
    transition: var(--transition);
    min-width: 70px;
    flex-shrink: 0;
}

.tab-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.tab-item.active {
    background: linear-gradient(135deg, var(--pastel-blue), #B6D4FE);
    border-color: #B6D4FE;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.tab-icon { /* Estilo para los nuevos iconos */
    font-size: 1.1rem; /* Un poco más grande para mejor visibilidad */
    margin-bottom: 4px;
    color: var(--text-secondary); /* Color por defecto */
    transition: var(--transition);
}

.tab-item.active .tab-icon {
    color: var(--text-primary); /* Color cuando está activo */
}

.tab-label {
    font-size: 0.65rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-align: center;
}

.tab-item.active .tab-label {
    color: var(--text-primary);
}

.tab-count {
    font-size: 0.6rem;
    font-weight: 600;
    color: white;
    background: var(--text-secondary);
    padding: 1px 4px;
    border-radius: 8px;
    margin-top: 2px;
    min-width: 16px;
    text-align: center;
}

.tab-item.active .tab-count {
    background: #073B4C;
}

/* Lista de notificaciones */
.notifications-list {
    max-height: 320px;
    overflow-y: auto;
    padding: 8px;
}

.notifications-list::-webkit-scrollbar {
    width: 6px;
}

.notifications-list::-webkit-scrollbar-track {
    background: var(--border-light);
    border-radius: 3px;
}

.notifications-list::-webkit-scrollbar-thumb {
    background: #CED4DA;
    border-radius: 3px;
}

/* Elemento de notificación */
.notifications-item {
    margin-bottom: 8px;
    border-radius: 8px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.notifications-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.notifications-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}

.notifications-item.unassigned::before {
    background: linear-gradient(135deg, #FFD166, #F77F00);
}

.notifications-item.inprogress::before {
    background: linear-gradient(135deg, #118AB2, #073B4C);
}

.notifications-item.review::before {
    background: linear-gradient(135deg, #7209B7, #560BAD);
}

.notifications-item.closed::before {
    background: linear-gradient(135deg, #06D6A0, #0A9396);
}

.notifications-desc {
    padding: 12px;
    background: white;
    border: 1px solid var(--border-light);
    border-radius: 8px;
}

.notification-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--pastel-gray);
    color: var(--text-secondary);
    flex-shrink: 0;
}

.notifications-item.unassigned .notification-icon {
    background: var(--pastel-yellow);
    color: #856404;
}

.notifications-item.inprogress .notification-icon {
    background: var(--pastel-blue);
    color: #084298;
}

.notifications-item.review .notification-icon {
    background: var(--pastel-purple);
    color: #4C1D95;
}

.notifications-item.closed .notification-icon {
    background: var(--pastel-green);
    color: #0F5132;
}

.single-task-list-link {
    text-decoration: none;
    color: inherit;
}

.single-task-list-link:hover {
    text-decoration: none;
    color: inherit;
}

.notifications-date {
    margin-top: 8px;
    padding-top: 8px;
    font-size: 0.75rem;
}

/* Pie de página */
.notifications-footer {
    padding: 12px;
    background: var(--pastel-gray);
    border-top: 1px solid var(--border-light);
}

.view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.view-all-link:hover {
    color: #073B4C;
    gap: 10px;
    text-decoration: none;
}

/* Estados vacíos */
.empty-icon {
    font-size: 2rem;
    margin-bottom: 8px;
    opacity: 0.7;
}

/* Responsive */
@media (max-width: 576px) {
    .notification-panel {
        width: 320px;
        max-height: 450px;
    }
    
    .notification-tabs {
        gap: 4px;
    }
    
    .tab-item {
        min-width: 60px;
        padding: 6px 8px;
    }
    
    .tab-icon {
        font-size: 1rem;
    }
    
    .tab-label {
        font-size: 0.6rem;
    }
}
</style>

<!-- ============================================
   JAVASCRIPT - FUNCIONALIDAD DE NOTIFICACIONES
   ============================================ -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contador = document.getElementById('contadorNotificaciones');
        const notificationIndicator = document.getElementById('notificationIndicator');
        const refreshBtn = document.getElementById('refreshBtn');
        const tabItems = document.querySelectorAll('.tab-item');
        
        let previousCount = 0;
        let notificationsData = {
            sinAsignar: [],
            enProceso: [],
            revision: [],
            cerrados: []
        };
        let currentCategory = 'sinAsignar';
        
        // Inicializar
        actualizarNotificacionesDetalladas();
        setInterval(actualizarNotificacionesDetalladas, 10000);
        
        // Event listeners
        refreshBtn.addEventListener('click', () => {
            refreshBtn.classList.add('spinning');
            actualizarNotificacionesDetalladas().finally(() => {
                setTimeout(() => {
                    refreshBtn.classList.remove('spinning');
                }, 1000);
            });
        });
        
        tabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                // Actualizar estado activo
                tabItems.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Cambiar categoría
                currentCategory = tab.getAttribute('data-category');
                mostrarNotificacionesPorCategoria(currentCategory);
            });
        });
        
        function actualizarNotificacionesDetalladas() {
            return fetch('{{ route('soportes.notificaciones.detalladas') }}')
                .then(data => {
                    notificationsData.sinAsignar = data.sinAsignar || [];
                    notificationsData.enProceso = data.enProceso || [];
                    notificationsData.revision = data.revision || [];
                    notificationsData.cerrados = data.cerrados || [];
                    
                    // Actualizar contador principal (solo no cerrados)
                    const totalCount = data.total || 0;
                    contador.textContent = totalCount;
                    
                    // Actualizar contadores de categorías
                    document.getElementById('countSinAsignar').textContent = data.sinAsignar_count || 0;
                    document.getElementById('countEnProceso').textContent = data.enProceso_count || 0;
                    document.getElementById('countRevision').textContent = data.revision_count || 0;
                    document.getElementById('countCerrados').textContent = data.cerrados_count || 0;
                    
                    // Mostrar indicador de nuevas notificaciones
                    if (totalCount > previousCount && previousCount > 0) {
                        mostrarIndicadorNuevasNotificaciones();
                    }
                    previousCount = totalCount;
                    
                    // Mostrar notificaciones según la categoría actual
                    mostrarNotificacionesPorCategoria(currentCategory);
                })
                .catch(err => {
                    console.error('Error al obtener notificaciones:', err);
                    mostrarErrorCarga();
                });
        }
        
        function mostrarNotificacionesPorCategoria(category) {
            const lista = document.getElementById('listaNotificaciones');
            lista.innerHTML = '';
            
            let notificaciones = [];
            
            switch(category) {
                case 'sinAsignar':
                    notificaciones = notificationsData.sinAsignar;
                    break;
                case 'enProceso':
                    notificaciones = notificationsData.enProceso;
                    break;
                case 'revision':
                    notificaciones = notificationsData.revision;
                    break;
                case 'cerrados':
                    notificaciones = notificationsData.cerrados;
                    break;
            }
            
            if (notificaciones.length > 0) {
                notificaciones.forEach(item => {
                    const notificationElement = crearElementoNotificacion(item);
                    lista.appendChild(notificationElement);
                });
            } else {
                mostrarEstadoVacio(category);
            }
        }
        
        function crearElementoNotificacion(item) {
            const notificationDiv = document.createElement('div');
            const estadoId = item.estado_id;
            
            // Determinar clase CSS según el estado (solo para el estilo visual)
            let estadoClass = '';
            let estadoIcon = '';
            
            switch(estadoId) {
                case '1': // Sin Asignar
                    estadoClass = 'unassigned';
                    estadoIcon = 'user-x';
                    break;
                case '2': // En Proceso
                    estadoClass = 'inprogress';
                    estadoIcon = 'loader';
                    break;
                case '3': // Revisión
                    estadoClass = 'review';
                    estadoIcon = 'eye';
                    break;
                case '4': // Cerrado
                    estadoClass = 'closed';
                    estadoIcon = 'check-circle';
                    break;
                default:
                    estadoClass = 'default';
                    estadoIcon = 'help-circle';
            }
            
            notificationDiv.className = `notifications-item ${estadoClass}`;
            notificationDiv.setAttribute('data-id', item.id);
            
            notificationDiv.innerHTML = `
                <div class="notifications-desc">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon me-2">
                            <i class="feather-${estadoIcon}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <a href="/soportes/soportes/${item.id}" class="single-task-list-link">
                                <div class="fs-13 fw-bold text-truncate-1-line">
                                    ${item.usuario_nombre}
                                    <span class="ms-2 badge bg-soft-${item.prioridad_color} text-${item.prioridad_color}">${item.prioridad}</span>
                                </div>
                                <div class="fs-12 fw-normal text-muted">${item.detalles_soporte}</div>
                            </a>
                            <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                ${item.fecha_creacion}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            return notificationDiv;
        }
        
        function mostrarIndicadorNuevasNotificaciones() {
            notificationIndicator.classList.add('show');
            
            setTimeout(() => {
                notificationIndicator.classList.remove('show');
            }, 5000);
        }
        
        function mostrarEstadoVacio(category) {
            const lista = document.getElementById('listaNotificaciones');
            
            let icon = '';
            let message = '';
            
            switch(category) {
                case 'sinAsignar':
                    icon = 'feather-user-x';
                    message = 'No hay soportes sin asignar';
                    break;
                case 'enProceso':
                    icon = 'feather-loader';
                    message = 'No hay soportes en proceso';
                    break;
                case 'revision':
                    icon = 'feather-eye';
                    message = 'No hay soportes en revisión';
                    break;
                case 'cerrados':
                    icon = 'feather-check-circle';
                    message = 'No hay soportes cerrados';
                    break;
            }
            
            lista.innerHTML = `
                <div class="text-center text-muted p-4">
                    <i class="${icon} empty-icon"></i>
                    <p class="mt-2">${message}</p>
                </div>
            `;
        }
        
        function mostrarErrorCarga() {
            const lista = document.getElementById('listaNotificaciones');
            lista.innerHTML = `
                <div class="text-center text-muted p-4">
                    <i class="feather-alert-circle"></i>
                    <p class="mt-2">Error al cargar las notificaciones</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="actualizarNotificacionesDetalladas()">Reintentar</button>
                </div>
            `;
        }
    });
</script>