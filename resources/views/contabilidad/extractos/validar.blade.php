<x-base-layout>
    {{-- MODAL DE CARGA (Bloqueo de Pantalla) --}}
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
        <div class="spinner-border text-primary mb-4" style="width: 4rem; height: 4rem; border-width: 0.3em;" role="status"></div>
        <h2 class="fw-bolder text-dark fs-1">Guardando Registros...</h2>
        <p class="text-muted fs-5 mb-5">Por favor, no recargues ni cierres esta ventana.</p>
        
        <div class="bg-light-primary border border-primary border-dashed rounded-3 px-5 py-4 text-center">
            <span class="fs-3 fw-bold text-primary d-block mb-1">Tiempo estimado restante</span>
            <div class="display-4 fw-bolder text-dark">
                <span id="countdown-timer">0</span> <span class="fs-3 text-muted">segundos</span>
            </div>
        </div>
    </div>

    <div class="app-container py-5" style="background-color: #f8f9fa;">

        {{-- RESUMEN DE VALIDACIÓN (Detección de Duplicados Dinámica) --}}
        @php
            $duplicadosCount = collect($registrosPrevia)->where('es_duplicado', true)->count();
            $totalCount = count($registrosPrevia);
        @endphp

        <div id="js-alerta-duplicados" class="alert alert-danger d-flex align-items-center p-5 mx-3 mb-4 shadow-sm border-danger" style="display: {{ $duplicadosCount > 0 ? 'flex' : 'none' }} !important;">
            <i class="fas fa-exclamation-triangle fs-2hx text-danger me-4"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-danger fw-bolder">Registros Duplicados Detectados</h4>
                <span>Se han identificado <strong id="js-conteo-duplicados">{{ $duplicadosCount }}</strong> registros que ya existen en la base de datos (marcados en rojo). Serán omitidos automáticamente al procesar para evitar duplicidad contable.</span>
            </div>
        </div>
        
        <form id="form-importacion" action="{{ route('contabilidad.extractos.confirmar-importacion') }}" method="POST">
            @csrf

            {{-- INPUTS CLAVE PARA JAVASCRIPT --}}
            <input type="hidden" name="registros_json" id="input_registros_json">
            <input type="hidden" id="id-cuenta-js" value="{{ $cuenta->id }}">

            {{-- Barra de Herramientas Estilo Google Workspace --}}
            <div class="d-flex align-items-center mb-4 px-3 pb-3 border-bottom border-gray-300">
                <div class="symbol symbol-40px me-3">
                    <div class="symbol-label bg-success text-white shadow-sm">
                        <i class="fas fa-table text-white fs-3"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold m-0 text-dark fs-4">Previsualización y Edición de Extracto</h3>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <span class="text-muted fs-8">Cuenta: <strong>{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</strong></span>
                        <span class="badge bg-light-success text-success fw-bold px-2 py-1 fs-9 border border-success">SISTEMA DE HASH DINÁMICO</span>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2 align-items-center">
                    {{-- BOTÓN NUEVO: FILTRAR DUPLICADOS --}}
                    <button type="button" id="btn-toggle-duplicados" class="btn btn-sm btn-outline-danger fw-bold px-4 rounded-1 shadow-sm me-2">
                        <i class="fas fa-filter me-1"></i> Ver Solo Duplicados
                    </button>
                    
                    <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-light fw-bold px-4 rounded-1 border border-gray-300">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-4 rounded-1 shadow-sm">
                        <i class="fas fa-check me-1"></i> Aprobar y Guardar
                    </button>
                </div>
            </div>

            {{-- Contenedor de la Hoja de Cálculo --}}
            <div class="bg-white border border-gray-300 mx-3 sheets-wrapper shadow-sm">
                
                {{-- Pestaña Inferior Simulada --}}
                <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                    <div class="sheet-tab active">
                        <i class="fas fa-list me-2 fs-9"></i> Extracto_{{ date('Ymd') }}
                    </div>
                </div>

                {{-- Contenedor con Scroll --}}
                <div class="table-container" id="resizable-container">
                    <table class="table-gsheets" id="main-table">
                        <thead>
                            <tr>
                                <th class="col-index-header"></th>
                                
                                {{-- COLUMNA: FECHA Y HORA --}}
                                <th class="resizable-th" style="width: 230px;">
                                    <div class="th-content">
                                        <span>FECHA Y HORA MOVIMIENTO</span>
                                        <button type="button" class="btn-filter" data-col="0"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-0">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar fecha...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                {{-- COLUMNA: HASH --}}
                                <th class="resizable-th" style="width: 300px;">
                                    <div class="th-content">
                                        <span>HASH TRANSACCIÓN (ID ÚNICO)</span>
                                        <button type="button" class="btn-filter" data-col="1"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-1">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar hash...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                {{-- COLUMNA: CÉDULA --}}
                                <th class="resizable-th">
                                    <div class="th-content">
                                        <span>CÉDULA/REF</span>
                                        <button type="button" class="btn-filter" data-col="2"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-2">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar cédula...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                {{-- COLUMNA: VALOR --}}
                                <th class="resizable-th">
                                    <div class="th-content">
                                        <span>VALOR INGRESO</span>
                                        <button type="button" class="btn-filter" data-col="3"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-3">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar valor...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                {{-- COLUMNA: OFICINA --}}
                                <th class="resizable-th">
                                    <div class="th-content">
                                        <span>OFICINA</span>
                                        <button type="button" class="btn-filter" data-col="4"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-4">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar oficina...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                <th class="resizable-th text-center">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @forelse($registrosPrevia as $index => $fila)
                            <tr class="data-row {{ $fila['es_duplicado'] ? 'bg-light-danger row-is-duplicate' : '' }}">
                                <td class="col-index">{{ $index + 1 }}</td>
                                
                                {{-- FECHA Y HORA EDITABLE (Input datetime-local) --}}
                                <td class="cell-editable">
                                    <input type="datetime-local" 
                                           value="{{ $fila['fecha_vista'] }}" 
                                           class="gs-input input-rehash field-fecha-hora">
                                </td>
                                
                                {{-- HASH QUE SE ACTUALIZA DINÁMICAMENTE POR JS --}}
                                <td class="cell-readonly font-monospace text-muted">
                                    <input type="text" value="{{ $fila['hash_transaccion'] }}" class="gs-input field-hash" readonly>
                                </td>
                                
                                <td class="cell-editable">
                                    <input type="text" value="{{ $fila['referencia_cedula'] }}" class="gs-input input-rehash field-cedula text-gray-800 fw-bold">
                                </td>
                                
                                <td class="cell-editable">
                                    <input type="text" value="{{ $fila['valor_ingreso'] }}" class="gs-input input-rehash field-valor text-end fw-bold text-success pe-2" required>
                                </td>

                                <td class="cell-editable">
                                    <input type="text" value="{{ $fila['referencia_oficina'] }}" class="gs-input field-oficina text-gray-700">
                                </td>

                                <td class="text-center container-badge">
                                    @if($fila['es_duplicado'])
                                        <span class="badge bg-danger text-white fw-bolder fs-9 shadow-sm">
                                            <i class="fas fa-copy me-1 text-white"></i> DUPLICADO
                                        </span>
                                    @else
                                        <span class="badge bg-light-warning text-warning fw-bolder fs-9">PENDIENTE</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-muted fs-8 italic">El archivo no tiene el formato correcto o está vacío.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Barra de Estado Inferior --}}
                <div class="bg-white border-top border-gray-300 px-3 py-1 d-flex justify-content-between align-items-center fs-9 text-dark font-monospace">
                    <div>Filas totales: <strong id="total-rows">{{ $totalCount }}</strong> | Duplicados: <strong id="js-total-duplicados-footer" class="text-danger">{{ $duplicadosCount }}</strong> | Visibles: <strong id="filtered-rows">{{ $totalCount }}</strong></div>
                    <div><i class="fas fa-info-circle text-primary me-1"></i> El Hash ID se sincroniza automáticamente al editar celdas clave.</div>
                </div>
            </div>
        </form>
    </div>

    <style>
        body, .app-container { font-family: 'Arial', sans-serif !important; }
        
        .sheets-wrapper {
            border-radius: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 65vh;
        }

        .table-container {
            overflow: auto;
            flex-grow: 1;
            position: relative;
        }

        .table-gsheets {
            border-collapse: collapse;
            table-layout: fixed;
            width: 100%;
            min-width: 1100px;
            font-size: 13px;
        }

        .table-gsheets th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            border: 1px solid #c0c0c0;
            color: #333;
            font-weight: bold;
            font-size: 12px;
            z-index: 2;
            padding: 0;
            vertical-align: top;
        }

        .table-gsheets td {
            border: 1px solid #d3d3d3;
            padding: 0;
            height: 24px;
            position: relative;
        }

        .col-index-header {
            width: 40px;
            min-width: 40px;
            position: sticky;
            left: 0;
            z-index: 3 !important;
            background-color: #f8f9fa;
        }

        .col-index {
            position: sticky;
            left: 0;
            background-color: #f8f9fa;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-right: 2px solid #c0c0c0 !important;
            z-index: 1;
            width: 40px;
            min-width: 40px;
        }

        .gs-input {
            width: 100%;
            height: 100%;
            border: none;
            background: transparent;
            padding: 4px 8px;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
            font-size: inherit;
        }
        
        .cell-readonly { background-color: #f8f9fa; }
        
        .cell-editable:has(.gs-input:focus) {
            outline: 2px solid #1a73e8;
            outline-offset: -2px;
            z-index: 10;
            background: #fff;
        }
        
        .gs-input:focus {
            background-color: #fff;
        }

        .bg-light-danger {
            background-color: #fff5f8 !important;
        }

        /* NUEVA CLASE PARA EL FILTRO DE DUPLICADOS */
        .hide-by-dup {
            display: none !important;
        }

        .resizable-th { position: relative; }
        .resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            cursor: col-resize;
            user-select: none;
            height: 100%;
            z-index: 5;
        }
        .resizer:hover, .resizer.resizing { background-color: #1a73e8; opacity: 0.5; }

        .th-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 8px;
            border-bottom: 1px solid transparent;
        }
        .btn-filter {
            background: none; border: none; color: #a0a0a0; cursor: pointer; padding: 2px 4px; border-radius: 3px;
        }
        .btn-filter:hover, .btn-filter.active { background-color: #e8eaed; color: #188038; }
        
        .filter-container {
            display: none;
            padding: 4px;
            background: #fff;
            border-top: 1px solid #c0c0c0;
        }
        .filter-container.show { display: block; }
        .gs-filter-input { font-size: 11px; padding: 2px 6px; height: 22px; border-radius: 2px; }

        .sheet-tab {
            padding: 6px 16px; font-size: 12px; font-weight: bold; color: #666;
            background-color: #e8eaed; border-right: 1px solid #c0c0c0; border-top-right-radius: 4px;
        }
        .sheet-tab.active { background-color: #fff; color: #188038; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            const hashesExistentes = @json($hashesExistentes ?? []);
            const idCuenta = document.getElementById('id-cuenta-js').value;
            
            // VARIABLES GLOBALES DEL NUEVO FILTRO
            const btnToggleDuplicados = document.getElementById('btn-toggle-duplicados');
            let showOnlyDuplicates = false;

            // --- 1. LÓGICA DE ACTUALIZACIÓN DE CONTADORES Y ALERTAS EN TIEMPO REAL ---
            function actualizarContadoresGlobales() {
                const totalDuplicados = document.querySelectorAll('.row-is-duplicate').length;
                
                // Actualizar Alerta Superior
                const alerta = document.getElementById('js-alerta-duplicados');
                const conteoTexto = document.getElementById('js-conteo-duplicados');
                
                if (totalDuplicados > 0) {
                    alerta.setAttribute('style', 'display: flex !important');
                    conteoTexto.innerText = totalDuplicados;
                } else {
                    alerta.setAttribute('style', 'display: none !important');
                }

                // Actualizar Footer
                document.getElementById('js-total-duplicados-footer').innerText = totalDuplicados;
            }

            // --- 2. LÓGICA DE RECALCULADO DE HASH Y VALIDACIÓN ---
            function recalcularHashYValidar(row) {
                const fechaInput = row.querySelector('.field-fecha-hora').value; // Formato: YYYY-MM-DDTHH:mm
                const cedula = row.querySelector('.field-cedula').value.trim();
                const valor = parseFloat(row.querySelector('.field-valor').value) || 0;
                const hashField = row.querySelector('.field-hash');
                const badgeContainer = row.querySelector('.container-badge');

                if (fechaInput) {
                    // Convertir fecha a formato compacto YYYYMMDDHHmmss
                    let datePart = fechaInput.replace(/[-T:]/g, '');
                    if(datePart.length === 12) datePart += '00';
                    
                    const nuevoHash = `${idCuenta}-${datePart}-${valor}-${cedula}`;
                    hashField.value = nuevoHash;

                    // Verificar contra la lista de hashes que vienen de la BD
                    if (hashesExistentes.includes(nuevoHash)) {
                        row.classList.add('bg-light-danger', 'row-is-duplicate');
                        row.classList.remove('hide-by-dup'); // Si se vuelve duplicado, aseguramos que se vea
                        badgeContainer.innerHTML = '<span class="badge bg-danger text-white fw-bolder fs-9 shadow-sm"><i class="fas fa-copy me-1 text-white"></i> DUPLICADO</span>';
                    } else {
                        row.classList.remove('bg-light-danger', 'row-is-duplicate');
                        badgeContainer.innerHTML = '<span class="badge bg-light-warning text-warning fw-bolder fs-9">PENDIENTE</span>';
                        // Si estamos en modo "Ver solo duplicados" y este registro se arregló, lo ocultamos
                        if (showOnlyDuplicates) {
                            row.classList.add('hide-by-dup');
                        }
                    }
                }
                
                // Llamar a actualizar la UI global cada vez que una fila cambia de estado
                actualizarContadoresGlobales();
                applyFilters(); // Recalcular contador de "Visibles" en el footer
            }

            // EVENTO DEL BOTÓN: VER SOLO DUPLICADOS
            btnToggleDuplicados.addEventListener('click', function() {
                showOnlyDuplicates = !showOnlyDuplicates;
                
                if (showOnlyDuplicates) {
                    this.classList.remove('btn-outline-danger');
                    this.classList.add('btn-danger', 'text-white');
                    this.innerHTML = '<i class="fas fa-filter me-1"></i> Mostrando Duplicados';
                } else {
                    this.classList.add('btn-outline-danger');
                    this.classList.remove('btn-danger', 'text-white');
                    this.innerHTML = '<i class="fas fa-filter me-1"></i> Ver Solo Duplicados';
                }
                
                document.querySelectorAll('.data-row').forEach(row => {
                    // Si el modo está activo y la fila NO es duplicada, la ocultamos
                    if (showOnlyDuplicates && !row.classList.contains('row-is-duplicate')) {
                        row.classList.add('hide-by-dup');
                    } else {
                        row.classList.remove('hide-by-dup');
                    }
                });
                
                applyFilters(); // Recalcula el número de filas visibles en el footer
            });

            // Escuchar cambios en los inputs clave (Fecha, Cédula, Valor)
            document.querySelectorAll('.input-rehash').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('.data-row');
                    recalcularHashYValidar(row);
                });
            });

            // --- 3. REDIMENSIONAMIENTO DE COLUMNAS ---
            const resizers = document.querySelectorAll('.resizer');
            let startX, startWidth, currentTh;

            resizers.forEach(resizer => {
                resizer.addEventListener('mousedown', function(e) {
                    currentTh = resizer.parentElement;
                    startX = e.pageX;
                    startWidth = currentTh.offsetWidth;
                    currentTh.classList.add('resizing');
                    
                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                });
            });

            function mouseMoveHandler(e) {
                if (currentTh) {
                    const newWidth = startWidth + (e.pageX - startX);
                    currentTh.style.width = `${newWidth}px`;
                    currentTh.style.minWidth = `${newWidth}px`;
                }
            }

            function mouseUpHandler() {
                if (currentTh) currentTh.classList.remove('resizing');
                document.removeEventListener('mousemove', mouseMoveHandler);
                document.removeEventListener('mouseup', mouseUpHandler);
            }

            // --- 4. SISTEMA DE FILTROS ---
            const filterButtons = document.querySelectorAll('.btn-filter');
            const filterInputs = document.querySelectorAll('.gs-filter-input');
            const tableRows = document.querySelectorAll('.data-row');
            const filteredRowsCount = document.getElementById('filtered-rows');

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const colIndex = this.getAttribute('data-col');
                    const filterContainer = document.getElementById('filter-col-' + colIndex);
                    this.classList.toggle('active');
                    filterContainer.classList.toggle('show');
                    if (filterContainer.classList.contains('show')) {
                        filterContainer.querySelector('input').focus();
                    } else {
                        filterContainer.querySelector('input').value = '';
                        applyFilters();
                    }
                });
            });

            filterInputs.forEach((input) => {
                input.addEventListener('keyup', applyFilters);
            });

            function applyFilters() {
                const activeFilters = [];
                filterInputs.forEach((input, index) => {
                    if (input.value.trim() !== '') {
                        activeFilters.push({ index: index, value: input.value.trim().toLowerCase() });
                    }
                });

                let visibleCount = 0;
                tableRows.forEach(row => {
                    let match = true;
                    // Mapeo de columnas para el filtro según el orden de la tabla
                    const inputsInRow = [
                        row.querySelector('.field-fecha-hora'),
                        row.querySelector('.field-hash'),
                        row.querySelector('.field-cedula'),
                        row.querySelector('.field-valor'),
                        row.querySelector('.field-oficina')
                    ];
                    
                    activeFilters.forEach(filter => {
                        const cellInput = inputsInRow[filter.index];
                        if (cellInput) {
                            const cellValue = cellInput.value.toLowerCase();
                            if (!cellValue.includes(filter.value)) {
                                match = false;
                            }
                        }
                    });

                    if (match) {
                        row.style.display = '';
                        // Contamos como visible solo si NO está oculta por el botón de duplicados
                        if (!row.classList.contains('hide-by-dup')) {
                            visibleCount++;
                        }
                    } else {
                        row.style.display = 'none';
                    }
                });
                filteredRowsCount.innerText = visibleCount;
            }

            // --- 5. GENERACIÓN DE JSON Y ENVÍO MASIVO ---
            const form = document.getElementById('form-importacion');
            const overlay = document.getElementById('loading-overlay');
            const timerElement = document.getElementById('countdown-timer');
            const totalRows = parseInt(document.getElementById('total-rows').innerText);

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                let datosJson = [];
                const filas = document.querySelectorAll('.data-row');

                filas.forEach(fila => {
                    datosJson.push({
                        fecha_movimiento: fila.querySelector('.field-fecha-hora').value,
                        referencia_cedula: fila.querySelector('.field-cedula').value,
                        valor_ingreso: fila.querySelector('.field-valor').value,
                        referencia_oficina: fila.querySelector('.field-oficina').value
                    });
                });

                document.getElementById('input_registros_json').value = JSON.stringify(datosJson);

                overlay.style.display = 'flex';

                let timeRemaining = Math.max(1, Math.ceil((totalRows * 50) / 1000));
                timerElement.innerText = timeRemaining;

                const countdown = setInterval(() => {
                    timeRemaining--;
                    if (timeRemaining > 0) {
                        timerElement.innerText = timeRemaining;
                    } else {
                        timerElement.innerText = "1";
                        document.querySelector('.fs-3.text-muted').innerText = "Finalizando...";
                        clearInterval(countdown);
                    }
                }, 1000);

                form.submit();
            });
        });
    </script>
</x-base-layout>