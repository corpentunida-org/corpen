{{-- ============================================================================
     RSV · ADMIN · AUDITORÍAS · INFORMES · INF-v2.0-DBG
     Parcial: resources/views/rsv/admin/partials/auditorias/informes.blade.php
     ============================================================================ --}}

@php
    $infErrores    = [];
    $infAutoLogs   = false;
    $infTablaUsada = 'rsv_audit_logs';
    $inmIdActual   = $inmuebleId ?? request('inmueble_id', 'todos') ?: 'todos';

    // 1. Obtener hasta 500 registros reales de rsv_audit_logs con relación user
    $coleccionCruda = collect();

    if (isset($informes) && $informes instanceof \Illuminate\Support\Collection && $informes->count() > 15) {
        $coleccionCruda = $informes;
    } else {
        try {
            if (class_exists(\App\Models\Rsv\AuditLog::class) && \Illuminate\Support\Facades\Schema::hasTable('rsv_audit_logs')) {
                $coleccionCruda = \App\Models\Rsv\AuditLog::with('user')
                    ->orderByDesc('id')
                    ->limit(500)
                    ->get();
                $infAutoLogs   = true;
                $infTablaUsada = 'rsv_audit_logs (Eloquent + user)';
            } else {
                foreach (['rsv_audit_logs', 'audit_logs'] as $tTry) {
                    if (\Illuminate\Support\Facades\Schema::hasTable($tTry)) {
                        $coleccionCruda = collect(\Illuminate\Support\Facades\DB::table($tTry)->orderByDesc('id')->limit(500)->get());
                        $infAutoLogs   = true;
                        $infTablaUsada = $tTry . ' (DB::table)';
                        break;
                    }
                }
            }
        } catch (\Throwable $infE) {
            $infErrores[] = 'Consulta AuditLog: ' . $infE->getMessage();
            if (isset($auditoria)) {
                $coleccionCruda = ($auditoria instanceof \Illuminate\Pagination\AbstractPaginator)
                    ? collect($auditoria->items())
                    : collect($auditoria);
            }
        }
    }

    // Mapa auxiliar de usuarios por si alguna fila viene de DB::table sin eager loading
    $mapaUsuarios = [];
    try {
        $idsUsr = $coleccionCruda->map(function ($r) {
            return is_object($r) ? ($r->id_user ?? $r->user_id ?? null) : ($r['id_user'] ?? $r['user_id'] ?? null);
        })->filter()->unique()->values()->all();

        if (!empty($idsUsr) && class_exists(\App\Models\User::class)) {
            $tieneNit = \Illuminate\Support\Facades\Schema::hasColumn('users', 'nit');
            $colsU = $tieneNit ? ['id', 'name', 'nit', 'email'] : ['id', 'name', 'email'];
            foreach (\App\Models\User::whereIn('id', $idsUsr)->get($colsU) as $uObj) {
                $mapaUsuarios[$uObj->id] = $uObj;
            }
        }
    } catch (\Throwable $eU) {}

    // Clasificador canónico de acción
    $infClasificarAccion = function (string $accRaw): string {
        $a = mb_strtolower(trim($accRaw));
        if (preg_match('/crea|regis|alta|nuev|store|insert/', $a)) return 'crear';
        if (preg_match('/actu|edi|modi|updat|camb/', $a)) return 'actualizar';
        if (preg_match('/elim|borr|baja|delet|destroy/', $a)) return 'eliminar';
        if (preg_match('/view|vist|frag|portal|dash|calend|ver/', $a)) return 'vista';
        if (preg_match('/search|busc|filt/', $a)) return 'busqueda';
        if (preg_match('/login|logout|sesi|auth|acces/', $a)) return 'login';
        return 'otro';
    };

    // Clasificador de origen/canal (Cliente vs Admin vs Modelos BD)
    $infClasificarCanal = function (string $tablaRaw, string $accRaw, array $nuevos): string {
        $t = mb_strtolower(trim($tablaRaw));
        $m = mb_strtolower((string) ($nuevos['modulo'] ?? $nuevos['_meta_url'] ?? ''));
        if ($t === 'portal_cliente' || str_contains($m, 'cliente')) return 'cliente';
        if ($t === 'panel_admin' || str_contains($m, 'admin')) return 'admin';
        return 'bd';
    };

    // Normalización de cada fila de auditoría
    $infItems = $coleccionCruda->values()->map(function ($r) use ($mapaUsuarios, $infClasificarAccion, $infClasificarCanal) {
        $esObj = is_object($r);
        $get = function (array $keys) use ($r, $esObj) {
            foreach ($keys as $k) {
                if ($esObj && isset($r->{$k})) return $r->{$k};
                if (is_array($r) && array_key_exists($k, $r)) return $r[$k];
            }
            return null;
        };

        $id         = (string) ($get(['id']) ?? '');
        $idUser     = $get(['id_user', 'user_id']);
        $tabla      = (string) ($get(['tabla_afectada', 'modulo', 'module', 'tabla']) ?? 'sistema');
        $regId      = $get(['registro_id', 'model_id']) ?? 0;
        $accionRaw  = (string) ($get(['accion', 'action', 'evento']) ?? 'INFO');
        $ip         = (string) ($get(['ip_address', 'ip']) ?? '127.0.0.1');
        $rawCreated = $get(['created_at', 'fecha', 'fecha_hora']);

        try {
            $carb = $rawCreated ? \Illuminate\Support\Carbon::parse($rawCreated) : null;
        } catch (\Throwable $eC) {
            $carb = null;
        }

        $antRaw = $get(['datos_anteriores']);
        $nueRaw = $get(['datos_nuevos']);
        $arrAnt = is_array($antRaw) ? $antRaw : (is_string($antRaw) && $antRaw !== '' ? (json_decode($antRaw, true) ?: []) : []);
        $arrNue = is_array($nueRaw) ? $nueRaw : (is_string($nueRaw) && $nueRaw !== '' ? (json_decode($nueRaw, true) ?: []) : []);

        // Resolver nombre del usuario actor
        $userRel = ($esObj && isset($r->user) && $r->user) ? $r->user : ($idUser && isset($mapaUsuarios[$idUser]) ? $mapaUsuarios[$idUser] : null);
        if ($userRel) {
            $usuarioNom = $userRel->name . (!empty($userRel->nit) ? ' (' . $userRel->nit . ')' : '');
        } elseif (!empty($arrNue['_meta_actor'])) {
            $usuarioNom = (string) $arrNue['_meta_actor'];
        } elseif ($idUser) {
            $usuarioNom = 'Usuario #' . $idUser;
        } else {
            $usuarioNom = (string) ($get(['user_name', 'usuario', 'name']) ?? 'Sistema');
        }

        // Construir resumen legible del detalle
        $partesDetalle = [];
        if (!empty($arrNue['modulo'])) {
            $partesDetalle[] = $arrNue['modulo'];
        }
        if (!empty($arrNue['termino'])) {
            $partesDetalle[] = 'Búsqueda: "' . $arrNue['termino'] . '"';
        }

        // Extraer campos de negocio sin las llaves internas _meta_*
        $negocioNue = array_diff_key($arrNue, array_flip(['_meta_actor', '_meta_url', '_meta_metodo', '_meta_ajax', '_meta_agent', 'modulo', 'updated_at', 'created_at']));
        $negocioAnt = array_diff_key($arrAnt, array_flip(['updated_at', 'created_at']));

        if (!empty($negocioAnt) && !empty($negocioNue)) {
            $diffs = [];
            foreach ($negocioNue as $kCampo => $vNue) {
                $vAnt = $negocioAnt[$kCampo] ?? '∅';
                $strAnt = is_scalar($vAnt) ? (string) $vAnt : json_encode($vAnt, JSON_UNESCAPED_UNICODE);
                $strNue = is_scalar($vNue) ? (string) $vNue : json_encode($vNue, JSON_UNESCAPED_UNICODE);
                $diffs[] = $kCampo . ': ' . \Illuminate\Support\Str::limit($strAnt, 24) . ' → ' . \Illuminate\Support\Str::limit($strNue, 28);
                if (count($diffs) >= 4) break;
            }
            if (!empty($diffs)) {
                $partesDetalle[] = implode(' | ', $diffs);
            }
        } elseif (!empty($negocioNue)) {
            $ares = [];
            foreach ($negocioNue as $kCampo => $vNue) {
                if (is_null($vNue) || $vNue === '') continue;
                $strNue = is_scalar($vNue) ? (string) $vNue : json_encode($vNue, JSON_UNESCAPED_UNICODE);
                $ares[] = $kCampo . '=' . \Illuminate\Support\Str::limit($strNue, 28);
                if (count($ares) >= 4) break;
            }
            if (!empty($ares)) {
                $partesDetalle[] = implode(' · ', $ares);
            }
        }

        $detalleTexto = !empty($partesDetalle)
            ? implode(' — ', $partesDetalle)
            : trim((string) ($get(['descripcion', 'description', 'detalles', 'detalle']) ?? 'Registro de auditoría #' . $id));

        $tipoAcc = $infClasificarAccion($accionRaw);
        $canal   = $infClasificarCanal($tabla, $accionRaw, $arrNue);

        return [
            'id'           => $id,
            'fecha'        => $carb ? $carb->format('Y-m-d') : '',
            'cuando'       => $carb ? $carb->format('d/m/Y H:i:s') : '—',
            'usuario'      => $usuarioNom,
            'id_user'      => $idUser ? (string) $idUser : '',
            'accion_raw'   => mb_strtoupper($accionRaw),
            'accion_tipo'  => $tipoAcc,
            'canal'        => $canal,
            'modulo'       => $tabla,
            'registro_id'  => (int) $regId,
            'ip'           => $ip,
            'detalle'      => $detalleTexto,
            'json_ant'     => !empty($arrAnt) ? json_encode($arrAnt, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '',
            'json_nue'     => !empty($arrNue) ? json_encode($arrNue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '',
            'tiene_json'   => (!empty($arrAnt) || !empty($arrNue)),
        ];
    });

    $infClase = [
        'crear'      => 'inf-b-crear',
        'actualizar' => 'inf-b-actualizar',
        'eliminar'   => 'inf-b-eliminar',
        'vista'      => 'inf-b-vista',
        'busqueda'   => 'inf-b-busqueda',
        'login'      => 'inf-b-login',
        'otro'       => '',
    ];
    $infTexto = [
        'crear'      => 'CREAR',
        'actualizar' => 'EDITAR',
        'eliminar'   => 'ELIMINAR',
        'vista'      => 'VISTA',
        'busqueda'   => 'BÚSQUEDA',
        'login'      => 'SESIÓN',
        'otro'       => 'EVENTO',
    ];

    // KPIs iniciales
    $infHoy      = now()->format('Y-m-d');
    $infKTotal   = $infItems->count();
    $infKHoy     = $infItems->where('fecha', $infHoy)->count();
    $infKEsc     = $infItems->whereIn('accion_tipo', ['crear', 'actualizar', 'eliminar'])->count();
    $infKVistas  = $infItems->whereIn('accion_tipo', ['vista', 'busqueda'])->count();
    $infKUsr     = $infItems->pluck('usuario')->unique()->count();

    $cntCanalAll     = $infKTotal;
    $cntCanalCliente = $infItems->where('canal', 'cliente')->count();
    $cntCanalAdmin   = $infItems->where('canal', 'admin')->count();
    $cntCanalBd      = $infItems->where('canal', 'bd')->count();

    // Ruta de refresco AJAX (con fallback al dashboard sin recargar navegador)
    $infFragmento = '';
    foreach ([
        'rsv.admin.auditorias.informes.fragmento',
        'rsv.admin.auditoria.informes.fragmento',
        'rsv.admin.auditorias.fragmento',
        'rsv.admin.dashboard',
    ] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $infFragmento = route($rn, ['inmueble_id' => $inmIdActual]);
            break;
        }
    }

    $infFechas = $infItems->pluck('fecha')->filter();
    $infDbg = [
        'version'       => 'INF-v2.0-DBG',
        'vista'         => 'partials/auditorias/informes.blade.php',
        'render'        => now()->format('H:i:s'),
        'auto_consulta' => $infAutoLogs,
        'tabla'         => $infTablaUsada,
        'eventos'       => $infKTotal,
        'hoy'           => $infKHoy,
        'crud'          => $infKEsc,
        'vistas'        => $infKVistas,
        'usuarios'      => $infKUsr,
        'canales'       => ['cliente' => $cntCanalCliente, 'admin' => $cntCanalAdmin, 'bd' => $cntCanalBd],
        'rango'         => ($infFechas->count() ? ($infFechas->min() . ' → ' . $infFechas->max()) : '—'),
        'rutas'         => ['fragmento' => $infFragmento ?: 'fallback URL actual'],
        'errores'       => $infErrores,
    ];
@endphp

<div id="sheet-informes"
     data-frag="{{ $infFragmento }}"
     data-inmueble-id="{{ $inmIdActual }}"
     data-total="{{ $infKTotal }}">

    {{-- INF-v2.0-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
    <div id="infDbgToggle" title="Debug Informes (INF-v2.0-DBG)">
        <span class="inf-dbg-led" id="infDbgLed"></span>DBG<span class="inf-dbg-badge" id="infDbgBadge">0</span>
    </div>
    <div id="infDbgPanel" role="dialog" aria-label="Panel de debug de Informes de Auditoría">
        <div class="inf-dbg-header" id="infDbgHeader">
            <span>DEBUG · Informes de Auditoría · INF-v2.0-DBG</span>
            <span class="inf-dbg-controls">
                <button type="button" class="inf-dbg-btn" id="infDbgRefresh" title="Refrescar bitácora sin recargar">↻ Frag</button>
                <button type="button" class="inf-dbg-btn" id="infDbgCopy">Copiar</button>
                <button type="button" class="inf-dbg-btn" id="infDbgClear">Limpiar</button>
                <button type="button" class="inf-dbg-btn" id="infDbgClose">✕</button>
            </span>
        </div>
        <div id="infDbgBody"></div>
    </div>

    <div class="reservas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .table > :not(caption) > * > * { padding: 4px 8px; line-height: 1.25; vertical-align: middle; }
            .reservas-soft .table thead th { font-size: 8.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid-2) !important; }
            .reservas-soft .table tbody td { font-size: 8.5px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .reservas-soft .table tbody tr:hover > * { background-color: #f8fafc; --bs-table-accent-bg: transparent; }
            .reservas-soft .table tfoot > tr > td { font-size: 8.5px; font-weight: 700; color: var(--ink-2); background-color: var(--head); border-top: 2px solid var(--grid-2) !important; border-bottom: 0; white-space: nowrap; }
            .reservas-soft .table tfoot .st-total-label { color: var(--accent); letter-spacing: .03em; }
            .reservas-soft .strong { color: var(--ink-2); font-weight: 700; }
            .reservas-soft .sub { font-size: 8px; color: var(--ink-3); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
            .reservas-soft .btn-mini:hover { color: var(--accent); border-color: var(--accent-bd); background: var(--accent-bg); }
            .reservas-soft .btn-green { background: var(--accent); color: #fff; border: 1px solid var(--accent); font-family: inherit; font-size: 8.5px; font-weight: 700; padding: 3px 10px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
            .reservas-soft .btn-green:hover { background: #2e5c43; border-color: #2e5c43; }
            .reservas-soft .xls-search { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 9px; box-shadow: none !important; outline: 0; min-width: 210px; }
            .reservas-soft .xls-search:focus { background: #fff; border-color: var(--accent-bd); }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 700; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 2px 9px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }

            /* ── CHIPS DE CANAL (CLIENTE / ADMIN / BD) ── */
            .inf-chip-bar { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
            .inf-canal-chip {
                border: 1px solid var(--grid-2); background: #fff; color: var(--ink);
                font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
            }
            .inf-canal-chip:hover { border-color: var(--accent-bd); color: var(--ink-2); }
            .inf-canal-chip.active { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); font-weight: 700; }
            .inf-canal-cnt { font-size: 7px; font-weight: 700; padding: 0 5px; border-radius: 99px; background: var(--grid); color: var(--ink-2); }
            .inf-canal-chip.active .inf-canal-cnt { background: var(--accent); color: #fff; }

            /* ── KPIs ── */
            .reservas-soft .inf-kpis { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 10px; }
            @media (max-width: 767px) { .reservas-soft .inf-kpis { grid-template-columns: repeat(2, 1fr); } }
            .reservas-soft .inf-kpi { border: 1px solid var(--grid-2); border-radius: 7px; padding: 8px 11px; background: #fff; }
            .reservas-soft .inf-kpi b { display: block; font-size: 15px; line-height: 1.2; color: var(--ink-2); }
            .reservas-soft .inf-kpi span { display: block; font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-3); margin-top: 2px; }
            .reservas-soft .inf-kpi.inf-k-hoy b { color: var(--accent); }
            .reservas-soft .inf-kpi.inf-k-esc b { color: #d97706; }
            .reservas-soft .inf-kpi.inf-k-vis b { color: #2563eb; }
            .reservas-soft .inf-kpi.inf-k-usr b { color: #7c3aed; }

            .reservas-soft .inf-filtros { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--grid); }
            .reservas-soft .inf-sel { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 7px; outline: 0; font-family: inherit; }
            .reservas-soft .inf-sel:focus { border-color: var(--accent-bd); background: #fff; }

            /* ── BADGES DE ACCIÓN ── */
            .reservas-soft .inf-badge { display: inline-flex; align-items: center; gap: 3px; font-size: 7px; font-weight: 800; letter-spacing: .04em; padding: 2px 7px; border-radius: 10px; border: 1px solid var(--grid-2); background: var(--head); color: var(--ink-2); text-transform: uppercase; }
            .reservas-soft .inf-b-crear      { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
            .reservas-soft .inf-b-actualizar { background: #fffbeb; border-color: #fde68a; color: #b45309; }
            .reservas-soft .inf-b-eliminar   { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
            .reservas-soft .inf-b-vista      { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
            .reservas-soft .inf-b-busqueda   { background: #f5f3ff; border-color: #ddd6fe; color: #6d28d9; }
            .reservas-soft .inf-b-login      { background: #ecfeff; border-color: #a5f3fc; color: #0e7490; }

            .reservas-soft .inf-detalle { max-width: 340px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--ink-2); }
            .inf-json-pre { background: #0f172a; color: #e2e8f0; border-radius: 8px; padding: 10px 12px; font-family: ui-monospace, monospace; font-size: 9px; max-height: 320px; overflow: auto; white-space: pre-wrap; word-break: break-word; margin: 0; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #infDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
            #infDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
            .inf-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .inf-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: infDbgBlink 1s steps(2,start) infinite; }
            @keyframes infDbgBlink { 50% { opacity: .35; } }
            .inf-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
            #infDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
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
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap: 8px;">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h6 class="fw-bold mb-0" style="font-size: 11px; color: #334155;">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> Bitácora General e Informes de Auditoría
                </h6>
                <span class="badge rounded-pill" id="infChipVer" style="font-size: 7px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; font-family: ui-monospace, monospace;">
                    INF-v2.0-DBG
                </span>
                <span class="count-badge" title="Eventos cargados en memoria (máximo 500)">
                    <b id="infCount">{{ $infKTotal }}</b> evento(s)
                </span>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                <input type="text" id="infSearch" class="xls-search" placeholder="Buscar usuario, doc, tabla, acción, IP o cambio…" autocomplete="off">
                <button type="button" class="btn-mini" id="infBtnRefrescar" title="Sincronizar últimos eventos sin recargar página">
                    <i class="bi bi-arrow-clockwise"></i> Refrescar
                </button>
                <button type="button" class="btn-green" id="infBtnCsv" title="Exportar vista filtrada a Excel CSV">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Exportar CSV
                </button>
            </div>
        </div>

        <!-- 5 KPIs DINÁMICOS (SE RECALCULAN AL FILTRAR) -->
        <div class="inf-kpis">
            <div class="inf-kpi">
                <b id="infKpiTotal">{{ $infKTotal }}</b>
                <span>Eventos (Filtro)</span>
            </div>
            <div class="inf-kpi inf-k-hoy">
                <b id="infKpiHoy">{{ $infKHoy }}</b>
                <span>Eventos de Hoy</span>
            </div>
            <div class="inf-kpi inf-k-esc">
                <b id="infKpiEsc">{{ $infKEsc }}</b>
                <span>Mutaciones CRUD</span>
            </div>
            <div class="inf-kpi inf-k-vis">
                <b id="infKpiVis">{{ $infKVistas }}</b>
                <span>Accesos y Vistas</span>
            </div>
            <div class="inf-kpi inf-k-usr">
                <b id="infKpiUsr">{{ $infKUsr }}</b>
                <span>Usuarios Distintos</span>
            </div>
        </div>

        <!-- BARRA DE FILTROS: CHIPS DE CANAL + TIPO DE ACCIÓN + RANGO DE FECHAS -->
        <div class="inf-filtros">
            <div class="inf-chip-bar">
                <button type="button" class="inf-canal-chip active" data-canal="">
                    Todos <span class="inf-canal-cnt" id="infCntAll">{{ $cntCanalAll }}</span>
                </button>
                <button type="button" class="inf-canal-chip" data-canal="cliente">
                    <i class="bi bi-person-badge text-success"></i> Portal Cliente <span class="inf-canal-cnt" id="infCntCli">{{ $cntCanalCliente }}</span>
                </button>
                <button type="button" class="inf-canal-chip" data-canal="admin">
                    <i class="bi bi-sliders text-primary"></i> Panel Admin <span class="inf-canal-cnt" id="infCntAdm">{{ $cntCanalAdmin }}</span>
                </button>
                <button type="button" class="inf-canal-chip" data-canal="bd">
                    <i class="bi bi-database text-warning"></i> Tablas BD (CRUD) <span class="inf-canal-cnt" id="infCntBd">{{ $cntCanalBd }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center flex-wrap gap-2">
                <select id="infFiltroTipo" class="inf-sel" title="Filtrar por tipo de operación">
                    <option value="">Todas las acciones</option>
                    <option value="crear">Crear (Altas / Store)</option>
                    <option value="actualizar">Editar (Cambios / Update)</option>
                    <option value="eliminar">Eliminar (Bajas / Delete)</option>
                    <option value="vista">Visualización (Ingresos / Fragmentos)</option>
                    <option value="busqueda">Búsquedas</option>
                    <option value="login">Sesiones</option>
                </select>
                <input type="date" id="infDesde" class="inf-sel" title="Fecha inicial">
                <input type="date" id="infHasta" class="inf-sel" title="Fecha final">
                <button type="button" class="btn-mini" id="infBtnLimpiar">
                    <i class="bi bi-eraser"></i> Limpiar
                </button>
            </div>
        </div>

        <!-- TABLA DE AUDITORÍA -->
        <div class="table-responsive" style="max-height: 490px; overflow-y: auto;">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 820px;">
                <thead style="position: sticky; top: 0; z-index: 2;">
                    <tr>
                        <th style="width: 65px;">#ID</th>
                        <th style="width: 125px;">Fecha / Hora</th>
                        <th style="width: 185px;">Usuario / Actor</th>
                        <th style="width: 110px;">Acción</th>
                        <th style="width: 165px;">Tabla / Módulo</th>
                        <th style="width: 95px;">IP</th>
                        <th>Detalle / Cambios Registrados</th>
                        <th style="width: 55px;" class="text-end">JSON</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infItems as $inf)
                        @php
                            $tAcc = $inf['accion_tipo'];
                            $haystack = mb_strtolower(preg_replace('/\s+/', ' ',
                                $inf['id'] . ' ' .
                                $inf['usuario'] . ' ' .
                                $inf['accion_raw'] . ' ' .
                                $inf['modulo'] . ' ' .
                                $inf['registro_id'] . ' ' .
                                $inf['ip'] . ' ' .
                                $inf['detalle']
                            ));
                        @endphp
                        <tr data-inf-search="{{ $haystack }}"
                            data-accion="{{ $tAcc }}"
                            data-canal="{{ $inf['canal'] }}"
                            data-fecha="{{ $inf['fecha'] }}"
                            data-usuario="{{ mb_strtolower($inf['usuario']) }}"
                            data-id="{{ $inf['id'] }}"
                            data-ant="{{ $inf['json_ant'] }}"
                            data-nue="{{ $inf['json_nue'] }}">
                            <td class="sub fw-bold">#{{ $inf['id'] }}</td>
                            <td class="strong">{{ $inf['cuando'] }}</td>
                            <td>
                                <span class="strong d-block text-truncate" style="max-width: 180px;" title="{{ $inf['usuario'] }}">
                                    {{ $inf['usuario'] }}
                                </span>
                            </td>
                            <td>
                                <span class="inf-badge {{ $infClase[$tAcc] ?? '' }}" title="{{ $inf['accion_raw'] }}">
                                    {{ $inf['accion_raw'] ?: ($infTexto[$tAcc] ?? 'EVENTO') }}
                                </span>
                            </td>
                            <td>
                                <code style="font-size: 8px; color: var(--ink-2);">{{ $inf['modulo'] }}</code>
                                @if($inf['registro_id'] > 0)
                                    <span class="sub ms-1">#{{ $inf['registro_id'] }}</span>
                                @endif
                            </td>
                            <td class="sub">{{ $inf['ip'] }}</td>
                            <td>
                                <div class="inf-detalle" title="{{ $inf['detalle'] }}">
                                    {{ $inf['detalle'] !== '' ? $inf['detalle'] : '—' }}
                                </div>
                            </td>
                            <td class="text-end">
                                @if($inf['tiene_json'])
                                    <button type="button"
                                            class="btn-mini js-inf-ver-json"
                                            title="Inspeccionar datos anteriores y nuevos (#{{ $inf['id'] }})">
                                        <i class="bi bi-code-square text-primary"></i>
                                    </button>
                                @else
                                    <span class="sub">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" style="font-size: 9px; color: #94a3b8;">
                                <i class="bi bi-journal-text d-block mb-1" style="font-size: 18px;"></i>
                                Sin eventos de auditoría registrados todavía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" class="st-total-label">
                            <i class="bi bi-shield-check me-1"></i>
                            MOSTRANDO <span id="infFootVisible">{{ $infKTotal }}</span> DE {{ $infKTotal }} EVENTO(S) EN BITÁCORA INMUTABLE
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- MODAL INSPECTOR DE PAYLOAD JSON (ANTES VS DESPUÉS) --}}
        <div class="modal fade" id="infModalJson" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content reservas-soft" style="border:0; border-radius:10px; overflow:hidden;">
                    <div class="modal-header bg-dark text-white py-2 px-3">
                        <span class="fw-bold" style="font-size: 10.5px;" id="infModalJsonTitle">
                            <i class="bi bi-code-slash text-warning me-1"></i> Inspector de Evento de Auditoría
                        </span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="font-size:9px;"></button>
                    </div>
                    <div class="modal-body p-3 bg-light">
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <div class="strong mb-1" style="font-size: 8.5px; text-transform: uppercase; color: #b91c1c;">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Datos Anteriores (Original)
                                </div>
                                <pre class="inf-json-pre" id="infModalJsonAnt">Sin estado previo (Creación o Vista)</pre>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="strong mb-1" style="font-size: 8.5px; text-transform: uppercase; color: #15803d;">
                                    <i class="bi bi-check2-circle me-1"></i> Datos Nuevos / Contexto
                                </div>
                                <pre class="inf-json-pre" id="infModalJsonNue">{}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL INF-v2.0-DBG (diseño TIP-v1.3) ═══ */
    var DBG = (window.InfDbg && window.InfDbg.alive && window.InfDbg.alive())
        ? window.InfDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function unicarDbg() {
                var ts = document.querySelectorAll('#infDbgToggle');
                for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                var ps = document.querySelectorAll('#infDbgPanel');
                for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
            }
            function asegurarNodos() {
                unicarDbg();
                var rootDbg = document.querySelector('#sheet-informes');
                if (!rootDbg) { return; }
                if (!$('infDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'infDbgToggle';
                    t.title = 'Debug Informes (INF-v2.0-DBG)';
                    t.innerHTML = '<span class="inf-dbg-led" id="infDbgLed"></span>DBG<span class="inf-dbg-badge" id="infDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('infDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'infDbgPanel';
                    p.innerHTML = '<div class="inf-dbg-header" id="infDbgHeader">' +
                        '<span>DEBUG · Informes de Auditoría · INF-v2.0-DBG</span>' +
                        '<span class="inf-dbg-controls">' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgCopy">Copiar</button>' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgClear">Limpiar</button>' +
                        '<button type="button" class="inf-dbg-btn" id="infDbgClose">✕</button>' +
                        '</span></div><div id="infDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; }
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('infDbgToggle'), p = $('infDbgPanel');
                if (t) { t.style.bottom = '0px'; }
                if (p) { p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px'; }
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
                    var txt = '=== DEBUG informes INF-v2.0-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        });
                    }
                },
                unicar: unicarDbg,
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('infDbgToggle'); }
            };
            window.InfDbg = api;
            return api;
        })();

    DBG.ensure();
    var infdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    /* ═══ 1) KPIs · re-calculados sobre las filas VISIBLES del filtro ═══ */
    function actualizarKpis(filas) {
        var hoy = new Date();
        var hoyStr = hoy.getFullYear() + '-' + ('0' + (hoy.getMonth() + 1)).slice(-2) + '-' + ('0' + hoy.getDate()).slice(-2);
        var total = 0, esc = 0, vis = 0, hoyN = 0, usrs = {};
        filas.forEach(function (tr) {
            total++;
            var acc = tr.getAttribute('data-accion') || '';
            if (acc === 'crear' || acc === 'actualizar' || acc === 'eliminar') { esc++; }
            if (acc === 'vista' || acc === 'busqueda') { vis++; }
            if ((tr.getAttribute('data-fecha') || '') === hoyStr) { hoyN++; }
            var u = tr.getAttribute('data-usuario') || '';
            if (u) { usrs[u] = 1; }
        });
        var fijar = function (id, v) { var el = document.getElementById(id); if (el) { el.textContent = v; } };
        fijar('infKpiTotal', total);
        fijar('infKpiHoy', hoyN);
        fijar('infKpiEsc', esc);
        fijar('infKpiVis', vis);
        fijar('infKpiUsr', Object.keys(usrs).length);
        fijar('infCount', total);
        fijar('infFootVisible', total);
    }

    /* ═══ 2) FILTROS (canal + búsqueda + tipo de acción + rango de fechas) ═══ */
    function aplicarFiltro(origen) {
        var s = document.getElementById('infSearch');
        var t = document.getElementById('infFiltroTipo');
        var d = document.getElementById('infDesde');
        var h = document.getElementById('infHasta');
        var chipActivo = document.querySelector('#sheet-informes .inf-canal-chip.active');

        var q     = (s && s.value ? s.value : '').toLowerCase().trim();
        var tipo  = (t && t.value) || '';
        var canal = chipActivo ? (chipActivo.getAttribute('data-canal') || '') : '';
        var desde = (d && d.value) || '';
        var hasta = (h && h.value) || '';

        var filas = [];
        document.querySelectorAll('#sheet-informes tbody tr[data-inf-search]').forEach(function (tr) {
            var hay = tr.getAttribute('data-inf-search') || '';
            var f   = tr.getAttribute('data-fecha') || '';
            var c   = tr.getAttribute('data-canal') || '';
            var a   = tr.getAttribute('data-accion') || '';

            var ok = (!q || hay.indexOf(q) !== -1)
                  && (!tipo || a === tipo)
                  && (!canal || c === canal);

            if (ok && (desde || hasta) && !f) { ok = false; }
            if (ok && desde && f < desde) { ok = false; }
            if (ok && hasta && f > hasta) { ok = false; }

            tr.style.display = ok ? '' : 'none';
            if (ok) { filas.push(tr); }
        });

        actualizarKpis(filas);
        if (origen) {
            infdbg('filtro (' + origen + ') · canal=' + (canal || 'todos') + ' · tipo=' + (tipo || 'todas') + ' · q="' + q + '" · visibles=' + filas.length, 'debug');
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
        document.querySelectorAll('#sheet-informes .inf-canal-chip').forEach(function (c, idx) {
            c.classList.toggle('active', idx === 0);
        });
        aplicarFiltro('limpiar');
    }

    /* ═══ 3) EXPORTAR CSV (filas visibles del filtro actual · BOM UTF-8 · separador ;) ═══ */
    function exportarCsv() {
        var filas = [];
        document.querySelectorAll('#sheet-informes tbody tr[data-inf-search]').forEach(function (tr) {
            if (tr.style.display !== 'none') { filas.push(tr); }
        });
        if (!filas.length) {
            infdbg('exportar CSV cancelado · 0 fila(s) en el filtro', 'warn');
            return;
        }
        var esc = function (v) {
            var s2 = String(v == null ? '' : v).replace(/\s+/g, ' ').trim();
            return (s2.indexOf(';') !== -1 || s2.indexOf('"') !== -1 || s2.indexOf('\n') !== -1)
                ? '"' + s2.replace(/"/g, '""') + '"'
                : s2;
        };
        var lineas = ['ID;Fecha/Hora;Usuario;Accion;Tabla/Modulo;IP;Detalle'];
        filas.forEach(function (tr) {
            var tds = tr.querySelectorAll('td');
            var fila = [];
            for (var i = 0; i < 7 && i < tds.length; i++) {
                fila.push(esc(tds[i].textContent));
            }
            lineas.push(fila.join(';'));
        });
        var blob = new Blob(['\ufeff' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'informe-auditoria-rsv-' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(a);
        a.click();
        if (a.remove) { a.remove(); }
        infdbg('CSV exportado · ' + filas.length + ' fila(s) · BOM UTF-8 · separador ;', 'ok');
    }

    /* ═══ 4) REFRESCAR EL FRAGMENTO 100% AJAX (SIN RECARGAR NAVEGADOR) ═══ */
    window.infReloadFragment = function (motivo) {
        var root = document.getElementById('sheet-informes');
        if (!root) return Promise.resolve();

        var fragUrl = root.getAttribute('data-frag') || window.location.pathname;
        var inmId   = root.getAttribute('data-inmueble-id') || 'todos';

        var urlObj = new URL(fragUrl, window.location.origin);
        urlObj.searchParams.set('inmueble_id', inmId);
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        infdbg('GET Fragmento (' + (motivo || 'manual') + ') → ' + urlObj.pathname + urlObj.search, 'net');

        return fetch(urlObj.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            cache: 'no-store'
        })
        .then(function (r) {
            return r.text().then(function (html) {
                return { ok: r.ok, status: r.status, html: html, ms: Math.round(performance.now() - t0) };
            });
        })
        .then(function (res) {
            if (!res.ok) {
                infdbg('Error HTTP ' + res.status + ' al refrescar Informes (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevo = doc.getElementById('sheet-informes');
            var copias = document.querySelectorAll('#sheet-informes');
            if (!nuevo || !copias.length) {
                infdbg('Respuesta sin #sheet-informes (' + res.ms + 'ms)', 'warn');
                return;
            }
            copias.forEach(function (actual) {
                actual.replaceWith(nuevo.cloneNode(true));
            });
            DBG.ensure();
            aplicarFiltro();
            infdbg('Bitácora de Informes actualizada (' + res.ms + 'ms) · eventos=' + (nuevo.getAttribute('data-total') || '?'), 'ok');
        })
        .catch(function (err) {
            infdbg('Excepción al refrescar Informes: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    /* ═══ 5) ABRIR INSPECTOR MODAL JSON (ANTES VS DESPUÉS) ═══ */
    function abrirModalJsonDeFila(tr) {
        if (!tr) return;
        var id  = tr.getAttribute('data-id') || '?';
        var ant = tr.getAttribute('data-ant') || '';
        var nue = tr.getAttribute('data-nue') || '';

        var elTitle = document.getElementById('infModalJsonTitle');
        var elAnt   = document.getElementById('infModalJsonAnt');
        var elNue   = document.getElementById('infModalJsonNue');
        var modalEl = document.getElementById('infModalJson');

        if (elTitle) elTitle.innerHTML = '<i class="bi bi-code-slash text-warning me-1"></i> Inspector de Evento #' + id;
        if (elAnt)   elAnt.textContent   = ant || '— Sin datos anteriores (Creación o Visualización) —';
        if (elNue)   elNue.textContent   = nue || '— Sin datos nuevos —';

        if (modalEl && window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
        infdbg('Inspeccionando JSON del evento #' + id, 'info');
    }

    if (!window.__jsInformesV2Listo) {
        window.__jsInformesV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }

            if (t.closest('#infDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#infDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#infDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#infDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#infDbgRefresh') || t.closest('#infBtnRefrescar')) {
                e.stopPropagation();
                window.infReloadFragment('btn-refrescar');
                return;
            }
            if (t.closest('#infBtnLimpiar')) { limpiarFiltros(); return; }
            if (t.closest('#infBtnCsv'))     { exportarCsv(); return; }

            // Filtro por chip de canal (Todos / Portal Cliente / Panel Admin / Tablas BD)
            var chip = t.closest('.inf-canal-chip');
            if (chip && chip.closest('#sheet-informes')) {
                document.querySelectorAll('#sheet-informes .inf-canal-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltro('canal');
                return;
            }

            // Abrir modal JSON
            var btnJson = t.closest('.js-inf-ver-json');
            if (btnJson) {
                e.preventDefault();
                abrirModalJsonDeFila(btnJson.closest('tr'));
            }
        });

        document.addEventListener('dblclick', function (e) {
            if (e.target && e.target.closest && e.target.closest('#infDbgHeader')) {
                DBG.close();
            }
        });

        document.addEventListener('input', function (e) {
            var t = e.target;
            if (!t || !t.id) return;
            if (t.id === 'infSearch' || t.id === 'infDesde' || t.id === 'infHasta') {
                aplicarFiltro('input');
            }
        });

        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || !t.id) return;
            if (t.id === 'infFiltroTipo' || t.id === 'infDesde' || t.id === 'infHasta') {
                aplicarFiltro('select');
            }
        });

        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target === '#aud-sub-logs' || target === '#single-aud-sub-logs') {
                window.infReloadFragment('tab-focus-logs');
            }
        });
    }

    var SRVDBG = @json($infDbg ?? null);
    infdbg('INF-v2.0-DBG activo · eventos=' + (SRVDBG ? SRVDBG.eventos : 0) +
           ' · hoy=' + (SRVDBG ? SRVDBG.hoy : 0) +
           ' · CRUD=' + (SRVDBG ? SRVDBG.crud : 0) +
           ' · vistas=' + (SRVDBG ? SRVDBG.vistas : 0) +
           ' · usuarios=' + (SRVDBG ? SRVDBG.usuarios : 0), 'ok');
    if (SRVDBG && SRVDBG.canales) {
        infdbg('Desglose por canal · Portal Cliente=' + SRVDBG.canales.cliente + ' · Panel Admin=' + SRVDBG.canales.admin + ' · Tablas BD=' + SRVDBG.canales.bd, 'info');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { infdbg('⚠ SERVIDOR: ' + er, 'err'); });
    }
})();
</script>
