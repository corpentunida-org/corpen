<!-- ============================================
   COMPONENTE DE NOTIFICACIONES DE SOPORTES
   ============================================ -->
<!-- MEJORA 14: Fallback para navegadores sin JavaScript -->
<noscript>
    <div class="nxl-h-item">
        <a href="{{ route('soportes.soportes.index') }}" class="nxl-head-link me-3 position-relative">
            <i class="feather-bell"></i>
            <span class="badge bg-danger nxl-h-badge">?</span>
        </a>
    </div>
</noscript>

<div class="d-flex align-items-center">
    @candirect('interacciones.chat.index')
    <div class="nxl-h-item d-none d-sm-flex me-2" id="messengerComponent">
        <div class="nxl-head-link position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMessenger"
            aria-controls="offcanvasMessenger" role="button" title="Mensajes Directos">
            <i class="feather-message-square notification-bell"></i>
        </div>
    </div>
    @endcandirect

    <div class="dropdown nxl-h-item d-none" id="notificationComponent">
        <div class="nxl-head-link me-3 position-relative" data-bs-toggle="dropdown" role="button"
            data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false" id="notificationDropdownButton">
            <i class="feather-bell notification-bell"></i>
            <span class="badge bg-danger nxl-h-badge pulse-animation" id="contadorNotificaciones"
                aria-label="Notificaciones no leídas"></span>
            <div class="notification-indicator" id="notificationIndicator" aria-hidden="true"></div>
        </div>

        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu notification-panel"
            role="region" aria-labelledby="notificationDropdownButton" aria-live="polite">
            <div class="notifications-header">
                <div class="d-flex justify-content-between align-items-center notifications-head">
                    <h6 class="fw-bold mb-0">Centro de Soportes</h6>
                    <div class="flex-shrink-0 d-flex">
                        <button class="btn btn-sm btn-icon me-1" id="speechToggle"
                            title="Activar/Desactivar lectura por voz" aria-label="Activar lectura por voz"
                            aria-pressed="false">
                            <i class="feather-message-circle" id="speechIcon"></i>
                        </button>
                        <button class="btn btn-sm btn-icon me-1" id="soundToggle" title="Activar/Desactivar sonido"
                            aria-label="Activar sonido de notificación" aria-pressed="false">
                            <i class="feather-volume-2" id="soundIcon"></i>
                        </button>
                        <button class="btn btn-sm btn-icon refresh-btn" id="refreshBtn" title="Actualizar"
                            aria-label="Actualizar notificaciones">
                            <i class="feather-refresh-cw"></i>
                        </button>
                    </div>
                </div>

                <div class="notification-tabs" role="tablist">
                    <div class="tab-item active" data-category="sinAsignar" role="tab" aria-selected="true"
                        aria-controls="listaNotificaciones" tabindex="0">
                        <i class="feather-user-x tab-icon"></i>
                        <span class="tab-label">Sin Asignar</span>
                        <span class="tab-count" id="countSinAsignar" aria-label="Conteo de sin asignar">0</span>
                    </div>
                    <div class="tab-item" data-category="enProceso" role="tab" aria-selected="false"
                        aria-controls="listaNotificaciones" tabindex="-1">
                        <i class="feather-loader tab-icon"></i>
                        <span class="tab-label">En Proceso</span>
                        <span class="tab-count" id="countEnProceso" aria-label="Conteo de en proceso">0</span>
                    </div>
                    <div class="tab-item" data-category="revision" role="tab" aria-selected="false"
                        aria-controls="listaNotificaciones" tabindex="-1">
                        <i class="feather-eye tab-icon"></i>
                        <span class="tab-label">Revisión</span>
                        <span class="tab-count" id="countRevision" aria-label="Conteo en revisión">0</span>
                    </div>
                    <div class="tab-item" data-category="cerrados" role="tab" aria-selected="false"
                        aria-controls="listaNotificaciones" tabindex="-1">
                        <i class="feather-check-circle tab-icon"></i>
                        <span class="tab-label">Cerrados</span>
                        <span class="tab-count" id="countCerrados" aria-label="Conteo de cerrados">0</span>
                    </div>
                </div>
            </div>

            <div id="listaNotificaciones" class="notifications-list" role="tabpanel"
                aria-label="Lista de notificaciones">
                <div id="skeletonLoader" class="skeleton-loader d-none">
                </div>
            </div>

            <div class="text-center notifications-footer">
                <small class="text-muted d-block mb-2" id="lastSyncTimestamp">Actualizando...</small>
                <a href="{{ route('soportes.soportes.index') }}" class="fs-13 fw-semibold text-dark view-all-link">
                    VER TODOS SOPORTES
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ============================================
   ESTILOS CSS - DISEÑO CORPORATIVO MINIMALISTA
   ============================================ -->
<style>
    /* ... (Todos los estilos anteriores se mantienen) ... */
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
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .notification-bell {
        transition: var(--transition);
    }

    .notification-bell:hover {
        transform: scale(1.05);
        color: #073B4C;
    }

    .pulse-animation {
        background: linear-gradient(135deg, #FFD166, #F77F00) !important;
        animation: pulse 2s infinite;
    }

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

    /* Mismo tamaño en escritorio y en celular (440px) — igual que la campanita de Alertas de
       Interacciones, así ninguna de las dos queda más angosta que su propio contenido. */
    /* !important porque el tema trae ".nxl-header .header-wrapper .nxl-h-dropdown{width:
       225px}" (selector de 3 clases, más específico que ".notification-panel" solo) — sin esto
       ESE 225px es el que de verdad se estaba aplicando, no el tamaño que se ve más abajo. */
    .notification-panel {
        width: 440px !important;
        max-height: 520px !important;
        border-radius: 14px;
        border: none;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: slideIn 0.3s ease;
    }

    /* Banner de color propio (no gris) — mismo patrón que la campanita de Alertas de
       Interacciones, pero en azul: este panel es de Soportes, no de urgencias de Daytrack. */
    .notifications-header {
        padding: 16px 16px 14px 16px;
        background: linear-gradient(135deg, #0c3572 0%, #1d4ed8 55%, #2563eb 100%);
        border-bottom: none;
    }

    .notifications-head h6 {
        font-size: 1rem;
        color: #fff;
    }

    .btn-icon {
        background: rgba(255, 255, 255, .18);
        border: 1px solid rgba(255, 255, 255, .25);
        padding: 5px 7px;
        border-radius: 6px;
        color: #fff;
        transition: var(--transition);
        flex-shrink: 0;
    }

    .btn-icon:hover {
        background: rgba(255, 255, 255, .25);
        color: #fff;
    }

    .btn-icon.spinning i {
        animation: spin 1s linear infinite;
    }

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

    /* flex:1 reparte el ancho disponible entre las 4 pestañas a partes iguales — así SIEMPRE
       caben las 4 dentro del panel sin desbordarse ni necesitar scroll horizontal, sin importar
       el ancho de pantalla (antes tenían min-width:70px fijo, así que en celular "Cerrados" no
       cabía y quedaba cortado en el borde). overflow-x:auto en .notification-tabs se deja como
       respaldo por si algún día se agrega una quinta pestaña. */
    .tab-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 6px;
        border-radius: 10px;
        background: white;
        border: 1px solid var(--border-light);
        cursor: pointer;
        transition: var(--transition);
        flex: 1 1 0;
        min-width: 0;
    }

    .tab-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    /* Pestaña activa en azul sólido y saturado (no pastel) — mismo criterio "de semáforo" que
       la campanita de Alertas de Interacciones: colores claros y con contraste, no lavados. */
    .tab-item.active {
        background: #2563eb;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .tab-icon {
        font-size: 1.1rem;
        margin-bottom: 4px;
        color: var(--text-secondary);
        transition: var(--transition);
    }

    .tab-item.active .tab-icon {
        color: #fff;
    }

    .tab-label {
        font-size: 0.65rem;
        font-weight: 500;
        color: var(--text-secondary);
        text-align: center;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .tab-item.active .tab-label {
        color: #fff;
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
        background: rgba(255, 255, 255, .3);
    }

    .notifications-list {
        max-height: 320px;
        overflow-y: auto;
        padding: 8px;
        transition: max-height 0.4s ease;
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

    .notifications-item {
        margin-bottom: 8px;
        border-radius: 8px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
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

    .empty-icon {
        font-size: 2rem;
        margin-bottom: 8px;
        opacity: 0.7;
    }

    /* MEJORA 15: Estilos para Skeleton Loading */
    .skeleton-loader {
        padding: 8px;
    }

    .skeleton-item {
        margin-bottom: 8px;
        border-radius: 8px;
        padding: 12px;
        background: white;
        border: 1px solid var(--border-light);
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    .skeleton-title {
        height: 16px;
        width: 60%;
        margin-bottom: 8px;
    }

    .skeleton-text {
        height: 12px;
        width: 100%;
        margin-bottom: 4px;
    }

    .skeleton-text:last-child {
        width: 80%;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    /* MEJORA 13: Estilos para Botón de Lectura */
    .speak-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid var(--border-light);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-secondary);
        font-size: 0.9rem;
        transition: var(--transition);
        z-index: 10;
    }

    .notifications-item:hover .speak-btn {
        display: flex;
    }

    .speak-btn:hover {
        background: var(--pastel-blue);
        color: white;
        transform: scale(1.1);
    }

    .speak-btn.speaking {
        animation: speaking-pulse 1.5s infinite;
    }

    /* MEJORA 18: Estilos para Botón de "Marcar como Leído" */
    .read-btn {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid var(--border-light);
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: var(--text-secondary);
        font-size: 0.9rem;
        transition: var(--transition);
        z-index: 10;
    }

    .notifications-item:hover .read-btn {
        display: flex;
    }

    .read-btn:hover {
        background: var(--pastel-green);
        color: white;
        transform: scale(1.1);
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
        0% {
            box-shadow: 0 0 0 0 rgba(255, 209, 102, 0.7);
        }

        70% {
            box-shadow: 0 0 0 8px rgba(255, 209, 102, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 209, 102, 0);
        }
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes speaking-pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }

    /* MEJORA 9: Micro-interacciones Suaves */
    .notifications-item:hover {
        transform: translateY(-1px) scale(1.005);
        box-shadow: var(--shadow-md);
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .notifications-item.new {
        animation: slideInRight 0.25s ease-out;
    }

    /* MEJORA 12: Estilos para Gestos Táctiles */
    .notifications-item {
        position: relative;
        touch-action: pan-y;
    }

    .notifications-item.swipe-out {
        opacity: 0.3;
        transform: translateX(-100%);
        transition: all 0.3s ease-out;
    }

    @media (max-width: 576px) {
        /* Igual que Alertas de Interacciones: se ajusta al ancho real de pantalla (con margen a
           los lados) sin pasar de 440px, en vez de quedar fijo y angosto en 320px. */
        .notification-panel {
            width: calc(100vw - 24px) !important;
            max-width: 440px !important;
            max-height: 70vh !important;
        }

        .notification-tabs {
            gap: 4px;
        }

        .tab-item {
            padding: 6px 4px;
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
        // ============================================
        // VARIABLES Y REFERENCIAS AL DOM
        // ============================================
        const notificationComponent = document.getElementById('notificationComponent');
        const notificationDropdownButton = document.getElementById('notificationDropdownButton');
        const contador = document.getElementById('contadorNotificaciones');
        const notificationIndicator = document.getElementById('notificationIndicator');
        const refreshBtn = document.getElementById('refreshBtn');
        const tabItems = document.querySelectorAll('.tab-item');
        const listaNotificaciones = document.getElementById('listaNotificaciones');
        const skeletonLoader = document.getElementById('skeletonLoader');
        const lastSyncTimestamp = document.getElementById('lastSyncTimestamp');

        // Estado de la aplicación
        let previousCount = 0;
        let notificationsData = {
            sinAsignar: [],
            enProceso: [],
            revision: [],
            cerrados: []
        };
        let currentCategory = 'sinAsignar';
        let lastSyncTime = null;

        // ============================================
        // MEJORA 21: Variables para Control de Actualizaciones
        // ============================================
        let autoUpdateInterval = null;
        let isAutoUpdatePaused = false;
        let pauseTimeout = null;
        const PAUSE_DURATION = 15000; // 15 segundos

        // ============================================
        // MEJORA 13: Variables para Texto a Voz
        // ============================================
        let speechEnabled = false;
        const speechToggle = document.getElementById('speechToggle');
        const speechIcon = document.getElementById('speechIcon');
        let currentUtterance = null;

        // ============================================
        // MEJORA 5: Variables para Notificaciones Sonoras
        // ============================================
        let audioEnabled = false;
        let audioContext = null;
        const soundToggle = document.getElementById('soundToggle');
        const soundIcon = document.getElementById('soundIcon');

        function initAudioContext() {
            if (!audioContext && window.AudioContext) {
                audioContext = new AudioContext();
            }
        }

        // ============================================
        // MEJORA 19: Variables para Debouncing
        // ============================================
        let refreshTimeout = null;
        const DEBOUNCE_DELAY = 500; // ms

        // ============================================
        // MEJORA 12: Variables para Gestos Táctiles
        // ============================================
        let touchStartX = 0;
        let touchEndX = 0;
        let currentSwipedItem = null;

        // ============================================
        // INICIALIZACIÓN
        // ============================================
        function init() {
            if (notificationComponent) {
                notificationComponent.classList.remove('d-none'); // Mostrar componente si JS está activo
            }

            initAudioContext();
            actualizarNotificacionesDetalladas();
            setupAutoUpdate(); // MEJORA 21: Usar nueva función para configurar actualizaciones
            setupEventListeners();
            setupTouchGestures();
            setupKeyboardNavigation(); // MEJORA 20
            updateLastSyncTimestamp();
            ejecutarCancelacionDiaria(); // Ejecutar función de cancelación diaria
            iniciarAvisoForzadoSoportes(); // Aviso forzado (soportes asignados a mí, sin cerrar)
        }

        // ============================================
        // AVISO FORZADO DE SOPORTES ASIGNADOS SIN CERRAR
        // Réplica del mismo mecanismo de Interacciones (components/alertas-interacciones.blade.php):
        // cada tantas horas (configurable en Admin → Configuración de Alertas de Soportes) se
        // fuerza a elegir "Responder Ahora" o "Posponer" — 3 (configurable) días seguidos
        // posponiendo sin responder ninguno escala a superadmin/admindesarrollo.
        // ============================================
        let avisoSoportesIntervaloMs = 3 * 60 * 60 * 1000;
        let avisoSoportesUltimosDatos = [];
        const AVISO_SOPORTES_LS_KEY = 'soportes_ultimo_aviso_pendientes';

        function iniciarAvisoForzadoSoportes() {
            consultarPendientesSoportes(true);
            setInterval(() => consultarPendientesSoportes(true), 5 * 60 * 1000);
        }

        function consultarPendientesSoportes(evaluarAviso) {
            fetch('{{ route('soportes.alertas.pendientes') }}', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    avisoSoportesUltimosDatos = data.pendientes || [];
                    if (data.aviso_intervalo_horas) {
                        avisoSoportesIntervaloMs = data.aviso_intervalo_horas * 60 * 60 * 1000;
                    }
                    if (evaluarAviso) {
                        evaluarAvisoForzadoSoportes(data.pendientes_count ?? 0);
                    }
                })
                .catch(err => console.error('Error consultando pendientes de soportes:', err));
        }

        function registrarDecisionSoporte(decision) {
            return fetch('{{ route('soportes.alertas.decision') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ decision }),
            }).then(r => r.json()).catch(err => {
                console.error('Error registrando decisión de alerta de soporte:', err);
                return { streak: null, escalado: false };
            });
        }

        function evaluarAvisoForzadoSoportes(countPendientes) {
            if (countPendientes <= 0) return;

            let ultimoAviso = 0;
            try {
                ultimoAviso = parseInt(localStorage.getItem(AVISO_SOPORTES_LS_KEY) || '0', 10);
            } catch (e) { /* almacenamiento no disponible — se sigue igual */ }

            const ahora = Date.now();
            if (ahora - ultimoAviso < avisoSoportesIntervaloMs) return;
            if (typeof Swal === 'undefined') return;

            const itemsHtml = avisoSoportesUltimosDatos.slice(0, 5).map(item => `
                <div style="text-align:left; padding:8px 10px; margin-bottom:6px; background:#fff; border:1px solid #f1f1f1; border-left:3px solid #1d4ed8; border-radius:6px;">
                    <div style="font-size:13px; font-weight:600; color:#212529;">${item.detalle}</div>
                    <div style="font-size:11.5px; color:#6c757d; margin-top:2px;">
                        ${item.estado} · <span style="color:#1d4ed8; font-weight:700;">${item.prioridad}</span>
                    </div>
                </div>`).join('');
            const masTexto = countPendientes > 5 ? `<div style="text-align:center; font-size:12px; color:#6c757d;">y ${countPendientes - 5} más...</div>` : '';

            Swal.fire({
                html: `
                    <div style="margin:-20px -24px 16px -24px; padding:16px 24px 14px 24px; background:linear-gradient(135deg,#0c3572 0%,#1d4ed8 55%,#2563eb 100%); color:#fff; border-radius:8px 8px 0 0;">
                        <div style="font-weight:700; font-size:16px;">Centro de Soportes</div>
                        <div style="font-size:12px; opacity:.85; margin-top:2px;">Tienes ${countPendientes} soporte(s) asignado(s) sin cerrar — decide qué hacer</div>
                    </div>
                    <div style="max-height:220px; overflow-y:auto; padding:0 2px;">${itemsHtml}${masTexto}</div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Responder Ahora',
                confirmButtonColor: '#16a34a',
                showDenyButton: true,
                denyButtonText: 'Posponer',
                denyButtonColor: '#d97706',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false,
                width: 420,
                padding: '20px 24px 24px 24px',
            }).then((result) => {
                if (result.isConfirmed) {
                    registrarDecisionSoporte('responder');
                    window.location.href = '{{ route('soportes.soportes.index') }}';
                    return;
                }
                if (result.isDenied) {
                    registrarDecisionSoporte('posponer').then((r) => {
                        let mensaje = 'Te lo recordaremos más tarde.';
                        let icono = 'info';
                        if (r.escalado) {
                            mensaje = `Llevas ${r.streak} días seguidos posponiendo — se notificó a superadmin/admindesarrollo.`;
                            icono = 'warning';
                        } else if (r.streak >= 2) {
                            mensaje = `Llevas ${r.streak} días seguidos posponiendo. Si llegas al umbral configurado, se escala.`;
                            icono = 'warning';
                        }
                        Swal.fire({
                            icon: icono,
                            title: 'Pospuesto',
                            text: mensaje,
                            timer: 5000,
                            timerProgressBar: true,
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#6c757d',
                        });
                    });
                }
            });

            try {
                localStorage.setItem(AVISO_SOPORTES_LS_KEY, String(ahora));
            } catch (e) { /* ignorar si no hay almacenamiento disponible */ }
        }

        // ============================================
        // MEJORA 21: Función para Configurar Actualizaciones Automáticas
        // ============================================
        function setupAutoUpdate() {
            // Limpiar intervalo existente si hay uno
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
            }

            // Configurar nuevo intervalo
            autoUpdateInterval = setInterval(() => {
                const dropdownOpen = document.querySelector('.dropdown-menu.show');
                if (dropdownOpen && !isAutoUpdatePaused) {
                    actualizarNotificacionesDetalladas();
                }
            }, 30000);
        }

        // ============================================
        // MEJORA 21: Función para Pausar Actualizaciones
        // ============================================
        function pauseAutoUpdate() {
            isAutoUpdatePaused = true;

            // Limpiar timeout existente si hay uno
            if (pauseTimeout) {
                clearTimeout(pauseTimeout);
            }

            // Configurar nuevo timeout para reanudar actualizaciones
            pauseTimeout = setTimeout(() => {
                isAutoUpdatePaused = false;
            }, PAUSE_DURATION);

            //console.log('Actualizaciones automáticas pausadas por', PAUSE_DURATION / 1000, 'segundos');
        }

        function ejecutarCancelacionDiaria() {
            const ultimaEjecucion = localStorage.getItem('cancelacion_reservas_fecha');
            const hoy = new Date().toISOString().split('T')[0];
            if (ultimaEjecucion === hoy) {
                return;
            }
            fetch('{{ route('reservas.cancelar.auto') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.mensaje);
                    localStorage.setItem('cancelacion_reservas_fecha', hoy);
                })
                .catch(error => {
                    console.error('Error cancelando reservas:', error);
                });
        }
        // ============================================
        // CONFIGURACIÓN DE EVENT LISTENERS
        // ============================================
        function setupEventListeners() {
            // MEJORA 13: Listener para el toggle de lectura
            speechToggle.addEventListener('click', () => {
                speechEnabled = !speechEnabled;
                speechIcon.style.color = speechEnabled ? 'var(--text-primary)' :
                    'var(--text-secondary)';
                speechToggle.setAttribute('aria-pressed', speechEnabled);
                trackEvent(`Lectura por voz ${speechEnabled ? 'activada' : 'desactivada'}`);
            });

            // MEJORA 5: Listener para el toggle de sonido
            soundToggle.addEventListener('click', () => {
                audioEnabled = !audioEnabled;
                soundIcon.className = audioEnabled ? 'feather-volume-2' : 'feather-volume-x';
                soundToggle.setAttribute('aria-pressed', audioEnabled);
                trackEvent(`Sonido ${audioEnabled ? 'activado' : 'desactivado'}`);
            });

            refreshBtn.addEventListener('click', () => {
                debouncedRefresh();
            });

            tabItems.forEach(tab => {
                tab.addEventListener('click', () => selectTab(tab));
            });
        }

        // ============================================
        // MEJORA 20: Navegación por Teclado y Accesibilidad
        // ============================================
        function setupKeyboardNavigation() {
            // Navegación con flechas entre pestañas
            document.addEventListener('keydown', (e) => {
                if (['ArrowLeft', 'ArrowRight'].includes(e.key)) {
                    const activeTab = document.querySelector('.tab-item[aria-selected="true"]');
                    const tabs = Array.from(tabItems);
                    const currentIndex = tabs.indexOf(activeTab);

                    let newIndex;
                    if (e.key === 'ArrowLeft') {
                        newIndex = currentIndex > 0 ? currentIndex - 1 : tabs.length - 1;
                    } else {
                        newIndex = currentIndex < tabs.length - 1 ? currentIndex + 1 : 0;
                    }

                    if (tabs[newIndex]) {
                        selectTab(tabs[newIndex]);
                        tabs[newIndex].focus();
                    }
                }
            });

            // Gestionar el foco al abrir/cerrar el dropdown
            const dropdown = notificationDropdownButton.nextElementSibling;
            notificationDropdownButton.addEventListener('shown.bs.dropdown', () => {
                document.querySelector('.tab-item[aria-selected="true"]').focus();
            });
            notificationDropdownButton.addEventListener('hidden.bs.dropdown', () => {
                notificationDropdownButton.focus();
            });
        }

        function selectTab(tab) {
            tabItems.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
                t.setAttribute('tabindex', '-1');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');
            tab.setAttribute('tabindex', '0');

            currentCategory = tab.getAttribute('data-category');
            mostrarNotificacionesPorCategoria(currentCategory);
            //trackEvent(`Clic en pestaña: ${currentCategory}`);
        }

        // ============================================
        // MEJORA 19: Función de Refresh con Debounce
        // ============================================
        function debouncedRefresh() {
            clearTimeout(refreshTimeout);
            refreshBtn.classList.add('spinning');
            refreshTimeout = setTimeout(() => {
                actualizarNotificacionesDetalladas().finally(() => {
                    setTimeout(() => refreshBtn.classList.remove('spinning'), 1000);
                });
            }, DEBOUNCE_DELAY);
            //trackEvent('Actualización manual');
        }

        // ============================================
        // MEJORA 13: Funciones de Texto a Voz
        // ============================================
        function speak(text, buttonElement) {
            if (!('speechSynthesis' in window) || !speechEnabled) return;
            window.speechSynthesis.cancel();
            document.querySelectorAll('.speak-btn').forEach(btn => btn.classList.remove('speaking'));

            currentUtterance = new SpeechSynthesisUtterance(text);
            currentUtterance.lang = 'es-ES';
            currentUtterance.rate = 0.9;

            currentUtterance.onstart = () => {
                if (buttonElement) buttonElement.classList.add('speaking');
            };
            currentUtterance.onend = () => {
                if (buttonElement) buttonElement.classList.remove('speaking');
            };

            window.speechSynthesis.speak(currentUtterance);
            trackEvent('Notificación leída en voz alta');
        }

        function stopSpeaking() {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                document.querySelectorAll('.speak-btn').forEach(btn => btn.classList.remove('speaking'));
            }
        }

        // ============================================
        // MEJORA 12: Configuración de Gestos Táctiles
        // ============================================
        function setupTouchGestures() {
            document.addEventListener('touchstart', handleTouchStart, {
                passive: true
            });
            document.addEventListener('touchend', handleTouchEnd, {
                passive: true
            });
        }

        function handleTouchStart(e) {
            const notificationItem = e.target.closest('.notifications-item');
            if (!notificationItem) return;
            touchStartX = e.changedTouches[0].screenX;
            currentSwipedItem = notificationItem;
        }

        function handleTouchEnd(e) {
            if (!currentSwipedItem) return;
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe(currentSwipedItem);
            currentSwipedItem = null;
        }

        function handleSwipe(element) {
            const swipeThreshold = 120;
            const diff = touchStartX - touchEndX;
            if (diff > swipeThreshold) {
                archiveNotification(element);
            }
        }

        function archiveNotification(element) {
            const id = element.getAttribute('data-id');
            element.classList.add('swipe-out');

            // MEJORA 21: Pausar actualizaciones automáticas
            pauseAutoUpdate();

            fetch(`/soportes/notificaciones/${id}/archivar`, {
                    method: 'POST'
                })
                .then(() => {
                    setTimeout(() => actualizarNotificacionesDetalladas(), 300);
                    trackEvent('Notificación archivada');
                })
                .catch(err => {
                    console.error('Error al archivar:', err);
                    element.classList.remove('swipe-out');
                });
        }

        // ============================================
        // FUNCIONES DE OBTENCIÓN DE DATOS
        // ============================================
        function actualizarNotificacionesDetalladas() {
            // MEJORA 15: Mostrar Skeleton Loader
            mostrarSkeletonLoader();

            return fetch('{{ route('soportes.notificaciones.detalladas') }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    notificationsData.sinAsignar = data.sinAsignar || [];
                    notificationsData.enProceso = data.enProceso || [];
                    notificationsData.revision = data.revision || [];
                    notificationsData.cerrados = data.cerrados || [];

                    actualizarContadores(data);
                    mostrarNotificacionesPorCategoria(currentCategory);
                    updateLastSyncTimestamp(); // MEJORA 16

                    return data;
                })
                .catch(err => {
                    console.error('Error al obtener notificaciones:', err);
                    mostrarErrorCarga();
                    throw err;
                });
        }

        // ============================================
        // FUNCIONES DE ACTUALIZACIÓN DE UI
        // ============================================
        function actualizarContadores(data) {
            const totalCount = data.total || 0;
            contador.textContent = totalCount;

            document.getElementById('countSinAsignar').textContent = data.sinAsignar_count || 0;
            document.getElementById('countEnProceso').textContent = data.enProceso_count || 0;
            document.getElementById('countRevision').textContent = data.revision_count || 0;
            document.getElementById('countCerrados').textContent = data.cerrados_count || 0;

            if (totalCount > previousCount && previousCount > 0) {
                mostrarIndicadorNuevasNotificaciones(data);
            }
            previousCount = totalCount;
        }

        function mostrarNotificacionesPorCategoria(category) {
            skeletonLoader.classList.add('d-none');
            listaNotificaciones.innerHTML = '';

            const notificaciones = obtenerNotificacionesPorCategoria(category);

            if (notificaciones.length > 0) {
                notificaciones.forEach((item, index) => {
                    const notificationElement = crearElementoNotificacion(item);
                    notificationElement.style.animationDelay = `${index * 0.03}s`;
                    notificationElement.classList.add('new');
                    listaNotificaciones.appendChild(notificationElement);
                });
            } else {
                mostrarEstadoVacio(category);
            }
        }

        // MEJORA 15: Función para mostrar Skeletons
        function mostrarSkeletonLoader() {
            skeletonLoader.innerHTML = '';
            skeletonLoader.classList.remove('d-none');
            listaNotificaciones.innerHTML = '';

            for (let i = 0; i < 3; i++) {
                const skeletonItem = document.createElement('div');
                skeletonItem.className = 'skeleton-item';
                skeletonItem.innerHTML = `
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text"></div>
                <div class="skeleton skeleton-text"></div>
            `;
                skeletonLoader.appendChild(skeletonItem);
            }
        }

        function mostrarIndicadorNuevasNotificaciones(data) {
            notificationIndicator.classList.add('show');
            const altaPrioridad = data.sinAsignar.some(n => n.prioridad === 'Alta');
            if (altaPrioridad && audioEnabled) {
                reproducirNotificacionSonora();
            }
            setTimeout(() => {
                notificationIndicator.classList.remove('show');
            }, 5000);
        }

        function mostrarEstadoVacio(category) {
            const configuraciones = {
                sinAsignar: {
                    icon: 'feather-user-x',
                    message: 'No hay soportes sin asignar'
                },
                enProceso: {
                    icon: 'feather-loader',
                    message: 'No hay soportes en proceso'
                },
                revision: {
                    icon: 'feather-eye',
                    message: 'No hay soportes en revisión'
                },
                cerrados: {
                    icon: 'feather-check-circle',
                    message: 'No hay soportes cerrados'
                }
            };
            const config = configuraciones[category] || {
                icon: 'feather-info',
                message: 'No hay notificaciones'
            };
            listaNotificaciones.innerHTML = `
            <div class="text-center text-muted p-4">
                <i class="${config.icon} empty-icon"></i>
                <p class="mt-2">${config.message}</p>
            </div>
        `;
        }

        function mostrarErrorCarga() {
            skeletonLoader.classList.add('d-none');
            listaNotificaciones.innerHTML = `
            <div class="text-center text-muted p-4">
                <i class="feather-alert-circle"></i>
                <p class="mt-2">Error al cargar las notificaciones</p>
                <button class="btn btn-sm btn-primary mt-2" onclick="actualizarNotificacionesDetalladas()">Reintentar</button>
            </div>
        `;
        }

        // MEJORA 16: Función para actualizar el timestamp
        function updateLastSyncTimestamp() {
            // Verificación de seguridad: asegurar que el elemento existe
            if (!lastSyncTimestamp) {
                console.warn('El elemento #lastSyncTimestamp no fue encontrado en el DOM.');
                return;
            }

            try {
                lastSyncTime = new Date();
                const timeString = lastSyncTime.toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                lastSyncTimestamp.textContent = `Última sincronización: ${timeString}`;
            } catch (error) {
                console.error('Error al actualizar el timestamp de sincronización:', error);
            }
        }

        // ============================================
        // MEJORA 5: Funciones para Notificaciones Sonoras
        // ============================================
        function reproducirNotificacionSonora() {
            if (!audioContext || !audioEnabled) return;

            // Verificación de seguridad por si el contexto sigue suspendido
            if (audioContext.state === 'suspended') {
                console.warn(
                    'AudioContext está suspendido. El sonido no se puede reproducir sin una interacción del usuario primero.'
                );
                return;
            }

            try {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 650;
                gainNode.gain.value = 0.1;

                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.15);
            } catch (error) {
                console.error('Error al intentar reproducir el sonido de notificación:', error);
            }
        }

        // ============================================
        // MEJORA 8: Funciones de Seguimiento de Interacciones
        // ============================================
        function trackEvent(action, category = 'Notificaciones') {
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    'event_category': category,
                    'event_label': 'Centro de Soportes'
                });
            }
        }

        // ============================================
        // FUNCIONES AUXILIARES
        // ============================================
        function obtenerNotificacionesPorCategoria(category) {
            switch (category) {
                case 'sinAsignar':
                    return notificationsData.sinAsignar;
                case 'enProceso':
                    return notificationsData.enProceso;
                case 'revision':
                    return notificationsData.revision;
                case 'cerrados':
                    return notificationsData.cerrados;
                default:
                    return [];
            }
        }

        function crearElementoNotificacion(item) {
            const notificationDiv = document.createElement('div');
            const estadoConfig = obtenerConfiguracionEstado(item.estado_id);

            notificationDiv.className = `notifications-item ${estadoConfig.class}`;
            notificationDiv.setAttribute('data-id', item.id);
            notificationDiv.setAttribute('role', 'article');
            notificationDiv.setAttribute('aria-label', `Notificación: ${item.detalles_soporte}`);

            // MEJORA 13: Botón de lectura
            const speakButton = document.createElement('div');
            speakButton.className = 'speak-btn';
            speakButton.innerHTML = '<i class="feather-message-circle"></i>';
            speakButton.setAttribute('title', 'Leer en voz alta');
            speakButton.setAttribute('aria-label', 'Leer notificación en voz alta');
            const textToSpeak = `Soporte de ${item.usuario_nombre}. ${item.detalles_soporte}`;
            speakButton.addEventListener('click', (e) => {
                e.stopPropagation();
                speak(textToSpeak, speakButton);
            });

            // MEJORA 18: Botón de marcar como leído
            const readButton = document.createElement('div');
            readButton.className = 'read-btn';
            readButton.innerHTML = '<i class="feather-check"></i>';
            readButton.setAttribute('title', 'Marcar como leído');
            readButton.setAttribute('aria-label', 'Marcar notificación como leída');
            readButton.addEventListener('click', (e) => {
                e.stopPropagation();
                markAsRead(item.id, notificationDiv);
            });

            notificationDiv.innerHTML = `
            <div class="notifications-desc">
                <div class="d-flex align-items-start">
                    <div class="notification-icon me-2">
                        <i class="feather-${estadoConfig.icon}"></i>
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

            notificationDiv.appendChild(readButton);
            notificationDiv.appendChild(speakButton);

            const link = notificationDiv.querySelector('.single-task-list-link');
            link.addEventListener('click', () => {
                trackEvent(`Clic en notificación: ${item.id}`);
                stopSpeaking();
            });

            return notificationDiv;
        }

        // MEJORA 18: Función para marcar como leído
        function markAsRead(id, element) {
            // MEJORA 21: Pausar actualizaciones automáticas
            pauseAutoUpdate();

            fetch(`/soportes/notificaciones/${id}/leer`, {
                    method: 'POST'
                })
                .then(() => {
                    element.style.opacity = '0.5';
                    element.style.pointerEvents = 'none';
                    trackEvent('Notificación marcada como leída');
                })
                .catch(err => console.error('Error al marcar como leído:', err));
        }

        function obtenerConfiguracionEstado(estadoId) {
            const configuraciones = {
                '1': {
                    class: 'unassigned',
                    icon: 'user-x'
                },
                '2': {
                    class: 'inprogress',
                    icon: 'loader'
                },
                '3': {
                    class: 'review',
                    icon: 'eye'
                },
                '4': {
                    class: 'closed',
                    icon: 'check-circle'
                }
            };
            return configuraciones[estadoId] || {
                class: 'default',
                icon: 'help-circle'
            };
        }

        // ============================================
        // INICIALIZAR LA APLICACIÓN
        // ============================================
        init();
    });
</script>
