{{-- TABLA COLAPSABLE DE AUDITORÍA --}}
<div class="card-g overflow-hidden mb-4 shadow-sm" id="tableCard" style="border: 1px solid var(--c-border); border-radius: var(--r-xl);">

    {{-- Cuadro Informativo (Contexto Corporativo) --}}
    <div class="px-4 py-3 border-bottom d-flex gap-3 align-items-start" style="background-color: #f8fafc;">
        <div class="mt-1">
            <i class="fas fa-clipboard-check fs-5" style="color: var(--c-primary);"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark" style="font-size: .9rem;">Auditoría de Lote (Zona Staging)</h6>
            <p class="mb-0 text-muted" style="font-size: .8rem; line-height: 1.5; max-width: 900px;">
                Este visor muestra los datos exactos que el sistema leyó de tu archivo Excel. Su objetivo es permitirte <strong>revisar la información</strong> y <strong>excluir registros específicos</strong> (con el botón de la papelera) si detectas alguna anomalía antes de procesar el bloque completo.
            </p>
        </div>
    </div>

    {{-- Controles y Filtros (Header Minimalista) --}}
    <div class="table-header-strip bg-white px-4 py-3 border-bottom d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

        {{-- Botón Colapsable e Indicadores --}}
        <button class="table-toggle-btn border-0 bg-transparent p-0 d-flex align-items-center gap-2 text-dark"
                id="tableToggleBtn"
                type="button"
                aria-expanded="true"
                aria-controls="tableCollapse"
                title="Mostrar u ocultar registros"
                style="outline: none;">
            <span class="toggle-arrow" style="color: var(--c-muted);"><i class="fas fa-chevron-down"></i></span>
            <span class="fw-bold" style="font-size: .95rem;">Registros del Bloque</span>
            <span class="badge-g badge-info ms-1" style="font-size: .7rem;">API-{{ str_pad($bloqueActivo ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
            <span class="text-muted fw-normal" style="font-size:.75rem;">({{ number_format($lotesCrudos->total(), 0, ',', '.') }} en total)</span>
        </button>

        {{-- Formulario de Filtros --}}
        <form action="{{ route('certificados.ingesta.index') }}" method="GET" id="formFiltros" class="d-flex flex-wrap gap-2 align-items-center m-0">
            <input type="hidden" name="bloque" value="{{ $bloqueActivo }}">

            <select name="estado" class="input-g shadow-sm text-muted"
                    style="min-width:140px; cursor:pointer; font-size:.85rem; padding: 0.4rem 1rem;"
                    onchange="this.form.submit()"
                    aria-label="Filtrar por estado">
                <option value="">Todos los estados</option>
                <option value="PENDIENTE" {{ request('estado') == 'PENDIENTE' ? 'selected' : '' }}>⏳ Pendientes</option>
                <option value="PROCESADO" {{ request('estado') == 'PROCESADO' ? 'selected' : '' }}>✅ Procesados</option>
                <option value="ANULADO" {{ request('estado') == 'ANULADO' ? 'selected' : '' }}>🚫 Anulados</option>
            </select>

            <div class="position-relative">
                <i class="fas fa-search position-absolute top-50 translate-middle-y" style="left:.85rem; color:#94a3b8; font-size:.8rem; pointer-events:none;"></i>
                <input type="text" name="buscar_cedula" id="inputBusqueda"
                       class="input-g shadow-sm"
                       style="padding-left:2.3rem; min-width:240px; font-size:.85rem; padding-top: 0.4rem; padding-bottom: 0.4rem;"
                       placeholder="Buscar NIT, nombre o factura…"
                       value="{{ request('buscar_cedula') }}"
                       aria-label="Buscar en este lote">
            </div>

            @if(request('buscar_cedula') || request('estado'))
                <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm text-decoration-none" title="Limpiar filtros" style="padding: 0.4rem 0.8rem; font-size: .85rem;">
                    <i class="fas fa-times me-1"></i> <span class="hide-sm">Limpiar</span>
                </a>
            @endif
        </form>
    </div>

    {{-- Tabla y Datos --}}
    <div class="collapsible-table show" id="tableCollapse">
        <div class="table-responsive">
            <table class="table tbl align-middle mb-0" aria-label="Registros del bloque de ingesta">
                <thead style="background-color: #f8fafc; color: var(--c-muted); font-size: .75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    <tr>
                        <th class="ps-4 py-3" style="width:105px; font-weight: 600;">Bloque</th>
                        <th class="py-3" style="width:120px; font-weight: 600;">Factura</th>
                        <th class="py-3" style="font-weight: 600;">Cliente / Tercero</th>
                        <th class="text-end py-3" style="width:145px; font-weight: 600;">Valor Neto</th>
                        <th class="py-3" style="width:140px; font-weight: 600;">Estado ETL</th>
                        <th class="py-3" style="width:155px; font-weight: 600;">Recepción</th>
                        <th class="text-center pe-4 py-3" style="width:85px; font-weight: 600;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotesCrudos as $lote)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td class="ps-4">
                            <span class="badge bg-light text-secondary border fw-semibold" style="font-family:monospace;font-size:.78rem;">
                                API-{{ str_pad($lote->numero_bloque ?? 0, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold" style="font-family:monospace;color:var(--c-text);">#{{ $lote->id_factura ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark" style="font-size:.85rem;">{{ $lote->nombre_tercero ?? 'Tercero desconocido' }}</div>
                            <div style="color:var(--c-muted);font-size:.75rem;margin-top:.1rem;">
                                <i class="fas fa-id-card me-1" style="font-size:.65rem; opacity: .7;"></i> NIT: {{ $lote->tercero ?? '—' }}
                            </div>
                        </td>
                        <td class="text-end fw-bold" style="font-size:.9rem; color: #0f172a;">
                            ${{ number_format((float)($lote->valor ?? 0), 2, ',', '.') }}
                        </td>
                        <td>
                            @if($lote->anular == 1)
                                <span class="badge-g badge-danger"><i class="fas fa-ban"></i> Anulado</span>
                            @elseif($lote->estado == 'PROCESADO')
                                <span class="badge-g badge-ok"><i class="fas fa-check-circle"></i> Procesado</span>
                            @else
                                <span class="badge-g badge-warn"><i class="fas fa-hourglass-half"></i> Pendiente</span>
                            @endif
                        </td>
                        <td style="color:var(--c-muted);font-size:.8rem;">
                            <i class="far fa-calendar-alt me-1 opacity-75"></i>
                            {{ $lote->fecha_ad ? \Carbon\Carbon::parse($lote->fecha_ad)->format('d/m/Y') : ($lote->created_at ? \Carbon\Carbon::parse($lote->created_at)->format('d/m/Y') : '—') }}
                        </td>
                        <td class="text-center pe-4">
                            @if($lote->estado != 'PROCESADO' && $lote->anular != 1)
                                <form action="{{ route('certificados.ingesta.anular', $lote->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas excluir la factura #{{ $lote->id_factura }} del bloque?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm border-0 bg-transparent p-2" style="color:#ef4444; transition: transform 0.2s;" title="Excluir del bloque" aria-label="Excluir factura {{ $lote->id_factura }}" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @else
                                <span style="color:var(--c-muted);opacity:.3;" title="{{ $lote->anular == 1 ? 'Anulado' : 'Ya procesado' }}">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center py-4">
                                <div class="p-3 rounded-circle mb-3 d-flex justify-content-center align-items-center" style="background:#f1f5f9; width: 60px; height: 60px;">
                                    <i class="fas fa-inbox fs-3" style="color:#94a3b8;"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Sin registros en este lote</h6>
                                <p class="mb-3" style="color:var(--c-muted);max-width:360px;font-size:.85rem;">
                                    @if(request('buscar_cedula') || request('estado'))
                                        Ningún registro coincide con los filtros de búsqueda.
                                    @else
                                        Carga un archivo Excel para poblar este bloque de datos.
                                    @endif
                                </p>
                                @if(request('buscar_cedula') || request('estado'))
                                    <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm text-decoration-none">
                                        <i class="fas fa-times me-1"></i> Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lotesCrudos->hasPages() || $lotesCrudos->total() > 0)
        <div class="bg-white border-top px-4 py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <p class="m-0 text-muted" style="font-size:.8rem;">
                Mostrando <strong class="text-dark">{{ $lotesCrudos->firstItem() ?? 0 }}</strong> – <strong class="text-dark">{{ $lotesCrudos->lastItem()  ?? 0 }}</strong> de <strong class="text-dark">{{ number_format($lotesCrudos->total(), 0, ',', '.') }}</strong> registros
            </p>
            <div class="pagination-minimal">
                {{ $lotesCrudos->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>
