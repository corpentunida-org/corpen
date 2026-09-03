{{-- CARGA DE ARCHIVOS BASE Y GRÁFICO --}}
<div class="card-g overflow-hidden mb-4 shadow-sm" style="border: 1px solid var(--c-border); border-radius: var(--r-xl);">

    {{-- Cabecera / Botón del Acordeón --}}
    <button class="w-100 border-0 bg-white px-4 py-3 d-flex align-items-center justify-content-between text-start collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#panelCargaBase"
            aria-expanded="false"
            aria-controls="panelCargaBase"
            style="outline: none;">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 42px; height: 42px; background-color: var(--c-primary-soft);">
                <i class="fas fa-cloud-upload-alt" style="color: var(--c-primary); font-size: 1.1rem;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark" style="font-size: .95rem;">Panel de Archivos Base</h6>
                <p class="mb-0 text-muted" style="font-size: .8rem;">Área principal donde se cargan los archivos bases y se visualiza el estado del lote.</p>
            </div>
        </div>
        <span class="text-muted"><i class="fas fa-chevron-down"></i></span>
    </button>

    {{-- Contenido Colapsable --}}
    <div class="collapse" id="panelCargaBase">
        <div class="p-4 border-top" style="background-color: #f8fafc;">
            <div class="row g-4">

                {{-- Gráfico Columnas (Sin bordes extra para mantener minimalismo) --}}
                <div class="col-12 col-lg-4">
                    <div class="bg-white p-4 h-100 d-flex flex-column rounded-3 shadow-sm" style="border: 1px solid #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: .9rem;">
                                    <i class="fas fa-chart-bar me-2" style="color: var(--c-primary);"></i>Estado del Bloque
                                </h6>
                                <p class="mb-0 mt-1" style="font-size:.75rem; color:var(--c-muted);">
                                    Clic en la columna para filtrar la tabla
                                </p>
                            </div>
                        </div>
                        <div style="position:relative; height:200px; flex:1;">
                            <canvas id="kpiChart" aria-label="Gráfico de estado"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Upload zona (Limpia y corporativa) --}}
                <div class="col-12 col-lg-8">
                    <div class="bg-white p-4 h-100 d-flex flex-column justify-content-center rounded-3 shadow-sm" id="uploadCard" style="border: 1px solid #e2e8f0;">
                        <h6 class="fw-bold text-dark mb-1" id="uploadTitle" style="font-size: .95rem;">
                            <i class="fas fa-file-excel me-2" id="uploadIcon" style="color: #10b981;"></i>
                            Cargar Lote de Facturas
                        </h6>
                        <p class="mb-3" style="font-size:.8rem; color:var(--c-muted);" id="uploadDesc">
                            Arrastra tu archivo <strong>.xlsx / .xls / .csv</strong> o haz clic para seleccionarlo. Optimizado para +30.000 registros.
                        </p>

                        <form action="{{ route('certificados.ingesta.cargar') }}"
                              method="POST" enctype="multipart/form-data"
                              id="formCargaMasiva"
                              class="d-flex flex-column gap-3">
                            @csrf

                            <!-- Selector de Periodo -->
                            <div>
                                <select name="id_periodo" class="input-g shadow-sm w-100" required aria-label="Seleccionar Periodo" style="font-size: .85rem; padding: 0.5rem 1rem;">
                                    <option value="" disabled selected>1. Elige a qué periodo pertenece este lote...</option>
                                    @foreach($periodos as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->anio }}-{{ str_pad($p->mes, 2, '0', STR_PAD_LEFT) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Zona de Drop y Botón -->
                            <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-stretch">
                                <div class="position-relative flex-grow-1" id="dropWrapper">
                                    <input type="file" name="archivo_excel" id="archivoExcelInput"
                                           accept=".xlsx,.xls,.csv" required
                                           class="position-absolute w-100 h-100 top-0 start-0"
                                           style="opacity:0; cursor:pointer; z-index:10;"
                                           aria-label="Seleccionar archivo Excel">

                                    <div class="drop-zone d-flex flex-column align-items-center justify-content-center rounded-3" id="dropZoneVisual" style="background-color: #f8fafc; border: 2px dashed #cbd5e1; padding: 1.5rem;">
                                        <i class="fas fa-cloud-upload-alt fs-3 mb-2" id="dropIcon" style="color: #94a3b8;"></i>
                                        <span class="fw-medium text-dark" id="dropLabel" style="font-size:.85rem;">
                                            2. Arrastra aquí o haz clic para buscar
                                        </span>
                                        <span class="mt-1" style="font-size:.7rem; color:var(--c-muted);" id="dropMeta">
                                            .xlsx · .xls · .csv — máx. 50 MB
                                        </span>
                                    </div>
                                </div>

                                <button type="submit" id="btnUploadSubmit"
                                        class="btn-g btn-outline-g fw-bold d-flex flex-column justify-content-center align-items-center"
                                        disabled aria-disabled="true"
                                        style="opacity:.45; pointer-events:none; min-width:140px;">
                                    <i class="fas fa-upload btn-ico mb-1 fs-5"></i>
                                    <span class="btn-label" style="font-size: .85rem;">Subir Lote</span>
                                    <span class="btn-spinner"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
