<x-base-layout>
    {{-- 1. OVERLAY DE CARGA (Glassmorphism UX) --}}
    <div id="loading-overlay" class="d-none" style="position: fixed; inset: 0; background: rgba(248, 250, 252, 0.7); z-index: 10000; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(8px); transition: opacity 0.3s ease;">
        <div class="bg-white p-6 rounded-4 shadow-lg d-flex flex-column align-items-center">
            <div class="spinner-border text-primary mb-4" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;" role="status"></div>
            <h3 class="fw-bolder text-dark tracking-tight m-0">Procesando...</h3>
            <span class="text-muted fs-8 mt-2">Por favor, espera un momento</span>
        </div>
    </div>

    <div class="app-container py-6" style="background-color: #f4f6f9; min-height: 100vh;">
        
        {{-- 2. HEADER DE CONTROL --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-6 px-5">
            <div class="d-flex align-items-center gap-4">
                <div class="bg-white p-3 rounded-4 shadow-sm border border-gray-200 d-flex align-items-center justify-content-center transition-all hover-elevate-up" style="width: 56px; height: 56px;">
                    <i class="fas fa-file-invoice-dollar text-primary fs-2"></i>
                </div>
                <div>
                    <h2 class="fw-bolder text-gray-900 mb-0 tracking-tight fs-1">Extractos Bancarios</h2>
                    <div class="text-muted fs-8 fw-semibold mt-1 d-flex align-items-center gap-2">
                        <span>Auditoría y Movimientos</span>
                        <span class="bullet bullet-dot bg-gray-400 h-4px w-4px"></span>
                        <span class="badge bg-light-success text-success fw-bold px-2 py-1"><i class="fas fa-lock fs-10 me-1 text-success"></i> S3 Seguro</span>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-3 mt-4 mt-sm-0">
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-light-primary fw-bolder px-5 py-2 shadow-sm fs-8 hover-elevate-up transition-all">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Importar Datos
                </a>
                <a href="{{ route('contabilidad.extractos.conciliacion') }}" class="btn btn-dark fw-bolder px-5 py-2 shadow-sm fs-8 hover-elevate-up transition-all">
                    <i class="fas fa-compress-arrows-alt me-2 text-white"></i> Mesa de Conciliación
                </a>
            </div>
        </div>

        {{-- 3. TOOLBAR DE FILTRADO --}}
        <div class="mx-5 mb-5">
            <form method="GET" action="{{ route('contabilidad.extractos.index') }}" id="form-master-filter" class="bg-white p-3 rounded-4 shadow-sm border border-gray-200 d-flex flex-wrap align-items-center gap-3">
                
                <div class="input-group input-group-solid w-auto flex-grow-1 bg-light rounded-3 px-2" style="min-width: 250px;">
                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-search fs-5"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-solid bg-transparent border-0 fs-7 fw-semibold ps-0" placeholder="Buscar Hash, Cédula o Valor...">
                </div>

                <div class="vr bg-gray-300 mx-2 d-none d-md-block" style="width: 1px; height: 30px;"></div>

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    {{-- SWITCH DE RASTREO GLOBAL --}}
                    <div class="form-check form-switch form-check-custom form-check-solid form-check-sm me-2" data-bs-toggle="tooltip" title="Ignorar el mes y buscar en toda la base histórica">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="is_global" value="1" id="isGlobalCheck" {{ isset($is_global) && $is_global ? 'checked' : '' }} />
                        <label class="form-check-label text-gray-700 fs-8 fw-bold text-uppercase cursor-pointer user-select-none" for="isGlobalCheck">
                            <i class="fas fa-globe-americas text-primary me-1"></i> Global
                        </label>
                    </div>

                    <div class="position-relative">
                        <input type="month" name="periodo" id="periodoInput" value="{{ $periodo }}" class="form-control form-control-solid bg-light border-0 fs-8 fw-bold text-gray-700 px-4 rounded-3 hover-elevate-up" style="width: 140px; cursor: pointer;" {{ isset($is_global) && $is_global ? 'disabled' : '' }}>
                    </div>
                    
                    <select name="estado" class="form-select form-select-solid bg-light border-0 fs-8 fw-bold text-gray-700 rounded-3 hover-elevate-up" style="width: 150px; cursor: pointer;">
                        <option value="">Cualquier Estado</option>
                        <option value="Pendiente" {{ $estado == 'Pendiente' ? 'selected' : '' }}>⏳ Pendientes</option>
                        <option value="Conciliados" {{ $estado == 'Conciliados' ? 'selected' : '' }}>✅ Conciliados</option>
                    </select>

                    <select name="banco_id" class="form-select form-select-solid bg-light border-0 fs-8 fw-bold text-gray-700 rounded-3 hover-elevate-up" style="width: 180px; cursor: pointer;">
                        <option value="">🏦 Todas las Cuentas</option>
                        @foreach($cuentas as $cuenta)
                            <option value="{{ $cuenta->id }}" {{ $banco_id == $cuenta->id ? 'selected' : '' }}>
                                {{ $cuenta->banco }} (...{{ substr($cuenta->numero_cuenta, -4) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="submit" class="btn btn-primary fw-bolder px-5 py-2 fs-8 rounded-3 shadow-sm hover-elevate-up">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    @if($search || $estado || $banco_id || (isset($is_global) && $is_global))
                        <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-icon btn-light-danger rounded-3 shadow-sm hover-elevate-up" data-bs-toggle="tooltip" title="Limpiar todos los filtros">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 4. GRID DE DATOS --}}
        <div class="bg-white shadow-sm border border-gray-200 mx-5 d-flex flex-column" style="border-radius: 12px; overflow: hidden; min-height: 65vh;">
            
            <div class="d-flex justify-content-between align-items-center px-5 py-3 bg-white border-bottom border-gray-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bolder fs-8 text-gray-600 tracking-widest text-uppercase">
                        Registros Encontrados <span class="badge badge-circle bg-primary text-white ms-2 fs-9 w-25px h-25px shadow-sm">{{ $extractos->total() }}</span>
                    </div>
                    <div id="js-filter-active" class="d-none animate__animated animate__fadeIn">
                        <span class="badge bg-light-warning text-warning border border-warning border-opacity-25 fs-9 fw-bold px-3 py-1 rounded-pill">
                            <i class="fas fa-filter fs-10 me-1 text-warning"></i> Búsqueda local activa
                        </span>
                    </div>
                </div>
                <div class="fs-9 text-muted font-monospace bg-light px-3 py-1 rounded-pill border border-gray-200">
                    Página {{ $extractos->currentPage() }} de {{ $extractos->lastPage() }}
                </div>
            </div>

            <div class="table-responsive flex-grow-1 custom-scrollbar" id="table-container">
                <table class="table-gsheets align-middle" id="main-table">
                    <thead>
                        <tr class="header-titles text-nowrap">
                            <th style="width: 40px;" class="text-center"><i class="fas fa-list-ol text-muted opacity-50"></i></th>
                            <th class="col-index">#</th>
                            <th style="width: 100px;">Fecha</th>
                            <th style="width: 180px;">Entidad Bancaria</th>
                            <th style="width: 130px;">Ref. Cliente</th>
                            <th style="width: 150px;">Oficina / Origen</th>
                            <th style="width: 200px;">Línea Afectada</th>
                            <th style="width: 120px;">Tipología</th>
                            <th style="width: 140px;" class="text-end">Monto ($)</th>
                            <th style="width: 130px;" class="text-center">Estado</th>
                            <th style="width: 80px;" class="text-center">Soporte</th>
                        </tr>
                        <tr class="filter-row shadow-sm">
                            <td class="bg-light"></td>
                            <td class="col-index bg-light"></td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar fecha..."></div></td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar entidad..."></div></td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar ref..."></div></td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar origen..."></div></td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar línea..."></div></td>
                            <td>
                                <div class="filter-wrapper">
                                    <i class="fas fa-search filter-icon"></i><input type="text" class="column-filter" placeholder="Filtrar tipo...">
                                </div>
                            </td>
                            <td><div class="filter-wrapper"><i class="fas fa-search filter-icon"></i><input type="text" class="column-filter text-end" placeholder="Filtrar monto..."></div></td>
                            <td colspan="2" class="bg-light"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extractos as $index => $movimiento)
                        @php
                            $comprobante = \App\Models\Cartera\CarComprobantePago::with(['user', 'obligacion'])
                                ->whereJsonContains('id_transaccion_bancaria', $movimiento->id_transaccion)
                                ->orWhereJsonContains('id_transaccion_bancaria', (string) $movimiento->id_transaccion)
                                ->first();
                            
                            $isConciliado = str_contains($movimiento->estado_conciliacion, 'Conciliado');
                            $montoFormat = number_format($movimiento->valor_ingreso, 0, ',', '.');
                            $fechaFormat = $movimiento->fecha_movimiento->format('d/m/Y');
                        @endphp

                        {{-- Fila Principal --}}
                        <tr class="data-row cursor-pointer transition-all hover-row" onclick="toggleDetails(this, {{ $movimiento->id_transaccion }})">
                            <td class="text-center">
                                <div class="btn btn-sm btn-icon btn-light btn-active-light-primary w-25px h-25px rounded-circle">
                                    <i class="fas fa-chevron-down fs-10 text-gray-500 chevron-icon transition-transform" id="icon-{{ $movimiento->id_transaccion }}"></i>
                                </div>
                            </td>
                            <td class="col-index text-muted fw-bold">{{ $extractos->firstItem() + $index }}</td>
                            <td class="font-monospace fs-8 text-gray-700 fw-semibold">{{ $fechaFormat }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="symbol symbol-30px symbol-circle bg-light-primary d-flex align-items-center justify-content-center">
                                        <i class="fas fa-university text-primary fs-9"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bolder text-gray-800 fs-9 text-truncate" style="max-width: 130px;" title="{{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}">
                                            {{ $movimiento->cuentaBancaria->banco ?? 'N/A' }}
                                        </span>
                                        <span class="text-muted fs-11 font-monospace">CTA: *{{ substr($movimiento->cuentaBancaria->numero_cuenta ?? '0000', -4) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-monospace fw-bolder text-primary fs-8">
                                {{ $movimiento->referencia_cedula ?? '---' }}
                            </td>
                            <td class="text-gray-600 fs-9 text-truncate fw-semibold" title="{{ $movimiento->referencia_oficina }}">
                                <i class="fas fa-map-marker-alt text-gray-400 me-1"></i> {{ $movimiento->referencia_oficina ?? '---' }}
                            </td>
                            <td class="fs-9 text-truncate text-gray-800 fw-semibold" title="{{ $comprobante?->obligacion?->nombre }}">
                                {{ $comprobante?->obligacion?->nombre ?? '---' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border border-secondary-subtle fs-10 text-uppercase fw-bolder px-3 py-2 rounded-pill shadow-sm">
                                    {{ $comprobante?->tipo_pago ?? 'EXTRACTO' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bolder fs-8 {{ $isConciliado ? 'text-success' : 'text-gray-900' }}">${{ $montoFormat }}</span>
                            </td>
                            <td class="text-center">
                                @if($isConciliado)
                                    <span class="badge bg-light-success text-success border border-success border-opacity-25 fs-10 fw-bolder px-3 py-2 rounded-pill shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: max-content;">
                                        <i class="fas fa-check-circle me-1 text-success fs-10"></i> CONCILIADO
                                    </span>
                                @else
                                    <span class="badge bg-light-warning text-warning border border-warning border-opacity-25 fs-10 fw-bolder px-3 py-2 rounded-pill shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: max-content;">
                                        <i class="fas fa-clock me-1 text-warning fs-10"></i> PENDIENTE
                                    </span>
                                @endif
                            </td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                @if($comprobante && $comprobante->url_archivo !== '#')
                                    <button type="button" 
                                            onclick="abrirModalSoporte('{{ $comprobante->url_archivo }}', 'Soporte del Tercero: {{ $comprobante->cod_ter_MaeTerceros ?? 'N/A' }}')" 
                                            class="btn btn-icon btn-sm btn-light-danger hover-elevate-up shadow-sm rounded-circle transition-all" 
                                            data-bs-toggle="tooltip" 
                                            title="Visualizar PDF del Soporte">
                                        <i class="fas fa-file-pdf fs-6"></i>
                                    </button>
                                @else
                                    <div class="bg-light rounded-circle d-flex justify-content-center align-items-center mx-auto" style="width: 30px; height: 30px;" data-bs-toggle="tooltip" title="No hay soporte vinculado">
                                        <span class="text-gray-400 fs-8 fw-bold">-</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        
                        {{-- Fila Desplegable (Detalles) --}}
                        <tr class="detail-row d-none bg-light" id="detail-{{ $movimiento->id_transaccion }}">
                            <td colspan="11" class="p-0 border-0">
                                <div class="px-6 py-5 shadow-inner" style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <div class="row g-5">
                                        {{-- Hash Card --}}
                                        <div class="col-md-3">
                                            <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
                                                <div class="card-body p-4">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-hashtag text-primary fs-5 me-2"></i>
                                                        <span class="fs-11 text-uppercase text-muted fw-bolder">Hash de Transacción</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mt-3">
                                                        <code class="fs-9 text-dark bg-light px-3 py-2 rounded-3 border flex-grow-1 text-truncate" title="{{ $movimiento->hash_transaccion }}">{{ $movimiento->hash_transaccion }}</code>
                                                        <button class="btn btn-sm btn-icon btn-light-primary shadow-sm ms-2" onclick="copyToClipboard('{{ $movimiento->hash_transaccion }}')" data-bs-toggle="tooltip" title="Copiar Hash">
                                                            <i class="fas fa-copy fs-8"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Datos Generales Card --}}
                                        <div class="col-md-5">
                                            <div class="card bg-white border-0 shadow-sm rounded-4 h-100">
                                                <div class="card-body p-4">
                                                    <div class="row g-4">
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-id-card text-info fs-8 me-2"></i>
                                                                <span class="fs-11 text-uppercase text-muted fw-bolder">Tercero Validado</span>
                                                            </div>
                                                            <div class="fs-8 fw-bolder text-gray-800 ps-5">{{ $comprobante->cod_ter_MaeTerceros ?? 'Sin procesar' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-project-diagram text-warning fs-8 me-2"></i>
                                                                <span class="fs-11 text-uppercase text-muted fw-bolder">PR / CCO</span>
                                                            </div>
                                                            <div class="fs-8 fw-bolder text-gray-800 ps-5">{{ $comprobante->pr ?? '0' }} / {{ $comprobante->cco ?? '0' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-list-ol text-success fs-8 me-2"></i>
                                                                <span class="fs-11 text-uppercase text-muted fw-bolder">Cuota N°</span>
                                                            </div>
                                                            <div class="fs-8 fw-bolder text-gray-800 ps-5">{{ $comprobante->numero_cuota ?? '---' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-user-tie text-primary fs-8 me-2"></i>
                                                                <span class="fs-11 text-uppercase text-muted fw-bolder">Usuario Gestor</span>
                                                            </div>
                                                            <div class="fs-8 fw-bolder text-gray-800 ps-5 text-truncate" title="{{ $comprobante->user->name ?? 'Sistema' }}">
                                                                {{ $comprobante->user->name ?? 'Sistema' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Observaciones & Action Card --}}
                                        <div class="col-md-4">
                                            <div class="card bg-white border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
                                                <div class="card-body p-4 flex-grow-1">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-comment-alt text-secondary fs-8 me-2"></i>
                                                        <span class="fs-11 text-uppercase text-muted fw-bolder">Observaciones</span>
                                                    </div>
                                                    <div class="fs-9 text-gray-600 fst-italic p-3 bg-light rounded-3 mt-2" style="min-height: 50px;">
                                                        {{ $comprobante->observacion ?? 'No se registraron observaciones adicionales para este movimiento.' }}
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-transparent border-0 pt-0 d-flex justify-content-end pb-4 px-4">
                                                    <a href="{{ route('recaudo.imputaciones.create', ['id_transaccion' => $movimiento->id_transaccion]) }}" 
                                                        class="btn btn-sm btn-success fs-9 fw-bolder px-4 py-2 shadow-sm rounded-pill hover-elevate-up d-flex align-items-center">
                                                        <i class="fas fa-file-invoice-dollar me-2 fs-10"></i> Hacer Recibo de Recaudo
                                                    </a>
                                                    <a href="{{ route('contabilidad.extractos.show', $movimiento->id_transaccion) }}" class="btn btn-sm btn-dark fs-9 fw-bolder px-5 py-2 shadow-sm rounded-pill hover-elevate-up">
                                                        Abrir Ficha Completa <i class="fas fa-arrow-right ms-2 fs-10"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-15 bg-light">
                                <div class="d-flex flex-column align-items-center justify-content-center p-10">
                                    <div class="bg-white p-5 rounded-circle shadow-sm mb-4">
                                        <i class="fas fa-search-minus fs-2hx text-gray-400"></i>
                                    </div>
                                    <h4 class="text-dark fw-bolder mb-1">No se encontraron movimientos</h4>
                                    <span class="text-muted fw-semibold fs-7">Intenta ajustar los filtros de búsqueda o el periodo seleccionado.</span>
                                    @if($search || $estado || $banco_id)
                                        <a href="{{ route('contabilidad.extractos.index') }}" class="btn btn-light-primary btn-sm mt-4 fw-bolder px-4">Limpiar Filtros</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. PAGINACIÓN --}}
            <div class="border-top border-gray-100 px-6 py-4 bg-white d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted fs-8 fw-semibold bg-light px-4 py-2 rounded-pill">
                    Mostrando <span class="text-primary fw-bolder mx-1">{{ $extractos->firstItem() ?? 0 }}</span> a <span class="text-primary fw-bolder mx-1">{{ $extractos->lastItem() ?? 0 }}</span> de <span class="text-dark fw-bolder mx-1">{{ $extractos->total() }}</span> registros
                </div>
                <div class="custom-pagination">
                    {{ $extractos->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        {{-- MODAL VISOR DE SOPORTES --}}
        <div class="modal fade" id="modalVisorSoporte" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header bg-white border-bottom border-gray-200 py-4 px-6">
                        <h5 class="modal-title fw-bolder text-gray-800 d-flex align-items-center gap-3">
                            <div class="bg-light-danger p-2 rounded-circle d-flex align-items-center justify-content-center">
                                <i class="fas fa-file-pdf text-danger fs-3"></i> 
                            </div>
                            <span id="visorSoporteTitulo" class="fs-4">Soporte de Pago</span>
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <a href="#" id="btnDescargarSoporte" target="_blank" class="btn btn-sm btn-light-primary fw-bolder px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="tooltip" title="Abrir y descargar en pestaña nueva">
                                <i class="fas fa-external-link-alt me-1"></i> Abrir Externo
                            </a>
                            <button type="button" class="btn btn-icon btn-sm btn-light-danger rounded-circle w-30px h-30px" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body p-0 bg-gray-200 position-relative" style="height: 75vh;">
                        {{-- Loader del iframe --}}
                        <div id="iframe-loader" class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 1;">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <div class="text-muted fw-bold fs-8">Cargando documento...</div>
                        </div>
                        <iframe id="visorSoporteFrame" src="" class="w-100 h-100 border-0" style="position: relative; z-index: 2;" onload="document.getElementById('iframe-loader').style.display='none';"></iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <style>
        :root {
            --gs-border: #e2e8f0;
            --gs-header-bg: #f8fafc;
            --gs-hover-bg: #f1f5f9;
        }
        
        /* Efectos y Transiciones Globales */
        .transition-all { transition: all 0.25s ease-in-out; }
        .transition-transform { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .rotate-180 { transform: rotate(180deg); }
        .hover-elevate-up:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
        
        /* Scrollbar Personalizado */
        .custom-scrollbar::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; border: 2px solid #f8fafc; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Tabla Principal */
        .table-gsheets { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; table-layout: fixed; }
        .table-gsheets thead { position: sticky; top: 0; z-index: 20; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .table-gsheets thead th { background: var(--gs-header-bg); border-bottom: 1px solid var(--gs-border); border-right: 1px solid var(--gs-border); padding: 14px 16px; font-weight: 800; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; }
        .table-gsheets tbody td { border-bottom: 1px solid var(--gs-border); border-right: 1px solid var(--gs-border); padding: 10px 16px; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; background-color: #ffffff; }
        
        /* Hover de Filas */
        .hover-row:hover td { background-color: var(--gs-hover-bg); }
        .row-active td { background-color: #f8fafc !important; border-bottom-color: transparent !important; }
        
        /* Utilidades de columnas */
        .col-index { width: 45px; text-align: center !important; background: var(--gs-header-bg); color: #64748b; font-size: 11px; }
        
        /* Buscadores de columna (Filtro local UI) */
        .filter-row td { padding: 6px 8px !important; background: #ffffff; border-bottom: 2px solid var(--gs-border); }
        .filter-wrapper { position: relative; display: flex; align-items: center; }
        .filter-icon { position: absolute; left: 10px; color: #94a3b8; font-size: 11px; }
        .column-filter { width: 100%; border: 1px solid transparent; background: #f8fafc; border-radius: 6px; font-size: 11px; padding: 6px 8px 6px 28px; color: #475569; transition: all 0.2s; font-weight: 500; }
        .column-filter:focus { background: #ffffff; border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .column-filter::placeholder { color: #94a3b8; }
        
        /* Detalles Panel Sombras */
        .shadow-inner { box-shadow: inset 0 6px 10px -8px rgba(0,0,0,0.15); }
        
        /* Paginación Mejorada */
        .custom-pagination .pagination { margin-bottom: 0; gap: 6px; }
        .custom-pagination .page-link { border: 1px solid transparent; color: #475569; background: #f8fafc; border-radius: 8px; font-size: 12px; font-weight: 700; padding: 8px 14px; transition: all 0.2s; }
        .custom-pagination .page-link:hover { background: #e2e8f0; color: #0f172a; border-color: #cbd5e1; }
        .custom-pagination .page-item.active .page-link { background: #3b82f6; color: #ffffff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
        .custom-pagination .page-item.disabled .page-link { color: #cbd5e1; background: transparent; }
    </style>

    <script>
        let modalVisor;

        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar el modal de Bootstrap
            const modalEl = document.getElementById('modalVisorSoporte');
            if (modalEl) {
                modalVisor = new bootstrap.Modal(modalEl);
                modalEl.addEventListener('hidden.bs.modal', function () {
                    // Limpiar el src para que no quede el PDF anterior en memoria y mostrar el loader para la proxima
                    document.getElementById('visorSoporteFrame').src = '';
                    document.getElementById('iframe-loader').style.display = 'block';
                });
            }

            // Activar tooltips (Se reinicializan tras cambios en DOM si fuera necesario)
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Lógica para el checkbox "Global"
            const isGlobalCheck = document.getElementById('isGlobalCheck');
            const periodoInput = document.getElementById('periodoInput');
            
            if(isGlobalCheck && periodoInput) {
                isGlobalCheck.addEventListener('change', function() {
                    periodoInput.disabled = this.checked;
                    if(this.checked) {
                        periodoInput.value = '';
                        periodoInput.classList.add('opacity-50');
                    } else {
                        periodoInput.classList.remove('opacity-50');
                    }
                });
            }

            // Filtrado Local Instantáneo
            const rows = document.querySelectorAll('#main-table tbody tr.data-row');
            const filters = document.querySelectorAll('.column-filter');
            const filterIndicator = document.getElementById('js-filter-active');

            filters.forEach((filter, index) => {
                filter.addEventListener('input', () => {
                    let active = false;

                    rows.forEach(row => {
                        let isMatch = true;
                        
                        // Evaluar todos los filtros para cada fila (index+2 porque la primera col es el chevron y la segunda el ID)
                        filters.forEach((f, i) => {
                            let val = f.value.toLowerCase().trim();
                            if(val) {
                                active = true;
                                let cellText = row.cells[i + 2].innerText.toLowerCase();
                                if (!cellText.includes(val)) {
                                    isMatch = false;
                                }
                            }
                        });

                        const detailRow = row.nextElementSibling;
                        
                        if (isMatch) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                            // Asegurar que se ocultan los detalles si se filtra la fila
                            if (detailRow && detailRow.classList.contains('detail-row')) {
                                detailRow.classList.add('d-none');
                                row.classList.remove('row-active');
                                const icon = row.querySelector('.chevron-icon');
                                if(icon) icon.classList.remove('rotate-180');
                            }
                        }
                    });

                    // Mostrar el badge visual de "Búsqueda local activa"
                    if (active) {
                        filterIndicator.classList.remove('d-none');
                    } else {
                        filterIndicator.classList.add('d-none');
                    }
                });
            });

            // Submit automático al cambiar selects generales (con overlay moderno)
            const masterForm = document.getElementById('form-master-filter');
            const masterSelects = masterForm.querySelectorAll('select, input[type="checkbox"], input[type="month"]');
            
            masterSelects.forEach(input => {
                input.addEventListener('change', () => {
                    // Prevenir doble submit si cambia el checkbox global que afecta al input month
                    if(input.id === 'isGlobalCheck') return; 
                    
                    mostrarLoader();
                    masterForm.submit();
                });
            });

            // Si dan click manual en global, enviar
            if(isGlobalCheck) {
                isGlobalCheck.addEventListener('change', () => {
                    mostrarLoader();
                    masterForm.submit();
                });
            }
        });

        function mostrarLoader() {
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('d-none');
            overlay.style.display = 'flex';
        }

        // Modal de Soporte Mejorado
        window.abrirModalSoporte = function(url, titulo) {
            if(!url || url === '#') {
                if(typeof toastr !== 'undefined') toastr.error('El enlace al documento no es válido o ha expirado.');
                else alert('El enlace al documento no es válido o ha expirado.');
                return;
            }
            
            document.getElementById('visorSoporteTitulo').innerText = titulo;
            document.getElementById('btnDescargarSoporte').href = url;
            document.getElementById('iframe-loader').style.display = 'block'; // Mostrar loader
            document.getElementById('visorSoporteFrame').src = url; // Renderizado nativo
            
            modalVisor.show();
        };

        // Expansión de filas de detalle
        function toggleDetails(rowElement, id) {
            const detailRow = document.getElementById('detail-' + id);
            const chevronIcon = document.getElementById('icon-' + id);
            const isHidden = detailRow.classList.contains('d-none');
            
            // Cerrar todas las demás filas primero (Acordeón funcional - Opcional, pero recomendado por UX)
            // Descomentar el bloque abajo si deseas que solo haya 1 fila abierta a la vez
            
            document.querySelectorAll('.detail-row').forEach(row => row.classList.add('d-none'));
            document.querySelectorAll('.data-row').forEach(row => row.classList.remove('row-active'));
            document.querySelectorAll('.chevron-icon').forEach(icon => icon.classList.remove('rotate-180'));
            
            
            if (isHidden) {
                // Abrir
                detailRow.classList.remove('d-none');
                detailRow.classList.add('animate__animated', 'animate__fadeIn', 'animate__faster');
                rowElement.classList.add('row-active');
                if(chevronIcon) chevronIcon.classList.add('rotate-180');
            } else {
                // Cerrar
                detailRow.classList.add('d-none');
                detailRow.classList.remove('animate__animated', 'animate__fadeIn', 'animate__faster');
                rowElement.classList.remove('row-active');
                if(chevronIcon) chevronIcon.classList.remove('rotate-180');
            }
        }

        // Copiar Portapapeles (Feedback visual mejorado)
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                if(typeof toastr !== 'undefined') {
                    toastr.success('Hash copiado exitosamente.', '¡Copiado!');
                } else {
                    alert('Hash copiado: ' + text);
                }
            }).catch(err => {
                console.error('Error copiando al portapapeles: ', err);
            });
        }
    </script>
    @endpush
</x-base-layout>