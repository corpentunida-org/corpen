<x-base-layout>
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bolder mb-0 text-gray-800">
                <i class="feather-file-text text-primary me-2"></i> Detalle del Soporte
            </h4>
            <div class="fs-6 text-muted">ID de Ticket: #{{ $soporte->id }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('soportes.soportes.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="feather-arrow-left me-1"></i> Volver
            </a>
            {{--             
            <a href="{{ route('soportes.soportes.edit', $soporte->id) }}" class="btn btn-sm btn-primary">
                <i class="feather-edit-2 me-1"></i> Editar
            </a>
            <form action="{{ route('soportes.soportes.destroy', $soporte->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este soporte?');" class="mb-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="feather-trash-2 me-1"></i> Eliminar
                </button>
            </form> 
            --}}
        </div>
    </div>

    <div class="row">
        {{-- COLUMNA PRINCIPAL --}}
        <div class="col-lg-8">
            {{-- Descripción del soporte (Actualizado) --}}
            <div class="card shadow-sm mb-4 support-description-card"> {{-- Clase para estilos específicos --}}
                <div class="card-body">
                    <h5 class="card-title text-dark fw-bold mb-3 d-flex align-items-center">
                        <i class="feather-book-open text-primary me-2"></i> {{-- Ícono para realzar el título --}}
                        Descripción del Soporte
                        {{-- Botón de expandir/colapsar para textos largos --}}
                        <button class="btn btn-sm btn-outline-primary ms-auto d-none" id="toggleDescription" type="button" aria-expanded="false" aria-controls="descriptionContent">
                            <i class="feather-maximize-2 me-1 icon-expand"></i>
                            <i class="feather-minimize-2 me-1 icon-collapse d-none"></i>
                            <span class="text-button">Expandir</span>
                        </button>
                    </h5>
                    <div id="descriptionContent" class="description-content-wrapper"> {{-- Nuevo contenedor para el contenido --}}
                        <p class="text-gray-700 bg-light-subtle p-3 rounded border support-description-text" style="white-space: pre-wrap;">
                            {{ $soporte->detalles_soporte }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Historial de observaciones --}}
                <div class="card shadow-sm history-tracking-card">
                    <div class="card-header border-0">

                    <h5 class="card-title text-dark fw-bold mb-0">
                        <i class="feather-message-square me-2 text-primary"></i> Historial y Seguimiento
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($soporte->observaciones as $obs)
                        <div class="d-flex mb-4">
                            <div class="me-3 text-center">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="feather-git-commit"></i>
                                </div>
                                @if(!$loop->last)
                                    <div class="border-start" style="height: 100%; margin-left: 19px;"></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="fw-bold text-dark mb-0">
                                        {{ $obs->tipoObservacion->nombre ?? 'Actualización' }}
                                    </p>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($obs->timestam)->diffForHumans() }}</small>
                                </div>
                                <p class="text-gray-700 mt-1 mb-2">{{ $obs->observacion }}</p>
                                <div class="d-flex align-items-center text-muted fs-7">
                                    {{-- Usuario que creó la observación --}}
                                    <span><i class="feather-user me-1"></i>{{ $obs->usuario->name ?? 'N/A' }}</span>

                                    {{-- Usuario asignado/escalado de esta observación --}}
                                    @if($obs->scpUsuarioAsignado && $obs->scpUsuarioAsignado->maeTercero)
                                        <span class="mx-2">|</span>
                                        <span>
                                            <i class="feather-user-plus me-1"></i>
                                            Asignado a: <strong>{{ $obs->scpUsuarioAsignado->maeTercero->nom_ter }}</strong>
                                        </span>
                                    @endif

                                    {{-- Estado de la observación --}}
                                    <span class="mx-2">|</span>
                                    <span>
                                        <i class="feather-activity me-1"></i>
                                        Cambió estado a: <strong class="text-dark">{{ $obs->estado->nombre ?? 'N/A' }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light text-center">
                            <i class="feather-info me-1"></i> Aún no hay seguimientos registrados.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Formulario de observación --}}
            <div class="accordion mt-4" id="accordionForm">
                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
                            <i class="feather-plus-circle me-2"></i> Agregar Seguimiento o Escalamiento
                        </button>
                    </h2>
                    <div id="collapseForm" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionForm">
                        <div class="accordion-body">
                            <form action="{{ route('soportes.soportes.observaciones.store', $soporte->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="observacion" class="form-label fw-semibold">Nueva Observación:</label>
                                    <textarea id="observacion" name="observacion" class="form-control" rows="3" required placeholder="Escribe los detalles de la actualización..."></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label fw-semibold">Actualizar Estado:</label>
                                        <select id="estado" name="id_scp_estados" class="form-select" required>
                                            <option value="" disabled selected>Seleccione un estado...</option>
                                            @foreach($estados as $estado)
                                                <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="tipoObservacion" class="form-label fw-semibold">Tipo de Observación:</label>
                                        <select id="tipoObservacion" name="id_tipo_observacion" class="form-select" required>
                                            <option value="" disabled selected>Seleccione un tipo...</option>
                                            @foreach($tiposObservacion as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 d-none" id="usuarioEscalamiento">
                                    <label for="terceroAsignado" class="form-label fw-semibold text-danger">Asignar a Usuario (Escalamiento):</label>
                                    <select id="terceroAsignado" name="id_scp_usuario_asignado" class="form-select">
                                        <option value="">Seleccione un usuario...</option>
                                        @foreach($usuariosEscalamiento as $usuarioEsc)
                                            <option value="{{ $usuarioEsc->id }}">
                                                {{ $usuarioEsc->maeTercero->nom_ter ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    <i class="feather-save me-1"></i> Guardar Seguimiento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARRA LATERAL --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title text-dark fw-bold mb-0">
                        <i class="feather-list me-2 text-primary"></i> Información Clave
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Prioridad --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold">Prioridad:</span>
                        @php
                            $prioridad = strtolower($soporte->prioridad->nombre ?? '');
                            $prioridadClass = match($prioridad) {
                                'alta' => 'text-bg-danger',
                                'media' => 'text-bg-warning',
                                'baja' => 'text-bg-success',
                                default => 'text-bg-secondary'
                            };
                        @endphp
                        <span class="badge fs-6 {{ $prioridadClass }}">{{ $soporte->prioridad->nombre ?? 'No Asignada' }}</span>
                    </div>

                    {{-- Estado actual --}}
                    @php
                        $ultimoEstado = $soporte->observaciones->first()?->estado ?? null;
                        $estadoClass = match(strtolower($ultimoEstado?->nombre ?? '')) {
                            'resuelto', 'cerrado', 'finalizado' => 'text-bg-success',
                            'pendiente', 'en espera' => 'text-bg-warning',
                            'cancelado' => 'text-bg-secondary',
                            default => 'text-bg-info'
                        };
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-semibold">Estado Actual:</span>
                        <span class="badge fs-6 {{ $estadoClass }}">{{ $ultimoEstado->nombre ?? 'Sin Estado' }}</span>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-calendar me-1"></i> Fecha de Creación:</span>
                        <p class="text-dark mb-0">{{ $soporte->created_at->format('d/m/Y H:i A') }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-clock me-1"></i> Última Actualización:</span>
                        <p class="text-dark mb-0">{{ $soporte->updated_at->format('d/m/Y H:i A') }}</p>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-bold mb-3">Asignación</h6>
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-user me-1"></i> Responsable:</span>
                        <p class="text-dark mb-0">
                            {{ $soporte->observaciones()->latest('id')->first()?->scpUsuarioAsignado?->maeTercero->nom_ter ?? 'No disponible' }}
                        </p>
                    </div>

                    <h6 class="text-muted fw-bold mb-3">Solicitante</h6>
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-user me-1"></i> Creada:</span>
                        <p class="text-dark mb-0">{{ $soporte->usuario->name ?? 'No disponible' }}</p>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-briefcase me-1"></i> Cargo / Área:</span>
                        <p class="text-dark mb-0">{{ $soporte->cargo->nombre_cargo ?? 'No disponible' }}</p>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-bold mb-3">Clasificación</h6>
                    <div class="mb-3">
                        <span class="text-muted fw-semibold d-block"><i class="feather-layers me-1"></i> Tipo:</span>
                        <p class="text-dark mb-0">{{ $soporte->tipo->nombre ?? 'No disponible' }}</p>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted fw-semibold d-block"><i class="feather-tag me-1"></i> Sub-Tipo:</span>
                        <p class="text-dark mb-0">{{ $soporte->subTipo->nombre ?? 'No disponible' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT ESCALAMIENTO --}}
    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Script para el campo de escalamiento en el formulario de observación
            const tipoObservacionSelect = document.getElementById("tipoObservacion");
            const usuarioEscalamientoDiv = document.getElementById("usuarioEscalamiento");

            if (tipoObservacionSelect && usuarioEscalamientoDiv) {
                tipoObservacionSelect.addEventListener("change", function () {
                    const isEscalamiento = tipoObservacionSelect.selectedOptions[0].text.toLowerCase().includes("escalamiento");
                    usuarioEscalamientoDiv.classList.toggle("d-none", !isEscalamiento);

                    if (!isEscalamiento) {
                        const terceroAsignadoSelect = document.getElementById("terceroAsignado");
                        if (terceroAsignadoSelect) {
                            terceroAsignadoSelect.value = "";
                        }
                    }
                });
                // Ejecutar al cargar la página por si ya hay una opción seleccionada
                tipoObservacionSelect.dispatchEvent(new Event('change'));
            }

            // Script para el efecto de expandir/colapsar la descripción del soporte
            const descriptionContent = document.getElementById('descriptionContent');
            const descriptionText = descriptionContent ? descriptionContent.querySelector('.support-description-text') : null;
            const toggleButton = document.getElementById('toggleDescription');
            const textButton = toggleButton ? toggleButton.querySelector('.text-button') : null;
            const iconExpand = toggleButton ? toggleButton.querySelector('.icon-expand') : null;
            const iconCollapse = toggleButton ? toggleButton.querySelector('.icon-collapse') : null;

            // Solo activar si el texto y los elementos existen
            if (descriptionText && descriptionContent && toggleButton) {
                const maxHeight = 150; // Altura máxima deseada antes de mostrar el botón

                if (descriptionText.scrollHeight > maxHeight) {
                    toggleButton.classList.remove('d-none');
                    descriptionContent.style.maxHeight = `${maxHeight}px`; // Limita la altura inicial
                    descriptionContent.style.overflow = 'hidden';
                    descriptionContent.style.transition = 'max-height 0.3s ease-out'; // Animación suave

                    toggleButton.addEventListener('click', function() {
                        const isExpanded = descriptionContent.classList.toggle('expanded');
                        if (isExpanded) {
                            descriptionContent.style.maxHeight = `${descriptionText.scrollHeight}px`; // Expande a la altura real
                            if (textButton) textButton.textContent = 'Colapsar';
                            if (iconExpand) iconExpand.classList.add('d-none');
                            if (iconCollapse) iconCollapse.classList.remove('d-none');
                        } else {
                            descriptionContent.style.maxHeight = `${maxHeight}px`; // Colapsa
                            if (textButton) textButton.textContent = 'Expandir';
                            if (iconExpand) iconExpand.classList.remove('d-none');
                            if (iconCollapse) iconCollapse.classList.add('d-none');
                        }
                    });
                }
            }
        });
    </script>
    @endpush

    {{-- CSS ADICIONAL PARA LOS EFECTOS VISUALES --}}
    {{-- Coloca este estilo en tu archivo CSS principal o en la sección <head> de tu layout si aplica globalmente --}}
    <style>

        /* ===== DESCRIPCIÓN DEL SOPORTE (Rojo pastel sutil) ===== */
        .support-description-card {
            background-color: #fdecef !important; /* Fondo pastel rojo muy suave */
            border: 1px solid #f5c2c7 !important; /* Borde pastel rojo */
        }
        .support-description-text {
            background-color: #fff !important; /* Texto siempre en blanco */
            border: 1px solid #f5c2c7 !important;
        }

        /* ===== HISTORIAL Y SEGUIMIENTO (Azul pastel sutil) ===== */
        .history-tracking-card {
            background-color: #eaf4ff !important; /* Fondo pastel azul muy suave */
            border: 1px solid #b6daff !important; /* Borde pastel azul */
        }
        .history-tracking-card .card-header {
            background-color: #d1e7ff !important; /* Azul pastel un poco más fuerte para el encabezado */
            border-bottom: 1px solid #b6daff !important;
        }
        .support-description-card {
            border-radius: 12px; /* Más redondeado */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Sombra más pronunciada pero suave */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .support-description-card:hover {
            transform: translateY(-3px); /* Pequeño levantamiento al pasar el ratón */
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }

        .support-description-text {
            border-radius: 8px !important; /* Asegura bordes redondeados en el p */
            background-color: var(--bs-light-bg-subtle, #f8f9fa) !important; /* Color de fondo consistente */
            border-color: var(--bs-border-color-translucent, rgba(0, 0, 0, 0.175)) !important; /* Borde consistente */
        }

        /* Estilos para el efecto de expandir/colapsar */
        .description-content-wrapper {
            position: relative;
            /* Estos estilos serán controlados por JS, pero definimos la transición aquí */
            transition: max-height 0.3s ease-out;
        }

        .description-content-wrapper::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px; /* Altura del gradiente */
            background: linear-gradient(to top, rgba(255,255,255,1), rgba(255,255,255,0)); /* Gradiente blanco que cubre el texto */
            pointer-events: none; /* Permite hacer clic en el texto debajo */
            opacity: 1;
            transition: opacity 0.3s ease-out;
        }

        .description-content-wrapper.expanded::after {
            opacity: 0; /* Oculta el gradiente cuando se expande */
        }

        /* Estilos para el timeline de observaciones (opcional, para un toque extra) */
        .card .card-body > .d-flex .me-3 .border-start {
            border-left: 2px dashed var(--bs-primary-subtle, #b4d4f8) !important; /* Hace la línea punteada */
        }
        .card .card-body > .d-flex:last-child .me-3 .border-start {
            display: none; /* Oculta la línea del último elemento */
        }
    </style>
</x-base-layout>