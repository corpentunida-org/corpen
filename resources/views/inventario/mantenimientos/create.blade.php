{{-- resources/views/inventario/mantenimientos/create.blade.php · INV-MANTENIMIENTOS-CREATE-v5.3-COLORGROUPS --}}
<x-base-layout>
    @php
        $activosJson  = $activos->map(fn($a) => ['id' => $a->id, 'text' => $a->codigo_activo . ' - ' . $a->nombre])->values()->toJson();
        $usuariosJson = $usuarios->map(fn($u) => ['id' => $u->id, 'text' => $u->name])->values()->toJson();
    @endphp

    <style>
        /* Sistema de Diseño Corporativo & UX Full-Width */
        :root {
            --corp-ink: #0f172a;
            --corp-muted: #64748b;
            --corp-border: #e2e8f0;
            --corp-surface: #ffffff;
            --corp-bg: #f8fafc;
            --corp-accent: #1d4ed8;
            --corp-accent-soft: #eff6ff;
        }

        .corp-shell {
            width: 100%;
            max-width: 100%;
            margin: 0;
            color: var(--corp-ink);
        }

        /* Cabecera ejecutiva */
        .corp-header {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        /* Barra minimalista de accesos rápidos por grupo de enfoque */
        .corp-quicknav {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow-x: auto;
            padding-top: 12px;
            margin-top: 12px;
            border-top: 1px solid #f1f5f9;
            scrollbar-width: none;
        }
        .corp-quicknav::-webkit-scrollbar {
            display: none;
        }
        .corp-nav-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid transparent;
            transition: all .15s ease;
        }
        .corp-nav-pill i {
            font-size: 13px;
        }
        .corp-nav-sep {
            width: 1px;
            height: 18px;
            background: #e2e8f0;
            margin: 0 2px;
            flex-shrink: 0;
        }

        /* 1. Grupo Principal / Vistas Core (Azul claro) */
        .pill-core {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }
        .pill-core:hover {
            background: #dbeafe;
            color: #1e40af;
            border-color: #bfdbfe;
        }
        .pill-core.active {
            background: #1d4ed8;
            color: #ffffff;
            border-color: #1d4ed8;
            box-shadow: 0 2px 6px rgba(29, 78, 216, 0.25);
        }

        /* 2. Grupo Operaciones / Altas Rápidas (Verde esmeralda claro) */
        .pill-ops {
            background: #ecfdf5;
            color: #047857;
            border-color: #d1fae5;
        }
        .pill-ops:hover {
            background: #d1fae5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        .pill-ops.active {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }

        /* 3. Grupo Control / Bitácoras y Alertas (Ámbar cálido claro) */
        .pill-audit {
            background: #fffbeb;
            color: #b45309;
            border-color: #fef3c7;
        }
        .pill-audit:hover {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }
        .pill-audit.active {
            background: #d97706;
            color: #ffffff;
            border-color: #d97706;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
        }

        /* 4. Grupo Configuración / Catálogos Maestros (Púrpura suave) */
        .pill-conf {
            background: #f5f3ff;
            color: #6d28d9;
            border-color: #ede9fe;
        }
        .pill-conf:hover {
            background: #ede9fe;
            color: #5b21b6;
            border-color: #ddd6fe;
        }
        .pill-conf.active {
            background: #7c3aed;
            color: #ffffff;
            border-color: #7c3aed;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);
        }

        /* Paneles del formulario */
        .corp-panel {
            background: var(--corp-surface);
            border: 1px solid var(--corp-border);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: visible;
        }
        .corp-panel-head {
            background: #f8fafc;
            border-bottom: 1px solid var(--corp-border);
            border-radius: 12px 12px 0 0;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .corp-panel-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--corp-ink);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }
        .corp-panel-body {
            padding: 18px 20px;
        }

        /* Inputs y etiquetas corporativas */
        .corp-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 5px;
        }
        .corp-input, .corp-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13.5px;
            padding: 8px 12px;
            background-color: #ffffff;
            color: var(--corp-ink);
            transition: all .15s ease;
        }
        .corp-input:focus, .corp-textarea:focus {
            outline: none;
            border-color: var(--corp-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        /* Grupo input dinero */
        .input-money-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-money-group::before {
            content: '$';
            position: absolute;
            left: 12px;
            color: #64748b;
            font-weight: 700;
            font-size: 13.5px;
            z-index: 5;
        }
        .input-money-group .cost-field {
            padding-left: 26px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            color: var(--corp-ink);
        }

        /* Autocomplete corporativo */
        .autocomplete-wrapper {
            position: relative;
        }
        .autocomplete-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            margin-top: 4px;
            max-height: 210px;
            overflow-y: auto;
            z-index: 60;
            display: none;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.12);
        }
        .autocomplete-item {
            padding: 9px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }
        .autocomplete-item:last-child {
            border-bottom: none;
        }
        .autocomplete-item:hover {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }
        .clear-btn {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            display: none;
            font-size: 13px;
            z-index: 5;
        }
        .clear-btn:hover {
            color: #ef4444;
        }

        .error-text {
            color: #dc2626;
            font-size: 11.5px;
            margin-top: 4px;
            display: block;
            font-weight: 600;
        }
    </style>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm" role="alert" style="z-index: 1060; min-width: 320px; border-radius: 10px; font-size: 13px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div class="flex-grow-1 fw-semibold">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>
    @endif

    <div class="container-fluid py-2 px-2 px-md-3">
        <div class="corp-shell">

            <form action="{{ route('inventario.mantenimientos.store') }}" method="POST" enctype="multipart/form-data" id="mantenimientoForm">
                @csrf

                {{-- 1. CABECERA EJECUTIVA + ACCESOS RÁPIDOS POR GRUPO DE ENFOQUE --}}
                <div class="corp-header mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <nav aria-label="breadcrumb" class="mb-1">
                                <ol class="breadcrumb mb-0" style="font-size: 11.5px;">
                                    <li class="breadcrumb-item text-muted">Gestión de Inventario</li>
                                    <li class="breadcrumb-item text-muted">Operaciones</li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Registrar Mantenimiento</li>
                                </ol>
                            </nav>
                            <h4 class="fw-bold mb-1" style="letter-spacing: -0.02em; color: #0f172a;">
                                Registrar Servicio Técnico / Mantenimiento
                            </h4>
                            <p class="text-secondary mb-0" style="font-size: 13.5px;">
                                Documenta intervenciones preventivas o correctivas, costos asociados y responsables técnicos del activo.
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('inventario.mantenimientos.index') }}"
                               class="btn btn-outline-secondary fw-semibold px-3 py-2"
                               style="font-size: 13px; border-radius: 9px;">
                                <i class="bi bi-arrow-left me-1"></i> Historial Técnico
                            </a>
                            <button type="submit"
                                    class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                    style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                <i class="bi bi-check2-circle me-1"></i> Guardar Registro
                            </button>
                        </div>
                    </div>

                    {{-- Barra Minimalista de Accesos Rápidos con Color por Enfoque --}}
                    <div class="corp-quicknav" aria-label="Accesos rápidos de inventario">
                        {{-- GRUPO 1: PRINCIPAL / VISTAS CORE (Azul claro) --}}
                        <a href="{{ route('inventario.tablero') }}" class="corp-nav-pill pill-core" title="Vista Principal">
                            <i class="bi bi-speedometer2"></i> Tablero
                        </a>
                        <a href="{{ route('inventario.activos.index') }}" class="corp-nav-pill pill-core" title="Vista Principal">
                            <i class="bi bi-laptop"></i> Activos
                        </a>

                        <span class="corp-nav-sep"></span>

                        {{-- GRUPO 2: OPERACIONES / ALTAS (Verde esmeralda claro) --}}
                        <a href="{{ route('inventario.compras.create') }}" class="corp-nav-pill pill-ops" title="Operaciones">
                            <i class="bi bi-cart-plus"></i> + Compra
                        </a>
                        <a href="{{ route('inventario.movimientos.create') }}" class="corp-nav-pill pill-ops" title="Operaciones">
                            <i class="bi bi-file-earmark-arrow-up"></i> + Acta / Mov.
                        </a>
                        <a href="{{ route('inventario.mantenimientos.create') }}" class="corp-nav-pill pill-ops active" title="Operaciones">
                            <i class="bi bi-wrench-adjustable"></i> + Mantenimiento
                        </a>

                        <span class="corp-nav-sep"></span>

                        {{-- GRUPO 3: CONTROL / BITÁCORAS Y ALERTAS (Ámbar cálido claro) --}}
                        <a href="{{ route('inventario.movimientos.index') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                            <i class="bi bi-arrow-left-right"></i> Movimientos
                        </a>
                        <a href="{{ route('inventario.compras.index') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                            <i class="bi bi-receipt"></i> Facturas
                        </a>
                        <a href="{{ route('inventario.activos.alertas') }}" class="corp-nav-pill pill-audit" title="Control y Seguimiento">
                            <i class="bi bi-exclamation-triangle"></i> Alertas
                        </a>

                        <span class="corp-nav-sep"></span>

                        {{-- GRUPO 4: CONFIGURACIÓN / CATÁLOGOS (Púrpura suave) --}}
                        <a href="{{ route('inventario.bodegas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                            <i class="bi bi-geo-alt"></i> Bodegas
                        </a>
                        <a href="{{ route('inventario.clasificacion.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                            <i class="bi bi-diagram-3"></i> Clasificación
                        </a>
                        <a href="{{ route('inventario.marcas.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                            <i class="bi bi-tags"></i> Marcas
                        </a>
                        <a href="{{ route('inventario.metodos-pago.index') }}" class="corp-nav-pill pill-conf" title="Configuración">
                            <i class="bi bi-credit-card"></i> Métodos Pago
                        </a>
                    </div>
                </div>

                {{-- 2. CUERPO DEL FORMULARIO EN 2 COLUMNAS --}}
                <div class="row g-3 align-items-start">

                    {{-- COLUMNA IZQUIERDA: EQUIPO Y RESPONSABLES --}}
                    <div class="col-12 col-lg-5">
                        <div class="corp-panel">
                            <div class="corp-panel-head">
                                <h6 class="corp-panel-title">
                                    <i class="bi bi-pc-display text-primary"></i> Equipo y Responsables
                                </h6>
                            </div>
                            <div class="corp-panel-body">

                                <div class="mb-3">
                                    <label class="corp-label">Activo Afectado <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrapper">
                                        <div class="position-relative">
                                            <i class="bi bi-search text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                            <input type="text" id="search_activos" class="corp-input ps-4 pe-4" placeholder="Escribe la placa o nombre del activo..." autocomplete="off">
                                            <span class="clear-btn" id="clear_activos" title="Limpiar"><i class="bi bi-x-lg"></i></span>
                                        </div>
                                        <input type="hidden" name="id_InvActivos" id="id_InvActivos" value="{{ old('id_InvActivos') }}">
                                        <div id="list_activos" class="autocomplete-list"></div>
                                    </div>
                                    @error('id_InvActivos') <span class="error-text">Debe seleccionar un activo válido de la lista.</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label">Técnico / Persona que Registra <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrapper">
                                        <div class="position-relative">
                                            <i class="bi bi-person-gear text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                            <input type="text" id="search_tecnico" class="corp-input ps-4 pe-4" placeholder="Buscar técnico..." autocomplete="off">
                                            <span class="clear-btn" id="clear_tecnico" title="Limpiar"><i class="bi bi-x-lg"></i></span>
                                        </div>
                                        <input type="hidden" name="id_usersRegistro" id="id_usersRegistro" value="{{ old('id_usersRegistro', auth()->id()) }}">
                                        <div id="list_tecnico" class="autocomplete-list"></div>
                                    </div>
                                    @error('id_usersRegistro') <span class="error-text">Seleccione un técnico válido.</span> @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="corp-label">Asignado a (Responsable Temporal) <span class="text-danger">*</span></label>
                                    <div class="autocomplete-wrapper">
                                        <div class="position-relative">
                                            <i class="bi bi-person-badge text-muted position-absolute" style="left: 11px; top: 50%; transform: translateY(-50%); font-size: 13px;"></i>
                                            <input type="text" id="search_asignado" class="corp-input ps-4 pe-4" placeholder="Buscar responsable..." autocomplete="off">
                                            <span class="clear-btn" id="clear_asignado" title="Limpiar"><i class="bi bi-x-lg"></i></span>
                                        </div>
                                        <input type="hidden" name="id_usersAsignado" id="id_usersAsignado" value="{{ old('id_usersAsignado', auth()->id()) }}">
                                        <div id="list_asignado" class="autocomplete-list"></div>
                                    </div>
                                    @error('id_usersAsignado') <span class="error-text">Seleccione un responsable válido.</span> @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: DIAGNÓSTICO Y COSTO DEL SERVICIO --}}
                    <div class="col-12 col-lg-7">
                        <div class="corp-panel">
                            <div class="corp-panel-head">
                                <h6 class="corp-panel-title">
                                    <i class="bi bi-wrench-adjustable text-primary"></i> Diagnóstico Técnico y Valoración
                                </h6>
                            </div>
                            <div class="corp-panel-body">

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-sm-6">
                                        <label class="corp-label">Costo del Servicio <span class="text-danger">*</span></label>
                                        <div class="input-money-group">
                                            <input type="text" id="costo_visual" class="corp-input cost-field" placeholder="0">
                                            <input type="hidden" name="costo_mantenimiento" id="costo_real" value="{{ old('costo_mantenimiento') }}">
                                        </div>
                                        @error('costo_mantenimiento') <span class="error-text">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 d-flex align-items-end">
                                        <div class="p-2 px-3 rounded-3 border w-100" style="background: #f8fafc; border-color: #e2e8f0 !important; font-size: 12px; color: #64748b;">
                                            <i class="bi bi-info-circle text-primary me-1"></i>
                                            El soporte PDF o factura podrá adjuntarse desde el <strong>Historial Técnico</strong> tras guardar.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="corp-label">Detalle Técnico / Reparación <span class="text-danger">*</span></label>
                                    <textarea name="detalle" class="corp-textarea" rows="5" placeholder="Describa el fallo reportado, diagnóstico técnico, repuestos utilizados y la solución aplicada...">{{ old('detalle') }}</textarea>
                                    @error('detalle') <span class="error-text">{{ $message }}</span> @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                    <a href="{{ route('inventario.mantenimientos.index') }}"
                                       class="btn btn-outline-secondary fw-semibold px-4 py-2"
                                       style="font-size: 13px; border-radius: 9px;">
                                        Cancelar
                                    </a>
                                    <button type="submit"
                                            class="btn btn-primary fw-semibold px-4 py-2 shadow-sm"
                                            style="font-size: 13px; border-radius: 9px; background-color: #1d4ed8; border-color: #1d4ed8;">
                                        <i class="bi bi-check2-circle me-1"></i> Guardar Registro
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. LÓGICA DEL COSTO CON SEPARADOR DE MILES ---
            const costoVisual = document.getElementById('costo_visual');
            const costoReal = document.getElementById('costo_real');
            const formateadorMoneda = new Intl.NumberFormat('es-CO');

            if (costoReal.value) {
                costoVisual.value = formateadorMoneda.format(costoReal.value);
            }

            costoVisual.addEventListener('input', function() {
                let numeroLimpio = this.value.replace(/\D/g, '');

                if (numeroLimpio === '') {
                    costoReal.value = '';
                    this.value = '';
                    return;
                }

                costoReal.value = numeroLimpio;
                this.value = formateadorMoneda.format(numeroLimpio);
            });

            // --- 2. LÓGICA DE LOS BUSCADORES (AUTOCOMPLETE) ---
            const dataActivos = {!! $activosJson !!};
            const dataUsuarios = {!! $usuariosJson !!};

            function setupAutocomplete(inputId, hiddenId, listId, clearId, dataArray) {
                const input = document.getElementById(inputId);
                const hidden = document.getElementById(hiddenId);
                const list = document.getElementById(listId);
                const clearBtn = document.getElementById(clearId);

                if (hidden.value) {
                    const match = dataArray.find(i => i.id == hidden.value);
                    if (match) {
                        input.value = match.text;
                        clearBtn.style.display = 'block';
                    }
                }

                input.addEventListener('input', function() {
                    const val = this.value.toLowerCase().trim();
                    list.innerHTML = '';
                    hidden.value = '';

                    if (val.length === 0) {
                        list.style.display = 'none';
                        clearBtn.style.display = 'none';
                        return;
                    }

                    clearBtn.style.display = 'block';

                    const matches = dataArray.filter(item => item.text.toLowerCase().includes(val)).slice(0, 15);

                    if (matches.length > 0) {
                        matches.forEach(match => {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.innerHTML = match.text;

                            div.addEventListener('click', function() {
                                input.value = match.text;
                                hidden.value = match.id;
                                list.style.display = 'none';
                            });
                            list.appendChild(div);
                        });
                        list.style.display = 'block';
                    } else {
                        list.innerHTML = '<div class="autocomplete-item" style="color: #ef4444; pointer-events: none;">No se encontraron resultados...</div>';
                        list.style.display = 'block';
                    }
                });

                clearBtn.addEventListener('click', function() {
                    input.value = '';
                    hidden.value = '';
                    list.style.display = 'none';
                    this.style.display = 'none';
                    input.focus();
                });

                document.addEventListener('click', function(e) {
                    if (e.target !== input) {
                        list.style.display = 'none';
                    }
                });
            }

            setupAutocomplete('search_activos', 'id_InvActivos', 'list_activos', 'clear_activos', dataActivos);
            setupAutocomplete('search_tecnico', 'id_usersRegistro', 'list_tecnico', 'clear_tecnico', dataUsuarios);
            setupAutocomplete('search_asignado', 'id_usersAsignado', 'list_asignado', 'clear_asignado', dataUsuarios);

            document.getElementById('mantenimientoForm').addEventListener('submit', function(e) {
                if(!document.getElementById('id_InvActivos').value) {
                    e.preventDefault();
                    alert('Por favor, busca y selecciona un Activo de la lista desplegable.');
                    document.getElementById('search_activos').focus();
                }
            });
        });
    </script>
</x-base-layout>
