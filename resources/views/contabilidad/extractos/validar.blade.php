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
        
        <form id="form-importacion" action="{{ route('contabilidad.extractos.confirmar-importacion') }}" method="POST">
            @csrf

            {{-- INPUT CLAVE DONDE VIAJARÁN TODOS LOS DATOS EN MASA (JSON) --}}
            <input type="hidden" name="registros_json" id="input_registros_json">

            {{-- Barra de Herramientas Estilo Google Workspace --}}
            <div class="d-flex align-items-center mb-4 px-3 pb-3 border-bottom border-gray-300">
                <div class="symbol symbol-40px me-3">
                    <div class="symbol-label bg-success text-white shadow-sm">
                        <i class="fas fa-table text-white fs-3"></i>
                    </div>
                </div>
                <div>
                    <h3 class="fw-bold m-0 text-dark fs-4">Previsualización de Extracto</h3>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <span class="text-muted fs-8">Cuenta: <strong>{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</strong></span>
                        <span class="badge bg-light-success text-success fw-bold px-2 py-1 fs-9 border border-success">GUARDADO AUTOMÁTICO DESACTIVADO</span>
                    </div>
                </div>
                <div class="ms-auto d-flex gap-2 align-items-center">
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
                                
                                {{-- COLUMNA: FECHA --}}
                                <th class="resizable-th">
                                    <div class="th-content">
                                        <span>FECHA MOVIMIENTO</span>
                                        <button type="button" class="btn-filter" data-col="0"><i class="fas fa-filter"></i></button>
                                    </div>
                                    <div class="filter-container" id="filter-col-0">
                                        <input type="text" class="form-control form-control-sm gs-filter-input" placeholder="Filtrar fecha...">
                                    </div>
                                    <div class="resizer"></div>
                                </th>

                                {{-- COLUMNA: HASH --}}
                                <th class="resizable-th">
                                    <div class="th-content">
                                        <span>HASH TRANSACCIÓN</span>
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
                            <tr class="data-row">
                                <td class="col-index">{{ $index + 1 }}</td>
                                
                                {{-- FECHA EDITABLE --}}
                                @php
                                    $f = $fila['fecha_movimiento'];
                                    $dateValue = strlen($f) == 8 ? substr($f,0,4).'-'.substr($f,4,2).'-'.substr($f,6,2) : '';
                                @endphp
                                <td class="cell-editable">
                                    <input type="date" value="{{ $dateValue }}" class="gs-input date-sync" data-target="hidden-date-{{ $index }}">
                                    {{-- El controlador recibirá este campo oculto con formato Ymd dentro del JSON --}}
                                    <input type="hidden" id="hidden-date-{{ $index }}" class="field-fecha" value="{{ $fila['fecha_movimiento'] }}">
                                </td>
                                
                                <td class="cell-readonly font-monospace text-muted">
                                    <input type="text" value="{{ $fila['hash_transaccion'] }}" class="gs-input" readonly>
                                </td>
                                
                                <td class="cell-editable">
                                    {{-- Se quitó el atributo name y se agregó class="field-cedula" --}}
                                    <input type="text" value="{{ $fila['referencia_cedula'] }}" class="gs-input field-cedula text-gray-800 fw-bold">
                                </td>
                                
                                <td class="cell-editable">
                                    {{-- Se quitó el atributo name y se agregó class="field-valor" --}}
                                    <input type="text" value="{{ $fila['valor_ingreso'] }}" class="gs-input field-valor text-end fw-bold text-success pe-2" required>
                                </td>

                                <td class="cell-editable">
                                    {{-- Se quitó el atributo name y se agregó class="field-oficina" --}}
                                    <input type="text" value="{{ $fila['referencia_oficina'] }}" class="gs-input field-oficina text-gray-700">
                                </td>

                                <td class="text-center bg-light">
                                    <span class="badge bg-light-warning text-warning fw-bolder fs-9">PENDIENTE</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-muted fs-8 italic">El archivo no tiene el formato correcto.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Barra de Estado Inferior --}}
                <div class="bg-white border-top border-gray-300 px-3 py-1 d-flex justify-content-between align-items-center fs-9 text-dark font-monospace">
                    <div>Filas: <strong id="total-rows">{{ count($registrosPrevia) }}</strong> | Resultados filtro: <strong id="filtered-rows">{{ count($registrosPrevia) }}</strong></div>
                    <div><i class="fas fa-info-circle text-primary me-1"></i> Optimizado con JSON (Supera límites de PHP)</div>
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
            min-width: 900px;
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
        }
        
        .gs-input:focus {
            background-color: #fff;
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
            
            // 1. SINCRONIZACIÓN DE FECHAS
            const dateInputs = document.querySelectorAll('.date-sync');
            dateInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const targetId = this.getAttribute('data-target');
                    const hiddenInput = document.getElementById(targetId);
                    hiddenInput.value = this.value.replace(/-/g, '');
                });
            });

            // 2. REDIMENSIONAMIENTO DE COLUMNAS (RESIZER)
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

            // 3. SISTEMA DE FILTROS ENCABEZADOS
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

            filterInputs.forEach((input, index) => {
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
                    const inputsInRow = row.querySelectorAll('.gs-input'); 
                    
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
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                filteredRowsCount.innerText = visibleCount;
            }

            // 4. GENERACIÓN DE JSON Y ANIMACIÓN DEL MODAL AL GUARDAR
            const form = document.getElementById('form-importacion');
            const overlay = document.getElementById('loading-overlay');
            const timerElement = document.getElementById('countdown-timer');
            const totalRows = parseInt(document.getElementById('total-rows').innerText);

            form.addEventListener('submit', function(e) {
                // Prevenir el envío automático
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                // RECOLECTAR DATOS DE LA TABLA Y CREAR EL JSON
                let datosJson = [];
                const filas = document.querySelectorAll('.data-row');

                filas.forEach(fila => {
                    let fecha = fila.querySelector('.field-fecha').value;
                    let cedula = fila.querySelector('.field-cedula').value;
                    let valor = fila.querySelector('.field-valor').value;
                    let oficina = fila.querySelector('.field-oficina').value;

                    datosJson.push({
                        fecha_movimiento: fecha,
                        referencia_cedula: cedula,
                        valor_ingreso: valor,
                        referencia_oficina: oficina
                    });
                });

                // Insertar el JSON en el input oculto
                document.getElementById('input_registros_json').value = JSON.stringify(datosJson);

                // Mostrar overlay y empezar el contador
                overlay.style.display = 'flex';

                let timeRemaining = Math.max(1, Math.ceil((totalRows * 30) / 1000));
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

                // Enviar el formulario manualmente ahora que tiene el JSON cargado
                form.submit();
            });
        });
    </script>
</x-base-layout>