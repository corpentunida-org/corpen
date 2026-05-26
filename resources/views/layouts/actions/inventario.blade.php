<li class="nxl-item nxl-hasmenu">
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="nxl-mtext">Gestión de Inventario</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu" style="padding: 0; background: #ffffff;">  
        
        {{-- 1. TABLERO --}}
        <li class="nxl-item" style="background: #f1f5f9;">
            <a class="nxl-link" href="{{ route('inventario.tablero') }}" style="color: #0f172a; font-weight: 600;">
                <i class="bi bi-speedometer2 me-2" style="color: #64748b;"></i> Tablero de Control
            </a>
        </li>
        
        {{-- 2. OPERACIONES --}}
        <li class="nxl-item" style="background: #f8fafc;"><a class="nxl-link" href="{{ route('inventario.compras.create') }}"><i class="bi bi-cart-plus me-2" style="color: #94a3b8;"></i> Registrar Compra</a></li>
        <li class="nxl-item" style="background: #f8fafc;"><a class="nxl-link" href="{{ route('inventario.movimientos.create') }}"><i class="bi bi-file-earmark-arrow-up me-2" style="color: #94a3b8;"></i> Crear Acta / Movimiento</a></li>
        <li class="nxl-item" style="background: #f8fafc;"><a class="nxl-link" href="{{ route('inventario.mantenimientos.create') }}"><i class="bi bi-wrench-adjustable me-2" style="color: #94a3b8;"></i> Registrar Mantenimiento</a></li>

        {{-- 3. ALMACÉN Y GESTIÓN --}}
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.activos.index') }}"><i class="bi bi-laptop me-2" style="color: #94a3b8;"></i> Almacén de Activos</a></li>
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.compras.index') }}"><i class="bi bi-receipt me-2" style="color: #94a3b8;"></i> Historial de Facturas</a></li>
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.movimientos.index') }}"><i class="bi bi-arrow-left-right me-2" style="color: #94a3b8;"></i> Bitácora de Movimientos</a></li>

        {{-- 4. TÉCNICO Y ALERTAS --}}
        <li class="nxl-item" style="background: #f8fafc;"><a class="nxl-link" href="{{ route('inventario.mantenimientos.index') }}"><i class="bi bi-clipboard-pulse me-2" style="color: #94a3b8;"></i> Historial Técnico</a></li>
        <li class="nxl-item" style="background: #f8fafc;"><a class="nxl-link" href="{{ route('inventario.activos.alertas') }}"><i class="bi bi-exclamation-triangle me-2" style="color: #94a3b8;"></i> Alertas y Garantías</a></li>

        {{-- 5. CONFIGURACIÓN --}}
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.clasificacion.index') }}" style="color: #64748b;"><i class="bi bi-diagram-3 me-2" style="color: #cbd5e1;"></i> Clasificación</a></li>
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.marcas.index') }}" style="color: #64748b;"><i class="bi bi-tags me-2" style="color: #cbd5e1;"></i> Gestión de Marcas</a></li>
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.bodegas.index') }}" style="color: #64748b;"><i class="bi bi-geo-alt me-2" style="color: #cbd5e1;"></i> Bodegas / Sedes</a></li>
        <li class="nxl-item" style="background: #ffffff;"><a class="nxl-link" href="{{ route('inventario.metodos-pago.index') }}" style="color: #64748b;"><i class="bi bi-credit-card me-2" style="color: #cbd5e1;"></i> Métodos de Pago</a></li>
    </ul>
</li>