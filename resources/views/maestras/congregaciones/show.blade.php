<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Congregación - {{ $congregacion->nombre }}</title>

    @unless(request()->has('pdf'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://unpkg.com/feather-icons"></script>
    @endunless

    <style>
        @page {
            margin: 60px 50px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 11.5px;
            color: #1e1e1e;
            margin: {{ request()->has('pdf') ? '0' : 'auto' }};
            background-color: {{ request()->has('pdf') ? 'white' : '#f8f9fa' }};
        }

        header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        .header-title {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
        }

        .header-subtitle {
            font-size: 12px;
            color: #555;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
        }

        .details th {
            text-align: left;
            background-color: #f0f3f5;
            padding: 8px;
            width: 30%;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }

        .details td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            color: white;
        }

        .bg-success { background-color: #28a745; }
        .bg-danger  { background-color: #dc3545; }

        footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #999;
        }

        .metadata {
            margin-top: 20px;
            font-size: 10px;
            color: #777;
        }

        .card-web {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            padding: 25px;
            margin-top: 30px;
        }

        @if(request()->has('pdf'))
        body::before {
            content: "C O R P E N   U N I D A";
            position: fixed;
            top: 35%;
            left: 10%;
            width: 80%;
            font-size: 60px;
            color: #999;
            text-align: center;
            opacity: 0.07;
            transform: rotate(-30deg);
            z-index: 0;
            pointer-events: none;
        }
        @endif
    </style>
</head>

<body>

@unless(request()->has('pdf'))
    <div class="container">
        <div class="card-web">
@endunless

<header>
    <div class="header-title">Informe Oficial de Congregación</div>
    <div class="header-subtitle">
        Sistema de Gestión de Congregaciones - {{ now()->format('d/m/Y H:i') }}
    </div>
</header>

@unless(request()->has('pdf'))
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('maestras.congregacion.index') }}"
           class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill">
            <i data-feather="chevron-left" class="me-2"></i> Volver a la Lista
        </a>

        <a href="{{ route('maestras.congregacion.show', [$congregacion->codigo, 'pdf' => 1]) }}"
           class="btn btn-outline-danger btn-sm shadow-sm rounded-pill">
            <i data-feather="download" class="me-2"></i> Descargar PDF
        </a>
    </div>
@endunless

<div class="section-title">Información General</div>
<table class="details">
    <tr>
        <th>Código</th>
        <td>{{ $congregacion->codigo }}</td>
    </tr>
    <tr>
        <th>Nombre</th>
        <td>{{ $congregacion->nombre }}</td>
    </tr>
    <tr>
        <th>Estado</th>
        <td>
            @if ($congregacion->estado)
                <span class="badge bg-success">Activo</span>
            @else
                <span class="badge bg-danger">Inactivo</span>
            @endif
        </td>
    </tr>
</table>

<div class="section-title">Ubicación Administrativa</div>
<table class="details">
    <tr>
        <th>Clase de Congregación</th>
        <td>{{ $congregacion->claseCongregacion->nombre ?? 'No especificada' }}</td>
    </tr>
    <tr>
        <th>Distrito</th>
        <td>{{ $congregacion->maeDistritos->NOM_DIST ?? 'No asignado' }}</td>
    </tr>
    <tr>
        <th>Municipio</th>
        <td>{{ $congregacion->maeMunicipios->nombre ?? 'No disponible' }}</td>
    </tr>
</table>

<div class="section-title">Responsable</div>
<table class="details">
    <tr>
        <th>Pastor Asignado</th>
        <td>
            <strong>{{ $congregacion->maeTerceros->nom_ter ?? 'Sin asignar' }}</strong><br>
            <small>Código: {{ $congregacion->maeTerceros->cod_ter ?? 'N/A' }}</small>
        </td>
    </tr>
</table>

<div class="metadata">
    Documento generado automáticamente. No requiere firma.<br>
    Para más información, consulte el sistema o comuníquese con el área administrativa.
</div>

<footer>
    &copy; {{ date('Y') }} Corpentunida. Todos los derechos reservados.
</footer>

@unless(request()->has('pdf'))
        </div>
    </div>
@endunless

@unless(request()->has('pdf'))
    <script>
        feather.replace();
    </script>
@endunless

</body>
</html>
