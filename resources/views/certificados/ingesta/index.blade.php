<x-base-layout>
    <!-- Importar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --color-primary: #4f46e5;
            --color-primary-hover: #4338ca;
            --color-primary-soft: #e0e7ff;
            --color-success: #10b981;
            --color-success-soft: #d1fae5;
            --color-danger: #ef4444;
            --color-danger-soft: #fee2e2;
            --color-warning: #f59e0b;
            --color-warning-soft: #fef3c7;
            --color-surface: #ffffff;
            --color-background: #f8fafc;
            --color-border: #e2e8f0;
            --color-text-main: #0f172a;
            --color-text-muted: #64748b;
            --radius-xl: 16px;
            --radius-lg: 12px;
            --radius-md: 8px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .app-container { font-family: 'Inter', sans-serif; background-color: var(--color-background); }
        .card-custom { border-radius: var(--radius-xl); background: var(--color-surface); border: 1px solid var(--color-border); box-shadow: var(--shadow-sm); }
        .kpi-icon-wrapper { width: 48px; height: 48px; border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
        .badge-modern { display: inline-flex; align-items: center; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background-color: var(--color-success-soft); color: #065f46; }
        .badge-danger { background-color: var(--color-danger-soft); color: #991b1b; }
        .badge-warning { background-color: var(--color-warning-soft); color: #92400e; }
        .badge-primary { background-color: var(--color-primary-soft); color: #3730a3; }
        .btn-modern { border-radius: var(--radius-lg); font-weight: 600; padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border: none; text-decoration: none; transition: all 0.2s ease;}
        .btn-primary-modern { background-color: var(--color-primary); color: white; box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39); }
        .btn-outline-modern { background-color: white; color: var(--color-text-main); border: 1px solid var(--color-border); }
        .btn-outline-modern:hover { background-color: #f8fafc; color: var(--color-primary); border-color: var(--color-primary-soft); }
        .table-custom th { font-size: 0.75rem; font-weight: 600; color: var(--color-text-muted); text-transform: uppercase; background-color: rgba(248, 250, 252, 0.5); padding: 1rem; border-bottom: 2px solid var(--color-border);}
        .table-custom td { vertical-align: middle; padding: 1rem; color: var(--color-text-main); border-bottom: 1px solid var(--color-border); }
        .input-modern { background-color: #f8fafc; border: 1px solid var(--color-border); border-radius: var(--radius-lg); padding: 0.5rem 1rem; color: var(--color-text-main); }

        /* Selectores Minimalistas Estilo Chip */
        .minimal-select {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            padding: 0.65rem 2.5rem 0.65rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }
        .minimal-select:hover { background-color: #f1f5f9; border-color: #cbd5e1; color: #334155; }
        .minimal-select:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-soft); background-color: #ffffff; }

        /* Animaciones para Drag and Drop */
        .drag-active { border-color: var(--color-primary) !important; background-color: var(--color-primary-soft) !important; transform: scale(1.02); }

        .pulse-dot { width: 12px; height: 12px; background-color: var(--color-primary); border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.5); } 70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); } 100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); } }
        .btn-spinner { display: none; width: 1.2rem; height: 1.2rem; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .is-loading .btn-text, .is-loading .btn-icon { display: none; }
        .is-loading .btn-spinner { display: inline-block; }

        /* NUEVO: Efecto minimalista para el botón de recarga (Adaptado al Theme) */
        .btn-reload { background-color: var(--color-surface); border: 1px solid var(--color-border); color: var(--color-text-muted); transition: all 0.3s ease; text-decoration: none; }
        .btn-reload:hover { background-color: var(--color-primary-soft); color: var(--color-primary); border-color: var(--color-primary-soft); }
        .btn-reload:hover i { transform: rotate(180deg); transition: transform 0.4s ease; }
        .btn-reload i { transition: transform 0.4s ease; display: inline-block; }
    </style>

    <div class="app-container py-4 position-relative" style="min-height: 100vh;">

        {{-- Encabezado y SELECTOR DE BLOQUE --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center shadow-sm" style="width: 54px; height: 54px; border-radius: var(--radius-lg); background-color: var(--color-primary-soft);">
                    <i class="fas fa-layer-group fs-4" style="color: var(--color-primary);"></i>
                </div>
                <div>
                    <h1 class="h4 fw-bold m-0 text-dark">Ingesta ERP <span class="badge badge-primary ms-2" style="font-size: 0.7rem; vertical-align: middle;">Staging</span></h1>
                    <p class="mb-0 mt-1" style="color: var(--color-text-muted); font-size: 0.9rem;">Visor aislado por lotes de carga.</p>
                </div>
            </div>

            <!-- CONTENEDOR BOTÓN NAVEGACIÓN + FILTRO -->
            <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">

                {{-- NUEVO BOTÓN: Recargar Vista --}}
                <a href="{{ request()->fullUrl() }}" class="btn-reload shadow-sm rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 42px; height: 42px; flex-shrink: 0;" title="Actualizar datos">
                    <i class="fas fa-sync-alt fs-5"></i>
                </a>

                <a href="{{ route('certificados.operaciones.index') }}" class="btn-modern btn-outline-modern shadow-sm text-nowrap">
                    <i class="fas fa-list-ul me-2" style="color: var(--color-primary);"></i> Ver Operaciones
                </a>

                @if($bloquesDisponibles->count() > 0)
                <form action="{{ route('certificados.ingesta.index') }}" method="GET" class="d-flex align-items-center bg-white p-2 border rounded-pill shadow-sm" style="min-width: 250px;">
                    <label class="fw-bold text-muted small mb-0 ms-3 me-2 text-nowrap"><i class="fas fa-filter me-1"></i> Bloque:</label>

                    @if(request('buscar_cedula')) <input type="hidden" name="buscar_cedula" value="{{ request('buscar_cedula') }}"> @endif
                    @if(request('estado')) <input type="hidden" name="estado" value="{{ request('estado') }}"> @endif

                    <select name="bloque" class="form-select border-0 shadow-none fw-bold" style="background-color: transparent; color: var(--color-primary); cursor:pointer;" onchange="this.form.submit()">
                        @foreach($bloquesDisponibles as $b)
                            <option value="{{ $b }}" {{ $bloqueActivo == $b ? 'selected' : '' }}>
                                Lote API-{{ str_pad($b, 4, '0', STR_PAD_LEFT) }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </div>

        {{-- Alertas del Sistema --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" style="border-radius: var(--radius-lg); background: #f0fdf4; color: #166534;">
                <i class="fas fa-check-circle fs-5 me-3"></i><span class="fw-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" style="border-radius: var(--radius-lg); background: #fef2f2; color: #991b1b;">
                <i class="fas fa-exclamation-circle fs-5 me-3"></i><span class="fw-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning d-flex align-items-center border-0 shadow-sm mb-4" style="border-radius: var(--radius-lg); background: #fffbeb; color: #92400e;">
                <i class="fas fa-exclamation-triangle fs-5 me-3"></i><span class="fw-medium">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- SECCIÓN KPI MINIMALISTAS --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-custom h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="kpi-icon-wrapper" style="background-color: #f1f5f9; color: var(--color-text-muted);"><i class="fas fa-database"></i></div>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--color-text-muted);">Total (Facturas)</div>
                        <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ number_format($kpi['total_registros'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-custom h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="kpi-icon-wrapper" style="background-color: var(--color-primary-soft); color: var(--color-primary);"><i class="fas fa-cube"></i></div>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--color-text-muted);">Bloque Actual</div>
                        <div class="fw-bold text-dark fs-4 lh-1 mt-1">#{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-custom h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="kpi-icon-wrapper" style="background-color: var(--color-warning-soft); color: #b45309;"><i class="fas fa-clock"></i></div>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--color-text-muted);">Pendientes (Este Bloque)</div>
                        <div class="fw-bold text-dark fs-4 lh-1 mt-1">{{ number_format($kpi['pendientes'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-custom h-100 p-4 d-flex flex-row align-items-center gap-3">
                    <div class="kpi-icon-wrapper" style="background-color: var(--color-success-soft); color: #047857;"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--color-text-muted);">Capital Total</div>
                        <div class="fw-bold text-dark fs-5 lh-1 mt-1">${{ number_format($kpi['valor_pendiente'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN GRAFICO Y CARGA DE EXCEL (ZONA DRAG & DROP) --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-4">
                <div class="card card-custom h-100 p-4">
                    <h6 class="fw-bold text-dark mb-3 fs-6"><i class="fas fa-chart-pie me-2 text-muted"></i>Estado del Bloque #{{ $bloqueActivo }}</h6>
                    <div class="position-relative" style="height: 150px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="kpiChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <div class="card card-custom h-100 transition-all" id="uploadCard" style="transition: all 0.3s ease;">
                    <div class="card-body p-4 d-flex flex-column justify-content-center gap-3 h-100">
                        <div class="d-flex gap-3 align-items-start">
                            <h6 class="fw-bold text-dark mb-1 fs-5" id="uploadTitle" style="transition: color 0.3s ease;">
                                <i class="fas fa-file-excel fs-4 text-muted me-2" id="uploadIcon"></i> Cargar Lote Inicial
                            </h6>
                            <span style="color: var(--color-text-muted); font-size: 0.9rem;" id="uploadDesc" class="mt-1">
                                Sube el archivo extraído del ERP (soporta +30.000 registros).
                            </span>
                        </div>

                        <form action="{{ route('certificados.ingesta.cargar') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 w-100" id="formCargaMasiva">
                            @csrf

                            <!-- WRAPPER PARA DRAG & DROP -->
                            <div class="position-relative flex-grow-1" id="dropWrapper">
                                <input type="file" name="archivo_excel" id="archivoExcelMasivo" accept=".xlsx, .xls, .csv" required
                                       class="position-absolute w-100 h-100 start-0 top-0" style="opacity: 0; cursor: pointer; z-index: 10;">

                                <div class="input-modern d-flex flex-column align-items-center justify-content-center p-3 bg-white" id="fakeUploadZone" style="border: 2px dashed var(--color-border); transition: all 0.3s ease;">
                                    <i class="fas fa-cloud-upload-alt fs-3 text-muted mb-2" id="fakeUploadIcon"></i>
                                    <span class="text-muted fw-medium text-center" id="fileNameDisplay">
                                        Arrastra tu archivo Excel aquí o haz clic para buscar...
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn-modern btn-outline-modern fw-bold text-nowrap disabled px-5 py-3" id="btnUploadSubmit" style="pointer-events: none; opacity: 0.6; height: 100%;">
                                Subir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- MATRIZ DE PREVISUALIZACIÓN --}}
        <div class="card card-custom overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-bottom d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-table" style="color: var(--color-text-muted);"></i>
                    <h6 class="fw-bold m-0" style="color: var(--color-text-main);">Registros del Bloque #{{ $bloqueActivo ?? 'N/A' }}</h6>
                </div>

                {{-- BUSCADOR Y FILTRO DE ESTADO --}}
                <form action="{{ route('certificados.ingesta.index') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2" style="width: 100%; max-width: 500px;">
                    <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

                    <select name="estado" class="form-select input-modern shadow-sm" style="width: auto; min-width: 140px; font-weight: 600; color: var(--color-text-muted);" onchange="this.form.submit()">
                        <option value="">Todos los Estados</option>
                        <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>⚠️ Pendientes</option>
                        <option value="PROCESADO" {{ request('estado') == 'PROCESADO' ? 'selected' : '' }}>✅ Procesados</option>
                    </select>

                    <div class="position-relative flex-grow-1">
                        <i class="fas fa-search position-absolute top-50 translate-middle-y" style="left: 1rem; color: var(--color-text-muted);"></i>
                        <input type="text" name="buscar_cedula" class="form-control input-modern w-100 shadow-sm" style="padding-left: 2.5rem;" placeholder="Buscar en este lote..." value="{{ request('buscar_cedula') }}">
                        @if(request('buscar_cedula') || request('estado'))
                            <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="position-absolute top-50 translate-middle-y" style="right: 1rem; color: var(--color-text-muted); text-decoration: none;" title="Limpiar Filtros">
                                <i class="fas fa-times-circle hover-opacity"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Bloque ID</th>
                            <th>ID Factura</th>
                            <th>Cliente / Tercero</th>
                            <th class="text-end">Valor Neto</th>
                            <th>Estado ETL</th>
                            <th>Fecha Recepción</th>
                            <th class="text-center pe-4">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lotesCrudos as $lote)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-secondary border fw-bold">API-{{ str_pad($lote->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td><span class="fw-bold" style="color: var(--color-text-main); font-family: monospace;">#{{ $lote->id_factura ?? 'N/A' }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $lote->nombre_tercero ?? 'Tercero Desconocido' }}</div>
                                <div style="color: var(--color-text-muted); font-size: 0.8rem; margin-top: 0.2rem;">NIT: {{ $lote->tercero ?? 'N/A' }}</div>
                            </td>
                            <td class="text-end"><span class="fw-bold" style="color: var(--color-text-main);">${{ number_format((float)$lote->valor, 2) }}</span></td>
                            <td>
                                @if($lote->anular == 1)
                                    <span class="badge-modern badge-danger"><i class="fas fa-ban me-1"></i> Anulado</span>
                                @elseif($lote->estado == 'PROCESADO')
                                    <span class="badge-modern badge-success"><i class="fas fa-check-circle me-1"></i> Procesado</span>
                                @else
                                    <span class="badge-modern badge-warning"><i class="fas fa-clock me-1"></i> Pendiente</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--color-text-muted); font-size: 0.85rem;"><i class="far fa-calendar-alt me-1"></i> {{ $lote->fecha_ad ?? $lote->created_at }}</span>
                            </td>
                            <td class="text-center pe-4">
                                @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                    <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm text-danger bg-transparent border-0" title="Excluir Lote" onclick="return confirm('¿Confirmas excluir este registro del bloque?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted opacity-50"><i class="fas fa-lock"></i></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                    <div class="mb-3 p-4 rounded-circle" style="background-color: var(--color-background);"><i class="fas fa-search fs-1" style="color: var(--color-border);"></i></div>
                                    <h5 class="fw-bold" style="color: var(--color-text-main);">Sin resultados</h5>
                                    <p style="color: var(--color-text-muted); max-width: 400px;">No se encontraron registros que coincidan con los filtros aplicados en este bloque.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lotesCrudos->hasPages() || $lotesCrudos->total() > 0)
                <div class="card-footer bg-white border-top-0 pt-4 pb-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <span class="text-muted" style="font-size: 0.85rem;">
                        Mostrando <span class="fw-bold text-dark">{{ $lotesCrudos->firstItem() ?? 0 }}</span> a <span class="fw-bold text-dark">{{ $lotesCrudos->lastItem() ?? 0 }}</span> de <span class="fw-bold text-dark">{{ number_format($lotesCrudos->total(), 0, ',', '.') }}</span> registros
                    </span>
                    <div class="m-0">
                        {{ $lotesCrudos->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- SECCIÓN ACCIÓN: INYECCIÓN (FIJA DESPUÉS DE LA PAGINACIÓN) --}}
        @if($totalPendientes > 0 && $bloqueActivo)
        <div class="d-flex justify-content-center w-100 mt-4 mb-2">
            <div class="bg-white border shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                 style="border-radius: 9999px; padding: 1rem 1.5rem; width: auto; min-width: 65%;">

                <div class="d-flex align-items-center px-3 border-end-md">
                    <span class="pulse-dot me-3"></span>
                    <div class="d-flex flex-column">
                        <span class="fw-bold fs-5 text-dark lh-1">{{ number_format($totalPendientes, 0, ',', '.') }}</span>
                        <span style="font-size: 0.75rem; color: var(--color-text-muted); font-weight: 600; text-transform: uppercase;">A inyectar del API-{{ $bloqueActivo }}</span>
                    </div>
                </div>

                <form action="{{ route('certificados.ingesta.inyectar') }}" method="POST" id="formInyeccionDirecta" class="d-flex flex-wrap flex-md-nowrap gap-3 align-items-center m-0">
                    @csrf

                    <input type="hidden" name="bloque_origen" value="{{ $bloqueActivo }}">

                    {{-- SELECTOR DE ESTADOS (Minimalista) --}}
                    <select name="id_car_sia_estados" class="minimal-select" required style="min-width: 170px;">
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- SELECTOR DE TIPOS (Minimalista) --}}
                    <select name="id_car_sia_tipos" class="minimal-select" required style="min-width: 170px;">
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ $loop->first ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>

                    {{-- BOTÓN PRINCIPAL --}}
                    <button type="submit" class="btn-modern btn-primary-modern rounded-pill ms-md-2 border-0 shadow-sm" id="btnInyectarDirecto" style="padding: 0.65rem 1.75rem; font-size: 0.95rem;">
                        <i class="fas fa-bolt btn-icon me-2" style="color: rgba(255, 255, 255, 0.8);"></i>
                        <span class="btn-text">Ejecutar Bloque {{ $bloqueActivo }}</span>
                        <span class="btn-spinner ms-2"></span>
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    {{-- SCRIPTS (VALIDACIÓN Y GRÁFICOS) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Lógica del formulario de inyección
            const formInyeccion = document.getElementById('formInyeccionDirecta');
            const btnInyectar = document.getElementById('btnInyectarDirecto');

            if(formInyeccion) {
                formInyeccion.addEventListener('submit', function(e) {
                    if (formInyeccion.checkValidity()) {
                        btnInyectar.classList.add('is-loading');
                        btnInyectar.disabled = true;
                    }
                });
            }

            // Inicialización de Chart.js Minimalista
            const ctx = document.getElementById('kpiChart').getContext('2d');
            const dataProcesados = {{ $kpi['procesados'] }};
            const dataPendientes = {{ $kpi['pendientes'] }};
            const dataAnulados = {{ $kpi['anulados'] }};

            if(dataProcesados === 0 && dataPendientes === 0 && dataAnulados === 0){
                new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels: ['Sin Datos'], datasets: [{ data: [1], backgroundColor: ['#e2e8f0'], borderWidth: 0 }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, cutout: '75%' }
                });
            } else {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pendientes', 'Procesados', 'Anulados'],
                        datasets: [{
                            data: [dataPendientes, dataProcesados, dataAnulados],
                            backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '70%',
                        plugins: {
                            legend: { display: true, position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { family: "'Inter', sans-serif", size: 11 } } },
                            tooltip: { backgroundColor: '#0f172a', titleFont: { family: "'Inter', sans-serif" }, bodyFont: { family: "'Inter', sans-serif" }, padding: 10, cornerRadius: 8, displayColors: false }
                        }
                    }
                });
            }

            // ==========================================
            // LÓGICA DE DRAG & DROP Y UX DE CARGA
            // ==========================================
            const fileInput = document.getElementById('archivoExcelMasivo');
            const fakeZone = document.getElementById('fakeUploadZone');
            const fileDisplay = document.getElementById('fileNameDisplay');
            const fakeIcon = document.getElementById('fakeUploadIcon');
            const uploadCard = document.getElementById('uploadCard');
            const uploadTitle = document.getElementById('uploadTitle');
            const uploadDesc = document.getElementById('uploadDesc');
            const btnUpload = document.getElementById('btnUploadSubmit');
            const formCargaMasiva = document.getElementById('formCargaMasiva');
            const dropWrapper = document.getElementById('dropWrapper');

            if(fileInput && dropWrapper) {

                // Evitar comportamiento por defecto
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropWrapper.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults (e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Efecto visual al arrastrar
                ['dragenter', 'dragover'].forEach(eventName => {
                    dropWrapper.addEventListener(eventName, () => fakeZone.classList.add('drag-active'), false);
                });

                // Quitar efecto al salir
                ['dragleave', 'drop'].forEach(eventName => {
                    dropWrapper.addEventListener(eventName, () => fakeZone.classList.remove('drag-active'), false);
                });

                // Manejar el Drop (soltar archivo)
                dropWrapper.addEventListener('drop', function(e) {
                    let dt = e.dataTransfer;
                    let files = dt.files;

                    if(files.length > 0) {
                        fileInput.files = files;
                        const event = new Event('change', { bubbles: true });
                        fileInput.dispatchEvent(event);
                    }
                }, false);

                // Lógica visual cuando cambia el input
                fileInput.addEventListener('change', function(e) {
                    if (this.files && this.files.length > 0) {
                        const archivo = this.files[0];
                        const pesoMB = (archivo.size / (1024 * 1024)).toFixed(2);

                        fileDisplay.innerHTML = `<span class="fw-bold fs-5" style="color: #065f46;">${archivo.name}</span><br><span style="font-size: 0.85rem;">(${pesoMB} MB) Listo para procesar</span>`;
                        fakeZone.style.borderColor = 'var(--color-success)';
                        fakeZone.style.backgroundColor = 'var(--color-success-soft)';
                        fakeIcon.className = 'fas fa-check-circle fs-1 mb-2';
                        fakeIcon.style.color = 'var(--color-success)';

                        uploadCard.style.backgroundColor = '#f0fdf4';
                        uploadCard.style.borderColor = '#86efac';
                        uploadTitle.innerHTML = `<i class="fas fa-rocket fs-4 me-2"></i> Archivo Validado`;
                        uploadTitle.style.color = '#166534';
                        uploadDesc.innerHTML = 'Haz clic en "Procesar Lote" para iniciar la lectura masiva.';
                        uploadDesc.style.color = '#15803d';

                        btnUpload.className = 'btn-modern btn-success-modern fw-bold text-nowrap shadow-sm px-5 py-3';
                        btnUpload.innerHTML = '<i class="fas fa-cogs me-2"></i> Procesar Lote';
                        btnUpload.style.pointerEvents = 'auto';
                        btnUpload.style.opacity = '1';
                    }
                });

                // Lógica de carga al hacer submit
                formCargaMasiva.addEventListener('submit', function() {
                    btnUpload.innerHTML = `<i class='fas fa-circle-notch fa-spin me-2'></i> Subiendo...`;
                    btnUpload.style.pointerEvents = 'none';
                    btnUpload.style.opacity = '0.7';
                    uploadCard.style.opacity = '0.7';
                });
            }
        });
    </script>
</x-base-layout>
