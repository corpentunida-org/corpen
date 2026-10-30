{{-- ============================================================================
     RSV · ADMIN · AUDITORÍAS · INFORMES · INF-v1-3
     Parcial: resources/views/rsv/admin/partials/auditorias/informes.blade.php

     · REENTREGA DEFINITIVA (INF-v1-3): reconstruida con el patrón EXACTO
       del ejemplo canónico BLQ-v1-2 (partials/calendario/bloqueos.blade.php
       · diseño TIP-v1.3-DBG). Correcciones frente a INF-v1-DBG:
         1) El LOG de debug vuelve a viajar DENTRO del fragmento: el toggle
            y el panel son los PRIMEROS hijos de #sheet-informes (nacen y
            mueren con cada copia del refresco AJAX, igual que en
            pasarelas) · unicarDbg() deja UN solo toggle/panel por página ·
            nada se cuelga de document.body.
         2) El bloque de script vive FUERA del root (igual que el ejemplo) y el
            guard usa la convención de la familia window.__jsInformesListo
            (delegación en document con removeEventListener + re-add).
         3) TABLA 100% Blade: las filas se renderizan EN SERVIDOR con el
            bucle de la familia (funciona incluso sin JavaScript; antes la
            tabla quedaba "Cargando eventos…" si el script no llegaba).
            Los filtros, KPIs y el CSV se resuelven EN CLIENTE sobre esas
            filas (igual que el buscador de pasarelas).
         4) (INF-v1-3) COMPATIBILIDAD DE DATOS: además de $informes, ahora
            acepta $auditoria (la variable que ya carga el controlador para
            la pestaña Audit Logs) antes de caer a la autoconsulta.
     · INFORME DE BITÁCORA (solo lectura · rsv_audit_logs es inmutable):
         - AUTOABASTECIDA: si el controlador no pasa $informes (ni
           $auditoria ni $auditorias ni $logs), consulta sola la bitácora
           (últimos 500 eventos) detectando las columnas reales con el
           listador de columnas del esquema (sirve con modelos Eloquent,
           filas de DB::table y arrays).
         - KPIs: eventos del filtro · eventos de hoy · acciones de
           escritura (altas·cambios·bajas) · usuarios distintos.
         - Filtros: búsqueda libre, tipo de acción (crear/actualizar/
           eliminar/sesiones/otros) y rango de fechas desde/hasta.
         - Tabla: fecha/hora, usuario, acción con badge por tipo, módulo,
           IP y detalle (truncado con tooltip).
         - Exportar CSV: filas del filtro actual, BOM UTF-8 y separador
           ";" (abre directo en Excel es-ES). Cero rutas nuevas.
         - Refrescar: SOLO si existe la ruta de fragmento (se prueban
           rsv.admin.auditorias.informes.fragmento y
           rsv.admin.auditorias.fragmento); reemplaza TODAS las copias de
           #sheet-informes y re-aplica filtros.
     · PANEL DE DEBUG (diseño TIP-v1.3 aprobado): pestaña terminal "DBG" de
       16px fija SIEMPRE en la esquina inferior-derecha (right:0 · bottom:0,
       radius 3px solo superior-izquierdo) con LED de estado (verde = ok ·
       rojo pulsante + conteo = errores) y panel cajón de 380px alineado al
       mismo borde. La regla exacta de distancias está documentada como
       "SPEC DE DISTANCIAS" dentro del CSS, y el panel esquiva a los toggles
       hermanos de las otras vistas midiendo píxeles reales
       (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Telemetría que reporta: eventos cargados, tabla usada, autoconsulta,
       rango temporal, ruta de fragmento, filtros aplicados, exportación
       CSV, refresco del fragmento y errores de red/servidor.
     · ESTÉTICA: sistema "reservas-soft" idéntico a la familia (variables
       CSS, tipografía, tabla, btn-mini/btn-green, count-badge, xls-search).
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

@php
    /* ── Datos: solo se autoconsulta si el controlador NO pasó las variables ── */
    $infErrores    = [];
    $infAutoLogs   = false;
    $infTablaUsada = null;

    if (!isset($informes) || $informes === null) {
        $informes = isset($auditoria) ? $auditoria : (isset($auditorias) ? $auditorias : (isset($logs) ? $logs : collect()));
    }
    if (count($informes) === 0) {
        foreach (['rsv_audit_logs', 'audit_logs'] as $infTabla) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable($infTabla)) { continue; }
                $infCols = \Illuminate\Support\Facades\Schema::getColumnListing($infTabla);
                $infQ = \Illuminate\Support\Facades\DB::table($infTabla);
                if (in_array('id', $infCols)) { $infQ->orderByDesc('id'); }
                elseif (in_array('created_at', $infCols)) { $infQ->orderByDesc('created_at'); }
                $informes = $infQ->limit(500)->get();
                $infAutoLogs   = true;
                $infTablaUsada = $infTabla;
                break; // la consulta funcionó: tabla válida
            } catch (\Throwable $infE) {
                $infErrores[] = $infTabla . ': ' . $infE->getMessage();
            }
        }
    }

    /* ── Getter tolerante (sirve con objetos Eloquent, stdClass y arrays) ── */
    $infG = function ($infR, array $infKeys) {
        foreach ($infKeys as $infK) {
            if (is_object($infR) && isset($infR->{$infK})) { return $infR->{$infK}; }
            if (is_array($infR) && array_key_exists($infK, $infR)) { return $infR[$infK]; }
        }
        return null;
    };

    /* ── Normalización al shape uniforme de las filas Blade ── */
    $infItems = collect($informes)->values()->map(function ($infR) use ($infG) {
        $infF = $infG($infR, ['created_at', 'fecha', 'fecha_hora']);
        try { $infC = $infF ? \Illuminate\Support\Carbon::parse($infF) : null; }
        catch (\Throwable $infE) { $infC = null; }
        return [
            'id'      => (string) ($infG($infR, ['id']) ?? ''),
            'fecha'   => $infC ? $infC->format('Y-m-d') : '',
            'cuando'  => $infC ? $infC->format('d/m/Y H:i') : '—',
            'usuario' => (string) ($infG($infR, ['user_name', 'usuario', 'name', 'user_id']) ?? 'sistema'),
            'accion'  => (string) ($infG($infR, ['accion', 'action', 'evento']) ?? 'otro'),
            'modulo'  => (string) ($infG($infR, ['modulo', 'module', 'modelo', 'model_type', 'tabla']) ?? '—'),
            'ip'      => (string) ($infG($infR, ['ip', 'ip_address']) ?? '—'),
            'detalle' => trim((string) ($infG($infR, ['descripcion', 'description', 'detalles', 'detalle', 'data']) ?? '')),
        ];
    });

    /* ── Clasificación de la acción (badge por tipo) ── */
    $infTipo = function ($infA) {
        $infA = strtolower(trim((string) $infA));
        if (preg_match('/crea|regis|alta|nuev|store|insert/', $infA)) { return 'crear'; }
        if (preg_match('/actu|edi|modi|updat|camb/', $infA)) { return 'actualizar'; }
        if (preg_match('/elim|borr|baja|delet|destroy/', $infA)) { return 'eliminar'; }
        if (preg_match('/login|logout|sesi|auth|acces/', $infA)) { return 'login'; }
        return 'otro';
    };
    $infClase = ['crear' => 'inf-b-crear', 'actualizar' => 'inf-b-actualizar', 'eliminar' => 'inf-b-eliminar', 'login' => 'inf-b-login', 'otro' => ''];
    $infTexto = ['crear' => 'Crear', 'actualizar' => 'Actualizar', 'eliminar' => 'Eliminar', 'login' => 'Sesión', 'otro' => 'Otro'];

    /* ── KPIs iniciales (servidor · el cliente los re-calcula al filtrar) ── */
    $infHoy    = now()->format('Y-m-d');
    $infKTotal = $infItems->count();
    $infKHoy   = $infItems->where('fecha', $infHoy)->count();
    $infKEsc   = $infItems->whereIn('accion', ['crear', 'actualizar', 'eliminar'])->count();
    $infKUsr   = $infItems->pluck('usuario')->unique()->count();

    /* ── Rutas con route() (null si no existen: la acción se deshabilita sola) ── */
    $infFragmento = \Illuminate\Support\Facades\Route::has('rsv.admin.auditorias.informes.fragmento')
        ? route('rsv.admin.auditorias.informes.fragmento')
        : (\Illuminate\Support\Facades\Route::has('rsv.admin.auditorias.fragmento') ? route('rsv.admin.auditorias.fragmento') : null);

    /* ── Colector del panel de debug (INF-v1-3) · via al panel vía la directiva json de Blade ── */
    $infFechas = $infItems->pluck('fecha')->filter();
    $infDbg = [
        'version'       => 'INF-v1-3',
        'vista'         => 'partials/auditorias/informes.blade.php',
        'render'        => now()->format('H:i:s'),
        'auto_consulta' => $infAutoLogs,
        'tabla'         => $infTablaUsada ?: 'NO detectada',
        'eventos'       => $infKTotal,
        'usuarios'      => $infKUsr,
        'rango'         => ($infFechas->count() ? ($infFechas->min() . ' → ' . $infFechas->max()) : '—'),
        'rutas'         => ['fragmento' => $infFragmento ?: 'NO definida'],
        'errores'       => $infErrores,
    ];
@endphp

<div id="sheet-informes">

    {{-- INF-v1-3 · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3 · vive y muere con esta copia · unicarDbg deja un solo par por página) --}}
    <div id="infDbgToggle" title="Debug Informes (INF-v1-3)"><span class="inf-dbg-led" id="infDbgLed"></span>DBG<span class="inf-dbg-badge" id="infDbgBadge">0</span></div>
    <div id="infDbgPanel" role="dialog" aria-label="Panel de debug de Informes de Auditoría">
        <div class="inf-dbg-header" id="infDbgHeader">
            <span>DEBUG · Informes de Auditoría · INF-v1-3</span>
            <span class="inf-dbg-controls">
                <button type="button" class="inf-dbg-btn" id="infDbgCopy">Copiar</button>
                <button type="button" class="inf-dbg-btn" id="infDbgClear">Limpiar</button>
                <button type="button" class="inf-dbg-btn" id="infDbgClose">✕</button>
            </span>
        </div>
        <div id="infDbgBody"></div>
    </div>

    <div class="reservas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
            .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
            .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
            .reservas-soft .table tfoot > tr > td { font-size: 8.5px; font-weight: 700; color: var(--ink-2); background-color: var(--head); border-top: 2px solid var(--grid-2) !important; border-bottom: 0; white-space: nowrap; }
            .reservas-soft .table tfoot .st-total-label { color: var(--accent); letter-spacing: .03em; }
            .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
            .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
            .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
            .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
            .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
            .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }

            /* ── Extras inf- (KPIs, filtros, badges de acción, detalle) ── */
            .reservas-soft .inf-kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 10px; }
            @media (max-width: 575px) { .reservas-soft .inf-kpis { grid-template-columns: repeat(2, 1fr); } }
            .reservas-soft .inf-kpi { border: 1px solid var(--grid-2); border-radius: 5px; padding: 7px 10px; background: #fff; }
            .reservas-soft .inf-kpi b { display: block; font-size: 15px; line-height: 1.2; color: var(--ink-2); }
            .reservas-soft .inf-kpi span { display: block; font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-3); margin-top: 2px; }
            .reservas-soft .inf-kpi.inf-k-hoy b { color: var(--accent); }
            .reservas-soft .inf-kpi.inf-k-esc b { color: #b36b64; }
            .reservas-soft .inf-kpi.inf-k-usr b { color: #4a6fa5; }
            .reservas-soft .inf-filtros { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin-bottom: 8px; }
            .reservas-soft .inf-sel { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 6px; box-shadow: none !important; outline: 0; font-family: inherit; }
            .reservas-soft .inf-sel:focus { border-color: #c8d6cd; }
            .reservas-soft .inf-badge { display: inline-block; font-size: 7.5px; font-weight: 700; letter-spacing: .04em; padding: 1px 6px; border-radius: 8px; border: 1px solid var(--grid-2); background: var(--head); color: var(--ink-2); }
            .reservas-soft .inf-b-crear      { background: var(--accent-bg); border-color: var(--accent-bd); color: var(--accent); }
            .reservas-soft .inf-b-actualizar { background: #fbf8ee; border-color: #eee5cd; color: #8f7a45; }
            .reservas-soft .inf-b-eliminar   { background: #fdf5f4; border-color: #f0dcd8; color: #b36b64; }
            .reservas-soft .inf-b-login      { background: #f0f4fa; border-color: #d8e2ee; color: #4a6fa5; }
            .reservas-soft .inf-detalle { max-width: 260px; overflow: hidden; text-overflow: ellipsis; }

            /* ═════════ SPEC DE DISTANCIAS · INF-v1-3 (tab terminal TIP-v1.3) ═════════
               TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la esquina
                           inferior-derecha (nunca sube: con 16px de alto cabe bajo
                           cualquier otro toggle sin tocarlo)
                         · altura fija 16px · padding lateral 8px
                         · radius 3px solo sup-izq · borde 1px arriba/izq
                         · monospace 8px · LED 5px (verde ok / rojo pulsante = error)
               PANEL     · right:0px · bottom = max(20, vecino + 4px) medido en px
                           reales con getBoundingClientRect (los toggles ocultos 0×0
                           no cuentan) · (20 = 16 de tab + 4 de aire mínimo)
                         · ancho 380px · radius 6px 0 0 0 (cajón pegado al borde)
               UNICIDAD  · este parcial puede renderizarse 2+ veces en la página: los
                           nodos nacen y mueren con cada copia y unicarDbg() deja UN
                           solo toggle/panel (los duplicados se eliminan al vuelo). */
            #infDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #infDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .inf-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .inf-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: infDbgBlink 1s steps(2,start) infinite; }
            @keyframes infDbgBlink { 50% { opacity: .35; } }
            .inf-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #infDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
            .inf-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .inf-dbg-controls { display: flex; gap: 4px; }
            .inf-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .inf-dbg-btn:hover { background: #475569; }
            #infDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .inf-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .inf-dbg-time { color: #64748b; margin-right: 4px; }
            .inf-dbg-info { color: #93c5fd; } .inf-dbg-ok { color: #86efac; }
            .inf-dbg-warn { color: #fde68a; } .inf-dbg-err { color: #fca5a5; }
            .inf-dbg-net { color: #c4b5fd; } .inf-dbg-debug { color: #94a3b8; }
        </style>

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-file-earmark-bar-graph me-1" style="font-size: 8px; color: #5d8a70;"></i> Informes de Auditoría
                <span class="badge rounded-pill ms-1" id="infChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">INF-v1-3</span>
            </h6>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span class="count-badge" title="Eventos cargados (máximo 500)">
                    <b id="infCount">{{ $infKTotal }}</b> evento(s)
                </span>
                <input type="text" id="infSearch" class="xls-search" placeholder="Buscar usuario, acción, módulo, detalle…" autocomplete="off" style="width: 190px;">
                @if($infFragmento)
                    <button type="button" class="btn-mini" id="infBtnRefrescar">
                        <i class="bi bi-arrow-clockwise" style="font-size: 8px;"></i> Refrescar
                    </button>
                @endif
                <button type="button" class="btn-green" id="infBtnCsv">
                    <i class="bi bi-download" style="font-size: 8px;"></i> Exportar CSV
                </button>
            </div>
        </div>

        <!-- KPIs (conjunto FILTRADO · valores iniciales del servidor) -->
        <div class="inf-kpis">
            <div class="inf-kpi"><b id="infKpiTotal">{{ $infKTotal }}</b><span>Eventos (filtro)</span></div>
            <div class="inf-kpi inf-k-hoy"><b id="infKpiHoy">{{ $infKHoy }}</b><span>Eventos de hoy</span></div>
            <div class="inf-kpi inf-k-esc"><b id="infKpiEsc">{{ $infKEsc }}</b><span>Escritura (altas·cambios·bajas)</span></div>
            <div class="inf-kpi inf-k-usr"><b id="infKpiUsr">{{ $infKUsr }}</b><span>Usuarios distintos</span></div>
        </div>

        <!-- FILTROS -->
        <div class="inf-filtros">
            <select id="infFiltroTipo" class="inf-sel" title="Tipo de acción">
                <option value="">Todas las acciones</option>
                <option value="crear">Crear</option>
                <option value="actualizar">Actualizar</option>
                <option value="eliminar">Eliminar</option>
                <option value="login">Sesiones</option>
                <option value="otro">Otros</option>
            </select>
            <input type="date" id="infDesde" class="inf-sel" title="Desde">
            <input type="date" id="infHasta" class="inf-sel" title="Hasta">
            <button type="button" class="btn-mini" id="infBtnLimpiar">Limpiar filtros</button>
        </div>

        <!-- TABLA (filas renderizadas en servidor: funciona incluso sin JS) -->
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 720px;">
                <thead>
                    <tr>
                        <th style="width: 112px;">Fecha / Hora</th>
                        <th>Usuario</th>
                        <th style="width: 86px;">Acción</th>
                        <th>Módulo</th>
                        <th style="width: 100px;">IP</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infItems as $inf)
                        @php
                            $infT = $infTipo($inf['accion']);
                            $infHay = mb_strtolower(preg_replace('/\s+/', ' ', $inf['usuario'] . ' ' . $inf['accion'] . ' ' . $inf['modulo'] . ' ' . $inf['detalle']));
                        @endphp
                        <tr data-inf-search="{{ $infHay }}" data-accion="{{ $infT }}" data-fecha="{{ $inf['fecha'] }}" data-usuario="{{ mb_strtolower($inf['usuario']) }}">
                            <td class="strong">{{ $inf['cuando'] }}</td>
                            <td>{{ $inf['usuario'] }}</td>
                            <td><span class="inf-badge {{ $infClase[$infT] }}">{{ $infTexto[$infT] }}</span></td>
                            <td class="sub">{{ $inf['modulo'] }}</td>
                            <td class="sub">{{ $inf['ip'] }}</td>
                            <td class="sub"><div class="inf-detalle" title="{{ $inf['detalle'] }}">{{ $inf['detalle'] !== '' ? $inf['detalle'] : '—' }}</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-journal-text me-1"></i> Sin eventos de auditoría registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="st-total-label">
                            <i class="bi bi-calculator me-1" style="font-size: 8px;"></i> TOTAL · {{ $infKTotal }} evento(s)
                        </td>
                        <td></td><td></td><td></td><td></td><td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

</div>

<script>
(function () {
    'use strict';
    if (window.__jsInformesListo) return;
    window.__jsInformesListo = true;

    var URL_FRAGMENTO = '{{ $infFragmento ?? "" }}';

    /* ═══ 0) PANEL DE DEBUG LOCAL INF-v1-3 (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de este fragmento: los nodos son HTML de la hoja (viven
       DENTRO de #sheet-informes y renacen con cada refresco) y el respaldo
       solo re-crea DENTRO de la primera hoja. Cero document.body.
       Con copias múltiples del parcial, unicarDbg() deja un solo par. */
    var DBG = (window.InfDbg && window.InfDbg.alive && window.InfDbg.alive())
        ? window.InfDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven dentro de la primera #sheet-informes y renacen con el
                   HTML del refresco), se re-crean DENTRO de esa hoja. */
                var rootDbg = document.querySelector('#sheet-informes');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('infDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'infDbgToggle';
                    t.title = 'Debug Informes (INF-v1-3)';
                    t.innerHTML = '<span class="inf-dbg-led" id="infDbgLed"></span>DBG<span class="inf-dbg-badge" id="infDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('infDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'infDbgPanel';
                    p.innerHTML = '<div class="inf-dbg-header" id="infDbgHeader">' +
                        '<span>DEBUG · Informes de Auditoría · INF-v1-3</span>' +
                        '<span class="inf-dbg-controls">' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgCopy">Copiar</button>' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgClear">Limpiar</button>' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgClose">✕</button>' +
                        '</span></div><div id="infDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* INF-v1-3: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('infDbgToggle'), p = $('infDbgPanel');
                if (t) { t.style.bottom = '0px'; } // SIEMPRE pegada abajo
                if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; } // 16 tab + 4 aire
            }
            function hora() {
                var d = new Date();
                function f(n2) { return (n2 < 10 ? '0' : '') + n2; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('infDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'inf-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="inf-dbg-time">[' + hora() + ']</span>' +
                        '<span class="inf-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('infDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('infDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('infDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('infDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('infDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('infDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('infDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('infDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('infDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG informes INF-v1-3 · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        }).catch(function () {
                            api.log('no se pudo copiar (permisos del navegador)', 'warn');
                        });
                    } else { api.log('portapapeles no disponible en este navegador', 'warn'); }
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('infDbgToggle'); }
            };
            window.InfDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var infdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function unicarDbg() {
        /* INF-v1-3: el parcial puede renderizarse 2+ veces; el HTML de cada
           copia trae sus propios nodos DBG → se conservan los de la primera
           hoja y se eliminan los duplicados (un solo toggle/panel por página). */
        var ts = document.querySelectorAll('#infDbgToggle');
        for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
        var ps = document.querySelectorAll('#infDbgPanel');
        for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
    }

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($infDbg ?? null);
    infdbg('INF-v1-3 activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · nodos nacen y mueren con el fragmento', 'ok');
    infdbg('init · eventos=' + (SRVDBG ? SRVDBG.eventos : '?') + ' · usuarios=' + (SRVDBG ? SRVDBG.usuarios : '?') +
        ' · autoconsulta=' + (SRVDBG ? (SRVDBG.auto_consulta ? 'sí (el controlador no pasó datos)' : 'no (datos del controlador)') : '?') +
        ' · tabla=' + (SRVDBG ? SRVDBG.tabla : '?'), 'info');
    if (SRVDBG && SRVDBG.rango && SRVDBG.rango !== '—') {
        infdbg('rango temporal del dataset · ' + SRVDBG.rango, 'debug');
    }
    if (SRVDBG && SRVDBG.rutas) {
        var _rk = Object.keys(SRVDBG.rutas);
        for (var _ri = 0; _ri < _rk.length; _ri++) {
            if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                infdbg('ruta no definida: rsv.admin.auditorias…' + _rk[_ri] + ' (el botón Refrescar se oculta solo)', 'warn');
            }
        }
        infdbg('Rutas · fragmento=' + SRVDBG.rutas.fragmento, 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            infdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    /* ═══ 1) KPIs · re-calculados sobre las filas VISIBLES del filtro ═══ */
    function actualizarKpis(filas) {
        var hoy = new Date();
        var hoyStr = hoy.getFullYear() + '-' + ('0' + (hoy.getMonth() + 1)).slice(-2) + '-' + ('0' + hoy.getDate()).slice(-2);
        var total = 0, esc = 0, hoyN = 0, usrs = {};
        filas.forEach(function (tr) {
            total++;
            var acc = tr.getAttribute('data-accion') || '';
            if (acc === 'crear' || acc === 'actualizar' || acc === 'eliminar') { esc++; }
            if ((tr.getAttribute('data-fecha') || '') === hoyStr) { hoyN++; }
            var u = tr.getAttribute('data-usuario') || '';
            if (u) { usrs[u] = 1; }
        });
        var fijar = function (id, v) { var el = document.getElementById(id); if (el) { el.textContent = v; } };
        fijar('infKpiTotal', total);
        fijar('infKpiHoy', hoyN);
        fijar('infKpiEsc', esc);
        fijar('infKpiUsr', Object.keys(usrs).length);
        fijar('infCount', total);
    }

    /* ═══ 2) FILTROS (búsqueda + tipo + rango de fechas) sobre filas Blade ═══ */
    function aplicarFiltro(origen) {
        var s = document.getElementById('infSearch');
        var t = document.getElementById('infFiltroTipo');
        var d = document.getElementById('infDesde');
        var h = document.getElementById('infHasta');
        var q = (s && s.value ? s.value : '').toLowerCase().trim();
        var tipo = (t && t.value) || '';
        var desde = (d && d.value) || '';
        var hasta = (h && h.value) || '';
        var filas = [];
        document.querySelectorAll('#sheet-informes tbody tr[data-inf-search]').forEach(function (tr) {
            var hay = tr.getAttribute('data-inf-search') || '';
            var f = tr.getAttribute('data-fecha') || '';
            var ok = (!q || hay.indexOf(q) !== -1) && (!tipo || tr.getAttribute('data-accion') === tipo);
            if (ok && (desde || hasta) && !f) { ok = false; } // filas sin fecha fuera del rango
            if (ok && desde && f < desde) { ok = false; }
            if (ok && hasta && f > hasta) { ok = false; }
            tr.style.display = ok ? '' : 'none';
            if (ok) { filas.push(tr); }
        });
        actualizarKpis(filas);
        if (origen) {
            infdbg('filtro (' + origen + ') · "' + q + '" · tipo=' + (tipo || 'todas') + ' · desde=' + (desde || '—') + ' · hasta=' + (hasta || '—') + ' · ' + filas.length + ' fila(s) visibles', 'debug');
        }
    }

    function limpiarFiltros() {
        var s = document.getElementById('infSearch');
        var t = document.getElementById('infFiltroTipo');
        var d = document.getElementById('infDesde');
        var h = document.getElementById('infHasta');
        if (s) { s.value = ''; }
        if (t) { t.value = ''; }
        if (d) { d.value = ''; }
        if (h) { h.value = ''; }
        aplicarFiltro('limpiar');
    }

    /* ═══ 3) EXPORTAR CSV (filas del filtro actual · BOM UTF-8 · separador ;) ═══ */
    function exportarCsv() {
        var filas = [];
        document.querySelectorAll('#sheet-informes tbody tr[data-inf-search]').forEach(function (tr) {
            if (tr.style.display !== 'none') { filas.push(tr); }
        });
        if (!filas.length) { infdbg('exportar CSV cancelado · 0 fila(s) en el filtro', 'warn'); return; }
        var esc = function (v) {
            var s2 = String(v == null ? '' : v);
            return (s2.indexOf(';') !== -1 || s2.indexOf('"') !== -1 || s2.indexOf('\n') !== -1) ? '"' + s2.replace(/"/g, '""') + '"' : s2;
        };
        var lineas = ['Fecha/Hora;Usuario;Accion;Modulo;IP;Detalle'];
        filas.forEach(function (tr) {
            var tds = tr.querySelectorAll('td');
            var fila = [];
            for (var i = 0; i < tds.length; i++) { fila.push(esc(tds[i].textContent.trim())); }
            lineas.push(fila.join(';'));
        });
        var blob = new Blob(['\ufeff' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'informe-auditoria-' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(a);
        a.click();
        if (a.remove) { a.remove(); }
        infdbg('CSV exportado · ' + filas.length + ' fila(s) del filtro actual · BOM UTF-8 · separador ;', 'ok');
    }

    /* ═══ 4) REFRESCAR el fragmento (solo si la ruta existe) ═══ */
    function refrescar() {
        if (!URL_FRAGMENTO) { infdbg('sin ruta de fragmento · fallback reload()', 'warn'); window.location.reload(); return; }
        infdbg('refrescando fragmento · ' + URL_FRAGMENTO, 'net');
        fetch(URL_FRAGMENTO, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) { throw new Error('fragmento'); } return r.text(); })
            .then(function (html) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-informes');
                var copias = document.querySelectorAll('#sheet-informes');
                if (!nuevo || copias.length === 0) { infdbg('respuesta sin #sheet-informes · fallback reload()', 'warn'); window.location.reload(); return; }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                unicarDbg(); aplicarFiltro();
                if (window.InfDbg && window.InfDbg.ensure) { window.InfDbg.ensure(); }
                infdbg('fragmento refrescado · copias=' + copias.length + ' · dataset releído del servidor', 'net');
            })
            .catch(function () { infdbg('fallo al refrescar el fragmento · fallback reload()', 'err'); window.location.reload(); });
    }

    /* Botones del panel + acciones de la vista por DELEGACIÓN en document
       (el guard __jsInformesListo garantiza que se registran UNA sola vez;
       sobreviven a los swaps) */
    if (window.__infDbgDocHandler) { document.removeEventListener('click', window.__infDbgDocHandler); }
    window.__infDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#infDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#infDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#infDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#infDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
        if (t.closest('#infBtnLimpiar')) { limpiarFiltros(); return; }
        if (t.closest('#infBtnCsv')) { exportarCsv(); return; }
        if (t.closest('#infBtnRefrescar')) { refrescar(); return; }
    };
    document.addEventListener('click', window.__infDbgDocHandler);
    if (window.__infDbgDblHandler) { document.removeEventListener('dblclick', window.__infDbgDblHandler); }
    window.__infDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#infDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__infDbgDblHandler);

    /* Filtros por DELEGACIÓN (input para texto y fechas, change para el select) */
    if (window.__infDocHandler) { document.removeEventListener('input', window.__infDocHandler); document.removeEventListener('change', window.__infDocHandler); }
    window.__infDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.id) { return; }
        if (t.id === 'infSearch' || t.id === 'infDesde' || t.id === 'infHasta') { aplicarFiltro(); }
        else if (t.id === 'infFiltroTipo') { aplicarFiltro(); }
    };
    document.addEventListener('input', window.__infDocHandler);
    document.addEventListener('change', window.__infDocHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    function iniciar() {
        unicarDbg(); aplicarFiltro();
        if (window.InfDbg && window.InfDbg.ensure) { window.InfDbg.ensure(); }
        infdbg('informes listos · KPIs y filtros operativos · exportación CSV activa', 'ok');
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>