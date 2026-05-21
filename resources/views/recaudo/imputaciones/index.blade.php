<x-base-layout>
    {{-- Dependencias Adicionales para el Dashboard --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <div class="gs-workspace">
        
        {{-- ========================================== --}}
        {{-- SUPER ULTRA DASHBOARD ANALÍTICO --}}
        {{-- ========================================== --}}
        <div class="dashboard-panel mb-4">
            <div class="dashboard-header d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="main-title">Control de Imputaciones Contables</h1>
                    <p class="text-muted m-0 fs-14">Módulo de Recaudo • Vista Analítica y Hoja de Trabajo</p>
                </div>
                <a href="{{ route('recaudo.imputaciones.create') }}" class="btn-gs-primary">
                    <i class="fas fa-plus me-2"></i> Nueva Imputación
                </a>
            </div>

            {{-- KPIs --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="kpi-card minimalist-border">
                        <span class="kpi-label">Total Recaudado</span>
                        <h3 class="kpi-value text-dark">$452.8M</h3>
                        <span class="kpi-trend text-success"><i class="fas fa-arrow-up"></i> 12% este mes</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card minimalist-border">
                        <span class="kpi-label">Pendientes por Conciliar</span>
                        <h3 class="kpi-value text-warning">24</h3>
                        <span class="kpi-trend text-muted">Requieren acción manual</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card minimalist-border">
                        <span class="kpi-label">Conciliación Automática</span>
                        <h3 class="kpi-value text-success">89%</h3>
                        <span class="kpi-trend text-success"><i class="fas fa-robot"></i> Eficiencia del bot</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="kpi-card minimalist-border">
                        <span class="kpi-label">Imputaciones Anuladas</span>
                        <h3 class="kpi-value text-danger">3</h3>
                        <span class="kpi-trend text-muted">En el periodo actual</span>
                    </div>
                </div>
            </div>

           {{-- Gráficos --}}
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="chart-container minimalist-border p-3 bg-white h-100 rounded-3 d-flex flex-column">
                        <h6 class="minimalist-chart-title mb-3">Volumen de Recaudo vs Conciliación (Últimos 7 días)</h6>
                        {{-- EL TRUCO: Contenedor relativo con altura fija --}}
                        <div class="position-relative w-100 flex-grow-1" style="min-height: 220px; height: 220px;">
                            <canvas id="recaudoChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="chart-container minimalist-border p-3 bg-white h-100 rounded-3 d-flex flex-column">
                        <h6 class="minimalist-chart-title mb-3">Distribución por Estado</h6>
                        {{-- EL TRUCO: Contenedor relativo con altura fija --}}
                        <div class="position-relative w-100 flex-grow-1" style="min-height: 220px; height: 220px;">
                            <canvas id="estadoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- BARRA DE HERRAMIENTAS TIPO GOOGLE SHEETS --}}
        {{-- ========================================== --}}
        <div class="gs-toolbar-wrapper shadow-sm border rounded-top bg-white mt-4">
            {{-- Menú superior simulado --}}
            <div class="gs-menu-bar border-bottom px-2 py-1 d-flex gap-3 text-muted fs-13">
                <span class="gs-menu-item">Archivo</span>
                <span class="gs-menu-item">Editar</span>
                <span class="gs-menu-item">Ver</span>
                <span class="gs-menu-item fw-bold text-dark">Filtros</span>
                <span class="gs-menu-item">Herramientas</span>
            </div>
            
            {{-- Barra de Filtros reales --}}
            <div class="gs-action-bar px-2 py-2 border-bottom bg-light">
                <form action="{{ route('recaudo.imputaciones.index') }}" method="GET" id="imputacionFilterForm" class="d-flex align-items-center gap-2 m-0 w-100">
                    
                    {{-- Herramientas de formato (Estéticas) --}}
                    <div class="gs-tool-group border-end pe-2 d-none d-md-flex gap-1">
                        <button type="button" class="gs-tool-btn"><i class="fas fa-undo"></i></button>
                        <button type="button" class="gs-tool-btn"><i class="fas fa-redo"></i></button>
                        <button type="button" class="gs-tool-btn"><i class="fas fa-print"></i></button>
                    </div>

                    {{-- Buscador (Barra Principal) --}}
                    <div class="search-input-wrapper flex-grow-1 mx-2 position-relative">
                        <i class="fas fa-search position-absolute top-50 translate-middle-y text-muted" style="left: 10px; font-size: 12px;"></i>
                        <input type="text" name="search" id="liveSearchImputaciones" class="gs-input w-100" placeholder="Buscar concepto, ID recibo o tercero..." value="{{ request('search') }}" autocomplete="off">
                    </div>

                    {{-- Filtros Selects --}}
                    <div class="custom-select-wrapper" style="min-width: 180px;">
                        <select name="estado_conciliacion" class="gs-select auto-filter-task w-100">
                            <option value="">Todos los Estados</option>
                            <option value="Pendiente" {{ request('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Conciliado_Auto" {{ request('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>Conciliado Auto</option>
                            <option value="Conciliado_Manual" {{ request('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>Conciliado Manual</option>
                            <option value="Anulado" {{ request('estado_conciliacion') == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                    </div>

                    <a href="{{ route('recaudo.imputaciones.index') }}" class="gs-tool-btn text-danger ms-2" data-bs-toggle="tooltip" title="Limpiar Filtros">
                        <i class="fas fa-filter-slash"></i>
                    </a>
                </form>
            </div>

            {{-- Barra de Fórmulas --}}
            <div class="gs-formula-bar px-2 py-1 border-bottom bg-white d-flex align-items-center">
                <div class="gs-fx-icon text-muted fw-bold fst-italic me-2 border-end pe-2">fx</div>
                <div class="text-muted fs-13 flex-grow-1" id="gs-formula-display">=BUSCARV(datos_imputaciones; A1:M99; ...)</div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- CONTENEDOR AJAX & HOJA DE CÁLCULO --}}
        {{-- ========================================== --}}
        <div id="ajaxImputacionContainer" class="bg-white border border-top-0 rounded-bottom">
            @fragment('imputaciones-list')
            <div class="main-content-area">
                
                {{-- VISTA DESKTOP: SPREADSHEET PURO --}}
                <div class="spreadsheet-container d-none-mobile custom-scrollbar">
                    <table class="table-gsheets" id="main-table">
                        <thead>
                            {{-- Letras de Columnas tipo Excel/Sheets --}}
                            <tr class="gs-column-letters">
                                <th class="col-index shadow-sm"></th>
                                <th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th><th>J</th><th>K</th><th>L</th><th>M</th>
                            </tr>
                            <tr class="gs-header-row">
                                <th class="col-index text-center shadow-sm"></th>
                                <th style="width: 90px;">N° Recibo</th>
                                <th style="width: 110px;">Transacción</th>
                                <th style="width: 100px;">Tipo</th> {{-- NUEVO CAMPO --}}
                                <th style="width: 180px;">Tercero / Origen</th>
                                <th style="width: 120px;">Distrito</th>
                                <th style="width: 140px;">Línea / Obligación</th>
                                <th style="width: 70px;" class="text-center">Cuota</th>
                                <th style="width: 220px;">Concepto Contable</th>
                                <th style="width: 120px;" class="text-end">Monto ($)</th>
                                <th style="width: 140px;">Registrado Por</th> {{-- NUEVO CAMPO (Usuario) --}}
                                <th style="width: 130px;" class="text-center">Estado</th>
                                <th style="width: 60px;" class="text-center">Soporte</th>
                                <th style="width: 50px;" class="text-center">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($imputaciones as $index => $imputacion)
                                @php
                                    $comprobante = $imputacion->comprobante;
                                    $urlSoporte = ($comprobante && $comprobante->url_archivo !== '#') ? $comprobante->url_archivo : null;

                                    $fichaData = json_encode([
                                        'recibo'          => $imputacion->id_recibo,
                                        'transaccion'     => $imputacion->id_transaccion,
                                        'tipo_imputacion' => $imputacion->tipo ?? 'N/A', // NUEVO
                                        'usuario'         => $imputacion->user->name ?? 'Sistema', // NUEVO
                                        'tercero'         => $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen,
                                        'doc_tercero'     => $imputacion->id_tercero_origen,
                                        'distrito'        => $imputacion->distrito->NOM_DIST ?? $imputacion->id_distrito,
                                        'linea'           => $comprobante?->obligacion?->nombre ?? 'N/A',
                                        'cuota'           => $comprobante?->numero_cuota ?? 'N/A',
                                        'tipo_pago'       => $comprobante?->tipo_pago ?? 'N/A',
                                        'pr'              => $comprobante?->pr ?? 'N/A',
                                        'cco'             => $comprobante?->cco ?? 'N/A',
                                        'observacion'     => $comprobante?->observacion ?? 'Sin observaciones.',
                                        'fecha_pago'      => $comprobante?->fecha_pago ? date('d/m/Y', $comprobante->fecha_pago) : 'N/A',
                                        'monto_orig'      => $comprobante?->monto_pagado ? number_format($comprobante->monto_pagado, 0, ',', '.') : 'N/A',
                                        'soporte_url'     => $urlSoporte,
                                        'concepto'        => $imputacion->concepto_contable,
                                        'valor'           => number_format($imputacion->valor_imputado, 0, ',', '.'),
                                        'estado'          => str_replace('_', ' ', $imputacion->estado_conciliacion),
                                        'url_show'        => route('recaudo.imputaciones.show', $imputacion->id)
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT);
                                @endphp

                                <tr class="gs-data-row" onclick="abrirTarjetaRelaciones(event, '{{ $fichaData }}', this)">
                                    <td class="col-index">{{ $imputaciones->firstItem() + $index }}</td>
                                    
                                    <td class="gs-cell text-primary fw-medium">#{{ $imputacion->id_recibo }}</td>
                                    <td class="gs-cell"><div class="text-truncate">Tx-{{ $imputacion->id_transaccion }}</div></td>
                                    <td class="gs-cell"><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">{{ $imputacion->tipo ?? 'N/A' }}</span></td> {{-- NUEVO --}}
                                    <td class="gs-cell fw-medium"><div class="text-truncate">{{ $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen }}</div></td>
                                    <td class="gs-cell text-muted">{{ $imputacion->distrito->NOM_DIST ?? $imputacion->id_distrito }}</td>
                                    <td class="gs-cell text-primary"><div class="text-truncate">{{ $comprobante?->obligacion?->nombre ?? 'N/A' }}</div></td>
                                    <td class="gs-cell text-center">{{ $comprobante?->numero_cuota ?? '-' }}</td>
                                    <td class="gs-cell"><div class="text-truncate" style="max-width:200px;">{{ $imputacion->concepto_contable }}</div></td>
                                    <td class="gs-cell text-end gs-number-font fw-medium text-dark">${{ number_format($imputacion->valor_imputado, 0, ',', '.') }}</td>
                                    <td class="gs-cell text-muted"><div class="text-truncate"><i class="fas fa-user-circle me-1 opacity-50"></i>{{ $imputacion->user->name ?? 'Sistema' }}</div></td> {{-- NUEVO --}}
                                    <td class="gs-cell text-center p-0">
                                        @php
                                            $estadoClass = match($imputacion->estado_conciliacion) {
                                                'Pendiente'         => 'gs-tag-warning',
                                                'Conciliado_Auto'   => 'gs-tag-success',
                                                'Conciliado_Manual' => 'gs-tag-info',
                                                'Anulado'           => 'gs-tag-danger',
                                                default             => 'gs-tag-default',
                                            };
                                        @endphp
                                        <div class="gs-status-tag {{ $estadoClass }}">
                                            {{ str_replace('Conciliado_', 'Conc. ', $imputacion->estado_conciliacion) }}
                                        </div>
                                    </td>
                                    <td class="gs-cell text-center gs-action-cell">
                                        @if($imputacion->link_ecm)
                                            <button type="button" onclick="abrirModalSoporte('{{ $imputacion->link_ecm }}', 'Soporte Recibo #{{ $imputacion->id_recibo }}')" class="gs-icon-btn text-primary" title="Soporte ECM"><i class="fas fa-file-pdf"></i></button>
                                        @else
                                            <span class="text-black-50">-</span>
                                        @endif
                                    </td>
                                    <td class="gs-cell text-center gs-action-cell">
                                        <a href="{{ route('recaudo.imputaciones.edit', $imputacion->id) }}" class="gs-icon-btn text-secondary" title="Editar"><i class="fas fa-pen"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-5">
                                        <div class="text-muted fs-14">Hoja vacía. No hay datos coincidentes en esta vista.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VISTA MÓVIL (Mantenida limpia y funcional) --}}
                <div class="mobile-tasks-grid d-only-mobile p-3">
                    @forelse($imputaciones as $imputacion)
                        <div class="gs-mobile-card mb-3" onclick="window.location='{{ route('recaudo.imputaciones.show', $imputacion->id) }}'">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold text-primary">#{{ $imputacion->id_recibo }}</span>
                                <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $imputacion->estado_conciliacion) }}</span>
                            </div>
                            <div class="text-muted fs-14 mb-1">
                                <i class="fas fa-tag me-1"></i>{{ $imputacion->tipo ?? 'N/A' }} <span class="mx-1">|</span> <i class="fas fa-user me-1"></i>{{ $imputacion->user->name ?? 'Sistema' }}
                            </div>
                            <div class="text-dark fs-14">{{ $imputacion->concepto_contable }}</div>
                            <div class="fw-bold mt-2 fs-5">${{ number_format($imputacion->valor_imputado, 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <div class="text-center p-4 text-muted border rounded">Sin registros.</div>
                    @endforelse
                </div>

            </div>

            <footer class="gs-footer bg-light p-3 border-top d-flex justify-content-between align-items-center fs-13">
                <div class="text-muted">
                    Mostrando filas <strong>{{ $imputaciones->firstItem() ?? 0 }} - {{ $imputaciones->lastItem() ?? 0 }}</strong> de <strong>{{ $imputaciones->total() }}</strong>
                </div>
                <div class="m-0 gs-pagination">
                    {{ $imputaciones->links('pagination::bootstrap-4') }}
                </div>
            </footer>
            @endfragment
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 1: MINIMALISTA - DETALLES DE FILA --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalTarjetaRelaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-0" style="box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 bg-white">
                    <h5 class="modal-title fw-medium text-dark fs-5">Detalle de Fila</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3 bg-white">
                    
                    <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <div class="text-muted fs-12 text-uppercase tracking-wide mb-1">Valor Imputado</div>
                            <div class="fs-2 fw-normal gs-number-font text-dark lh-1" id="tarjeta-valor">$0</div>
                            <div class="text-muted fs-12 mt-1">Monto Original: <span id="tarjeta-monto-orig"></span></div>
                        </div>
                        <div class="text-end">
                            <div class="text-muted fs-12 text-uppercase tracking-wide mb-1">Estado</div>
                            <div class="fs-6 fw-medium text-dark" id="tarjeta-estado">---</div>
                        </div>
                    </div>

                    <div class="row g-0 mb-4 gs-minimal-grid">
                        <div class="col-12 cell pb-2 border-bottom mb-2">
                            <div class="lbl">Tercero / Origen</div>
                            <div class="val" id="tarjeta-tercero">---</div>
                            <div class="sub-val" id="tarjeta-doc">---</div>
                        </div>
                        
                        {{-- NUEVOS CAMPOS (TIPO Y USUARIO) EN EL MODAL --}}
                        <div class="col-6 cell pb-2 border-bottom mb-2">
                            <div class="lbl">Tipo Imputación</div>
                            <div class="val text-dark" id="tarjeta-tipo-imputacion">---</div>
                        </div>
                        <div class="col-6 cell pb-2 border-bottom mb-2 pl-3">
                            <div class="lbl">Usuario Responsable</div>
                            <div class="val text-dark" id="tarjeta-usuario">---</div>
                        </div>

                        <div class="col-6 cell pb-2 border-bottom mb-2">
                            <div class="lbl">Transacción</div>
                            <div class="val gs-number-font" id="tarjeta-transaccion">---</div>
                        </div>
                        <div class="col-6 cell pb-2 border-bottom mb-2 pl-3">
                            <div class="lbl">Fecha Pago</div>
                            <div class="val gs-number-font" id="tarjeta-fecha">---</div>
                        </div>
                        <div class="col-6 cell pb-2 border-bottom mb-2">
                            <div class="lbl">Línea / Obligación</div>
                            <div class="val text-primary" id="tarjeta-linea">---</div>
                        </div>
                        <div class="col-6 cell pb-2 border-bottom mb-2 pl-3">
                            <div class="lbl">Cuota / CCO</div>
                            <div class="val"><span id="tarjeta-cuota"></span> - <span id="tarjeta-cco"></span></div>
                        </div>
                        <div class="col-12 cell mt-2">
                            <div class="lbl">Concepto Contable</div>
                            <div class="val" id="tarjeta-concepto">---</div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4 pt-2">
                        <a href="#" id="btnIrFicha" class="gs-btn-flat text-primary text-decoration-none">
                            Ver Registro Completo &rarr;
                        </a>
                        <button type="button" id="btnSoporteModal" class="gs-btn-flat text-secondary bg-transparent border-0 ms-auto" style="display: none;">
                            <i class="fas fa-paperclip me-1"></i> Abrir Soporte
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 2: MINIMALISTA - VISOR DE DOCUMENTOS --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 rounded-0 bg-transparent">
                <div class="modal-header border-0 pb-2 px-0 d-flex justify-content-between text-white">
                    <span id="visorSoporteTitulo" class="fs-6 fw-light font-monospace">visor_documento.pdf</span>
                    <div>
                        <a href="#" id="btnDescargarSoporte" target="_blank" class="text-white text-decoration-none me-4 fs-14">
                            <i class="fas fa-external-link-alt me-1"></i> Externo
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-0 shadow-lg" style="height: 80vh; background: #fff;">
                    <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- CSS: ESTILO GOOGLE SHEETS & MINIMALISMO --}}
    {{-- ========================================== --}}
    <style>
        :root {
            --gs-border: #e2e3e3;
            --gs-header-bg: #f8f9fa;
            --gs-cell-hover: #f1f3f4;
            --gs-active-border: #1a73e8;
            --gs-active-bg: #e8f0fe;
            --gs-text: #202124;
            --gs-muted: #5f6368;
            --font-main: 'Roboto', Arial, sans-serif;
        }

        body { font-family: var(--font-main); background-color: #f0f2f5; color: var(--gs-text); }
        .gs-workspace { max-width: 1700px; margin: 20px auto; padding: 0 20px; }

        /* Dashboard Styles */
        .minimalist-border { border: 1px solid var(--gs-border); background: white; border-radius: 6px; padding: 20px; }
        .kpi-label { font-size: 13px; color: var(--gs-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-value { font-size: 28px; font-weight: 400; margin: 10px 0 5px; }
        .kpi-trend { font-size: 12px; }
        .minimalist-chart-title { font-size: 14px; color: var(--gs-muted); margin-bottom: 15px; font-weight: 500;}

        /* Barra Herramientas Sheets */
        .gs-menu-item { cursor: pointer; padding: 4px 8px; border-radius: 4px; transition: background 0.1s;}
        .gs-menu-item:hover { background: var(--gs-cell-hover); color: var(--gs-text); }
        .gs-input, .gs-select { border: none; background: transparent; font-size: 13px; font-family: var(--font-main); color: var(--gs-text); padding: 6px 10px; border-radius: 4px;}
        .gs-input:focus, .gs-select:focus { outline: none; background: #fff; box-shadow: inset 0 0 0 1px var(--gs-active-border); }
        .search-input-wrapper { background: #f1f3f4; border-radius: 4px; padding-left: 25px; transition: background 0.2s;}
        .search-input-wrapper:focus-within { background: #fff; }
        .gs-tool-btn { background: transparent; border: none; color: var(--gs-muted); width: 32px; height: 32px; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .gs-tool-btn:hover { background: var(--gs-cell-hover); color: var(--gs-text); }
        .gs-formula-bar { font-family: monospace; font-size: 13px; }
        .btn-gs-primary { background: var(--gs-active-border); color: white; border: none; padding: 8px 16px; font-size: 14px; border-radius: 4px; text-decoration: none; font-weight: 500;}
        .btn-gs-primary:hover { background: #1557b0; color: white;}

        /* Tabla Estilo Hoja de Cálculo */
        .spreadsheet-container { max-height: 60vh; overflow: auto; border: none; background: white; position: relative;}
        .table-gsheets { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 13px; }
        
        .gs-column-letters th { background: var(--gs-header-bg); border: 1px solid var(--gs-border); text-align: center; color: var(--gs-muted); font-weight: 500; position: sticky; top: 0; z-index: 10; padding: 4px 0; font-size: 12px; }
        .gs-header-row th { background: var(--gs-header-bg); border: 1px solid var(--gs-border); padding: 8px 10px; font-weight: 500; color: var(--gs-text); white-space: nowrap; position: sticky; top: 25px; z-index: 9; text-align: left; box-shadow: 0 1px 2px rgba(0,0,0,0.05);}
        
        .gs-cell { border: 1px solid var(--gs-border); padding: 6px 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; vertical-align: middle; height: 32px; }
        .gs-number-font { font-family: "Roboto Mono", monospace; letter-spacing: -0.5px; font-size: 12.5px;}
        
        .col-index { position: sticky; left: 0; width: 45px; text-align: center !important; background: var(--gs-header-bg) !important; color: var(--gs-muted) !important; border-right: 1px solid var(--gs-border) !important; border-bottom: 1px solid var(--gs-border); z-index: 15; font-size: 12px;}
        .gs-column-letters .col-index { z-index: 20; top: 0;}
        .gs-header-row .col-index { z-index: 20; top: 25px; }

        .gs-data-row { cursor: crosshair; }
        .gs-data-row:hover td { background: var(--gs-cell-hover); }
        .gs-data-row.active-row td { background-color: var(--gs-active-bg); border-top: 1px solid var(--gs-active-border); border-bottom: 1px solid var(--gs-active-border); }
        .gs-data-row.active-row td:first-child { border-left: 2px solid var(--gs-active-border); background: var(--gs-active-border) !important; color: white !important;}

        .gs-icon-btn { background: transparent; border: none; padding: 2px 6px; font-size: 14px; cursor: pointer; opacity: 0.7;}
        .gs-icon-btn:hover { opacity: 1; }
        .gs-action-cell { z-index: 2;} /* Evitar clic de fila en botones */

        /* Etiquetas de Estado Limpias */
        .gs-status-tag { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 500; line-height: 1.4;}
        .gs-tag-warning { background: #fff8e1; color: #b45309; }
        .gs-tag-success { background: #e6f4ea; color: #137333; }
        .gs-tag-info { background: #e8f0fe; color: #1967d2; }
        .gs-tag-danger { background: #fce8e6; color: #c5221f; }
        .gs-tag-default { background: #f1f3f4; color: #3c4043; }

        /* Scrollbar estilo GSheets */
        .custom-scrollbar::-webkit-scrollbar { width: 12px; height: 12px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f9fa; border-left: 1px solid var(--gs-border); border-top: 1px solid var(--gs-border);}
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 6px; border: 2px solid #f8f9fa;}
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #bdc1c6; }

        /* Minimal Modal */
        .gs-minimal-grid .lbl { font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 2px; }
        .gs-minimal-grid .val { font-size: 14px; color: #222; }
        .gs-minimal-grid .sub-val { font-size: 12px; color: #888; font-family: monospace; }
        .gs-minimal-grid .pl-3 { padding-left: 1rem; border-left: 1px solid #f0f0f0;}
        .gs-btn-flat { font-size: 13px; font-weight: 500; cursor: pointer; }

        .d-only-mobile { display: none; }
        .gs-mobile-card { background: white; border: 1px solid var(--gs-border); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);}

        @media (max-width: 768px) {
            .d-none-mobile { display: none !important; }
            .d-only-mobile { display: block; }
            .kpi-card { margin-bottom: 15px; }
        }
    </style>

    {{-- ========================================== --}}
    {{-- SCRIPTS LÓGICA Y GRÁFICOS --}}
    {{-- ========================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar Tooltips
            var tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function (el) { return new bootstrap.Tooltip(el) });

            // Inicializar Dashboard Chart.js
            initDashboardCharts();

            // Lógica AJAX (Filtros)
            const liveSearch = document.getElementById('liveSearchImputaciones');
            const filterForm = document.getElementById('imputacionFilterForm');
            const ajaxContainer = document.getElementById('ajaxImputacionContainer');
            let debounceTimer;

            const performFilter = async () => {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                ajaxContainer.innerHTML = '<div class="text-center p-5 text-muted"><span class="spinner-border text-primary spinner-border-sm me-2"></span> <i>Recalculando hoja...</i></div>';

                try {
                    const url = `${window.location.pathname}?${params.toString()}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await response.text();
                    
                    setTimeout(() => {
                        ajaxContainer.innerHTML = html;
                        window.history.pushState({}, '', url);
                    }, 200);

                } catch (error) {
                    ajaxContainer.innerHTML = '<div class="alert alert-danger m-3 border-0 bg-white text-danger">Error de conexión con la hoja.</div>';
                }
            };

            if(liveSearch) {
                liveSearch.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(performFilter, 500);
                });
            }

            document.querySelectorAll('.auto-filter-task').forEach(el => el.addEventListener('change', performFilter));

            // Limpiar Iframe al cerrar modal documento
            const modalEl = document.getElementById('modalVisorSoporte');
            if(modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('visorSoporteFrame').src = '';
                });
            }
        });

        // Simular lógica de barra de fórmulas dinámica al hacer clic en celdas
        document.addEventListener('click', function(e) {
            if(e.target.closest('.gs-cell')) {
                const cellText = e.target.innerText;
                document.getElementById('gs-formula-display').innerText = cellText;
            }
        });

        // Función Modal Tarjeta Relaciones
        window.abrirTarjetaRelaciones = function(event, jsonData, rowElement) {
            // Evitar modal si hace click en botones de acción
            if (event.target.closest('.gs-action-cell')) return; 

            // Resaltar fila seleccionada estilo spreadsheet
            document.querySelectorAll('.gs-data-row').forEach(row => row.classList.remove('active-row'));
            rowElement.classList.add('active-row');

            try {
                const data = JSON.parse(jsonData);

                document.getElementById('tarjeta-valor').innerText = '$' + data.valor;
                document.getElementById('tarjeta-estado').innerText = data.estado;
                document.getElementById('tarjeta-tercero').innerText = data.tercero;
                document.getElementById('tarjeta-doc').innerText = 'ID: ' + data.doc_tercero;
                
                // ASIGNAMOS LOS DATOS NUEVOS
                document.getElementById('tarjeta-tipo-imputacion').innerText = data.tipo_imputacion;
                document.getElementById('tarjeta-usuario').innerText = data.usuario;

                document.getElementById('tarjeta-transaccion').innerText = 'Tx-' + data.transaccion;
                document.getElementById('tarjeta-linea').innerText = data.linea;
                document.getElementById('tarjeta-cuota').innerText = data.cuota;
                document.getElementById('tarjeta-concepto').innerText = data.concepto;
                document.getElementById('tarjeta-monto-orig').innerText = data.monto_orig !== 'N/A' ? '$' + data.monto_orig : data.monto_orig;
                document.getElementById('tarjeta-fecha').innerText = data.fecha_pago;
                document.getElementById('tarjeta-cco').innerText = data.cco;
                
                document.getElementById('btnIrFicha').href = data.url_show;

                const btnSoporte = document.getElementById('btnSoporteModal');
                if (data.soporte_url) {
                    btnSoporte.style.display = 'block';
                    btnSoporte.onclick = function() {
                        abrirModalSoporte(data.soporte_url, 'Tx-' + data.transaccion + '.pdf');
                    };
                } else {
                    btnSoporte.style.display = 'none';
                    btnSoporte.onclick = null;
                }

                var modal = new bootstrap.Modal(document.getElementById('modalTarjetaRelaciones'));
                modal.show();

            } catch (error) { console.error("Error celda:", error); }
        };

        // Función Visor PDF Minimalista
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') return;
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            document.getElementById('visorSoporteFrame').src = url;
            
            var modal = new bootstrap.Modal(document.getElementById('modalVisorSoporte'));
            modal.show();
        };

        // Inicialización de Gráficos del Super Dashboard (Chart.js)
        function initDashboardCharts() {
            // Config básica para estética limpia
            Chart.defaults.font.family = "'Roboto', sans-serif";
            Chart.defaults.color = '#888';
            
            const ctxRecaudo = document.getElementById('recaudoChart').getContext('2d');
            new Chart(ctxRecaudo, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [{
                        label: 'Ingresos ($M)',
                        data: [12, 19, 15, 25, 22, 30, 28],
                        borderColor: '#1a73e8',
                        backgroundColor: 'rgba(26, 115, 232, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { border: { display: false }, grid: { color: '#f0f0f0' } },
                        x: { border: { display: false }, grid: { display: false } }
                    }
                }
            });

            const ctxEstado = document.getElementById('estadoChart').getContext('2d');
            new Chart(ctxEstado, {
                type: 'doughnut',
                data: {
                    labels: ['Automático', 'Manual', 'Pendiente'],
                    datasets: [{
                        data: [75, 15, 10],
                        backgroundColor: ['#137333', '#1a73e8', '#f9ab00'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
                    }
                }
            });
        }
    </script>
</x-base-layout>