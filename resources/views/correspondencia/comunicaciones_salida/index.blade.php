<x-base-layout>
    <div class="app-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title h3 fw-bold m-0">Comunicaciones de Salida</h1>
                <p class="text-muted">Registro y control de oficios enviados.</p>
            </div>
            <a href="{{ route('correspondencia.comunicaciones-salida.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-paper-plane me-2"></i> Nueva Salida
            </a>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">Oficio Nro.</th>
                            <th>Radicado Origen</th>
                            <th>Estado</th>
                            <th>Generado por</th>
                            <th>Fecha</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comunicaciones as $com)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $com->nro_oficio_salida }}</td>
                            <td><span class="badge bg-light text-dark border">#{{ $com->correspondencia->nro_radicado ?? $com->id_correspondencia }}</span></td>
                            <td>
                                @php
                                    $color = [
                                        'en_revision' => 'warning',
                                        'enviado' => 'success',
                                        'borrador' => 'secondary'
                                    ][$com->estado_envio] ?? 'info';
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill">{{ ucfirst(str_replace('_', ' ', $com->estado_envio)) }}</span>
                            </td>
                            <td>{{ $com->usuario->name }}</td>
                            <td class="small">{{ $com->fecha_generacion ? $com->fecha_generacion->format('d/m/Y') : 'Pendiente' }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="{{ route('correspondencia.comunicaciones-salida.show', $com) }}" class="btn btn-sm btn-light border"><i class="fas fa-eye"></i></a>
                                    @if($com->ruta_pdf)
                                        <a href="{{ route('correspondencia.comunicaciones-salida.descargarPdf', $com->id_respuesta) }}" class="btn btn-sm btn-light border text-danger"><i class="fas fa-file-pdf"></i></a>
                                    @endif
                                    <a href="{{ route('correspondencia.comunicaciones-salida.edit', $com) }}" class="btn btn-sm btn-light border"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-base-layout>