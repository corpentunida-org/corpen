<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Tercero - {{ $tercero->nom_ter }}</title>

    @unless(request()->has('pdf'))
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://unpkg.com/feather-icons"></script>
    @endunless

    <style>
        @page {
            margin: 50px 45px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 10.5px;
            color: #1e1e1e;
            margin: {{ request()->has('pdf') ? '0' : 'auto' }};
            background-color: {{ request()->has('pdf') ? 'white' : '#f8f9fa' }};
        }

        header {
            text-align: center;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .header-subtitle {
            font-size: 11px;
            color: #555;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 8px;
            color: #2c3e50;
            background-color: #f0f3f5;
            padding: 5px 8px;
            border-left: 3px solid #2c3e50;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .details th {
            text-align: left;
            background-color: #f8f9fa;
            padding: 6px 8px;
            width: 33%;
            font-weight: 600;
            border: 1px solid #e0e0e0;
        }

        .details td {
            padding: 6px 8px;
            border: 1px solid #e0e0e0;
            word-wrap: break-word;
        }
        
        .details tr:nth-child(even) td {
            background-color: #fdfdfd;
        }
        
        .text-muted {
            color: #888;
            font-style: italic;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            color: white;
        }

        .bg-success { background-color: #28a745; }
        .bg-danger  { background-color: #dc3545; }

        footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
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
            top: 40%;
            left: 5%;
            width: 90%;
            font-size: 50px;
            color: #e0e0e0;
            text-align: center;
            opacity: 0.5;
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
    <div class="header-title">Ficha de Información de Tercero</div>
    <div class="header-subtitle">
        Sistema de Gestión - {{ now()->format('d/m/Y H:i') }}
    </div>
</header>

@unless(request()->has('pdf'))
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('maestras.terceros.index') }}"
           class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill">
            <i data-feather="chevron-left" class="me-2"></i> Volver
        </a>
        <a href="{{ route('maestras.terceros.show', [$tercero->cod_ter, 'pdf' => 1]) }}"
           class="btn btn-outline-danger btn-sm shadow-sm rounded-pill">
            <i data-feather="download" class="me-2"></i> Descargar PDF
        </a>
    </div>
@endunless

{{-- SECCIÓN 1: DATOS DE IDENTIFICACIÓN --}}
<div class="section-title">1. Información General y de Identificación</div>
<table class="details">
    <tr>
        <th>Nombre Completo / Razón Social</th>
        <td><strong>{{ $tercero->nom_ter ?? 'No registrado' }}</strong></td>
    </tr>
    <tr>
        <th>Identificación (Cod. Tercero / NIT)</th>
        <td>{{ $tercero->cod_ter ?? 'N/A' }} - DV: {{ $tercero->dv ?? 'N/A' }}</td>
    </tr>
    <tr>
        <th>Estado</th>
        <td>
            @if(isset($tercero->estado))
                @if ($tercero->estado)
                    <span class="badge bg-success">Activo</span>
                @else
                    <span class="badge bg-danger">Inactivo</span>
                @endif
            @else
                 <span class="text-muted">No definido</span>
            @endif
        </td>
    </tr>
     <tr>
        <th>Tipo de Tercero</th>
        <td>{{ optional($tercero->maeTipos)->nombre ?? 'No definido' }}</td>
    </tr>
     <tr>
        <th>Clasificación</th>
        <td>{{ $tercero->clasific ?? 'No definida' }}</td>
    </tr>
    <tr>
        <th>Fecha de Ingreso</th>
        <td>@if($tercero->fec_ing){{ \Carbon\Carbon::parse($tercero->fec_ing)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Fecha de Actualización</th>
        <td>@if($tercero->fec_act){{ \Carbon\Carbon::parse($tercero->fec_act)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Primer Nombre / Apellido</th>
        <td>{{ $tercero->nom1 ?? '' }} {{ $tercero->apl1 ?? '' }}</td>
    </tr>
    <tr>
        <th>Segundo Nombre / Apellido</th>
        <td>{{ $tercero->nom2 ?? '' }} {{ $tercero->apl2 ?? '' }}</td>
    </tr>
</table>

{{-- SECCIÓN 2: UBICACIÓN Y CONTACTO --}}
<div class="section-title">2. Información de Ubicación y Contacto</div>
<table class="details">
    <tr>
        <th>Dirección Principal</th>
        <td>{{ $tercero->dir ?? 'No registrada' }}</td>
    </tr>
    <tr>
        <th>Dirección Comercial</th>
        <td>{{ $tercero->dir_comer ?? ($tercero->dir1 ?? 'No registrada') }}</td>
    </tr>
     <tr>
        <th>País</th>
        <td>{{ $tercero->cod_pais ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Departamento</th>
        <td>{{ $tercero->cod_depa ?? ($tercero->dpto ?? 'No registrado') }}</td>
    </tr>
    <tr>
        <th>Municipio / Ciudad</th>
        <td>{{ $tercero->mun ?? ($tercero->ciudad ?? 'No registrado') }}</td>
    </tr>
    <tr>
        <th>Código Postal</th>
        <td>{{ $tercero->cod_postal ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Barrio</th>
        <td>{{ $tercero->barrio ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Teléfono Fijo</th>
        <td>{{ $tercero->tel1 ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Teléfono Celular</th>
        <td>{{ $tercero->cel ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Email Principal</th>
        <td>{{ $tercero->email ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Email Facturación Electrónica</th>
        <td>{{ $tercero->email_fe ?? ($tercero->email_fac ?? 'No registrado') }}</td>
    </tr>
     <tr>
        <th>Persona de Contacto</th>
        <td>{{ $tercero->contacto ?? 'No registrado' }}</td>
    </tr>
     <tr>
        <th>Cargo del Contacto</th>
        <td>{{ $tercero->cargo ?? 'No registrado' }}</td>
    </tr>
</table>

{{-- SECCIÓN 3: DATOS PERSONALES --}}
<div class="section-title">3. Información Personal (Si Aplica)</div>
<table class="details">
    <tr>
        <th>Fecha de Nacimiento</th>
        <td>
            @if($tercero->fec_nac){{ \Carbon\Carbon::parse($tercero->fec_nac)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif
        </td>
    </tr>
    <tr>
        <th>Lugar de Nacimiento</th>
        <td>{{ $tercero->lugar_naci ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Sexo</th>
        <td>
            @if(isset($tercero->sexo))
                @switch(strtoupper($tercero->sexo))
                    @case('M')
                        Masculino
                        @break
                    @case('F')
                        Femenino
                        @break
                    @default
                        {{ $tercero->sexo }}
                @endswitch
            @else
                <span class="text-muted">No registrado</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Estado Civil</th>
        <td>
            @if(isset($tercero->est_civil))
                {{-- PUEDES AJUSTAR ESTOS VALORES SEGÚN TU SISTEMA --}}
                @switch($tercero->est_civil)
                    @case('1') Soltero(a) @break
                    @case('2') Casado(a) @break
                    @case('3') Viudo(a) @break
                    @case('4') Unión Libre @break
                    @case('5') Divorciado(a) @break
                    @default
                        {{ $tercero->est_civil }} (Código no definido)
                @endswitch
            @else
                <span class="text-muted">No registrado</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Nombre del Cónyuge</th>
        <td>{{ $tercero->nom_conyug ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th># de Hijos</th>
        <td>{{ $tercero->num_hijos ?? 'No registrado' }}</td>
    </tr>
</table>


{{-- SECCIÓN 4: DATOS FISCALES Y FINANCIEROS --}}
<div class="section-title">4. Información Fiscal y Financiera</div>
<table class="details">
    <tr>
        <th>Tipo de Persona</th>
        <td>{{ $tercero->tip_pers ?? 'No definido' }}</td>
    </tr>
    <tr>
        <th>Régimen</th>
        <td>{{ $tercero->regimen ?? 'No definido' }}</td>
    </tr>
    <tr>
        <th>Responsabilidad Fiscal</th>
        <td>{{ $tercero->cod_respfiscal ?? 'No definida' }}</td>
    </tr>
     <tr>
        <th>Actividad Económica (CIIU)</th>
        <td>{{ $tercero->Cod_acteco ?? ($tercero->cod_act ?? 'No definida') }}</td>
    </tr>
    <tr>
        <th>Información de Retenciones</th>
        <td>
            ReteFuente: {{ isset($tercero->ind_rete) && $tercero->ind_rete ? 'Sí' : 'No' }} | 
            ReteIVA: {{ isset($tercero->ret_iva) && $tercero->ret_iva ? 'Sí' : 'No' }} | 
            ReteICA: {{ isset($tercero->ret_ica) && $tercero->ret_ica ? 'Sí' : 'No' }} ({{ $tercero->por_ica ?? 0 }}%)
        </td>
    </tr>
    <tr>
        <th>Banco</th>
        <td>{{ $tercero->cod_ban ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Número de Cuenta</th>
        <td>{{ $tercero->cta_ban ?? ($tercero->cta ?? 'No registrada') }}</td>
    </tr>
    <tr>
        <th>Cupo de Crédito</th>
        <td>${{ number_format($tercero->cupo_cred ?? 0, 0, ',', '.') }}</td>
    </tr>
</table>


{{-- SECCIÓN 5: DATOS ECLESIÁSTICOS --}}
<div class="section-title">5. Información Eclesiástica (Si Aplica)</div>
<table class="details">
    <tr>
        <th>Fecha de Ministerio</th>
        <td>@if($tercero->fec_minis){{ \Carbon\Carbon::parse($tercero->fec_minis)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Fecha IPUC</th>
        <td>@if($tercero->fecha_ipuc){{ \Carbon\Carbon::parse($tercero->fecha_ipuc)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Fecha Inicio Aportes</th>
        <td>@if($tercero->fec_aport){{ \Carbon\Carbon::parse($tercero->fec_aport)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Código de Licencia</th>
        <td>{{ $tercero->cod_lice ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Fecha de Licencia</th>
        <td>@if($tercero->fecha_lice){{ \Carbon\Carbon::parse($tercero->fecha_lice)->format('d/m/Y') }}@else <span class="text-muted">No registrada</span> @endif</td>
    </tr>
    <tr>
        <th>Distrito Ministerial</th>
        <td>{{ $tercero->cod_dist ?? 'No registrado' }}</td>
    </tr>
     <tr>
        <th>Clase Ministerial</th>
        <td>{{ $tercero->cod_clase ?? 'No registrado' }}</td>
    </tr>
     <tr>
        <th>Estado Ministerial</th>
        <td>{{ $tercero->cod_est ?? 'No registrado' }}</td>
    </tr>
    <tr>
        <th>Congregación Asignada</th>
        <td>{{ $tercero->congrega ?? 'No registrada' }}</td>
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