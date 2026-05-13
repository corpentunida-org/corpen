<x-base-layout>

    @if(!isset($registrosPrevia))
        {{-- ======================================================== --}}
        {{-- PASO 1: VISTA PRINCIPAL (EXPORTAR / IMPORTAR)            --}}
        {{-- ======================================================== --}}
        
        <div id="loading-overlay-1" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
            <div class="spinner-border text-primary mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
            <h2 class="fw-bold text-dark">Analizando Archivo...</h2>
            <p class="text-muted">Procesando miles de registros. No cierres esta ventana.</p>
        </div>

        <div class="app-container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    
                    <div class="mb-5 text-center">
                        <h3 class="fw-bold text-dark">Sincronización Excel</h3>
                        <p class="text-muted fs-7">Gestión bidireccional de la base de datos (Super Admin)</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0 bg-light-success text-success p-4 rounded-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 fs-3"></i>
                                <div>
                                    <span class="fw-bolder">¡Logrado!</span><br>
                                    <span class="fs-7">{{ session('success') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 bg-light-danger text-danger p-4 rounded-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-3 fs-3"></i>
                                <div>
                                    <span class="fw-bolder">Hubo un problema</span><br>
                                    <span class="fs-7">{{ $errors->first() }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-5">
                            <div class="row g-0">
                                
                                <div class="col-12 mb-5">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="fw-bold m-0 text-dark">Exportar a Excel</h5>
                                            <p class="text-muted fs-8 m-0">Descarga la tabla completa de AWS</p>
                                        </div>
                                        <a href="{{ route('contabilidad.sincronizar.descargar') }}" class="btn btn-light-primary btn-sm px-4 fw-bold rounded-pill">
                                            <i class="fas fa-download me-2"></i>Descargar Backup
                                        </a>
                                    </div>
                                </div>

                                <hr class="text-gray-200">

                                <div class="col-12 mt-5">
                                    <h5 class="fw-bold mb-3 text-dark">Restaurar / Importar desde Excel</h5>
                                    <form id="sync-form-1" action="{{ route('contabilidad.sincronizar.subir') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex flex-column gap-3">
                                            <input class="form-control form-control-sm bg-light border-0 px-4 py-2 rounded-pill" type="file" name="archivo_excel" accept=".xlsx, .xls" required>
                                            <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold rounded-pill py-2 shadow-sm">
                                                <i class="fas fa-search me-2"></i>Leer Archivo y Validar
                                            </button>
                                        </div>
                                        <p class="text-muted fs-9 mt-3 text-center">Formatos soportados: <strong>.xlsx</strong> y <strong>.xls</strong></p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <a href="{{ route('contabilidad.extractos.index') }}" class="text-muted fs-8 text-decoration-none hover-primary">
                            <i class="fas fa-arrow-left me-2"></i>Regresar al panel de extractos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form1 = document.getElementById('sync-form-1');
                if (form1) {
                    form1.addEventListener('submit', function() {
                        document.getElementById('loading-overlay-1').style.display = 'flex';
                        const btn = this.querySelector('button');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Analizando...';
                    });
                }
            });
        </script>

    @else
        {{-- ======================================================== --}}
        {{-- PASO 2: VISTA DE VALIDACIÓN Y RESOLVER HORAS             --}}
        {{-- ======================================================== --}}

        <div id="loading-overlay-2" class="bank-overlay" style="display: none;">
            <div class="bank-loader-card shadow-lg">
                <div class="shield-container">
                    <div class="pulse-ring"></div>
                    <div class="pulse-ring delay"></div>
                    <div class="shield-icon bg-primary">
                        <i class="fas fa-server text-white fs-1 fa-bounce"></i>
                    </div>
                </div>
                
                <h2 class="fw-bolder text-dark mt-5 mb-1 fs-2">Sincronizando con AWS</h2>
                <p id="loading-message" class="text-primary fw-bold fs-6 mb-5" style="min-height: 24px;">Aplicando Upsert Masivo (Insert/Update)...</p>
                
                <div class="timer-widget bg-light rounded-4 p-4 text-center border border-gray-200 w-100">
                    <span class="d-block text-uppercase fw-bolder text-muted fs-8 mb-1 tracking-wider">Tiempo estimado</span>
                    <div class="d-flex align-items-baseline justify-content-center">
                        <span id="countdown-timer" class="display-3 fw-bolder text-dark" style="letter-spacing: -2px;">0</span>
                        <span class="ms-2 fs-3 text-muted fw-bold">seg</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-container py-5" style="background-color: #f8f9fa;">
            <div id="js-alerta-duplicados" class="alert alert-danger d-flex align-items-center p-5 mx-3 mb-4 shadow-sm border-danger" style="display: none !important;">
                <i class="fas fa-exclamation-triangle fs-2hx text-danger me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger fw-bolder">Conflicto de Hash Detectado</h4>
                    <span>Hay <strong id="js-conteo-duplicados">0</strong> registros con Hash idéntico. Usa el botón mágico para diferenciarlos añadiendo segundos automáticamente.</span>
                </div>
            </div>

            <form id="form-sincronizar-2" action="{{ route('contabilidad.sincronizar.confirmar') }}" method="POST">
                @csrf
                <input type="hidden" name="registros_json" id="input_registros_json">

                <div class="d-flex align-items-center mb-4 px-3 pb-3 border-bottom border-gray-300">
                    <div class="symbol symbol-40px me-3">
                        <div class="symbol-label bg-dark text-white shadow-sm"><i class="fas fa-database text-white fs-3"></i></div>
                    </div>
                    <div>
                        <h3 class="fw-bold m-0 text-dark fs-4">Sincronización Maestra (AWS)</h3>
                        <div class="d-flex align-items-center gap-3 mt-1">
                            <span class="text-muted fs-8">Acceso: <strong>Super Administrador</strong></span>
                            <span class="badge bg-light-primary text-primary fw-bold px-2 py-1 fs-9 border border-primary">MODO UPSERT</span>
                        </div>
                    </div>
                    <div class="ms-auto d-flex gap-2 align-items-center">
                        <button type="button" id="btn-fix-duplicados" class="btn btn-sm btn-warning text-dark fw-bolder px-4 rounded-1 shadow-sm me-1" title="Añade segundos secuenciales a los duplicados">
                            <i class="fas fa-magic me-1 text-dark"></i> Resolver Horas Duplicadas
                        </button>
                        <button type="button" id="btn-toggle-duplicados" class="btn btn-sm btn-outline-danger fw-bold px-4 rounded-1 shadow-sm me-2">
                            <i class="fas fa-filter me-1"></i> Ver Solo Duplicados
                        </button>
                        <a href="{{ route('contabilidad.sincronizar.index') }}" class="btn btn-sm btn-light fw-bold px-4 rounded-1 border border-gray-300">Cancelar</a>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold px-4 rounded-1 shadow-sm">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Confirmar Sincronización
                        </button>
                    </div>
                </div>

                <div class="bg-white border border-gray-300 mx-3 sheets-wrapper shadow-sm">
                    <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                        <div class="sheet-tab active"><i class="fas fa-table me-2 fs-9"></i> DB_Restore_Previa</div>
                    </div>
                    <div class="table-container" id="resizable-container">
                        <table class="table-gsheets" id="main-table">
                            <thead>
                                <tr>
                                    <th class="col-index-header" style="width: 80px;">ID TRANS.</th>
                                    <th style="width: 210px;">FECHA Y HORA</th>
                                    <th style="width: 150px;">CÉDULA/REF</th>
                                    <th style="width: 150px;">VALOR INGRESO</th>
                                    <th style="width: 120px;">OFICINA</th>
                                    <th style="width: 80px;">ID CUENTA</th>
                                    <th style="width: 140px;">ESTADO</th>
                                    <th style="width: 250px;">HASH ÚNICO (AWS)</th>
                                    <th style="width: 90px; text-align: center;">VALIDACIÓN</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @foreach($registrosPrevia as $fila)
                                <tr class="data-row {{ $fila['es_duplicado'] ? 'bg-light-danger row-is-duplicate' : '' }}">
                                    <td class="col-index font-monospace bg-light">
                                        <input type="text" value="{{ $fila['id_transaccion'] }}" class="gs-input field-id text-center text-muted" readonly>
                                    </td>
                                    <td class="cell-editable">
                                        <input type="datetime-local" step="1" value="{{ $fila['fecha_movimiento'] }}" class="gs-input input-rehash field-fecha-hora">
                                    </td>
                                    <td class="cell-editable">
                                        <input type="text" value="{{ $fila['referencia_cedula'] }}" class="gs-input input-rehash field-cedula fw-bold text-gray-800">
                                    </td>
                                    <td class="cell-editable">
                                        <input type="text" value="{{ $fila['valor_ingreso'] }}" class="gs-input input-rehash field-valor text-end fw-bold text-success pe-2">
                                    </td>
                                    <td class="cell-editable">
                                        <input type="text" value="{{ $fila['referencia_oficina'] }}" class="gs-input field-oficina text-gray-700">
                                    </td>
                                    <td class="cell-editable">
                                        <input type="text" value="{{ $fila['id_con_cuentas_bancaria'] }}" class="gs-input input-rehash field-cuenta text-center fw-bold">
                                    </td>
                                    <td class="cell-editable">
                                        <select class="gs-input field-estado border-0 bg-transparent text-gray-700" style="appearance: none;">
                                            <option value="Pendiente" {{ $fila['estado_conciliacion'] == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="Conciliado_Auto" {{ $fila['estado_conciliacion'] == 'Conciliado_Auto' ? 'selected' : '' }}>Conciliado_Auto</option>
                                            <option value="Conciliado_Manual" {{ $fila['estado_conciliacion'] == 'Conciliado_Manual' ? 'selected' : '' }}>Conciliado_Manual</option>
                                            <option value="Anulado" {{ $fila['estado_conciliacion'] == 'Anulado' ? 'selected' : '' }}>Anulado</option>
                                        </select>
                                    </td>
                                    <td class="cell-readonly font-monospace text-muted">
                                        <input type="text" value="{{ $fila['hash_transaccion'] }}" class="gs-input field-hash fs-10" readonly>
                                    </td>
                                    <td class="text-center container-badge">
                                        @if($fila['es_duplicado'])
                                            <span class="badge bg-danger text-white fs-10 shadow-sm"><i class="fas fa-times me-1"></i> ERROR</span>
                                        @else
                                            <span class="badge bg-success text-white fs-10 shadow-sm"><i class="fas fa-check me-1"></i> OK</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-white border-top border-gray-300 px-3 py-1 d-flex justify-content-between align-items-center fs-9 text-dark font-monospace">
                        <div>Total a sincronizar: <strong id="js-total">{{ count($registrosPrevia) }}</strong> | Duplicados: <strong id="js-total-duplicados-footer" class="text-danger">0</strong></div>
                    </div>
                </div>
            </form>
        </div>

        <style>
            body, .app-container { font-family: 'Inter', 'Arial', sans-serif !important; }
            .bank-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(243, 246, 249, 0.85); backdrop-filter: blur(10px); z-index: 9999; display: flex; justify-content: center; align-items: center; }
            .bank-loader-card { background: #ffffff; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; display: flex; flex-direction: column; align-items: center; border: 1px solid rgba(0,0,0,0.05); }
            .shield-container { position: relative; width: 100px; height: 100px; display: flex; justify-content: center; align-items: center; }
            .shield-icon { width: 70px; height: 70px; border-radius: 50%; display: flex; justify-content: center; align-items: center; z-index: 3; box-shadow: 0 10px 20px rgba(0, 123, 255, 0.3); }
            .pulse-ring { position: absolute; width: 100%; height: 100%; background-color: rgba(0, 123, 255, 0.2); border-radius: 50%; z-index: 1; animation: pulse-animation 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite; }
            .pulse-ring.delay { animation-delay: 1s; }
            @keyframes pulse-animation { 0% { transform: scale(0.7); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
            .sheets-wrapper { border-radius: 0; overflow: hidden; display: flex; flex-direction: column; height: 65vh; }
            .table-container { overflow: auto; flex-grow: 1; position: relative; }
            .table-gsheets { border-collapse: collapse; table-layout: fixed; width: 100%; min-width: 1200px; font-size: 13px; }
            .table-gsheets th { position: sticky; top: 0; background-color: #f8f9fa; border: 1px solid #c0c0c0; color: #333; font-weight: bold; font-size: 12px; z-index: 2; padding: 6px 4px; vertical-align: top; }
            .table-gsheets td { border: 1px solid #d3d3d3; padding: 0; height: 26px; }
            .col-index-header { position: sticky; left: 0; z-index: 3 !important; }
            .col-index { position: sticky; left: 0; z-index: 1; border-right: 2px solid #c0c0c0 !important; }
            .gs-input { width: 100%; height: 100%; border: none; background: transparent; padding: 4px 6px; outline: none; }
            .cell-editable:has(.gs-input:focus) { outline: 2px solid #1a73e8; outline-offset: -2px; z-index: 10; background: #fff; }
            .bg-light-danger { background-color: #fff5f8 !important; }
            .hide-by-dup { display: none !important; }
            .sheet-tab { padding: 6px 16px; font-size: 12px; font-weight: bold; color: #666; background-color: #e8eaed; border-right: 1px solid #c0c0c0; border-top-right-radius: 4px; }
            .sheet-tab.active { background-color: #fff; color: #0056b3; }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const hashesExistentes = @json($hashesExistentes ?? []);
                let showOnlyDuplicates = false;

                function actualizarUI() {
                    const totalDuplicados = document.querySelectorAll('.row-is-duplicate').length;
                    const alerta = document.getElementById('js-alerta-duplicados');
                    if (totalDuplicados > 0) {
                        alerta.setAttribute('style', 'display: flex !important');
                        document.getElementById('js-conteo-duplicados').innerText = totalDuplicados;
                    } else {
                        alerta.setAttribute('style', 'display: none !important');
                    }
                    document.getElementById('js-total-duplicados-footer').innerText = totalDuplicados;
                }

                function recalcularHashYValidar() {
                    const rows = document.querySelectorAll('.data-row');
                    const hashCounts = {};

                    rows.forEach(row => {
                        const idC = row.querySelector('.field-cuenta').value.trim();
                        const fechaInput = row.querySelector('.field-fecha-hora').value;
                        const valor = parseFloat(row.querySelector('.field-valor').value) || 0;
                        const cedula = row.querySelector('.field-cedula').value.trim();
                        const hashField = row.querySelector('.field-hash');

                        if (fechaInput && idC) {
                            let datePart = fechaInput.replace(/[-T:]/g, '');
                            if(datePart.length === 12) datePart += '00'; 
                            if(datePart.length > 14) datePart = datePart.substring(0, 14); 
                            
                            const nuevoHash = `${idC}-${datePart}-${valor}-${cedula}`;
                            hashField.value = nuevoHash;
                            hashCounts[nuevoHash] = (hashCounts[nuevoHash] || 0) + 1;
                        }
                    });

                    rows.forEach(row => {
                        const hashValue = row.querySelector('.field-hash').value;
                        const badgeContainer = row.querySelector('.container-badge');
                        
                        if (hashesExistentes.includes(hashValue) || hashCounts[hashValue] > 1) {
                            row.classList.add('bg-light-danger', 'row-is-duplicate');
                            row.classList.remove('hide-by-dup');
                            badgeContainer.innerHTML = '<span class="badge bg-danger text-white fs-10 shadow-sm"><i class="fas fa-times me-1"></i> ERROR</span>';
                        } else {
                            row.classList.remove('bg-light-danger', 'row-is-duplicate');
                            badgeContainer.innerHTML = '<span class="badge bg-success text-white fs-10 shadow-sm"><i class="fas fa-check me-1"></i> OK</span>';
                            if (showOnlyDuplicates) row.classList.add('hide-by-dup');
                        }
                    });

                    actualizarUI();
                }

                document.getElementById('btn-fix-duplicados').addEventListener('click', function() {
                    const duplicateRows = document.querySelectorAll('.row-is-duplicate');
                    if (duplicateRows.length === 0) return alert('¡Excelente! No hay conflictos de Hash que resolver.');

                    let secondOffset = 1;
                    duplicateRows.forEach(row => {
                        const dateInput = row.querySelector('.field-fecha-hora');
                        let cv = dateInput.value; 
                        
                        if (cv) {
                            let [dateP, timeP] = cv.split('T');
                            let [Y, M, D] = dateP.split('-');
                            let timeParts = timeP.split(':');
                            let h = timeParts[0], m = timeParts[1], s = timeParts[2] || '00';
                            
                            let dObj = new Date(Y, M - 1, D, h, m, s);
                            dObj.setSeconds(dObj.getSeconds() + secondOffset);
                            secondOffset++;

                            const pad = n => String(n).padStart(2, '0');
                            dateInput.value = `${dObj.getFullYear()}-${pad(dObj.getMonth()+1)}-${pad(dObj.getDate())}T${pad(dObj.getHours())}:${pad(dObj.getMinutes())}:${pad(dObj.getSeconds())}`;
                        }
                    });
                    recalcularHashYValidar();
                });

                document.getElementById('btn-toggle-duplicados').addEventListener('click', function() {
                    showOnlyDuplicates = !showOnlyDuplicates;
                    this.classList.toggle('btn-outline-danger');
                    this.classList.toggle('btn-danger');
                    this.classList.toggle('text-white');
                    
                    document.querySelectorAll('.data-row').forEach(row => {
                        if (showOnlyDuplicates && !row.classList.contains('row-is-duplicate')) row.classList.add('hide-by-dup');
                        else row.classList.remove('hide-by-dup');
                    });
                });

                document.querySelectorAll('.input-rehash').forEach(input => {
                    input.addEventListener('change', recalcularHashYValidar);
                });

                actualizarUI(); 

                document.getElementById('form-sincronizar-2').addEventListener('submit', function(e) {
                    e.preventDefault();
                    this.querySelector('button[type="submit"]').disabled = true;
                    
                    let datos = [];
                    document.querySelectorAll('.data-row').forEach(f => {
                        datos.push({
                            id_transaccion: f.querySelector('.field-id').value,
                            fecha_movimiento: f.querySelector('.field-fecha-hora').value,
                            referencia_cedula: f.querySelector('.field-cedula').value,
                            valor_ingreso: f.querySelector('.field-valor').value,
                            referencia_oficina: f.querySelector('.field-oficina').value,
                            id_con_cuentas_bancaria: f.querySelector('.field-cuenta').value,
                            estado_conciliacion: f.querySelector('.field-estado').value,
                            hash_transaccion: f.querySelector('.field-hash').value
                        });
                    });

                    document.getElementById('input_registros_json').value = JSON.stringify(datos);
                    document.getElementById('loading-overlay-2').style.display = 'flex';

                    let time = Math.max(1, Math.ceil(datos.length / 500)); 
                    document.getElementById('countdown-timer').innerText = time;
                    
                    const timer = setInterval(() => {
                        time--;
                        if (time > 0) document.getElementById('countdown-timer').innerText = time;
                        else clearInterval(timer);
                    }, 1000);

                    this.submit();
                });
            });
        </script>
    @endif
</x-base-layout>