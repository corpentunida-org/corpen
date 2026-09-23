<x-base-layout>
    @section('titlepage', 'Adjuntos de ' . $anio)
    <x-success />
    <x-error />

    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1"><i class="feather-folder me-2"></i>Adjuntos de {{ $anio }}</h5>
                    <p class="text-muted mb-0 small">{{ number_format($seguimientos->total()) }} archivos en total. El tamaño de los que aún no lo tenían guardado se calcula al mostrarlos aquí.</p>
                </div>
                <a href="{{ route('admin.adjuntos.index') }}" class="btn btn-sm btn-light">
                    <i class="feather-arrow-left me-1"></i>Volver al resumen
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Desde</label>
                        <input type="date" name="desde" class="form-control form-control-sm" value="{{ request('desde') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Hasta</label>
                        <input type="date" name="hasta" class="form-control form-control-sm" value="{{ request('hasta') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Tamaño mín. (MB)</label>
                        <input type="number" step="0.1" min="0" name="tamano_min" class="form-control form-control-sm" value="{{ request('tamano_min') }}" placeholder="Ej: 1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Tamaño máx. (MB)</label>
                        <input type="number" step="0.1" min="0" name="tamano_max" class="form-control form-control-sm" value="{{ request('tamano_max') }}" placeholder="Ej: 5">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Cliente (nombre o cédula)</label>
                        <input type="text" name="cliente" class="form-control form-control-sm" value="{{ request('cliente') }}" placeholder="Ej: 123456789">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="feather-filter"></i></button>
                        @if (request()->anyFilled(['desde', 'hasta', 'tamano_min', 'tamano_max', 'cliente']))
                            <a href="{{ route('admin.adjuntos.ver', $anio) }}" class="btn btn-sm btn-outline-secondary" title="Quitar filtros"><i class="feather-x"></i></a>
                        @endif
                    </div>
                    @if (request()->filled('tamano_min') || request()->filled('tamano_max'))
                        <div class="col-12">
                            <small class="text-muted"><i class="feather-info me-1"></i>El filtro de tamaño solo compara entre los archivos que ya tienen el tamaño calculado (columna "Tamaño"). Los que todavía no lo tienen quedan fuera hasta que se hayan visto sin ese filtro al menos una vez.</small>
                        </div>
                    @endif
                </form>

                @if ($seguimientos->isEmpty())
                    <p class="text-muted mb-0">No hay adjuntos de {{ $anio }} para mostrar.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Interacción</th>
                                    <th>Cédula</th>
                                    <th>Cliente</th>
                                    <th>Subido por</th>
                                    <th>Archivo</th>
                                    <th class="text-end">Tamaño</th>
                                    <th class="text-end">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($seguimientos as $seguimiento)
                                    @php
                                        $ruta = is_array($seguimiento->attachment_urls) ? ($seguimiento->attachment_urls[0] ?? null) : $seguimiento->attachment_urls;
                                        $nombreArchivo = $ruta ? basename($ruta) : '—';
                                    @endphp
                                    <tr>
                                        <td class="small">{{ $seguimiento->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="small">
                                            @if ($seguimiento->interaction)
                                                <a href="{{ route('interactions.show', $seguimiento->id_interaction) }}">#{{ $seguimiento->id_interaction }}</a>
                                            @else
                                                #{{ $seguimiento->id_interaction }}
                                            @endif
                                        </td>
                                        <td class="small">{{ optional($seguimiento->interaction)->client_id ?? '—' }}</td>
                                        <td class="small">{{ optional(optional($seguimiento->interaction)->client)->nom_ter ?? '—' }}</td>
                                        <td class="small">{{ optional($seguimiento->creator)->name ?? '—' }}</td>
                                        <td class="small text-truncate d-inline-block" style="max-width: 260px;" title="{{ $nombreArchivo }}">{{ $nombreArchivo }}</td>
                                        <td class="text-end small">
                                            {{ \App\Http\Controllers\Admin\AdjuntosInteraccionController::formatoTamano($seguimiento->attachment_size) }}
                                        </td>
                                        <td class="text-end">
                                            @if ($ruta)
                                                <a href="{{ $seguimiento->getFile($seguimiento->attachment_urls) }}" target="_blank" class="btn btn-sm btn-icon-modern">
                                                    <i class="feather-external-link"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $seguimientos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .btn-icon-modern {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #eee; background: white; color: #555;
        }
        .btn-icon-modern:hover { background: #000; color: #fff; }
    </style>
</x-base-layout>
