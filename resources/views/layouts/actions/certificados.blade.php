{{-- Verificamos si tiene acceso a al menos una vista para mostrar el bloque "Certificados" --}}
@canany([
    'certificados.operaciones.index',
    'certificados.frontdesk.index',
    'certificados.catalogos.index',
    'certificados.ingesta.index',
    'certificados.informes.index',
    'certificados.auditoria.index'
])
<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">Certificados</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">

        @can('certificados.operaciones.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.operaciones.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i> Matriz de Cartera y Cobros
            </a>
        </li>
        @endcan

        @can('certificados.frontdesk.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.frontdesk.index') }}">
                <i class="bi bi-person-badge me-2"></i> Atención y Clientes
            </a>
        </li>
        @endcan

        @can('certificados.catalogos.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.catalogos.index') }}">
                <i class="bi bi-list-check me-2"></i> Reglas y Parámetros
            </a>
        </li>
        @endcan

        @can('certificados.ingesta.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.ingesta.index') }}">
                <i class="bi bi-database-gear me-2"></i> Subir Excel / Archivos
            </a>
        </li>
        @endcan

        @can('certificados.informes.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.informes.index') }}">
                <i class="bi bi-pie-chart me-2"></i> Informes y Analítica
            </a>
        </li>
        @endcan

        @can('certificados.auditoria.index')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.auditoria.index') }}">
                <i class="bi bi-terminal me-2"></i> Registro de Actividad
            </a>
        </li>
        @endcan

    </ul>
</li>
@endcanany
