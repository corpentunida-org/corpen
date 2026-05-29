<li class="nxl-item nxl-hasmenu">
    {{-- MÓDULO MAESTRO: GESTIÓN DE ASOCIADOS --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-person-vcard"></i></span>
        <span class="nxl-mtext">Asociados</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- 1. INDICADORES --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('asociados.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Tablero de Asociados
            </a>
        </li>

        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 5px 20px;"></li>
        
        {{-- 2. ACCIONES OPERATIVAS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('asociados.maestro.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-person-plus me-2"></i> Nuevo Asociado
            </a>
        </li>

        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 3. CONTROL Y SEGUIMIENTO --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('asociados.maestro.index') }}">
                <i class="bi bi-card-list me-2"></i> Directorio de Pastores
            </a>
        </li>

        {{-- 4. GESTIÓN DOCUMENTAL (RUTA CORREGIDA) --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('asociados.ecm.index') }}">
                <i class="bi bi-folder2-open me-2"></i> Expedientes y ECM
            </a>
        </li>

        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
    </ul>
</li>