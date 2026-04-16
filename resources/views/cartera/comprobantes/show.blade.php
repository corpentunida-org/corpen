<x-base-layout>
    <div class="app-container py-5">
        
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('cartera.comprobantes.index') }}" class="btn btn-icon btn-light btn-sm me-3 shadow-sm rounded-circle">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bolder m-0 text-dark fs-2">Soporte de Pago de Cartera</h3>
                <p class="text-muted m-0 fs-6">Inspección de recibo y metadatos de la transacción</p>
            </div>
            
            {{-- Badge de Estado en el Header --}}
            <div class="ms-auto">
                @if($comprobante->estado == 'conciliado')
                    <span class="badge bg-success fs-5 px-4 py-2 rounded-pill shadow-sm"><i class="fas fa-check-circle text-white me-2"></i>Conciliado</span>
                @elseif($comprobante->estado == 'rechazado')
                    <span class="badge bg-danger fs-5 px-4 py-2 rounded-pill shadow-sm"><i class="fas fa-times-circle text-white me-2"></i>Rechazado</span>
                @else
                    <span class="badge bg-warning text-dark fs-5 px-4 py-2 rounded-pill shadow-sm"><i class="fas fa-clock text-dark me-2"></i>Pendiente</span>
                @endif
            </div>
        </div>

        <div class="row g-4">
            {{-- Columna Principal: Visualizador del Documento REAL --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-transparent border-bottom border-light p-4 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bolder m-0 text-dark fs-4"><i class="fas fa-file-pdf text-danger me-2"></i>Documento Adjunto</h4>
                        <span class="badge bg-light-primary text-primary fw-bolder px-3 py-2 fs-6">
                            Transacción Bancaria ID: #{{ $comprobante->id_transaccion_bancaria ?? 'Pendiente de Conciliar' }}
                        </span>
                    </div>
                    
                    {{-- Iframe que carga la URL temporal de S3 generada en el Modelo --}}
                    <div class="card-body p-0 bg-light position-relative" style="min-height: 600px;">
                        @if($comprobante->ruta_archivo)
                            <iframe src="{{ $comprobante->url_archivo }}" 
                                    class="w-100 h-100 position-absolute" 
                                    style="border: none; top:0; left:0; right:0; bottom:0;"
                                    title="Visor de Soporte">
                            </iframe>
                        @else
                            <div class="d-flex flex-column align-items-center justify-content-center h-100 py-15">
                                <i class="fas fa-file-excel text-gray-300 fa-4x mb-3"></i>
                                <h4 class="text-gray-500">Este registro no tiene un archivo físico asociado</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Columna Lateral: Metadatos --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm bg-white p-5 h-100" style="border-radius: 12px;">
                    
                    <h5 class="fw-bolder mb-5 text-dark fs-4 pb-3 border-bottom border-light">
                        <i class="fas fa-info-circle text-primary me-2"></i> Detalles del Registro
                    </h5>
                    
                    <ul class="list-unstyled mb-0">
                        {{-- 1. AGENTE / USUARIO --}}
                        <li class="mb-5 d-flex align-items-center p-3 bg-light rounded">
                            <div class="me-3">
                                <span class="d-flex align-items-center justify-content-center bg-white text-primary rounded-circle shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user-headset fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Registrado por (Agente)</span>
                                <span class="text-dark fw-bolder fs-5">{{ optional($comprobante->user)->name ?? 'Agente ID: ' . $comprobante->id_user }}</span>
                            </div>
                        </li>

                        {{-- 2. TERCERO --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Tercero (Siasoft)</span>
                            <span class="text-dark fw-bolder fs-4"><i class="fas fa-user-tag text-muted me-1 fs-6"></i> #{{ $comprobante->cod_ter_MaeTerceros }}</span>
                        </li>

                        {{-- 3. OBLIGACIÓN / LÍNEA (NUEVO CAMPO) --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Obligación / Línea</span>
                            <span class="text-dark fw-bolder fs-5">
                                <i class="fas fa-file-contract text-muted me-1 fs-6"></i> 
                                @if($comprobante->id_obligacion)
                                    {{ optional($comprobante->obligacion)->nombre ?? 'ID Obligación: ' . $comprobante->id_obligacion }}
                                @else
                                    <span class="text-muted fw-normal fs-6"><i>No registrada</i></span>
                                @endif
                            </span>
                        </li>
                        
                        {{-- 4. BANCO (NUEVO CAMPO) --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Banco Destino</span>
                            <span class="text-dark fw-bolder fs-5">
                                <i class="fas fa-university text-muted me-1 fs-6"></i> 
                                @if($comprobante->id_banco)
                                    {{ optional($comprobante->banco)->numero_cuenta }} - {{ optional($comprobante->banco)->banco ?? 'ID: ' . $comprobante->id_banco }}
                                @else
                                    <span class="text-muted fw-normal fs-6"><i>No registrado</i></span>
                                @endif
                            </span>
                        </li>
                        
                        {{-- 5. MONTO --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Monto Reportado</span>
                            <span class="text-success fw-bolder fs-1 lh-1">${{ number_format($comprobante->monto_pagado, 0, ',', '.') }}</span>
                        </li>
                        
                        {{-- 6. FECHA --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Fecha de Pago</span>
                            <span class="text-dark fw-bold fs-5">
                                <i class="far fa-calendar-alt text-muted me-1"></i> 
                                {{ \Carbon\Carbon::parse($comprobante->fecha_pago)->translatedFormat('d \d\e F, Y') }}
                            </span>
                        </li>

                        {{-- 7. INTERACCIÓN CRM --}}
                        <li class="mb-5">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">ID Interacción CRM</span>
                            <span class="text-dark fw-bold fs-5">
                                @if($comprobante->id_interaction)
                                    #{{ $comprobante->id_interaction }}
                                @else
                                    <span class="text-muted fw-normal fs-6"><i>No registrado</i></span>
                                @endif
                            </span>
                        </li>

                        {{-- 8. HASH --}}
                        <li class="mb-0">
                            <span class="text-gray-500 fw-bold d-block text-uppercase ls-1 fs-8 mb-1">Token (Hash Único)</span>
                            <div class="bg-light p-3 rounded text-break border border-light">
                                <code class="text-dark fw-bold fs-7">{{ $comprobante->hash_transaccion ?? 'Sin Token' }}</code>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-auto pt-5">
                        @if($comprobante->ruta_archivo)
                            <a href="{{ $comprobante->url_archivo }}" target="_blank" class="btn btn-light-primary w-100 rounded-pill fw-bolder py-3 fs-6">
                                <i class="fas fa-external-link-alt me-2"></i> Abrir en Pestaña Nueva
                            </a>
                        @else
                            <button disabled class="btn btn-light w-100 rounded-pill fw-bolder py-3 fs-6">
                                Sin Archivo
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        .shadow-xs { box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important; }
        .ls-1 { letter-spacing: 0.05em; }
        .text-break { word-wrap: break-word; overflow-wrap: break-word; }
    </style>
</x-base-layout>