<li class="nxl-item nxl-hasmenu">
    {{-- MÓDULO MAESTRO: GESTIÓN DE INVENTARIO --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="nxl-mtext">Gestión de Inventario</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        
        {{-- 1. TABLERO --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.tablero') }}">
                <i class="bi bi-speedometer2 me-2"></i> Tablero de Activos
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 5px 20px;"></li>
        
        {{-- 2. REGISTRAR COMPRA --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.compras.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-cart-plus me-2"></i> Registrar Compra
            </a>
        </li>

        {{-- 3. CREAR ACTA --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.movimientos.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-file-earmark-arrow-up me-2"></i> Crear Acta / Movimiento
            </a>
        </li>

        {{-- 4. REGISTRAR MANTENIMIENTO --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.mantenimientos.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-wrench-adjustable me-2"></i> Registrar Mantenimiento
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 5. ALMACÉN DE ACTIVOS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.activos.index') }}">
                <i class="bi bi-laptop me-2"></i> Almacén de Activos
            </a>
        </li>

        {{-- 6. HISTORIAL DE FACTURAS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.compras.index') }}">
                <i class="bi bi-receipt me-2"></i> Historial de Facturas
            </a>
        </li>

        {{-- 7. BITÁCORA DE MOVIMIENTOS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.movimientos.index') }}">
                <i class="bi bi-arrow-left-right me-2"></i> Bitácora de Movimientos
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 8. HISTORIAL TÉCNICO --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.mantenimientos.index') }}">
                <i class="bi bi-clipboard-pulse me-2"></i> Historial Técnico
            </a>
        </li>

        {{-- 9. ALERTAS Y GARANTÍAS --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.activos.alertas') }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Alertas y Garantías
            </a>
        </li>

        {{-- SEPARADOR DE CONFIGURACIÓN --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>
        
        {{-- 10. CONFIGURACIÓN Y CATÁLOGOS (NUEVA SECCIÓN) --}}
        
        {{-- Clasificación (Grupos y Líneas) --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.clasificacion.index') }}">
                <i class="bi bi-diagram-3 me-2"></i> Clasificación y Grupos
            </a>
        </li>

        {{-- Marcas --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.marcas.index') }}">
                <i class="bi bi-tags me-2"></i> Gestión de Marcas
            </a>
        </li>

        {{-- Bodegas --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.bodegas.index') }}">
                <i class="bi bi-geo-alt me-2"></i> Bodegas / Sedes
            </a>
        </li>

        {{-- Métodos de Pago --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('inventario.metodos-pago.index') }}">
                <i class="bi bi-credit-card me-2"></i> Métodos de Pago
            </a>
        </li>

        {{-- ZONA DE TRAMPA SCROLL --}}
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
    </ul>
</li>