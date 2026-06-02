<style>
    /* =========================================================
       0. VARIABLES GLOBALES (Añadido para facilitar el mantenimiento)
       ========================================================= */
    :root {
        /* Colores Base */
        --bg-main: #f8f9fa;
        --bg-white: #ffffff;
        --text-dark: #212529;
        --text-muted: #6c757d;
        --text-light-muted: #adb5bd;
        
        /* Bordes y Sombras */
        --border-color: #e9ecef;
        --border-input: #ced4da;
        --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 8px 15px rgba(0, 0, 0, 0.08);
        
        /* Utilidades */
        --radius-md: 6px;
        --radius-lg: 8px;
        --transition-base: all 0.2s ease-in-out;
        --font-family-base: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* =========================================================
       1. ENVOLTURA PRINCIPAL
       ========================================================= */
    .task-index-wrapper {
        padding: 2rem;
        background-color: var(--bg-main);
        min-height: calc(100vh - 60px);
        font-family: var(--font-family-base);
    }

    /* =========================================================
       2. HEADER DEL MÓDULO
       ========================================================= */
    .index-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end; /* Alinea los elementos al fondo */
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 1rem;
    }

    .system-tag {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: var(--text-muted);
        font-weight: 700;
        display: block;
        margin-bottom: 0.5rem;
    }

    .main-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0 0 0.25rem 0;
        line-height: 1.2;
    }

    .main-subtitle {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    /* Mejora Responsiva para el Header */
    @media (max-width: 768px) {
        .index-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        .header-actions {
            width: 100%;
            justify-content: flex-start;
            flex-wrap: wrap; /* Permite que los botones bajen si no caben */
        }
    }

    /* =========================================================
       3. BOTONES CORPORATIVOS
       ========================================================= */
    /* Estilo base compartido para botones */
    .btn-base {
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition-base);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    /* Botón Transparente (Ghost) */
    .btn-ghost-corporate {
        border: 1px solid var(--border-input);
        background: transparent;
        color: #495057;
    }

    .btn-ghost-corporate:hover,
    .btn-ghost-corporate:focus {
        background: var(--border-color);
        color: var(--text-dark);
        outline: none;
    }

    /* Botón Principal (Negro) */
    .btn-corporate-black {
        padding: 0.5rem 1.2rem;
        border: 1px solid var(--text-dark);
        background: var(--text-dark);
        color: var(--bg-white);
    }

    .btn-corporate-black:hover,
    .btn-corporate-black:focus {
        background: #343a40;
        border-color: #343a40;
        color: var(--bg-white);
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.25);
    }

    /* =========================================================
       4. TARJETAS DE VISUALIZACIÓN (SHOW CARDS)
       ========================================================= */
    .show-card-corp {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    .show-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #fcfcfc;
    }

    .show-header h2 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .show-body {
        padding: 2rem;
    }

    /* =========================================================
       5. BADGES DE ESTADO
       ========================================================= */
    .status-badge-large {
        padding: 0.5rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge-large i {
        font-size: 0.6rem;
    }

    /* Variantes de estado */
    .st-completed { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .st-pending   { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
    .st-danger    { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }

    /* =========================================================
       6. GRILLAS Y BLOQUES DE INFORMACIÓN
       ========================================================= */
    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .info-block {
        margin-bottom: 0.5rem;
    }

    .info-block h4 {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: var(--text-light-muted);
        margin-bottom: 0.3rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .info-block p {
        font-size: 1.05rem;
        color: var(--text-dark);
        margin: 0;
        font-weight: 500;
    }

    /* =========================================================
       7. ITEMS DETALLADOS (CON ICONOS)
       ========================================================= */
    .detail-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: var(--bg-main);
        padding: 1.2rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        transition: background-color 0.2s;
    }

    .detail-item:hover {
        background-color: #f1f3f5;
    }

    .detail-item .icon-muted {
        font-size: 1.8rem;
        color: var(--border-input);
    }

    .detail-item .label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.1rem;
        font-weight: 600;
    }

    .detail-item strong {
        display: block;
        font-size: 1.1rem;
        color: var(--text-dark);
    }

    /* =========================================================
       8. INPUTS DEL FORMULARIO
       ========================================================= */
    .corporate-input {
        border: 1px solid var(--border-input);
        border-radius: var(--radius-md);
        padding: 0.6rem 0.75rem;
        font-size: 0.95rem;
        background-color: var(--bg-white);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        width: 100%;
    }

    .corporate-input:focus {
        border-color: var(--text-dark);
        box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.1);
        outline: none;
    }

    /* =========================================================
       9. WIDGETS DASHBOARD SUPERIOR
       ========================================================= */
    .dash-widget {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dash-widget:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .widget-icon {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 1.2rem;
        flex-shrink: 0;
    }

    .widget-details {
        display: flex;
        flex-direction: column;
    }

    .widget-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.1;
    }

    .widget-title {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-top: 0.3rem;
    }

    /* Utilidades de Color para Dashboard */
    .border-bottom-primary { border-bottom: 4px solid #0d6efd; }
    .bg-primary-light      { background-color: rgba(13, 110, 253, 0.1); }
    .text-primary          { color: #0d6efd !important; }

    .border-bottom-success { border-bottom: 4px solid #198754; }
    .bg-success-light      { background-color: rgba(25, 135, 84, 0.1); }
    .text-success          { color: #198754 !important; }

    .border-bottom-info    { border-bottom: 4px solid #0dcaf0; }
    .bg-info-light         { background-color: rgba(13, 202, 240, 0.1); }
    .text-info             { color: #0dcaf0 !important; }

    .border-bottom-warning { border-bottom: 4px solid #ffc107; }
    .bg-warning-light      { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning          { color: #ffc107 !important; }

    .border-bottom-danger  { border-bottom: 4px solid #dc3545; }
    .bg-danger-light       { background-color: rgba(220, 53, 69, 0.1); }
    .text-danger           { color: #dc3545 !important; }

    /* =========================================================
       10. TARJETA FLOTANTE DE ARCHIVO (PREVIEW MODAL)
       ========================================================= */
    .archive-preview-card {
        position: absolute;
        z-index: 1060;
        width: 320px;
        background: var(--bg-white);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #dcdcdc;
        pointer-events: none;
        transition: opacity 0.15s ease-in-out;
        font-family: var(--font-family-base);
        overflow: hidden;
    }

    .preview-header {
        background-color: #1a1e22;
        color: #ffffff;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .text-primary-corporate {
        color: #0dcaf0;
        font-size: 1.4rem;
    }

    .preview-tag {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #adb5bd;
        display: block;
        font-weight: 700;
    }

    .preview-title {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 230px;
    }

    .preview-body {
        padding: 1.25rem;
        background-color: var(--bg-white);
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .p-item {
        display: flex;
        flex-direction: column;
    }

    .p-label {
        font-size: 0.7rem;
        color: #8c96a0;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
        margin-bottom: 0.15rem;
    }

    .p-item strong {
        font-size: 0.9rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    .preview-divider {
        height: 1px;
        background-color: var(--border-color);
        margin: 0.8rem 0;
    }

    .preview-notes {
        background-color: var(--bg-main);
        border-left: 3px solid var(--text-muted);
        padding: 0.5rem 0.75rem;
        margin-top: 0.75rem;
        border-radius: 0 4px 4px 0;
    }

    .preview-notes p {
        margin: 0;
        font-size: 0.75rem;
        color: #495057;
        line-height: 1.3;
        font-style: italic;
    }

    .row-hover-archive {
        transition: background-color 0.15s ease-in-out;
        cursor: help;
    }
    
    .row-hover-archive:hover {
        background-color: rgba(13, 110, 253, 0.04) !important;
    }

    /* =========================================================
       11. COMPONENTES UX - MÓDULO ECM
       ========================================================= */
    /* Tabla Avanzada */
    .ecm-table td {
        padding: 1rem 0.5rem;
        vertical-align: middle;
    }

    /* Avatares Dinámicos */
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #212529;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        letter-spacing: 1px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Icono de Radicado */
    .radicado-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .bg-light-primary { background-color: rgba(13, 110, 253, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }

    /* Caja de Ubicación Topográfica */
    .location-box {
        background-color: #f8f9fa;
        border: 1px dashed #ced4da;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.85rem;
    }

    /* Modificaciones al Menú Dropdown (Acciones) */
    .dropdown-menu {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px;
        padding: 0.5rem 0;
    }
    
    .dropdown-item {
        transition: background-color 0.2s ease, padding-left 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 1.5rem;
        color: #212529;
        font-weight: 500;
    }

    .dropdown-header {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        font-weight: 700;
        color: #adb5bd;
    }

    /* Barras de progreso suaves */
    .progress {
        background-color: #e9ecef;
        border-radius: 50px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 1s ease-in-out;
        border-radius: 50px;
    }

    /* =========================================================
       12. INTEGRACIÓN SELECT2 + BOOTSTRAP INPUT-GROUP-SM
       ========================================================= */
    /* 1. Forzar la altura exacta para que coincida con los inputs pequeños (SM) */
    .select2-premium-wrapper .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.5rem + 2px) !important;
        min-height: calc(1.5em + 0.5rem + 2px) !important;
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
        border: 1px solid #dee2e6 !important;
        border-left: 0 !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
        background-color: #fff;
    }

    /* 2. Evitar que el texto interior empuje la caja hacia abajo */
    .select2-premium-wrapper .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 0 !important;
        color: #495057 !important;
    }

    /* 3. Centrar la flecha desplegable de la derecha */
    .select2-premium-wrapper .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        right: 5px !important;
        display: flex !important;
        align-items: center !important;
    }

    /* 4. Igualar el tamaño de la caja del ícono de la izquierda */
    .select2-premium-wrapper .input-group-text {
        border: 1px solid #dee2e6 !important;
        border-right: 0 !important;
        padding: 0.25rem 0.6rem !important;
        font-size: 0.875rem !important;
        height: calc(1.5em + 0.5rem + 2px) !important;
    }

    /* 5. Quitar el borde al hacer clic */
    .select2-container--default .select2-selection--single:focus,
    .select2-premium-wrapper .select2-container--open .select2-selection--single {
        outline: none !important;
        box-shadow: none !important;
        border-color: #86b7fe !important;
    }
</style>