<x-base-layout>
    <x-success />

    @if (isset($no_actualizados))
        <div class="card stretch stretch-full">
            <div class="card-body p-4">
                <h4>Registros no encontrados</h4>
                <table class="table table-hover" id="tabla-no-actualizados">
                    <thead>
                        <tr>
                            <th>seg_asegurado_id</th>
                            <th>fecha_novedad</th>
                            <th>extra_prima</th>
                            <th>seg_plan_id</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($no_actualizados as $fila)
                            <tr>
                                <td>{{ $fila->seg_asegurado_id }}</td>
                                <td>{{ $fila->fecha_novedad }}</td>
                                <td>{{ $fila->extra_prima }}</td>
                                <td>{{ $fila->seg_plan_id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <script>
        $(document).ready(function() {

            $('#tabla-no-actualizados').DataTable({
                pageLength: 25,
                responsive: true,
                ordering: true,

                dom: 'Bfrtip',

                buttons: [{
                        extend: 'excel',
                        text: 'Exportar Excel'
                    },
                    {
                        extend: 'csv',
                        text: 'Exportar CSV'
                    },
                    {
                        extend: 'pdf',
                        text: 'Exportar PDF'
                    }
                ],

                language: {
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "No hay registros disponibles",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }

            });

        });
    </script>
</x-base-layout>
