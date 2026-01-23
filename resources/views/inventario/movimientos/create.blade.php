<x-base-layout>
    <style>
        .split-layout { display: grid; grid-template-columns: 350px 1fr; gap: 30px; max-width: 1300px; margin: 0 auto; padding: 30px; font-family: 'Inter', sans-serif; color: #0f172a; }
        @media (max-width: 900px) { .split-layout { grid-template-columns: 1fr; } }

        .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; }
        .panel-head { padding: 20px; background: #0f172a; color: #fff; font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 10px; }
        .panel-body { padding: 24px; flex-grow: 1; }

        .form-label { display: block; font-size: 0.8rem; font-weight: 700; margin-bottom: 6px; color: #475569; text-transform: uppercase; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 20px; font-family: inherit; font-size: 0.9rem; }
        
        .list-container { max-height: 500px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; }
        .item-row { display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; cursor: pointer; }
        .item-row:hover { background: #f8fafc; }
        .chk { width: 18px; height: 18px; margin-right: 15px; accent-color: #0f172a; }
        .item-info { flex-grow: 1; }
        .item-name { font-weight: 600; font-size: 0.95rem; display: block; }
        .item-meta { font-size: 0.8rem; color: #64748b; }

        .btn-action { width: 100%; background: #4f46e5; color: white; padding: 15px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; margin-top: auto; }
        .btn-action:hover { background: #4338ca; }
    </style>

    <form action="{{ route('inventario.movimientos.store') }}" method="POST">
        @csrf
        <div class="split-layout">
            {{-- PANEL IZQUIERDO: CONFIGURACIÓN --}}
            <div class="panel" style="height: fit-content;">
                <div class="panel-head"><i class="bi bi-gear-wide-connected"></i> Datos del Acta</div>
                <div class="panel-body">
                    <label class="form-label">Código (Automático)</label>
                    <input type="text" name="codigo_acta" class="form-control" value="ACT-{{ date('Ymd-His') }}" readonly style="background: #f1f5f9;">

                    <label class="form-label">Tipo de Movimiento</label>
                    <select name="id_InvTiposRegistros" class="form-control">
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Funcionario / Responsable</label>
                    <select name="id_usersAsignado" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($usuarios as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Observaciones</label>
                    <textarea name="observacion_general" class="form-control" rows="3"></textarea>
                    
                    <button type="submit" class="btn-action">Generar Acta y Asignar</button>
                </div>
            </div>

            {{-- PANEL DERECHO: SELECCIÓN DE ACTIVOS --}}
            <div class="panel">
                <div class="panel-head" style="background: #fff; color: #0f172a; border-bottom: 1px solid #e2e8f0;">
                    <i class="bi bi-box-seam"></i> Seleccionar Activos Disponibles
                </div>
                <div class="panel-body" style="background: #fbfcfd;">
                    <div class="list-container">
                        @forelse($activosDisponibles as $activo)
                        <label class="item-row">
                            <input type="checkbox" name="activos_seleccionados[]" value="{{ $activo->id }}" class="chk">
                            <div class="item-info">
                                <span class="item-name">{{ $activo->nombre }}</span>
                                <span class="item-meta">
                                    Placa: <b>{{ $activo->codigo_activo }}</b> | S/N: {{ $activo->serial }}
                                </span>
                            </div>
                            <div class="item-meta">{{ $activo->marca->nombre }}</div>
                        </label>
                        @empty
                        <div style="padding: 30px; text-align: center; color: #94a3b8;">
                            No hay activos disponibles en bodega (Estado: Disponible).
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-base-layout>