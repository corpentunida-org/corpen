<!-- ===== Menú: Soporte ===== -->
<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-headset"></i></span>
        <span class="nxl-mtext">Soporte</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <!-- Submenú: Soporte -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.create') }}">
                <i class="bi bi-plus-circle me-2"></i> Crear Soporte
            </a>
        </li>

        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.soportes.index') }}">
                <i class="bi bi-list-ul me-2"></i> Mi Lista
            </a>
        </li>

        {{-- Ejemplo de opción adicional (desactivada)
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.pendientes', ['estado' => 'resuelto']) }}">
                <i class="bi bi-check2-square me-2"></i> Lista Soportes
            </a>
        </li>
        --}}

        @candirect('soporte.lista.administrador')
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('soportes.tablero') }}">
                <i class="bi bi-gear me-2"></i> Parámetros de Soportes
            </a>
        </li>
        @endcandirect
    </ul>
</li>
