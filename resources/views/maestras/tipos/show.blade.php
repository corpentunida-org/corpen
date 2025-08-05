<x-base-layout>
    <div class="card">
        {{-- Encabezado de la tarjeta --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detalles del Tipo: <span class="text-primary">{{ $tipo->nombre }}</span></h5>
            <a href="{{ route('maestras.tipos.index') }}" class="btn btn-secondary btn-sm">
                <i class="feather-arrow-left me-2"></i>Volver al listado
            </a>
        </div>

        {{-- Cuerpo de la tarjeta con la lista de detalles --}}
        <div class="card-body">
            <dl class="row">
                {{-- Información Básica --}}
                <dt class="col-sm-3">Código:</dt>
                <dd class="col-sm-9 fw-bold">{{ $tipo->codigo }}</dd>

                <dt class="col-sm-3">Nombre:</dt>
                <dd class="col-sm-9">{{ $tipo->nombre }}</dd>

                <dt class="col-sm-3">Descripción:</dt>
                <dd class="col-sm-9">{{ $tipo->descripcion ?? '-' }}</dd>

                {{-- Detalles de Clasificación --}}
                <dt class="col-sm-3">Grupo:</dt>
                <dd class="col-sm-9">{{ $tipo->grupo ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Categoría:</dt>
                <dd class="col-sm-9">{{ $tipo->categoria ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Orden:</dt>
                <dd class="col-sm-9">{{ $tipo->orden ?? 'N/A' }}</dd>

                {{-- Estado y Uso --}}
                <dt class="col-sm-3">Estado:</dt>
                <dd class="col-sm-9">
                    @if ($tipo->activo)
                        <span class="badge bg-soft-success text-success rounded-pill">Activo</span>
                    @else
                        <span class="badge bg-soft-danger text-danger rounded-pill">Inactivo</span>
                    @endif
                </dd>
                
                {{-- ================================================================= --}}
                {{--                 AQUÍ ESTÁ LA NUEVA FUNCIONALIDAD                 --}}
                {{-- ================================================================= --}}
                <dt class="col-sm-3 border-top pt-3">Terceros Vinculados:</dt>
                <dd class="col-sm-9 border-top pt-3">
                    @if($tipo->mae_terceros_count > 0)
                        <span class="badge bg-primary rounded-pill fs-6 fw-normal">
                            <i class="feather-users me-1"></i>
                            {{ $tipo->mae_terceros_count }} {{ Str::plural('registro', $tipo->mae_terceros_count) }}
                        </span>
                    @else
                        <span class="badge bg-secondary rounded-pill fw-normal">
                            0 registros
                        </span>
                    @endif
                </dd>
                {{-- ================================================================= --}}
                {{--                     FIN DE LA NUEVA FUNCIONALIDAD                     --}}
                {{-- ================================================================= --}}

                {{-- Información de Auditoría --}}
                <dt class="col-sm-3">Creado:</dt>
                <dd class="col-sm-9">{{ $tipo->created_at ? $tipo->created_at->format('d/m/Y h:i A') : '-' }}</dd>

                <dt class="col-sm-3">Última Actualización:</dt>
                <dd class="col-sm-9">{{ $tipo->updated_at ? $tipo->updated_at->format('d/m/Y h:i A') : 'N/A' }}</dd>
            </dl>

            {{-- Pie de página de la tarjeta con las acciones --}}
            <div class="mt-4 border-top pt-4">
                <a href="{{ route('maestras.tipos.edit', $tipo) }}" class="btn btn-primary">
                    <i class="feather-edit-3 me-2"></i>Editar Tipo
                </a>
            </div>
        </div>
    </div>
</x-base-layout>