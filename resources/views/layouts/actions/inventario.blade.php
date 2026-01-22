<li class="nxl-item nxl-hasmenu">
    {{-- 
        ========================================================================
        MÓDULO MAESTRO: GESTIÓN DE INVENTARIO (ITAM)
        ========================================================================
        Este es el contenedor padre. Agrupa todo el ciclo de vida del activo:
        Desde que se compra (InvCompras) -> Se almacena (InvActivos) -> 
        Se asigna (InvMovimientos) -> Hasta que se daña (InvMantenimientos).
    --}}
    <a href="javascript:void(0);" class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-box-seam-fill"></i></span>
        <span class="nxl-mtext">Gestión de Inventario</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        
        {{-- 
            ********************************************************************
            1
            VISTA: TABLERO DE CONTROL (DASHBOARD)
            ********************************************************************
            Solo lectura. Aquí van las gráficas de torta y contadores.
            Objetivo: Ver cuánta plata tenemos en equipos y cuántos están dañados.
            No permite ingresar datos, es solo informativo.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-speedometer2 me-2"></i> Tablero de Activos
            </a>
        </li>

        {{-- SEPARADOR VISUAL: Inicio de la zona de Operaciones (Data Entry) --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 5px 20px;"></li>
        
        {{-- 
            ********************************************************************
            2
            BOTÓN: REGISTRAR COMPRA (EL INICIO DEL FLUJO)
            ********************************************************************
            UML FASE 1: Ingreso de Facturas.
            TABLAS: Escribe en 'InvCompras' (Cabecera) y 'InvDetalleCompras' (Items).
            
            ¡¡ OJO !! LÓGICA CRÍTICA:
            Al guardar este formulario, se dispara el Servicio que crea AUTOMÁTICAMENTE 
            las filas en la tabla 'InvActivos' según la cantidad (campo 'cantidades').
            Si no se registra la compra aquí, los activos no existen.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-cart-plus me-2"></i> Registrar Compra
            </a>
        </li>

        {{-- 
            ********************************************************************
            3
            BOTÓN: CREAR ACTA / MOVIMIENTO (ASIGNACIONES)
            ********************************************************************
            UML FASE 4: Entrega a Funcionarios.
            TABLAS: 
            1. 'InvMovimientos': Guarda el PDF del Acta una sola vez.
            2. 'InvMovimientosDetalle': Relaciona qué equipos se lleva la persona.
            
            ACCIÓN:
            Esto cambia el estado del activo a "ASIGNADO" y actualiza la columna 
            'id_ultimo_usuario_asignado' en la tabla maestra para búsquedas rápidas.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-file-earmark-arrow-up me-2"></i> Crear Acta / Movimiento
            </a>
        </li>

        {{-- 
            ********************************************************************
            4
            BOTÓN: REGISTRAR MANTENIMIENTO (SOPORTE TÉCNICO)
            ********************************************************************
            UML: Tabla independiente 'InvMantenimientos'.
            USO: Aquí registramos cuando toca pagarle a un técnico externo.
            OBJETIVO: Llevar el control de GASTOS ($). Si no hay costo, no es 
            necesario registrarlo aquí (se puede usar un movimiento simple).
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);" style="color: var(--primary-indigo); font-weight: 600;">
                <i class="bi bi-wrench-adjustable me-2"></i> Registrar Mantenimiento
            </a>
        </li>

        {{-- SEPARADOR VISUAL: Inicio de la zona de Consultas y Edición --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 
            ********************************************************************
            5
            BOTÓN: ALMACÉN DE ACTIVOS (LA TABLA MAESTRA)
            ********************************************************************
            UML: Tabla 'InvActivos'.
            QUÉ SE HACE AQUÍ:
            1. Buscar equipos por serial o placa.
            2. Editar el registro para ponerle el Serial real (después de la compra automática).
            3. Subir la Hoja de Vida (PDF técnico).
            4. Ver rápidamente quién tiene el equipo (sin ir al historial).
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-laptop me-2"></i> Almacén de Activos
            </a>
        </li>

        {{-- 
            ********************************************************************
            6
            BOTÓN: HISTORIAL DE FACTURAS (ARCHIVO)
            ********************************************************************
            UML: Consulta sobre 'InvCompras'.
            USO: Es un archivo muerto/vivo. Sirve para descargar el PDF de la factura
            o revisar garantías de compra cuando Contabilidad lo pida.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-receipt me-2"></i> Historial de Facturas
            </a>
        </li>

        {{-- 
            ********************************************************************
            7
            BOTÓN: BITÁCORA DE MOVIMIENTOS (TRAZABILIDAD)
            ********************************************************************
            UML: Consulta sobre 'InvMovimientos'.
            USO: Auditoría de personal.
            Aquí busco por "Nombre de Empleado" y me salen todas las actas (PDFs)
            que ha firmado en su historia. Vital para Paz y Salvos.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-arrow-left-right me-2"></i> Bitácora de Movimientos
            </a>
        </li>

        {{-- SEPARADOR VISUAL: Inicio de la zona de Reportes e Inteligencia --}}
        <li class="nxl-item-separator" style="height: 1px; background: rgba(0,0,0,0.04); margin: 10px 20px;"></li>

        {{-- 
            ********************************************************************
            8
            BOTÓN: HISTORIAL TÉCNICO (GASTOS ACUMULADOS)
            ********************************************************************
            UML: Sumatoria de 'InvMantenimientos'.
            USO: Reporte financiero para la gerencia.
            Muestra qué equipos han salido defectuosos y cuánto hemos gastado
            en reparaciones este año.
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-clipboard-pulse me-2"></i> Historial Técnico
            </a>
        </li>

        {{-- 
            ********************************************************************
            9
            BOTÓN: ALERTAS Y GARANTÍAS (PREVENCIÓN)
            ********************************************************************
            UML: Filtro automático sobre 'InvActivos'.
            LÓGICA: Compara la fecha actual vs campo 'fecha_fin_garantia'.
            VISUAL: Debe mostrar un badge rojo si hay equipos por vencer garantía
            en los próximos 30 días. ¡Importante revisar a diario!
        --}}
        <li class="nxl-item">
            <a class="nxl-link" href="javascript:void(0);">
                <i class="bi bi-exclamation-triangle me-2"></i> Alertas y Garantías
            </a>
        </li>

        {{-- 
            ====================================================================
            ¡¡¡ ZONA DE TRAMPA PARA EL SCROLL (NO BORRAR) !!!
            ====================================================================
            El sidebar tiene un bug visual y se come el último ítem.
            Estos dos LI vacíos fuerzan el scroll hacia abajo para que el botón
            de "Alertas" siempre sea visible y clicable.
        --}}
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>
        <li class="nxl-item" style="height: 40px; pointer-events: none; list-style: none;"></li>

    </ul>
</li>