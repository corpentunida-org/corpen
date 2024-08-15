<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <x-component-header />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <form action="{{route('cartera.morosos.store')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="documento">
        <button type="submit">Importar</button>
    </form>

    @if(isset($data) && count($data) > 0)
        <table class="table">
        @php
            $headers = $data[0];
        @endphp

        @foreach($data as $rowIndex => $row)
            <form action="{{ route('cartera.morosos.pdfMora') }}" method="post" enctype="multipart/form-data">
            @csrf
                <tr>
                    @foreach($row as $cellIndex => $cell)
                        @php
                            $name = $headers[$cellIndex];
                        @endphp
                        <input type="hidden" name="{{ $name }}" value="{{ $cell }}">
                        <td>{{ $cell }}</td>
                    @endforeach
                    <td><button onclick="abrirWhatsApp(this)">*</button></td>
                </tr>
            </form>
        @endforeach
        </table>        
    @endif

    <script>
        function abrirWhatsApp(boton) {
            var fila = $(boton).closest('tr');
            var nombre = fila.find('td:eq(2)').text();
            var telefono = fila.find('td:eq(4)').text();
            
         
            var mensaje = encodeURIComponent(`hola ${nombre}, prueba del reporte`);
            var url = "https://web.whatsapp.com/send?phone=57" + telefono + "&text=" + mensaje;
            //console.log(url)
            window.open(url, '_blank');
        }
    </script>
</body>

</html>