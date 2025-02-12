<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title>Duralux || Tags Settings</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
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

    <div class="col-md-12">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <form action="{{ route('cartera.morosos.store') }}" method="post" enctype="multipart/form-data" class="input-group dropdown mb-4">
                    @csrf
                    <span class="input-group-text text-primary">
                        <i class="feather-tag"></i>
                    </span>                    
                    <input type="file" name="documento" class="form-control">
                    <button type="submit" class="btn btn-primary">Importar</button>
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
                                <td><button onclick="abrirWhatsApp(this)">Enviar Mensaje</button></td>
                            @endif
                        </tr>
                    </form>
                @endforeach
            </table>
        @endif
    </div>
    <script>
        function abrirWhatsApp(boton) {
            var fila = $(boton).closest('tr');
            var nombre = fila.find('td:eq(2)').text();
            var cedula = fila.find('td:eq(3)').text();
            var telefono = fila.find('td:eq(19)').text();


            var mensaje = encodeURIComponent(`Dios lo bendiga Hermano ${nombre} CC ${cedula},\n 
            Adjunto encontrará el reporte de su estado de cuenta del crédito Rapicredito de Libre Inversión\nPor favor, verificar si tiene alguna novedad o inquietud frente a la información suministrada.\n
            Quedo atento a cualquier comentario o sugerencia que desee compartir.\n
            Gracias por su atención.\n
            Cordialmente, \n
            Fabian Andres Fandiño\nAuxiliar de Cartera`);
            var url = "https://web.whatsapp.com/send?phone=57" + telefono + "&text=" + mensaje;
            //console.log(url)
            window.open(url, '_blank');
        }
    </script>
</body>

</html>
