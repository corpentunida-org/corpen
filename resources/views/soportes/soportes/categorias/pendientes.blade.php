<x-base-layout>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <!-- Encabezado con contador -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Soportes Pendientes 
                    <span class="badge bg-danger fs-6 ms-2">
                        {{ $soportesPendientes->count() }}
                    </span>
                </h4>
                <div id="exportButtons"></div>
            </div>

             <a class="nxl-link" href="{{ route('soportes.pendientes', ['estado' => 'resuelto']) }}">
                Sin Asignar
            </a>

            <!-- Filtros -->
            <div class="card p-3 bg-light mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Área</label>
                        <select id="filterArea" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($soportesPendientes->pluck('cargo.gdoArea.nombre')->unique() as $area)
                                @if($area)
                                    <option value="{{ $area }}">{{ $area }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Prioridad</label>
                        <select id="filterPrioridad" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($soportesPendientes->pluck('prioridad.nombre')->unique() as $p)
                                @if($p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <select id="filterUsuario" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            @foreach($soportesPendientes->pluck('usuario.name')->unique() as $u)
                                @if($u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Creación</label>
                        <input type="date" id="filterFecha" class="form-control form-control-sm" />
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle small soporteTable"
                       style="font-size: 0.875rem; width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fecha Creado</th>
                            <th>Creado Por</th>
                            <th>Área</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>SubTipo</th>
                            <th>Prioridad</th>
                            <th>Descripción</th>
                            <th>Asignado</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soportesPendientes as $soporte)
                            @php
                                $areaModal = $soporte->cargo->gdoArea->nombre ?? 'Soporte';
                                $prioridadColor = match($soporte->prioridad->nombre ?? '') {
                                    'Alta' => 'danger',
                                    'Media' => 'warning',
                                    'Baja' => 'success',
                                    default => 'secondary'
                                };
                                $rowClass = match($soporte->prioridad->nombre ?? '') {
                                    'Alta' => 'table-danger',
                                    'Media' => 'table-warning',
                                    'Baja' => 'table-success',
                                    default => ''
                                };
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td>{{ $soporte->id }}</td>
                                <td>{{ $soporte->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $soporte->usuario->name ?? 'N/A' }}</td>
                                <td>{{ $areaModal }}</td>
                                <td>{{ $soporte->categoria->nombre ?? 'N/A' }}</td>
                                <td>{{ $soporte->tipo->nombre ?? 'N/A' }}</td>
                                <td>{{ $soporte->subTipo->nombre ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $prioridadColor }}">
                                        {{ $soporte->prioridad->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($soporte->detalles_soporte, 50) }}</td>
                                <td>{{ $soporte->scpUsuarioAsignado->maeTercero->nom_ter ?? 'Sin escalar' }}</td>
                                <td><span class="badge bg-warning">Pendiente</span></td>
                                <td class="text-end">
                                    <a href="{{ route('soportes.soportes.show', $soporte) }}" class="btn btn-sm btn-info">Ver</a>
                                    <a href="{{ route('soportes.soportes.edit', $soporte) }}" class="btn btn-sm btn-primary">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No hay soportes pendientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <!-- DataTables Buttons -->
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var table = $('.soporteTable').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success btn-sm' },
                        { extend: 'pdfHtml5', text: '📄 PDF', className: 'btn btn-danger btn-sm' },
                        { extend: 'print', text: '🖨️ Imprimir', className: 'btn btn-secondary btn-sm' }
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });

                // Mover los botones al header
                table.buttons().container().appendTo('#exportButtons');

                // Filtros personalizados
                $('#filterArea').on('change', function() {
                    table.column(3).search(this.value).draw();
                });
                $('#filterPrioridad').on('change', function() {
                    table.column(7).search(this.value).draw();
                });
                $('#filterUsuario').on('change', function() {
                    table.column(2).search(this.value).draw();
                });
                $('#filterFecha').on('change', function() {
                    table.column(1).search(this.value).draw();
                });
            });
        </script>
    @endpush
</x-base-layout>
