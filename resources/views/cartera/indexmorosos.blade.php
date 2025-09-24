<x-base-layout>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-body lead-status">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Cargar Archivo</span>
                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">El archivo debe tener el siguiente formato:</span>
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="" class="btn btn-primary p-2 mb-2">
                        <i class="feather-download me-2"></i>
                        <span>Descargar Archivo</span>
                    </a>
                </div>
                <ul class="list-unstyled text-muted mb-0">
                    <li class="d-flex align-items-start mb-1">
                        <span class="text-danger">
                            <i class="feather-check fs-10"></i>
                        </span>
                        <span class="fs-12 fw-normal text-truncate-1-line">"LINEA DE CRÉDITO": </span>
                    </li>
                </ul>
                <form class="row" action="{{ route('cartera.morosos.store') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="my-3">
                        <label for="observacion" class="form-label">Importar Excel <span
                                class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="documento" required>
                        @if ($errors->has('file'))
                            <div class="invalid-feedback">
                                Solo se permiten archivos de tipo: .xls, .xlsx
                            </div>
                        @endif
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
    <div class="p-2" style="font-size: 11px;">
        @if (isset($data) && count($data) > 0)
            <table class="table table-striped">
                @php
                    $headers = $data[0];
                @endphp

                @foreach ($data as $rowIndex => $row)
                    <form action="{{ route('cartera.morosos.pdfMora') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <tr>
                            @foreach ($row as $cellIndex => $cell)
                                @php
                                    $name = $headers[$cellIndex];
                                @endphp
                                <input type="hidden" name="{{ $name }}" value="{{ $cell }}">
                                <td>{{ $cell }}</td>
                            @endforeach
                            @if ($rowIndex != 0)
                                <td><button onclick="abrirWhatsApp(this)" class="badge text-bg-primary text-wrap border border-0">Enviar Mensaje</button></td>
                            @endif
                        </tr>
                    </form>
                @endforeach
            </table>
        @endif
    </div>
    <script>
        function abrirWhatsApp(boton) {
            $(boton).removeClass('text-bg-primary').addClass('text-bg-success');
            var fila = $(boton).closest('tr');
            var cedula = fila.find('td:eq(0)').text();
            var nombre = fila.find('td:eq(1)').text();
            var telefono = fila.find('td:eq(2)').text();
            var linea = fila.find('td:eq(3)').text();
            console.log("estas entrando aca pa?" + telefono);

            var mensaje = encodeURIComponent(`Dios lo bendiga Hermano ${nombre} CC ${cedula},\n 
            Adjunto encontrará el reporte de su estado de cuenta del crédito ${linea}\nPor favor, verificar si tiene alguna novedad o inquietud frente a la información suministrada.\n
            Quedo atento a cualquier comentario o sugerencia que desee compartir.\n
            Gracias por su atención.\n
            Cordialmente, \n
            Fabian Andres Fandiño\nAuxiliar de Cartera`);
            var url = "https://web.whatsapp.com/send?phone=57" + telefono + "&text=" + mensaje;
            window.open(url, '_blank');
        }
    </script>
</x-base-layout>
