{{-- resources/views/flujo/componentes/histories-card.blade.php --}}
<div class="corporate-history-card">
    <style>
        .corporate-history-card { background: #fff; padding: 10px 5px; font-family: 'Inter', sans-serif; }
        
        /* Tabla de Auditoría */
        .audit-table-minimal { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .audit-table-minimal th { 
            text-align: left; padding: 12px 15px; font-size: 0.65rem; 
            text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; 
            border-bottom: 2px solid #f1f5f9;
        }
        .audit-table-minimal td { padding: 16px 15px; font-size: 0.85rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .audit-table-minimal tbody tr:hover { background: #fcfdfe; }

        /* Trazabilidad y Transiciones */
        .audit-info-group { display: flex; flex-direction: column; gap: 4px; }
        .audit-task-link { color: #0f172a; font-weight: 700; text-decoration: none; font-size: 0.85rem; }
        .audit-task-link:hover { color: #2563eb; }
        .audit-project-path { font-size: 0.7rem; color: #94a3b8; display: flex; align-items: center; gap: 5px; }

        .transition-flow { display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 0.75rem; }
        .state-tag { padding: 2px 8px; border-radius: 4px; background: #f1f5f9; color: #475569; font-family: monospace; }
        .state-new { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        
        .arrow-divider { color: #cbd5e1; font-size: 0.7rem; }

        /* Usuario y Tiempo */
        .audit-user { display: flex; align-items: center; gap: 8px; }
        .user-chip { 
            width: 24px; height: 24px; border-radius: 6px; background: #0f172a; color: #fff; 
            display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 700;
        }
        .timestamp-log { font-size: 0.75rem; color: #64748b; white-space: nowrap; }
        
        .ref-log { font-family: monospace; font-size: 0.7rem; color: #94a3b8; }
    </style>

    <table class="audit-table-minimal">
        <thead>
            <tr>
                <th width="80">Evento</th>
                <th>Objeto y Trazabilidad</th>
                <th>Transición de Estado</th>
                <th>Responsable del Cambio</th>
                <th width="150" class="text-right">Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr>
                {{-- ID de Auditoría --}}
                <td><span class="ref-log">#LOG-{{ $history->id }}</span></td>

                {{-- Tarea y Proyecto --}}
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

                {{-- Transición de Estados --}}
                <td>
                    <div class="transition-flow">
                        <span class="state-tag">{{ $history->estado_anterior ?? 'N/D' }}</span>
                        <i class="fas fa-long-arrow-alt-right arrow-divider"></i>
                        <span class="state-tag state-new">{{ $history->estado_nuevo ?? 'N/D' }}</span>
                    </div>
                </td>

                {{-- Usuario --}}
                <td>
                    <div class="audit-user">
                        <div class="user-chip">{{ substr($history->user->name ?? 'U', 0, 1) }}</div>
                        <span style="font-weight: 600;">{{ $history->user->name ?? 'Sistema' }}</span>
                    </div>
                </td>

                {{-- Fecha del Cambio --}}
                <td class="timestamp-log text-right">
                    <div style="font-weight: 700; color: #1e293b;">
                        {{ $history->created_at->diffForHumans() }}
                    </div>
                    <div style="font-size: 0.65rem; margin-top: 2px;">
                        {{ $history->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center; padding: 60px; color: #94a3b8; font-style: italic;">
                    <i class="fas fa-shield-alt" style="display: block; font-size: 2.5rem; margin-bottom: 15px; opacity: 0.1;"></i>
                    No se registran eventos de auditoría en el ciclo actual.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrap">
        {{ $histories->links() }}
    </div>
</div>