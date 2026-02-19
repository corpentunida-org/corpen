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
                        @forelse($comunicaciones as $com)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $com->nro_oficio_salida }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    #{{ $com->correspondencia->nro_radicado ?? $com->id_correspondencia }}
                                </span>
                            </td>
                            <td>
                                @php
                                    // 💡 ACTUALIZADO: Colores basados en tu ENUM de la base de datos
                                    $color = [
                                        'Generado' => 'primary',
                                        'Enviado por Email' => 'info',
                                        'Notificado Físicamente' => 'success'
                                    ][$com->estado_envio] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }} rounded-pill">{{ $com->estado_envio }}</span>
                            </td>
                            <td>{{ $com->usuario->name ?? 'Usuario Desconocido' }}</td>
                            <td class="small">{{ $com->fecha_generacion ? $com->fecha_generacion->format('d/m/Y') : 'Pendiente' }}</td>
                            <td class="text-end px-4">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('correspondencia.comunicaciones-salida.show', $com) }}" class="btn btn-sm btn-light border" title="Ver Detalle">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                    
                                    <a href="{{ route('correspondencia.comunicaciones-salida.descargarPdf', $com->id_respuesta) }}" target="_blank" class="btn btn-sm btn-light border text-danger" title="Descargar / Ver PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    
                                    <a href="{{ route('correspondencia.comunicaciones-salida.edit', $com) }}" class="btn btn-sm btn-light border" title="Editar">
                                        <i class="fas fa-edit text-secondary"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3 d-block opacity-50"></i>
                                No hay comunicaciones de salida registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($comunicaciones->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $comunicaciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-base-layout>