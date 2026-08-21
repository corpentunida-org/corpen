<x-base-layout>
    <style>
        /* Paleta de Colores Pasteles Soft UI */
        .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; border: none; }
        .bg-pastel-info { background-color: #e0f7fa !important; color: #00838f !important; border: none; }
        .bg-pastel-warning { background-color: #fff9c4 !important; color: #f57f17 !important; border: none; }
        .bg-pastel-success { background-color: #e8f5e9 !important; color: #2e7d32 !important; border: none; }
        .bg-pastel-danger { background-color: #ffebee !important; color: #c62828 !important; border: none; }
        
        .card-custom { border-radius: 20px; background: #ffffff; border: 1px solid #f0f0f0; }
        .table-hover tbody tr:hover { background-color: #fcfdfe !important; transition: all 0.2s ease; }
        
        .btn-pastel-primary { background-color: #4a90e2; color: white; border: none; transition: all 0.3s ease; }
        .btn-pastel-primary:hover { background-color: #357abd; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3); color: white; }
        
        .search-minimal {
            background-color: #f8f9fa; border: 1px solid #ececec; border-radius: 12px; padding: 10px 15px; transition: all 0.3s ease;
        }
        .search-minimal:focus { background-color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-color: #4a90e2; outline: none; }
        
        .form-select-custom { background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 12px; padding: 0.6rem 1rem; transition: all 0.3s ease;}
        .form-select-custom:focus { background-color: #fff; box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.1); border-color: #4a90e2; }
    </style>

    <div class="app-container py-4">
        
        {{-- ======================================================= --}}
        {{-- ENCABEZADO Y BOTÓN DE INYECCIÓN AL MOTOR --}}
        {{-- ======================================================= --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="symbol-label bg-pastel-warning me-4 shadow-sm" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 18px;">
                    <i class="fas fa-database text-warning fs-3"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold m-0" style="color: #2c3e50; letter-spacing: -0.5px;">Ingesta ERP (Staging)</h1>
                    <p class="text-muted mt-1 mb-0">Recepción de Lotes Crudos e Inyección al Motor SIA</p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                {{-- BOTÓN: Abre el modal de configuración en lugar de procesar directo --}}
                <button type="button" class="btn btn-pastel-primary shadow-sm rounded-pill px-4 py-2 fw-bold" 
                        data-bs-toggle="modal" data-bs-target="#modalConfigurarInyeccion"
                        @if($totalPendientes == 0) disabled @endif>
                    <i class="fas fa-play me-2"></i> Procesar Lotes ({{ $totalPendientes }})
                </button>
            </div>
        </div>

        {{-- ALERTAS GLOBALES --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4">
                <ul class="mb-0 ps-3">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        {{-- ======================================================= --}}
        {{-- TARJETA: CARGA DE ARCHIVO EXCEL --}}
        {{-- ======================================================= --}}
        <div class="card card-custom shadow-sm border-0 mb-4 bg-pastel-info">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold text-dark mb-1"><i class="fas fa-file-excel text-success me-2"></i> Cargar Lote Inicial (Excel/CSV)</h6>
                    <span class="text-muted fs-8">Sube el archivo extraído del ERP para poblar la tabla de staging.</span>
                </div>
                <form action="{{ route('certificados.ingesta.cargar') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                    @csrf
                    <input class="form-control form-control-sm bg-white border-0 shadow-sm" style="border-radius: 10px;" type="file" name="archivo_excel" accept=".xlsx, .xls, .csv" required>
                    <button type="submit" class="btn btn-success shadow-sm rounded-pill px-4 fw-bold btn-sm text-nowrap">
                        <i class="fas fa-upload me-2"></i> Cargar
                    </button>
                </form>
            </div>
        </div>

        {{-- ======================================================= --}}
        {{-- MATRIZ DE PREVISUALIZACIÓN (STAGING) --}}
        {{-- ======================================================= --}}
        <div class="card card-custom shadow-sm border-0">
            <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-muted mb-0"><i class="fas fa-list me-2"></i> Historial de Lotes Recibidos</h6>
                
                {{-- Buscador por Cédula --}}
                <form action="{{ route('certificados.ingesta.index') }}" method="GET" class="d-flex align-items-center" style="width: 300px;">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 position-absolute" style="z-index: 10; left: 5px; top: 4px;">
                            <i class="fas fa-search text-muted opacity-50"></i>
                        </span>
                        <input type="text" name="buscar_cedula" class="form-control search-minimal ps-5 w-100" placeholder="Buscar por Cédula/NIT..." value="{{ request('buscar_cedula') }}">
                        @if(request('buscar_cedula'))
                            <a href="{{ route('certificados.ingesta.index') }}" class="btn btn-light position-absolute" style="z-index: 10; right: 2px; top: 2px; border-radius: 10px; padding: 5px 10px;">
                                <i class="fas fa-times text-danger"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 10px;">
                    <thead class="text-muted small text-uppercase bg-light">
                        <tr>
                            <th class="ps-4 border-0 py-3">ID Factura</th>
                            <th class="border-0 py-3">Cliente / Tercero</th>
                            <th class="border-0 py-3 text-end">Valor Neto</th>
                            <th class="border-0 py-3">Estado ETL</th>
                            <th class="border-0 py-3">Fecha Recepción</th>
                            <th class="border-0 text-end pe-4 py-3">Excluir</th>
                        </tr>
                    </thead>
                    <tbody class="px-3">
                        @forelse($lotesCrudos as $lote)
                        <tr class="bg-white">
                            <td class="ps-4 fw-bold text-dark">
                                <i class="fas fa-hashtag text-muted me-1 opacity-50"></i> {{ $lote->id_factura ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark fs-7">{{ $lote->nombre_tercero ?? 'Desconocido' }}</div>
                                <div class="text-muted fs-8">NIT: {{ $lote->tercero ?? 'N/A' }}</div>
                            </td>
                            <td class="text-end fw-bold text-gray-800">
                                ${{ number_format((float)$lote->valor, 2) }}
                            </td>
                            <td>
                                @if($lote->anular == 1)
                                    <span class="badge bg-pastel-danger rounded-pill px-3 py-2"><i class="fas fa-ban me-1"></i> Anulado</span>
                                @elseif($lote->estado == 'PROCESADO')
                                    <span class="badge bg-pastel-success rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i> Procesado</span>
                                @else
                                    <span class="badge bg-pastel-warning rounded-pill px-3 py-2 text-dark"><i class="fas fa-clock me-1"></i> Pendiente</span>
                                @endif
                            </td>
                            <td><div class="fs-8 text-muted">{{ $lote->fecha_ad ?? $lote->created_at }}</div></td>
                            <td class="text-end pe-4">
                                @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                    <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-icon btn-light-danger btn-sm rounded-circle shadow-sm" title="Anular Lote Defectuoso" onclick="return confirm('¿Seguro que desea excluir este registro?');">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-icon btn-light btn-sm rounded-circle" disabled><i class="fas fa-lock text-muted"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No hay lotes que coincidan con la búsqueda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($lotesCrudos->hasPages())
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-5">{{ $lotesCrudos->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL DE CONFIGURACIÓN PRE-INYECCIÓN --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="modalConfigurarInyeccion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg card-custom">
                <div class="modal-header border-0 bg-light pt-4 pb-3">
                    <h5 class="fw-bold text-dark m-0"><i class="fas fa-cogs text-primary me-2"></i> Configurar Inyección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Formulario actualizado con el ID necesario para JavaScript --}}
                <form action="{{ route('certificados.ingesta.inyectar') }}" method="POST" id="formInyectar">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">
                            Seleccione los parámetros que se aplicarán a todas las operaciones y facturas de este bloque (CER-YYYY).
                        </p>
                        
                        {{-- Select de Estado --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase"><i class="fas fa-info-circle me-1"></i> Estado Inicial</label>
                            <select name="id_car_sia_estados" class="form-select form-select-custom" required>
                                <option value="">-- Seleccione un estado --</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre ?? 'Estado ID: '.$estado->id }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select de Tipo de Evento --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold text-muted small text-uppercase"><i class="fas fa-tags me-1"></i> Tipo de Evento</label>
                            <select name="id_car_sia_tipos" class="form-select form-select-custom" required>
                                <option value="">-- Seleccione un tipo --</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre ?? 'Tipo ID: '.$tipo->id }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pt-0 justify-content-center gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-pastel-primary rounded-pill px-5 fw-bold">
                            Iniciar Inyección
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL DE PROGRESO (NUEVO) --}}
    {{-- ======================================================= --}}
    <div class="modal fade" id="modalProgreso" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg card-custom p-4 text-center">
                <h5 class="fw-bold text-primary mb-3">Procesando Inyección...</h5>
                <p class="text-muted small mb-4">Estamos construyendo los bloques y registrando las operaciones en el motor SIA. Por favor, no cierres esta ventana.</p>
                
                <div class="progress mb-4" style="height: 20px; border-radius: 10px; background-color: #e7f0ff;">
                    <div id="barraProgreso" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>

                <div>
                    <button type="button" id="btnCancelarInyeccion" class="btn btn-outline-danger rounded-pill px-4 fw-bold btn-sm">
                        <i class="fas fa-times me-1"></i> Cancelar Operación
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL DE RESUMEN DE INYECCIÓN (AUTO-APERTURA) --}}
    {{-- ======================================================= --}}
    @if(session('inyeccion_exitosa'))
    <div class="modal fade" id="modalResumenInyeccion" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg card-custom overflow-hidden">
                <div class="modal-header border-0 bg-pastel-success pt-4 pb-3 justify-content-center position-relative">
                    <button type="button" class="btn-close position-absolute" style="right: 20px; top: 20px;" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 70px; height: 70px;">
                            <i class="fas fa-check text-success fs-1"></i>
                        </div>
                        <h4 class="fw-bold text-success m-0">¡Inyección y Estructuración Exitosa!</h4>
                    </div>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted mb-4 fs-6">
                        El motor SIA ha procesado los datos, agrupado las facturas y generado los bloques lógicos (CER-YYYY) correctamente.
                    </p>
                    
                    <div class="row g-3 justify-content-center mb-2">
                        {{-- Tarjeta Clientes (Operaciones Creadas) --}}
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border d-flex flex-column align-items-center">
                                <i class="fas fa-users text-primary fs-3 mb-2 opacity-75"></i>
                                <h3 class="fw-bolder text-dark m-0">{{ session('resumen_clientes') }}</h3>
                                <span class="text-muted fs-8 text-uppercase fw-semibold">Operaciones Creadas</span>
                            </div>
                        </div>
                        {{-- Tarjeta Líneas (Facturas Asociadas) --}}
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 border d-flex flex-column align-items-center">
                                <i class="fas fa-file-invoice text-info fs-3 mb-2 opacity-75"></i>
                                <h3 class="fw-bolder text-dark m-0">{{ session('resumen_lineas') }}</h3>
                                <span class="text-muted fs-8 text-uppercase fw-semibold">Líneas Inyectadas</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pt-0 justify-content-center flex-column gap-2">
                    <button type="button" class="btn btn-success shadow-sm rounded-pill px-5 fw-bold" data-bs-dismiss="modal">
                        Continuar
                    </button>
                    {{-- Enlace directo al Backoffice --}}
                    <a href="{{ route('certificados.operaciones.index') }}" class="text-muted small text-decoration-none hover-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Ver en el Motor de Operaciones
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para abrir el modal de éxito automáticamente --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalResumen = new bootstrap.Modal(document.getElementById('modalResumenInyeccion'));
            modalResumen.show();
        });
    </script>
    @endif

    {{-- ======================================================= --}}
    {{-- SCRIPTS DE FUNCIONAMIENTO JAVASCRIPT --}}
    {{-- ======================================================= --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formInyectar = document.getElementById('formInyectar');
            const barraProgreso = document.getElementById('barraProgreso');
            const btnCancelar = document.getElementById('btnCancelarInyeccion');
            
            let abortController = null;
            let intervaloProgreso = null;

            if(formInyectar) {
                formInyectar.addEventListener('submit', async function(e) {
                    e.preventDefault(); // Detenemos el envío tradicional

                    // 1. Ocultar modal de configuración y mostrar el de progreso
                    var modalConfig = bootstrap.Modal.getInstance(document.getElementById('modalConfigurarInyeccion'));
                    if (modalConfig) {
                        modalConfig.hide();
                    }

                    var modalProgreso = new bootstrap.Modal(document.getElementById('modalProgreso'));
                    modalProgreso.show();

                    // 2. Iniciar animación de la barra (simulada hasta 90%)
                    let progreso = 0;
                    barraProgreso.style.width = '0%';
                    barraProgreso.innerText = '0%';
                    barraProgreso.classList.replace('bg-danger', 'bg-primary');
                    barraProgreso.classList.replace('bg-success', 'bg-primary');
                    
                    intervaloProgreso = setInterval(() => {
                        if (progreso < 90) {
                            progreso += Math.floor(Math.random() * 10) + 1;
                            if(progreso > 90) progreso = 90;
                            barraProgreso.style.width = progreso + '%';
                            barraProgreso.innerText = progreso + '%';
                        }
                    }, 400);

                    // 3. Preparar la petición Fetch con capacidad de abortar
                    abortController = new AbortController();
                    const formData = new FormData(formInyectar);

                    try {
                        const response = await fetch(formInyectar.action, {
                            method: 'POST',
                            body: formData,
                            signal: abortController.signal,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        // 4. Si termina bien, llenamos al 100% y recargamos la página
                        // para que el backend nos muestre el modal de éxito nativo
                        clearInterval(intervaloProgreso);
                        barraProgreso.style.width = '100%';
                        barraProgreso.innerText = '100%';
                        barraProgreso.classList.replace('bg-primary', 'bg-success');
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);

                    } catch (error) {
                        clearInterval(intervaloProgreso);
                        
                        if (error.name === 'AbortError') {
                            // El usuario hizo clic en Cancelar
                            barraProgreso.style.width = '100%';
                            barraProgreso.innerText = 'Cancelado';
                            barraProgreso.classList.replace('bg-primary', 'bg-danger');
                            
                            setTimeout(() => {
                                modalProgreso.hide();
                                alert('La inyección fue cancelada desde el navegador.');
                                window.location.reload();
                            }, 800);
                        } else {
                            // Ocurrió un error real de servidor
                            alert('Ocurrió un error de conexión con el motor SIA.');
                            window.location.reload();
                        }
                    }
                });
            }

            // Manejador del botón cancelar
            if(btnCancelar) {
                btnCancelar.addEventListener('click', function() {
                    if(abortController) {
                        abortController.abort(); // Detiene la petición de red instantáneamente
                    }
                });
            }
        });
    </script>
</x-base-layout>