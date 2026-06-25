<x-base-layout>
    <style>
        :root {
            --brand-color: #0f172a;
            --brand-surface: #ffffff;
            --accent-primary: #2563eb;
            --accent-hover: #1d4ed8;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-subtle: #f1f5f9;
            --border-main: #e2e8f0;
        }
        
        body { background-color: #f8fafc; color: var(--text-main); font-family: 'Inter', system-ui, sans-serif; }
        
        /* Tarjetas (Cards) */
        .card-ux { 
            background: var(--brand-surface); 
            border: 1px solid var(--border-main); 
            border-radius: 12px; 
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            transition: all 0.2s ease;
        }
        .card-ux:hover { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
        
        /* Tipografía Avanzada */
        .text-overline { font-size: 0.75rem; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); font-weight: 700; }
        .text-body-sm { font-size: 0.875rem; color: var(--text-muted); }
        
        /* Tabla UX */
        .table-ux { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-ux th { 
            background: #f8fafc; padding: 1rem 1.25rem; font-size: 0.8125rem; font-weight: 600; 
            color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.025em; 
            border-bottom: 2px solid var(--border-main); white-space: nowrap;
        }
        .table-ux td { 
            padding: 1rem 1.25rem; vertical-align: middle; border-bottom: 1px solid var(--border-subtle); 
            transition: background-color 0.15s ease;
        }
        .table-ux tr:last-child td { border-bottom: none; }
        .table-ux tbody tr:hover td { background-color: #f1f5f9; cursor: pointer; }
        
        /* Badges & Indicadores */
        .badge-ux { 
            display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; 
            border-radius: 9999px; font-size: 0.75rem; font-weight: 600; gap: 0.375rem;
        }
        .badge-ux.active { background-color: #dcfce7; color: #166534; }
        .badge-ux.inactive { background-color: #fee2e2; color: #991b1b; }
        .badge-ux.area { background-color: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }

        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .status-dot.active { background-color: #22c55e; }
        .status-dot.inactive { background-color: #ef4444; }

        /* Avatares */
        .avatar-group { display: flex; align-items: center; }
        .avatar-ux { 
            width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; color: #475569;
            display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600;
            border: 2px solid #ffffff; margin-left: -10px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .avatar-ux:first-child { margin-left: 0; }
        
        /* Barra de acciones masivas */
        #bulk-actions-bar {
            position: fixed; bottom: 2rem; left: 50%; transform: translateX(-50%) translateY(120%);
            opacity: 0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); z-index: 1050;
            background: var(--brand-color); color: white; padding: 0.875rem 2rem; border-radius: 999px;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        #bulk-actions-bar.show { transform: translateX(-50%) translateY(0); opacity: 1; }

        /* Botones Base */
        .btn-ux { border-radius: 8px; font-weight: 500; padding: 0.5rem 1rem; transition: all 0.2s; }
        .btn-ux-primary { background: var(--accent-primary); color: white; border: none; }
        .btn-ux-primary:hover { background: var(--accent-hover); transform: translateY(-1px); }
        .btn-ux-icon { background: #fff; border: 1px solid var(--border-main); color: var(--text-main); }
        .btn-ux-icon:hover { background: #f1f5f9; }
    </style>

    <div class="container-fluid py-5 px-lg-5 max-w-7xl mx-auto">
        
        <div class="row align-items-end mb-4 g-3">
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-body-sm">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Correspondencia</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Procesos</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.025em;">Gestión de Procesos</h2>
            </div>
            
            <div class="col-12 col-md-6 d-flex justify-content-md-end gap-2 align-items-center">
                <form action="{{ route('correspondencia.procesos.index') }}" method="GET" class="position-relative flex-grow-1 flex-md-grow-0" style="max-width: 250px;">
                    <i class="fas fa-search position-absolute text-muted" style="top: 12px; left: 14px;"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control btn-ux border-main ps-5" placeholder="Buscar ID o nombre...">
                </form>
                
                <button class="btn btn-ux btn-ux-icon d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtrosPanel">
                    <i class="fas fa-sliders-h"></i> <span class="d-none d-sm-inline">Filtros</span>
                </button>
                <a href="{{ route('correspondencia.procesos.create') }}" class="btn btn-ux btn-ux-primary d-flex align-items-center gap-2">
                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Nuevo Proceso</span>
                </a>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-ux p-4 h-100 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-overline">Total Procesos</span>
                        <i class="fas fa-layer-group text-muted opacity-50"></i>
                    </div>
                    <div class="fs-2 fw-bold text-dark">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-ux p-4 h-100 d-flex flex-column justify-content-center" style="border-bottom: 4px solid #22c55e;">
                    <div class="text-overline text-success mb-2">Activos</div>
                    <div class="fs-2 fw-bold text-dark">{{ number_format($stats['activos']) }}</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-ux p-4 h-100 d-flex flex-column justify-content-center" style="border-bottom: 4px solid #ef4444;">
                    <div class="text-overline text-danger mb-2">Inactivos / Pausados</div>
                    <div class="fs-2 fw-bold text-dark">{{ number_format($stats['inactivos']) }}</div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card-ux p-4 h-100 bg-light d-flex flex-column justify-content-center border-0">
                    <div class="text-overline mb-2">Índice de Actividad</div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-2 fw-bold">{{ $stats['total'] > 0 ? round(($stats['activos'] / $stats['total']) * 100) : 0 }}%</div>
                        <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" style="width: {{ $stats['total'] > 0 ? ($stats['activos'] / $stats['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="bulkForm" action="{{ route('correspondencia.procesos.bulk') }}" method="POST">
            @csrf
            <input type="hidden" name="action" id="bulkActionInput" value="">
            
            <div class="card-ux overflow-hidden">
                <div class="table-responsive">
                    <table class="table-ux">
                        <thead>
                            <tr>
                                <th style="width: 48px;" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="selectAll" style="cursor: pointer;">
                                </th>
                                <th>Detalle del Proceso</th>
                                <th>Área y Flujo</th>
                                <th>Estado</th>
                                <th>Equipo Asignado</th>
                                <th>Creación</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($procesos as $proceso)
                            <tr onclick="window.location='{{ route('correspondencia.procesos.show', $proceso) }}'">
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <input type="checkbox" name="ids[]" value="{{ $proceso->id }}" class="form-check-input row-checkbox" style="cursor: pointer;">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded p-2 text-muted d-none d-md-block">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $proceso->nombre }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem; font-family: monospace;">
                                                ID: {{ str_pad($proceso->id, 5, '0', STR_PAD_LEFT) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-1">
                                        <span class="badge-ux area">
                                            <i class="fas fa-building text-primary opacity-75"></i> 
                                            {{ $proceso->flujo?->area?->nombre ?? 'Sin Área Asignada' }} 
                                        </span>
                                    </div>
                                    <div class="text-body-sm text-truncate" style="max-width: 200px;" title="{{ $proceso->flujo?->nombre ?? 'Sin flujo definido' }}">
                                        {{ $proceso->flujo?->nombre ?? 'Sin flujo definido' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-ux {{ $proceso->activo ? 'active' : 'inactive' }}">
                                        <span class="status-dot {{ $proceso->activo ? 'active' : 'inactive' }}"></span>
                                        {{ $proceso->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="avatar-group">
                                        @forelse($proceso->usuariosAsignados->take(4) as $user)
                                            <div class="avatar-ux" data-bs-toggle="tooltip" title="{{ $user->usuario->name ?? 'Usuario Desconocido' }}">
                                                {{ strtoupper(substr($user->usuario->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @empty
                                            <span class="text-muted" style="font-size: 0.8rem; font-style: italic;">Sin asignar</span>
                                        @endforelse
                                        @if($proceso->usuariosAsignados->count() > 4)
                                            <div class="avatar-ux bg-light text-muted border-secondary">
                                                +{{ $proceso->usuariosAsignados->count() - 4 }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium" style="font-size: 0.875rem;">{{ $proceso->created_at->format('d M, Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $proceso->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light rounded-circle" type="button" onclick="event.stopPropagation();" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" onclick="event.stopPropagation();">
                                        <li><a class="dropdown-item py-2" href="{{ route('correspondencia.procesos.show', $proceso) }}"><i class="fas fa-eye w-20px text-center text-primary me-2"></i> Ver Detalles</a></li>
                                        <li><a class="dropdown-item py-2" href="#"><i class="fas fa-edit w-20px text-center text-warning me-2"></i> Editar Proceso</a></li>
                                    </ul>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="fas fa-search-minus text-muted fs-1 opacity-50"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">No se encontraron procesos</h5>
                                        <p class="text-muted mb-4" style="max-width: 400px;">No hay datos que coincidan con tu búsqueda actual o aún no has creado ningún proceso operativo.</p>
                                        <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-ux btn-ux-icon">Limpiar Filtros</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 d-flex justify-content-end">
                {{ $procesos->links('pagination::bootstrap-5') }}
            </div>
        </form>
    </div>

    <div id="bulk-actions-bar" class="d-flex align-items-center justify-content-center gap-4">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-white text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem;">
                <span id="selectedCount">0</span>
            </div>
            <span class="fw-medium">Procesos seleccionados</span>
        </div>
        <div class="vr bg-light opacity-25"></div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success px-3 fw-bold border-0 d-flex align-items-center gap-2" onclick="submitBulk('activate')">
                <i class="fas fa-play"></i> Activar
            </button>
            <button type="button" class="btn btn-sm btn-danger px-3 fw-bold border-0 d-flex align-items-center gap-2" onclick="submitBulk('deactivate')">
                <i class="fas fa-pause"></i> Inactivar
            </button>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="filtrosPanel" aria-labelledby="filtrosLabel">
        <div class="offcanvas-header border-bottom border-subtle">
            <h5 class="offcanvas-title fw-bold" id="filtrosLabel"><i class="fas fa-filter me-2 text-primary"></i> Filtros Avanzados</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('correspondencia.procesos.index') }}" method="GET">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                <div class="mb-4">
                    <label class="text-overline mb-2 d-block">Estado del Proceso</label>
                    <select name="estado" class="form-select border-main p-2">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>🟢 Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>🔴 Inactivos</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="text-overline mb-2 d-block">Fecha de Creación (Desde)</label>
                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control border-main p-2">
                </div>
                
                <div class="d-grid gap-2 mt-5">
                    <button type="submit" class="btn btn-ux btn-ux-primary">Aplicar Filtros</button>
                    @if(request()->anyFilled(['estado', 'fecha_desde', 'search']))
                        <a href="{{ route('correspondencia.procesos.index') }}" class="btn btn-ux btn-ux-icon text-center">Limpiar Todo</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inicializar Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // 2. Lógica del Bulk Actions Bar (Checkboxes)
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const bulkBar = document.getElementById('bulk-actions-bar');
            const selectedCountSpan = document.getElementById('selectedCount');
            
            function updateBulkBar() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                selectedCountSpan.textContent = checkedCount;
                
                if (checkedCount > 0) {
                    bulkBar.classList.add('show');
                } else {
                    bulkBar.classList.remove('show');
                    if(selectAll) selectAll.checked = false;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', (e) => {
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                    updateBulkBar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });
        });

        // 3. Envío del formulario masivo
        function submitBulk(action) {
            document.getElementById('bulkActionInput').value = action;
            document.getElementById('bulkForm').submit();
        }
    </script>
</x-base-layout>