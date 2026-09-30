<x-base-layout>
    {{-- ==============================================================================
         REGION 1: ESTILOS (CSS)
         ============================================================================== --}}
    <style>
        :root {
            --c-primary      : #4a90e2;
            --c-primary-soft : #e7f0ff;
            --c-success      : #2e7d32;
            --c-success-soft : #e8f5e9;
            --c-warning      : #f57f17;
            --c-warning-soft : #fff9c4;
            --c-info         : #00838f;
            --c-info-soft    : #e0f7fa;
            --c-danger       : #ef4444;
            --c-surface      : #ffffff;
            --c-bg           : #f8fafc;
            --c-border       : #e9ecef;
            --c-text         : #2c3e50;
            --c-muted        : #64748b;
        }

        .bg-pastel-primary { background-color: var(--c-primary-soft) !important; color: var(--c-primary) !important; border: none; }
        .bg-pastel-success { background-color: var(--c-success-soft) !important; color: var(--c-success) !important; border: none; }
        .bg-pastel-warning { background-color: var(--c-warning-soft) !important; color: var(--c-warning) !important; border: none; }
        
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-custom:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        
        .progress-custom { height: 8px; border-radius: 10px; background-color: #f1f5f9; overflow: hidden; }
    </style>

    {{-- Importar Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ==============================================================================
         REGION 2: CONTENEDOR PRINCIPAL DEL DASHBOARD DE LOTE
         ============================================================================== --}}
    <div class="app-container py-4" style="min-height: 100vh; background: var(--c-bg);">
        <div class="container-fluid px-xl-4">
            
            {{-- 3.1 NAVEGACIÓN, ENCABEZADO Y BOTÓN PDF --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: 12px; background-color: var(--c-primary-soft); flex-shrink: 0;">
                        <i class="fas fa-chart-line fs-4" style="color: var(--c-primary);"></i>
                    </div>
                    <div>
                        <div class="mb-1">
                            <a href="{{ route('certificados.informes.index') }}" class="text-decoration-none text-muted fw-bold" style="font-size: 0.8rem;">
                                <i class="fas fa-arrow-left me-1"></i> Volver al Centro de Control
                            </a>
                        </div>
                        <h1 class="h3 fw-bold m-0" style="color: var(--c-text); letter-spacing: -0.5px;">
                            Dashboard Analítico: Lote API-{{ str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) }}
                        </h1>
                        <p class="text-muted mt-1 mb-0" style="font-size: 0.85rem;">
                            {{ $bloqueInfo->descripcion ?? 'Monitoreo de operaciones y clientes procesados para este lote.' }}
                        </p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-pastel-primary px-3 py-2 fw-bold shadow-sm" style="font-size: 0.85rem;">
                        <i class="far fa-calendar-alt me-1"></i> {{ $textoPeriodo }}
                    </span>

                    {{-- Botón de Descarga en PDF con control de estado --}}
                    <form action="{{ route('certificados.informes.exportar_pdf') }}" method="POST" class="d-inline" id="formDownloadPdf">
                        @csrf
                        <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">
                        <button type="submit" id="btnDownloadPdf" class="btn btn-danger rounded-pill px-3 py-2 fw-bold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;" title="Exportar reporte del lote a PDF">
                            <i class="fas fa-file-pdf" id="pdfIcon"></i> <span id="pdfText">Descargar PDF</span>
                        </button>
                    </form>

                    {{-- Script para evitar que el botón se quede cargando infinitamente --}}
                    <script>
                        document.getElementById('formDownloadPdf').addEventListener('submit', function () {
                            const btn = document.getElementById('btnDownloadPdf');
                            const icon = document.getElementById('pdfIcon');
                            const text = document.getElementById('pdfText');

                            // Cambiar icono temporalmente a modo carga
                            icon.className = "fas fa-spinner fa-spin";
                            text.innerText = "Generando...";
                            btn.style.opacity = "0.7";

                            // Como el navegador descarga el archivo en segundo plano, 
                            // restauramos el botón a la normalidad a los 3 segundos.
                            setTimeout(function () {
                                icon.className = "fas fa-file-pdf";
                                text.innerText = "Descargar PDF";
                                btn.style.opacity = "1";
                                btn.disabled = false;
                            }, 3000);
                        });
                    </script>
                </div>
            </div>

            {{-- 3.2 CÁLCULOS PARA KPIs --}}
            @php
                $totalOps      = $kpi['total_operaciones'] ?? 0;
                $totalVal      = $kpi['total_lineas'] ?? 0;
                $generadosVal  = $kpi['generados'] ?? 0;
                $pendientesVal = $kpi['pendientes'] ?? 0;
                
                $porcentajeGen = $totalVal > 0 ? round(($generadosVal / $totalVal) * 100, 1) : 0;
                $porcentajePen = $totalVal > 0 ? round(($pendientesVal / $totalVal) * 100, 1) : 0;
            @endphp

            {{-- 3.3 TARJETAS KPI AVANZADAS --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card card-custom h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Clientes / Operaciones</div>
                            <div class="bg-pastel-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                                <i class="fas fa-users fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <div class="fs-2 fw-bolder" style="color: var(--c-text); line-height: 1;">{{ number_format($totalOps, 0, ',', '.') }}</div>
                            <span class="text-muted small">operaciones en lote</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <i class="fas fa-info-circle text-primary me-1"></i> Total de terceros procesados en este bloque
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card card-custom h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Completados / Generados</div>
                            <span class="badge bg-pastel-success fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                {{ $porcentajeGen }}% Eficiencia
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <div class="fs-2 fw-bolder text-success" style="line-height: 1;">{{ number_format($generadosVal, 0, ',', '.') }}</div>
                            <span class="text-muted small">líneas procesadas</span>
                        </div>
                        <div class="progress-custom mb-1">
                            <div class="progress-bar bg-success rounded-pill" role="progressbar" style="width: {{ $porcentajeGen }}%;"></div>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">Documentos generados exitosamente en el sistema.</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card card-custom h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Pendientes de Gestión</div>
                            <span class="badge bg-pastel-warning text-warning fw-bold px-2 py-1" style="font-size: 0.75rem;">
                                {{ $porcentajePen }}% Restante
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <div class="fs-2 fw-bolder text-warning" style="line-height: 1;">{{ number_format($pendientesVal, 0, ',', '.') }}</div>
                            <span class="text-muted small">por procesar</span>
                        </div>
                        <div class="progress-custom mb-1">
                            <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: {{ $porcentajePen }}%;"></div>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">Registros que requieren atención o ejecución manual.</div>
                    </div>
                </div>
            </div>

            {{-- ==============================================================================
                 REGION 3.4: SECCIÓN DE GRÁFICOS ANALÍTICOS (CHART.JS)
                 ============================================================================== --}}
            <div class="row g-4 mb-4">
                <div class="col-12 col-xl-6">
                    <div class="card card-custom shadow border-0 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark" style="font-size: 1rem;">
                                <i class="fas fa-chart-pie text-primary me-2"></i> Distribución de Estado de Líneas
                            </h5>
                            <span class="badge bg-light text-muted border">General</span>
                        </div>
                        <div class="d-flex justify-content-center align-items-center" style="position: relative; height: 240px;">
                            <canvas id="chartEficiencia"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class="card card-custom shadow border-0 p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-dark" style="font-size: 1rem;">
                                <i class="fas fa-chart-bar text-success me-2"></i> Operaciones por Método de Creación
                            </h5>
                            <span class="badge bg-light text-muted border">Lote Actual</span>
                        </div>
                        <div class="d-flex justify-content-center align-items-center" style="position: relative; height: 240px;">
                            <canvas id="chartMetodos"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                {{-- 3.5 TABLA CON LAS OPERACIONES (CLIENTES PROCESADOS) DEL LOTE --}}
                <div class="col-12 col-xl-8">
                    <div class="card card-custom shadow border-0 mb-4 h-100">
                        <div class="card-header bg-white border-bottom p-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text); font-size: 1.05rem;">
                                <i class="fas fa-user-tie text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                Operaciones y Clientes Procesados en el Lote
                            </h5>
                        </div>
                        <div class="card-body bg-light p-3 p-md-4">
                            <div class="bg-white border rounded-3 shadow-sm overflow-hidden">
                                <div class="table-responsive custom-scrollbar" style="max-height: 450px; overflow-y: auto;">
                                    <table class="table table-sm table-hover align-middle mb-0 text-nowrap" style="font-size: 0.8rem;">
                                        <thead class="table-light text-muted text-uppercase sticky-top" style="z-index: 10; font-size: 0.7rem;">
                                            <tr>
                                                <th class="ps-3 py-2">ID Operación / Radicado</th>
                                                <th class="py-2">Tercero / Cliente</th>
                                                <th class="py-2 text-center">Método</th>
                                                <th class="py-2 text-center">Líneas Asociadas</th>
                                                <th class="pe-3 py-2 text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($operacionesLote as $operacion)
                                                <tr style="border-bottom: 1px solid #f1f3f5;">
                                                    <td class="ps-3 py-2">
                                                        <div class="fw-bold text-primary">Op ID: {{ $operacion->id }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-file-invoice me-1"></i> Radicado: {{ $operacion->numero_radicado ?? 'S/N' }}</div>
                                                    </td>
                                                    <td class="py-2">
                                                        <div class="fw-bold text-dark">{{ optional($operacion->tercero)->nom_ter ?? 'Sin Tercero Asignado' }}</div>
                                                        <div class="text-muted" style="font-size: 0.7rem;">NIT/Código: {{ $operacion->id_tercero ?? 'N/A' }}</div>
                                                    </td>
                                                    <td class="py-2 text-center">
                                                        <span class="badge {{ $operacion->metodo_creacion == 1 ? 'bg-pastel-warning text-warning' : 'bg-pastel-primary' }} px-2 py-1">
                                                            {{ $operacion->metodo_creacion == 1 ? 'MANUAL' : 'AUTOMÁTICO' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 text-center">
                                                        <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                                            {{ $operacion->lineas->count() }} Líneas
                                                        </span>
                                                    </td>
                                                    <td class="pe-3 py-2 text-center">
                                                        <a href="{{ route('certificados.operaciones.show', $operacion->id) }}" class="btn btn-light btn-sm rounded-1 border shadow-sm" title="Ver Detalle de Operación">
                                                            <i class="fas fa-eye text-primary" style="font-size: 0.75rem;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 bg-white text-muted">
                                                        No hay operaciones registradas en este lote.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if(isset($operacionesLote) && $operacionesLote->hasPages())
                                    <div class="bg-light border-top pt-3 pb-3 px-4 d-flex justify-content-between align-items-center">
                                        <div class="m-0 pagination-sm">
                                            {{ $operacionesLote->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3.6 SECCIÓN LATERAL: TIPOS DE CERTIFICADOS / AUDITORÍA --}}
                <div class="col-12 col-xl-4 d-flex flex-column gap-4">
                    
                    {{-- Tipos de Certificados Generados en el Lote --}}
                    <div class="card card-custom shadow border-0">
                        <div class="card-header bg-white border-bottom p-3" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text); font-size: 0.95rem;">
                                <i class="fas fa-certificate text-primary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                Tipos de Certificados del Lote
                            </h5>
                        </div>
                        <div class="card-body bg-light p-3">
                            <div class="overflow-auto custom-scrollbar pe-2" style="max-height: 200px;">
                                @if(isset($tiposCertificadosLote) && $tiposCertificadosLote->count() > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($tiposCertificadosLote as $tipoOp)
                                            <div class="p-2 rounded bg-white border shadow-sm d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                                <div>
                                                    <div class="fw-bold text-dark">
                                                        <i class="fas fa-file-pdf text-danger me-1"></i>
                                                        {{ optional($tipoOp->tipo)->nombre ?? 'Certificado General' }}
                                                    </div>
                                                    <div class="text-muted mt-1">Op ID: {{ $tipoOp->id_car_sia_operaciones ?? 'Global Bloque' }}</div>
                                                </div>
                                                <span class="badge bg-pastel-primary px-2 py-1">Asignado</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-file-alt fs-3 mb-1 opacity-25"></i>
                                        <p class="small mb-0" style="font-size: 0.75rem;">Sin tipologías específicas registradas.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Historial y Auditoría --}}
                    <div class="card card-custom shadow border-0 flex-grow-1">
                        <div class="card-header bg-white border-bottom p-3" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--c-text); font-size: 0.95rem;">
                                <i class="fas fa-history text-secondary border rounded p-1" style="border-color: var(--c-border) !important;"></i>
                                Historial y Auditoría
                            </h5>
                        </div>
                        <div class="card-body bg-light p-3">
                            <div class="overflow-auto custom-scrollbar pe-2" style="max-height: 220px;">
                                @if(isset($historialBloque) && $historialBloque->count() > 0)
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($historialBloque as $log)
                                            <div class="p-2 rounded bg-white border shadow-sm" style="font-size: 0.75rem;">
                                                <div class="fw-bold text-dark">
                                                    <i class="fas fa-circle text-primary" style="font-size: 5px; vertical-align: middle;"></i> 
                                                    {{ optional($log->eventoAuditoria)->nombre ?? 'Evento' }}
                                                </div>
                                                <div class="text-muted mt-1">Usuario: {{ optional($log->usuario)->name ?? 'Sistema' }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-list-ul fs-3 mb-2 opacity-25"></i>
                                        <p class="small mb-0" style="font-size: 0.75rem;">Sin movimientos registrados.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ==============================================================================
         SCRIPTS PARA RENDERIZAR LOS GRÁFICOS (CHART.JS)
         ============================================================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Gráfico de Eficiencia (Doughnut)
            const ctxEficiencia = document.getElementById('chartEficiencia').getContext('2d');
            new Chart(ctxEficiencia, {
                type: 'doughnut',
                data: {
                    labels: ['Generados / Completados', 'Pendientes'],
                    datasets: [{
                        data: [{{ $generadosVal }}, {{ $pendientesVal }}],
                        backgroundColor: ['#2e7d32', '#f57f17'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    },
                    cutout: '70%'
                }
            });

            // 2. Gráfico de Métodos de Creación (Bar)
            const ctxMetodos = document.getElementById('chartMetodos').getContext('2d');
            new Chart(ctxMetodos, {
                type: 'bar',
                data: {
                    labels: ['Automáticas', 'Manuales'],
                    datasets: [{
                        label: 'Operaciones',
                        data: [{{ $chartMetodos['automaticas'] }}, {{ $chartMetodos['manuales'] }}],
                        backgroundColor: ['#4a90e2', '#f57f17'],
                        borderRadius: 8,
                        barThickness: 35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
</x-base-layout>