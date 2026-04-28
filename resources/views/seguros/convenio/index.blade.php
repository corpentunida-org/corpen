@php
    $colors = ['success', 'info', 'secondary', 'warning', 'primary'];
@endphp
<x-base-layout>
    <x-success />
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Convenios:</h5>

                <a href="{{ route('seguros.convenio.create') }}" class="btn btn-sm btn-light-brand">
                    <i class="feather feather-plus"></i> Nuevo Convenio
                </a>
            </div>
            <div class="card-body table-responsive">
                <table id="tablaConvenios" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Aseguradora</th>
                            <th>Contrato</th>
                            <th>Fecha inicio</th>
                            <th>Fecha fin</th>
                            <th>Estado</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($convenios as $i => $convenio)
                            <tr>
                                <td>
                                    {{ $convenio->idAseguradora }}
                                </td>
                                <td>
                                    <a href="{{ route('seguros.convenio.show', ['convenio' => $convenio->id]) }}">
                                        {{ $convenio->nombre }}
                                    </a>
                                </td>

                                <td>
                                    {{ $convenio->fecha_inicio }}
                                </td>

                                <td>
                                    {{ $convenio->fecha_fin }}
                                </td>

                                <td>
                                    @if ($convenio->vigente)
                                        <span class="badge bg-success">Vigente</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('seguros.convenio.show', ['convenio' => $convenio->id]) }}"
                                        data-bs-toggle="tooltip" title="Ver detalle">
                                        <i class="feather feather-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-header">
            <h5 class="fw-bold mb-0 me-4">
                <span class="d-block mb-2">Cargar Archivo</span>
                <span class="fs-12 fw-normal text-muted text-truncate-1-line">El archivo debe tener el siguiente formato, este mismo orden de encabezados
                    y debe estar en formato CSV:</span>
            </h5>
        </div>
        <div class="card-body lead-status">
            <ul class="list-unstyled text-muted mb-0">
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"seg_asegurado_id": Número de identificación del asegurado de la póliza</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"fecha_novedad": Fecha de la novedad en formato YYYY-MM-DD</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"extra_prima": Porcentaje de la extraprima en formato de texto plano</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"seg_plan_id": Número identificador del plan que corresponda</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"valor_prima": Valor pago mensual individual a la aseguradora prestadora del servicio, en
                        valor de texto plano sin simbolos</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"valor_asegurado": Valor asegurado individual del plan, en valor de texto plano sin simbolos</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"valorpagaraseguradora": Valor pago mensual del titular (solo titulares)
                        por su grupo familiar, en
                        valor de texto plano sin simbolos</span>
                </li>
                <li class="d-flex align-items-start mb-1">
                    <span class="text-danger">
                        <i class="feather-check fs-10"></i>
                    </span>
                    <span class="fs-12 fw-normal text-truncate-1-line">"primapagar": Valor pago mensual individual a Corpentunida, en
                        valor de texto plano sin simbolos</span>
                </li>
            </ul>
            <form action="{{ route('seguros.poliza.nuevo-convenio') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="my-3">
                    <label class="form-label">Importar archivo (CSV) <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" name="archivo_csv" required>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">Actualizar pólizas</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#tablaConvenios').DataTable({
                pageLength: 10,
                order: []
            });
        });
    </script>
</x-base-layout>
