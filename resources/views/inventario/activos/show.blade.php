<x-base-layout>
    <style>
        .profile-container { max-width: 1000px; margin: 40px auto; font-family: 'Inter', sans-serif; color: #0f172a; }
        .profile-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; }
        .ph-title h1 { font-size: 2rem; font-weight: 800; margin: 0; }
        .ph-title p { color: #64748b; margin: 5px 0 0 0; }
        
        .grid-layout { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
        
        .side-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; }
        .detail-row { margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }
        .detail-row:last-child { border: none; }
        .dr-label { display: block; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .dr-value { font-size: 1rem; font-weight: 600; }
        
        .main-content { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; }
        .history-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
        
        .timeline-item { position: relative; padding-left: 20px; border-left: 2px solid #e2e8f0; margin-bottom: 20px; padding-bottom: 20px; }
        .timeline-item::before { content: ''; position: absolute; left: -6px; top: 0; width: 10px; height: 10px; background: #0f172a; border-radius: 50%; }
        .tl-date { font-size: 0.8rem; color: #64748b; font-weight: 600; }
        .tl-title { font-weight: 700; font-size: 1rem; margin: 4px 0; }
        .tl-desc { font-size: 0.9rem; color: #475569; }
    </style>

    <div class="profile-container">
        <div class="profile-header">
            <div class="ph-title">
                <h1>{{ $activo->nombre }}</h1>
                <p>Placa: {{ $activo->codigo_activo }} | Serial: {{ $activo->serial }}</p>
            </div>
            <div>
                <a href="{{ route('inventario.activos.index') }}" style="color: #64748b; text-decoration: none; font-weight: 600;">&larr; Volver</a>
            </div>
        </div>

        <div class="grid-layout">
            <div class="side-card">
                <div class="detail-row">
                    <span class="dr-label">Estado Actual</span>
                    <span class="dr-value">{{ $activo->estado->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="dr-label">Marca</span>
                    <span class="dr-value">{{ $activo->marca->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="dr-label">Ubicación</span>
                    <span class="dr-value">{{ $activo->bodega->nombre }}</span>
                </div>
                <div class="detail-row">
                    <span class="dr-label">Asignado A</span>
                    <span class="dr-value">{{ $activo->usuarioAsignado->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="dr-label">Compra Original</span>
                    <span class="dr-value">{{ $activo->detalleCompra->compra->numero_factura ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="main-content">
                <h3 class="history-title">Historial de Movimientos y Mantenimientos</h3>
                
                {{-- Ejemplo de Timeline Combinado --}}
                @foreach($activo->movimientos as $mov)
                <div class="timeline-item">
                    <div class="tl-date">{{ $mov->created_at->format('d M Y, h:i A') }}</div>
                    <div class="tl-title">Movimiento: {{ $mov->tipoRegistro->nombre }}</div>
                    <div class="tl-desc">Acta: {{ $mov->codigo_acta }} - Responsable: {{ $mov->responsable->name }}</div>
                </div>
                @endforeach
                
                @if($activo->movimientos->isEmpty())
                    <p style="color: #94a3b8;">No hay historial registrado para este equipo.</p>
                @endif
            </div>
        </div>
    </div>
</x-base-layout>