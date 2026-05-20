<x-base-layout>
    <div class="task-index-wrapper">
        {{-- Encabezado Ejecutivo --}}
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Módulo de Recaudo</span>
                <h1 class="main-title">Control de Imputaciones Contables</h1>
                <p class="main-subtitle">Gestión centralizada de recibos, transacciones y conciliaciones.</p>
            </div>
            
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.create') }}" class="btn-corporate-black shadow-sm">
                    <i class="fas fa-plus"></i> <span class="d-none-mobile">Nueva Imputación</span>
                </a>
            </div>
        </header>

        {{-- Toolbar de Filtrado Avanzado --}}
        <div class="index-toolbar">
            <form action="{{ route('recaudo.imputaciones.index') }}" method="GET" id="imputacionFilterForm" class="filters-container-corp">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="liveSearchImputaciones" placeholder="Buscar por concepto, ID recibo o tercero..." value="{{ request('search') }}" autocomplete="off">
                </div>

                <div class="select-group-corp scroll-x-mobile">
                    <div class="custom-select-wrapper">
                        <select name="estado_conciliacion" class="auto-filter-task">
                            <option value="">Cualquier Estado</option>
                            <option value="Pendiente" {{ request('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                            <option value="Conciliado_Auto" {{ request('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>🤖 Conciliado Auto</option>
                            <option value="Conciliado_Manual" {{ request('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>👤 Conciliado Manual</option>
                            <option value="Anulado" {{ request('estado_conciliacion') == 'Anulado' ? 'selected' : '' }}>❌ Anulado</option>
                        </select>
                    </div>
                </div>

                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-reset-filters-corp" data-bs-toggle="tooltip" title="Limpiar todo">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </form>
        </div>

        {{-- Contenedor Maestro para AJAX --}}
        <div id="ajaxImputacionContainer">
            @fragment('imputaciones-list')
            <div class="main-content-area">
                
                {{-- VISTA DESKTOP: HOJA DE CÁLCULO (SPREADSHEET) --}}
                <div class="spreadsheet-container d-none-mobile custom-scrollbar bg-white border border-gray-200 shadow-sm rounded-4 overflow-hidden mb-4">
                    <table class="table-gsheets align-middle uniform-text-table" id="main-table">
                        <thead>
                            <tr>
                                <th class="col-index text-center"><i class="fas fa-list-ol text-muted opacity-50"></i></th>
                                <th style="width: 110px;">N° Recibo</th>
                                <th style="width: 140px;">Transacción</th>
                                <th style="width: 220px;">Tercero / Origen</th>
                                <th style="width: 130px;">Distrito</th>
                                <th style="width: 170px;">Línea / Obligación</th>
                                <th style="width: 90px;" class="text-center">Cuota</th>
                                <th style="width: 280px;">Concepto Contable</th>
                                <th style="width: 140px;" class="text-end">Monto ($)</th>
                                <th style="width: 160px;" class="text-center">Estado</th>
                                <th style="width: 90px;" class="text-center">Soporte</th>
                                <th style="width: 100px;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($imputaciones as $index => $imputacion)
                                {{-- 
                                    Preparamos el JSON para la tarjeta de relaciones. 
                                    La fila entera es clickeable.
                                --}}
                                @php
                                    $fichaData = json_encode([
                                        'recibo' => $imputacion->id_recibo,
                                        'transaccion' => $imputacion->id_transaccion,
                                        'tercero' => $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen,
                                        'doc_tercero' => $imputacion->id_tercero_origen,
                                        'distrito' => $imputacion->distrito->NOM_DIST ?? $imputacion->id_distrito,
                                        'linea' => $imputacion->obligacion->nombre ?? 'N/A',
                                        'cuota' => $imputacion->numero_cuota ?? 'N/A',
                                        'concepto' => $imputacion->concepto_contable,
                                        'valor' => number_format($imputacion->valor_imputado, 0, ',', '.'),
                                        'estado' => str_replace('_', ' ', $imputacion->estado_conciliacion),
                                        'url_show' => route('recaudo.imputaciones.show', $imputacion->id)
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT);
                                @endphp

                                <tr class="data-row row-clickable" onclick="abrirTarjetaRelaciones(event, '{{ $fichaData }}')">
                                    {{-- N° de Fila --}}
                                    <td class="col-index text-muted fw-bold">{{ $imputaciones->firstItem() + $index }}</td>
                                    
                                    {{-- Recibo --}}
                                    <td>
                                        <span class="font-monospace fw-bolder text-primary">#{{ $imputacion->id_recibo }}</span>
                                    </td>

                                    {{-- ID Transacción --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2 font-monospace text-gray-800">
                                            <i class="fas fa-university text-muted"></i> 
                                            <span class="text-truncate fw-bold" style="max-width: 100px;" title="{{ $imputacion->id_transaccion }}">Tx-{{ $imputacion->id_transaccion }}</span>
                                        </div>
                                    </td>

                                    {{-- Tercero --}}
                                    <td>
                                        <div class="text-truncate fw-bolder text-gray-900" style="max-width: 200px;" title="{{ $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen }}">
                                            {{ $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen }}
                                        </div>
                                    </td>

                                    {{-- Distrito --}}
                                    <td>
                                        <span class="badge bg-light text-dark border border-secondary fw-bolder px-2 py-1">
                                            {{ $imputacion->distrito->NOM_DIST ?? $imputacion->id_distrito }}
                                        </span>
                                    </td>

                                    {{-- Línea / Obligación --}}
                                    <td>
                                        <div class="text-truncate fw-bolder text-primary" style="max-width: 150px;" title="{{ $imputacion->obligacion->nombre ?? 'N/A' }}">
                                            {{ $imputacion->obligacion->nombre ?? 'N/A' }}
                                        </div>
                                    </td>

                                    {{-- Cuota N° --}}
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border border-gray-400 fw-bolder px-3 py-1 shadow-sm">
                                            {{ $imputacion->numero_cuota ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Concepto Contable --}}
                                    <td>
                                        <div class="text-truncate text-gray-800 fw-semibold" style="max-width: 260px;" title="{{ $imputacion->concepto_contable }}">
                                            @if($imputacion->estado_conciliacion == 'Pendiente') 
                                                <span class="status-dot-pulse"></span> 
                                            @endif
                                            {{ $imputacion->concepto_contable }}
                                        </div>
                                    </td>

                                    {{-- Valor --}}
                                    <td class="text-end">
                                        <span class="font-monospace fw-bolder text-gray-900">
                                            ${{ number_format($imputacion->valor_imputado, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="text-center">
                                        @php
                                            $estadoData = match($imputacion->estado_conciliacion) {
                                                'Pendiente'         => ['class' => 'bg-light-warning text-warning border-warning', 'icon' => 'fa-clock'],
                                                'Conciliado_Auto'   => ['class' => 'bg-light-success text-success border-success', 'icon' => 'fa-robot'],
                                                'Conciliado_Manual' => ['class' => 'bg-light-primary text-primary border-primary', 'icon' => 'fa-user-check'],
                                                'Anulado'           => ['class' => 'bg-light-danger text-danger border-danger', 'icon' => 'fa-times'],
                                                default             => ['class' => 'bg-light text-muted border-gray-300', 'icon' => 'fa-question'],
                                            };
                                        @endphp
                                        <span class="badge {{ $estadoData['class'] }} border border-opacity-25 fw-bolder px-2 py-1 rounded-1 w-100 text-uppercase" style="letter-spacing: -0.2px;">
                                            <i class="fas {{ $estadoData['icon'] }} me-1"></i> {{ str_replace('Conciliado_', 'Conc. ', $imputacion->estado_conciliacion) }}
                                        </span>
                                    </td>

                                    {{-- Soporte --}}
                                    <td class="text-center action-cell">
                                        @if($imputacion->link_ecm)
                                            <button type="button" onclick="abrirModalSoporte('{{ $imputacion->link_ecm }}', 'Soporte Recibo #{{ $imputacion->id_recibo }}')" class="btn btn-icon btn-light-info w-35px h-35px rounded-2 border border-info border-opacity-25 shadow-sm" data-bs-toggle="tooltip" title="Ver Soporte">
                                                <i class="fas fa-file-pdf text-info"></i>
                                            </button>
                                        @else
                                            <span class="text-gray-400 fw-bold">-</span>
                                        @endif
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="text-center action-cell">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('recaudo.imputaciones.edit', $imputacion->id) }}" class="btn btn-icon btn-light-primary w-35px h-35px rounded-2 border border-primary border-opacity-25 shadow-sm" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-10 bg-light">
                                        <div class="empty-content text-muted">
                                            <i class="fas fa-folder-open fs-2 mb-3 d-block text-gray-400"></i>
                                            <p class="fw-bold fs-5 mb-0">No se encontraron imputaciones contables.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VISTA MÓVIL (Con Texto Unificado) --}}
                <div class="mobile-tasks-grid d-only-mobile">
                    @forelse($imputaciones as $imputacion)
                        <div class="mobile-task-card shadow-sm border border-gray-200" onclick="window.location='{{ route('recaudo.imputaciones.show', $imputacion->id) }}'">
                            <div class="card-top">
                                <span class="m-id-tag font-monospace">#{{ $imputacion->id_recibo }}</span>
                                @php
                                    $statusStyle = match($imputacion->estado_conciliacion) {
                                        'Pendiente' => 'st-pending',
                                        'Conciliado_Auto', 'Conciliado_Manual' => 'st-completed',
                                        'Anulado' => 'st-danger',
                                        default => 'st-default',
                                    };
                                @endphp
                                <span class="badge-status-corp {{ $statusStyle }}">{{ str_replace('_', ' ', $imputacion->estado_conciliacion) }}</span>
                            </div>
                            <div class="card-mid mt-3">
                                <span class="m-task-title fw-bolder text-dark">{{ $imputacion->concepto_contable }}</span>
                                
                                <div class="m-workflow-tag mt-2">
                                    <i class="fas fa-university"></i> Ext: {{ $imputacion->id_transaccion }}
                                </div>
                                
                                <div class="mt-2">
                                    <span class="badge bg-light text-primary border border-gray-300 fw-bolder px-2 py-1" style="font-size: 14px;">
                                        Línea: {{ $imputacion->obligacion->nombre ?? 'N/A' }} | Cuota: {{ $imputacion->numero_cuota ?? '-' }}
                                    </span>
                                </div>
                                
                                <p class="m-task-desc mt-2" style="font-size: 15px;"><strong>Valor:</strong> ${{ number_format($imputacion->valor_imputado, 0, ',', '.') }}</p>
                            </div>
                            <div class="card-bottom mt-3 pt-3 border-top border-gray-100">
                                <div class="m-user">
                                    <i class="fas fa-user-circle text-gray-400 fs-3"></i>
                                    <span style="font-size: 15px;">{{ Str::limit($imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen, 20) }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                         <div class="empty-mobile text-center p-5 text-muted bg-light rounded-4 border border-gray-200">No hay registros de imputaciones.</div>
                    @endforelse
                </div>

            </div>

            <footer class="index-footer bg-white p-4 rounded-4 border border-gray-200 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="pagination-info text-muted fw-semibold bg-light px-3 py-2 rounded-pill" style="font-size: 15px;">
                    Mostrando <strong class="text-primary mx-1">{{ $imputaciones->firstItem() ?? 0 }}-{{ $imputaciones->lastItem() ?? 0 }}</strong> de <strong class="text-dark mx-1">{{ $imputaciones->total() }}</strong>
                </div>
                <div class="corporate-pagination m-0">
                    {{ $imputaciones->links('pagination::bootstrap-4') }}
                </div>
            </footer>
            @endfragment
        </div>
    </div>

    {{-- MODAL 1: TARJETA DE RELACIONES (Al hacer clic en la fila) --}}
    <div class="modal fade" id="modalTarjetaRelaciones" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-light-primary border-bottom border-primary border-opacity-10 py-4 px-5">
                    <h5 class="modal-title fw-bolder text-gray-900 d-flex align-items-center gap-3 m-0 fs-4">
                        <i class="fas fa-project-diagram text-primary fs-3"></i> 
                        Resumen de Imputación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    
                    {{-- Bloque Cifras Principales --}}
                    <div class="d-flex justify-content-between align-items-center bg-light-success p-4 rounded-3 border border-success border-opacity-25 mb-4">
                        <div>
                            <span class="d-block text-success fw-bolder text-uppercase fs-8 ls-1 mb-1">Valor Imputado</span>
                            <span class="fs-1 fw-bolder text-gray-900 lh-1" id="tarjeta-valor">$0</span>
                        </div>
                        <div class="text-end">
                            <span class="d-block text-muted fw-bold text-uppercase fs-8 ls-1 mb-1">Estado</span>
                            <span class="badge bg-dark text-white fs-6 px-3 py-1" id="tarjeta-estado">Estado</span>
                        </div>
                    </div>

                    {{-- Grid de Relaciones --}}
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <span class="text-muted fw-bold d-block text-uppercase fs-8 ls-1 mb-1"><i class="fas fa-user text-gray-400 me-1"></i> Tercero / Cliente</span>
                            <span class="text-gray-900 fw-bolder fs-5 d-block" id="tarjeta-tercero">---</span>
                            <span class="text-muted font-monospace fs-7" id="tarjeta-doc">---</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fw-bold d-block text-uppercase fs-8 ls-1 mb-1"><i class="fas fa-university text-gray-400 me-1"></i> Transacción Origen</span>
                            <span class="text-gray-900 fw-bolder font-monospace fs-5 d-block" id="tarjeta-transaccion">---</span>
                        </div>
                        
                        <div class="col-12 border-top border-gray-200 pt-4 mt-2"></div>

                        <div class="col-6">
                            <span class="text-muted fw-bold d-block text-uppercase fs-8 ls-1 mb-1"><i class="fas fa-layer-group text-primary me-1"></i> Línea / Obligación</span>
                            <span class="text-primary fw-bolder fs-5" id="tarjeta-linea">---</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fw-bold d-block text-uppercase fs-8 ls-1 mb-1"><i class="fas fa-hashtag text-gray-400 me-1"></i> Cuota</span>
                            <span class="badge bg-light-dark text-dark border border-gray-400 fs-5 px-3 py-1" id="tarjeta-cuota">---</span>
                        </div>

                        <div class="col-12">
                            <span class="text-muted fw-bold d-block text-uppercase fs-8 ls-1 mb-1"><i class="fas fa-align-left text-gray-400 me-1"></i> Concepto Contable</span>
                            <p class="text-gray-800 fw-semibold fs-6 m-0 bg-light p-3 rounded" id="tarjeta-concepto">---</p>
                        </div>
                    </div>

                    <a href="#" id="btnIrFicha" class="btn btn-primary w-100 fw-bolder py-3 fs-5">
                        <i class="fas fa-external-link-alt me-2"></i> Ver Ficha Técnica Completa
                    </a>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 2: VISOR DE SOPORTES S3 --}}
    <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light-primary border-bottom border-primary border-opacity-10 py-3 px-4">
                    <h5 class="modal-title fw-bolder text-gray-800 d-flex align-items-center gap-2 m-0 fs-5">
                        <i class="fas fa-file-pdf text-primary fs-3"></i> 
                        <span id="visorSoporteTitulo">Soporte Documental</span>
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <a href="#" id="btnDescargarSoporte" target="_blank" class="btn btn-sm btn-primary fw-bolder" data-bs-toggle="tooltip" title="Abrir en pestaña nueva">
                            <i class="fas fa-external-link-alt me-2"></i> Abrir Externo
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-dark position-relative" style="height: 75vh;">
                    <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0 bg-white"></iframe>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        :root {
            --brand-dark: #0f172a;
            --brand-accent: #2563eb;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --gs-border: #e2e8f0;
            --gs-header-bg: #f1f5f9;
            --gs-hover-bg: #f8fafc;
        }

        body { background-color: var(--bg-page); color: var(--text-main); font-family: 'Inter', sans-serif; }
        .task-index-wrapper { max-width: 1550px; margin: 30px auto; padding: 0 20px; }

        /* SPREADSHEET STYLES - TAMAÑO DE TEXTO UNIFICADO (15px) */
        .spreadsheet-container { min-height: 50vh; overflow-x: auto; }
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; border: 2px solid white;}
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .table-gsheets { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed; margin-bottom: 0; }
        .table-gsheets thead { position: sticky; top: 0; z-index: 20; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .table-gsheets thead th { background: var(--gs-header-bg); border-bottom: 2px solid var(--gs-border); border-right: 1px solid var(--gs-border); padding: 14px 16px; font-weight: 800; color: #475569; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; white-space: nowrap;}
        
        /* LA MAGIA DE LA UNIFICACIÓN DE TAMAÑO: 15px GLOBAL PARA CELDAS Y SUS HIJOS */
        .uniform-text-table tbody td { 
            border-bottom: 1px solid var(--gs-border); 
            border-right: 1px solid var(--gs-border); 
            padding: 14px 16px; 
            vertical-align: middle; 
            white-space: nowrap; 
            overflow: hidden; 
            text-overflow: ellipsis; 
            background-color: #ffffff; 
            transition: background 0.15s ease; 
            font-size: 15px !important; /* TAMAÑO PRINCIPAL */
            color: #334155;
        }
        .uniform-text-table tbody td span, 
        .uniform-text-table tbody td div, 
        .uniform-text-table tbody td a, 
        .uniform-text-table tbody td i {
            font-size: 15px !important; /* FUERZA TAMAÑO UNIFORME EN ELEMENTOS INTERNOS */
        }
        
        /* Fila Clickable */
        .row-clickable { cursor: pointer; }
        .row-clickable:hover td { background-color: #f1f5f9 !important; } /* Efecto hover gris claro */

        .col-index { width: 45px; text-align: center !important; background: var(--gs-header-bg) !important; color: #64748b; border-right: 2px solid var(--gs-border) !important; }

        /* Header UI */
        .index-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .system-tag { font-size: 0.8rem; font-weight: 800; color: var(--brand-accent); text-transform: uppercase; letter-spacing: 0.05em; background: #eff6ff; padding: 4px 10px; border-radius: 6px;}
        .main-title { font-size: 2.25rem; font-weight: 800; color: var(--brand-dark); letter-spacing: -0.03em; margin: 8px 0; }
        .main-subtitle { color: var(--text-muted); font-size: 1.1rem; }

        /* FILTROS */
        .index-toolbar { background: white; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--gs-border); margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .filters-container-corp { display: flex; gap: 15px; align-items: center; }
        .search-input-wrapper { position: relative; flex: 1.5; }
        .search-input-wrapper input { width: 100%; padding: 12px 12px 12px 45px; border-radius: 10px; border: 1px solid var(--gs-border); background: #fdfdfd; font-size: 1rem; font-weight: 500; transition: all 0.3s; }
        .search-input-wrapper input:focus { border-color: var(--brand-accent); background: white; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .search-input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.2rem; }

        .select-group-corp { display: flex; gap: 10px; flex: 2; }
        .custom-select-wrapper { flex: 1; position: relative; }
        .custom-select-wrapper select { width: 100%; appearance: none; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--gs-border); background: white; font-size: 0.95rem; font-weight: 600; cursor: pointer; color: var(--text-main); transition: border 0.2s; }
        .custom-select-wrapper select:hover { border-color: var(--brand-accent); }

        /* Badge Status */
        .badge-status-corp { padding: 6px 12px; border-radius: 8px; font-weight: 800; text-transform: uppercase; }
        .st-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .st-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .st-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Pulse */
        .status-dot-pulse { height: 10px; width: 10px; background-color: #f59e0b; border-radius: 50%; display: inline-block; margin-right: 8px; box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); animation: pulse-orange 2s infinite; }
        @keyframes pulse-orange { 0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); } 70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); } 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); } }

        /* Buttons */
        .btn-corporate-black { background: var(--brand-dark); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .btn-corporate-black:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.15); background: #000; color: white;}
        .btn-reset-filters-corp { background: #f1f5f9; color: var(--text-muted); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: all 0.3s; }
        .btn-reset-filters-corp:hover { background: #fee2e2; color: #ef4444; transform: rotate(180deg); }
        .hover-elevate-up { transition: transform 0.2s ease; }
        .hover-elevate-up:hover { transform: translateY(-2px); }

        /* Mobile View */
        .d-only-mobile { display: none; }
        @media (max-width: 992px) {
            .filters-container-corp { flex-direction: column; align-items: stretch; }
            .select-group-corp { flex-wrap: wrap; }
            .index-header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .header-actions { width: 100%; }
            .header-actions a { flex: 1; justify-content: center; }
        }

        @media (max-width: 768px) {
            .d-none-mobile { display: none !important; }
            .d-only-mobile { display: block; }
            .mobile-task-card { background: white; border-radius: 16px; padding: 20px; margin-bottom: 15px;}
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearch = document.getElementById('liveSearchImputaciones');
            const filterForm = document.getElementById('imputacionFilterForm');
            const ajaxContainer = document.getElementById('ajaxImputacionContainer');
            let debounceTimer;

            // Limpiar Iframe al cerrar Modal (Vanilla JS Event)
            const modalEl = document.getElementById('modalVisorSoporte');
            if(modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('visorSoporteFrame').src = '';
                });
            }

            // Inicializar Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Función Ajax
            const performFilter = async () => {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                ajaxContainer.innerHTML = '<div class="text-center p-5 text-muted"><span class="spinner-border text-primary" style="width:3rem;height:3rem;"></span><p class="mt-3 fs-5 fw-bold text-dark">Cargando datos...</p></div>';

                try {
                    const url = `${window.location.pathname}?${params.toString()}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await response.text();
                    
                    setTimeout(() => {
                        ajaxContainer.innerHTML = html;
                        window.history.pushState({}, '', url);
                        
                        // Reinicializar tooltips
                        var newTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                        newTooltips.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl)
                        });
                    }, 350);

                } catch (error) {
                    console.error('Error:', error);
                    ajaxContainer.innerHTML = '<div class="alert alert-danger m-4 border-dashed"><i class="fas fa-exclamation-circle me-2"></i> Error de conexión. Reintente.</div>';
                }
            };

            if(liveSearch) {
                liveSearch.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(performFilter, 500);
                });
            }

            document.querySelectorAll('.auto-filter-task').forEach(el => {
                el.addEventListener('change', performFilter);
            });
        });

        // =========================================================
        // FUNCIÓN 1: ABRIR LA TARJETA DE RELACIONES (MODAL) AL CLIC
        // =========================================================
        window.abrirTarjetaRelaciones = function(event, jsonData) {
            // Evitar que abra si se hizo clic en un botón interno (Editar o PDF)
            if (event.target.closest('.action-cell')) {
                return; 
            }

            try {
                // Decodificamos el JSON
                const data = JSON.parse(jsonData);

                // Llenamos la información de la tarjeta
                document.getElementById('tarjeta-valor').innerText = '$' + data.valor;
                document.getElementById('tarjeta-estado').innerText = data.estado;
                document.getElementById('tarjeta-tercero').innerText = data.tercero;
                document.getElementById('tarjeta-doc').innerText = 'CC/NIT: ' + data.doc_tercero;
                document.getElementById('tarjeta-transaccion').innerText = 'Tx-' + data.transaccion;
                document.getElementById('tarjeta-linea').innerText = data.linea;
                document.getElementById('tarjeta-cuota').innerText = data.cuota;
                document.getElementById('tarjeta-concepto').innerText = data.concepto;
                
                // Actualizamos el enlace del botón de Ver Ficha
                document.getElementById('btnIrFicha').href = data.url_show;

                // Abrimos el Modal de la Tarjeta (A prueba de fallos BS4/BS5)
                var modalDOM = document.getElementById('modalTarjetaRelaciones');
                try {
                    var modal = bootstrap.Modal.getInstance(modalDOM) || new bootstrap.Modal(modalDOM);
                    modal.show();
                } catch (err) {
                    if (typeof jQuery !== 'undefined') { jQuery('#modalTarjetaRelaciones').modal('show'); }
                }

            } catch (error) {
                console.error("Error al leer datos de la fila:", error);
            }
        };

        // =========================================================
        // FUNCIÓN 2: ABRIR EL SOPORTE EN PDF (MODAL)
        // =========================================================
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') {
                alert('El documento no está disponible.');
                return;
            }
            
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            document.getElementById('visorSoporteFrame').src = url;
            
            // Abrimos el Modal del PDF (A prueba de fallos BS4/BS5)
            var modalDOM = document.getElementById('modalVisorSoporte');
            try {
                var modal = bootstrap.Modal.getInstance(modalDOM) || new bootstrap.Modal(modalDOM);
                modal.show();
            } catch (err) {
                if (typeof jQuery !== 'undefined') {
                    jQuery('#modalVisorSoporte').modal('show');
                } else {
                    window.open(url, '_blank');
                }
            }
        };
    </script>
</x-base-layout>