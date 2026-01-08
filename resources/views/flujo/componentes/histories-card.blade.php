{{-- resources/views/flujo/componentes/histories-card.blade.php --}}
<div class="corporate-history-card">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;800&display=swap');

        .corporate-history-card { 
            background: #fff; 
            padding: 20px; 
            font-family: 'Inter', sans-serif; 
            border-radius: 16px;
        }
        
        /* Tabla de Auditoría Desktop */
        .audit-table-minimal { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .audit-table-minimal th { 
            text-align: left; padding: 12px 15px; font-size: 0.65rem; 
            text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; 
            border-bottom: 2px solid #f1f5f9; font-weight: 800;
        }
        .audit-table-minimal td { padding: 16px 15px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .audit-table-minimal tbody tr:hover { background: #fcfdfe; }

        /* Trazabilidad y Transiciones */
        .audit-info-group { display: flex; flex-direction: column; gap: 4px; }
        .audit-task-link { color: #0f172a; font-weight: 700; text-decoration: none; font-size: 0.85rem; transition: 0.2s; }
        .audit-task-link:hover { color: #4f46e5; }
        .audit-project-path { font-size: 0.7rem; color: #94a3b8; display: flex; align-items: center; gap: 5px; font-weight: 500; }

        .transition-flow { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.7rem; }
        .state-tag { 
            padding: 3px 10px; border-radius: 6px; background: #f1f5f9; color: #475569; 
            font-family: 'Outfit', sans-serif; text-transform: uppercase; border: 1px solid transparent;
        }
        .state-new { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        
        .arrow-divider { color: #cbd5e1; font-size: 0.7rem; }

        /* Usuario y Tiempo */
        .audit-user { display: flex; align-items: center; gap: 10px; }
        .user-chip { 
            width: 28px; height: 28px; border-radius: 8px; background: #0f172a; color: #fff; 
            display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 800;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .timestamp-log { font-size: 0.75rem; color: #64748b; white-space: nowrap; text-align: right; }
        .ref-log { font-family: 'Outfit'; font-size: 0.7rem; color: #94a3b8; font-weight: 600; opacity: 0.6; }

        /* --- MÓVIL (Activity Feed Style) --- */
        .mobile-audit-feed { display: none; flex-direction: column; gap: 16px; }

        @media (max-width: 900px) {
            .audit-table-minimal { display: none; }
            .mobile-audit-feed { display: flex; }
            .corporate-history-card { padding: 15px 10px; }

            .audit-mobile-item {
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                padding: 16px;
                position: relative;
                overflow: hidden;
            }

            .m-audit-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }

            .m-user-info { display: flex; align-items: center; gap: 10px; }
            .m-user-name { font-size: 0.85rem; font-weight: 700; color: #0f172a; }
            .m-time { font-size: 0.7rem; color: #94a3b8; font-weight: 500; }

            .m-audit-body {
                background: #f8fafc;
                border-radius: 12px;
                padding: 12px;
                margin-bottom: 12px;
            }

            .m-task-info { margin-bottom: 10px; }
            .m-transition { 
                display: flex; 
                flex-wrap: wrap; 
                align-items: center; 
                gap: 6px; 
                padding: 8px 0;
            }

            .m-audit-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.65rem;
                color: #94a3b8;
                border-top: 1px solid #f1f5f9;
                padding-top: 10px;
            }
        }
    </style>

    {{-- Vista Desktop (Tabla Corporativa) --}}
    <table class="audit-table-minimal">
        <thead>
            <tr>
                <th width="100">Evento</th>
                <th>Objeto y Trazabilidad</th>
                <th>Transición de Estado</th>
                <th>Responsable</th>
                <th width="180" class="text-right">Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr>
                <td><span class="ref-log">#LOG-{{ $history->id }}</span></td>
                <td>
                    <div class="audit-info-group">
                        <a href="{{ route('flujo.tasks.show', $history->task_id) }}" class="audit-task-link">
                            {{ $history->task->titulo ?? 'Unidad de Trabajo' }}
                        </a>
                        <div class="audit-project-path">
                            <i class="fas fa-layer-group"></i> 
                            {{ $history->task->workflow->nombre ?? 'Sistema General' }}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="transition-flow">
                        <span class="state-tag">{{ $history->estado_anterior ?? 'N/D' }}</span>
                        <i class="fas fa-long-arrow-alt-right arrow-divider"></i>
                        <span class="state-tag state-new">{{ $history->estado_nuevo ?? 'N/D' }}</span>
                    </div>
                </td>
                <td>
                    <div class="audit-user">
                        <div class="user-chip">{{ substr($history->user->name ?? 'U', 0, 1) }}</div>
                        <span style="font-weight: 700; color: #0f172a; font-size: 0.85rem;">{{ $history->user->name ?? 'Sistema' }}</span>
                    </div>
                </td>
                <td class="timestamp-log">
                    <div style="font-weight: 700; color: #0f172a;">{{ $history->created_at->diffForHumans() }}</div>
                    <div style="font-size: 0.65rem; margin-top: 4px; font-weight: 500;">{{ $history->created_at->format('d/m/Y H:i:s') }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding: 80px; color: #94a3b8;">
                    <i class="fas fa-history" style="display: block; font-size: 3rem; margin-bottom: 20px; opacity: 0.1;"></i>
                    No se registran eventos de auditoría.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Vista Móvil (Activity Feed) --}}
    <div class="mobile-audit-feed">
        @forelse($histories as $history)
        <div class="audit-mobile-item">
            <div class="m-audit-header">
                <div class="m-user-info">
                    <div class="user-chip">{{ substr($history->user->name ?? 'U', 0, 1) }}</div>
                    <div>
                        <div class="m-user-name">{{ $history->user->name ?? 'Sistema' }}</div>
                        <div class="m-time">{{ $history->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                <span class="ref-log">#{{ $history->id }}</span>
            </div>

            <div class="m-audit-body">
                <div class="m-task-info">
                    <a href="{{ route('flujo.tasks.show', $history->task_id) }}" class="audit-task-link">
                        <i class="fas fa-thumbtack"></i> {{ $history->task->titulo ?? 'Unidad' }}
                    </a>
                </div>
                <div class="m-transition">
                    <span class="state-tag" style="font-size: 0.6rem;">{{ $history->estado_anterior ?? 'INICIO' }}</span>
                    <i class="fas fa-arrow-right arrow-divider"></i>
                    <span class="state-tag state-new" style="font-size: 0.6rem;">{{ $history->estado_nuevo }}</span>
                </div>
            </div>

            <div class="m-audit-footer">
                <span><i class="fas fa-folder"></i> {{ Str::limit($history->task->workflow->nombre ?? 'General', 25) }}</span>
                <span>{{ $history->created_at->format('H:i:s d/m/y') }}</span>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            Sin historial disponible.
        </div>
        @endforelse
    </div>

    <div class="pagination-wrap">
        {{ $histories->links() }}
    </div>
</div>