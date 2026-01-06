<x-base-layout>
    <div class="app-container">
        {{-- Header Compacto --}}
        <header class="main-header">
            <div>
                <h1 class="text-title">Proyectos</h1>
                <p class="subtitle">Gestión y control de flujos de trabajo</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('flujo.tablero') }}" class="btn-ghost-modern">
                    <i class="fas fa-home-alt"></i>
                    <span>Volver al Inicio</span>
                </a>
                <a href="{{ route('flujo.workflows.create') }}" class="btn-primary-compact">
                    <i class="fas fa-plus"></i> Nuevo
                </a>
            </div>
        </header>

        {{-- Dashboard de Estados (Métricas) --}}
        @php
            $counts = App\Models\Flujo\Workflow::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
            $total = $counts->sum();
        @endphp

        <div class="status-summary">
            <div class="summary-item">
                <span class="count-val">{{ $total }}</span>
                <span class="count-label">Total</span>
            </div>
            <div class="summary-item">
                <span class="count-val color-borrador">{{ $counts['borrador'] ?? 0 }}</span>
                <span class="count-label">Borrador</span>
            </div>
            <div class="summary-item">
                <span class="count-val color-activo">{{ $counts['activo'] ?? 0 }}</span>
                <span class="count-label">Activos</span>
            </div>
            <div class="summary-item">
                <span class="count-val color-pausado">{{ $counts['pausado'] ?? 0 }}</span>
                <span class="count-label">Pausados</span>
            </div>
            <div class="summary-item">
                <span class="count-val color-completado">{{ $counts['completado'] ?? 0 }}</span>
                <span class="count-label">Hechos</span>
            </div>
            <div class="summary-item">
                <span class="count-val color-archivado">{{ $counts['archivado'] ?? 0 }}</span>
                <span class="count-label">Archivados</span>
            </div>
        </div>

        {{-- Toolbar Minimalista --}}
        <div class="toolbar">
            <form action="{{ route('flujo.workflows.index') }}" method="GET" class="search-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Buscar proyecto o responsable..." value="{{ request('search') }}" />
            </form>
        </div>

        {{-- Lista Compacta --}}
        <div class="compact-list">
            @forelse($workflows as $workflow)
                <div class="compact-row">
                    <div class="col-info">
                        <span class="p-id">#{{ $workflow->id }}</span>
                        <div class="name-wrapper">
                            <a href="{{ route('flujo.workflows.show', $workflow) }}" class="p-name">{{ $workflow->nombre }}</a>
                            @if($workflow->es_plantilla)
                                <span class="badge-mini">Plantilla</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-status">
                        <span class="status-indicator">
                            <span class="dot {{ $workflow->estado }}"></span>
                            {{ ucfirst($workflow->estado) }}
                        </span>
                    </div>

                    <div class="col-priority">
                        <span class="prio-text {{ $workflow->prioridad }}">{{ $workflow->prioridad }}</span>
                    </div>

                    <div class="col-users">
                        <div class="user-item">
                            <i class="far fa-user"></i>
                            <span>{{ $workflow->asignado?->name ?? 'Sin asignar' }}</span>
                        </div>
                    </div>

                    <div class="col-dates">
                        <span class="date-text">{{ $workflow->fecha_inicio?->format('d M') ?? '-' }} — {{ $workflow->fecha_fin?->format('d M') ?? '-' }}</span>
                    </div>

                    <div class="col-toggle">
                        <span class="status-pill {{ $workflow->activo ? 'active' : 'inactive' }}">
                            {{ $workflow->activo ? 'Live' : 'Off' }}
                        </span>
                    </div>

                    <div class="col-actions">
                        <a href="{{ route('flujo.workflows.edit', $workflow) }}" class="btn-icon-only"><i class="far fa-edit"></i></a>
                    </div>
                </div>
            @empty
                <div class="empty-state">No hay resultados para mostrar.</div>
            @endforelse
        </div>

        <div class="footer-pagination">
            {{ $workflows->links() }}
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        :root {
            --bg: #ffffff;
            --border: #eeeeee;
            --text-main: #111827;
            --text-muted: #6b7280;
            --primary: #4f46e5;
            
            /* Colores de Estado */
            --color-borrador: #94a3b8;
            --color-activo: #10b981;
            --color-pausado: #f59e0b;
            --color-completado: #4f46e5;
            --color-archivado: #64748b;
        }

        .app-container { max-width: 1100px; margin: 20px auto; font-family: 'Inter', sans-serif; color: var(--text-main); }

        /* Header */
        .main-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; padding: 0 10px; }
        .text-title { font-size: 1.5rem; font-weight: 700; margin: 0; letter-spacing: -0.02em; }
        .subtitle { color: var(--text-muted); font-size: 0.85rem; margin: 0; }

        /* Status Summary */
        .status-summary { 
            display: flex; 
            gap: 2.5rem; 
            padding: 0 10px; 
            margin-bottom: 30px; 
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }
        .summary-item { display: flex; flex-direction: column; gap: 4px; }
        .count-val { font-size: 1.3rem; font-weight: 700; color: var(--text-main); line-height: 1; }
        .count-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; }
        
        .color-borrador { color: var(--color-borrador) !important; }
        .color-activo { color: var(--color-activo) !important; }
        .color-pausado { color: var(--color-pausado) !important; }
        .color-completado { color: var(--color-completado) !important; }
        .color-archivado { color: var(--color-archivado) !important; }

        /* Buttons */
        .btn-primary-compact { background: var(--primary); color: white; padding: 7px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .btn-ghost { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; margin-right: 15px; font-weight: 500; }

        /* Toolbar */
        .toolbar { padding: 0 10px 15px; }
        .search-form { position: relative; width: 300px; }
        .search-form i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.8rem; color: var(--text-muted); }
        .search-form input { width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 8px 10px 8px 32px; font-size: 0.85rem; outline: none; background: #fdfdfd; }

        /* Compact Row */
        .compact-list { border-top: 1px solid var(--border); }
        .compact-row { 
            display: grid; 
            grid-template-columns: 2.2fr 1fr 0.8fr 1.2fr 1fr 0.6fr 0.4fr;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            transition: background 0.1s;
        }
        .compact-row:hover { background: #f9fafb; }

        .p-id { font-size: 0.7rem; color: var(--text-muted); font-family: monospace; font-weight: 600; width: 35px; }
        .p-name { font-size: 0.9rem; font-weight: 600; color: var(--text-main); text-decoration: none; }
        .badge-mini { font-size: 0.6rem; background: #eff6ff; color: #1e40af; padding: 1px 6px; border-radius: 4px; font-weight: 700; text-transform: uppercase; }

        /* Dots */
        .dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .dot.borrador { background: var(--color-borrador); }
        .dot.activo { background: var(--color-activo); box-shadow: 0 0 8px rgba(16, 185, 129, 0.4); }
        .dot.pausado { background: var(--color-pausado); }
        .dot.completado { background: var(--color-completado); }
        .dot.archivado { background: var(--color-archivado); }
        
        .status-indicator { font-size: 0.8rem; color: var(--text-main); font-weight: 500; }

        .prio-text { font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .prio-text.crítica { color: #ef4444; }
        .prio-text.alta { color: #f59e0b; }
        .prio-text.media { color: #6366f1; }
        .prio-text.baja { color: #94a3b8; }

        .user-item { font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
        .date-text { font-size: 0.8rem; color: var(--text-muted); }

        .status-pill { font-size: 0.6rem; padding: 2px 8px; border-radius: 6px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.02em; }
        .status-pill.active { color: #065f46; background: #d1fae5; }
        .status-pill.inactive { color: #4b5563; background: #f3f4f6; }

        .col-actions { text-align: right; }
        .btn-icon-only { color: var(--text-muted); transition: 0.2s; }
        .btn-icon-only:hover { color: var(--primary); }

        .empty-state { padding: 40px; text-align: center; color: var(--text-muted); font-size: 0.85rem; font-style: italic; }
        .footer-pagination { margin-top: 25px; }
    </style>
</x-base-layout>