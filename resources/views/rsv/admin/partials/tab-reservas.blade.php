{{--
    ========================================================================
    VISTA: tab-reservas.blade.php
    ESTILO: Minimalista, espaciado fluido, menú de estados corregido y búsqueda robusta.
    ========================================================================
--}}

<div class="container-fluid px-0" style="color: #475569; font-size: 0.78rem;">

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .card-subtle {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02);
        }

        .metric-pill {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            padding: 0.5rem 0.85rem;
            border-radius: 10px;
        }

        .table-friendly tbody tr {
            border-bottom: 1px solid #F1F5F9;
            transition: background-color 0.15s ease-in-out;
        }
        .table-friendly tbody tr:hover {
            background-color: #F8FAFC !important;
        }

        .input-friendly {
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            color: #334155;
            font-size: 0.75rem;
        }
        .input-friendly:focus {
            border-color: #94A3B8;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.15);
        }

        .btn-friendly {
            background-color: #FFFFFF;
            color: #475569;
            border: 1px solid #CBD5E1;
            font-size: 0.72rem;
            font-weight: 500;
        }
        .btn-friendly:hover {
            background-color: #F8FAFC;
            border-color: #94A3B8;
            color: #1E293B;
        }

        /* Corrección del menú de estados espichado */
        .status-pill-menu {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 1rem;
            background-color: #FFFFFF;
            border-bottom: 1px solid #E2E8F0;
            align-items: center;
        }
        .status-pill-item {
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 500;
            white-space: nowrap;
            text-decoration: none;
            transition: all 0.2s ease;
        }
    </style>

    {{-- 1. ENCABEZADO Y ACCIONES PRINCIPALES --}}
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color: #E2E8F0 !important;">
        <div class="d-flex align-items-center gap-2">
            <h6 class="fw-semibold mb-0" style="color: #1E293B; font-size: 0.95rem;">Reservas</h6>
            <span class="badge rounded-pill fw-normal" style="background-color: #F1F5F9; color: #64748B; font-size: 0.68rem; border: 1px solid #E2E8F0;">
                {{ $metrics['total_reservas'] ?? (isset($reservas) && method_exists($reservas, 'total') ? $reservas->total() : count($reservas ?? [])) }} registros
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-friendly px-3 py-1.5 rounded-2 d-flex align-items-center gap-1.5">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Exportar
            </button>
            <button type="button" class="btn px-3 py-1.5 rounded-2 fw-medium d-flex align-items-center gap-1.5 shadow-sm" style="background-color: #4F46E5; color: #FFFFFF; font-size: 0.72rem; border: none;" data-bs-toggle="modal" data-bs-target="#modalNuevaReserva">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Nueva Reserva
            </button>
        </div>
    </div>

    {{-- 2. PANEL DE MÉTRICAS Y ALERTAS --}}
    <div class="row g-2 mb-3">
        <div class="col-12 col-md-6">
            <div class="card-subtle rounded-3 p-2.5 d-flex align-items-center justify-content-between h-100">
                <div class="metric-pill d-flex align-items-center gap-2 flex-fill me-2" style="background-color: #F0FDF4; border-color: #DCFCE7;">
                    <span class="rounded-circle d-block" style="width: 7px; height: 7px; background-color: #16A34A;"></span>
                    <div>
                        <div style="color: #15803D; font-size: 0.62rem; font-weight: 600;">ACTIVAS HOY</div>
                        <div class="fw-bold" style="color: #166534; font-size: 0.88rem;">{{ $metrics['activas'] ?? 0 }}</div>
                    </div>
                </div>

                <div class="metric-pill d-flex align-items-center gap-2 flex-fill me-2" style="background-color: #F0F9FF; border-color: #E0F2FE;">
                    <span class="rounded-circle d-block" style="width: 7px; height: 7px; background-color: #0284C7;"></span>
                    <div>
                        <div style="color: #0369A1; font-size: 0.62rem; font-weight: 600;">ESTIMADO MES</div>
                        <div class="fw-bold" style="color: #075985; font-size: 0.88rem;">${{ number_format($metrics['monto_total_mes'] ?? 0, 0) }}</div>
                    </div>
                </div>

                <div class="metric-pill d-flex align-items-center gap-2 flex-fill" style="background-color: #FAF5FF; border-color: #F3E8FF;">
                    <span class="rounded-circle d-block" style="width: 7px; height: 7px; background-color: #9333EA;"></span>
                    <div>
                        <div style="color: #6B21A8; font-size: 0.62rem; font-weight: 600;">OCUPACIÓN</div>
                        <div class="fw-bold" style="color: #581C87; font-size: 0.88rem;">{{ $metrics['porcentaje_ocupacion'] ?? '78%' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card-subtle rounded-3 p-3 h-100 d-flex flex-column justify-content-center" style="background-color: #F8FAFC;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="fw-semibold" style="color: #475569; font-size: 0.68rem;">LLEGADAS HOY</span>
                    <span class="badge rounded-pill px-2 py-0.5" style="background-color: #E2E8F0; color: #334155; font-size: 0.6rem;">2 aptos</span>
                </div>
                <div class="text-truncate" style="color: #64748B; font-size: 0.72rem;">
                    • Apto 402 <span style="color: #94A3B8;">(14:00)</span><br>
                    • Villa Sol <span style="color: #94A3B8;">(16:00)</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="card-subtle rounded-3 p-3 h-100 d-flex align-items-center justify-content-between" style="background-color: #FFFBEB; border-color: #FEF3C7;">
                <div>
                    <div class="d-flex align-items-center gap-1.5 mb-1">
                        <span class="rounded-circle d-inline-block" style="width: 6px; height: 6px; background-color: #D97706;"></span>
                        <span class="fw-semibold" style="color: #92400E; font-size: 0.68rem;">Endosos por revisar</span>
                    </div>
                    <span style="color: #B45309; font-size: 0.72rem;">{{ $metrics['endosos_pendientes'] ?? 3 }} solicitudes pendientes</span>
                </div>
                <a href="#" class="btn btn-sm px-2.5 py-1 rounded-2 text-decoration-none fw-medium" style="background-color: #FEF3C7; color: #78350F; border: 1px solid #FDE68A; font-size: 0.68rem;">
                    Revisar
                </a>
            </div>
        </div>
    </div>

    {{-- 3. TABLA PRINCIPAL Y CONTROLES DE FILTRADO --}}
    <div class="card-subtle rounded-3 overflow-hidden">

        {{-- Barra de Filtros Integrada --}}
        <div class="p-3 border-bottom" style="background-color: #FAFAFA; border-color: #E2E8F0 !important;">
            <form action="{{ url()->current() }}" method="GET" class="row g-2 align-items-center" id="formFiltroReservas">
                @if(request('id_rsv_statuses'))
                    <input type="hidden" name="id_rsv_statuses" value="{{ request('id_rsv_statuses') }}">
                @endif

                <div class="col-12 col-md-6">
                    <input type="text" id="inputSearchReservas" name="search" class="form-control form-control-sm input-friendly rounded-2 px-3 py-1.5" placeholder="Buscar por código, titular, correo o inmueble..." value="{{ request('search') }}" autocomplete="off">
                </div>

                <div class="col-5 col-md-2.5">
                    <input type="date" name="fecha_desde" class="form-control form-control-sm input-friendly rounded-2 px-2 py-1.5" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-5 col-md-2.5">
                    <input type="date" name="fecha_hasta" class="form-control form-control-sm input-friendly rounded-2 px-2 py-1.5" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-2 col-md-1 text-end">
                    <button type="submit" class="btn btn-friendly w-100 rounded-2 py-1.5 d-flex justify-content-center align-items-center" title="Aplicar filtros">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Menú de Pestañas de Estado Mejorado (Diseño holgado y sin comprimirse) --}}
        @php
            $statusesList = class_exists(\App\Models\Rsv\Status::class) ? \App\Models\Rsv\Status::all() : collect();
            $currentStatus = request('id_rsv_statuses');
            $listadoReservas = $reservas ?? [];
        @endphp
        <div class="status-pill-menu no-scrollbar">
            <a href="{{ request()->fullUrlWithQuery(['id_rsv_statuses' => null, 'page' => null]) }}"
               class="status-pill-item"
               style="{{ is_null($currentStatus) ? 'color: #FFFFFF; background-color: #1E293B;' : 'color: #64748B; background-color: #F1F5F9;' }}">
                Todas
            </a>
            @foreach($statusesList as $st)
                @php $isActive = ($currentStatus == $st->id); @endphp
                <a href="{{ request()->fullUrlWithQuery(['id_rsv_statuses' => $st->id, 'page' => null]) }}"
                   class="status-pill-item"
                   style="{{ $isActive ? 'color: #FFFFFF; background-color: #4F46E5;' : 'color: #64748B; background-color: #F1F5F9;' }}">
                    {{ $st->name }}
                </a>
            @endforeach
        </div>

        {{-- Tabla de Datos --}}
        <div class="table-responsive">
            <table class="table table-friendly align-middle mb-0 text-nowrap" style="font-size: 0.78rem;">
                <thead>
                    <tr style="background-color: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                        <th class="py-2.5 px-3 border-0" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">CÓDIGO / FECHAS</th>
                        <th class="py-2.5 px-3 border-0" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">TITULAR</th>
                        <th class="py-2.5 px-3 border-0" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">INMUEBLE</th>
                        <th class="py-2.5 px-3 border-0 text-end" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">MONTO</th>
                        <th class="py-2.5 px-3 border-0 text-center" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">ESTADO</th>
                        <th class="py-2.5 px-3 border-0 text-center" style="color: #475569; font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px;">ACCIONES</th>
                    </tr>
                </thead>
                <tbody id="tbodyReservas">
                    @forelse($listadoReservas as $reserva)
                        <tr class="reserva-row">
                            <td class="px-3 py-3">
                                <span class="d-block fw-semibold" style="color: #1E293B;">#{{ $reserva->codigo_reserva }}</span>
                                <span class="d-block text-muted" style="font-size: 0.68rem;">
                                    {{ optional($reserva->fecha_inicio)->format('d M') }} — {{ optional($reserva->fecha_fin)->format('d M Y') }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <span class="d-block fw-medium text-dark">{{ optional($reserva->user)->name ?? 'Sin asignar' }}</span>
                                <span class="d-block text-muted" style="font-size: 0.68rem;">{{ optional($reserva->user)->email ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-3 fw-medium text-secondary">
                                {{ optional($reserva->inmueble)->name ?? 'N/D' }}
                            </td>
                            <td class="px-3 py-3 text-end fw-semibold text-dark">
                                ${{ number_format($reserva->monto_total ?? 0, 2) }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                @php
                                    $stName = optional($reserva->status)->name ?? 'Pendiente';
                                    $bg = '#F1F5F9'; $color = '#475569'; $border = '#E2E8F0';
                                    if(str_contains(strtolower($stName), 'confirm') || str_contains(strtolower($stName), 'activ')) {
                                        $bg = '#DCFCE7'; $color = '#15803D'; $border = '#BBF7D0';
                                    } elseif(str_contains(strtolower($stName), 'pendien')) {
                                        $bg = '#FEF3C7'; $color = '#B45309'; $border = '#FDE68A';
                                    } elseif(str_contains(strtolower($stName), 'cancela')) {
                                        $bg = '#FEE2E2'; $color = '#B91C1C'; $border = '#FCA5A5';
                                    }
                                @endphp
                                <span class="px-2.5 py-1 rounded-2 fw-medium d-inline-block shadow-sm" style="background-color: {{ $bg }}; color: {{ $color }}; font-size: 0.68rem; border: 1px solid {{ $border }};">
                                    {{ $stName }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-center">
                                <button class="btn btn-friendly px-2.5 py-1 rounded-2 shadow-sm" style="font-size: 0.68rem;">
                                    Detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted fst-italic">
                                No hay reservas registradas en este criterio.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if(isset($reservas) && method_exists($reservas, 'hasPages') && $reservas->hasPages())
            <div class="px-3 py-2.5 border-top d-flex justify-content-between align-items-center" style="background-color: #FAFAFA; border-color: #E2E8F0 !important; font-size: 0.7rem; color: #64748B;">
                <span>Mostrando {{ $reservas->firstItem() }} a {{ $reservas->lastItem() }} de {{ $reservas->total() }} registros</span>
                <div>{{ $reservas->appends(request()->query())->links() }}</div>
            </div>
        @endif

    </div>
</div>

{{-- Script de filtrado dinámico en cliente --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('inputSearchReservas');
        const tbody = document.getElementById('tbodyReservas');
        if (!searchInput || !tbody) return;

        const rows = tbody.querySelectorAll('.reserva-row');

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
