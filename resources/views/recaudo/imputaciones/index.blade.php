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
                <a href="{{ route('recaudo.imputaciones.create') }}" class="btn-corporate-black">
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
                            <option value="Pendiente" {{ request('estado_conciliacion') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="Conciliado_Auto" {{ request('estado_conciliacion') == 'Conciliado_Auto' ? 'selected' : '' }}>Conciliado Auto</option>
                            <option value="Conciliado_Manual" {{ request('estado_conciliacion') == 'Conciliado_Manual' ? 'selected' : '' }}>Conciliado Manual</option>
                            <option value="Anulado" {{ request('estado_conciliacion') == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                        </select>
                    </div>
                </div>

                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-reset-filters-corp" title="Limpiar todo">
                    <i class="fas fa-redo-alt"></i>
                </a>
            </form>
        </div>

        {{-- Contenedor Maestro para AJAX --}}
        <div id="ajaxImputacionContainer">
            @fragment('imputaciones-list')
            <div class="main-content-area">
                
                {{-- VISTA DESKTOP: Tabla Corporativa Actualizada --}}
                <div class="table-card-container d-none-mobile">
                    <table class="corporate-table">
                        <thead>
                            <tr>
                                <th width="100">Recibo</th>
                                <th>Concepto Contable</th>
                                <th>Transacción</th> 
                                <th>Valor</th>
                                <th>Estado</th>
                                <th>Tercero / Distrito</th>
                                <th width="120" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($imputaciones as $imputacion)
                                <tr class="task-row">
                                    {{-- CLIC EN ID -> VER --}}
                                    <td>
                                        <a href="{{ route('recaudo.imputaciones.show', $imputacion->id) }}" class="id-link-badge" title="Ver Detalle">
                                            #{{ $imputacion->id_recibo }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="task-info-cell">
                                            {{-- CLIC EN CONCEPTO -> EDITAR --}}
                                            <a href="{{ route('recaudo.imputaciones.edit', $imputacion->id) }}" class="task-title-main action-edit-link" title="Editar Imputación">
                                                @if($imputacion->estado_conciliacion == 'Pendiente') <span class="status-dot-pulse"></span> @endif
                                                {{ Str::limit($imputacion->concepto_contable, 45) }}
                                            </a>
                                            <span class="task-desc-sub">
                                                @if($imputacion->link_ecm)
                                                    <i class="fas fa-paperclip text-info"></i> Documento adjunto
                                                @else
                                                    Sin soporte documental
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="#" class="workflow-link-cell" title="Ver Extracto">
                                            <i class="fas fa-university"></i>
                                            <span class="workflow-name-text">Tx: {{ $imputacion->id_transaccion }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($imputacion->valor_imputado, 0) }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $statusStyle = match($imputacion->estado_conciliacion) {
                                                'Pendiente' => 'st-pending',
                                                'Conciliado_Auto', 'Conciliado_Manual' => 'st-completed',
                                                'Anulado' => 'st-danger',
                                                default => 'st-default',
                                            };
                                        @endphp
                                        <span class="badge-status-corp {{ $statusStyle }}">
                                            {{ str_replace('_', ' ', $imputacion->estado_conciliacion) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar-small"><i class="fas fa-user"></i></div>
                                            <div style="display: flex; flex-direction: column; line-height: 1.2;">
                                                <span>{{ $imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen }}</span>
                                                <small class="text-muted">{{ $imputacion->distrito->NOM_DIST ?? $imputacion->id_distrito }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="actions-group">
                                            @if($imputacion->link_ecm)
                                                <a href="{{ $imputacion->link_ecm }}" target="_blank" class="btn-icon-table text-info" title="Ver Soporte ECM"><i class="fas fa-file-pdf"></i></a>
                                            @endif
                                            <a href="{{ route('recaudo.imputaciones.show', $imputacion->id) }}" class="btn-icon-table" title="Consultar"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('recaudo.imputaciones.edit', $imputacion->id) }}" class="btn-icon-table" title="Gestionar"><i class="fas fa-pen-nib"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state-container">
                                        <div class="empty-content">
                                            <i class="fas fa-folder-open"></i>
                                            <p>No se encontraron imputaciones contables.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- VISTA MÓVIL: Cards Intuitivas --}}
                <div class="mobile-tasks-grid d-only-mobile">
                    @forelse($imputaciones as $imputacion)
                        <div class="mobile-task-card">
                            <div class="card-top">
                                <a href="{{ route('recaudo.imputaciones.show', $imputacion->id) }}" class="m-id-tag">Recibo #{{ $imputacion->id_recibo }}</a>
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
                            <div class="card-mid">
                                <a href="{{ route('recaudo.imputaciones.edit', $imputacion->id) }}" class="m-task-title action-edit-link">{{ $imputacion->concepto_contable }}</a>
                                
                                <div class="m-workflow-tag">
                                    <i class="fas fa-university"></i> Transacción: {{ $imputacion->id_transaccion }}
                                </div>
                                
                                <p class="m-task-desc"><strong>Valor:</strong> ${{ number_format($imputacion->valor_imputado, 0) }}</p>
                            </div>
                            <div class="card-bottom">
                                <div class="m-user">
                                    <div class="user-avatar-small"><i class="fas fa-user"></i></div>
                                    <span>{{ Str::limit($imputacion->tercero->nom_ter ?? $imputacion->id_tercero_origen, 15) }}</span>
                                </div>
                                @if($imputacion->link_ecm)
                                    <a href="{{ $imputacion->link_ecm }}" target="_blank" class="text-info"><i class="fas fa-paperclip"></i> Soporte</a>
                                @endif
                            </div>
                        </div>
                    @empty
                         <div class="empty-mobile">No hay registros de imputaciones.</div>
                    @endforelse
                </div>

            </div>

            <footer class="index-footer">
                <div class="pagination-info">
                    Mostrando <strong>{{ $imputaciones->firstItem() }}-{{ $imputaciones->lastItem() }}</strong> de {{ $imputaciones->total() }}
                </div>
                <div class="corporate-pagination">
                    {{ $imputaciones->links() }}
                </div>
            </footer>
            @endfragment
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
            --white: #ffffff;
            --border-soft: #e2e8f0;
            --prio-high: #ef4444;
        }

        body { background-color: var(--bg-page); color: var(--text-main); }
        .task-index-wrapper { max-width: 1450px; margin: 30px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }

        /* Estilos de Workflow Link en Tabla */
        .workflow-link-cell { 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            text-decoration: none;
            color: var(--text-main);
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .workflow-link-cell:hover { 
            background: white;
            border-color: var(--brand-accent);
            color: var(--brand-accent);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .workflow-name-text { font-size: 0.8rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; }
        
        /* Estilo Workflow en Móvil */
        .m-workflow-tag { display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; color: var(--brand-accent); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 10px; border: 1px solid #dbeafe; }

        .id-link-badge { background: #f1f5f9; color: var(--text-main); font-weight: 800; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 0.75rem; transition: all 0.2s; border: 1px solid var(--border-soft); }
        .id-link-badge:hover { background: var(--brand-dark); color: white; border-color: var(--brand-dark); transform: translateY(-1px); }

        .action-edit-link { color: var(--brand-dark); font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.2s; line-height: 1.2; }
        .action-edit-link:hover { color: var(--brand-accent); transform: translateX(3px); }

        /* Header UI */
        .index-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px; }
        .system-tag { font-size: 0.7rem; font-weight: 800; color: var(--brand-accent); text-transform: uppercase; letter-spacing: 0.05em; }
        .main-title { font-size: 2.25rem; font-weight: 800; color: var(--brand-dark); letter-spacing: -0.03em; margin: 5px 0; }
        .main-subtitle { color: var(--text-muted); font-size: 1rem; }

        /* FILTROS */
        .index-toolbar { background: white; padding: 15px 20px; border-radius: 16px; border: 1px solid var(--border-soft); margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .filters-container-corp { display: flex; gap: 15px; align-items: center; }
        .search-input-wrapper { position: relative; flex: 1.5; }
        .search-input-wrapper input { width: 100%; padding: 12px 12px 12px 45px; border-radius: 12px; border: 1px solid var(--border-soft); background: #fdfdfd; font-size: 0.9rem; transition: all 0.3s; }
        .search-input-wrapper input:focus { border-color: var(--brand-accent); background: white; outline: none; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
        .search-input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem; }

        .select-group-corp { display: flex; gap: 10px; flex: 2; }
        .custom-select-wrapper { flex: 1; position: relative; }
        .custom-select-wrapper select { width: 100%; appearance: none; padding: 11px 15px; border-radius: 12px; border: 1px solid var(--border-soft); background: white; font-size: 0.85rem; font-weight: 500; cursor: pointer; color: var(--text-main); transition: border 0.2s; }
        .custom-select-wrapper select:hover { border-color: var(--brand-accent); }

        /* Skeleton Animation */
        .skeleton-row td { padding: 25px 20px; }
        .skeleton-box { height: 15px; background: linear-gradient(90deg, #f0f3f5 25%, #e6e9ef 50%, #f0f3f5 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; border-radius: 6px; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Desktop Table */
        .table-card-container { background: white; border-radius: 16px; border: 1px solid var(--border-soft); box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
        .corporate-table { width: 100%; border-collapse: collapse; }
        .corporate-table th { background: #f8fafc; padding: 16px 20px; text-align: left; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border-soft); }
        .corporate-table td { padding: 18px 20px; border-bottom: 1px solid var(--border-soft); transition: background 0.2s; }
        .task-row:hover { background-color: #f9fbff; }

        /* Badge Status (Adaptados a Imputaciones) */
        .badge-status-corp { padding: 5px 12px; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .st-pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .st-completed { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .st-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        /* Pulse for Pending Priority */
        .status-dot-pulse { height: 8px; width: 8px; background-color: #f59e0b; border-radius: 50%; display: inline-block; margin-right: 10px; box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); animation: pulse-orange 2s infinite; }
        @keyframes pulse-orange { 0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); } 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); } }

        /* Buttons & Actions */
        .btn-corporate-black { background: var(--brand-dark); color: white; padding: 12px 22px; border-radius: 12px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
        .btn-corporate-black:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); background: #000; color: white;}
        .btn-reset-filters-corp { background: #f1f5f9; color: var(--text-muted); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; transition: all 0.3s; }
        .btn-reset-filters-corp:hover { background: #fee2e2; color: #ef4444; transform: rotate(180deg); }
        .btn-icon-table { color: var(--text-muted); font-size: 1.1rem; transition: color 0.2s; text-decoration: none; margin-left: 8px; }
        .btn-icon-table:hover { color: var(--brand-accent); }

        /* User Cell Style */
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-avatar-small { width: 32px; height: 32px; border-radius: 50%; background: var(--border-soft); display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-weight: bold; }

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
            .d-none-mobile { display: none; }
            .d-only-mobile { display: block; }
            .mobile-task-card { background: white; border: 1px solid var(--border-soft); border-radius: 20px; padding: 20px; margin-bottom: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
            .m-task-title { font-size: 1.15rem; margin-bottom: 8px; display: block; }
            .card-top { display: flex; justify-content: space-between; margin-bottom: 15px; }
            .card-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9; }
            .m-user { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 0.85rem;}
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearch = document.getElementById('liveSearchImputaciones');
            const filterForm = document.getElementById('imputacionFilterForm');
            const ajaxContainer = document.getElementById('ajaxImputacionContainer');
            let debounceTimer;

            const showSkeleton = () => {
                const skeleton = `
                    <div class="table-card-container">
                        <table class="corporate-table">
                            <tbody>
                                ${Array(5).fill(`
                                    <tr class="skeleton-row">
                                        <td><div class="skeleton-box" style="width: 60px"></div></td>
                                        <td><div class="skeleton-box" style="width: 200px"></div></td>
                                        <td><div class="skeleton-box" style="width: 120px"></div></td>
                                        <td><div class="skeleton-box" style="width: 80px"></div></td>
                                        <td><div class="skeleton-box" style="width: 100px"></div></td>
                                        <td><div class="skeleton-box" style="width: 150px"></div></td>
                                        <td><div class="skeleton-box" style="width: 60px"></div></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                ajaxContainer.innerHTML = skeleton;
            };

            const performFilter = async () => {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                showSkeleton();

                try {
                    const url = `${window.location.pathname}?${params.toString()}`;
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await response.text();
                    
                    setTimeout(() => {
                        ajaxContainer.innerHTML = html;
                        window.history.pushState({}, '', url);
                    }, 300);

                } catch (error) {
                    console.error('Error:', error);
                    ajaxContainer.innerHTML = '<div class="error-msg text-center p-4">Error de conexión. Reintente.</div>';
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
    </script>
</x-base-layout>