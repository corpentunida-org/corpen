@php
    // Detectar si estamos en modo edición o creación de interacción
    // Esta variable controla el comportamiento del formulario y el título de la página
    $modoEdicion = isset($interaction) && $interaction->id;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Título dinámico según el modo (edición o creación) -->
    <title>{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</title>
    
    <!-- Librerías externas: Iconos Bootstrap, Select2 para selects mejorados, y Toastr para notificaciones -->
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
        /* Sombras suavizadas para mejor profundidad */
        --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.04);
        --shadow-md: 0 8px 16px rgba(0, 0, 0, 0.06);
        --border-radius: 12px; /* Ligeramente más redondeado para aspecto moderno */
        --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); /* Transición más natural */
    }

    /* ===== ESTILOS GENERALES ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        /* Mejora la renderización de fuentes */
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body {
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        background-color: var(--background-color);
        color: var(--text-primary);
        line-height: 1.6;
    }

    /* SOLICITUD: ANCHO COMPLETO */
    .container-fluid {
        width: 100%;
        max-width: 100%; /* Elimina la restricción de 1200px */
        margin: 0;
        padding: 0 20px; /* Padding lateral de seguridad para que no pegue al borde */
    }

    /* ===== TARJETA PRINCIPAL ===== */
    .card {
        width: 100%;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
        background-color: var(--card-background);
        transition: var(--transition);
        margin-bottom: 2rem; /* Espacio inferior asegurado */
    }

    /* Header más limpio */
    .card-header {
        background-color: var(--primary-light);
        color: var(--text-primary);
        padding: 1.75rem 2rem; /* Más aire */
        border-bottom: 1px solid var(--border-color);
    }

    .card-body {
        padding: 0;
    }

    /* ===== BARRA DE PROGRESO ===== */
    .progress-section {
        background-color: #ffffff; /* Fondo blanco para mejor contraste */
        padding: 24px 32px;
        border-bottom: 1px solid var(--border-color);
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .progress-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .progress-percentage {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary-color);
        background: var(--primary-light);
        padding: 2px 8px;
        border-radius: 12px;
    }

    .progress-bar-container {
        height: 8px; /* Un poco más visible */
        background: #EDF2F7;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 8px;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--primary-color);
        border-radius: 4px;
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .progress-message {
        font-size: 0.8rem;
        color: var(--text-secondary);
        text-align: right;
    }

    /* ===== NAVEGACIÓN POR PESTAÑAS (UX MEJORADO) ===== */
    .tab-navigation {
        display: flex;
        background-color: #fff;
        border-bottom: 1px solid var(--border-color);
        overflow-x: auto; /* Permite scroll horizontal en móviles pequeños */
        scrollbar-width: none; /* Oculta scrollbar en Firefox */
    }
    
    .tab-navigation::-webkit-scrollbar {
        display: none; /* Oculta scrollbar en Chrome/Safari */
    }

    .tab-button {
        flex: 1;
        min-width: 120px; /* Asegura ancho mínimo legible */
        padding: 20px 16px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary);
        transition: var(--transition);
        position: relative;
    }

    /* Efecto hover suave */
    .tab-button::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 0%;
        height: 3px;
        background-color: var(--primary-color);
        transition: width 0.3s ease;
        opacity: 0.5;
    }

    .tab-button:hover:not(.active)::after {
        width: 100%;
    }

    .tab-button.active {
        color: var(--primary-color);
        background: linear-gradient(to bottom, transparent 90%, var(--primary-light) 100%);
        border-bottom-color: var(--primary-color);
    }

    .tab-button i { font-size: 20px; margin-bottom: 2px; }
    .tab-button span { font-size: 0.85rem; font-weight: 600; letter-spacing: 0.3px; }

    /* ===== CONTENIDO DEL FORMULARIO ===== */
    .form-content { 
        padding: 2rem; 
    }
    
    .tab-panel { 
        display: none; 
        animation: slideIn 0.3s ease; /* Cambiado a slide sutil */
    }
    
    .tab-panel.active { 
        display: block; 
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== CATEGORÍAS ===== */
    .category-container {
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        overflow: hidden;
        background: var(--card-background);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }

    .category-container:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px); /* Elevación sutil */
    }

    .category-header {
        background-color: #fafbfc;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .category-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
    }

    .category-icon {
        width: 40px; /* Icono un poco más grande */
        height: 40px;
        background-color: var(--primary-light); /* Fondo suave en vez de sólido fuerte */
        color: var(--primary-color); /* Icono con color primario */
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 18px;
        transition: var(--transition);
    }
    
    /* Inversión de color al hover de la categoría */
    .category-container:hover .category-icon {
        background-color: var(--primary-color);
        color: white;
    }

    .category-content { 
        padding: 2rem; 
    }

    /* ===== FORMULARIOS (UX CRÍTICO) ===== */
    .form-group {
        margin-bottom: 1.75rem; /* Más espacio entre campos */
        position: relative;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.6rem;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-primary);
    }
    
    /* Campos de entrada mejorados */
    .form-control, .form-select {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 0.8rem 1rem; /* Padding más cómodo */
        transition: var(--transition);
        font-size: 16px; /* CRÍTICO: 16px previene zoom en iOS */
        width: 100%;
        background-color: var(--card-background);
        color: var(--text-primary);
        min-height: 48px; /* Altura mínima táctil accesible */
    }

    .form-control::placeholder {
        color: #9CA3AF;
    }

    /* Estado Focus ultra claro */
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(107, 155, 209, 0.15); /* Anillo de foco accesible */
        outline: none;
    }
    
    /* Hover sobre inputs */
    .form-control:hover, .form-select:hover {
        border-color: #b0c4de;
    }

    /* ===== BOTONES (INTERACCIÓN MEJORADA) ===== */
    .btn-primary {
        background-color: var(--primary-color);
        border: none; 
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem; /* Botones más grandes */
        border-radius: 8px;
        transition: var(--transition);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 2px 4px rgba(107, 155, 209, 0.3);
    }
    
    .btn-primary:hover {
        background-color: #5A8AC1;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(107, 155, 209, 0.4);
    }
    
    .btn-primary:active {
        transform: translateY(0); /* Efecto click */
        box-shadow: 0 1px 2px rgba(107, 155, 209, 0.3);
    }

    .btn-light {
        background-color: #fff;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        transition: var(--transition);
        cursor: pointer;
    }
    
    .btn-light:hover {
        background-color: var(--input-background);
        border-color: #d1d5db;
        color: #111827;
    }

    /* Estilos Select2 Integrados */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 48px !important; /* Igualar inputs */
        border: 1px solid var(--border-color) !important;
        border-radius: 8px !important;
        padding-top: 8px !important;
    }

    /* ===== VALIDACIÓN VISUAL ===== */
    .required-field::after {
        content: "*";
        color: #EF4444;
        margin-left: 4px;
        font-weight: bold;
    }
    
    .invalid-feedback {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 6px;
        font-size: 0.85rem;
    }

    /* ===== ACCIONES DEL FORMULARIO ===== */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 3rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
        background: #fff; /* Asegura legibilidad sobre fondo */
    }

    .action-buttons {
        display: flex;
        gap: 12px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 10px; /* Menos padding lateral en móvil */
        }
        
        .card-header {
            padding: 1.25rem;
        }

        .form-content, .category-content { 
            padding: 1.25rem; 
        }

        /* Pestañas estilo scroll horizontal en móvil */
        .tab-button span { 
            display: block; /* Mostrar texto si es posible */
            font-size: 0.75rem;
        }
        
        .tab-button { 
            min-width: auto;
            flex: 0 0 auto; /* No encoger */
            padding: 12px 16px;
        }

        .form-actions {
            flex-direction: column-reverse; /* Guardar arriba en móvil es más fácil */
            gap: 1.5rem;
        }

        .action-buttons, .form-actions > button {
            width: 100%;
        }
        
        .action-buttons .btn {
            flex: 1;
        }
    }
    
    /* Utilidades de espaciado mejoradas */
    .mb-3 { margin-bottom: 1.25rem !important; }
    .mb-4 { margin-bottom: 2rem !important; }
    
    /* Clases helpers */
    .d-none { display: none !important; }
    .w-100 { width: 100% !important; }
</style>
</head>
<body>
    <div class="container-fluid">
        <!-- Tarjeta principal que contiene todo el formulario -->
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            <!-- Header del formulario con título dinámico según modo -->
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="icon-box bg-white bg-opacity-20 rounded-3 p-2 me-3">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <!-- Título dinámico según si estamos en modo edición o creación -->
                        <h4 class="mb-1 fw-bold">{{ $modoEdicion ? 'Editar Interacción' : 'Registro de Seguimiento Diario' }}</h4>
                        <p class="mb-0 opacity-75 small">
                            {{ $modoEdicion ? 'Modifica la información de la interacción existente' : 'Completa el formulario para registrar una nueva interacción' }}
                        </p>
                    </div>
                    <!-- En modo edición, mostrar botón para ver detalles de la interacción -->
                    @if ($modoEdicion && $interaction->id)
                        <div class="flex-shrink-0">
                            <a href="{{ route('interactions.show', $interaction->id) }}" class="btn btn-light btn-sm">
                                <i class="bi bi-eye me-1"></i> Ver Detalles
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barra de progreso que muestra el estado de completitud del formulario -->
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

            <!-- Navegación por pestañas para organizar el formulario en secciones -->
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

            <!-- Contenedor principal del formulario -->
            <div class="form-content">
                <form id="interaction-form"
                    action="{{ $modoEdicion ? route('interactions.update', $interaction->id) : route('interactions.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if ($modoEdicion)
                        @method('PUT')
                    @endif

                    <!-- PESTAÑA 1 -->
                    <!-- INICIO -->
                    <!-- INFORMACIÓN PRINCIPAL -->
                        <div class="tab-panel active" id="principal-tab">

                            <style>
                                /* TARJETAS VISUALES (SIN LISTAS) */
                                .visual-card {
                                    position: relative;
                                    background: #fff;
                                    border: 2px solid #eaecf0;
                                    border-radius: 12px;
                                    padding: 1rem;
                                    cursor: pointer;
                                    transition: all 0.2s ease-in-out;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                    height: 100%;
                                    min-height: 110px;
                                }
                                .visual-card:hover {
                                    border-color: #b0c4de;
                                    background-color: #f8fbff;
                                    transform: translateY(-3px);
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                                }
                                .btn-check:checked + .visual-card {
                                    border-color: var(--primary-color);
                                    background-color: #eff6ff;
                                    box-shadow: 0 0 0 2px rgba(107, 155, 209, 0.3);
                                }
                                .btn-check:checked + .visual-card .card-icon {
                                    color: var(--primary-color);
                                    transform: scale(1.1);
                                }
                                .btn-check:checked + .visual-card .card-title {
                                    color: var(--primary-color);
                                    font-weight: 700;
                                }
                                
                                /* ICONO CHECK */
                                .check-badge {
                                    position: absolute;
                                    top: 10px;
                                    right: 10px;
                                    color: var(--primary-color);
                                    opacity: 0;
                                    transform: scale(0);
                                    transition: all 0.2s ease;
                                }
                                .btn-check:checked + .visual-card .check-badge { opacity: 1; transform: scale(1); }

                                /* CHIPS (TIPIFICACIÓN) */
                                .smart-tag {
                                    display: inline-flex;
                                    align-items: center;
                                    padding: 10px 20px;
                                    background: #fff;
                                    border: 1px solid #d0d5dd;
                                    border-radius: 50px;
                                    color: #475467;
                                    cursor: pointer;
                                    transition: all 0.2s ease;
                                    font-weight: 500;
                                    user-select: none;
                                }
                                .smart-tag:hover { background-color: #f9fafb; border-color: #98a2b3; }
                                .btn-check:checked + .smart-tag {
                                    background-color: var(--primary-color);
                                    color: white;
                                    border-color: var(--primary-color);
                                    padding-left: 14px;
                                    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
                                }
                                .tag-icon { width: 0; overflow: hidden; opacity: 0; transition: all 0.2s ease; margin-right: 0; }
                                .btn-check:checked + .smart-tag .tag-icon { width: 20px; opacity: 1; margin-right: 8px; }

                                /* GRIDS */
                                .grid-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; }
                                .card-icon { font-size: 2rem; color: #98a2b3; margin-bottom: 0.5rem; transition: color 0.2s ease; }

                                /* ANIMACIÓN RELOJ */
                                .live-indicator {
                                    width: 8px; height: 8px; background-color: #10B981; border-radius: 50%; display: inline-block; margin-right: 6px;
                                    animation: pulse-green 1.5s infinite;
                                }
                                @keyframes pulse-green {
                                    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
                                    70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
                                    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
                                }
                            </style>

                            <div class="category-container mb-4">
                                <div class="category-header pb-2">
                                    <h5 class="category-title"><i class="bi bi-telephone-inbound me-2 text-primary"></i>Origen del Contacto</h5>
                                </div>
                                <div class="category-content pt-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-3">¿Quién inicia la interacción?</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="caller_type" id="caller_client" value="client" checked>
                                            <label class="visual-card flex-row justify-content-start p-3 gap-3" for="caller_client" style="min-height: auto;">
                                                <i class="bi bi-person-circle card-icon mb-0 fs-3"></i>
                                                <div class="text-start">
                                                    <div class="card-title fw-bold">Es el Titular</div>
                                                    <small class="text-muted">Cliente registrado</small>
                                                </div>
                                                <i class="bi bi-check-circle-fill check-badge fs-5" style="top: 50%; transform: translateY(-50%);"></i>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="caller_type" id="caller_third" value="third_party">
                                            <label class="visual-card flex-row justify-content-start p-3 gap-3" for="caller_third" style="min-height: auto;">
                                                <i class="bi bi-people-fill card-icon mb-0 fs-3"></i>
                                                <div class="text-start">
                                                    <div class="card-title fw-bold">Es un Tercero</div>
                                                    <small class="text-muted">Familiar o autorizado</small>
                                                </div>
                                                <i class="bi bi-check-circle-fill check-badge fs-5" style="top: 50%; transform: translateY(-50%);"></i>
                                            </label>
                                        </div>
                                    </div>

                                    <div id="third-party-fields" class="mt-3 p-4 bg-light rounded-3 border border-dashed" style="display: none;">
                                        <h6 class="text-primary fw-bold small text-uppercase mb-3"><i class="bi bi-pencil-square me-2"></i>Datos de quien llama</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Nombre Completo</label>
                                                <input type="text" class="form-control" id="nombre_quien_llama" name="nombre_quien_llama">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Identificación</label>
                                                <input type="text" class="form-control" id="cedula_quien_llama" name="cedula_quien_llama">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Teléfono</label>
                                                <input type="text" class="form-control" id="celular_quien_llama" name="celular_quien_llama">
                                            </div>
                                            <div class="col-12 pt-2">
                                                <label class="form-label small fw-bold mb-2">Relación con el titular:</label>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach(['familiar'=>'Familiar', 'amigo'=>'Amigo', 'representante'=>'Representante', 'otro'=>'Otro'] as $v => $l)
                                                        <input type="radio" class="btn-check" name="parentezco_quien_llama" id="rel_{{$v}}" value="{{$v}}">
                                                        <label class="smart-tag py-1 px-3 small" style="font-size: 0.8rem;" for="rel_{{$v}}">{{$l}}</label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="category-container mb-4">
                                <div class="category-content">
                                    <div class="row align-items-end g-3">
                                        <div class="col-md-8">
                                            <label for="client_id" class="form-label required-field text-uppercase small fw-bold text-muted">Buscar Cliente</label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                                <select class="form-select border-start-0 select2" id="client_id" name="client_id" required>
                                                    <option value="">Buscar por nombre, documento o código...</option>
                                                    @if ($modoEdicion && $interaction->client_id)
                                                        <option value="{{ $interaction->client_id }}" selected>{{ $interaction->client->nom_ter }}</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="bg-white border rounded p-2 d-flex align-items-center justify-content-between shadow-sm">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-2"><i class="bi bi-headset"></i></div>
                                                    <div class="lh-1">
                                                        <div class="small text-muted fw-bold" style="font-size: 0.7rem;">AGENTE</div>
                                                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <span class="live-indicator"></span>
                                                        <div class="fw-bold text-primary font-monospace fs-5" id="timer">00:00</div>
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.7rem;">DURACIÓN</small>
                                                </div>
                                            </div>
                                            <input type="hidden" name="agent_id" value="{{ auth()->user()->id }}">
                                            <input type="hidden" name="interaction_date" value="{{ now()->toDateTimeString() }}">
                                            <input type="hidden" name="duration" id="duration" value="0"> </div>
                                    </div>

                                    <div id="client-info-card" class="mt-4" style="display:none;">
                                        <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 5px solid var(--primary-color) !important;">
                                            <div class="card-body p-4">
                                                <div class="row" id="client-info-content">
                                                    <div class="col-md-5 border-end">
                                                        <div class="d-flex align-items-center mb-4">
                                                            <div class="client-avatar me-3 shadow-sm" id="info-avatar" 
                                                                style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg, var(--primary-color), #818cf8);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:1.5rem;">-</div>
                                                            <div>
                                                                <h5 class="fw-bold text-dark mb-0" id="info-nombre">Nombre Cliente</h5>
                                                                <div class="text-muted small">ID: <span id="info-id" class="fw-medium text-dark">-</span></div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 mb-3">
                                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-tag me-1"></i> <span id="info-categoria">Cat</span></span>
                                                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill"><i class="bi bi-geo-alt me-1"></i> <span id="info-distrito">Zona</span></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-7 ps-md-4">
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <label class="small text-muted fw-bold d-block">Correo</label>
                                                                <span class="text-dark d-flex align-items-center"><i class="bi bi-envelope text-primary me-2"></i> <span id="info-email" class="text-truncate">...</span></span>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="small text-muted fw-bold d-block">Teléfono</label>
                                                                <span class="text-dark d-flex align-items-center"><i class="bi bi-telephone text-primary me-2"></i> <span id="info-telefono">...</span></span>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="small text-muted fw-bold d-block">Dirección</label>
                                                                <span class="text-dark d-flex align-items-center"><i class="bi bi-geo text-primary me-2"></i> <span id="info-direccion">...</span></span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                                            <a id="btn-editar-cliente" href="#" target="_blank" class="btn btn-sm btn-light border fw-medium"><i class="bi bi-pencil me-1"></i> Editar</a>
                                                            <a id="btn-ver-cliente" href="#" target="_blank" class="btn btn-sm btn-outline-primary fw-medium"><i class="bi bi-eye me-1"></i> Ver Ficha</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="category-container mb-4">
                                <div class="category-header pb-2">
                                    <h5 class="category-title"><i class="bi bi-ui-checks-grid me-2 text-primary"></i>Parametrización</h5>
                                </div>
                                <div class="category-content pt-4">
                                    
                                    <div class="mb-5">
                                        <label class="form-label required-field text-uppercase small fw-bold text-muted mb-3 d-block">1. Canal de Entrada</label>
                                        <div class="grid-gallery">
                                            @foreach ($channels as $channel)
                                                <div class="position-relative">
                                                    <input type="radio" class="btn-check" name="interaction_channel" id="ch_{{ $channel->id }}" value="{{ $channel->id }}" 
                                                        {{ old('interaction_channel', $interaction->interaction_channel ?? '') == $channel->id ? 'checked' : '' }} required>
                                                    
                                                    <label class="visual-card p-2" for="ch_{{ $channel->id }}">
                                                        <i class="bi bi-check-circle-fill check-badge"></i>
                                                        <div class="card-icon">
                                                            @if(stripos($channel->name, 'tel') !== false) <i class="bi bi-telephone"></i>
                                                            @elseif(stripos($channel->name, 'mail') !== false || stripos($channel->name, 'correo') !== false) <i class="bi bi-envelope"></i>
                                                            @elseif(stripos($channel->name, 'what') !== false) <i class="bi bi-whatsapp"></i>
                                                            @elseif(stripos($channel->name, 'chat') !== false) <i class="bi bi-chat-dots"></i>
                                                            @elseif(stripos($channel->name, 'pres') !== false) <i class="bi bi-shop"></i>
                                                            @else <i class="bi bi-broadcast"></i>
                                                            @endif
                                                        </div>
                                                        <span class="card-title fw-semibold small text-center lh-sm">{{ $channel->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('interaction_channel') <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label required-field text-uppercase small fw-bold text-muted mb-3 d-block">2. Motivo / Tipificación</label>
                                        <div class="p-4 bg-light rounded-3 border">
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach ($types as $type)
                                                    <input type="radio" class="btn-check" name="interaction_type" id="tp_{{ $type->id }}" value="{{ $type->id }}"
                                                        {{ old('interaction_type', $interaction->interaction_type ?? '') == $type->id ? 'checked' : '' }} required>
                                                    
                                                    <label class="smart-tag" for="tp_{{ $type->id }}">
                                                        <span class="tag-icon"><i class="bi bi-check-lg"></i></span>
                                                        {{ $type->name }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('interaction_type') <div class="text-danger small mt-2 fw-bold">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label required-field text-uppercase small fw-bold text-muted mb-2">3. Notas Finales</label>
                                            <textarea class="form-control" name="notes" rows="3" placeholder="Resume los puntos clave..." 
                                                style="border-left: 4px solid var(--primary-color);" required>{{ old('notes', $interaction->notes ?? '') }}</textarea>
                                            @error('notes') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions sticky-bottom bg-white border-top pt-3 pb-2">
                                <div class="text-end w-100">
                                    <button type="button" class="btn btn-primary px-5 rounded-pill shadow fw-bold" onclick="showTab('adicional')">
                                        Siguiente <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const clientSelect = document.getElementById('client_id');
                                const thirdPartyFields = document.getElementById('third-party-fields');
                                const clientCard = document.getElementById('client-info-card');
                                const timerDisplay = document.getElementById('timer');
                                const durationInput = document.getElementById('duration');
                                
                                // --- LÓGICA DEL CRONÓMETRO ---
                                let seconds = 0;
                                function startTimer() {
                                    setInterval(function() {
                                        seconds++;
                                        const mins = Math.floor(seconds / 60);
                                        const secs = seconds % 60;
                                        // Formato 00:00
                                        timerDisplay.textContent = 
                                            (mins < 10 ? "0" + mins : mins) + ":" + 
                                            (secs < 10 ? "0" + secs : secs);
                                        
                                        // Actualizar input oculto para backend
                                        if(durationInput) durationInput.value = seconds;
                                    }, 1000);
                                }
                                startTimer(); // Iniciar inmediatamente

                                // --- FETCH DATOS ---
                                async function fetchClientDetails(clientId) {
                                    if (!clientId) return null;
                                    try {
                                        const response = await fetch(`/clientes/${clientId}/detalles`);
                                        return response.ok ? await response.json() : null;
                                    } catch (e) { return null; }
                                }

                                // --- MANEJO QUIEN LLAMA ---
                                function handleCallerChange() {
                                    const isThird = document.getElementById('caller_third').checked;
                                    thirdPartyFields.style.display = isThird ? 'block' : 'none';
                                    
                                    if (!isThird && clientSelect.value) {
                                        fetchClientDetails(clientSelect.value).then(data => {
                                            if(data) {
                                                document.getElementById('nombre_quien_llama').value = data.nom_ter || '';
                                                document.getElementById('cedula_quien_llama').value = clientSelect.value;
                                                document.getElementById('celular_quien_llama').value = data.tel || '';
                                                const rep = document.getElementById('rel_representante');
                                                if(rep) rep.checked = true;
                                            }
                                        });
                                    } else if (isThird) {
                                        document.getElementById('nombre_quien_llama').value = '';
                                        document.getElementById('cedula_quien_llama').value = '';
                                        document.getElementById('celular_quien_llama').value = '';
                                    }
                                }

                                document.querySelectorAll('input[name="caller_type"]').forEach(btn => btn.addEventListener('change', handleCallerChange));

                                // --- MANEJO CLIENTE ---
                                clientSelect.addEventListener('change', async function() {
                                    const clientId = this.value;
                                    if(clientId) {
                                        const data = await fetchClientDetails(clientId);
                                        if(data) {
                                            document.getElementById('info-nombre').textContent = data.nom_ter || '--';
                                            document.getElementById('info-id').textContent = clientId;
                                            document.getElementById('info-categoria').textContent = data.categoria || 'General';
                                            document.getElementById('info-distrito').textContent = data.distrito || 'Zona';
                                            document.getElementById('info-email').textContent = data.email || 'No email';
                                            document.getElementById('info-telefono').textContent = data.tel || 'No tel';
                                            document.getElementById('info-direccion').textContent = data.dir || 'No dir';
                                            
                                            document.getElementById('info-avatar').textContent = (data.nom_ter || 'C').charAt(0);
                                            
                                            const btnVer = document.getElementById('btn-ver-cliente');
                                            const btnEditar = document.getElementById('btn-editar-cliente');
                                            if(btnVer) btnVer.href = `/clientes/${clientId}`;
                                            if(btnEditar) btnEditar.href = `/clientes/${clientId}/editar`;

                                            clientCard.style.display = 'block';
                                            handleCallerChange();
                                        }
                                    } else {
                                        clientCard.style.display = 'none';
                                    }
                                });
                                
                                if(clientSelect.value) { clientSelect.dispatchEvent(new Event('change')); }
                            });
                            </script>
                        </div>
                    <!-- FINAL -->

                    <!-- PESTAÑA 2 -->
                    <!-- INICIO -->
                    <!-- INFORMACIÓN ADICIONAL -->
                        <div class="tab-panel" id="adicional-tab" 
                            data-agent-area-id="{{ $idAreaAgente ?? '' }}" 
                            data-agent-cargo-id="{{ $idCargoAgente ?? '' }}">

                            <style>
                                /* 1. Toggle Gigante de Responsabilidad */
                                .segment-control {
                                    display: flex;
                                    background: #f1f5f9;
                                    padding: 6px;
                                    border-radius: 12px;
                                    position: relative;
                                    user-select: none;
                                }
                                .segment-option {
                                    flex: 1;
                                    text-align: center;
                                    position: relative;
                                    z-index: 1;
                                }
                                .segment-input {
                                    display: none;
                                }
                                .segment-label {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    padding: 12px;
                                    cursor: pointer;
                                    border-radius: 8px;
                                    transition: all 0.3s ease;
                                    font-weight: 600;
                                    color: #64748b;
                                    gap: 8px;
                                }
                                .segment-input:checked + .segment-label {
                                    background: #fff;
                                    color: var(--primary-color);
                                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                                    transform: scale(1.02);
                                }

                                /* 2. Tarjeta de Credencial (Aparece cuando es "Yo") */
                                .agent-id-card {
                                    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                                    border: 1px solid #e2e8f0;
                                    border-left: 5px solid var(--primary-color);
                                    border-radius: 10px;
                                    padding: 20px;
                                    display: flex;
                                    align-items: center;
                                    gap: 15px;
                                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
                                    animation: slideInUp 0.3s ease-out;
                                }
                                @keyframes slideInUp {
                                    from { opacity: 0; transform: translateY(10px); }
                                    to { opacity: 1; transform: translateY(0); }
                                }

                                /* 3. Panel de Delegación (Aparece cuando es "Otro") */
                                .delegation-panel {
                                    background: #fff;
                                    border: 1px dashed #cbd5e1;
                                    border-radius: 10px;
                                    padding: 20px;
                                    animation: fadeIn 0.3s ease-out;
                                }

                                /* 4. Sync Control para Distrito */
                                .sync-wrapper {
                                    display: flex;
                                    align-items: stretch;
                                    border: 1px solid #ced4da;
                                    border-radius: 8px;
                                    overflow: hidden;
                                    transition: all 0.2s;
                                }
                                .sync-wrapper:focus-within {
                                    border-color: var(--primary-color);
                                    box-shadow: 0 0 0 3px rgba(107, 155, 209, 0.15);
                                }
                                .sync-select-container {
                                    flex-grow: 1;
                                }
                                .sync-select-container .select2-container--bootstrap-5 .select2-selection {
                                    border: none !important;
                                    border-radius: 0 !important;
                                    box-shadow: none !important;
                                }
                                .sync-btn {
                                    border: none;
                                    background: #f8f9fa;
                                    border-left: 1px solid #dee2e6;
                                    padding: 0 15px;
                                    color: #6c757d;
                                    transition: all 0.2s;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                }
                                .sync-btn:hover {
                                    background: #e9ecef;
                                    color: var(--primary-color);
                                }
                                .sync-btn-upload:hover {
                                    color: #10b981; /* Verde al subir */
                                }
                            </style>

                            <div class="category-container">
                                <div class="category-header pb-3" style="background: linear-gradient(to right, #f8f9fa, #fff);">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="category-title mb-1 text-primary"><i class="bi bi-briefcase-fill me-2"></i>Asignación y Contexto</h5>
                                            <p class="category-description mb-0 text-muted" style="margin-left: 0;">Define quién se hace cargo y dónde ocurre.</p>
                                        </div>
                                        <div class="text-end d-none d-md-block">
                                            <span class="badge bg-light text-secondary border">Paso 2 de 3</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="category-content pt-4 px-4 pb-4">
                                    
                                    <div class="mb-4">
                                        <label class="form-label text-uppercase small fw-bold text-muted mb-3 d-block text-center">¿Quién es el responsable?</label>
                                        
                                        <div class="segment-control shadow-sm">
                                            <div class="segment-option">
                                                <input type="radio" name="handled_by_agent" id="handled_by_me" value="yes" class="segment-input" checked>
                                                <label for="handled_by_me" class="segment-label">
                                                    <i class="bi bi-person-check-fill fs-5"></i>
                                                    <span>Yo me encargo</span>
                                                </label>
                                            </div>
                                            <div class="segment-option">
                                                <input type="radio" name="handled_by_agent" id="handled_by_other" value="no" class="segment-input">
                                                <label for="handled_by_other" class="segment-label">
                                                    <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                                                    <span>Delegar a otro</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div id="panel-me">
                                            <div class="agent-id-card">
                                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary">
                                                    <i class="bi bi-shield-check fs-2"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold text-dark mb-1">Asignación Automática</h6>
                                                    <div class="text-muted small">La interacción quedará registrada bajo tus credenciales:</div>
                                                    <div class="d-flex gap-3 mt-2">
                                                        <span class="badge bg-white text-secondary border px-3 py-2">
                                                            <i class="bi bi-building me-1"></i> {{ $idAreaAgente ? $areas[$idAreaAgente] : 'Sin Área' }}
                                                        </span>
                                                        <span class="badge bg-white text-secondary border px-3 py-2">
                                                            <i class="bi bi-person-badge me-1"></i> {{ $idCargoAgente ? $cargos[$idCargoAgente] : 'Sin Cargo' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <i class="bi bi-check-lg text-success fs-1"></i>
                                            </div>
                                        </div>

                                        <div id="panel-other" class="delegation-panel" style="display: none;">
                                            <h6 class="text-secondary small fw-bold text-uppercase mb-3"><i class="bi bi-diagram-3 me-2"></i>Selecciona el Destino</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="id_area_de_asignacion" class="form-label small fw-bold">Área Responsable</label>
                                                    <select class="form-select select2" id="id_area_de_asignacion" name="id_area_de_asignacion">
                                                        <option value="">Buscar área...</option>
                                                        @if (isset($areas))
                                                            @foreach ($areas as $id => $nombre)
                                                                <option value="{{ $id }}">{{ $nombre }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="id_cargo_asignacion" class="form-label small fw-bold">Cargo Responsable</label>
                                                    <select class="form-select select2" id="id_cargo_asignacion" name="id_cargo_asignacion">
                                                        <option value="">Buscar cargo...</option>
                                                        @if (isset($cargos))
                                                            @foreach ($cargos as $id => $nombre)
                                                                <option value="{{ $id }}">{{ $nombre }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="text-muted opacity-10">

                                    <div class="row g-4 mt-2">
                                        <div class="col-md-6">
                                            <label for="id_linea_de_obligacion" class="form-label required-field text-uppercase small fw-bold text-muted">Línea de Obligación</label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-credit-card-2-front"></i></span>
                                                <select class="form-select border-start-0 select2" id="id_linea_de_obligacion" name="id_linea_de_obligacion">
                                                    <option value="">Selecciona la línea...</option>
                                                    @if (isset($lineasCredito))
                                                        @foreach ($lineasCredito as $id => $nombre)
                                                            <option value="{{ $id }}">{{ $nombre }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="id_distrito_interaccion" class="form-label text-uppercase small fw-bold text-muted">Distrito / Zona</label>
                                            
                                            <div class="sync-wrapper shadow-sm">
                                                <div class="sync-select-container">
                                                    <select class="form-select select2" id="id_distrito_interaccion" name="id_distrito_interaccion">
                                                        <option value="">Selecciona el distrito...</option>
                                                        @if (isset($distrito))
                                                            @foreach ($distrito as $id => $nombre)
                                                                <option value="{{ $id }}">{{ $nombre }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <button type="button" class="sync-btn" id="sync-from-client-btn" 
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Traer distrito del Cliente">
                                                    <i class="bi bi-cloud-download"></i>
                                                </button>
                                                <button type="button" class="sync-btn sync-btn-upload" id="sync-to-client-btn"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Guardar este distrito en el Cliente">
                                                    <i class="bi bi-cloud-upload"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end mt-1">
                                                <span class="badge bg-light text-muted border" style="font-size: 0.65rem;">SYNC TOOLS</span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="form-actions sticky-bottom bg-white border-top pt-3 pb-2 mt-4">
                                <div class="action-buttons w-100 d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill border-0 hover-shadow" onclick="showTab('principal')">
                                        <i class="bi bi-arrow-left me-2"></i> Volver
                                    </button>
                                    <button type="button" class="btn btn-primary px-5 rounded-pill shadow-lg fw-bold" onclick="showTab('resultado')">
                                        Siguiente Paso <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const infoTab = document.getElementById('adicional-tab');
                                const handledByMe = document.getElementById('handled_by_me');
                                const handledByOther = document.getElementById('handled_by_other');
                                
                                const panelMe = document.getElementById('panel-me');
                                const panelOther = document.getElementById('panel-other');
                                
                                const areaSelect = document.getElementById('id_area_de_asignacion');
                                const cargoSelect = document.getElementById('id_cargo_asignacion');

                                // Referencias para Sync
                                const clientSelect = document.getElementById('client_id');
                                const districtSelect = document.getElementById('id_distrito_interaccion');
                                const btnSyncFrom = document.getElementById('sync-from-client-btn');
                                const btnSyncTo = document.getElementById('sync-to-client-btn');

                                // Inicializar Tooltips de Bootstrap
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl)
                                })

                                // --- 1. LÓGICA DE VISUALIZACIÓN Y ASIGNACIÓN ---
                                function toggleResponsibility() {
                                    // Datos del agente (Dataset)
                                    const myArea = infoTab.dataset.agentAreaId;
                                    const myCargo = infoTab.dataset.agentCargoId;

                                    if (handledByMe.checked) {
                                        // Mostrar tarjeta Yo, Ocultar Selectores
                                        panelMe.style.display = 'block';
                                        panelOther.style.display = 'none';
                                        
                                        // Asignar valores por debajo (Silent Assignment)
                                        if(areaSelect) $(areaSelect).val(myArea).trigger('change');
                                        if(cargoSelect) $(cargoSelect).val(myCargo).trigger('change');
                                    } else {
                                        // Ocultar tarjeta Yo, Mostrar Selectores con Animación
                                        panelMe.style.display = 'none';
                                        panelOther.style.display = 'block';
                                        
                                        // Limpiar para obligar a seleccionar
                                        if(areaSelect) $(areaSelect).val('').trigger('change');
                                        if(cargoSelect) $(cargoSelect).val('').trigger('change');
                                    }
                                }

                                handledByMe.addEventListener('change', toggleResponsibility);
                                handledByOther.addEventListener('change', toggleResponsibility);
                                
                                // Ejecutar al inicio
                                toggleResponsibility();

                                // --- 2. LÓGICA DE SINCRONIZACIÓN (SYNC) ---
                                
                                // Descargar (Client -> Form)
                                if(btnSyncFrom) {
                                    btnSyncFrom.addEventListener('click', async () => {
                                        const clientId = clientSelect ? clientSelect.value : null;
                                        if(!clientId) return Swal.fire({icon:'warning', title:'¡Espera!', text:'Selecciona un cliente en la pestaña anterior.', confirmButtonColor: 'var(--primary-color)'});

                                        const originalIcon = btnSyncFrom.innerHTML;
                                        btnSyncFrom.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                                        
                                        try {
                                            const url = "{{ route('interactions.clientes.distrito', ':id') }}".replace(':id', clientId);
                                            const res = await fetch(url);
                                            if(res.ok) {
                                                const data = await res.json();
                                                if(data.district_id) {
                                                    $(districtSelect).val(data.district_id).trigger('change');
                                                    // Feedback visual sutil (Toast)
                                                    const toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
                                                    toast.fire({icon: 'success', title: 'Distrito cargado del cliente'});
                                                } else {
                                                    Swal.fire({icon:'info', text:'Este cliente no tiene distrito registrado.'});
                                                }
                                            }
                                        } catch(e) { console.error(e); }
                                        btnSyncFrom.innerHTML = originalIcon;
                                    });
                                }

                                // Subir (Form -> Client)
                                if(btnSyncTo) {
                                    btnSyncTo.addEventListener('click', async () => {
                                        const clientId = clientSelect ? clientSelect.value : null;
                                        const distId = districtSelect.value;

                                        if(!clientId) return Swal.fire({icon:'warning', text:'Falta seleccionar Cliente'});
                                        if(!distId) return Swal.fire({icon:'warning', text:'Selecciona un distrito para guardar'});

                                        const confirm = await Swal.fire({
                                            title: 'Actualizar Cliente',
                                            text: '¿Asignar este distrito a la ficha permanente del cliente?',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: 'var(--primary-color)',
                                            confirmButtonText: 'Sí, Actualizar'
                                        });

                                        if(confirm.isConfirmed) {
                                            const originalIcon = btnSyncTo.innerHTML;
                                            btnSyncTo.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                                            
                                            try {
                                                const url = `/interactions/clientes/${clientId}/actualizar-distrito`; // Asegúrate que esta ruta exista
                                                const res = await fetch(url, {
                                                    method: 'PUT',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                    },
                                                    body: JSON.stringify({ district_id: distId })
                                                });
                                                
                                                if(res.ok) {
                                                    Swal.fire({icon:'success', title:'Guardado', text:'Distrito del cliente actualizado.', timer:1500, showConfirmButton:false});
                                                }
                                            } catch(e) { 
                                                Swal.fire({icon:'error', title:'Error', text:'No se pudo actualizar.'});
                                            }
                                            btnSyncTo.innerHTML = originalIcon;
                                        }
                                    });
                                }
                            });
                            </script>
                        </div>
                    <!-- FINAL -->

                    <!-- PESTAÑA 3 INI: RESULTADO Y PLANIFICACIÓN -->
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
                                    <!-- Separador visual para la sección de resultado -->
                                    <div class="section-divider">
                                        <h6 class="section-title">
                                            <i class="bi bi-flag me-2 text-primary"></i>Resultado de la Interacción
                                        </h6>
                                    </div>
                                    
                                    <!-- Selector para el resultado de la interacción -->
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
                                    
                                    <!-- Separador visual para la sección de planificación -->
                                    <div class="section-divider">
                                        <h6 class="section-title">
                                            <i class="bi bi-calendar-check me-2 text-primary"></i>Planificación
                                        </h6>
                                    </div>
                                    
                                    <!-- Sección de planificación (inicialmente oculta, se muestra según el resultado) -->
                                    <div class="card border-0 bg-light mb-4" id="planning-section" style="display:none;">
                                        <div class="card-body p-3">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <!-- Campo para fecha y hora de la próxima acción -->
                                                        <label for="next_action_date" class="form-label">Próxima Acción</label>
                                                        <input type="datetime-local"
                                                            class="form-control @error('next_action_date') is-invalid @enderror"
                                                            id="next_action_date" name="next_action_date"
                                                            value="{{ old('next_action_date', $interaction->next_action_date ?? '') }}">
                                                        <!-- Botones para establecer fechas rápidas -->
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
                                                        <!-- Selector para el tipo de próxima acción -->
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
                                                        <!-- Campo para notas sobre la próxima acción -->
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
                            
                            <!-- Botones de navegación entre pestañas -->
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
                    <!-- PESTAÑA 3 FIN: RESULTADO Y PLANIFICACIÓN -->

                    <!-- PESTAÑA 4 INI: ADJUNTOS Y REFERENCIAS -->
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
                                    <!-- Campos para adjuntar archivos y agregar enlaces de referencia -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="attachment" class="form-label">Archivo Adjunto</label>
                                                <!-- Campo para subir archivo -->
                                                <input type="file"
                                                    class="form-control @error('attachment') is-invalid @enderror"
                                                    id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx">
                                                <div class="form-text">Puedes adjuntar un archivo (máx. 10MB)</div>
                                                
                                                <!-- En modo edición, mostrar archivo existente si hay -->
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
                                                <!-- Campo para agregar URL de referencia -->
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
                                    
                                    <!-- Vista previa de imagen (inicialmente oculta) -->
                                    <div id="image-preview-container" class="mt-3" style="display: none;">
                                        <div class="small text-muted fw-semibold mb-2">Vista previa:</div>
                                        <img id="image-preview" class="img-thumbnail file-preview" alt="Vista previa de la imagen">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de navegación entre pestañas -->
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
                    <!-- PESTAÑA 4 FIN: ADJUNTOS Y REFERENCIAS -->
  
                    <!-- PESTAÑA 5 -->
                    <!-- INICIO -->
                    <!-- HISTORIAL -->
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
                                    <!-- Mensaje cuando no hay cliente seleccionado -->
                                    <div id="no-client-selected" class="text-center py-5">
                                        <i class="bi bi-person-x display-1 text-muted"></i>
                                        <p class="mt-3 text-muted">Selecciona un cliente para ver su historial de interacciones</p>
                                    </div>
                                    
                                    <!-- Campo oculto para almacenar el ID de la interacción padre (para escalamiento) -->
                                    <input type="hidden" id="parent_interaction_id" name="parent_interaction_id" value="">
                                    
                                    <!-- Sección de historial (inicialmente oculta) -->
                                    <div class="card border-0 bg-light mb-4" id="history-section" style="display:none;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-3">
                                                <i class="bi bi-clock-history text-primary me-2"></i>
                                                <h6 class="mb-0 fw-semibold">Historial Reciente</h6>
                                                <div class="ms-auto">
                                                    <!-- Botón para actualizar el historial -->
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="refresh-history">
                                                        <i class="bi bi-arrow-clockwise"></i> Actualizar
                                                    </button>
                                                    <!-- Botón para expandir/contraer el historial -->
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="toggle-history">
                                                        <i class="bi bi-chevron-down"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Indicador de interacción padre seleccionada para escalamiento -->
                                            <div id="selected-parent-info" class="alert alert-info d-flex align-items-center" style="display:none;">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <div class="flex-grow-1">
                                                    <strong>Interacción padre seleccionada:</strong> 
                                                    <span id="selected-parent-text">Ninguna</span>
                                                </div>
                                                <!-- Botón para limpiar la selección de interacción padre -->
                                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-parent-selection">
                                                    <i class="bi bi-x-circle"></i> Limpiar
                                                </button>
                                            </div>
                                            
                                            <!-- Contenedor para la lista de interacciones del historial -->
                                            <div id="history-content">
                                                <div id="interaction-history-list" class="history-list" style="max-height:400px; overflow-y:auto;">
                                                    <!-- Los elementos del historial se cargan dinámicamente con JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de acción final del formulario -->
                            <div class="form-actions">
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-light" onclick="showTab('adjuntos')">
                                        <i class="bi bi-arrow-left"></i> Anterior
                                    </button>
                                </div>
                                <div class="action-buttons">
                                    <!-- Botón para limpiar borrador guardado -->
                                    <button id="clear-draft" type="button" class="btn btn-outline-secondary me-2">
                                        <i class="bi bi-trash me-1"></i> Borrar Borrador
                                    </button>
                                    <!-- Botón principal para enviar el formulario -->
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i>
                                        {{ $modoEdicion ? 'Actualizar Interacción' : 'Guardar Interacción' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    <!-- FINAL -->
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay para mostrar durante operaciones AJAX -->
    <div id="ajax-loader" class="ajax-loader" aria-hidden="true"
        style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div class="card text-center p-4">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mb-0">Cargando información del cliente...</p>
        </div>
    </div>

    <!-- Librerías JavaScript externas -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // =================================================================
            // INICIALIZACIÓN DE COMPONENTES
            // =================================================================
            
            // Inicialización de Select2 para todos los selects con clase select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: function() {
                    return $(this).attr('placeholder');
                },
                dropdownParent: $('body')
            });

            // Configuración especial para el campo de cliente con búsqueda AJAX
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

            // Variables para el cronómetro de duración de la interacción
            let startTimeInterval = null;
            let startTime = null;

            // =================================================================
            // FUNCIONES DE UTILIDAD
            // =================================================================
            
            // Función para mostrar el overlay de carga
            function showLoader() {
                $ajaxLoader.show();
            }

            // Función para ocultar el overlay de carga
            function hideLoader() {
                $ajaxLoader.hide();
            }

            // Configuración de notificaciones Toastr
            toastr.options = {
                "positionClass": "toast-bottom-right",
                "timeOut": "2500",
                "progressBar": true,
            };

            // =================================================================
            // LÓGICA DE NAVEGACIÓN POR PESTAÑAS
            // =================================================================
            
            // Event listener para los botones de navegación por pestañas
            $('.tab-button').on('click', function() {
                const tabId = $(this).data('tab');
                showTab(tabId);
                saveDraft();
            });

            // Función para mostrar una pestaña específica
            window.showTab = function(tabId) {
                // Actualizar botones de pestaña
                $('.tab-button').removeClass('active');
                $(`.tab-button[data-tab="${tabId}"]`).addClass('active');

                // Actualizar paneles de contenido
                $('.tab-panel').removeClass('active');
                $(`#${tabId}-tab`).addClass('active');

                // Si es la pestaña de historial, actualizar su visibilidad
                if (tabId === 'historial') {
                    updateHistoryTabVisibility();
                }

                // Actualizar barra de progreso
                updateProgress();
            }

            // =================================================================
            // LÓGICA DE PROGRESO DEL FORMULARIO
            // =================================================================
            
            // Función para actualizar la barra de progreso según campos completados
            function updateProgress() {
                const requiredFields = ['client_id', 'interaction_channel', 'interaction_type', 'notes', 'outcome'];
                let completed = 0;

                // Contar campos obligatorios completados
                requiredFields.forEach(field => {
                    const value = $(`[name="${field}"]`).val();
                    if (value && value.trim() !== '') {
                        completed++;
                    }
                });

                // Calcular porcentaje de progreso
                const progress = Math.round((completed / requiredFields.length) * 100);

                // Actualizar barra de progreso visualmente
                $('#progress-bar').css('width', progress + '%');
                $('#progress-percentage').text(progress + '%');

                // Actualizar mensaje según el progreso
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
            
            // Función para guardar el estado actual del formulario en localStorage
            function saveDraft() {
                try {
                    const data = {};
                    // Recopilar datos de todos los campos del formulario
                    $form.find('input,textarea,select').each(function() {
                        const name = this.name;
                        if (!name) return;
                        if (this.type === 'file') return; // No guardar archivos
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
                    // Guardar pestaña activa
                    data.activeTab = $('.tab-button.active').data('tab');
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    toastr.success('Borrador guardado automáticamente');
                } catch (e) {
                    console.warn('No se pudo guardar el borrador', e);
                }
            }

            // Función de debounce para evitar guardar demasiado frecuentemente
            function debounce(fn, wait) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            const saveDraftDebounced = debounce(saveDraft, 700);

            // Función para cargar un borrador guardado desde localStorage
            function loadDraft() {
                try {
                    const raw = localStorage.getItem(storageKey);
                    if (!raw) return;
                    const data = JSON.parse(raw);
                    
                    // Restaurar valores de campos
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

                    // Restaurar el estado del cronómetro si hay datos
                    if (data.start_time && data.client_id) {
                        $('#start_time').val(data.start_time);
                        if (data.duration) {
                            $('#duration-display').val(`${data.duration} segundos`);
                        }

                        startTime = new Date(data.start_time);

                        // Reiniciar el cronómetro
                        startTimeInterval = setInterval(() => {
                            const now = new Date();
                            const durationSeconds = Math.round((now - startTime) / 1000);
                            $('#duration-display').val(`${durationSeconds} segundos`);
                            $('#duration').val(durationSeconds);
                        }, 1000);
                    }

                    // Restaurar pestaña activa
                    if (data.activeTab) {
                        showTab(data.activeTab);
                    }

                    toastr.info('Borrador restaurado automáticamente');
                } catch (e) {
                    console.warn('No se pudo cargar el borrador', e);
                }
            }

            // Eventos para guardar borrador automáticamente
            $form.on('input change', 'input, textarea, select', function() {
                updateProgress();
                saveDraftDebounced();
            });

            // Cargar borrador al iniciar la página
            loadDraft();
            updateProgress();

            // Botón para limpiar borrador guardado
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
            
            // Función para validar un campo específico
            function validateField(el) {
                const $el = $(el);
                const name = $el.attr('name');

                // Si no es obligatorio, solo validar si tiene valor
                if (!$el.prop('required')) {
                    if ($el.val() && $el.val().toString().trim() !== '') {
                        $el.removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $el.removeClass('is-valid is-invalid');
                    }
                    return true;
                }

                // Validar que tenga valor
                const val = $el.val();
                if (!val || val.toString().trim() === '') {
                    $el.removeClass('is-valid').addClass('is-invalid');
                    return false;
                }

                // Validación específica para URLs
                if ($el.attr('type') === 'url' && val) {
                    try {
                        new URL(val);
                    } catch (e) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                // Validación específica para números
                if ($el.attr('type') === 'number' && val) {
                    if (isNaN(val) || parseFloat(val) < 0) {
                        $el.removeClass('is-valid').addClass('is-invalid');
                        return false;
                    }
                }

                $el.removeClass('is-invalid').addClass('is-valid');
                return true;
            }

            // Manejador de envío del formulario
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

                // Validar todos los campos obligatorios
                let valid = true;

                $form.find('select[required], textarea[required], input[required]').each(function() {
                    const ok = validateField(this);
                    if (!ok) valid = false;
                });

                if (!valid) {
                    // Enfocar el primer campo inválido
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

                // Confirmar envío del formulario
                Swal.fire({
                    title: 'Confirmar envío',
                    text: "¿Deseas enviar la interacción ahora?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Enviar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Limpiar borrador y enviar formulario
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
            
            // Función para manejar la selección de interacción padre para escalamiento
            function selectParentInteraction(interactionId, interactionInfo) {
                console.log('selectParentInteraction llamado con ID:', interactionId); // DEBUG
                // Actualizar el campo oculto con el ID de la interacción padre
                $('#parent_interaction_id').val(interactionId);
                // Agregar esta línea para verificar
                console.log('Valor de parent_interaction_id después de establecer:', $('#parent_interaction_id').val());

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
            
            // Event listener para cuando se selecciona un cliente
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
                    // Si no hay cliente seleccionado, ocultar información
                    clientCard.fadeOut();
                    historySection.fadeOut();
                    $durationDisplay.val('0 segundos');
                    $startTimeField.val('');
                    $('#duration').val(0);
                    updateProgress();
                    updateHistoryTabVisibility();
                    return;
                }

                // Registrar hora de inicio y empezar a contar duración
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

                // Mostrar indicador de carga
                showLoader();
                
                // Obtener información del cliente mediante AJAX
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
                    
                    // Actualizar avatar con iniciales del cliente
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

                    // Cargar historial de interacciones del cliente
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

                    // Mostrar tarjeta de información del cliente
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
            
            // Lógica para mostrar/ocultar sección de planificación según el resultado seleccionado
            const outcomeSelect = $('#outcome');
            const planningSection = $('#planning-section');

            function handleOutcomeChange() {
                const selectedOutcomeText = outcomeSelect.find("option:selected").text().trim();
                // Mostrar sección de planificación para resultados que requieren seguimiento
                if (selectedOutcomeText === 'Pendiente' || selectedOutcomeText === 'No contesta' ||
                    selectedOutcomeText.toLowerCase().includes('pendiente')) {
                    planningSection.slideDown();
                } else {
                    planningSection.slideUp();
                }
                updateProgress();
            }

            // Event listener para cambios en el selector de resultado
            outcomeSelect.on('change', handleOutcomeChange);
            handleOutcomeChange();

            // Botones de fecha rápida para la próxima acción
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

                // Cambiar color si excede el límite
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
            
            // Atajos de teclado para mejorar la usabilidad
            $(document).on('keydown', function(e) {
                // Ctrl+S para guardar borrador
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    saveDraft();
                    toastr.success('Borrador guardado');
                }

                // Alt+1-5 para navegar entre pestañas
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