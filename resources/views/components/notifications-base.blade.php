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

<div class="dropdown nxl-h-item d-none" id="notificationComponent">
    <!-- Botón de activación del dropdown -->
    <div class="nxl-head-link me-3 position-relative" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside"
        aria-haspopup="true" aria-expanded="false" id="notificationDropdownButton">
        <i class="feather-bell notification-bell"></i>
        <span class="badge bg-danger nxl-h-badge pulse-animation" id="contadorNotificaciones"
            aria-label="Notificaciones no leídas"></span>
        <div class="notification-indicator" id="notificationIndicator" aria-hidden="true"></div>
    </div>

    <!-- Panel de notificaciones -->
    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu notification-panel" role="region"
        aria-labelledby="notificationDropdownButton" aria-live="polite">
        <!-- Cabecera del panel -->
        <div class="notifications-header">
            <div class="d-flex justify-content-between align-items-center notifications-head">
                <h6 class="fw-bold text-dark mb-0">Centro de Soportes</h6>
                <div>
                    <!-- MEJORA 13: Control de Lectura por Voz -->
                    <button class="btn btn-sm btn-icon me-1" id="speechToggle"
                        title="Activar/Desactivar lectura por voz" aria-label="Activar lectura por voz"
                        aria-pressed="false">
                        <i class="feather-message-circle" id="speechIcon"></i>
                    </button>
                    <!-- MEJORA 5: Control de Sonido -->
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

            <!-- Pestañas de categorías -->
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

        <!-- Lista de notificaciones -->
        <div id="listaNotificaciones" class="notifications-list" role="tabpanel" aria-label="Lista de notificaciones">
            <!-- MEJORA 15: Contenedor para Skeleton Loading -->
            <div id="skeletonLoader" class="skeleton-loader d-none">
                <!-- Los skeletons se generarán dinámicamente -->
            </div>
            <!-- Las notificaciones se cargarán aquí -->
        </div>

        <!-- Pie de página del panel -->
        <div class="text-center notifications-footer">
            <!-- MEJORA 16: Timestamp de última sincronización -->
            <small class="text-muted d-block mb-2" id="lastSyncTimestamp">Actualizando...</small>
            <a href="{{ route('soportes.soportes.index') }}" class="fs-13 fw-semibold text-dark view-all-link">
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

    .badge.bg-danger {
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

    .notification-panel {
        width: 380px;
        max-height: 500px;
        border-radius: 12px;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        animation: slideIn 0.3s ease;
    }

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

    .tab-icon {
        font-size: 1.1rem;
        margin-bottom: 4px;
        color: var(--text-secondary);
        transition: var(--transition);
    }

    .tab-item.active .tab-icon {
        color: var(--text-primary);
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
                // Solo actualizar si no está pausado
                if (!isAutoUpdatePaused) {
                    actualizarNotificacionesDetalladas();
                }
            }, 10000);
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
                console.log('Actualizaciones automáticas reanudadas');
            }, PAUSE_DURATION);

            console.log('Actualizaciones automáticas pausadas por', PAUSE_DURATION / 1000, 'segundos');
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
                console.log('Sonido de notificación reproducido.'); // Para depuración
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
            console.log(`Evento: ${action}`);
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
