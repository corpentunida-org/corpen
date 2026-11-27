{{-- partials/mis-reservas/activas-pasadas.blade.php · Cliente · Minimalista 360° Reactivo sin Reload (AP-v4.2-DBG) --}}
<div id="sheet-activas-pasadas-cliente">

    {{-- AP-v4.2-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="apDbgToggle" title="Debug Activas y Pasadas (AP-v4.2-DBG)">
        <span class="ap-dbg-led" id="apDbgLed"></span>DBG<span class="ap-dbg-badge" id="apDbgBadge">0</span>
    </div>
    <div id="apDbgPanel" role="dialog" aria-label="Panel de debug de Activas y Pasadas">
        <div class="ap-dbg-header" id="apDbgHeader">
            <span>DEBUG · Activas y Pasadas · AP-v4.2-DBG</span>
            <span class="ap-dbg-controls">
                <button type="button" class="ap-dbg-btn" id="apDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="ap-dbg-btn" id="apDbgCopy">Copiar</button>
                <button type="button" class="ap-dbg-btn" id="apDbgClear">Limpiar</button>
                <button type="button" class="ap-dbg-btn" id="apDbgClose">✕</button>
            </span>
        </div>
        <div id="apDbgBody"></div>
    </div>

    @php
        $uid = \Illuminate\Support\Facades\Auth::id();
        $userName = optional(\Illuminate\Support\Facades\Auth::user())->name ?? 'Tú';
        $ridSel = request('reserva_id') ?? request('id_reserva');
        $apErrores = [];

        // 1. Aprovechar las colecciones inyectadas por ClientePortalController@misReservasActivasFragmento
        $mis = collect();
        if (isset($todas) && $todas instanceof \Illuminate\Support\Collection && $todas->isNotEmpty()) {
            $mis = $todas;
        } elseif ((isset($activas) && $activas instanceof \Illuminate\Support\Collection) || (isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection)) {
            $cAct = (isset($activas) && $activas instanceof \Illuminate\Support\Collection) ? $activas : collect();
            $cPas = (isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection) ? $pasadas : collect();
            $mis = $cAct->concat($cPas)->unique('id')->values();
        }

        try {
            if ($mis->isEmpty() && $uid) {
                $mis = \App\Models\Rsv\Reserva::where('id_user', $uid)
                    ->orderByDesc('created_at')
                    ->get();
            }

            if ($mis->isNotEmpty()) {
                $mis->loadMissing([
                    'inmueble.multimedia',
                    'inmueble.tipoInmueble',
                    'inmueble.latestTarifa',
                    'status',
                    'origen',
                    'huespedes',
                    'transacciones.pasarela',
                    'mensajes.remitente',
                    'mensajes.ticket.estadoSoporte',
                    'reviews.tipoReceptor',
                    'historialEstados.estadoAnterior',
                    'historialEstados.estadoNuevo',
                    'historialEstados.user',
                    'historialEndosos.usuarioAnterior',
                    'historialEndosos.usuarioNuevo',
                    'historialEndosos.autorizadoPor',
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('cli.activas-pasadas: '.$e->getMessage());
            $apErrores[] = $e->getMessage();
        }

        // Detección de ruta del controlador de fragmentos
        $rutaFragmento = '';
        foreach (['rsv.cliente.mis-reservas.activas.fragmento', 'rsv.cliente.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) {
                $rutaFragmento = route($rn);
                break;
            }
        }

        $rutaStoreMensaje = '';
        foreach (['rsv.mensajes.store-ajax', 'rsv.mensajes.store', 'rsv.cliente.mensajes.store', 'mensajes.store'] as $rName) {
            if (\Illuminate\Support\Facades\Route::has($rName)) {
                $rutaStoreMensaje = route($rName);
                break;
            }
        }

        $rutaStoreReview = '';
        foreach (['rsv.reviews.store-ajax', 'rsv.reviews.store', 'rsv.cliente.reviews.store', 'reviews.store'] as $rName) {
            if (\Illuminate\Support\Facades\Route::has($rName)) {
                $rutaStoreReview = route($rName);
                break;
            }
        }

        $tieneRutaPdf = \Illuminate\Support\Facades\Route::has('rsv.reservas.pdf');

        $tiposReceptor = collect();
        $tipoReceptorDefaultId = 1;
        try {
            if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                $tiposReceptor = \App\Models\Rsv\TipoReceptor::all();
                $tipoReceptorDefaultId = optional($tiposReceptor->first())->id ?? 1;
            }
        } catch (\Throwable $e) {}

        $pillSt = function ($name) {
            $s = mb_strtolower((string) $name);
            if (str_contains($s, 'cancela') || str_contains($s, 'anula') || str_contains($s, 'rechaz') || str_contains($s, 'incumpl') || str_contains($s, 'no show')) return 'danger';
            if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
            if (str_contains($s, 'pendien') || str_contains($s, 'pre')) return 'warning';
            if (str_contains($s, 'confirm') || str_contains($s, 'activ') || str_contains($s, 'aprob') || str_contains($s, 'hosped') || str_contains($s, 'curso')) return 'success';
            return 'secondary';
        };

        // Conteo exacto por categoría para los chips de filtro
        $conteosCategorias = [
            'all'       => $mis->count(),
            'success'   => 0,
            'warning'   => 0,
            'primary'   => 0,
            'danger'    => 0,
            'secondary' => 0,
        ];

        foreach ($mis as $rItem) {
            $stNameItem = optional($rItem->status)->name ?? 'Pendiente';
            $grpItem = $pillSt($stNameItem);
            if (isset($conteosCategorias[$grpItem])) {
                $conteosCategorias[$grpItem]++;
            }
        }

        // Filtros por ESTADO (catálogo rsv_statuses, en orden de id): un chip por estado, con su color.
        $estadosRsv = collect();
        try {
            $estadosRsv = \App\Models\Rsv\Status::orderBy('id')->get(['id', 'name', 'color_hex']);
        } catch (\Throwable $e) {
            $apErrores[] = $e->getMessage();
        }
        // El estado "Pendiente" se muestra dividido según el soporte de pago (el estado en BD sigue
        // siendo Pendiente: calendario, límite anual y cancelación automática no cambian):
        //   subir  → "Pendiente subir pago"        (sin soporte cargado)
        //   valida → "Pendiente validación pago"   (soporte cargado, contabilidad aún no lo aprueba)
        $subPendiente = function ($r) {
            $stN = mb_strtolower((string) (optional($r->status)->name ?? ''));
            if (!str_contains($stN, 'pendien')) return null;
            $txs = $r->relationLoaded('transacciones') ? $r->transacciones : collect();
            // Rechazado / anulado por contabilidad = como si no hubiera soporte → vuelve a "subir"
            $conSoporte = $txs->filter(fn ($tx) => $tx->esSoporteValido());
            if ($conSoporte->isEmpty()) return 'subir';
            $aprobado = $conSoporte->contains(fn ($tx) => str_contains(mb_strtolower((string) $tx->estado_pago), 'aprob'));
            return $aprobado ? null : 'valida';
        };
        $claveFiltro = function ($r) use ($subPendiente) {
            $sub = $subPendiente($r);
            return 'st-' . ($r->id_rsv_statuses ?? '') . ($sub ? '-' . $sub : '');
        };
        $conteosClave = $mis->countBy($claveFiltro);

        // Chips: uno por estado del catálogo; Pendiente se parte en sus dos sub-estados
        $chipsFiltro = collect();
        foreach ($estadosRsv as $est) {
            $colEst = preg_match('/^#[0-9a-fA-F]{6}$/', (string) $est->color_hex) ? $est->color_hex : '#64748b';
            if (str_contains(mb_strtolower((string) $est->name), 'pendien')) {
                $chipsFiltro->push(['key' => 'st-' . $est->id . '-subir',  'name' => 'Pendiente subir pago',      'col' => $colEst]);
                $chipsFiltro->push(['key' => 'st-' . $est->id . '-valida', 'name' => 'Pendiente validación pago', 'col' => '#0ea5e9']);
                // Pendiente con pago ya aprobado (a la espera de confirmación): solo si existe alguno
                if (($conteosClave['st-' . $est->id] ?? 0) > 0) {
                    $chipsFiltro->push(['key' => 'st-' . $est->id, 'name' => $est->name, 'col' => $colEst]);
                }
            } else {
                $chipsFiltro->push(['key' => 'st-' . $est->id, 'name' => $est->name, 'col' => $colEst]);
            }
        }

        // Filtro por defecto: el primer filtro CON reservas en este orden de prioridad:
        // Pendiente subir pago → Pendiente validación pago → Confirmada (activa) → Hospedado/En curso
        // → Completada. Si no hay ninguna → Todas.
        $filtroDefecto = 'all';
        $prioridad = [
            fn ($c) => str_ends_with($c['key'], '-subir'),
            fn ($c) => str_ends_with($c['key'], '-valida'),
            fn ($c) => str_contains(mb_strtolower($c['name']), 'confirm'),
            fn ($c) => str_contains(mb_strtolower($c['name']), 'hosped') || str_contains(mb_strtolower($c['name']), 'curso'),
            fn ($c) => str_contains(mb_strtolower($c['name']), 'complet'),
        ];
        foreach ($prioridad as $cumple) {
            $chipPrio = $chipsFiltro->first(fn ($c) => $cumple($c) && ($conteosClave[$c['key']] ?? 0) > 0);
            if ($chipPrio) {
                $filtroDefecto = $chipPrio['key'];
                break;
            }
        }
        // Si llega una reserva señalada (?reserva_id=) que el filtro ocultaría, se muestran Todas
        $resSelFiltro = $ridSel ? $mis->firstWhere('id', (int) $ridSel) : null;
        if ($resSelFiltro && $filtroDefecto !== $claveFiltro($resSelFiltro)) {
            $filtroDefecto = 'all';
        }

        $cntActivas = isset($activas) && $activas instanceof \Illuminate\Support\Collection ? $activas->count() : null;
        $cntPasadas = isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection ? $pasadas->count() : null;

        $apDbgData = [
            'version'     => 'AP-v4.2-DBG',
            'controlador' => 'ClientePortalController@misReservasActivasFragmento',
            'vista'       => 'partials/mis-reservas/activas-pasadas.blade.php',
            'reservas'    => $mis->count(),
            'conteos'     => $conteosCategorias,
            'activas_ctl' => $cntActivas,
            'pasadas_ctl' => $cntPasadas,
            'reserva_sel' => $ridSel,
            'uid'         => $uid,
            'frag_url'    => $rutaFragmento ?: 'fallback location',
            'rutas'       => [
                'mensaje_store' => $rutaStoreMensaje ?: 'NO definida',
                'review_store'  => $rutaStoreReview  ?: 'NO definida',
                'pdf_comprobante' => $tieneRutaPdf ? 'rsv.reservas.pdf' : 'url fallback',
            ],
            'errores'     => $apErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliMisResRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="reservas-activas"
         data-total="{{ $mis->count() }}"
         data-reserva-sel="{{ $ridSel ?? '' }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:10px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:13.5px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:11px; color:var(--ink-3); line-height:1.4; }

            /* ── FILTROS MINIMALISTAS CON CONTADORES ── */
            .ap-filter-bar { display:flex; gap:4px; flex-wrap:wrap; align-items:center; }
            .ap-filter-chip {
                border:1px solid transparent; background:transparent; color:var(--ink-3);
                font-size:11px; font-weight:600; padding:3px 9px; border-radius:6px;
                cursor:pointer; transition:all .15s ease; display:inline-flex; align-items:center; gap:5px;
            }
            .ap-filter-chip:hover { color:var(--ink-2); background:var(--head); }
            .ap-filter-chip.active {
                background:var(--accent-bg); color:var(--accent); border-color:var(--accent-bd); font-weight:700;
            }
            .ap-chip-count {
                font-size:10px; font-weight:700; padding:0 5px; border-radius:999px;
                background:var(--grid); color:var(--ink); line-height:14px;
            }
            .ap-filter-chip.active .ap-chip-count {
                background:var(--accent); color:#fff;
            }
            .ap-filter-chip[data-filter="danger"].active {
                background:#fef2f2; color:#b91c1c; border-color:#fee2e2;
            }
            .ap-filter-chip[data-filter="danger"].active .ap-chip-count {
                background:#b91c1c; color:#fff;
            }
            .ap-filter-chip[data-filter="warning"].active {
                background:#fffbeb; color:#b45309; border-color:#fef3c7;
            }
            .ap-filter-chip[data-filter="warning"].active .ap-chip-count {
                background:#b45309; color:#fff;
            }
            .ap-filter-chip[data-filter="primary"].active {
                background:#eff6ff; color:#1d4ed8; border-color:#dbeafe;
            }
            .ap-filter-chip[data-filter="primary"].active .ap-chip-count {
                background:#1d4ed8; color:#fff;
            }

            /* Aviso "Pendiente subir soporte de pago" (fila y detalle) → enlace a Pagos con la reserva elegida */
            .ap-subir-pago { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:#b45309; background:#fffbeb; border:1px solid #fcd34d; border-radius:6px; padding:2px 8px; text-decoration:none; }
            .ap-subir-pago:hover { background:#fef3c7; color:#92400e; }
            .ap-subir-pago-lim { font-weight:600; color:#92400e; }
            .ap-subir-pago-banner { display:flex; align-items:center; gap:10px; background:#fffbeb; border:1px solid #fcd34d; border-left:4px solid #f59e0b; border-radius:8px; padding:10px 14px; color:#78350f; font-size:12.5px; text-decoration:none; }
            .ap-subir-pago-banner:hover { background:#fef3c7; color:#78350f; }
            /* Botones de acción dentro del detalle de la reserva (Pagos / Huéspedes) */
            .ap-btn-accion { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:#fff; background:var(--accent); border:1px solid var(--accent); border-radius:6px; padding:5px 12px; text-decoration:none; cursor:pointer; }
            .ap-btn-accion:hover { background:#2e5c43; border-color:#2e5c43; color:#fff; }
            .ap-btn-accion-sec { color:var(--accent); background:#fff; border-color:var(--accent-bd); padding:3px 10px; }
            .ap-btn-accion-sec:hover { background:var(--accent-bg); color:var(--accent); }
            .ap-btn-accion-del { color:#b91c1c; background:#fef2f2; border-color:#fecaca; padding:3px 10px; }
            .ap-btn-accion-del:hover { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
            .ap-hu-item { background:#fff; border:1px solid var(--grid); border-radius:8px; padding:8px 12px; }
            .ap-hu-box { background:var(--accent-bg); border:1px solid var(--accent-bd); border-radius:8px; padding:10px 12px; }
            .ap-hu-ok { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:8px 10px; font-size:12px; }
            .ap-hu-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:8px 10px; font-size:12px; }
            .ap-en-validacion { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; color:#0369a1; background:#f0f9ff; border:1px solid #bae6fd; border-radius:6px; padding:2px 8px; }
            .ap-en-validacion-banner { display:flex; align-items:center; gap:10px; background:#f0f9ff; border:1px solid #bae6fd; border-left:4px solid #0ea5e9; border-radius:8px; padding:10px 14px; color:#0c4a6e; font-size:12.5px; }
            .ap-subir-pago-cta { white-space:nowrap; background:#d97706; color:#fff; font-weight:700; border-radius:6px; padding:6px 12px; font-size:12px; }

            /* Chips por estado: color del catálogo rsv_statuses (--st-col) */
            .ap-st-dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--st-col); margin-right:4px; vertical-align:middle; }
            .ap-filter-chip.ap-chip-estado.active { background:color-mix(in srgb, var(--st-col) 14%, #fff); color:color-mix(in srgb, var(--st-col) 70%, #000); border-color:color-mix(in srgb, var(--st-col) 40%, #fff); }
            .ap-filter-chip.ap-chip-estado.active .ap-chip-count { background:color-mix(in srgb, var(--st-col) 75%, #000); color:#fff; }

            .ap-search-input {
                font-size:11px; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2);
                border-radius:6px; padding:4px 10px; outline:0; width:170px; transition:all .15s;
            }
            .ap-search-input:focus { background:#fff; border-color:var(--accent-bd); width:210px; }

            /* ── FILA MINIMALISTA CLICABLE ── */
            .ap-res-row {
                padding:10px 14px; background:#fff; border:1px solid var(--grid);
                border-radius:8px; margin-bottom:6px; cursor:pointer;
                transition:all .15s ease; display:flex; align-items:center;
                justify-content:space-between; gap:12px;
            }
            .ap-res-row:hover {
                border-color:var(--accent-bd); background:#fafdfc;
                transform:translateX(2px);
            }
            .ap-res-row.is-selected {
                border-color:var(--accent) !important; background:var(--accent-bg);
            }

            .ap-res-thumb {
                width:38px; height:38px; border-radius:6px; overflow:hidden;
                background:var(--head); border:1px solid var(--grid-2); flex-shrink:0;
                display:flex; align-items:center; justify-content:center; color:var(--ink-3);
            }
            .ap-res-thumb img { width:100%; height:100%; object-fit:cover; }

            /* ── ICONOS DISCRETOS ── */
            .ap-icon-btn {
                width:26px; height:26px; border-radius:6px; border:1px solid transparent;
                background:transparent; color:var(--ink-3); display:inline-flex;
                align-items:center; justify-content:center; font-size:14.5px;
                transition:all .15s; text-decoration:none; cursor:pointer;
            }
            .ap-icon-btn:hover { background:var(--head); border-color:var(--grid-2); color:var(--accent); }
            .ap-icon-btn.ap-pdf-btn:hover { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
            .ap-chevron { color:var(--ink-4); font-size:12px; transition:transform .15s, color .15s; }
            .ap-res-row:hover .ap-chevron { transform:translateX(3px); color:var(--accent); }

            .ap-dot-indicator { width:6px; height:6px; border-radius:50%; display:inline-block; }

            /* ── PILLS SUAVES ── */
            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:10px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px;
            }
            .reservas-soft .pill-success { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-info { color:#0369a1; background:#f0f9ff; border-color:#bae6fd; }
            .reservas-soft .pill-danger  { color:#b91c1c; background:#fef2f2; border-color:#fee2e2; }
            .reservas-soft .pill-primary { color:#1d4ed8; background:#eff6ff; border-color:#dbeafe; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .ap-meta-chip {
                display:inline-flex; align-items:center; gap:4px;
                font-size:10.5px; font-weight:600; color:var(--ink);
                background:var(--head); border:1px solid var(--grid-2);
                padding:2px 7px; border-radius:5px;
            }

            .reservas-soft .btn-mini {
                border:1px solid var(--grid-2); background:#fff; color:var(--ink-2);
                font-size:11px; font-weight:700; padding:5px 10px; border-radius:5px;
                cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px;
            }
            .reservas-soft .btn-mini:hover { color:var(--accent); border-color:var(--accent-bd); background:var(--accent-bg); }
            .reservas-soft .btn-mini-accent { background:var(--accent); color:#fff; border:1px solid var(--accent); }
            .reservas-soft .btn-mini-accent:hover { background:#2e5c43; color:#fff; }

            .ap-err-box { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:8px 10px; font-size:11px; margin-bottom:10px; }
            .ap-ok-box  { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:8px 10px; font-size:11px; margin-bottom:10px; }

            /* ── LÍNEA DE TIEMPO ── */
            .rsv-timeline { position:relative; padding-left:20px; margin:10px 0; }
            .rsv-timeline::before { content:''; position:absolute; left:5px; top:4px; bottom:4px; width:1.5px; background:var(--grid-2); }
            .rsv-timeline-item { position:relative; margin-bottom:12px; }
            .rsv-timeline-item:last-child { margin-bottom:0; }
            .rsv-timeline-dot { position:absolute; left:-20px; width:11px; height:11px; border-radius:50%; background:#fff; border:2px solid var(--ink-3); top:3px; z-index:2; }
            .rsv-timeline-dot.dot-success { background:#10b981; border-color:#d1fae5; }
            .rsv-timeline-dot.dot-warning { background:#f59e0b; border-color:#fef3c7; }
            .rsv-timeline-dot.dot-primary { background:#3b82f6; border-color:#dbeafe; }
            .rsv-timeline-content { background:#fff; border:1px solid var(--grid); border-radius:7px; padding:9px 12px; }

            /* ── CHAT SOPORTE ── */
            .ap-chat-box { max-height:230px; overflow-y:auto; padding:10px; background:var(--head); border:1px solid var(--grid-2); border-radius:8px; }
            .ap-msg-bubble { max-width:82%; padding:7px 11px; border-radius:8px; margin-bottom:7px; font-size:11.5px; line-height:1.4; }
            .ap-msg-mine { margin-left:auto; background:var(--accent-bg); border:1px solid var(--accent-bd); color:#1e3a2f; }
            .ap-msg-other { margin-right:auto; background:#fff; border:1px solid var(--grid-2); color:var(--ink-2); }
            .ap-msg-ticket { margin-left:auto; background:#eff6ff; border:1px solid #bfdbfe; color:#1e3a8a; }
            .ap-sop-estado { display:flex; align-items:center; gap:7px; font-size:11.5px; font-weight:600; color:var(--ink-2); margin-bottom:8px; }
            .ap-sop-dot { width:9px; height:9px; border-radius:50%; background:#cbd5e1; flex:0 0 auto; }
            .ap-sop-dot.on { background:#16a34a; box-shadow:0 0 0 3px rgba(22,163,74,.18); }
            .ap-sop-dot.off { background:#94a3b8; }
            .ap-sop-offline { border:1px dashed #93c5fd; background:#f8fbff; border-radius:8px; padding:12px; font-size:11.5px; }

            /* ── ESTRELLAS REVIEW ── */
            .ap-star-group { display:inline-flex; flex-direction:row-reverse; gap:4px; }
            .ap-star-group input { display:none; }
            .ap-star-group label { font-size:20px; color:#cbd5e1; cursor:pointer; transition:color .15s; }
            .ap-star-group input:checked ~ label,
            .ap-star-group label:hover,
            .ap-star-group label:hover ~ label { color:#f59e0b; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #apDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:10.5px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #apDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .ap-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .ap-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:apDbgBlink 1s steps(2,start) infinite; }
            @keyframes apDbgBlink { 50% { opacity:.35; } }
            .ap-dbg-badge { display:none; color:#f87171; font-size:10.5px; font-weight:700; }
            #apDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .ap-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:11.5px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .ap-dbg-controls { display:flex; gap:4px; }
            .ap-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:11.5px; padding:2px 7px; cursor:pointer; }
            .ap-dbg-btn:hover { background:#475569; }
            #apDbgBody { overflow-y:auto; padding:6px 8px; font-size:12.5px; line-height:1.55; }
            .ap-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .ap-dbg-time { color:#64748b; margin-right:4px; }
            .ap-dbg-info { color:#93c5fd; } .ap-dbg-ok { color:#86efac; } .ap-dbg-warn { color:#fde68a; } .ap-dbg-err { color:#fca5a5; } .ap-dbg-net { color:#c4b5fd; } .ap-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- CABECERA MINIMALISTA + BUSCADOR Y FILTROS CON CONTEO REAL --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="ap-filter-bar">
                <span class="val me-2" style="font-size:14.5px;">
                    <i class="bi bi-calendar2-check text-success me-1"></i> Mis Reservas
                </span>
                <button type="button" class="ap-filter-chip {{ $filtroDefecto === 'all' ? 'active' : '' }}" data-filter="all">
                    Todas <span class="ap-chip-count" id="apCntAll">{{ $conteosCategorias['all'] }}</span>
                </button>
                @foreach($chipsFiltro as $chip)
                    <button type="button" class="ap-filter-chip ap-chip-estado {{ $filtroDefecto === $chip['key'] ? 'active' : '' }}" data-filter="{{ $chip['key'] }}" style="--st-col: {{ $chip['col'] }};">
                        <span class="ap-st-dot"></span>{{ $chip['name'] }}
                        <span class="ap-chip-count" data-cnt-key="{{ $chip['key'] }}">{{ $conteosClave[$chip['key']] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>
            <input type="text" id="apSearchInput" class="ap-search-input" placeholder="Buscar reserva…">
        </div>

        {{-- LISTA MINIMALISTA (MUESTRA TODAS LAS RESERVAS DEL CLIENTE SIN LÍMITE DE 1) --}}
        <div id="apReservasList">
        @forelse($mis as $r)
            @php
                $fi = $r->fecha_inicio ? \Carbon\Carbon::parse($r->fecha_inicio) : null;
                $ff = $r->fecha_fin ? \Carbon\Carbon::parse($r->fecha_fin) : null;
                $noches = ($fi && $ff) ? max(1, (int) $fi->diffInDays($ff)) : 0;
                $st = optional($r->status)->name ?? 'Pendiente';
                $stGroup = $pillSt($st);
                $sel = (string) $ridSel === (string) $r->id;

                $inm = $r->inmueble;
                $thumb = ($inm && $inm->relationLoaded('multimedia') && $inm->multimedia->first())
                    ? $inm->multimedia->first()->url_archivo
                    : null;
                // Tarifa del inmueble para el AÑO de esta reserva
                $tarifaSnap = ($inm && $r->fecha_inicio)
                    ? \App\Models\Rsv\TarifaTemporada::aplicable($inm->id, \Carbon\Carbon::parse($r->fecha_inicio), $r->fecha_fin ? \Carbon\Carbon::parse($r->fecha_fin) : null)
                    : null;
                $origenNombre = optional($r->origen)->nombre ?? 'Portal Asociado';
                $urlPdfReserva = $tieneRutaPdf
                    ? route('rsv.reservas.pdf', $r->id)
                    : url('/rsv/reservas/' . $r->id . '/comprobante-pdf');

                $huespedesList = $r->relationLoaded('huespedes') ? $r->huespedes : collect();
                // Más reciente primero
                $txList        = $r->relationLoaded('transacciones') ? $r->transacciones->sortByDesc(fn ($t) => [$t->created_at, $t->id])->values() : collect();
                $msgList       = $r->relationLoaded('mensajes') ? $r->mensajes->sortBy('created_at') : collect();
                $revList       = $r->relationLoaded('reviews') ? $r->reviews : collect();
                $miReview      = $revList->where('id_user_autor', $uid)->first()
                              ?? $revList->where('id_user', $uid)->first()
                              ?? $revList->first();

                $montoTotal = (float) ($r->monto_total ?? 0);
                $montoPagado = (float) $txList->filter(function($tx) {
                    $est = mb_strtolower((string) $tx->estado_pago);
                    return str_contains($est, 'aprob') || str_contains($est, 'pagad') || str_contains($est, 'ok') || str_contains($est, 'exitos') || str_contains($est, 'complet');
                })->sum('monto');
                if ($montoPagado <= 0 && $txList->count() > 0) {
                    // Sin aprobados: se muestra lo reportado CON soporte (no anulado). Un intento sin archivo no abona.
                    $montoPagado = (float) $txList->filter(function ($tx) {
                        $est = mb_strtolower((string) $tx->estado_pago);
                        return $tx->tieneSoporte() && !str_contains($est, 'anul') && !str_contains($est, 'rechaz') && !str_contains($est, 'cancel');
                    })->sum('monto');
                }
                $saldoPendiente = max(0, $montoTotal - $montoPagado);

                // Pendiente sin soporte de pago cargado → aviso + acceso directo a Pagos con la reserva elegida
                $subPend      = $subPendiente($r);           // 'subir' | 'valida' | null
                $faltaSoporte = $subPend === 'subir';
                $enValidacion = $subPend === 'valida';
                $stVista = $faltaSoporte ? 'Pendiente subir pago' : ($enValidacion ? 'Pendiente validación pago' : $st);
                $pillVista = $enValidacion ? 'info' : $stGroup;
                $urlSubirPago = $faltaSoporte
                    ? route('rsv.cliente.portal') . '?tab=pagos-transacciones&reserva_id=' . $r->id . '&subir=1'
                    : null;
                $limitePago = $faltaSoporte
                    ? \App\Http\Controllers\Rsv\ReservaController::fechaLimitePagoDe($r)
                    : null;
                // Último soporte rechazado por contabilidad (para avisarle al asociado)
                $txRechazada = $faltaSoporte
                    ? $txList->first(fn ($t) => $t->tieneSoporte() && str_contains(mb_strtolower((string) $t->estado_pago), 'rechaz'))
                    : null;
                $pctPago = $montoTotal > 0 ? min(100, round(($montoPagado / $montoTotal) * 100)) : 0;

                // Línea de Tiempo
                $timeline = collect();
                $timeline->push((object)[
                    'fecha'      => $r->created_at,
                    'titulo'     => 'Reserva Registrada (' . $origenNombre . ')',
                    'desc'       => 'Solicitud inicial por $' . number_format($montoTotal, 0) . ' COP.',
                    'comentario' => $r->comentario_reserva,
                    'usuario'    => optional($r->user)->name ?? 'Titular',
                    'icono'      => 'bi-stars',
                    'color'      => 'success',
                ]);

                if ($r->relationLoaded('historialEstados')) {
                    foreach ($r->historialEstados as $he) {
                        $timeline->push((object)[
                            'fecha'      => $he->created_at,
                            'titulo'     => 'Cambio de Estado',
                            'desc'       => 'De <b>' . e(optional($he->estadoAnterior)->name ?? 'Inicial') . '</b> a <b>' . e(optional($he->estadoNuevo)->name ?? 'Actual') . '</b>.',
                            'comentario' => $he->comentario,
                            'usuario'    => optional($he->user)->name,
                            'icono'      => 'bi-arrow-left-right',
                            'color'      => 'primary',
                        ]);
                    }
                }

                if ($r->relationLoaded('historialEndosos')) {
                    foreach ($r->historialEndosos as $hen) {
                        $timeline->push((object)[
                            'fecha'      => $hen->created_at,
                            'titulo'     => 'Endoso / Cesión de Reserva',
                            'desc'       => 'Cedida de <b>' . e(optional($hen->usuarioAnterior)->name ?? 'N/D') . '</b> a <b>' . e(optional($hen->usuarioNuevo)->name ?? 'N/D') . '</b>.',
                            'comentario' => $hen->motivo_endoso,
                            'usuario'    => 'Autorizó: ' . (optional($hen->autorizadoPor)->name ?? 'Administración'),
                            'icono'      => 'bi-person-bounding-box',
                            'color'      => 'warning',
                        ]);
                    }
                }

                $timeline = $timeline->sortByDesc('fecha');
            @endphp

            {{-- FILA MINIMALISTA: Clic en cualquier lado abre el modal --}}
            <div class="ap-res-row {{ $sel ? 'is-selected' : '' }}"
                 id="apResRow_{{ $r->id }}"
                 data-reserva-id="{{ $r->id }}"
                 data-reserva-cod="{{ $r->codigo_reserva }}"
                 data-st-group="{{ $stGroup }}"
                 data-st-id="{{ $r->id_rsv_statuses }}"
                 data-st-key="{{ $claveFiltro($r) }}"
                 data-search="{{ mb_strtolower($r->codigo_reserva . ' ' . ($inm->name ?? '') . ' ' . ($inm->city ?? '') . ' ' . $st) }}"
                 data-bs-toggle="modal"
                 data-bs-target="#modalExp360_{{ $r->id }}">

                {{-- Izquierda: Foto mini + Título limpio + Subtítulo en una línea --}}
                <div class="d-flex align-items-center gap-3" style="min-width:0; flex:1;">
                    <div class="ap-res-thumb">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $inm->name ?? 'Inmueble' }}" loading="lazy">
                        @else
                            <i class="bi bi-house-door"></i>
                        @endif
                    </div>

                    <div style="min-width:0; flex:1;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="val text-truncate" style="font-size:13.5px;">
                                {{ $inm->name ?? 'Propiedad' }}
                            </span>
                            <span class="sub" style="font-size:10.5px; color:var(--ink-3);">#{{ $r->codigo_reserva }}</span>
                        </div>
                        <div class="sub text-truncate mt-1" style="font-size:11px;">
                            {{ $fi?->format('d M') ?? '—' }} – {{ $ff?->format('d M Y') ?? '—' }}
                            <span class="mx-1">·</span> {{ $noches }} {{ $noches === 1 ? 'noche' : 'noches' }}
                            <span class="mx-1">·</span> {{ $huespedesList->count() }} huésped(es)
                        </div>
                        @if($enValidacion)
                            <span class="ap-en-validacion mt-1">
                                <i class="bi bi-hourglass-split"></i> Pendiente validación pago · soporte en revisión por contabilidad
                            </span>
                        @endif
                        @if($faltaSoporte)
                            <a href="{{ $urlSubirPago }}" onclick="event.stopPropagation();" class="ap-subir-pago mt-1"
                               title="Ir a Pagos para subir el soporte de esta reserva">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                Pendiente subir soporte de pago
                                @if($limitePago)
                                    <span class="ap-subir-pago-lim">· hasta {{ $limitePago->locale('es')->translatedFormat('D j M') }}</span>
                                @endif
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Derecha: Monto + Estado + PDF + Pin reactivo sin reload + Chevron --}}
                <div class="d-flex align-items-center gap-2 gap-sm-3 flex-shrink-0">
                    <div class="text-end d-none d-sm-block">
                        <div class="val" style="font-size:13.5px; color:var(--ink-2);">
                            ${{ number_format($montoTotal, 0) }}
                        </div>
                        <div class="sub" style="font-size:10px;">
                            @if($stGroup === 'danger')
                                <span style="color:#b91c1c;">Cancelada</span>
                            @elseif($saldoPendiente > 0)
                                <span style="color:#d97706;">Pendiente</span>
                            @else
                                <span style="color:var(--accent);">Al día</span>
                            @endif
                        </div>
                    </div>

                    <span class="pill pill-{{ $pillVista }}">{{ $stVista }}</span>

                    {{-- Descarga directa del comprobante PDF oficial --}}
                    <a href="{{ $urlPdfReserva }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       onclick="event.stopPropagation();"
                       class="ap-icon-btn ap-pdf-btn text-danger js-ap-pdf-link"
                       data-reserva-cod="{{ $r->codigo_reserva }}"
                       title="Descargar comprobante PDF (#{{ $r->codigo_reserva }})">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>

                    {{-- Ícono sutil para fijar reserva reactivamente sin recargar toda la página --}}
                    <button type="button"
                            class="ap-icon-btn js-ap-pin-reserva"
                            data-reserva-id="{{ $r->id }}"
                            data-reserva-cod="{{ $r->codigo_reserva }}"
                            title="Fijar esta reserva para Itinerario y Huéspedes">
                        <i class="bi {{ $sel ? 'bi-pin-fill text-success' : 'bi-pin-angle' }}"></i>
                    </button>

                    <i class="bi bi-chevron-right ap-chevron"></i>
                </div>
            </div>

            {{-- ==================================================================== --}}
            {{-- MODAL EXPEDIENTE 360° (Al dar clic en cualquier parte de la fila)    --}}
            {{-- ==================================================================== --}}
            <div class="modal fade" id="modalExp360_{{ $r->id }}" data-reserva-cod="{{ $r->codigo_reserva }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content reservas-soft" id="printArea_{{ $r->id }}" style="border:none; border-radius:12px; overflow:hidden; box-shadow:0 12px 36px rgba(0,0,0,0.16);">

                        <div class="modal-header bg-white border-bottom py-3 px-4">
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val" style="font-size:13px; color:var(--accent);">#{{ $r->codigo_reserva }}</span>
                                    <span class="val" style="font-size:13px;">· {{ $inm->name ?? 'Inmueble' }}</span>
                                    <span class="pill pill-{{ $pillVista }}">{{ $stVista }}</span>
                                </div>
                                <div class="sub mt-1">
                                    {{ $fi?->format('d/m/Y') }} al {{ $ff?->format('d/m/Y') }} ({{ $noches }} noches) · Origen: {{ $origenNombre }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ $urlPdfReserva }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn-mini js-ap-pdf-link"
                                   data-reserva-cod="{{ $r->codigo_reserva }}"
                                   style="color:#b91c1c; border-color:#fecaca; background:#fef2f2;"
                                   title="Descargar comprobante PDF oficial">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <button type="button" class="ap-icon-btn" onclick="window.apPrintReserva({{ $r->id }}, '{{ $r->codigo_reserva }}')" title="Imprimir o guardar vista rápida">
                                    <i class="bi bi-printer"></i>
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="font-size:13px;"></button>
                            </div>
                        </div>

                        {{-- Pestañas minimalistas con íconos suaves --}}
                        <div class="px-4 pt-1 bg-white border-bottom">
                            <ul class="nav nav-tabs border-0 gap-2" role="tablist" style="font-size:11.5px; font-weight:600;">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Trazabilidad" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-ruta-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-clock-history me-1"></i> Trazabilidad
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Pagos" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-pagos-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-credit-card me-1"></i> Pagos ({{ $txList->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Huéspedes" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-huespedes-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-people me-1"></i> Huéspedes ({{ $huespedesList->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Soporte" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-mensajes-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-chat-dots me-1"></i> Soporte (<span class="js-msg-cnt-tab-{{ $r->id }}">{{ $msgList->count() }}</span>)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link py-2 px-2 border-0 js-ap-modal-tab"
                                            data-tab-name="Calificar" data-reserva-cod="{{ $r->codigo_reserva }}"
                                            data-bs-toggle="tab" data-bs-target="#exp-review-{{ $r->id }}" type="button" role="tab">
                                        <i class="bi bi-star me-1"></i> Calificar
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="modal-body p-4" style="background:#fafbfd;">
                            @if($enValidacion)
                                <div class="ap-en-validacion-banner mb-3">
                                    <i class="bi bi-hourglass-split" style="font-size:18px;"></i>
                                    <span><strong>Pendiente validación pago.</strong> Recibimos tu soporte de pago; contabilidad lo está revisando. Te avisaremos cuando la reserva quede confirmada.</span>
                                </div>
                            @endif
                            @if($faltaSoporte)
                                <a href="{{ $urlSubirPago }}" class="ap-subir-pago-banner mb-3">
                                    <i class="bi bi-exclamation-triangle-fill" style="font-size:18px;"></i>
                                    <span style="flex:1;">
                                        <strong>Pendiente subir soporte de pago.</strong>
                                        @if($txRechazada)
                                            Contabilidad <strong>rechazó</strong> el soporte del {{ optional($txRechazada->created_at)->format('d/m/Y') }}; sube un comprobante válido.
                                        @endif
                                        @if($limitePago && $limitePago->lt(now()->startOfDay()))
                                            El plazo venció el <strong>{{ $limitePago->locale('es')->translatedFormat('l j \d\e F') }}</strong>; la reserva se cancelará automáticamente.
                                        @elseif($limitePago)
                                            Tienes hasta el <strong>{{ $limitePago->locale('es')->translatedFormat('l j \d\e F') }}</strong>; si no se recibe, la reserva se cancelará automáticamente.
                                        @else
                                            Sube el comprobante para confirmar tu reserva.
                                        @endif
                                    </span>
                                    <span class="ap-subir-pago-cta">Subir pago <i class="bi bi-arrow-right"></i></span>
                                </a>
                            @endif
                            <div class="tab-content">

                                {{-- TAB 1: TRAZABILIDAD Y TARIFA --}}
                                <div class="tab-pane fade show active" id="exp-ruta-{{ $r->id }}" role="tabpanel">
                                    @if($tarifaSnap)
                                        <div class="p-3 bg-white border rounded-3 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color:var(--grid-2) !important;">
                                            <div>
                                                <div class="lbl">Tarifa de Temporada</div>
                                                <div class="val">{{ $tarifaSnap->nombre_temporada ?? 'Estándar' }}</div>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <span class="ap-meta-chip">Aporte Mín.: <b>${{ number_format((float)$tarifaSnap->precio_minimo_reserva, 0) }}</b></span>
                                                <span class="ap-meta-chip">Máx. Días: <b>{{ $tarifaSnap->dias_maximos ?? '—' }}</b></span>
                                                <span class="ap-meta-chip">Máx. Anual: <b>{{ $tarifaSnap->max_reservas_anuales_cliente ?? '—' }}</b></span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="rsv-timeline">
                                        @foreach($timeline as $item)
                                            <div class="rsv-timeline-item">
                                                <div class="rsv-timeline-dot dot-{{ $item->color }}"></div>
                                                <div class="rsv-timeline-content">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="val">{{ $item->titulo }}</span>
                                                        <span class="sub">{{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y h:i A') }}</span>
                                                    </div>
                                                    <div class="sub" style="color:var(--ink-2);">{!! $item->desc !!}</div>
                                                    @if(!empty($item->comentario))
                                                        <div class="mt-1 p-2 rounded bg-light fst-italic sub">"{{ $item->comentario }}"</div>
                                                    @endif
                                                    @if(!empty($item->usuario))
                                                        <div class="sub mt-1">{{ $item->usuario }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- TAB 2: PAGOS --}}
                                <div class="tab-pane fade" id="exp-pagos-{{ $r->id }}" role="tabpanel">
                                    @php
                                        // Se puede reportar pago si la reserva sigue vigente y no está saldada
                                        // Siempre se puede subir otro pago (o reemplazar un soporte errado) mientras
                                        // la reserva siga vigente y lo APROBADO por contabilidad no cubra el total.
                                        $montoAprobado = (float) $txList->filter(fn ($tx) => str_contains(mb_strtolower((string) $tx->estado_pago), 'aprob'))->sum('monto');
                                        $puedePagar = !in_array($stGroup, ['danger', 'primary'], true)
                                            && ($montoTotal <= 0 || $montoAprobado < $montoTotal);
                                        $urlPagarReserva = route('rsv.cliente.portal') . '?tab=pagos-transacciones&reserva_id=' . $r->id . '&subir=1';
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        @php $txFallidos = $txList->reject(fn ($t) => $t->tieneSoporte())->count(); @endphp
                                        <span class="sub" style="font-size:12px;">
                                            {{ $txList->count() - $txFallidos }} pago(s) con soporte
                                            @if($txFallidos) · {{ $txFallidos }} intento(s) sin archivo (no cuentan) @endif
                                        </span>
                                        @if($puedePagar)
                                            <a href="{{ $urlPagarReserva }}" class="ap-btn-accion">
                                                <i class="bi bi-cloud-arrow-up"></i> {{ $txList->isEmpty() ? 'Subir pago' : 'Subir otro pago' }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="p-3 bg-white border rounded-3 mb-3" style="border-color:var(--grid-2) !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="sub">Abonado ({{ $pctPago }}%)</span>
                                            <span class="val">${{ number_format($montoPagado, 0) }} / ${{ number_format($montoTotal, 0) }} COP</span>
                                        </div>
                                        <div class="progress" style="height:5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctPago }}%;"></div>
                                        </div>
                                    </div>

                                    @forelse($txList as $tx)
                                        @php
                                            $pas = $tx->pasarela;
                                            $dotColor = $pas && $pas->color ? $pas->color : '#3d7a5c';
                                            $urlSoporte = $tx->url_soporte_pago ?? '#';
                                            $txFallido = !$tx->tieneSoporte();
                                        @endphp
                                        <div class="p-2 px-3 bg-white border rounded-3 mb-2 d-flex justify-content-between align-items-center flex-wrap gap-2"
                                             style="border-color:var(--grid) !important; {{ $txFallido ? 'opacity:.6; background:#f8fafc !important;' : '' }}">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="ap-dot-indicator" style="background:{{ $txFallido ? '#94a3b8' : $dotColor }};"></span>
                                                    @if($txFallido)
                                                        <span class="val">Intento de pago sin archivo</span>
                                                        <span class="pill pill-danger">No válido</span>
                                                    @else
                                                        <span class="val">{{ $pas->nombre ?? ($tx->metodo_pago ?? 'Pago') }}</span>
                                                        <span class="pill pill-{{ $pillSt($tx->estado_pago) }}">{{ $tx->estado_pago ?? 'Registrado' }}</span>
                                                    @endif
                                                </div>
                                                <div class="sub mt-1">
                                                    @if($txFallido && $pas) Canal elegido: {{ $pas->nombre }} · @endif
                                                    Ref: {{ $tx->referencia_externa ?? 'S/R' }} · {{ optional($tx->created_at)->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="val" style="color:{{ $txFallido ? '#94a3b8' : 'var(--accent)' }}; {{ $txFallido ? 'text-decoration:line-through;' : '' }}">${{ number_format((float)$tx->monto, 0) }}</span>
                                                @if($urlSoporte && $urlSoporte !== '#')
                                                    <a href="{{ $urlSoporte }}" target="_blank" rel="noopener"
                                                       class="ap-btn-accion ap-btn-accion-sec js-ap-soporte-link"
                                                       data-ref="{{ $tx->referencia_externa ?? $tx->id }}"
                                                       title="Abrir el soporte de pago en una pestaña nueva">
                                                        <i class="bi bi-file-earmark-text"></i> Ver soporte
                                                    </a>
                                                @elseif($tx->tieneSoporte())
                                                    <span class="sub" title="El archivo existe pero no se pudo generar el enlace de descarga">
                                                        <i class="bi bi-paperclip"></i> Soporte cargado
                                                    </span>
                                                @else
                                                    <span class="sub" style="color:#b91c1c;" title="El archivo no se guardó; sube el pago de nuevo">
                                                        <i class="bi bi-exclamation-circle"></i> Archivo no recibido
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 bg-white border rounded-3 sub">Sin pagos registrados aún.</div>
                                    @endforelse
                                </div>

                                {{-- TAB 3: HUÉSPEDES · gestión directa (agregar / editar / retirar) respetando el cupo --}}
                                <div class="tab-pane fade" id="exp-huespedes-{{ $r->id }}" role="tabpanel">
                                    @php
                                        // Regla del inmueble: titular + máx. acompañantes de la tarifa (≤ capacidad − 1).
                                        // El servidor (ReservaHuespedController store/update) aplica la MISMA regla.
                                        $maxAcompR  = \App\Http\Controllers\Rsv\ReservaHuespedController::maxAcompanantesDe($r);
                                        $cupoR      = $maxAcompR + 1;
                                        $acompR     = $huespedesList->where('es_titular', false)->count();
                                        $libresR    = max(0, $maxAcompR - $acompR);
                                        $editableR  = !in_array($stGroup, ['danger', 'primary'], true);
                                        $tieneTitR  = $huespedesList->contains(fn ($x) => (bool) $x->es_titular);
                                        $urlHuStore = route('rsv.reserva-huespedes.store');
                                        $yo         = auth()->user();
                                        $partesYo   = preg_split('/\s+/', trim((string) ($yo->name ?? '')), -1, PREG_SPLIT_NO_EMPTY);
                                        $mitadYo    = max(1, (int) ceil(count($partesYo) / 2));
                                        $nomYo      = implode(' ', array_slice($partesYo, 0, $mitadYo));
                                        $apeYo      = implode(' ', array_slice($partesYo, $mitadYo));
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <span class="sub" style="font-size:12px;">
                                            {{ $huespedesList->count() }} de {{ $cupoR }} cupos ·
                                            titular + hasta <b>{{ $maxAcompR }}</b> acompañante{{ $maxAcompR === 1 ? '' : 's' }}
                                            @if($libresR === 0) · <b style="color:#b45309;">cupo completo</b>
                                            @else · <b style="color:var(--accent);">{{ $libresR }} disponible{{ $libresR === 1 ? '' : 's' }}</b> @endif
                                        </span>
                                        @if($editableR && $tieneTitR && $libresR > 0)
                                            <button type="button" class="ap-btn-accion" data-bs-toggle="collapse" data-bs-target="#apHuAdd_{{ $r->id }}">
                                                <i class="bi bi-person-plus-fill"></i> Agregar acompañante
                                            </button>
                                        @endif
                                    </div>

                                    <div class="d-none mb-2" id="apHuAlert_{{ $r->id }}"></div>

                                    @if(!$editableR)
                                        <div class="sub mb-2">Esta reserva está en estado <b>{{ $st }}</b>; los huéspedes se muestran solo para consulta.</div>
                                    @endif

                                    {{-- Sin titular registrado (reservas antiguas): primero incluirse como titular --}}
                                    @if($editableR && !$tieneTitR)
                                        <form action="{{ $urlHuStore }}" method="POST" class="js-ap-hu-form ap-hu-box mb-2" data-reserva-id="{{ $r->id }}" data-op="titular">
                                            @csrf
                                            <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                            <input type="hidden" name="es_titular" value="1">
                                            <div class="val mb-2" style="font-size:12.5px;"><i class="bi bi-person-check"></i> Primero inclúyete como titular de la reserva</div>
                                            <div class="row g-2 align-items-end">
                                                <div class="col-6 col-md-3"><label class="lbl d-block mb-1">Nombres</label><input type="text" name="nombre" class="form-control form-control-sm" value="{{ $nomYo }}" required maxlength="120"></div>
                                                <div class="col-6 col-md-3"><label class="lbl d-block mb-1">Apellidos</label><input type="text" name="apellidos" class="form-control form-control-sm" value="{{ $apeYo }}" required maxlength="120"></div>
                                                <div class="col-4 col-md-2"><label class="lbl d-block mb-1">Tipo</label><select name="tipo_documento" class="form-select form-select-sm">@foreach(['CC','CE','PAS'] as $td)<option value="{{ $td }}">{{ $td }}</option>@endforeach</select></div>
                                                <div class="col-8 col-md-2"><label class="lbl d-block mb-1">Documento</label><input type="text" name="numero_documento" class="form-control form-control-sm" value="{{ $yo->nid ?? '' }}" required maxlength="40"></div>
                                                <div class="col-12 col-md-2"><button type="submit" class="ap-btn-accion w-100 justify-content-center">Incluirme</button></div>
                                            </div>
                                        </form>
                                    @endif

                                    {{-- Nuevo acompañante (solo si hay cupo) --}}
                                    @if($editableR && $tieneTitR && $libresR > 0)
                                        <div class="collapse mb-2" id="apHuAdd_{{ $r->id }}">
                                            <form action="{{ $urlHuStore }}" method="POST" class="js-ap-hu-form ap-hu-box" data-reserva-id="{{ $r->id }}" data-op="agregar">
                                                @csrf
                                                <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                                <input type="hidden" name="es_titular" value="0">
                                                <div class="val mb-2" style="font-size:12.5px;">Acompañante {{ $acompR + 1 }} de {{ $maxAcompR }}</div>
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-6 col-md-3"><label class="lbl d-block mb-1">Nombres</label><input type="text" name="nombre" class="form-control form-control-sm" required maxlength="120"></div>
                                                    <div class="col-6 col-md-3"><label class="lbl d-block mb-1">Apellidos</label><input type="text" name="apellidos" class="form-control form-control-sm" required maxlength="120"></div>
                                                    <div class="col-4 col-md-2"><label class="lbl d-block mb-1">Tipo</label><select name="tipo_documento" class="form-select form-select-sm">@foreach(['CC','TI','RC','CE','PAS'] as $td)<option value="{{ $td }}">{{ $td }}</option>@endforeach</select></div>
                                                    <div class="col-8 col-md-2"><label class="lbl d-block mb-1">Documento</label><input type="text" name="numero_documento" class="form-control form-control-sm" required maxlength="40"></div>
                                                    <div class="col-12 col-md-2"><button type="submit" class="ap-btn-accion w-100 justify-content-center"><i class="bi bi-check2"></i> Guardar</button></div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- Listado: titular (solo lectura) + acompañantes (editar / retirar) --}}
                                    @forelse($huespedesList->sortByDesc('es_titular')->values() as $h)
                                        <div class="ap-hu-item mb-2">
                                            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                                <div>
                                                    <div class="val" style="font-size:12.5px;">{{ $h->nombre }} {{ $h->apellidos }}</div>
                                                    <div class="sub" style="font-size:11px;">{{ $h->tipo_documento }} · {{ $h->numero_documento }}</div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="pill {{ $h->es_titular ? 'pill-success' : 'pill-secondary' }}">{{ $h->es_titular ? 'Titular' : 'Acompañante' }}</span>
                                                    @if($editableR && !$h->es_titular)
                                                        <button type="button" class="ap-btn-accion ap-btn-accion-sec" data-bs-toggle="collapse" data-bs-target="#apHuEdit_{{ $h->id }}" title="Editar datos">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="ap-btn-accion ap-btn-accion-del js-ap-hu-del"
                                                                data-url="{{ route('rsv.reserva-huespedes.destroy', $h->id) }}"
                                                                data-reserva-id="{{ $r->id }}"
                                                                data-nombre="{{ $h->nombre }} {{ $h->apellidos }}" title="Retirar acompañante">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($editableR && !$h->es_titular)
                                                <div class="collapse" id="apHuEdit_{{ $h->id }}">
                                                    <form action="{{ route('rsv.reserva-huespedes.update', $h->id) }}" method="POST" class="js-ap-hu-form mt-2" data-reserva-id="{{ $r->id }}" data-op="editar">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-6 col-md-3"><input type="text" name="nombre" class="form-control form-control-sm" value="{{ $h->nombre }}" required maxlength="120"></div>
                                                            <div class="col-6 col-md-3"><input type="text" name="apellidos" class="form-control form-control-sm" value="{{ $h->apellidos }}" required maxlength="120"></div>
                                                            <div class="col-4 col-md-2"><select name="tipo_documento" class="form-select form-select-sm">@foreach(['CC','TI','RC','CE','PAS'] as $td)<option value="{{ $td }}" {{ $h->tipo_documento === $td ? 'selected' : '' }}>{{ $td }}</option>@endforeach</select></div>
                                                            <div class="col-8 col-md-2"><input type="text" name="numero_documento" class="form-control form-control-sm" value="{{ $h->numero_documento }}" required maxlength="40"></div>
                                                            <div class="col-12 col-md-2"><button type="submit" class="ap-btn-accion w-100 justify-content-center"><i class="bi bi-check2"></i> Guardar</button></div>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-4 bg-white border rounded-3 sub">Sin huéspedes registrados.</div>
                                    @endforelse
                                </div>

                                {{-- TAB 4: SOPORTE / CHAT --}}
                                <div class="tab-pane fade" id="exp-mensajes-{{ $r->id }}" role="tabpanel">
                                    {{-- Disponibilidad: agente en línea → chat; si no → ofrecer ticket (lo resuelve JS al abrir la pestaña) --}}
                                    <div class="ap-sop-estado js-sop-estado" data-reserva-id="{{ $r->id }}">
                                        <span class="ap-sop-dot js-sop-dot"></span>
                                        <span class="js-sop-txt">Verificando si hay agentes en línea…</span>
                                    </div>
                                    <div class="ap-chat-box mb-3" id="apChatBox_{{ $r->id }}">
                                        @forelse($msgList as $m)
                                            @php $esMio = ($m->id_user_remitente == $uid); @endphp
                                            @if($m->tipo_mensaje === 'ticket')
                                                @php $tk = $m->ticket; $tkEstado = optional(optional($tk)->estadoSoporte)->nombre ?? 'Sin asignar'; @endphp
                                                <div class="ap-msg-bubble ap-msg-ticket">
                                                    <div class="d-flex justify-content-between align-items-center mb-1" style="gap:8px;">
                                                        <strong style="font-size:10.5px;"><i class="bi bi-ticket-perforated me-1"></i>Ticket #{{ $m->id_scp_soporte }}</strong>
                                                        <span class="badge rounded-pill" style="font-size:9.5px; background:#dbeafe; color:#1e40af;">{{ \Illuminate\Support\Str::headline($tkEstado) }}</span>
                                                    </div>
                                                    <div style="white-space:pre-line;">{{ \Illuminate\Support\Str::after($m->contenido, ': ') }}</div>
                                                    <div style="font-size:10px; opacity:.7;" class="mt-1">Creado {{ optional($m->created_at)->format('d/m/Y H:i') }} · te contactaremos por correo o teléfono</div>
                                                </div>
                                                @continue
                                            @endif
                                            <div class="ap-msg-bubble {{ $esMio ? 'ap-msg-mine' : 'ap-msg-other' }}">
                                                <div class="d-flex justify-content-between align-items-center mb-1" style="gap:8px;">
                                                    <strong style="font-size:10.5px;">
                                                        @if($esMio) Tú
                                                        @else <i class="bi bi-headset me-1"></i>Soporte Corpentunida{{ optional($m->remitente)->name ? ' · ' . \Illuminate\Support\Str::of($m->remitente->name)->title()->words(2, '') : '' }}
                                                        @endif
                                                    </strong>
                                                    <span style="font-size:10px; opacity:.7;">{{ optional($m->created_at)->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <div style="white-space:pre-line;">{{ $m->contenido }}</div>
                                                @if(!empty($m->url_archivo))
                                                    <a href="{{ $m->url_archivo }}" target="_blank" rel="noopener" class="d-inline-block mt-1" style="font-size:11px; font-weight:700;">
                                                        <i class="bi bi-paperclip"></i> Ver archivo adjunto
                                                    </a>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="text-center py-4 sub js-empty-chat">Escribe cualquier duda sobre tu reserva #{{ $r->codigo_reserva }}.</div>
                                        @endforelse
                                    </div>

                                    <div id="apMsgAlert_{{ $r->id }}" class="d-none"></div>

                                    {{-- Sin agente en línea: ofrecer ticket en la plataforma Soportes --}}
                                    <div class="ap-sop-offline js-sop-offline d-none" data-reserva-id="{{ $r->id }}">
                                        <div class="js-sop-oferta">
                                            <div class="mb-2"><i class="bi bi-moon-stars me-1"></i>
                                                En este momento <strong>no hay agentes en línea</strong>. ¿Deseas crear un ticket de soporte? Nuestro equipo lo atenderá y te contactará.
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary js-sop-abrir-ticket" style="font-size:11px;">
                                                <i class="bi bi-ticket-perforated me-1"></i> Sí, crear ticket de soporte
                                            </button>
                                        </div>
                                        <form class="js-sop-ticket-form d-none mt-1" data-reserva-id="{{ $r->id }}" enctype="multipart/form-data"
                                              action="{{ route('rsv.cliente.soporte.ticket') }}">
                                            @csrf
                                            <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                            <label class="form-label mb-1" style="font-size:11px; font-weight:700;">¿Qué necesitas? (reserva #{{ $r->codigo_reserva }})</label>
                                            <textarea name="descripcion" class="form-control form-control-sm mb-2" rows="4" minlength="10" maxlength="2000" required
                                                      style="font-size:11.5px;" placeholder="Describe el problema o la solicitud con el mayor detalle posible."></textarea>
                                            <label class="form-label mb-1" style="font-size:11px;">Adjunto (opcional · PDF, JPG o PNG, máx. 10 MB)</label>
                                            <input type="file" name="adjunto" class="form-control form-control-sm mb-2" accept=".pdf,.jpg,.jpeg,.png" style="font-size:11px;">
                                            <div class="js-sop-ticket-alert d-none"></div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-primary" style="font-size:11px;"><i class="bi bi-send me-1"></i> Enviar ticket</button>
                                                <button type="button" class="btn btn-sm btn-light js-sop-cancelar-ticket" style="font-size:11px;">Cancelar</button>
                                            </div>
                                        </form>
                                    </div>

                                    @if($rutaStoreMensaje)
                                        <form action="{{ $rutaStoreMensaje }}" method="POST"
                                              class="js-ap-ajax-form js-sop-chat d-none"
                                              data-tipo="mensaje"
                                              data-reserva-id="{{ $r->id }}"
                                              data-reserva="{{ $r->codigo_reserva }}">
                                            @csrf
                                            <input type="hidden" name="_ajax" value="1">
                                            <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                            <input type="hidden" name="id_rsv_reserva" value="{{ $r->id }}">
                                            <input type="hidden" name="reserva_id" value="{{ $r->id }}">
                                            <input type="hidden" name="id_user_remitente" value="{{ $uid }}">
                                            <input type="hidden" name="id_user" value="{{ $uid }}">
                                            <input type="hidden" name="user_id" value="{{ $uid }}">
                                            <input type="hidden" name="tipo_mensaje" value="texto">

                                            <div class="input-group input-group-sm">
                                                <input type="text" name="contenido" class="form-control" style="font-size:11.5px;"
                                                       placeholder="Escribe un mensaje…" required maxlength="1000"
                                                       oninput="this.form.querySelector('[name=mensaje]').value = this.value">
                                                <input type="hidden" name="mensaje" value="">
                                                <button class="btn btn-success" type="submit" style="font-size:11px; background:var(--accent); border-color:var(--accent);">
                                                    Enviar
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>

                                {{-- TAB 5: CALIFICACIÓN (REVIEW) --}}
                                <div class="tab-pane fade" id="exp-review-{{ $r->id }}" role="tabpanel">
                                    @if($miReview)
                                        <div class="p-3 bg-white border rounded-3 text-center" style="border-color:var(--grid) !important;">
                                            <div class="mb-1" style="font-size:16px; color:#f59e0b;">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi {{ $i <= (int)$miReview->puntuacion ? 'bi-star-fill' : 'bi-star' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="val mb-1">"{{ $miReview->comentario ?? 'Sin comentario.' }}"</p>
                                            <span class="sub">{{ optional($miReview->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                    @else
                                        <div class="p-3 bg-white border rounded-3" style="border-color:var(--grid) !important;">
                                            <div id="apRevAlert_{{ $r->id }}" class="d-none"></div>

                                            @if($rutaStoreReview)
                                                <form action="{{ $rutaStoreReview }}" method="POST"
                                                      class="js-ap-ajax-form"
                                                      data-tipo="review"
                                                      data-reserva-id="{{ $r->id }}"
                                                      data-reserva="{{ $r->codigo_reserva }}">
                                                    @csrf
                                                    <input type="hidden" name="_ajax" value="1">
                                                    <input type="hidden" name="id_rsv_reservas" value="{{ $r->id }}">
                                                    <input type="hidden" name="id_rsv_reserva" value="{{ $r->id }}">
                                                    <input type="hidden" name="reserva_id" value="{{ $r->id }}">
                                                    <input type="hidden" name="id_user" value="{{ $uid }}">
                                                    <input type="hidden" name="id_user_autor" value="{{ $uid }}">
                                                    <input type="hidden" name="user_id" value="{{ $uid }}">

                                                    <div class="row g-2 mb-2">
                                                        <div class="col-12 col-sm-6">
                                                            <label class="lbl d-block mb-1">Receptor</label>
                                                            @if($tiposReceptor->count() > 0)
                                                                <select name="id_rsv_tipo_receptor" class="form-select form-select-sm" style="font-size:11.5px;" required
                                                                        onchange="this.form.querySelector('[name=id_rsv_tipos_receptor]').value = this.value">
                                                                    @foreach($tiposReceptor as $tr)
                                                                        <option value="{{ $tr->id }}">{{ $tr->nombre ?? $tr->name ?? ('Receptor #' . $tr->id) }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                            @else
                                                                <input type="hidden" name="id_rsv_tipo_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                                <input type="hidden" name="id_rsv_tipos_receptor" value="{{ $tipoReceptorDefaultId }}">
                                                                <input type="text" class="form-control form-control-sm" style="font-size:11.5px;" value="Estadía General" disabled>
                                                            @endif
                                                        </div>
                                                        <div class="col-12 col-sm-6">
                                                            <label class="lbl d-block mb-1">Aspecto</label>
                                                            <select name="tipo_evaluacion" class="form-select form-select-sm" style="font-size:11.5px;" required>
                                                                <option value="General">Estadía General</option>
                                                                <option value="Inmueble">Inmueble</option>
                                                                <option value="Limpieza">Limpieza</option>
                                                                <option value="Atencion">Atención</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 text-center">
                                                        <div class="ap-star-group">
                                                            @for($s = 5; $s >= 1; $s--)
                                                                <input type="radio" id="star_{{ $r->id }}_{{ $s }}" name="puntuacion" value="{{ $s }}" {{ $s === 5 ? 'checked' : '' }}
                                                                       onchange="this.form.querySelector('[name=calificacion]').value = this.value" required>
                                                                <label for="star_{{ $r->id }}_{{ $s }}"><i class="bi bi-star-fill"></i></label>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="calificacion" value="5">
                                                    </div>

                                                    <div class="mb-2">
                                                        <textarea name="comentario" rows="2" class="form-control form-control-sm" style="font-size:11.5px;" placeholder="Escribe un breve comentario…" required></textarea>
                                                    </div>

                                                    <button type="submit" class="btn-mini btn-mini-accent w-100 justify-content-center py-2">
                                                        Guardar Calificación
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-white border-top py-2 px-4 justify-content-between">
                            <button type="button"
                                    class="btn-mini js-ap-pin-reserva"
                                    data-reserva-id="{{ $r->id }}"
                                    data-reserva-cod="{{ $r->codigo_reserva }}"
                                    style="color:var(--accent);">
                                <i class="bi bi-pin-angle"></i> Fijar esta reserva activa
                            </button>
                            <button type="button" class="btn-mini" data-bs-dismiss="modal">Cerrar</button>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                <span class="sub d-block mb-2">Aún no tienes reservas registradas.</span>
                <button type="button" class="btn-mini btn-mini-accent js-ap-go-nueva">
                    Crear Reserva
                </button>
            </div>
        @endforelse

        @if($mis->isNotEmpty())
            <div id="apEmptyFilterMsg" class="text-center py-4 bg-light border rounded-3 d-none" style="border-style:dashed !important;">
                <span class="sub d-block">No hay reservas en esta categoría o que coincidan con tu búsqueda.</span>
            </div>
        @endif
        </div>

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL AP-v4.2-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.ApDbg && window.ApDbg.alive && window.ApDbg.alive())
        ? window.ApDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-activas-pasadas-cliente') || document.body;
                if (!$('apDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'apDbgToggle'; t.title = 'Debug Activas y Pasadas (AP-v4.2-DBG)';
                    t.innerHTML = '<span class="ap-dbg-led" id="apDbgLed"></span>DBG<span class="ap-dbg-badge" id="apDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('apDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'apDbgPanel';
                    p.innerHTML = '<div class="ap-dbg-header" id="apDbgHeader">' +
                        '<span>DEBUG · Activas y Pasadas · AP-v4.2-DBG</span>' +
                        '<span class="ap-dbg-controls">' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgCopy">Copiar</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgClear">Limpiar</button>' +
                        '<button type="button" class="ap-dbg-btn" id="apDbgClose">✕</button>' +
                        '</span></div><div id="apDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'itDbgToggle', 'huDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('apDbgToggle'), p = $('apDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('apDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ap-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="ap-dbg-time">[' + hora() + ']</span><span class="ap-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('apDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('apDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('apDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('apDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('apDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('apDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('apDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('apDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('apDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Activas y Pasadas AP-v4.2-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('apDbgToggle'); }
            };
            window.ApDbg = api;
            return api;
        })();

    DBG.ensure();
    var apdbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#apDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#apDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // Limpiar cualquier modal/backdrop abierto antes de reemplazar el DOM del fragmento
    function limpiarModalesAbiertos() {
        document.querySelectorAll('.modal.show').forEach(function (m) {
            try {
                if (window.bootstrap && window.bootstrap.Modal) {
                    var inst = window.bootstrap.Modal.getInstance(m);
                    if (inst) inst.hide();
                }
            } catch (e) {}
            m.classList.remove('show');
        });
        document.querySelectorAll('.modal-backdrop').forEach(function (bd) { bd.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function actualizarContadoresChips() {
        var rows = document.querySelectorAll('#apReservasList .ap-res-row');
        var porEstado = {};
        rows.forEach(function (r) {
            var sid = r.getAttribute('data-st-key') || '';
            porEstado[sid] = (porEstado[sid] || 0) + 1;
        });
        var elAll = document.getElementById('apCntAll');
        if (elAll) elAll.textContent = rows.length;
        document.querySelectorAll('#cliMisResRoot [data-cnt-key]').forEach(function (el) {
            el.textContent = porEstado[el.getAttribute('data-cnt-key')] || 0;
        });
    }

    // ═══ RECARGA DEL FRAGMENTO VÍA ClientePortalController@misReservasActivasFragmento (SIN RECARGAR PÁGINA) ═══
    window.apReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliMisResRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'reservas-activas';
        var resId    = reservaIdOpcional || root.getAttribute('data-reserva-sel') || '';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (resId) urlObj.searchParams.set('reserva_id', resId);
        urlObj.searchParams.set('_t', Date.now());

        try {
            var navUrl = new URL(window.location.href);
            if (resId) navUrl.searchParams.set('reserva_id', resId);
            window.history.replaceState({}, document.title, navUrl.toString());
        } catch (e) {}

        var t0 = performance.now();
        apdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                apdbg('Error HTTP ' + res.status + ' al refrescar fragmento (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliMisResRoot');
            if (nuevoRoot) {
                limpiarModalesAbiertos();
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                actualizarContadoresChips();
                var tot = nuevoRoot.getAttribute('data-total') || '0';
                var sel = nuevoRoot.getAttribute('data-reserva-sel') || 'ninguna';
                apdbg('Fragmento actualizado (' + res.ms + 'ms) · total=' + tot + ' · fijada=' + sel, 'ok');
            } else {
                apdbg('Respuesta recibida (' + res.ms + 'ms), pero sin #cliMisResRoot', 'warn');
            }
        })
        .catch(function (err) {
            apdbg('Excepción recargando fragmento: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // ═══ HUÉSPEDES DENTRO DEL DETALLE: agregar / editar / retirar sin salir del modal ═══
    // El servidor valida el cupo (ReservaHuespedController); aquí solo se envía y se refresca.
    function apHuMostrarMsg(rid, ok, html) {
        var box = document.getElementById('apHuAlert_' + rid);
        if (!box) return;
        box.className = 'mb-2 ' + (ok ? 'ap-hu-ok' : 'ap-hu-err');
        box.innerHTML = html;
    }
    function apHuReabrir(rid, ok, msg) {
        return window.apReloadFragment(rid, 'huespedes-detalle').then(function () {
            var modal = document.getElementById('modalExp360_' + rid);
            if (modal && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
                var tab = document.querySelector('[data-bs-target="#exp-huespedes-' + rid + '"]');
                if (tab && window.bootstrap.Tab) window.bootstrap.Tab.getOrCreateInstance(tab).show();
            }
            apHuMostrarMsg(rid, ok, msg);
        });
    }
    function apHuEnviar(url, fd, rid, okMsg) {
        var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = fd.get('_token') || (tokMt && tokMt.content) || '';
        return fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: fd
        })
        .then(function (r) {
            return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, status: r.status, j: j }; });
        })
        .then(function (res) {
            if (res.ok && res.j.success !== false) {
                apdbg('Huéspedes (detalle) OK · HTTP ' + res.status, 'ok');
                return apHuReabrir(rid, true, '<i class="bi bi-check-circle-fill me-1"></i> ' + okMsg);
            }
            var errs = [];
            if (res.j.errors) Object.keys(res.j.errors).forEach(function (k) { errs = errs.concat(res.j.errors[k]); });
            apdbg('Huéspedes (detalle) error HTTP ' + res.status + ': ' + (res.j.message || ''), 'err');
            apHuMostrarMsg(rid, false, '<b>' + escHtml(res.j.message || 'No se pudo guardar.') + '</b>' +
                (errs.length ? '<ul class="mb-0 mt-1 ps-3">' + errs.map(function (m) { return '<li>' + escHtml(m) + '</li>'; }).join('') + '</ul>' : ''));
        })
        .catch(function () { apHuMostrarMsg(rid, false, 'Error de conexión. Intenta de nuevo.'); });
    }
    function escHtml(s) {
        return String(s || '').replace(/[<>&"]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : (c === '&' ? '&amp;' : '&quot;')); });
    }
    if (!window.__apHuDetalleListo) {
        window.__apHuDetalleListo = true;
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('js-ap-hu-form')) return;
            e.preventDefault();
            var rid = form.getAttribute('data-reserva-id');
            var op  = form.getAttribute('data-op');
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            var okMsg = op === 'agregar' ? 'Acompañante agregado.' : (op === 'titular' ? 'Quedaste incluido como titular.' : 'Datos actualizados.');
            apHuEnviar(form.getAttribute('action'), new FormData(form), rid, okMsg)
                .finally(function () { if (btn) btn.disabled = false; });
        });
        document.addEventListener('click', function (e) {
            var del = e.target && e.target.closest ? e.target.closest('.js-ap-hu-del') : null;
            if (!del) return;
            e.preventDefault();
            var nom = del.getAttribute('data-nombre') || 'el acompañante';
            if (!confirm('¿Retirar a ' + nom + ' de esta reserva?')) return;
            var fd = new FormData();
            fd.append('_method', 'DELETE');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var tokIn = document.querySelector('#cliMisResRoot input[name="_token"]');
            fd.append('_token', (tokIn && tokIn.value) || (tokMt && tokMt.content) || '');
            apHuEnviar(del.getAttribute('data-url'), fd, del.getAttribute('data-reserva-id'), nom + ' fue retirado de la reserva.');
        });
    }

    window.apPrintReserva = function (id, codigo) {
        apdbg('Click: Imprimir vista rápida de reserva #' + codigo + ' (id=' + id + ')', 'ev');
        var el = document.getElementById('printArea_' + id);
        if (!el) { window.print(); return; }
        var w = window.open('', '_blank', 'width=850,height=700');
        if (!w) { window.print(); return; }
        w.document.write('<html><head><title>Reserva #' + codigo + '</title>');
        w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        w.document.write('<style>body{font-family:sans-serif;padding:24px;} .btn-close,.nav-tabs,form,.ap-icon-btn,.btn-mini{display:none !important;} .tab-pane{display:block !important;opacity:1 !important;margin-bottom:20px;}</style>');
        w.document.write('</head><body>' + el.innerHTML + '</body></html>');
        w.document.close();
        setTimeout(function () { w.focus(); w.print(); }, 400);
    };

    function aplicarFiltros(logAccion) {
        var chipActivo = document.querySelector('#cliMisResRoot .ap-filter-chip.active');
        var grupo = chipActivo ? chipActivo.getAttribute('data-filter') : 'all';
        var q = (document.getElementById('apSearchInput')?.value || '').toLowerCase().trim();
        var visibles = 0;

        document.querySelectorAll('#apReservasList .ap-res-row').forEach(function (row) {
            var matchGrupo = (grupo === 'all') || ((row.getAttribute('data-st-key') || '') === grupo);
            var matchTexto = !q || (row.getAttribute('data-search') || '').indexOf(q) !== -1;
            var mostrar = matchGrupo && matchTexto;
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var emptyMsg = document.getElementById('apEmptyFilterMsg');
        if (emptyMsg) {
            emptyMsg.classList.toggle('d-none', visibles > 0);
        }

        if (logAccion) {
            apdbg('Filtro aplicado · grupo="' + grupo + '" · q="' + q + '" · visibles=' + visibles, 'info');
        }
    }

    // Registrar handlers globales una sola vez
    if (!window.__jsActivasPasadasV4Listo) {
        window.__jsActivasPasadasV4Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#apDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#apDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#apDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#apDbgCopy'))    { e.stopPropagation(); DBG.copy(); apdbg('Bitácora copiada al portapapeles', 'info'); return; }
            if (t.closest('#apDbgRefresh')) {
                e.stopPropagation();
                window.apReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // Descarga PDF oficial
            var pdfBtn = t.closest('.js-ap-pdf-link');
            if (pdfBtn) {
                var codPdf = pdfBtn.getAttribute('data-reserva-cod') || '?';
                apdbg('Click: Descargar Comprobante PDF oficial de #' + codPdf, 'net');
                return;
            }

            // 1. Fijar reserva activa sin recargar la página (sincroniza Itinerario y Huéspedes)
            var pinBtn = t.closest('.js-ap-pin-reserva');
            if (pinBtn) {
                e.preventDefault();
                e.stopPropagation();
                var rid = pinBtn.getAttribute('data-reserva-id') || '';
                var cod = pinBtn.getAttribute('data-reserva-cod') || '';
                apdbg('Click: Fijar reserva activa #' + cod + ' (id=' + rid + ') sin recargar vista', 'ev');

                // Actualizar estado visual inmediato en las filas
                document.querySelectorAll('#apReservasList .ap-res-row').forEach(function (row) {
                    var esEsta = row.getAttribute('data-reserva-id') === rid;
                    row.classList.toggle('is-selected', esEsta);
                    var ic = row.querySelector('.js-ap-pin-reserva i');
                    if (ic) {
                        ic.className = 'bi ' + (esEsta ? 'bi-pin-fill text-success' : 'bi-pin-angle');
                    }
                });

                var root = document.getElementById('cliMisResRoot');
                if (root) root.setAttribute('data-reserva-sel', rid);

                try {
                    var navUrl = new URL(window.location.href);
                    navUrl.searchParams.set('reserva_id', rid);
                    window.history.replaceState({}, document.title, navUrl.toString());
                } catch (err) {}

                if (window.itReloadFragment) window.itReloadFragment(rid, 'pin-desde-activas');
                if (window.huReloadFragment) window.huReloadFragment(rid, 'pin-desde-activas');
                return;
            }

            // 2. Saltar a la pestaña de Huéspedes desde el modal de Expediente 360°
            var goHues = t.closest('.js-ap-go-huespedes');
            if (goHues) {
                e.preventDefault();
                var ridH = goHues.getAttribute('data-reserva-id') || '';
                var codH = goHues.getAttribute('data-reserva-cod') || '';
                apdbg('Click: Saltar a Gestión de Huéspedes para #' + codH + ' (id=' + ridH + ')', 'ev');
                limpiarModalesAbiertos();
                if (window.huReloadFragment && ridH) {
                    window.huReloadFragment(ridH, 'salto-desde-expediente');
                }
                var tabHues = document.querySelector('[data-bs-target="#mr-sub-huespedes"]');
                if (tabHues) tabHues.click();
                return;
            }

            // 3. Cambio de pestaña interna dentro del Modal Expediente 360°
            var modalTab = t.closest('.js-ap-modal-tab');
            if (modalTab) {
                var tabNom = modalTab.getAttribute('data-tab-name') || 'Tab';
                var resCod = modalTab.getAttribute('data-reserva-cod') || '?';
                apdbg('Click: Pestaña Expediente #' + resCod + ' → ' + tabNom, 'ev');
                return;
            }

            // 4. Apertura de soporte de pago externo
            var sopLink = t.closest('.js-ap-soporte-link');
            if (sopLink) {
                apdbg('Click: Abriendo soporte de pago (Ref: ' + (sopLink.getAttribute('data-ref') || 'S/R') + ')', 'net');
                return;
            }

            // 5. Clic en fila de reserva (abre el modal Expediente 360°)
            var rowClick = t.closest('.ap-res-row');
            if (rowClick) {
                var rCod = rowClick.getAttribute('data-reserva-cod') || '?';
                var rId  = rowClick.getAttribute('data-reserva-id') || '?';
                apdbg('Click: Abriendo Expediente 360° de #' + rCod + ' (id=' + rId + ')', 'ev');
                return;
            }

            // 6. Filtros por chip de estado
            var chip = t.closest('.ap-filter-chip');
            if (chip) {
                document.querySelectorAll('#cliMisResRoot .ap-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltros(true);
                return;
            }

            // 7. Ir a Crear Reserva desde estado vacío
            if (t.closest('.js-ap-go-nueva')) {
                apdbg('Click: Ir a Nueva Reserva', 'ev');
                var tabNueva = document.querySelector('[data-bs-target="#cli-nueva"]');
                if (tabNueva) tabNueva.click();
            }
        });

        // Buscador en vivo con registro en DBG
        var searchTimer = null;
        document.addEventListener('input', function (e) {
            if (e.target && e.target.id === 'apSearchInput') {
                aplicarFiltros(false);
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    aplicarFiltros(true);
                }, 350);
            }
        });

        // ── SOPORTE: agente en línea → chat; sin agente → ofrecer ticket (plataforma Soportes) ──
        var SOP_URL_ESTADO = @json(route('rsv.cliente.soporte.estado'));
        function sopPintar(resId, enLinea, n) {
            var pane = document.getElementById('exp-mensajes-' + resId);
            if (!pane) return;
            var dot = pane.querySelector('.js-sop-dot'), txt = pane.querySelector('.js-sop-txt');
            var chat = pane.querySelector('.js-sop-chat'), off = pane.querySelector('.js-sop-offline');
            if (dot) dot.className = 'ap-sop-dot js-sop-dot ' + (enLinea ? 'on' : 'off');
            if (txt) txt.textContent = enLinea
                ? (n > 1 ? n + ' agentes en línea · escríbenos' : 'Agente en línea · escríbenos')
                : 'Sin agentes en línea en este momento';
            if (chat) chat.classList.toggle('d-none', !enLinea);
            if (off) off.classList.toggle('d-none', !!enLinea);
        }
        function sopConsultar(resId) {
            return fetch(SOP_URL_ESTADO, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (j) { sopPintar(resId, !!j.en_linea, j.agentes || 0); apdbg('Soporte #' + resId + ': ' + (j.en_linea ? j.agentes + ' agente(s) en línea' : 'sin agentes'), 'info'); })
                .catch(function () { sopPintar(resId, false, 0); });
        }
        document.addEventListener('shown.bs.tab', function (e) {
            var tg = e.target && e.target.getAttribute && e.target.getAttribute('data-bs-target');
            if (tg && tg.indexOf('#exp-mensajes-') === 0) sopConsultar(tg.replace('#exp-mensajes-', ''));
        });
        // Modal abierto directamente en la pestaña Soporte (p. ej. desde el correo "Ver la conversación")
        document.addEventListener('shown.bs.modal', function (e) {
            var p = e.target && e.target.querySelector && e.target.querySelector('[id^="exp-mensajes-"].active');
            if (p) sopConsultar(p.id.replace('exp-mensajes-', ''));
        });
        // Mientras la pestaña Soporte esté visible, re-verificar cada 60 s
        setInterval(function () {
            document.querySelectorAll('[id^="exp-mensajes-"].active.show').forEach(function (p) {
                sopConsultar(p.id.replace('exp-mensajes-', ''));
            });
        }, 60000);

        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;
            var ab = t.closest('.js-sop-abrir-ticket'), ca = t.closest('.js-sop-cancelar-ticket');
            if (!ab && !ca) return;
            var box = t.closest('.js-sop-offline');
            box.querySelector('.js-sop-oferta').classList.toggle('d-none', !!ab);
            box.querySelector('.js-sop-ticket-form').classList.toggle('d-none', !ab);
            if (ab) box.querySelector('textarea[name="descripcion"]').focus();
        });

        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('js-sop-ticket-form')) return;
            e.preventDefault();
            var resId = form.getAttribute('data-reserva-id');
            var btn = form.querySelector('button[type="submit"]'), old = btn.innerHTML;
            var al = form.querySelector('.js-sop-ticket-alert');
            al.className = 'js-sop-ticket-alert d-none';
            btn.disabled = true; btn.innerHTML = 'Enviando…';
            var tok = form.querySelector('input[name="_token"]');
            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': tok ? tok.value : '' },
                body: new FormData(form)
            })
            .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (res) {
                if (res.ok && res.j.success) {
                    apdbg('Ticket creado #' + (res.j.ticket ? res.j.ticket.id : '?') + ' para reserva ' + resId, 'ok');
                    var aviso = document.getElementById('apMsgAlert_' + resId);
                    if (aviso) { aviso.className = 'ap-ok-box'; aviso.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> '; aviso.appendChild(document.createTextNode(res.j.message)); }
                    var chatBox = document.getElementById('apChatBox_' + resId);
                    if (chatBox) {
                        var vacio = chatBox.querySelector('.js-empty-chat'); if (vacio) vacio.remove();
                        var b = document.createElement('div');
                        b.className = 'ap-msg-bubble ap-msg-ticket';
                        b.innerHTML = '<div class="d-flex justify-content-between mb-1"><strong style="font-size:10.5px;"><i class="bi bi-ticket-perforated me-1"></i>Ticket #' +
                            (res.j.ticket ? parseInt(res.j.ticket.id, 10) : '') + '</strong><span class="badge rounded-pill" style="font-size:9.5px;background:#dbeafe;color:#1e40af;">Sin Asignar</span></div><div style="white-space:pre-line;"></div>';
                        b.lastChild.textContent = form.querySelector('textarea[name="descripcion"]').value;
                        chatBox.appendChild(b); chatBox.scrollTop = chatBox.scrollHeight;
                    }
                    form.reset();
                    form.classList.add('d-none');
                    form.closest('.js-sop-offline').querySelector('.js-sop-oferta').classList.remove('d-none');
                    return;
                }
                var errs = [];
                if (res.j.errors) Object.keys(res.j.errors).forEach(function (k) { errs = errs.concat(res.j.errors[k]); });
                al.className = 'js-sop-ticket-alert ap-err-box mb-2';
                al.textContent = errs.length ? errs.join(' ') : (res.j.message || 'No se pudo crear el ticket.');
            })
            .catch(function () { al.className = 'js-sop-ticket-alert ap-err-box mb-2'; al.textContent = 'Error de conexión. Intenta de nuevo.'; })
            .finally(function () { btn.disabled = false; btn.innerHTML = old; });
        });

        // Envío AJAX de Mensajes de Soporte y Calificaciones (Reviews) sin recargar toda la página
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('js-ap-ajax-form')) return;
            e.preventDefault();

            var tipo   = form.getAttribute('data-tipo') || 'form';
            var resId  = form.getAttribute('data-reserva-id') || '';
            var cod    = form.getAttribute('data-reserva') || '';
            var btn    = form.querySelector('button[type="submit"]');
            var oldTxt = btn ? btn.innerHTML : '';

            var alertBox = document.getElementById((tipo === 'mensaje' ? 'apMsgAlert_' : 'apRevAlert_') + resId);
            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Enviando…'; }

            var fd = new FormData(form);
            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            apdbg('Enviando (' + tipo + ' #' + cod + ') → ' + form.getAttribute('action'), 'net');

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json, text/html'
                },
                body: fd
            })
            .then(function (r) {
                var ms = Math.round(performance.now() - t0);
                var ct = (r.headers.get('content-type') || '').toLowerCase();
                if (ct.indexOf('application/json') !== -1) {
                    return r.json().then(function (j) { return { ok: r.ok, status: r.status, ms: ms, json: j, isHtml: false }; });
                }
                return r.text().then(function () { return { ok: r.ok, status: r.status, ms: ms, json: { success: r.ok }, isHtml: true }; });
            })
            .then(function (res) {
                var j = res.json || {};
                if (res.ok && (res.isHtml || j.success !== false)) {
                    apdbg('Operación exitosa (' + tipo + ' en #' + cod + ') · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');

                    if (tipo === 'mensaje') {
                        var chatBox = document.getElementById('apChatBox_' + resId);
                        var inp = form.querySelector('input[name="contenido"]');
                        var txt = inp ? inp.value : '';
                        if (chatBox && txt) {
                            var emptyNode = chatBox.querySelector('.js-empty-chat');
                            if (emptyNode) emptyNode.remove();
                            var div = document.createElement('div');
                            div.className = 'ap-msg-bubble ap-msg-mine';
                            div.innerHTML = '<div class="d-flex justify-content-between mb-1"><strong style="font-size:10.5px;">Tú</strong><span style="font-size:10px;opacity:.7;">Ahora</span></div><div></div>';
                            div.lastChild.textContent = txt;
                            chatBox.appendChild(div);
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                        if (inp) inp.value = '';
                        var cntSpan = document.querySelector('.js-msg-cnt-tab-' + resId);
                        if (cntSpan) cntSpan.textContent = parseInt(cntSpan.textContent || '0', 10) + 1;
                    } else {
                        // Review guardada: recargar únicamente el fragmento desde ClientePortalController sin recargar la página
                        if (alertBox) {
                            alertBox.className = 'ap-ok-box';
                            alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Calificación guardada.';
                        }
                        setTimeout(function () {
                            window.apReloadFragment(resId, 'post-review-#' + cod);
                        }, 450);
                    }
                    return;
                }

                apdbg('Error al guardar (' + tipo + ' en #' + cod + ') · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'err');
                // El agente se desconectó entre la verificación y el envío: pasar a la oferta de ticket
                if (tipo === 'mensaje' && j.sin_agente) sopPintar(resId, false, 0);
                var listaErrores = [];
                if (j.errors && typeof j.errors === 'object') {
                    Object.keys(j.errors).forEach(function (campo) {
                        [].concat(j.errors[campo]).forEach(function (m) {
                            listaErrores.push('<li><b>' + campo + ':</b> ' + m + '</li>');
                            apdbg('422 [' + campo + ']: ' + m, 'err');
                        });
                    });
                }
                if (alertBox) {
                    alertBox.className = 'ap-err-box';
                    alertBox.innerHTML = '<b>' + (j.message || 'Error') + '</b>' + (listaErrores.length ? '<ul class="mb-0 mt-1 ps-3">' + listaErrores.join('') + '</ul>' : '');
                }
            })
            .catch(function (err) {
                apdbg('Error de red en (' + tipo + '): ' + (err && err.message ? err.message : 'desconocido'), 'err');
                if (alertBox) { alertBox.className = 'ap-err-box'; alertBox.innerHTML = 'Error de conexión.'; }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldTxt; }
            });
        });
    }

    actualizarContadoresChips();
    aplicarFiltros(false); // aplica el filtro por defecto (Pendiente → Confirmada → En curso → Completada → Todas)

    var SRV = @json($apDbgData);
    apdbg('AP-v4.2-DBG activo · reservas=' + (SRV ? SRV.reservas : 0) +
          (SRV && SRV.conteos ? ' [act=' + SRV.conteos.success + ', pend=' + SRV.conteos.warning + ', fin=' + SRV.conteos.primary + ', canc=' + SRV.conteos.danger + ']' : '') +
          ' · fijada=' + (SRV && SRV.reserva_sel ? '#' + SRV.reserva_sel : 'ninguna'), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { apdbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
