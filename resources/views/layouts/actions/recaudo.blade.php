<li class="nxl-item nxl-hasmenu">
    {{-- MÓDULO MAESTRO: GESTIÓN DE RECAUDO --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-cash-coin"></i></span>
        <span class="nxl-mtext">Recaudo</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        
        {{-- 1. INDICADORES --}}
        <li class="nxl-item">
            <a class="nxl-link" href="#">
                <i class="bi bi-speedometer2 me-2"></i> Tablero de Recaudo
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 5px 20px;"></li>
        
        {{-- 2. ACCIONES OPERATIVAS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('recaudo.imputaciones.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-journal-plus me-2"></i> Nueva Imputación
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 3. CONTROL Y SEGUIMIENTO --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('recaudo.imputaciones.index') }}">
                <i class="bi bi-check2-square me-2"></i> Control de Imputaciones
            </a>
        </li>

        {{-- 4. GESTIÓN DOCUMENTAL --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('recaudo.imputaciones.index') }}">
                <i class="bi bi-file-earmark-pdf me-2"></i> Archivo de Soportes (ECM)
            </a>
        </li>

        {{-- ZONA DE TRAMPA SCROLL PARA UI --}}
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
    </ul>
</li>