{{-- KPI CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-total h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:#f1f5f9;color:var(--c-muted);">
                    <i class="fas fa-database"></i>
                </div>
                <span class="kpi-trend trend-neu">
                    <i class="fas fa-globe" style="font-size:.6rem;"></i> Global
                </span>
            </div>
            <div class="kpi-number">{{ number_format($kpi['total_registros'] ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                Total Facturas
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-bloque h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:var(--c-primary-soft);color:var(--c-primary);">
                    <i class="fas fa-cube"></i>
                </div>
                <span class="kpi-trend trend-neu">
                    <i class="fas fa-dot-circle" style="font-size:.6rem;"></i> Activo
                </span>
            </div>
            <div class="kpi-number" style="font-family:monospace;">
                #{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}
            </div>
            <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                Bloque Activo
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-pend h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:var(--c-warning-soft);color:#b45309;">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                @php
                    $pct = isset($kpi['total_registros']) && $kpi['total_registros'] > 0
                        ? round((($kpi['total_registros'] - $kpi['pendientes']) / $kpi['total_registros']) * 100)
                        : 0;
                @endphp
                <span class="kpi-trend" style="background:var(--c-warning-soft);color:#92400e;">
                    {{ $pct }}% listo
                </span>
            </div>
            <div class="kpi-number">{{ number_format($kpi['pendientes'] ?? 0, 0, ',', '.') }}</div>
            <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                Clientes pendientes
            </div>
            <div class="mt-2" style="height:3px;background:var(--c-border);border-radius:9999px;overflow:hidden;">
                <div style="width:{{ $pct }}%;height:100%;background:var(--c-warning);border-radius:9999px;transition:width 1s ease;"></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="kpi-card kpi-capital h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background:var(--c-success-soft);color:#047857;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <span class="kpi-trend trend-up">
                    <i class="fas fa-arrow-up" style="font-size:.55rem;"></i> Capital
                </span>
            </div>
            <div class="kpi-number" style="font-size:1.3rem;">
                ${{ number_format($kpi['valor_pendiente'] ?? 0, 0, ',', '.') }}
            </div>
            <div class="mt-1" style="font-size:.75rem;color:var(--c-muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em;">
                Valor Pendiente
            </div>
        </div>
    </div>
</div>
