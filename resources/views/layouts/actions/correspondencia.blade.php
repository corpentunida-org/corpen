<li class="nxl-item nxl-hasmenu">
    {{-- MÓDULO CORRESPONDENCIA --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-envelope-fill"></i></span>
        <span class="nxl-mtext">Correspondencia</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        {{-- 1. DASHBOARD ESTRATÉGICO --}}
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.tablero') }}">
                <i class="bi bi-speedometer2 me-2"></i> Tablero de Control
            </a>
        </li> -->

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height:1px; background: rgba(0,0,0,0.04); margin:5px 20px;"></li>

        {{-- 2. GESTIÓN DOCUMENTAL --}}
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.correspondencias.index') }}">
                <i class="bi bi-journal-text me-2"></i> Listado de Radicados
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.correspondencias.create') }}" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-plus-square-fill me-2"></i> Radicar Documento
            </a>
        </li>

        {{-- SEPARADOR --}}
        <li class="nxl-item-separator" style="height:1px; background: rgba(0,0,0,0.04); margin:10px 20px;"></li>

        {{-- 3. CONFIGURACIÓN TÉCNICA (TRD Y FLUJOS) --}}
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.trds.index') }}">
                <i class="bi bi-archive me-2"></i> Tablas de Retención (TRD)
            </a>
        </li> -->
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.flujos.index') }}">
                <i class="bi bi-bezier2 me-2"></i> Flujos de Trabajo
            </a>
        </li>
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.procesos.index') }}">
                <i class="bi bi-diagram-3 me-2"></i> Definición de Procesos
            </a>
        </li> -->

        {{-- SEPARADOR --}}
        <!-- <li class="nxl-item-separator" style="height:1px; background: rgba(0,0,0,0.04); margin:10px 20px;"></li> -->

        {{-- 4. OPERACIÓN Y SEGUIMIENTO --}}
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.correspondencias-procesos.index') }}">
                <i class="bi bi-eye me-2"></i> Seguimiento (Tracking)
            </a>
        </li> -->
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.comunicaciones-salida.index') }}">
                <i class="bi bi-send-check me-2"></i> Comunicaciones de Salida
            </a>
        </li> -->
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.notificaciones.index') }}">
                <i class="bi bi-bell me-2"></i> Alertas y Notificaciones
            </a>
        </li> -->

        {{-- SEPARADOR --}}
        <!-- <li class="nxl-item-separator" style="height:1px; background: rgba(0,0,0,0.04); margin:10px 20px;"></li> -->

        {{-- 5. PARAMETRIZACIÓN --}}
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.plantillas.index') }}">
                <i class="bi bi-file-earmark-richtext me-2"></i> Plantillas de Texto
            </a>
        </li> -->
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.estados.index') }}">
                <i class="bi bi-gear-wide-connected me-2"></i> Estados del Sistema
            </a>
        </li> -->
        {{-- NUEVO: MEDIOS DE RECEPCIÓN --}}
        <!-- <li class="nxl-item">
            <a class="nxl-link" href="{{ route('correspondencia.medios-recepcion.index') }}">
                <i class="bi bi-mailbox2 me-2"></i> Medios de Recepción
            </a>
        </li> -->

        {{-- SEPARADOR FINAL --}}
        <!-- <li class="nxl-item-separator" style="height:1px; background: rgba(0,0,0,0.04); margin:10px 20px;"></li> -->

        {{-- ESPACIADO PARA SCROLL --}}
        <li class="nxl-item" style="height: 80px; pointer-events: none; list-style: none;"></li>
    </ul>
</li>