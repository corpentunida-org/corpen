{{-- DENTRO DEL MENU (ul) --}}
<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-wallet2"></i></span>
        <span class="nxl-mtext">Certificados</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.operaciones.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i> Matriz de Cartera y Cobros
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.frontdesk.index') }}">
                <i class="bi bi-person-badge me-2"></i> Atención y Clientes
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.catalogos.index') }}">
                <i class="bi bi-list-check me-2"></i> Reglas y Parámetros
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.ingesta.index') }}">
                <i class="bi bi-database-gear me-2"></i> Subir Excel / Archivos
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('certificados.auditoria.index') }}">
                <i class="bi bi-terminal me-2"></i> Registro de Actividad
            </a>
        </li>
    </ul>
</li>
