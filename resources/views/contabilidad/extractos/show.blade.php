<x-base-layout>
    @php
        // Buscamos el comprobante de cartera vinculado a este extracto
        $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion', 'tercero', 'interaccion'])
            ->whereJsonContains('id_transaccion_bancaria', $extracto->id_transaccion)
            ->orWhereJsonContains('id_transaccion_bancaria', (string) $extracto->id_transaccion)
            ->first();

        // Configuración visual del estado
        $estadoConfig = match($extracto->estado_conciliacion) {
            'Conciliado_Auto'   => ['color' => 'success', 'icon' => 'fa-robot', 'text' => 'Conciliado Automáticamente'],
            'Conciliado_Manual' => ['color' => 'primary', 'icon' => 'fa-user-check', 'text' => 'Conciliado Manualmente'],
            'Pendiente'         => ['color' => 'warning', 'icon' => 'fa-clock', 'text' => 'Pendiente por Conciliar'],
            'Anulado'           => ['color' => 'danger',  'icon' => 'fa-times-circle', 'text' => 'Anulado'],
            default             => ['color' => 'secondary', 'icon' => 'fa-question', 'text' => 'Desconocido']
        };
    @endphp

    <div class="app-container py-6" style="background-color: #fafbfc; min-height: 100vh;">
        
        {{-- HEADER SUPERIOR --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 px-4">
            <div class="d-flex align-items-center gap-4">
                <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light bg-white border border-gray-200 shadow-sm rounded-circle hover-elevate-up">
                    <i class="fas fa-arrow-left text-dark"></i>
                </a>
                <div>
                    <h2 class="fw-bolder m-0 text-gray-900 fs-2 tracking-tight">Ficha Técnica de Transacción</h2>
                    <p class="text-muted m-0 fs-7 fw-semibold mt-1">ID Sistema: <span class="text-dark font-monospace">#{{ $extracto->id_transaccion }}</span></p>
                </div>
            </div>
            
            <div class="mt-3 mt-sm-0">
                <span class="badge bg-light-{{ $estadoConfig['color'] }} text-{{ $estadoConfig['color'] }} border border-{{ $estadoConfig['color'] }} border-opacity-25 fs-6 px-4 py-2 rounded-pill shadow-sm fw-bolder">
                    <i class="fas {{ $estadoConfig['icon'] }} me-2"></i>{{ $estadoConfig['text'] }}
                </span>
            </div>
        </div>

        <div class="row g-5 px-4">
            
            {{-- COLUMNA IZQUIERDA: DATOS BANCARIOS (ORIGEN) --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-bottom border-gray-100 py-4 px-6">
                        <h4 class="fw-bolder m-0 text-gray-800 fs-4 d-flex align-items-center">
                            <div class="symbol symbol-35px me-3">
                                <div class="symbol-label bg-light-info"><i class="fas fa-university text-info fs-5"></i></div>
                            </div>
                            Origen Bancario
                        </h4>
                    </div>
                    <div class="card-body p-6">
                        
                        {{-- Monto Principal --}}
                        <div class="mb-6 p-4 bg-light-success rounded-3 border border-success border-opacity-25 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-success fw-bolder text-uppercase ls-1 fs-9 mb-1 d-block">Importe Recibido</span>
                                <span class="text-gray-900 fw-bolder fs-1 lh-1">$ {{ number_format($extracto->valor_ingreso, 2, ',', '.') }}</span>
                            </div>
                            <i class="fas fa-arrow-down text-success opacity-50" style="font-size: 2.5rem;"></i>
                        </div>

                        <div class="row g-5">
                            <div class="col-md-6">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Entidad Financiera</span>
                                <span class="text-gray-800 fw-bolder fs-6">{{ $extracto->cuentaBancaria->banco ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Número de Cuenta</span>
                                <span class="text-gray-800 fw-bolder font-monospace fs-6">{{ $extracto->cuentaBancaria->numero_cuenta ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Fecha de Acreditación</span>
                                <span class="text-gray-800 fw-bolder fs-6">{{ $extracto->fecha_movimiento->format('d M, Y - H:i') }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Referencia Cédula (Banco)</span>
                                <span class="text-primary fw-bolder font-monospace fs-6">{{ $extracto->referencia_cedula ?? 'Sin Referencia' }}</span>
                            </div>
                            <div class="col-md-12">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Oficina de Origen</span>
                                <span class="text-gray-800 fw-bolder fs-6">{{ $extracto->referencia_oficina ?? 'No especificada' }}</span>
                            </div>
                            <div class="col-12 mt-2">
                                <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-2">Hash Criptográfico de Transacción</span>
                                <div class="d-flex align-items-center bg-light p-2 rounded border border-gray-200">
                                    <code class="text-dark fs-7 font-monospace text-break flex-grow-1">{{ $extracto->hash_transaccion }}</code>
                                    <button class="btn btn-sm btn-icon btn-active-light-primary ms-2" onclick="copyToClipboard('{{ $extracto->hash_transaccion }}')" data-bs-toggle="tooltip" title="Copiar Hash">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUMNA DERECHA: DATOS SIASOFT / CARTERA (VINCULACIÓN) --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-bottom border-gray-100 py-4 px-6 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bolder m-0 text-gray-800 fs-4 d-flex align-items-center">
                            <div class="symbol symbol-35px me-3">
                                <div class="symbol-label bg-light-primary"><i class="fas fa-project-diagram text-primary fs-5"></i></div>
                            </div>
                            Vinculación Cartera
                        </h4>
                        @if($comprobante)
                            <span class="badge badge-light-success fs-9 fw-bolder"><i class="fas fa-link text-success me-1 fs-10"></i> VINCULADO</span>
                        @else
                            <span class="badge badge-light-danger fs-9 fw-bolder"><i class="fas fa-unlink text-danger me-1 fs-10"></i> SIN VINCULAR</span>
                        @endif
                    </div>
                    <div class="card-body p-6">
                        
                        @if($comprobante)
                            {{-- DATOS DEL COMPROBANTE --}}
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Tercero Identificado (Mae)</span>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-gray-400 fs-3 me-2"></i>
                                        <span class="text-gray-900 fw-bolder fs-5">{{ $comprobante->cod_ter_MaeTerceros }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Línea / Obligación</span>
                                    <span class="text-primary fw-bolder fs-6">{{ $comprobante->obligacion->nombre ?? 'N/A' }}</span>
                                </div>
                                
                                <div class="col-4">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Cuota N°</span>
                                    <span class="badge badge-light-dark fs-6 fw-bolder px-3">{{ $comprobante->numero_cuota ?? 'N/A' }}</span>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">PR</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $comprobante->pr ?? '0' }}</span>
                                </div>
                                <div class="col-4">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">CCO</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $comprobante->cco ?? '0' }}</span>
                                </div>

                                <div class="col-md-6">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Usuario Gestor</span>
                                    <span class="text-gray-800 fw-bolder fs-6">{{ $comprobante->user->name ?? 'Sistema' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-1">Tipo de Pago</span>
                                    <span class="text-gray-800 fw-bolder text-uppercase fs-6">{{ $comprobante->tipo_pago ?? 'N/A' }}</span>
                                </div>

                                <div class="col-12 border-top border-gray-100 pt-4 mt-2">
                                    <span class="text-muted fw-bold d-block text-uppercase ls-1 fs-10 mb-2">Observaciones de Cartera</span>
                                    <div class="bg-light p-4 rounded-3 text-gray-700 fs-7 fst-italic">
                                        "{{ $comprobante->observacion ?? 'No se registraron observaciones durante la conciliación.' }}"
                                    </div>
                                </div>

                                {{-- BOTÓN SOPORTE --}}
                                <div class="col-12 mt-5">
                                    @if($comprobante->url_archivo && $comprobante->url_archivo !== '#')
                                        <button type="button" onclick="abrirModalSoporte('{{ $comprobante->url_archivo }}', 'Soporte de {{ $comprobante->cod_ter_MaeTerceros }}')" class="btn btn-primary w-100 fw-bolder py-3 d-flex align-items-center justify-content-center hover-elevate-up shadow-sm">
                                            <i class="fas fa-file-pdf fs-3 me-2"></i> Ver Soporte Documental Original
                                        </button>
                                    @else
                                        <div class="alert alert-warning border-warning border-dashed d-flex align-items-center p-4 mb-0">
                                            <i class="fas fa-exclamation-triangle fs-2 text-warning me-3"></i>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark fw-bolder">Sin Soporte Físico</h6>
                                                <span class="text-muted fs-8">Este comprobante no tiene un archivo adjunto en S3.</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        @else
                            {{-- ESTADO VACÍO (NO CONCILIADO) --}}
                            <div class="d-flex flex-column align-items-center justify-content-center text-center h-100 py-10">
                                <div class="bg-light-warning p-4 rounded-circle mb-4">
                                    <i class="fas fa-inbox text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <h4 class="text-dark fw-bolder mb-2">Movimiento No Procesado</h4>
                                <p class="text-muted fs-7 mb-6 px-4">Este extracto bancario aún se encuentra en la mesa de identificación. No se ha generado ni vinculado un comprobante de pago en el módulo de cartera.</p>
                                
                                <a href="{{ route('contabilidad.extractos.conciliacion') }}?search={{ $extracto->hash_transaccion }}" class="btn btn-dark fw-bolder px-6 rounded-pill">
                                    <i class="fas fa-compress-arrows-alt me-2"></i> Ir a Mesa de Conciliación
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL VISOR DE SOPORTES S3 --}}
    <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light-primary border-bottom border-primary border-opacity-10 py-3">
                    <h5 class="modal-title fw-bolder text-gray-800 d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-primary fs-3"></i> 
                        <span id="visorSoporteTitulo">Soporte Documental</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <a href="#" id="btnDescargarSoporte" target="_blank" class="btn btn-sm btn-icon btn-active-light-primary text-gray-600" data-bs-toggle="tooltip" title="Abrir en pestaña nueva">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-dark position-relative" style="height: 75vh;">
                    {{-- Iframe nativo para visualizar --}}
                    <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0 bg-white"></iframe>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        .ls-1 { letter-spacing: 0.05em; }
        .border-dashed { border-style: dashed !important; }
        .text-break { word-wrap: break-word; overflow-wrap: break-word; }
        .hover-elevate-up { transition: transform 0.2s ease; }
        .hover-elevate-up:hover { transform: translateY(-3px); }
    </style>
    <script>
        let modalVisor;

        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Inicializar Modal
            const modalEl = document.getElementById('modalVisorSoporte');
            if (modalEl) {
                modalVisor = new bootstrap.Modal(modalEl);
                modalEl.addEventListener('hidden.bs.modal', function () {
                    document.getElementById('visorSoporteFrame').src = ''; // Limpiar memoria
                });
            }
        });

        // Abrir Modal de S3
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') {
                alert('El documento no está disponible.');
                return;
            }
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            document.getElementById('visorSoporteFrame').src = url;
            modalVisor.show();
        };

        // Copiar Hash al portapapeles
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') {
                    toastr.success('Hash copiado al portapapeles');
                } else {
                    alert('Hash copiado: ' + text);
                }
            });
        }
    </script>
    @endpush
</x-base-layout>