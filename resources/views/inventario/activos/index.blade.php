<x-base-layout>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        .container { max-width: 1400px; margin: 0 auto; padding: 40px 20px; font-family: 'Inter', system-ui, sans-serif; color: var(--slate-900); }
        
        /* Header & Search */
        .header-section { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; gap: 20px; flex-wrap: wrap; }
        .page-title { font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; margin-top: 5px; }
        .search-container { position: relative; }
        .search-input { 
            padding: 12px 20px 12px 45px; 
            border: 1px solid var(--slate-200); 
            border-radius: 12px; 
            width: 350px; 
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        /* Table Styling */
        .t-card { background: #fff; border-radius: 20px; border: 1px solid var(--slate-200); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; }
        .t-min { width: 100%; border-collapse: collapse; table-layout: auto; }
        .t-min th { background: var(--slate-50); text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; border-bottom: 1px solid var(--slate-200); }
        .t-min tr:hover { background-color: #fcfcfd; }
        .t-min td { padding: 16px 24px; border-bottom: 1px solid var(--slate-200); vertical-align: middle; }

        /* Badges & Pills */
        .pill { padding: 5px 12px; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; }
        .st-ok { background: #dcfce7; color: #15803d; }
        .st-busy { background: #e0f2fe; color: #0369a1; }
        .st-bad { background: #fef2f2; color: #b91c1c; }
        .st-fix { background: #fffbeb; color: #b45309; }
        
        .warranty-tag { font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; background: var(--slate-100); color: var(--slate-700); font-weight: 600; }
        .expired { background: #fee2e2; color: #ef4444; }

        /* Action Buttons */
        .actions-wrapper { display: flex; gap: 8px; justify-content: flex-end; }
        .btn-action { 
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--slate-200);
            color: var(--slate-700); transition: 0.2s; background: white;
        }
        .btn-view { width: auto; padding: 0 15px; font-size: 0.8rem; font-weight: 600; text-decoration: none; gap: 6px; }
        .btn-view:hover { background: var(--slate-900); color: white; border-color: var(--slate-900); }
        .btn-edit:hover { background: #f0f9ff; color: #0284c7; border-color: #0284c7; }
        
        /* Empty State */
        .empty-state { padding: 60px; text-align: center; color: #64748b; }
    </style>

    <div class="container">
        <div class="header-section">
            <div>
                <span style="font-size: 0.75rem; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.1em;">
                    Módulo de Inventario
                </span>
                <h1 class="page-title">Almacén de Activos</h1>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <form action="{{ route('inventario.activos.index') }}" method="GET" class="search-container">
                    <input type="text" name="search" class="search-input" 
                           placeholder="Buscar por placa, serial o nombre..." 
                           value="{{ request('search') }}">
                    <span style="position: absolute; left: 18px; top: 12px; color: #94a3b8;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                </form>
                
                <!-- <a href="{{ route('inventario.activos.create') }}" 
                   style="background: var(--primary); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.9rem; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);">
                    + Nuevo Activo
                </a> -->
                <a href="{{ route('inventario.compras.create') }}" 
                    class="btn btn-primary btn-lg shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    Registrar Compra
                </a>
            </div>
        </div>

        <div class="t-card">
            <table class="t-min">
                <thead>
                    <tr>
                        <th>Activo / Clasificación</th>
                        <th>Identificación & Serial</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Garantía</th>
                        <th>Responsable</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activos as $activo)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--slate-900);">{{ $activo->nombre }}</div>
                            <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 4px;">
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="font-size: 0.7rem; color: #475569; background: #f1f5f9; padding: 1px 6px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        {{ $activo->marca->nombre ?? 'Sin Marca' }}
                                    </span>
                                    <span style="font-size: 0.7rem; color: #64748b; font-weight: 500;">
                                        {{ $activo->subgrupo->nombre ?? '' }}
                                    </span>
                                </div>
                                @if($activo->referencia)
                                    <span style="font-size: 0.75rem; color: #94a3b8;">Ref: {{ $activo->referencia->referencia ?? 'N/A' }}</span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div style="font-family: 'Mono', monospace; font-weight: 700; color: var(--primary); font-size: 0.95rem;">
                                {{ $activo->codigo_activo }}
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                <span style="font-weight: 500;">S/N:</span> {{ $activo->serial ?? 'No registra' }}
                            </div>
                        </td>

                        <td>
                            <div style="font-size: 0.85rem; font-weight: 500;">
                                {{ $activo->municipio->nombre ?? 'No asignado' }}
                            </div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">
                                {{ $activo->referencia->bodega->nombre ?? 'Sin bodega' }}
                            </div>
                        </td>

                        <td>
                            @php
                                $statusClass = match($activo->id_Estado) {
                                    1 => 'st-ok', 
                                    2 => 'st-busy', 
                                    3 => 'st-bad', 
                                    4 => 'st-fix', 
                                    default => 'st-busy'
                                };
                            @endphp
                            <span class="pill {{ $statusClass }}">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                {{ $activo->estado->nombre ?? 'N/A' }}
                            </span>
                        </td>

                        <td>
                            @if($activo->fecha_fin_garantia)
                                <div class="warranty-tag {{ $activo->fecha_fin_garantia->isPast() ? 'expired' : '' }}">
                                    Exp: {{ $activo->fecha_fin_garantia->format('d/m/Y') }}
                                </div>
                                <div style="font-size: 0.65rem; color: #94a3b8; margin-top: 4px;">
                                    Vida útil: {{ $activo->vida_util_meses ?? '0' }} meses
                                </div>
                            @else
                                <span style="color: #cbd5e1; font-size: 0.75rem;">Sin registro</span>
                            @endif
                        </td>

                        <td>
                            @if($activo->usuarioAsignado)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--slate-200); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 800;">
                                        {{ strtoupper(substr($activo->usuarioAsignado->name, 0, 2)) }}
                                    </div>
                                    <div style="font-size: 0.85rem; font-weight: 500;">{{ $activo->usuarioAsignado->name }}</div>
                                </div>
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 0.8rem;">-- Disponible --</span>
                            @endif
                        </td>

                        <td style="text-align: right;">
                            <div class="actions-wrapper">
                                <a href="{{ route('inventario.activos.show', $activo->id) }}" class="btn-action btn-view" title="Ver Hoja de Vida">
                                    <span>Ver Detalle</span>
                                </a>
                                <a href="{{ route('inventario.activos.edit', $activo->id) }}" class="btn-action btn-edit" title="Editar Activo">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg width="48" height="48" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24" style="margin-bottom: 10px;"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p>No se encontraron activos que coincidan con la búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if($activos->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--slate-200); background: var(--slate-50);">
                    {{ $activos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>