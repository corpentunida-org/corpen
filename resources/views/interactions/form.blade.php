@php
    // Detectar si estamos en modo edición
    $modoEdicion = isset($interaction) && $interaction->id;
@endphp
<<<<<<< HEAD

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        /* ===== COLORES PASTELES CORPORATIVOS MINIMALISTAS ===== */
        :root {
            --primary-color: #6B9BD1;
            --primary-light: #E6F3FF;
            --secondary-color: #F5F0FF;
            --accent-color: #E8F5E8;
            --text-primary: #374151;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            --background-color: #FAFBFC;
            --card-background: #FFFFFF;
            --input-background: #F8F9FA;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --border-radius: 8px;
            --transition: all 0.2s ease;
        }

        /* ===== ESTILOS GENERALES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /*  body {
            margin: 0;
            padding: 20px;
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;} 
        */

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        /* ===== TARJETA PRINCIPAL ===== */
        .card {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            overflow: hidden;
            background-color: var(--card-background);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: var(--primary-light);
            color: var(--text-primary);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-body {
            padding: 0;
        }

        /* ===== BARRA DE PROGRESO ===== */
        .progress-section {
            background-color: var(--input-background);
            padding: 20px 32px;
            border-bottom: 1px solid var(--border-color);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .progress-title {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .progress-percentage {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .progress-bar-container {
            height: 6px;
            background: rgba(107, 155, 209, 0.2);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary-color);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .progress-message {
            font-size: 12px;
            color: var(--text-secondary);
            text-align: center;
        }

        /* ===== NAVEGACIÓN POR PESTAÑAS ===== */
        .tab-navigation {
            display: flex;
            background-color: var(--input-background);
            border-bottom: 1px solid var(--border-color);
        }

        .tab-button {
            flex: 1;
            padding: 18px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .tab-button i { 
            font-size: 18px; 
        }
        
        .tab-button span { 
            font-size: 12px; 
            font-weight: 500; 
        }

        .tab-button.active {
            color: var(--primary-color);
            background: var(--card-background);
            border-bottom-color: var(--primary-color);
        }

        .tab-button:hover:not(.active) {
            color: var(--text-primary);
            background-color: rgba(107, 155, 209, 0.05);
        }

        /* ===== CONTENIDO DEL FORMULARIO ===== */
        .form-content { 
            padding: 1.5rem; 
        }
        
        .tab-panel { 
            display: none; 
            animation: fadeIn 0.3s ease; 
        }
        
        .tab-panel.active { 
            display: block; 
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ===== CATEGORÍAS ===== */
        .category-container {
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            background: var(--card-background);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .category-container:hover {
            box-shadow: var(--shadow-md);
        }

        .category-header {
            background-color: var(--input-background);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .category-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .category-icon {
            width: 36px;
            height: 36px;
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }

        .category-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin: 4px 0 0 48px;
        }

        .category-content { 
            padding: 1.5rem; 
        }

        /* ===== SECCIONES INTERNAS ===== */
        .section-divider {
            position: relative;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .section-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        /* ===== FORMULARIOS ===== */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem;
            transition: var(--transition);
            font-size: 0.95rem;
            width: 100%;
            background-color: var(--card-background);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.1);
            outline: none;
        }

        /* ===== BOTONES ===== */
        .btn-primary {
            background-color: var(--primary-color);
            border: none; 
            color: white;
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background-color: #5A8AC1;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn-light {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-light:hover {
            background-color: var(--input-background);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            background: transparent;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            background: transparent;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--input-background);
            transform: translateY(-1px);
        }

        /* ===== TARJETAS ===== */
        .card.border-0.bg-light {
            background-color: var(--input-background) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius) !important;
            margin-bottom: 1rem !important;
            transition: var(--transition);
        }

        .card.border-0.bg-light:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* ===== AVATAR ===== */
        .client-avatar {
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: 50%;
            transition: var(--transition);
        }

        .client-avatar:hover {
            transform: scale(1.05);
        }

        /* ===== SELECT2 ===== */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
        }
        
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.1);
        }

        /* ===== VALIDACIÓN ===== */
        .is-invalid { 
            border-color: #EF4444; 
        }
        
        .is-valid { 
            border-color: #10B981; 
        }
        
        .invalid-feedback { 
            color: #EF4444; 
            font-size: 0.875rem; 
            margin-top: 0.25rem; 
        }

        /* ===== CAMPO SOLO LECTURA ===== */
        .form-control[readonly] {
            background-color: var(--input-background);
            opacity: 1;
        }

        /* ===== ACCIONES DEL FORMULARIO ===== */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* ===== ESTILOS PARA EL HISTORIAL ===== */
        .history-item {
            transition: var(--transition);
        }
        
        .history-item:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        
        .history-list::-webkit-scrollbar { 
            width: 6px; 
        }
        
        .history-list::-webkit-scrollbar-track { 
            background: var(--input-background); 
            border-radius: 3px; 
        }
        
        .history-list::-webkit-scrollbar-thumb { 
            background: var(--border-color); 
            border-radius: 3px; 
        }
        
        .history-list::-webkit-scrollbar-thumb:hover { 
            background: var(--text-secondary); 
        }

        /* ===== IMÁGENES ===== */
        .img-thumbnail {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            padding: 0.25rem;
            background-color: var(--card-background);
            transition: var(--transition);
        }

        .img-thumbnail:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-sm);
        }

        .file-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            object-fit: contain;
        }

        /* ===== UTILIDADES BOOTSTRAP REEMPLAZADAS ===== */
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .align-items-start {
            align-items: flex-start;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-muted {
            color: var(--text-secondary);
        }
        
        .text-primary {
            color: var(--primary-color);
        }
        
        .text-danger {
            color: #EF4444;
        }
        
        .text-white {
            color: white;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        .mb-1 {
            margin-bottom: 0.25rem;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .mt-1 {
            margin-top: 0.25rem;
        }
        
        .mt-2 {
            margin-top: 0.5rem;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .me-1 {
            margin-right: 0.25rem;
        }
        
        .me-2 {
            margin-right: 0.5rem;
        }
        
        .me-3 {
            margin-right: 1rem;
        }
        
        .me-auto {
            margin-right: auto;
        }
        
        .ms-1 {
            margin-left: 0.25rem;
        }
        
        .ms-auto {
            margin-left: auto;
        }
        
        .py-3 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        
        .py-5 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        
        .p-2 {
            padding: 0.5rem;
        }
        
        .p-3 {
            padding: 1rem;
        }
        
        .p-4 {
            padding: 1.5rem;
        }
        
        .px-4 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .gap-1 {
            gap: 0.25rem;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 1rem;
        }
        
        .g-3 > * {
            margin-bottom: 1rem;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-md-12 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        @media (min-width: 768px) {
            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
            
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .col-md-8 {
                flex: 0 0 66.666667%;
                max-width: 66.666667%;
            }
            
            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        .flex-grow-1 {
            flex-grow: 1;
        }
        
        .flex-shrink-0 {
            flex-shrink: 0;
        }
        
        .small {
            font-size: 0.875em;
        }
        
        .fw-bold {
            font-weight: 700;
        }
        
        .fw-semibold {
            font-weight: 600;
        }
        
        .rounded-3 {
            border-radius: var(--border-radius);
        }
        
        .border {
            border: 1px solid var(--border-color);
        }
        
        .border-0 {
            border: 0;
        }
        
        .rounded {
            border-radius: var(--border-radius);
        }
        
        .bg-light {
            background-color: var(--input-background);
        }
        
        .bg-gradient-primary {
            background: var(--primary-light);
        }
        
        .opacity-75 {
            opacity: 0.75;
        }
        
        .overflow-hidden {
            overflow: hidden;
        }
        
        .shadow-lg {
            box-shadow: var(--shadow-md);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: calc(var(--border-radius) - 2px);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: calc(var(--border-radius) - 2px);
        }
        
        .bg-primary {
            background-color: var(--primary-color);
        }
        
        .bg-success {
            background-color: #10B981;
        }
        
        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: -0.125em;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.125em;
        }
        
        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }
        
        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: var(--text-secondary);
        }
        
        .position-fixed {
            position: fixed;
        }
        
        .z-index-9999 {
            z-index: 9999;
        }
        
        .top-0 {
            top: 0;
        }
        
        .start-0 {
            left: 0;
        }
        
        .end-0 {
            right: 0;
        }
        
        .bottom-0 {
            bottom: 0;
        }
        
        .display-1 {
            font-size: 6rem;
            font-weight: 300;
            line-height: 1.2;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body { 
                padding: 10px; 
            }
            
            .form-content { 
                padding: 1rem; 
            }
            
            .category-content { 
                padding: 1rem; 
            }
            
            .tab-button span { 
                display: none; 
            }
            
            .tab-button { 
                padding: 16px; 
            }
            
            .d-flex.justify-content-between.mt-4 { 
                flex-direction: column; 
                gap: 1rem; 
            }
            
            .d-flex.justify-content-between.mt-4>div { 
                width: 100%; 
            }
            
            .d-flex.justify-content-between.mt-4>div>button { 
                width: 100%; 
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* ===== MEJORAS VISUALES ===== */
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .spinner-border {
            width: 1rem;
            height: 1rem;
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--input-background);
        }

        /* ===== ESTADOS ESPECIALES ===== */
        .highlight {
            background-color: var(--primary-light);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .highlight:hover {
            background-color: #D1E9FF;
        }

        .required-field::after {
            content: " *";
            color: #EF4444;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            <!-- Header del formulario -->
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-white bg-opacity-20 rounded-3 p-2 me-3">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold">{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</h4>
                        <p class="mb-0 opacity-75 small">
                            {{ $modoEdicion ? 'Modifica la información de la interacción existente' : 'Completa el formulario para registrar una nueva interacción' }}
                        </p>
                    </div>
                    @if ($modoEdicion && $interaction->id)
                        <div class="flex-shrink-0">
                            <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-eye me-1"></i> Ver Detalles
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="progress-section">
                <div class="progress-header">
                    <span class="progress-title">Progreso del formulario</span>
                    <span class="progress-percentage" id="progress-percentage">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress-message" id="progress-message">Comienza seleccionando un cliente</div>
            </div>

            <!-- Navegación por pestañas -->
            <div class="tab-navigation">
                <button type="button" class="tab-button active" data-tab="principal">
                    <i class="bi bi-info-circle"></i>
                    <span>Información Principal</span>
                </button>
                <button type="button" class="tab-button" data-tab="adicional">
                    <i class="bi bi-briefcase"></i>
                    <span>Información Adicional</span>
                </button>
                <button type="button" class="tab-button" data-tab="resultado">
                    <i class="bi bi-check-circle"></i>
                    <span>Resultado y Planificación</span>
                </button>
                <button type="button" class="tab-button" data-tab="adjuntos">
                    <i class="bi bi-paperclip"></i>
                    <span>Adjuntos y Referencias</span>
                </button>
                <button type="button" class="tab-button" data-tab="historial">
                    <i class="bi bi-clock-history"></i>
                    <span>Historial</span>
                </button>
            </div>

            <div class="form-content">
                <form id="interaction-form"
                    action="{{ $modoEdicion ? route('interactions.update', $interaction->id) : route('interactions.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($modoEdicion)
                        @method('PUT')
                    @endif

                    <!-- PESTAÑA 1: INFORMACIÓN PRINCIPAL -->
                    <div class="tab-panel active" id="principal-tab">
                        <div class="category-container">
                            <!-- ================================================================= -->
                            <!-- INICIO: BLOQUE DE "QUIEN LLAMA" (SOLUCIÓN DEFINITIVA) -->
                            <!-- ================================================================= -->
                            <div class="category-container mb-4">
                                <div class="category-header">
                                    <h5 class="category-title">
                                        <div class="category-icon">
                                            <i class="bi bi-person-check"></i>
                                        </div>
                                        Información de Quien Llama
                                    </h5>
                                    <p class="category-description">Especifica si la llamada es realizada por el asociado o un tercero.</p>
                                </div>
                                <div class="category-content">
                                    <div class="form-group">
                                        <label class="form-label required-field">¿Quién realiza la llamada?</label>
                                        <style>
                                            /* ESTOS ESTILOS EVITAN QUE EL BOTÓN SE PIERDA CON EL FONDO AL PASAR EL MOUSE */
                                            .btn-soft-choice:hover {
                                                /* Fuerza a que el botón mantenga su color de fondo por defecto al pasar el mouse */
                                                background-color: #f8f9fa !important;
                                                border-color: #dee2e6 !important;
                                                color: #212529 !important;
                                            }
                                        </style>

                                        <div class="d-flex gap-2 mb-3">
                                            <input type="radio" class="btn-check" name="caller_type" id="caller_is_client" value="client" autocomplete="off" checked>
                                            <label class="btn btn-soft-choice flex-grow-1" for="caller_is_client">
                                                <i class="bi bi-person me-2"></i>Es el cliente
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="caller_type" id="caller_is_third_party" value="third_party" autocomplete="off">
                                            <label class="btn btn-soft-choice flex-grow-1" for="caller_is_third_party">
                                                <i class="bi bi-people me-2"></i>Es un tercero
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Campos para cuando es un tercero quien llama -->
                                    <div id="third-party-fields" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="nombre_quien_llama" class="form-label">Nombre del Tercero</label>
                                                    <input type="text" class="form-control" id="nombre_quien_llama" name="nombre_quien_llama" 
                                                        placeholder="Nombre completo de quien llama">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cedula_quien_llama" class="form-label">Identificación</label>
                                                    <input type="text" class="form-control" id="cedula_quien_llama" name="cedula_quien_llama" 
                                                        placeholder="Cédula o documento de identidad">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="celular_quien_llama" class="form-label">Teléfono</label>
                                                    <input type="text" class="form-control" id="celular_quien_llama" name="celular_quien_llama" 
                                                        placeholder="Número de contacto">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="parentezco_quien_llama" class="form-label">Relación con el Cliente</label>
                                                    <select class="form-select" id="parentezco_quien_llama" name="parentezco_quien_llama">
                                                        <option value="">Selecciona una opción</option>
                                                        <option value="familiar">Familiar</option>
                                                        <option value="amigo">Amigo</option>
                                                        <option value="representante">Representante legal</option>
                                                        <option value="conocido">Conocido</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SCRIPT PARA CONTROLAR LA VISIBILIDAD Y AUTOCOMPLETADO -->
                            <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const clientSelect = document.getElementById('client_id');
                                const radioButtons = document.querySelectorAll('input[name="caller_type"]');
                                const thirdPartyFields = document.getElementById('third-party-fields');

                                // Variable para guardar los datos del cliente seleccionado
                                let currentClientData = null;

                                // Función para obtener los detalles del cliente desde el servidor
                                async function fetchClientDetails(clientId) {
                                    if (!clientId) {
                                        currentClientData = null;
                                        return;
                                    }
                                    try {
                                        const response = await fetch(`/clientes/${clientId}/detalles`);
                                        if (!response.ok) {
                                            throw new Error('Network response was not ok');
                                        }
                                        currentClientData = await response.json();
                                    } catch (error) {
                                        console.error('Error fetching client details:', error);
                                        currentClientData = null;
                                    }
                                }

                                // Función principal que maneja los cambios en quién llama
                                function handleCallerChange() {
                                    const selectedButton = document.querySelector('input[name="caller_type"]:checked');
                                    
                                    if (selectedButton.value === 'third_party') {
                                        thirdPartyFields.style.display = 'block';
                                        // Limpiar campos para que el usuario los llene
                                        document.getElementById('nombre_quien_llama').value = '';
                                        document.getElementById('cedula_quien_llama').value = '';
                                        document.getElementById('celular_quien_llama').value = '';
                                        document.getElementById('parentezco_quien_llama').value = '';
                                    } else { // 'client'
                                        thirdPartyFields.style.display = 'none';
                                        // Autocompletar campos si hay datos de cliente disponibles
                                        if (currentClientData) {
                                            document.getElementById('nombre_quien_llama').value = currentClientData.nom_ter;
                                            document.getElementById('cedula_quien_llama').value = clientSelect.value; // Usa el ID del cliente
                                            document.getElementById('celular_quien_llama').value = currentClientData.tel;
                                            document.getElementById('parentezco_quien_llama').value = 'representante'; // Valor fijo
                                        } else {
                                            // Limpiar si no hay cliente seleccionado
                                            document.getElementById('nombre_quien_llama').value = '';
                                            document.getElementById('cedula_quien_llama').value = '';
                                            document.getElementById('celular_quien_llama').value = '';
                                            document.getElementById('parentezco_quien_llama').value = '';
                                        }
                                    }
                                }

                                // Event Listeners
                                radioButtons.forEach(radioButton => {
                                    radioButton.addEventListener('change', handleCallerChange);
                                });

                                clientSelect.addEventListener('change', function() {
                                    const selectedClientId = this.value;
                                    fetchClientDetails(selectedClientId).then(() => {
                                        // Después de obtener los datos, actualiza el formulario
                                        handleCallerChange();
                                    });
                                });

                                // Establecer el estado inicial al cargar la página
                                // Si hay un cliente preseleccionado (en modo edición), obtén sus datos
                                if (clientSelect.value) {
                                    fetchClientDetails(clientSelect.value).then(() => {
                                        handleCallerChange();
                                    });
                                } else {
                                    handleCallerChange();
                                }
                            });
                            </script>
                            <!-- ================================================================= -->
                            <!-- FIN: BLOQUE DE "QUIEN LLAMA" -->
                            <!-- ================================================================= -->
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    Información Principal
                                </h5>
                                <p class="category-description">Datos esenciales de la interacción y el cliente</p>
                            </div>
                            <div class="category-content">
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>Registro del Asociado
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="client_id" class="form-label required-field">Cliente</label>
                                            <select class="form-select select2 @error('client_id') is-invalid @enderror"
                                                id="client_id" name="client_id" required>
                                                <option value="">Selecciona un cliente</option>
                                                @if ($modoEdicion && $interaction->client_id)
                                                    <option value="{{ $interaction->client_id }}" selected>
                                                        {{ $interaction->client->nom_ter }} ({{ $interaction->client_id }})
                                                    </option>
                                                @endif
                                            </select>
                                            @error('client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="agent" class="form-label">Agente</label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ auth()->user()->name }}" readonly>
                                            <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                                            <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
                                            <div class="form-text mt-1">
                                                <i class="bi bi-calendar me-1"></i> {{ now()->format('d/m/Y h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="client-info-card" class="card border-0 bg-light mb-4" style="display:none;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            <h6 class="mb-0 fw-semibold">Información del Cliente</h6>
                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    id="toggle-client-info">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="client-info-content">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="client-avatar me-2" id="info-avatar"
                                                            style="width:40px;height:40px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                                        </div>
                                                        <div>
                                                            <div id="info-nombre" class="fw-semibold">—</div>
                                                            <div id="info-id" class="text-muted small">ID: —</div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-geo-alt text-muted me-2"></i>
                                                        <span id="info-distrito">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-tag text-muted me-2"></i>
                                                        <span id="info-categoria">Cargando...</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-envelope text-muted me-2"></i>
                                                        <span id="info-email">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-telephone text-muted me-2"></i>
                                                        <span id="info-telefono">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-geo text-muted me-2"></i>
                                                        <span id="info-direccion">Cargando...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <a id="btn-editar-cliente"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">                             
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Actualizar datos</span>
                                                </a>
                                                <a id="btn-ver-cliente"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">                             
                                                    <i class="bi bi-pencil-square"></i>
                                                    <span>Ver ficha completa</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-chat-dots me-2 text-primary"></i>Detalles de la Interacción
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="interaction_channel" class="form-label required-field">Canal</label>
                                            <select
                                                class="form-select select2 @error('interaction_channel') is-invalid @enderror"
                                                id="interaction_channel" name="interaction_channel" required>
                                                <option value="">Selecciona un canal</option>
                                                @foreach ($channels as $channel)
                                                    <option value="{{ $channel->id }}"
                                                        {{ old('interaction_channel', $interaction->interaction_channel ?? '') == $channel->id ? 'selected' : '' }}>
                                                        {{ $channel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('interaction_channel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="interaction_type" class="form-label required-field">Tipo</label>
                                            <select class="form-select select2 @error('interaction_type') is-invalid @enderror"
                                                id="interaction_type" name="interaction_type" required>
                                                <option value="">Selecciona un tipo</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ old('interaction_type', $interaction->interaction_type ?? '') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('interaction_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="duration-display" class="form-label">Duración</label>
                                            <input type="text" class="form-control bg-light" id="duration-display"
                                                readonly
                                                value="{{ $modoEdicion && $interaction->duration ? $interaction->duration . ' segundos' : '0 segundos' }}">
                                            <input type="hidden" id="start_time" name="start_time"
                                                value="{{ old('start_time', $interaction->start_time ?? '') }}">
                                            <input type="hidden" id="duration" name="duration"
                                                value="{{ old('duration', $interaction->duration ?? '') }}">
                                            <div class="form-text mt-1">Se calcula automáticamente al seleccionar un cliente.</div>
                                            @error('duration')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes" class="form-label required-field">Notas</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                                placeholder="Describe los detalles de la interacción..." required>{{ old('notes', $interaction->notes ?? '') }}</textarea>
                                            <div class="d-flex justify-content-between">
                                                <div class="form-text">Añade aquí todos los detalles relevantes de la interacción.</div>
                                                <div class="form-text text-end" id="notes-counter">0/500 caracteres</div>
                                            </div>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div></div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('adicional')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 2: INFORMACIÓN ADICIONAL -->
                    <div class="tab-panel" id="adicional-tab" 
                        data-agent-area-id="{{ $idAreaAgente ?? '' }}" 
                        data-agent-cargo-id="{{ $idCargoAgente ?? '' }}">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="feather-briefcase"></i>
                                    </div>
                                    Información Adicional
                                </h5>
                                <p class="category-description">Datos complementarios del cliente y asignación</p>
                            </div>
                            <div class="category-content">
                                <!-- Fila 1: Pregunta de Asignación -->
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-muted small">
                                            <i class="feather-help-circle me-1"></i>¿Quién gestionará esta interacción?
                                        </label>
                                        <div class="btn-group w-100" role="group" aria-label="Gestión de la interacción">
                                            <input type="radio" class="btn-check" name="handled_by_agent" id="handled_by_me" value="yes" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="handled_by_me">
                                                <i class="feather-user me-1"></i>Yo la gestionaré
                                            </label>

                                            <input type="radio" class="btn-check" name="handled_by_agent" id="handled_by_other" value="no" autocomplete="off">
                                            <label class="btn btn-outline-secondary" for="handled_by_other">
                                                <i class="feather-users me-1"></i>Otro cargo/área la gestionará
                                            </label>
                                        </div>
                                        <div class="form-text mt-1">Selecciona si tú serás el responsable o si la interacción será asignada a otra área o cargo.</div>
                                    </div>
                                </div>

                                <!-- Fila 2: Datos del Agente (Solo Lectura) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="area-agente-display" class="form-label fw-semibold text-muted small">
                                            <i class="feather-layers me-1"></i>Área del Agente
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-user"></i></span>
                                            <input type="text" class="form-control bg-light" id="area-agente-display" readonly 
                                                value="{{ $idAreaAgente ? $areas[$idAreaAgente] : 'No asignada' }}">
                                            <!-- CORRECCIÓN: ID del campo oculto cambiado para evitar duplicados -->
                                            <input type="hidden" id="id_area" name="id_area" value="{{ old('id_area', $interaction->id_area ?? $idAreaAgente ?? '') }}">
                                        </div>
                                        <div class="form-text mt-1">Tu área actual, asignada automáticamente.</div>
                                        @error('id_area')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cargo-agente-display" class="form-label fw-semibold text-muted small">
                                            <i class="feather-briefcase me-1"></i>Cargo del Agente
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-user"></i></span>
                                            <input type="text" class="form-control bg-light" id="cargo-agente-display" readonly 
                                                value="{{ $idCargoAgente ? $cargos[$idCargoAgente] : 'No asignado' }}">
                                            <!-- CORRECCIÓN: ID del campo oculto cambiado para evitar duplicados -->
                                            <input type="hidden" id="id_cargo_agente" name="id_cargo_agente" value="{{ old('id_cargo_agente', $idCargoAgente ?? '') }}">
                                        </div>
                                        <div class="form-text mt-1">Tu cargo actual, asignado automáticamente.</div>
                                        @error('id_cargo_agente')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fila 3: Datos de Asignación (Seleccionables) -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label for="id_area_de_asignacion" class="form-label fw-semibold text-muted small">
                                            <i class="feather-target me-1"></i>Área de Asignación
                                        </label>
                                        <select class="form-select select2 @error('id_area_de_asignacion') is-invalid @enderror"
                                                id="id_area_de_asignacion" name="id_area_de_asignacion">
                                            <option value="">Selecciona un área</option>
                                            @if (isset($areas))
                                                @foreach ($areas as $id => $nombre)
                                                    <option value="{{ $id }}" {{ old('id_area_de_asignacion', $interaction->id_area_de_asignacion ?? '') == $id ? 'selected' : '' }}>
                                                        {{ $nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text mt-1">Área responsable de gestionar esta interacción.</div>
                                        @error('id_area_de_asignacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="id_cargo_asignacion" class="form-label fw-semibold text-muted small">
                                            <i class="feather-user-check me-1"></i>Cargo de Asignación
                                        </label>
                                        <!-- CORRECCIÓN: ID del select cambiado para evitar duplicados -->
                                        <select class="form-select select2 @error('id_cargo_asignacion') is-invalid @enderror"
                                                id="id_cargo_asignacion" name="id_cargo_asignacion">
                                            <option value="">Selecciona un cargo</option>
                                            @if (isset($cargos))
                                                @foreach ($cargos as $id => $nombre)
                                                    <option value="{{ $id }}" {{ old('id_cargo_asignacion', $interaction->id_cargo ?? '') == $id ? 'selected' : '' }}>
                                                        {{ $nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text mt-1">Cargo específico que atenderá o desarrollará la interacción.</div>
                                        @error('id_cargo_asignacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fila 4: Detalles Adicionales -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="id_linea_de_obligacion" class="form-label fw-semibold text-muted small">
                                            <i class="feather-credit-card me-1"></i>Línea de Obligación
                                        </label>
                                        <select class="form-select select2 @error('id_linea_de_obligacion') is-invalid @enderror"
                                                id="id_linea_de_obligacion" name="id_linea_de_obligacion">
                                            <option value="">Selecciona una línea</option>
                                            @if (isset($lineasCredito))
                                                @foreach ($lineasCredito as $id => $nombre)
                                                    <option value="{{ $id }}" {{ old('id_linea_de_obligacion', $interaction->id_linea_de_obligacion ?? '') == $id ? 'selected' : '' }}>
                                                        {{ $nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="form-text mt-1">Relaciona la interacción con una línea de crédito o producto específico.</div>
                                        @error('id_linea_de_obligacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
<div class="col-md-6">
    <label for="id_distrito_interaccion" class="form-label fw-semibold text-muted small">
        <i class="feather-map-pin me-1"></i>Distrito de la Interacción
    </label>
    <div class="input-group">
        <select class="form-select select2 @error('id_distrito_interaccion') is-invalid @enderror"
                id="id_distrito_interaccion" name="id_distrito_interaccion">
            <option value="">Selecciona un distrito</option>
            @if (isset($distrito))
                @foreach ($distrito as $id => $nombre)
                    <option value="{{ $id }}" {{ old('id_distrito_interaccion', $interaction->id_distrito_interaccion ?? '') == $id ? 'selected' : '' }}>
                        {{ $nombre }}
                    </option>
                @endforeach
            @endif
        </select>
        <button class="btn btn-outline-secondary" type="button" id="sync-from-client-btn" 
                title="Sincroniza el campo de distrito con la información del cliente seleccionado.">
            <i class="feather-download"></i>
        </button>
        <button class="btn btn-outline-primary" type="button" id="sync-to-client-btn" 
                title="Actualiza el distrito del cliente con el valor seleccionado en este formulario.">
            <i class="feather-upload"></i>
        </button>
    </div>
    <div class="form-text mt-1">
        Selecciona el distrito, usa el botón de descarga para sincronizar desde el cliente 
        o el botón de subida para actualizar el cliente.
    </div>
    @error('id_distrito_interaccion')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('principal')">
                                    <i class="feather-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('resultado')">
                                    Continuar <i class="feather-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Script Unificado para Autocompletado y Sincronización -->
                        <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            // =================================================================
                            // ELEMENTOS DEL DOM PARA LA LÓGICA DE AUTOCOMPLETADO
                            // =================================================================
                            const infoTab = document.getElementById('adicional-tab');
                            const handledByMeRadio = document.getElementById('handled_by_me');
                            const handledByOtherRadio = document.getElementById('handled_by_other');
                            const areaAsignacionSelect = document.getElementById('id_area_de_asignacion');
                            const cargoAsignacionSelect = document.getElementById('id_cargo_asignacion');

                            // =================================================================
                            // ELEMENTOS DEL DOM PARA LA LÓGICA DE SINCRONIZACIÓN DE DISTRITO
                            // =================================================================
                            const clientSelect = document.getElementById('client_id');
                            const districtSelect = document.getElementById('id_distrito_interaccion');
                            const syncFromClientBtn = document.getElementById('sync-from-client-btn'); // Botón para sincronizar desde cliente
                            const syncToClientBtn = document.getElementById('sync-to-client-btn'); // Botón para actualizar cliente

                            // =================================================================
                            // LÓGICA DE AUTOCOMPLETADO DE ASIGNACIÓN
                            // =================================================================
                            function handleAssignmentChange() {
                                if (!areaAsignacionSelect || !cargoAsignacionSelect || !infoTab) return;

                                const agentAreaId = infoTab.dataset.agentAreaId;
                                const agentCargoId = infoTab.dataset.agentCargoId;

                                if (handledByMeRadio.checked) {
                                    areaAsignacionSelect.value = agentAreaId;
                                    cargoAsignacionSelect.value = agentCargoId;
                                } else {
                                    areaAsignacionSelect.value = '';
                                    cargoAsignacionSelect.value = '';
                                }

                                // Notificar a Select2 del cambio para que actualice la visualización
                                if (typeof jQuery !== 'undefined' && jQuery().select2) {
                                    jQuery(areaAsignacionSelect).trigger('change');
                                    jQuery(cargoAsignacionSelect).trigger('change');
                                }
                            }

                            // Asignar event listeners a los botones de radio
                            if (handledByMeRadio) handledByMeRadio.addEventListener('change', handleAssignmentChange);
                            if (handledByOtherRadio) handledByOtherRadio.addEventListener('change', handleAssignmentChange);

                            // Ejecutar la función una vez al cargar la página para establecer el estado inicial
                            handleAssignmentChange();

                            // =================================================================
                            // LÓGICA DE SINCRONIZACIÓN DE DISTRITO DESDE CLIENTE
                            // =================================================================
                            async function handleSyncFromClientClick() {
                                if (!clientSelect || !districtSelect || !syncFromClientBtn) return;
                                
                                const selectedClientId = clientSelect.value;
                                if (!selectedClientId) {
                                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor, selecciona un cliente primero.' });
                                    return;
                                }

                                syncFromClientBtn.disabled = true;
                                syncFromClientBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                                try {
                                    console.log(`Intentando obtener distrito para cliente ID: ${selectedClientId}`);
                                    
                                    // ✅ MEJOR PRÁCTICA: Usar el helper de ruta de Laravel
                                    const url = "{{ route('interactions.clientes.distrito', ':id') }}".replace(':id', selectedClientId);
                                    const response = await fetch(url);
                                    
                                    console.log('Respuesta del servidor:', response.status, response.statusText);
                                    
                                    if (!response.ok) {
                                        let errorMessage = `Error ${response.status}: ${response.statusText}`;
                                        try {
                                            const errorData = await response.json();
                                            if (errorData.error) {
                                                errorMessage = errorData.error;
                                            }
                                        } catch (e) {
                                            console.log('No se pudo parsear el error como JSON');
                                        }
                                        throw new Error(errorMessage);
                                    }

                                    const data = await response.json();
                                    console.log('Datos recibidos:', data);
                                    
                                    const newDistrictId = data.district_id;

                                    if (newDistrictId) {
                                        districtSelect.value = newDistrictId;
                                        if (typeof jQuery !== 'undefined' && jQuery().select2) {
                                            jQuery(districtSelect).trigger('change');
                                        }
                                        Swal.fire({ 
                                            icon: 'success', 
                                            title: 'Actualizado', 
                                            text: 'El distrito ha sido sincronizado desde el cliente.', 
                                            timer: 2000, 
                                            showConfirmButton: false 
                                        });
                                    } else {
                                        Swal.fire({ 
                                            icon: 'info', 
                                            title: 'Sin Datos', 
                                            text: 'El cliente seleccionado no tiene un distrito asignado.' 
                                        });
                                    }
                                } catch (error) {
                                    console.error('Error al actualizar distrito:', error);
                                    Swal.fire({ 
                                        icon: 'error', 
                                        title: 'Error', 
                                        text: `No se pudo sincronizar el distrito: ${error.message}` 
                                    });
                                } finally {
                                    syncFromClientBtn.disabled = false;
                                    syncFromClientBtn.innerHTML = '<i class="feather-download"></i>';
                                }
                            }

                            // =================================================================
                            // LÓGICA DE ACTUALIZACIÓN DE DISTRITO EN CLIENTE
                            // =================================================================
                            async function handleSyncToClientClick() {
                                if (!clientSelect || !districtSelect || !syncToClientBtn) return;
                                
                                const selectedClientId = clientSelect.value;
                                const selectedDistrictId = districtSelect.value;
                                
                                if (!selectedClientId) {
                                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor, selecciona un cliente primero.' });
                                    return;
                                }
                                
                                if (!selectedDistrictId) {
                                    Swal.fire({ icon: 'warning', title: 'Atención', text: 'Por favor, selecciona un distrito primero.' });
                                    return;
                                }

                                // Confirmar antes de actualizar
                                const result = await Swal.fire({
                                    title: '¿Estás seguro?',
                                    text: `¿Quieres actualizar el distrito del cliente con el valor seleccionado?`,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Sí, actualizar',
                                    cancelButtonText: 'Cancelar'
                                });

                                if (!result.isConfirmed) {
                                    return;
                                }

                                syncToClientBtn.disabled = true;
                                syncToClientBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                                try {
                                    console.log(`Intentando actualizar distrito para cliente ID: ${selectedClientId} con distrito: ${selectedDistrictId}`);
                                    
                                    // ✅ MEJOR PRÁCTICA: Usar el helper de ruta de Laravel
                                    const url = "{{ route('interactions.clientes.actualizar-distrito', ':id') }}".replace(':id', selectedClientId);
                                    const response = await fetch(url, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            district_id: selectedDistrictId
                                        })
                                    });
                                    
                                    console.log('Respuesta del servidor:', response.status, response.statusText);
                                    
                                    if (!response.ok) {
                                        let errorMessage = `Error ${response.status}: ${response.statusText}`;
                                        try {
                                            const errorData = await response.json();
                                            if (errorData.error) {
                                                errorMessage = errorData.error;
                                            }
                                        } catch (e) {
                                            console.log('No se pudo parsear el error como JSON');
                                        }
                                        throw new Error(errorMessage);
                                    }

                                    const data = await response.json();
                                    console.log('Datos recibidos:', data);
                                    
                                    Swal.fire({ 
                                        icon: 'success', 
                                        title: 'Actualizado', 
                                        text: 'El distrito del cliente ha sido actualizado correctamente.', 
                                        timer: 2000, 
                                        showConfirmButton: false 
                                    });
                                } catch (error) {
                                    console.error('Error al actualizar distrito del cliente:', error);
                                    Swal.fire({ 
                                        icon: 'error', 
                                        title: 'Error', 
                                        text: `No se pudo actualizar el distrito del cliente: ${error.message}` 
                                    });
                                } finally {
                                    syncToClientBtn.disabled = false;
                                    syncToClientBtn.innerHTML = '<i class="feather-upload"></i>';
                                }
                            }

                            // =================================================================
                            // ASIGNACIÓN DE EVENTOS A LOS BOTONES
                            // =================================================================
                            // Asignar evento al botón de sincronización desde cliente
                            if (syncFromClientBtn) {
                                syncFromClientBtn.addEventListener('click', handleSyncFromClientClick);
                            }
                            
                            // Asignar evento al botón de actualización de cliente
                            if (syncToClientBtn) {
                                syncToClientBtn.addEventListener('click', handleSyncToClientClick);
                            }
                        });
                        </script>
                    </div>

                    <!-- PESTAÑA 3: RESULTADO Y PLANIFICACIÓN -->
                    <div class="tab-panel" id="resultado-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    Resultado y Planificación
                                </h5>
                                <p class="category-description">Resultado de la interacción y próximas acciones</p>
                            </div>
                            <div class="category-content">
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-flag me-2 text-primary"></i>Resultado de la Interacción
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="outcome" class="form-label required-field">Resultado</label>
                                            <select class="form-select select2 @error('outcome') is-invalid @enderror"
                                                id="outcome" name="outcome" required>
                                                <option value="">Selecciona un resultado</option>
                                                @foreach ($outcomes as $outcome)
                                                    <option value="{{ $outcome->id }}"
                                                        {{ old('outcome', $interaction->outcome ?? '') == $outcome->id ? 'selected' : '' }}>
                                                        {{ $outcome->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('outcome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-calendar-check me-2 text-primary"></i>Planificación
                                    </h6>
                                </div>
                                <div class="card border-0 bg-light mb-4" id="planning-section" style="display:none;">
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="next_action_date" class="form-label">Próxima Acción</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('next_action_date') is-invalid @enderror"
                                                        id="next_action_date" name="next_action_date"
                                                        value="{{ old('next_action_date', $interaction->next_action_date ?? '') }}">
                                                    <div class="d-flex gap-1 flex-wrap mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="1">+1 día</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="3">+3 días</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="7">+1 sem</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="14">+2 sem</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="30">+1 mes</button>
                                                    </div>
                                                    @error('next_action_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="next_action_type" class="form-label">Tipo de Acción</label>
                                                    <select
                                                        class="form-select select2 @error('next_action_type') is-invalid @enderror"
                                                        id="next_action_type" name="next_action_type">
                                                        <option value="">Selecciona un tipo</option>
                                                        @foreach ($nextActions as $action)
                                                            <option value="{{ $action->id }}"
                                                                {{ old('next_action_type', $interaction->next_action_type ?? '') == $action->id ? 'selected' : '' }}>
                                                                {{ $action->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('next_action_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="next_action_notes" class="form-label">Notas sobre la Próxima Acción</label>
                                                    <textarea class="form-control @error('next_action_notes') is-invalid @enderror" id="next_action_notes"
                                                        name="next_action_notes" rows="3" placeholder="Detalles sobre la próxima acción programada...">{{ old('next_action_notes', $interaction->next_action_notes ?? '') }}</textarea>
                                                    @error('next_action_notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('adicional')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('adjuntos')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 4: ADJUNTOS Y REFERENCIAS -->
                    <div class="tab-panel" id="adjuntos-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-paperclip"></i>
                                    </div>
                                    Adjuntos y Referencias
                                </h5>
                                <p class="category-description">Archivos y enlaces relacionados con la interacción</p>
                            </div>
                            <div class="category-content">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="attachment" class="form-label">Archivo Adjunto</label>
                                            <input type="file"
                                                class="form-control @error('attachment') is-invalid @enderror"
                                                id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                            <div class="form-text">Puedes adjuntar un archivo (máx. 10MB)</div>
                                            
                                            @if ($modoEdicion && $interaction->attachment_urls)
                                                <div class="mt-3">
                                                    <div class="small text-muted fw-semibold">Archivo existente:</div>
                                                    <div class="d-flex align-items-center mt-2 p-2 border rounded bg-light">
                                                        <i class="bi bi-file-earmark me-2"></i>
                                                        <span class="me-auto">{{ basename($interaction->attachment_urls) }}</span>
                                                        <a href="{{ route('interactions.download', basename($interaction->attachment_urls)) }}" 
                                                           class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                            <i class="bi bi-download"></i> Descargar
                                                        </a>
                                                        <a href="{{ route('interactions.view', basename($interaction->attachment_urls)) }}" 
                                                           class="btn btn-sm btn-outline-secondary" target="_blank">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @error('attachment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="interaction_url" class="form-label">Enlace de Referencia</label>
                                            <input type="url"
                                                class="form-control @error('interaction_url') is-invalid @enderror"
                                                id="interaction_url" name="interaction_url" placeholder="https://ejemplo.com"
                                                value="{{ old('interaction_url', $interaction->interaction_url ?? '') }}">
                                            @error('interaction_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vista previa de imagen -->
                                <div id="image-preview-container" class="mt-3" style="display: none;">
                                    <div class="small text-muted fw-semibold mb-2">Vista previa:</div>
                                    <img id="image-preview" class="img-thumbnail file-preview" alt="Vista previa de la imagen">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('resultado')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('historial')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

<!-- PESTAÑA 5: HISTORIAL -->
<div class="tab-panel" id="historial-tab">
    <div class="category-container">
        <div class="category-header">
            <h5 class="category-title">
                <div class="category-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                Historial de Interacciones
            </h5>
            <p class="category-description">Interacciones previas con este cliente. Selecciona una para escalar.</p>
        </div>
        <div class="category-content">
            <div id="no-client-selected" class="text-center py-5">
                <i class="bi bi-person-x display-1 text-muted"></i>
                <p class="mt-3 text-muted">Selecciona un cliente para ver su historial de interacciones</p>
            </div>
            
            <!-- Campo oculto para almacenar el ID de la interacción padre -->
            <input type="hidden" id="parent_interaction_id" name="parent_interaction_id" value="">
            
            <div class="card border-0 bg-light mb-4" id="history-section" style="display:none;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        <h6 class="mb-0 fw-semibold">Historial Reciente</h6>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-history">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="toggle-history">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Indicador de interacción padre seleccionada -->
                    <div id="selected-parent-info" class="alert alert-info d-flex align-items-center" style="display:none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Interacción padre seleccionada:</strong> 
                            <span id="selected-parent-text">Ninguna</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-parent-selection">
                            <i class="bi bi-x-circle"></i> Limpiar
                        </button>
                    </div>
                    
                    <div id="history-content">
                        <div id="interaction-history-list" class="history-list" style="max-height:400px; overflow-y:auto;">
                            <!-- items cargados por JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <div class="action-buttons">
            <button type="button" class="btn btn-light" onclick="showTab('adjuntos')">
                <i class="bi bi-arrow-left"></i> Anterior
            </button>
        </div>
        <div class="action-buttons">
            <button id="clear-draft" type="button" class="btn btn-outline-secondary me-2">
                <i class="bi bi-trash me-1"></i> Borrar Borrador
            </button>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i>
                {{ $modoEdicion ? 'Actualizar Interacción' : 'Guardar Interacción' }}
            </button>
        </div>
    </div>
</div>
                </form>
            </div>
        </div>
    </div>

    <!-- AJAX Loader overlay -->
    <div id="ajax-loader" class="ajax-loader" aria-hidden="true"
        style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div class="card text-center p-4">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mb-0">Cargando información del cliente...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // =================================================================
        // INICIALIZACIÓN DE COMPONENTES
        // =================================================================
        
        // Inicialización de Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: function() {
                return $(this).attr('placeholder');
            },
            dropdownParent: $('body')
        });

        // Configuración especial para el campo de cliente con AJAX
        $('#client_id').select2({
            theme: 'bootstrap-5',
            placeholder: 'Buscar cliente...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: @json(route('interactions.search-clients')),
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(function(item) {
                            return {
                                id: item.cod_ter,
                                text: `${item.cod_ter} - ${item.apl1} ${item.apl2} ${item.nom1} ${item.nom2}`
                            };
                        }),
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });

        // =================================================================
        // VARIABLES GLOBALES
        // =================================================================
        
        const $ajaxLoader = $('#ajax-loader');
        const $form = $('#interaction-form');
        const storageKey = 'interaction_form_draft_v1';

        // Variables para el cronómetro
        let startTimeInterval = null;
        let startTime = null;

        // =================================================================
        // FUNCIONES DE UTILIDAD
        // =================================================================
        
        function showLoader() {
            $ajaxLoader.show();
        }

        function hideLoader() {
            $ajaxLoader.hide();
        }

        // Configuración de notificaciones
        toastr.options = {
            "positionClass": "toast-bottom-right",
            "timeOut": "2500",
            "progressBar": true,
        };

        // =================================================================
        // LÓGICA DE NAVEGACIÓN POR PESTAÑAS
        // =================================================================
        
        // Navegación por pestañas
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');
            showTab(tabId);
            saveDraft();
        });

        window.showTab = function(tabId) {
            $('.tab-button').removeClass('active');
            $(`.tab-button[data-tab="${tabId}"]`).addClass('active');

            $('.tab-panel').removeClass('active');
            $(`#${tabId}-tab`).addClass('active');

            if (tabId === 'historial') {
                updateHistoryTabVisibility();
            }

            updateProgress();
        }

        // =================================================================
        // LÓGICA DE PROGRESO DEL FORMULARIO
        // =================================================================
        
        // Actualizar progreso
        function updateProgress() {
            const requiredFields = ['client_id', 'interaction_channel', 'interaction_type', 'notes', 'outcome'];
            let completed = 0;

            requiredFields.forEach(field => {
                const value = $(`[name="${field}"]`).val();
                if (value && value.trim() !== '') {
                    completed++;
                }
            });

            const progress = Math.round((completed / requiredFields.length) * 100);

            $('#progress-bar').css('width', progress + '%');
            $('#progress-percentage').text(progress + '%');

            // Actualizar mensaje
            const messages = [
                'Comienza seleccionando un cliente',
                'Agrega los detalles de la interacción',
                'Completa la información adicional',
                'Define el resultado',
                'Adjunta archivos y revisa el historial',
                'Formulario completo'
            ];

            const messageIndex = Math.min(Math.floor(progress / 20), messages.length - 1);
            $('#progress-message').text(messages[messageIndex]);
        }

        // =================================================================
        // LÓGICA DE GUARDADO Y CARGA DE BORRADOR
        // =================================================================
        
        // Guardado automático en localStorage
        function saveDraft() {
            try {
                const data = {};
                $form.find('input,textarea,select').each(function() {
                    const name = this.name;
                    if (!name) return;
                    if (this.type === 'file') return;
                    if (this.type === 'checkbox' || this.type === 'radio') {
                        if (this.checked) data[name] = this.value;
                    } else {
                        if ($(this).is('select[multiple]')) {
                            data[name] = $(this).val() || [];
                        } else {
                            data[name] = $(this).val();
                        }
                    }
                });
                data.activeTab = $('.tab-button.active').data('tab');
                localStorage.setItem(storageKey, JSON.stringify(data));
                toastr.success('Borrador guardado automáticamente');
            } catch (e) {
                console.warn('No se pudo guardar el borrador', e);
            }
        }

        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const saveDraftDebounced = debounce(saveDraft, 700);

        // Cargar borrador guardado
        function loadDraft() {
            try {
                const raw = localStorage.getItem(storageKey);
                if (!raw) return;
                const data = JSON.parse(raw);
                Object.keys(data).forEach(name => {
                    if (name === 'activeTab') return;
                    const value = data[name];
                    const $el = $form.find(`[name="${name}"]`);
                    if (!$el.length) return;
                    if ($el.is('select[multiple]')) {
                        $el.val(value).trigger('change');
                    } else if ($el.is('select')) {
                        $el.val(value).trigger('change');
                    } else {
                        $el.val(value);
                    }
                });

                // Restaurar el estado del cronómetro
                if (data.start_time && data.client_id) {
                    $('#start_time').val(data.start_time);
                    if (data.duration) {
                        $('#duration-display').val(`${data.duration} segundos`);
                    }

                    startTime = new Date(data.start_time);

                    startTimeInterval = setInterval(() => {
                        const now = new Date();
                        const durationSeconds = Math.round((now - startTime) / 1000);
                        $('#duration-display').val(`${durationSeconds} segundos`);
                        $('#duration').val(durationSeconds);
                    }, 1000);
                }

                if (data.activeTab) {
                    showTab(data.activeTab);
                }

                toastr.info('Borrador restaurado automáticamente');
            } catch (e) {
                console.warn('No se pudo cargar el borrador', e);
            }
        }

        // Eventos para guardar borrador
        $form.on('input change', 'input, textarea, select', function() {
            updateProgress();
            saveDraftDebounced();
        });

        // Cargar borrador al iniciar
        loadDraft();
        updateProgress();

        // Botón para limpiar borrador
        $('#clear-draft').on('click', function() {
            Swal.fire({
                title: '¿Borrar el borrador guardado?',
                text: "Esto eliminará el borrador localmente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, borrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(storageKey);
                    $form[0].reset();
                    $('.select2').val(null).trigger('change');
                    $('#planning-section').hide();
                    $('#client-info-card').hide();
                    $('#history-section').hide();
                    updateProgress();
                    toastr.success('Borrador eliminado');
                }
            });
        });

        // =================================================================
        // LÓGICA DE VALIDACIÓN Y ENVÍO DEL FORMULARIO
        // =================================================================
        
        // Validación de formulario
        function validateField(el) {
            const $el = $(el);
            const name = $el.attr('name');

            if (!$el.prop('required')) {
                if ($el.val() && $el.val().toString().trim() !== '') {
                    $el.removeClass('is-invalid').addClass('is-valid');
                } else {
                    $el.removeClass('is-valid is-invalid');
                }
                return true;
            }

            const val = $el.val();
            if (!val || val.toString().trim() === '') {
                $el.removeClass('is-valid').addClass('is-invalid');
                return false;
            }

            if ($el.attr('type') === 'url' && val) {
                try {
                    new URL(val);
                } catch (e) {
                    $el.removeClass('is-valid').addClass('is-invalid');
                    return false;
                }
            }

            if ($el.attr('type') === 'number' && val) {
                if (isNaN(val) || parseFloat(val) < 0) {
                    $el.removeClass('is-valid').addClass('is-invalid');
                    return false;
                }
            }

            $el.removeClass('is-invalid').addClass('is-valid');
            return true;
        }

        // Envío de formulario con cálculo final
        $form.on('submit', function(e) {
            e.preventDefault();

            // Detener el cronómetro y calcular la duración final
            if (startTimeInterval) {
                clearInterval(startTimeInterval);
                startTimeInterval = null;
            }

            const $startTimeField = $('#start_time');
            const $durationField = $('#duration');
            const $durationDisplay = $('#duration-display');

            if (startTime && $startTimeField.val()) {
                const endTime = new Date();
                const durationInSeconds = Math.round((endTime - startTime) / 1000);
                $durationField.val(durationInSeconds);
                $durationDisplay.val(`${durationInSeconds} segundos`);
            } else {
                $durationField.val(0);
                $durationDisplay.val('0 segundos');
            }

            let valid = true;

            $form.find('select[required], textarea[required], input[required]').each(function() {
                const ok = validateField(this);
                if (!ok) valid = false;
            });

            if (!valid) {
                const $first = $form.find('.is-invalid').first();
                $('html,body').animate({
                    scrollTop: $first.offset().top - 90
                }, 350);
                Swal.fire({
                    icon: 'error',
                    title: 'Errores en el formulario',
                    text: 'Por favor corrige los campos marcados antes de enviar.',
                });
                return false;
            }

            Swal.fire({
                title: 'Confirmar envío',
                text: "¿Deseas enviar la interacción ahora?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(storageKey);
                    showLoader();
                    $form.off('submit');
                    $form.submit();
                }
            });
        });

        // =================================================================
        // LÓGICA DE ESCALAMIENTO DESDE EL HISTORIAL
        // =================================================================
        
        // Función para manejar la selección de interacción padre
        function selectParentInteraction(interactionId, interactionInfo) {
            console.log('selectParentInteraction llamado con ID:', interactionId); // DEBUG
            // Actualizar el campo oculto
            $('#parent_interaction_id').val(interactionId);
            
            // Mostrar información de la interacción seleccionada
            const $selectedInfo = $('#selected-parent-info');
            const $selectedText = $('#selected-parent-text');
            
            $selectedText.text(interactionInfo);
            $selectedInfo.fadeIn();
            
            // Resaltar visualmente el elemento seleccionado
            $('.history-item').removeClass('border-primary bg-light');
            $(`#history-item-${interactionId}`).addClass('border-primary bg-light');
            
            // Guardar en el borrador
            saveDraftDebounced();
            
            // Notificar al usuario
            toastr.info('Interacción padre seleccionada para escalamiento');
        }

        // Función para limpiar la selección de interacción padre
        function clearParentSelection() {
            console.log('clearParentSelection llamado'); // DEBUG
            $('#parent_interaction_id').val('');
            $('#selected-parent-info').fadeOut();
            
            // Quitar resaltado visual
            $('.history-item').removeClass('border-primary bg-light');
            
            // Guardar en el borrador
            saveDraftDebounced();
            
            // Notificar al usuario
            toastr.info('Selección de interacción padre eliminada');
        }

        // Hacer las funciones globales por si acaso
        window.selectParentInteraction = selectParentInteraction;
        window.clearParentSelection = clearParentSelection;

        // Evento para el botón de limpiar selección
        $('#clear-parent-selection').on('click', clearParentSelection);

        // Delegación de eventos para los botones de escalar (para elementos dinámicos)
        $('#interaction-history-list').on('click', '.btn-escalate', function() {
            console.log('Botón de escalar clickeado'); // DEBUG
            const $button = $(this);
            const interactionId = $button.data('id');
            const interactionInfo = $button.data('info');
            
            selectParentInteraction(interactionId, interactionInfo);
        });

        // =================================================================
        // LÓGICA DE HISTORIAL DE CLIENTES
        // =================================================================
        
        // Función para actualizar el historial del cliente (versión mejorada con escalamiento)
        function refreshClientHistory() {
            console.log('refreshClientHistory llamado'); // DEBUG
            const cod_ter = $('#client_id').val();
            if (!cod_ter) return;
            
            const $historyList = $('#interaction-history-list');
            $historyList.empty().html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div> Cargando historial...</div>');
            
            $.ajax({
                url: @json(route('interactions.cliente.show', ['cod_ter' => ':cod_ter'])).replace(':cod_ter', cod_ter),
                type: 'GET',
                dataType: 'json',
                timeout: 15000,
            }).done(function(data) {
                console.log('Datos de historial recibidos:', data); // DEBUG
                $historyList.empty();
                if (data.history && data.history.length) {
                    let historyHtml = '';
                    data.history.forEach(item => {
                        // Crear una cadena de info segura para el atributo data
                        const interactionInfo = `${item.type} - ${item.date.substring(0, 10)}`;
                        
                        historyHtml += `
                            <div class="history-item mb-3 p-3 border rounded bg-white" id="history-item-${item.id}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary me-2">${item.type}</span>
                                        <span class="badge bg-success">${item.outcome}</span>
                                    </div>
                                    <small class="text-muted">${item.date}</small>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <strong>Agente:</strong> ${item.agent}
                                </div>
                                <div class="text-muted mb-2">
                                    <i class="bi bi-chat-text me-1"></i>
                                    ${item.notes}
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-escalate" 
                                            data-id="${item.id}" 
                                            data-info="${interactionInfo}">
                                        <i class="bi bi-arrow-up-circle me-1"></i> Escalar desde aquí
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    $historyList.html(historyHtml);
                    
                    // Restaurar la selección si existe en el borrador
                    const parentId = $('#parent_interaction_id').val();
                    if (parentId) {
                        console.log('Restaurando selección para ID de padre:', parentId); // DEBUG
                        const $selectedItem = $(`#history-item-${parentId}`);
                        if ($selectedItem.length) {
                            $selectedItem.addClass('border-primary bg-light');
                            
                            // Mostrar información de la interacción seleccionada
                            const $selectedInfo = $('#selected-parent-info');
                            const $selectedText = $('#selected-parent-text');
                            
                            // Buscar la información de la interacción seleccionada
                            const parentItem = data.history.find(item => item.id == parentId);
                            if (parentItem) {
                                const interactionInfo = `${parentItem.type} - ${parentItem.date.substring(0, 10)}`;
                                $selectedText.text(interactionInfo);
                                $selectedInfo.show();
                            }
                        }
                    }
                } else {
                    $historyList.html('<div class="text-center text-muted py-3">No hay interacciones previas con este cliente</div>');
                }
                toastr.success('Historial actualizado correctamente');
            }).fail(function(xhr, status, error) {
                console.error('Error al actualizar historial:', {xhr, status, error});
                
                let errorMessage = 'No se pudo cargar el historial del cliente';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado tiempo. Inténtalo de nuevo.';
                }
                
                $historyList.html(`<div class="text-center text-danger py-3">${errorMessage}</div>`);
                toastr.error(errorMessage);
            });
        }

        // Función para actualizar la visibilidad de elementos en la pestaña de historial
        function updateHistoryTabVisibility() {
            const clientId = $('#client_id').val();
            if (clientId) {
                $('#no-client-selected').hide();
                $('#history-section').show();
                // Cargar el historial si no está cargado
                if ($('#interaction-history-list').is(':empty')) {
                    refreshClientHistory();
                }
            } else {
                $('#no-client-selected').show();
                $('#history-section').hide();
            }
        }

        // =================================================================
        // LÓGICA DE CARGA DE INFORMACIÓN DEL CLIENTE
        // =================================================================
        
        // Cargar información del cliente con cronómetro
        $('#client_id').on('change', function() {
            const cod_ter = $(this).val();
            const clientCard = $('#client-info-card');
            const historySection = $('#history-section');
            const historyList = $('#interaction-history-list');
            const $durationDisplay = $('#duration-display');
            const $startTimeField = $('#start_time');

            // Limpiar cualquier cronómetro existente
            if (startTimeInterval) {
                clearInterval(startTimeInterval);
                startTimeInterval = null;
            }

            if (!cod_ter) {
                clientCard.fadeOut();
                historySection.fadeOut();
                $durationDisplay.val('0 segundos');
                $startTimeField.val('');
                $('#duration').val(0);
                updateProgress();
                updateHistoryTabVisibility();
                return;
            }

            // Registrar hora de inicio y empezar a contar
            startTime = new Date();
            $startTimeField.val(startTime.toISOString());
            $durationDisplay.val('0 segundos');
            $('#duration').val(0);

            startTimeInterval = setInterval(() => {
                const now = new Date();
                const durationSeconds = Math.round((now - startTime) / 1000);
                $durationDisplay.val(`${durationSeconds} segundos`);
                $('#duration').val(durationSeconds);
            }, 1000);

            showLoader();
            
            $.ajax({
                url: @json(route('interactions.cliente.show', ['cod_ter' => ':cod_ter'])).replace(':cod_ter', cod_ter),
                type: 'GET',
                dataType: 'json',
                timeout: 15000,
            })
            .done(function(data) {                    
                // Verificar si hay error en la respuesta
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Actualizar avatar con iniciales
                const initials = ((data.nom1 || '').charAt(0) + (data.apl1 || '').charAt(0))
                    .toUpperCase();
                $('#info-avatar').text(initials || '—');
                
                // Actualizar información básica del cliente
                $('#info-nombre').text(data.nom_ter || 'No registrado');
                $('#info-id').text(`ID: ${data.cod_ter || 'N/A'}`);
                $('#info-distrito').text(data.distrito?.NOM_DIST || 'No registrado');
                $('#info-categoria').text(data.maeTipos?.nombre || 'No registrado');
                $('#info-email').text(data.email || 'No registrado');
                $('#info-telefono').text(data.tel1 || 'No registrado');
                $('#info-direccion').text(data.dir || 'No registrado');
                $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`);
                $('#btn-ver-cliente').attr('href', `/maestras/terceros/${data.cod_ter}`);

                // Mejora en la carga del historial
                historyList.empty();
                if (data.history && data.history.length > 0) {
                    console.log(data);
                    let historyHtml = '';
                    data.history.forEach(item => {
                        // Crear una cadena de info segura para el atributo data
                        const interactionInfo = `${item.type} - ${item.date.substring(0, 10)}`;
                        
                        historyHtml += `
                            <div class="history-item mb-3 p-3 border rounded bg-white" id="history-item-${item.id}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="badge bg-primary me-2">${item.type}</span>
                                        <span class="badge bg-success">${item.outcome}</span>
                                    </div>
                                    <small class="text-muted">${item.date}</small>
                                </div>
                                <div class="mb-1">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <strong>Agente:</strong> ${item.agent}
                                </div>
                                <div class="text-muted mb-2">
                                    <i class="bi bi-chat-text me-1"></i>
                                    ${item.notes}
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-escalate" 
                                            data-id="${item.id}" 
                                            data-info="${interactionInfo}">
                                        <i class="bi bi-arrow-up-circle me-1"></i> Escalar desde aquí
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    historyList.html(historyHtml);
                    historySection.fadeIn();
                } else {
                    historyList.html('<div class="text-center text-muted py-3">No hay interacciones previas con este cliente</div>');
                    historySection.fadeIn();
                }

                clientCard.fadeIn();
                updateProgress();
                toastr.success('Información del cliente cargada correctamente');
            })
            .fail(function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', {xhr, status, error});
                
                let errorMessage = 'No se pudo cargar la información del cliente';
                
                // Intentar obtener un mensaje de error más específico
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (status === 'timeout') {
                    errorMessage = 'La solicitud tardó demasiado tiempo. Inténtalo de nuevo.';
                } else if (status === 'error') {
                    errorMessage = 'Error de conexión. Verifica tu conexión a internet.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar cliente',
                    text: errorMessage,
                    footer: 'Código de error: ' + xhr.status
                });
                
                $('#client-info-card').fadeOut();
                $('#history-section').fadeOut();
            })
            .always(function() {
                hideLoader();
            });
        });

        // =================================================================
        // LÓGICA DE PLANIFICACIÓN
        // =================================================================
        
        // Lógica para mostrar/ocultar sección de planificación
        const outcomeSelect = $('#outcome');
        const planningSection = $('#planning-section');

        function handleOutcomeChange() {
            const selectedOutcomeText = outcomeSelect.find("option:selected").text().trim();
            if (selectedOutcomeText === 'Pendiente' || selectedOutcomeText === 'No contesta' ||
                selectedOutcomeText.toLowerCase().includes('pendiente')) {
                planningSection.slideDown();
            } else {
                planningSection.slideUp();
            }
            updateProgress();
        }

        outcomeSelect.on('change', handleOutcomeChange);
        handleOutcomeChange();

        // Botones de fecha rápida
        $('.quick-date').on('click', function() {
            const daysToAdd = parseInt($(this).data('days')) || 0;
            const nextActionInput = $('#next_action_date');
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + daysToAdd);
            const year = targetDate.getFullYear();
            const month = String(targetDate.getMonth() + 1).padStart(2, '0');
            const day = String(targetDate.getDate()).padStart(2, '0');
            const hours = '09';
            const minutes = '00';
            const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
            nextActionInput.val(formatted);
            updateProgress();
            saveDraftDebounced();
            toastr.info('Fecha establecida: ' + new Date(formatted).toLocaleString());
        });

        // =================================================================
        // LÓGICA DE INTERFAZ DE USUARIO
        // =================================================================
        
        // Contador de caracteres para el campo de notas
        const $notes = $('#notes');
        const $notesCounter = $('#notes-counter');

        $notes.on('input', function() {
            const length = $(this).val().length;
            $notesCounter.text(`${length}/500 caracteres`);

            if (length > 500) {
                $notesCounter.addClass('text-danger');
            } else {
                $notesCounter.removeClass('text-danger');
            }
        });

        // Inicializar contador de caracteres
        $notes.trigger('input');

        // Funcionalidad de colapsar/expandir secciones
        $('#toggle-client-info').on('click', function() {
            const $content = $('#client-info-content');
            const $icon = $(this).find('i');

            $content.slideToggle();
            $icon.toggleClass('bi-chevron-down bi-chevron-up');
        });

        $('#refresh-history').on('click', function() {
            refreshClientHistory();
        });

        $('#toggle-history').on('click', function() {
            const $content = $('#history-content');
            const $icon = $(this).find('i');

            $content.slideToggle();
            $icon.toggleClass('bi-chevron-down bi-chevron-up');
        });

        // Mejora en la carga de imágenes de avatar
        function generateAvatar(name) {
            const colors = ['#6B9BD1', '#7FA9D3', '#93B7D5', '#A7C5D7', '#BBD3D9'];
            const initials = name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2);
            const colorIndex = name.charCodeAt(0) % colors.length;

            return {
                initials: initials || '—',
                color: colors[colorIndex]
            };
        }

        // Actualizar avatar con colores dinámicos
        $('#client_id').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            const avatarData = generateAvatar(selectedText);
            const $avatar = $('#info-avatar');

            $avatar.text(avatarData.initials);
            $avatar.css('background-color', avatarData.color);
        });

        // Mejora en la visualización de archivos adjuntos
        $('#attachment').on('change', function() {
            const file = this.files[0];
            
            if (file) {
                // Mostrar información del archivo
                toastr.info(`Archivo seleccionado: ${file.name}`);
                
                // Si es una imagen, mostrar vista previa
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        $('#image-preview').attr('src', e.target.result);
                        $('#image-preview-container').show();
                    };
                    
                    reader.onerror = function() {
                        toastr.error('Error al cargar la vista previa de la imagen');
                        $('#image-preview-container').hide();
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    $('#image-preview-container').hide();
                }
            }
        });

        // Función para validar URL
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Validación de URL en tiempo real
        $('#interaction_url').on('input', function() {
            const url = $(this).val();
            if (url && !isValidUrl(url)) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // =================================================================
        // ATAJOS DE TECLADO
        // =================================================================
        
        // Atajos de teclado
        $(document).on('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                saveDraft();
                toastr.success('Borrador guardado');
            }

            if (e.altKey) {
                switch (e.key) {
                    case '1':
                        showTab('principal');
                        break;
                    case '2':
                        showTab('adicional');
                        break;
                    case '3':
                        showTab('resultado');
                        break;
                    case '4':
                        showTab('adjuntos');
                        break;
                    case '5':
                        showTab('historial');
                        break;
                }
            }
        });
    });
</script>
</body>
</html>
=======

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <style>
        /* ===== COLORES PASTELES CORPORATIVOS MINIMALISTAS ===== */
        :root {
            --primary-color: #6B9BD1;
            --primary-light: #E6F3FF;
            --secondary-color: #F5F0FF;
            --accent-color: #E8F5E8;
            --text-primary: #374151;
            --text-secondary: #6B7280;
            --border-color: #E5E7EB;
            --background-color: #FAFBFC;
            --card-background: #FFFFFF;
            --input-background: #F8F9FA;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --border-radius: 8px;
            --transition: all 0.2s ease;
        }

        /* ===== ESTILOS GENERALES ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /*  body {
            margin: 0;
            padding: 20px;
            background-color: var(--background-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;} 
        */

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        /* ===== TARJETA PRINCIPAL ===== */
        .card {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            overflow: hidden;
            background-color: var(--card-background);
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: var(--primary-light);
            color: var(--text-primary);
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-body {
            padding: 0;
        }

        /* ===== BARRA DE PROGRESO ===== */
        .progress-section {
            background-color: var(--input-background);
            padding: 20px 32px;
            border-bottom: 1px solid var(--border-color);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .progress-title {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .progress-percentage {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .progress-bar-container {
            height: 6px;
            background: rgba(107, 155, 209, 0.2);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary-color);
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .progress-message {
            font-size: 12px;
            color: var(--text-secondary);
            text-align: center;
        }

        /* ===== NAVEGACIÓN POR PESTAÑAS ===== */
        .tab-navigation {
            display: flex;
            background-color: var(--input-background);
            border-bottom: 1px solid var(--border-color);
        }

        .tab-button {
            flex: 1;
            padding: 18px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .tab-button i { 
            font-size: 18px; 
        }
        
        .tab-button span { 
            font-size: 12px; 
            font-weight: 500; 
        }

        .tab-button.active {
            color: var(--primary-color);
            background: var(--card-background);
            border-bottom-color: var(--primary-color);
        }

        .tab-button:hover:not(.active) {
            color: var(--text-primary);
            background-color: rgba(107, 155, 209, 0.05);
        }

        /* ===== CONTENIDO DEL FORMULARIO ===== */
        .form-content { 
            padding: 1.5rem; 
        }
        
        .tab-panel { 
            display: none; 
            animation: fadeIn 0.3s ease; 
        }
        
        .tab-panel.active { 
            display: block; 
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ===== CATEGORÍAS ===== */
        .category-container {
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            background: var(--card-background);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .category-container:hover {
            box-shadow: var(--shadow-md);
        }

        .category-header {
            background-color: var(--input-background);
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .category-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
        }

        .category-icon {
            width: 36px;
            height: 36px;
            background-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 16px;
        }

        .category-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin: 4px 0 0 48px;
        }

        .category-content { 
            padding: 1.5rem; 
        }

        /* ===== SECCIONES INTERNAS ===== */
        .section-divider {
            position: relative;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .section-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        /* ===== FORMULARIOS ===== */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem;
            transition: var(--transition);
            font-size: 0.95rem;
            width: 100%;
            background-color: var(--card-background);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.1);
            outline: none;
        }

        /* ===== BOTONES ===== */
        .btn-primary {
            background-color: var(--primary-color);
            border: none; 
            color: white;
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background-color: #5A8AC1;
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .btn-light {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-light:hover {
            background-color: var(--input-background);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            background: transparent;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.6rem 1.25rem;
            border-radius: var(--border-radius);
            background: transparent;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--input-background);
            transform: translateY(-1px);
        }

        /* ===== TARJETAS ===== */
        .card.border-0.bg-light {
            background-color: var(--input-background) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: var(--border-radius) !important;
            margin-bottom: 1rem !important;
            transition: var(--transition);
        }

        .card.border-0.bg-light:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* ===== AVATAR ===== */
        .client-avatar {
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border-radius: 50%;
            transition: var(--transition);
        }

        .client-avatar:hover {
            transform: scale(1.05);
        }

        /* ===== SELECT2 ===== */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
        }
        
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.1);
        }

        /* ===== VALIDACIÓN ===== */
        .is-invalid { 
            border-color: #EF4444; 
        }
        
        .is-valid { 
            border-color: #10B981; 
        }
        
        .invalid-feedback { 
            color: #EF4444; 
            font-size: 0.875rem; 
            margin-top: 0.25rem; 
        }

        /* ===== CAMPO SOLO LECTURA ===== */
        .form-control[readonly] {
            background-color: var(--input-background);
            opacity: 1;
        }

        /* ===== ACCIONES DEL FORMULARIO ===== */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* ===== ESTILOS PARA EL HISTORIAL ===== */
        .history-item {
            transition: var(--transition);
        }
        
        .history-item:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        
        .history-list::-webkit-scrollbar { 
            width: 6px; 
        }
        
        .history-list::-webkit-scrollbar-track { 
            background: var(--input-background); 
            border-radius: 3px; 
        }
        
        .history-list::-webkit-scrollbar-thumb { 
            background: var(--border-color); 
            border-radius: 3px; 
        }
        
        .history-list::-webkit-scrollbar-thumb:hover { 
            background: var(--text-secondary); 
        }

        /* ===== IMÁGENES ===== */
        .img-thumbnail {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            padding: 0.25rem;
            background-color: var(--card-background);
            transition: var(--transition);
        }

        .img-thumbnail:hover {
            transform: scale(1.02);
            box-shadow: var(--shadow-sm);
        }

        .file-preview {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--border-radius);
            margin-top: 0.5rem;
            object-fit: contain;
        }

        /* ===== UTILIDADES BOOTSTRAP REEMPLAZADAS ===== */
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .align-items-start {
            align-items: flex-start;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-muted {
            color: var(--text-secondary);
        }
        
        .text-primary {
            color: var(--primary-color);
        }
        
        .text-danger {
            color: #EF4444;
        }
        
        .text-white {
            color: white;
        }
        
        .mb-0 {
            margin-bottom: 0;
        }
        
        .mb-1 {
            margin-bottom: 0.25rem;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .mt-1 {
            margin-top: 0.25rem;
        }
        
        .mt-2 {
            margin-top: 0.5rem;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .me-1 {
            margin-right: 0.25rem;
        }
        
        .me-2 {
            margin-right: 0.5rem;
        }
        
        .me-3 {
            margin-right: 1rem;
        }
        
        .me-auto {
            margin-right: auto;
        }
        
        .ms-1 {
            margin-left: 0.25rem;
        }
        
        .ms-auto {
            margin-left: auto;
        }
        
        .py-3 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        
        .py-5 {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        
        .p-2 {
            padding: 0.5rem;
        }
        
        .p-3 {
            padding: 1rem;
        }
        
        .p-4 {
            padding: 1.5rem;
        }
        
        .px-4 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        
        .gap-1 {
            gap: 0.25rem;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 1rem;
        }
        
        .g-3 > * {
            margin-bottom: 1rem;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-md-12 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        
        @media (min-width: 768px) {
            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
            
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            
            .col-md-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .col-md-8 {
                flex: 0 0 66.666667%;
                max-width: 66.666667%;
            }
            
            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        .flex-grow-1 {
            flex-grow: 1;
        }
        
        .flex-shrink-0 {
            flex-shrink: 0;
        }
        
        .small {
            font-size: 0.875em;
        }
        
        .fw-bold {
            font-weight: 700;
        }
        
        .fw-semibold {
            font-weight: 600;
        }
        
        .rounded-3 {
            border-radius: var(--border-radius);
        }
        
        .border {
            border: 1px solid var(--border-color);
        }
        
        .border-0 {
            border: 0;
        }
        
        .rounded {
            border-radius: var(--border-radius);
        }
        
        .bg-light {
            background-color: var(--input-background);
        }
        
        .bg-gradient-primary {
            background: var(--primary-light);
        }
        
        .opacity-75 {
            opacity: 0.75;
        }
        
        .overflow-hidden {
            overflow: hidden;
        }
        
        .shadow-lg {
            box-shadow: var(--shadow-md);
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: calc(var(--border-radius) - 2px);
        }
        
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: calc(var(--border-radius) - 2px);
        }
        
        .bg-primary {
            background-color: var(--primary-color);
        }
        
        .bg-success {
            background-color: #10B981;
        }
        
        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: -0.125em;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border 0.75s linear infinite;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.125em;
        }
        
        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }
        
        .visually-hidden {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
        
        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: var(--text-secondary);
        }
        
        .position-fixed {
            position: fixed;
        }
        
        .z-index-9999 {
            z-index: 9999;
        }
        
        .top-0 {
            top: 0;
        }
        
        .start-0 {
            left: 0;
        }
        
        .end-0 {
            right: 0;
        }
        
        .bottom-0 {
            bottom: 0;
        }
        
        .display-1 {
            font-size: 6rem;
            font-weight: 300;
            line-height: 1.2;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            body { 
                padding: 10px; 
            }
            
            .form-content { 
                padding: 1rem; 
            }
            
            .category-content { 
                padding: 1rem; 
            }
            
            .tab-button span { 
                display: none; 
            }
            
            .tab-button { 
                padding: 16px; 
            }
            
            .d-flex.justify-content-between.mt-4 { 
                flex-direction: column; 
                gap: 1rem; 
            }
            
            .d-flex.justify-content-between.mt-4>div { 
                width: 100%; 
            }
            
            .d-flex.justify-content-between.mt-4>div>button { 
                width: 100%; 
            }

            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .action-buttons {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* ===== MEJORAS VISUALES ===== */
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .spinner-border {
            width: 1rem;
            height: 1rem;
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--input-background);
        }

        /* ===== ESTADOS ESPECIALES ===== */
        .highlight {
            background-color: var(--primary-light);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .highlight:hover {
            background-color: #D1E9FF;
        }

        .required-field::after {
            content: " *";
            color: #EF4444;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            <!-- Header del formulario -->
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-white bg-opacity-20 rounded-3 p-2 me-3">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold">{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</h4>
                        <p class="mb-0 opacity-75 small">
                            {{ $modoEdicion ? 'Modifica la información de la interacción existente' : 'Completa el formulario para registrar una nueva interacción' }}
                        </p>
                    </div>
                    @if ($modoEdicion && $interaction->id)
                        <div class="flex-shrink-0">
                            <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-eye me-1"></i> Ver Detalles
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="progress-section">
                <div class="progress-header">
                    <span class="progress-title">Progreso del formulario</span>
                    <span class="progress-percentage" id="progress-percentage">0%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progress-bar" style="width: 0%"></div>
                </div>
                <div class="progress-message" id="progress-message">Comienza seleccionando un cliente</div>
            </div>

            <!-- Navegación por pestañas -->
            <div class="tab-navigation">
                <button type="button" class="tab-button active" data-tab="principal">
                    <i class="bi bi-info-circle"></i>
                    <span>Información Principal</span>
                </button>
                <button type="button" class="tab-button" data-tab="adicional">
                    <i class="bi bi-briefcase"></i>
                    <span>Información Adicional</span>
                </button>
                <button type="button" class="tab-button" data-tab="resultado">
                    <i class="bi bi-check-circle"></i>
                    <span>Resultado y Planificación</span>
                </button>
                <button type="button" class="tab-button" data-tab="adjuntos">
                    <i class="bi bi-paperclip"></i>
                    <span>Adjuntos y Referencias</span>
                </button>
                <button type="button" class="tab-button" data-tab="historial">
                    <i class="bi bi-clock-history"></i>
                    <span>Historial</span>
                </button>
            </div>

            <div class="form-content">
                <form id="interaction-form"
                    action="{{ $modoEdicion ? route('interactions.update', $interaction->id) : route('interactions.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($modoEdicion)
                        @method('PUT')
                    @endif

                    <!-- PESTAÑA 1: INFORMACIÓN PRINCIPAL -->
                    <div class="tab-panel active" id="principal-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    Información Principal
                                </h5>
                                <p class="category-description">Datos esenciales de la interacción y el cliente</p>
                            </div>
                            <div class="category-content">
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>Registro del Asociado
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="client_id" class="form-label required-field">Cliente</label>
                                            <select class="form-select select2 @error('client_id') is-invalid @enderror"
                                                id="client_id" name="client_id" required>
                                                <option value="">Selecciona un cliente</option>
                                                @if ($modoEdicion && $interaction->client_id)
                                                    <option value="{{ $interaction->client_id }}" selected>
                                                        {{ $interaction->client->nom_ter }} ({{ $interaction->client_id }})
                                                    </option>
                                                @endif
                                            </select>
                                            @error('client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="agent" class="form-label">Agente</label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ auth()->user()->name }}" readonly>
                                            <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                                            <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
                                            <div class="form-text mt-1">
                                                <i class="bi bi-calendar me-1"></i> {{ now()->format('d/m/Y h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="client-info-card" class="card border-0 bg-light mb-4" style="display:none;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-info-circle text-primary me-2"></i>
                                            <h6 class="mb-0 fw-semibold">Información del Cliente</h6>
                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    id="toggle-client-info">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="client-info-content">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="client-avatar me-2" id="info-avatar"
                                                            style="width:40px;height:40px;border-radius:50%;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                                        </div>
                                                        <div>
                                                            <div id="info-nombre" class="fw-semibold">—</div>
                                                            <div id="info-id" class="text-muted small">ID: —</div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-geo-alt text-muted me-2"></i>
                                                        <span id="info-distrito">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-tag text-muted me-2"></i>
                                                        <span id="info-categoria">Cargando...</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-envelope text-muted me-2"></i>
                                                        <span id="info-email">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-telephone text-muted me-2"></i>
                                                        <span id="info-telefono">Cargando...</span>
                                                    </div>
                                                    <div class="info-item mb-2">
                                                        <i class="bi bi-geo text-muted me-2"></i>
                                                        <span id="info-direccion">Cargando...</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-end mt-2">
                                                <a id="btn-editar-cliente" href="#"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil me-1"></i> Ver ficha completa
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-chat-dots me-2 text-primary"></i>Detalles de la Interacción
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="interaction_channel" class="form-label required-field">Canal</label>
                                            <select
                                                class="form-select select2 @error('interaction_channel') is-invalid @enderror"
                                                id="interaction_channel" name="interaction_channel" required>
                                                <option value="">Selecciona un canal</option>
                                                @foreach ($channels as $channel)
                                                    <option value="{{ $channel->id }}"
                                                        {{ old('interaction_channel', $interaction->interaction_channel ?? '') == $channel->id ? 'selected' : '' }}>
                                                        {{ $channel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('interaction_channel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="interaction_type" class="form-label required-field">Tipo</label>
                                            <select class="form-select select2 @error('interaction_type') is-invalid @enderror"
                                                id="interaction_type" name="interaction_type" required>
                                                <option value="">Selecciona un tipo</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ old('interaction_type', $interaction->interaction_type ?? '') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('interaction_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="duration-display" class="form-label">Duración</label>
                                            <input type="text" class="form-control bg-light" id="duration-display"
                                                readonly
                                                value="{{ $modoEdicion && $interaction->duration ? $interaction->duration . ' segundos' : '0 segundos' }}">
                                            <input type="hidden" id="start_time" name="start_time"
                                                value="{{ old('start_time', $interaction->start_time ?? '') }}">
                                            <input type="hidden" id="duration" name="duration"
                                                value="{{ old('duration', $interaction->duration ?? '') }}">
                                            <div class="form-text mt-1">Se calcula automáticamente al seleccionar un cliente.</div>
                                            @error('duration')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes" class="form-label required-field">Notas</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                                placeholder="Describe los detalles de la interacción..." required>{{ old('notes', $interaction->notes ?? '') }}</textarea>
                                            <div class="d-flex justify-content-between">
                                                <div class="form-text">Añade aquí todos los detalles relevantes de la interacción.</div>
                                                <div class="form-text text-end" id="notes-counter">0/500 caracteres</div>
                                            </div>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div></div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('adicional')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 2: INFORMACIÓN ADICIONAL -->
                    <div class="tab-panel" id="adicional-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-briefcase"></i>
                                    </div>
                                    Información Adicional
                                </h5>
                                <p class="category-description">Datos complementarios del cliente y asignación</p>
                            </div>
                            <div class="category-content">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="area-display" class="form-label">Área</label>
                                            <input type="text" class="form-control bg-light" id="area-display" readonly 
                                                   value="{{ $idAreaAgente ? $areas[$idAreaAgente] : 'No asignada' }}">
                                            <input type="hidden" id="id_area" name="id_area" value="{{ old('id_area', $interaction->id_area ?? $idAreaAgente ?? '') }}">
                                            <div class="form-text mt-1">Asignada automáticamente según el cargo del agente.</div>
                                            @error('id_area')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cargo-display" class="form-label">Cargo</label>
                                            <input type="text" class="form-control bg-light" id="cargo-display" readonly 
                                                   value="{{ $idCargoAgente ? $cargos[$idCargoAgente] : 'No asignado' }}">
                                            <input type="hidden" id="id_cargo" name="id_cargo" value="{{ old('id_cargo', $interaction->id_cargo ?? $idCargoAgente ?? '') }}">
                                            <div class="form-text mt-1">Asignado automáticamente según el agente logueado.</div>
                                            @error('id_cargo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_linea_de_obligacion" class="form-label">Línea de Obligación</label>
                                            <select
                                                class="form-select select2 @error('id_linea_de_obligacion') is-invalid @enderror"
                                                id="id_linea_de_obligacion" name="id_linea_de_obligacion">
                                                <option value="">Selecciona una línea</option>
                                                @if (isset($lineasCredito))
                                                    @foreach ($lineasCredito as $id => $nombre)
                                                        <option value="{{ $id }}"
                                                            {{ old('id_linea_de_obligacion', $interaction->id_linea_de_obligacion ?? '') == $id ? 'selected' : '' }}>
                                                            {{ $nombre }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('id_linea_de_obligacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_area_de_asignacion" class="form-label">Área de Asignación</label>
                                            <select
                                                class="form-select select2 @error('id_area_de_asignacion') is-invalid @enderror"
                                                id="id_area_de_asignacion" name="id_area_de_asignacion">
                                                <option value="">Selecciona un área</option>
                                                @if (isset($areas))
                                                    @foreach ($areas as $id => $nombre)
                                                        <option value="{{ $id }}"
                                                            {{ old('id_area_de_asignacion', $interaction->id_area_de_asignacion ?? '') == $id ? 'selected' : '' }}>
                                                            {{ $nombre }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('id_area_de_asignacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_distrito_interaccion" class="form-label">Distrito de la Interacción</label>
                                            <select
                                                class="form-select select2 @error('id_distrito_interaccion') is-invalid @enderror"
                                                id="id_distrito_interaccion" name="id_distrito_interaccion">
                                                <option value="">Selecciona un distrito</option>
                                                @if (isset($distrito))
                                                    @foreach ($distrito as $id => $nombre)
                                                        <option value="{{ $id }}"
                                                            {{ old('id_distrito_interaccion', $interaction->id_distrito_interaccion ?? '') == $id ? 'selected' : '' }}>
                                                            {{ $nombre }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('id_distrito_interaccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('principal')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('resultado')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 3: RESULTADO Y PLANIFICACIÓN -->
                    <div class="tab-panel" id="resultado-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    Resultado y Planificación
                                </h5>
                                <p class="category-description">Resultado de la interacción y próximas acciones</p>
                            </div>
                            <div class="category-content">
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-flag me-2 text-primary"></i>Resultado de la Interacción
                                    </h6>
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="outcome" class="form-label required-field">Resultado</label>
                                            <select class="form-select select2 @error('outcome') is-invalid @enderror"
                                                id="outcome" name="outcome" required>
                                                <option value="">Selecciona un resultado</option>
                                                @foreach ($outcomes as $outcome)
                                                    <option value="{{ $outcome->id }}"
                                                        {{ old('outcome', $interaction->outcome ?? '') == $outcome->id ? 'selected' : '' }}>
                                                        {{ $outcome->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('outcome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="section-divider">
                                    <h6 class="section-title">
                                        <i class="bi bi-calendar-check me-2 text-primary"></i>Planificación
                                    </h6>
                                </div>
                                <div class="card border-0 bg-light mb-4" id="planning-section" style="display:none;">
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="next_action_date" class="form-label">Próxima Acción</label>
                                                    <input type="datetime-local"
                                                        class="form-control @error('next_action_date') is-invalid @enderror"
                                                        id="next_action_date" name="next_action_date"
                                                        value="{{ old('next_action_date', $interaction->next_action_date ?? '') }}">
                                                    <div class="d-flex gap-1 flex-wrap mt-2">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="1">+1 día</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="3">+3 días</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="7">+1 sem</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="14">+2 sem</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary quick-date"
                                                            data-days="30">+1 mes</button>
                                                    </div>
                                                    @error('next_action_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="next_action_type" class="form-label">Tipo de Acción</label>
                                                    <select
                                                        class="form-select select2 @error('next_action_type') is-invalid @enderror"
                                                        id="next_action_type" name="next_action_type">
                                                        <option value="">Selecciona un tipo</option>
                                                        @foreach ($nextActions as $action)
                                                            <option value="{{ $action->id }}"
                                                                {{ old('next_action_type', $interaction->next_action_type ?? '') == $action->id ? 'selected' : '' }}>
                                                                {{ $action->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('next_action_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="next_action_notes" class="form-label">Notas sobre la Próxima Acción</label>
                                                    <textarea class="form-control @error('next_action_notes') is-invalid @enderror" id="next_action_notes"
                                                        name="next_action_notes" rows="3" placeholder="Detalles sobre la próxima acción programada...">{{ old('next_action_notes', $interaction->next_action_notes ?? '') }}</textarea>
                                                    @error('next_action_notes')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('adicional')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('adjuntos')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 4: ADJUNTOS Y REFERENCIAS -->
                    <div class="tab-panel" id="adjuntos-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-paperclip"></i>
                                    </div>
                                    Adjuntos y Referencias
                                </h5>
                                <p class="category-description">Archivos y enlaces relacionados con la interacción</p>
                            </div>
                            <div class="category-content">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="attachment" class="form-label">Archivo Adjunto</label>
                                            <input type="file"
                                                class="form-control @error('attachment') is-invalid @enderror"
                                                id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                            <div class="form-text">Puedes adjuntar un archivo (máx. 10MB)</div>
                                            
                                            @if ($modoEdicion && $interaction->attachment_urls)
                                                <div class="mt-3">
                                                    <div class="small text-muted fw-semibold">Archivo existente:</div>
                                                    <div class="d-flex align-items-center mt-2 p-2 border rounded bg-light">
                                                        <i class="bi bi-file-earmark me-2"></i>
                                                        <span class="me-auto">{{ basename($interaction->attachment_urls) }}</span>
                                                        <a href="{{ route('interactions.download', basename($interaction->attachment_urls)) }}" 
                                                           class="btn btn-sm btn-outline-primary me-1" target="_blank">
                                                            <i class="bi bi-download"></i> Descargar
                                                        </a>
                                                        <a href="{{ route('interactions.view', basename($interaction->attachment_urls)) }}" 
                                                           class="btn btn-sm btn-outline-secondary" target="_blank">
                                                            <i class="bi bi-eye"></i> Ver
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @error('attachment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="interaction_url" class="form-label">Enlace de Referencia</label>
                                            <input type="url"
                                                class="form-control @error('interaction_url') is-invalid @enderror"
                                                id="interaction_url" name="interaction_url" placeholder="https://ejemplo.com"
                                                value="{{ old('interaction_url', $interaction->interaction_url ?? '') }}">
                                            @error('interaction_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Vista previa de imagen -->
                                <div id="image-preview-container" class="mt-3" style="display: none;">
                                    <div class="small text-muted fw-semibold mb-2">Vista previa:</div>
                                    <img id="image-preview" class="img-thumbnail file-preview" alt="Vista previa de la imagen">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('resultado')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-primary" onclick="showTab('historial')">
                                    Continuar <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PESTAÑA 5: HISTORIAL -->
                    <div class="tab-panel" id="historial-tab">
                        <div class="category-container">
                            <div class="category-header">
                                <h5 class="category-title">
                                    <div class="category-icon">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    Historial de Interacciones
                                </h5>
                                <p class="category-description">Interacciones previas con este cliente</p>
                            </div>
                            <div class="category-content">
                                <div id="no-client-selected" class="text-center py-5">
                                    <i class="bi bi-person-x display-1 text-muted"></i>
                                    <p class="mt-3 text-muted">Selecciona un cliente para ver su historial de interacciones</p>
                                </div>
                                <div class="card border-0 bg-light mb-4" id="history-section" style="display:none;">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-clock-history text-primary me-2"></i>
                                            <h6 class="mb-0 fw-semibold">Historial Reciente</h6>
                                            <div class="ms-auto">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-history">
                                                    <i class="bi bi-arrow-clockwise"></i> Actualizar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="toggle-history">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="history-content">
                                            <div id="interaction-history-list" class="history-list" style="max-height:400px; overflow-y:auto;">
                                                <!-- items cargados por JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="action-buttons">
                                <button type="button" class="btn btn-light" onclick="showTab('adjuntos')">
                                    <i class="bi bi-arrow-left"></i> Anterior
                                </button>
                            </div>
                            <div class="action-buttons">
                                <button id="clear-draft" type="button" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-trash me-1"></i> Borrar Borrador
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i>
                                    {{ $modoEdicion ? 'Actualizar Interacción' : 'Guardar Interacción' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AJAX Loader overlay -->
    <div id="ajax-loader" class="ajax-loader" aria-hidden="true"
        style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div class="card text-center p-4">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mb-0">Cargando información del cliente...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicialización de Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).attr('placeholder');
                },
                dropdownParent: $('body')
            });

            // Configuración especial para el campo de cliente con AJAX
            $('#client_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Buscar cliente...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: @json(route('interactions.search-clients')),
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results.map(function(item) {
                                return {
                                    id: item.cod_ter,
                                    text: `${item.cod_ter} - ${item.apl1} ${item.apl2} ${item.nom1} ${item.nom2}`
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                }
            });

            // Variables globales
            const $ajaxLoader = $('#ajax-loader');
            const $form = $('#interaction-form');
            const storageKey = 'interaction_form_draft_v1';

            // Variables para el cronómetro
            let startTimeInterval = null;
            let startTime = null;

            // Funciones de utilidad
            function showLoader() {
                $ajaxLoader.show();
            }

            function hideLoader() {
                $ajaxLoader.hide();
            }

            // Configuración de notificaciones
            toastr.options = {
                "positionClass": "toast-bottom-right",
                "timeOut": "2500",
                "progressBar": true,
            };

            // Navegación por pestañas
            $('.tab-button').on('click', function() {
                const tabId = $(this).data('tab');
                showTab(tabId);
                saveDraft();
            });

            window.showTab = function(tabId) {
                $('.tab-button').removeClass('active');
                $(`.tab-button[data-tab="${tabId}"]`).addClass('active');

                $('.tab-panel').removeClass('active');
                $(`#${tabId}-tab`).addClass('active');

                if (tabId === 'historial') {
                    updateHistoryTabVisibility();
                }

                updateProgress();
            }

            // Actualizar progreso
            function updateProgress() {
                const requiredFields = ['client_id', 'interaction_channel', 'interaction_type', 'notes', 'outcome'];
                let completed = 0;

                requiredFields.forEach(field => {
                    const value = $(`[name="${field}"]`).val();
                    if (value && value.trim() !== '') {
                        completed++;
                    }
                });

                const progress = Math.round((completed / requiredFields.length) * 100);

                $('#progress-bar').css('width', progress + '%');
                $('#progress-percentage').text(progress + '%');

                // Actualizar mensaje
                const messages = [
                    'Comienza seleccionando un cliente',
                    'Agrega los detalles de la interacción',
                    'Completa la información adicional',
                    'Define el resultado',
                    'Adjunta archivos y revisa el historial',
                    'Formulario completo'
                ];

                const messageIndex = Math.min(Math.floor(progress / 20), messages.length - 1);
                $('#progress-message').text(messages[messageIndex]);
            }

            // Guardado automático en localStorage
            function saveDraft() {
                try {
                    const data = {};
                    $form.find('input,textarea,select').each(function() {
                        const name = this.name;
                        if (!name) return;
                        if (this.type === 'file') return;
                        if (this.type === 'checkbox' || this.type === 'radio') {
                            if (this.checked) data[name] = this.value;
                        } else {
                            if ($(this).is('select[multiple]')) {
                                data[name] = $(this).val() || [];
                            } else {
                                data[name] = $(this).val();
                            }
                        }
                    });
                    data.activeTab = $('.tab-button.active').data('tab');
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    toastr.success('Borrador guardado automáticamente');
                } catch (e) {
                    console.warn('No se pudo guardar el borrador', e);
                }
            }

            function debounce(fn, wait) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            const saveDraftDebounced = debounce(saveDraft, 700);

            // Cargar borrador guardado
            function loadDraft() {
                try {
                    const raw = localStorage.getItem(storageKey);
                    if (!raw) return;
                    const data = JSON.parse(raw);
                    Object.keys(data).forEach(name => {
                        if (name === 'activeTab') return;
                        const value = data[name];
                        const $el = $form.find(`[name="${name}"]`);
                        if (!$el.length) return;
                        if ($el.is('select[multiple]')) {
                            $el.val(value).trigger('change');
                        } else if ($el.is('select')) {
                            $el.val(value).trigger('change');
                        } else {
                            $el.val(value);
                        }
                    });

                    // Restaurar el estado del cronómetro
                    if (data.start_time && data.client_id) {
                        $('#start_time').val(data.start_time);
                        if (data.duration) {
                            $('#duration-display').val(`${data.duration} segundos`);
                        }

                        startTime = new Date(data.start_time);

                        startTimeInterval = setInterval(() => {
                            const now = new Date();
                            const durationSeconds = Math.round((now - startTime) / 1000);
                            $('#duration-display').val(`${durationSeconds} segundos`);
                            $('#duration').val(durationSeconds);
                        }, 1000);
                    }

                    if (data.activeTab) {
                        showTab(data.activeTab);
                    }

                    toastr.info('Borrador restaurado automáticamente');
                } catch (e) {
                    console.warn('No se pudo cargar el borrador', e);
                }
            }

            // Eventos para guardar borrador
            $form.on('input change', 'input, textarea, select', function() {
                updateProgress();
                saveDraftDebounced();
            });

            // Cargar borrador al iniciar
            loadDraft();
            updateProgress();

            // Botón para limpiar borrador
            $('#clear-draft').on('click', function() {
                Swal.fire({
                    title: '¿Borrar el borrador guardado?',
                    text: "Esto eliminará el borrador localmente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, borrar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(storageKey);
                        $form[0].reset();
                        $('.select2').val(null).trigger('change');
                        $('#planning-section').hide();
                        $('#client-info-card').hide();
                        $('#history-section').hide();
                        updateProgress();
                        toastr.success('Borrador eliminado');
                    }
                });
            });

            // Validación de formulario
            function validateField(el) {
                const $el = $(el);
                const name = $el.attr('name');

                if (!$el.prop('required')) {
                    if ($el.val() && $el.val().toString().trim() !== '') {
                        $el.removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $el.removeClass('is-valid is-invalid');
                    }
                    return true;
                }

                const val = $el.val();
                if (!val || val.toString().trim() === '') {
                    $el.removeClass('is-valid').addClass('is-invalid');
                    return false;
                }

                if ($el.attr('type') === 'url' && val) {
                    try {
                        new URL(val);
                    } catch (e) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                if ($el.attr('type') === 'number' && val) {
                    if (isNaN(val) || parseFloat(val) < 0) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                $el.removeClass('is-invalid').addClass('is-valid');
                return true;
            }

            // Envío de formulario con cálculo final
            $form.on('submit', function(e) {
                e.preventDefault();

                // Detener el cronómetro y calcular la duración final
                if (startTimeInterval) {
                    clearInterval(startTimeInterval);
                    startTimeInterval = null;
                }

                const $startTimeField = $('#start_time');
                const $durationField = $('#duration');
                const $durationDisplay = $('#duration-display');

                if (startTime && $startTimeField.val()) {
                    const endTime = new Date();
                    const durationInSeconds = Math.round((endTime - startTime) / 1000);
                    $durationField.val(durationInSeconds);
                    $durationDisplay.val(`${durationInSeconds} segundos`);
                } else {
                    $durationField.val(0);
                    $durationDisplay.val('0 segundos');
                }

                let valid = true;

                $form.find('select[required], textarea[required], input[required]').each(function() {
                    const ok = validateField(this);
                    if (!ok) valid = false;
                });

                if (!valid) {
                    const $first = $form.find('.is-invalid').first();
                    $('html,body').animate({
                        scrollTop: $first.offset().top - 90
                    }, 350);
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores en el formulario',
                        text: 'Por favor corrige los campos marcados antes de enviar.',
                    });
                    return false;
                }

                Swal.fire({
                    title: 'Confirmar envío',
                    text: "¿Deseas enviar la interacción ahora?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        localStorage.removeItem(storageKey);
                        showLoader();
                        $form.off('submit');
                        $form.submit();
                    }
                });
            });

            // Función para actualizar el historial del cliente
            function refreshClientHistory() {
                const cod_ter = $('#client_id').val();
                if (!cod_ter) return;
                
                const historyList = $('#interaction-history-list');
                historyList.empty().html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Cargando...</span></div> Cargando historial...</div>');
                
                $.ajax({
                    url: `/interactions/cliente/${cod_ter}`,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 15000,
                }).done(function(data) {
                    historyList.empty();
                    if (data.history && data.history.length) {
                        let historyHtml = '';
                        data.history.forEach(item => {
                            historyHtml += `
                                <div class="history-item mb-3 p-3 border rounded bg-white">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-primary me-2">${item.type}</span>
                                            <span class="badge bg-success">${item.outcome}</span>
                                        </div>
                                        <small class="text-muted">${item.date}</small>
                                    </div>
                                    <div class="mb-1">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <strong>Agente:</strong> ${item.agent}
                                    </div>
                                    <div class="text-muted">
                                        <i class="bi bi-chat-text me-1"></i>
                                        ${item.notes}
                                    </div>
                                </div>
                            `;
                        });
                        historyList.html(historyHtml);
                    } else {
                        historyList.html('<div class="text-center text-muted py-3">No hay interacciones previas con este cliente</div>');
                    }
                    toastr.success('Historial actualizado correctamente');
                }).fail(function(xhr, status, error) {
                    console.error('Error al actualizar historial:', {xhr, status, error});
                    
                    let errorMessage = 'No se pudo cargar el historial del cliente';
                    
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (status === 'timeout') {
                        errorMessage = 'La solicitud tardó demasiado tiempo. Inténtalo de nuevo.';
                    }
                    
                    historyList.html(`<div class="text-center text-danger py-3">${errorMessage}</div>`);
                    toastr.error(errorMessage);
                });
            }

            // Función para actualizar la visibilidad de elementos en la pestaña de historial
            function updateHistoryTabVisibility() {
                const clientId = $('#client_id').val();
                if (clientId) {
                    $('#no-client-selected').hide();
                    $('#history-section').show();
                    // Cargar el historial si no está cargado
                    if ($('#interaction-history-list').is(':empty')) {
                        refreshClientHistory();
                    }
                } else {
                    $('#no-client-selected').show();
                    $('#history-section').hide();
                }
            }

            // Cargar información del cliente con cronómetro
            $('#client_id').on('change', function() {
                const cod_ter = $(this).val();
                const clientCard = $('#client-info-card');
                const historySection = $('#history-section');
                const historyList = $('#interaction-history-list');
                const $durationDisplay = $('#duration-display');
                const $startTimeField = $('#start_time');

                // Limpiar cualquier cronómetro existente
                if (startTimeInterval) {
                    clearInterval(startTimeInterval);
                    startTimeInterval = null;
                }

                if (!cod_ter) {
                    clientCard.fadeOut();
                    historySection.fadeOut();
                    $durationDisplay.val('0 segundos');
                    $startTimeField.val('');
                    $('#duration').val(0);
                    updateProgress();
                    updateHistoryTabVisibility();
                    return;
                }

                // Registrar hora de inicio y empezar a contar
                startTime = new Date();
                $startTimeField.val(startTime.toISOString());
                $durationDisplay.val('0 segundos');
                $('#duration').val(0);

                startTimeInterval = setInterval(() => {
                    const now = new Date();
                    const durationSeconds = Math.round((now - startTime) / 1000);
                    $durationDisplay.val(`${durationSeconds} segundos`);
                    $('#duration').val(durationSeconds);
                }, 1000);

                showLoader();
                
                $.ajax({
                    url: `/interactions/cliente/${cod_ter}`,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 15000,
                })
                .done(function(data) {
                    console.log('Datos del cliente recibidos:', data);
                    
                    // Verificar si hay error en la respuesta
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Actualizar avatar con iniciales
                    const initials = ((data.nom1 || '').charAt(0) + (data.apl1 || '').charAt(0))
                        .toUpperCase();
                    $('#info-avatar').text(initials || '—');
                    
                    // Actualizar información básica del cliente
                    $('#info-nombre').text(data.nom_ter || 'No registrado');
                    $('#info-id').text(`ID: ${data.cod_ter || 'N/A'}`);
                    $('#info-distrito').text(data.distrito?.NOM_DIST || 'No registrado');
                    $('#info-categoria').text(data.maeTipos?.nombre || 'No registrado');
                    $('#info-email').text(data.email || 'No registrado');
                    $('#info-telefono').text(data.tel1 || 'No registrado');
                    $('#info-direccion').text(data.dir || 'No registrado');
                    $('#btn-editar-cliente').attr('href', `/maestras/terceros/${data.cod_ter}/edit`);

                    // Mejora en la carga del historial
                    historyList.empty();
                    if (data.history && data.history.length > 0) {
                        let historyHtml = '';
                        data.history.forEach(item => {
                            historyHtml += `
                                <div class="history-item mb-3 p-3 border rounded bg-white">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-primary me-2">${item.type}</span>
                                            <span class="badge bg-success">${item.outcome}</span>
                                        </div>
                                        <small class="text-muted">${item.date}</small>
                                    </div>
                                    <div class="mb-1">
                                        <i class="bi bi-person-circle me-1"></i>
                                        <strong>Agente:</strong> ${item.agent}
                                    </div>
                                    <div class="text-muted">
                                        <i class="bi bi-chat-text me-1"></i>
                                        ${item.notes}
                                    </div>
                                </div>
                            `;
                        });
                        historyList.html(historyHtml);
                        historySection.fadeIn();
                    } else {
                        historyList.html('<div class="text-center text-muted py-3">No hay interacciones previas con este cliente</div>');
                        historySection.fadeIn();
                    }

                    clientCard.fadeIn();
                    updateProgress();
                    toastr.success('Información del cliente cargada correctamente');
                })
                .fail(function(xhr, status, error) {
                    console.error('Error en la solicitud AJAX:', {xhr, status, error});
                    
                    let errorMessage = 'No se pudo cargar la información del cliente';
                    
                    // Intentar obtener un mensaje de error más específico
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (status === 'timeout') {
                        errorMessage = 'La solicitud tardó demasiado tiempo. Inténtalo de nuevo.';
                    } else if (status === 'error') {
                        errorMessage = 'Error de conexión. Verifica tu conexión a internet.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al cargar cliente',
                        text: errorMessage,
                        footer: 'Código de error: ' + xhr.status
                    });
                    
                    $('#client-info-card').fadeOut();
                    $('#history-section').fadeOut();
                })
                .always(function() {
                    hideLoader();
                });
            });

            // Lógica para mostrar/ocultar sección de planificación
            const outcomeSelect = $('#outcome');
            const planningSection = $('#planning-section');

            function handleOutcomeChange() {
                const selectedOutcomeText = outcomeSelect.find("option:selected").text().trim();
                if (selectedOutcomeText === 'Pendiente' || selectedOutcomeText === 'No contesta' ||
                    selectedOutcomeText.toLowerCase().includes('pendiente')) {
                    planningSection.slideDown();
                } else {
                    planningSection.slideUp();
                }
                updateProgress();
            }
<<<<<<< HEAD
        }
    });
});
</script>
@endpush
>>>>>>> dc320ee (Interacciones)
=======

            outcomeSelect.on('change', handleOutcomeChange);
            handleOutcomeChange();

            // Botones de fecha rápida
            $('.quick-date').on('click', function() {
                const daysToAdd = parseInt($(this).data('days')) || 0;
                const nextActionInput = $('#next_action_date');
                const targetDate = new Date();
                targetDate.setDate(targetDate.getDate() + daysToAdd);
                const year = targetDate.getFullYear();
                const month = String(targetDate.getMonth() + 1).padStart(2, '0');
                const day = String(targetDate.getDate()).padStart(2, '0');
                const hours = '09';
                const minutes = '00';
                const formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
                nextActionInput.val(formatted);
                updateProgress();
                saveDraftDebounced();
                toastr.info('Fecha establecida: ' + new Date(formatted).toLocaleString());
            });

            // Contador de caracteres para el campo de notas
            const $notes = $('#notes');
            const $notesCounter = $('#notes-counter');

            $notes.on('input', function() {
                const length = $(this).val().length;
                $notesCounter.text(`${length}/500 caracteres`);

                if (length > 500) {
                    $notesCounter.addClass('text-danger');
                } else {
                    $notesCounter.removeClass('text-danger');
                }
            });

            // Inicializar contador de caracteres
            $notes.trigger('input');

            // Funcionalidad de colapsar/expandir secciones
            $('#toggle-client-info').on('click', function() {
                const $content = $('#client-info-content');
                const $icon = $(this).find('i');

                $content.slideToggle();
                $icon.toggleClass('bi-chevron-down bi-chevron-up');
            });

            $('#refresh-history').on('click', function() {
                refreshClientHistory();
            });

            $('#toggle-history').on('click', function() {
                const $content = $('#history-content');
                const $icon = $(this).find('i');

                $content.slideToggle();
                $icon.toggleClass('bi-chevron-down bi-chevron-up');
            });

            // Mejora en la carga de imágenes de avatar
            function generateAvatar(name) {
                const colors = ['#6B9BD1', '#7FA9D3', '#93B7D5', '#A7C5D7', '#BBD3D9'];
                const initials = name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2);
                const colorIndex = name.charCodeAt(0) % colors.length;

                return {
                    initials: initials || '—',
                    color: colors[colorIndex]
                };
            }

            // Actualizar avatar con colores dinámicos
            $('#client_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                const avatarData = generateAvatar(selectedText);
                const $avatar = $('#info-avatar');

                $avatar.text(avatarData.initials);
                $avatar.css('background-color', avatarData.color);
            });

            // Mejora en la visualización de archivos adjuntos
            $('#attachment').on('change', function() {
                const file = this.files[0];
                
                if (file) {
                    // Mostrar información del archivo
                    toastr.info(`Archivo seleccionado: ${file.name}`);
                    
                    // Si es una imagen, mostrar vista previa
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            $('#image-preview').attr('src', e.target.result);
                            $('#image-preview-container').show();
                        };
                        
                        reader.onerror = function() {
                            toastr.error('Error al cargar la vista previa de la imagen');
                            $('#image-preview-container').hide();
                        };
                        
                        reader.readAsDataURL(file);
                    } else {
                        $('#image-preview-container').hide();
                    }
                }
            });

            // Función para validar URL
            function isValidUrl(string) {
                try {
                    new URL(string);
                    return true;
                } catch (_) {
                    return false;
                }
            }

            // Validación de URL en tiempo real
            $('#interaction_url').on('input', function() {
                const url = $(this).val();
                if (url && !isValidUrl(url)) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Atajos de teclado
            $(document).on('keydown', function(e) {
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    saveDraft();
                    toastr.success('Borrador guardado');
                }

                if (e.altKey) {
                    switch (e.key) {
                        case '1':
                            showTab('principal');
                            break;
                        case '2':
                            showTab('adicional');
                            break;
                        case '3':
                            showTab('resultado');
                            break;
                        case '4':
                            showTab('adjuntos');
                            break;
                        case '5':
                            showTab('historial');
                            break;
                    }
                }
            });
        });
    </script>
<<<<<<< HEAD
@endpush
>>>>>>> a605699 (INTERACCIONES 001)
=======
</body>
</html>
>>>>>>> 0e82171 (INTERACCIONES 003)
