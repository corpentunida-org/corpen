{{-- ============================================================================
     RSV · ADMIN · FINANZAS · PASARELAS · PS-v2.0-DBG
     Parcial: resources/views/rsv/admin/partials/finanzas/pasarela.blade.php
     ============================================================================ --}}

@php
    $psErrores   = [];
    $inmIdActual = isset($inmueble) && $inmueble
        ? $inmueble->id
        : ($inmuebleId ?? request('inmueble_id', 'todos') ?: 'todos');

    // 1. Dataset blindado con autoconsulta si el controlador no envió $pasarelas
    $psItems = isset($pasarelas) && ($pasarelas instanceof \Illuminate\Support\Collection || is_array($pasarelas))
        ? collect($pasarelas)
        : collect();

    try {
        if ($psItems->isEmpty() && class_exists(\App\Models\Rsv\Pasarela::class)) {
            $tablaPas = (new \App\Models\Rsv\Pasarela)->getTable();
            $colNom   = \Illuminate\Support\Facades\Schema::hasColumn($tablaPas, 'nombre') ? 'nombre' : 'id';
            $psItems  = \App\Models\Rsv\Pasarela::orderBy($colNom, 'asc')->get();
        }
    } catch (\Throwable $ePas) {
        $psErrores[] = 'Pasarela: ' . $ePas->getMessage();
    }

    // 2. Estadísticas agregadas en 1 sola query (transacciones y monto por pasarela, evita N+1)
    $psStatsMap = [];
    $psTotalTxGlobal = 0;
    $psTotalMontoGlobal = 0.0;

    try {
        if ($psItems->isNotEmpty() && class_exists(\App\Models\Rsv\TransaccionFinanciera::class)) {
            $tablaTx = (new \App\Models\Rsv\TransaccionFinanciera)->getTable();
            $colFkP  = null;
            foreach (['id_rsv_pasarelas', 'id_rsv_pasarela', 'pasarela_id'] as $cTry) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($tablaTx, $cTry)) {
                    $colFkP = $cTry;
                    break;
                }
            }
            if ($colFkP) {
                $qStats = \App\Models\Rsv\TransaccionFinanciera::query()
                    ->selectRaw($colFkP . ' as pid, COUNT(*) as cnt, COALESCE(SUM(monto), 0) as total_monto')
                    ->groupBy($colFkP);

                if ($inmIdActual !== 'todos' && is_numeric($inmIdActual) && method_exists(\App\Models\Rsv\TransaccionFinanciera::class, 'reserva')) {
                    $qStats->whereHas('reserva', fn ($rq) => $rq->where('id_rsv_catalogo_inmueble', (int) $inmIdActual));
                }

                foreach ($qStats->get() as $stRow) {
                    $pid = (int) ($stRow->pid ?? 0);
                    $c   = (int) ($stRow->cnt ?? 0);
                    $m   = (float) ($stRow->total_monto ?? 0);
                    $psStatsMap[$pid] = ['cnt' => $c, 'monto' => $m];
                    $psTotalTxGlobal += $c;
                    $psTotalMontoGlobal += $m;
                }
            }
        }
    } catch (\Throwable $eSt) {
        $psErrores[] = 'Stats Tx: ' . $eSt->getMessage();
    }

    // 3. Rutas centralizadas con fallback seguro
    $psStore = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.store')
        ? route('rsv.pasarelas.store')
        : null;

    $psBase = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.index')
        ? route('rsv.pasarelas.index')
        : ($psStore ? preg_replace('/\/$/', '', $psStore) : null);

    $psFragmento = '';
    foreach (['rsv.admin.pasarelas.fragmento', 'rsv.admin.finanzas.pasarelas.fragmento', 'rsv.admin.dashboard'] as $rn) {
        if (\Illuminate\Support\Facades\Route::has($rn)) {
            $psFragmento = route($rn, ['inmueble_id' => $inmIdActual]);
            break;
        }
    }

    $psColor = function ($color) {
        $c = strtoupper(trim((string) $color));
        return (preg_match('/^#[0-9A-F]{6}$/', $c) || preg_match('/^#[0-9A-F]{3}$/', $c)) ? $c : '#3D7A5C';
    };

    $cntConUso = 0;
    $cntSinUso = 0;
    foreach ($psItems as $pItem) {
        $cUso = $psStatsMap[$pItem->id]['cnt'] ?? 0;
        if ($cUso > 0) $cntConUso++;
        else $cntSinUso++;
    }

    $iconosSugeridos = [
        'bi-bank'                  => 'Banco / PSE',
        'bi-phone-fill'            => 'Billetera Móvil',
        'bi-credit-card-2-front'   => 'Tarjeta',
        'bi-globe2'                => 'Pasarela Web',
        'bi-cash-coin'             => 'Efectivo / Caja',
        'bi-qr-code-scan'          => 'Código QR',
    ];

    $psDbg = [
        'version'     => 'PS-v2.0-DBG',
        'vista'       => 'partials/finanzas/pasarela.blade.php',
        'render'      => now()->format('H:i:s'),
        'inmueble_id' => $inmIdActual,
        'pasarelas'   => $psItems->count(),
        'con_uso'     => $cntConUso,
        'sin_uso'     => $cntSinUso,
        'tx_asociadas'=> $psTotalTxGlobal,
        'editable'    => (bool) ($psStore || $psBase),
        'rutas'       => [
            'store'     => $psStore     ?: 'NO definida',
            'base'      => $psBase      ?: 'NO definida',
            'fragmento' => $psFragmento ?: 'fallback URL actual',
        ],
        'errores'     => $psErrores,
    ];
@endphp

<div id="sheet-pasarelas"
     data-frag="{{ $psFragmento }}"
     data-inmueble-id="{{ $inmIdActual }}"
     data-total="{{ $psItems->count() }}">

    {{-- PS-v2.0-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3) --}}
    <div id="psDbgToggle" title="Debug Pasarelas (PS-v2.0-DBG)">
        <span class="ps-dbg-led" id="psDbgLed"></span>DBG<span class="ps-dbg-badge" id="psDbgBadge">0</span>
    </div>
    <div id="psDbgPanel" role="dialog" aria-label="Panel de debug de Pasarelas">
        <div class="ps-dbg-header" id="psDbgHeader">
            <span>DEBUG · Pasarelas de Pago · PS-v2.0-DBG</span>
            <span class="ps-dbg-controls">
                <button type="button" class="ps-dbg-btn" id="psDbgRefresh" title="Refrescar catálogo sin recargar página">↻ Frag</button>
                <button type="button" class="ps-dbg-btn" id="psDbgCopy">Copiar</button>
                <button type="button" class="ps-dbg-btn" id="psDbgClear">Limpiar</button>
                <button type="button" class="ps-dbg-btn" id="psDbgClose">✕</button>
            </span>
        </div>
        <div id="psDbgBody"></div>
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
            .reservas-soft .num { font-variant-numeric: tabular-nums; }
            .reservas-soft .sub { font-size: 7.5px; color: var(--ink-3); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-2); font-family: inherit; font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s; }
            .reservas-soft .btn-mini:hover { color: var(--accent); border-color: var(--accent-bd); background: var(--accent-bg); }
            .reservas-soft .btn-green { background: var(--accent); color: #fff; border: 1px solid var(--accent); font-family: inherit; font-size: 8.5px; font-weight: 700; padding: 3px 10px; border-radius: 5px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; }
            .reservas-soft .btn-green:hover { background: #2e5c43; border-color: #2e5c43; }
            .reservas-soft .xls-search { font-size: 8.5px; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 5px; padding: 3px 9px; box-shadow: none !important; outline: 0; min-width: 170px; }
            .reservas-soft .xls-search:focus { background: #fff; border-color: var(--accent-bd); }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 700; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 2px 8px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }
            .reservas-soft .st-dot { display: inline-block; width: 10px; height: 10px; border-radius: 3px; border: 1px solid rgba(0,0,0,.1); vertical-align: middle; }
            .reservas-soft .st-warn { background: #fef2f2; border: 1px solid #fecaca; border-radius: 5px; color: #b91c1c; font-size: 8px; font-weight: 600; padding: 6px 9px; }

            /* ── CHIPS DE FILTRO Y PILLS ── */
            .ps-chip-bar { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
            .ps-filter-chip {
                border: 1px solid var(--grid-2); background: #fff; color: var(--ink);
                font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 6px;
                cursor: pointer; display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
            }
            .ps-filter-chip:hover { border-color: var(--accent-bd); color: var(--ink-2); }
            .ps-filter-chip.active { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); font-weight: 700; }
            .ps-chip-cnt { font-size: 7px; font-weight: 700; padding: 0 5px; border-radius: 99px; background: var(--grid); color: var(--ink-2); }
            .ps-filter-chip.active .ps-chip-cnt { background: var(--accent); color: #fff; }

            .ps-pill { display: inline-flex; align-items: center; gap: 4px; padding: 1px 7px; border-radius: 10px; font-size: 7.5px; font-weight: 700; border: 1px solid; }
            .ps-pill-ok { color: #15803d; background: #f0fdf4; border-color: #bbf7d0; }
            .ps-pill-muted { color: #64748b; background: #f8fafc; border-color: #e2e8f0; }

            /* ── PICKER RÁPIDO DE ICONOS EN MODAL ── */
            .ps-icon-pick {
                border: 1px solid var(--grid-2); background: var(--head); color: var(--ink-2);
                font-size: 8px; padding: 3px 7px; border-radius: 4px; cursor: pointer;
                display: inline-flex; align-items: center; gap: 4px; transition: all .15s;
            }
            .ps-icon-pick:hover { background: var(--accent-bg); color: var(--accent); border-color: var(--accent-bd); }

            .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 8px; font-size: 9.5px; color: #64748b; }
            .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
            .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .ps-err-msg { font-size: 7.5px; color: #b91c1c; font-weight: 600; margin-top: 2px; }

            @keyframes destelloFila { 0%,100% { background-color: transparent; } 35% { background-color: rgba(16,185,129,.18); } }
            .reservas-soft tr.fila-nueva > td { animation: destelloFila 1.6s ease; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #psDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; transition: color .15s ease, background .15s ease; }
            #psDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); }
            .ps-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .ps-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: psDbgBlink 1s steps(2,start) infinite; }
            @keyframes psDbgBlink { 50% { opacity: .35; } }
            .ps-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; }
            #psDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 390px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
            .ps-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .ps-dbg-controls { display: flex; gap: 4px; }
            .ps-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .ps-dbg-btn:hover { background: #475569; }
            #psDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .ps-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .ps-dbg-time { color: #64748b; margin-right: 4px; }
            .ps-dbg-info { color: #93c5fd; } .ps-dbg-ok { color: #86efac; }
            .ps-dbg-warn { color: #fde68a; } .ps-dbg-err { color: #fca5a5; }
            .ps-dbg-net { color: #c4b5fd; } .ps-dbg-debug { color: #94a3b8; }
        </style>

        {{-- ALERTAS DE SESIÓN --}}
        @if (session('success'))
            <div class="alert alert-success py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
            </div>
        @endif

        <!-- HEADER + CHIPS DE FILTRO + BUSCADOR + NUEVA PASARELA -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap: 8px; border-color: var(--grid) !important;">
            <div class="ps-chip-bar">
                <h6 class="fw-bold mb-0 me-1" style="font-size: 10.5px; color: #334155;">
                    <i class="bi bi-credit-card-2-front-fill text-success me-1"></i> Pasarelas de Pago
                </h6>
                <span class="badge rounded-pill me-1" id="psChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; font-family: ui-monospace, monospace;">
                    PS-v2.0-DBG
                </span>

                <button type="button" class="ps-filter-chip active" data-uso="">
                    Todas <span class="ps-chip-cnt">{{ $psItems->count() }}</span>
                </button>
                <button type="button" class="ps-filter-chip" data-uso="con">
                    Con Pagos <span class="ps-chip-cnt">{{ $cntConUso }}</span>
                </button>
                <button type="button" class="ps-filter-chip" data-uso="sin">
                    Sin Uso <span class="ps-chip-cnt">{{ $cntSinUso }}</span>
                </button>
            </div>

            <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                <span class="count-badge" title="Transacciones vinculadas a pasarelas">
                    <b>{{ $psTotalTxGlobal }}</b> tx · <b>${{ number_format($psTotalMontoGlobal, 0) }}</b>
                </span>
                <input type="text" id="psSearch" class="xls-search" placeholder="Buscar pasarela, icono o color…" autocomplete="off">
                <button type="button" class="btn-mini" id="psBtnRefrescar" title="Sincronizar catálogo sin recargar página">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                @if($psStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#psModalCreate">
                        <i class="bi bi-plus-lg"></i> Nueva Pasarela
                    </button>
                @endif
            </div>
        </div>

        <!-- TABLA DE PASARELAS -->
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 620px;">
                <thead>
                    <tr>
                        <th style="width: 60px;">#ID</th>
                        <th>Pasarela / Canal</th>
                        <th>Icono Bootstrap</th>
                        <th>Color Identificador</th>
                        <th class="text-center">Movimientos</th>
                        <th class="text-end">Volumen Recaudado</th>
                        <th class="text-end" style="width: 125px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="psTbody">
                    @forelse($psItems as $p)
                        @php
                            $nomP  = $p->nombre ?? $p->name ?? ('Pasarela #' . $p->id);
                            $hexP  = $psColor($p->color);
                            $icoP  = trim((string) ($p->icono ?? ''));
                            $stP   = $psStatsMap[$p->id] ?? ['cnt' => 0, 'monto' => 0.0];
                            $cntTx = (int) $stP['cnt'];
                            $mtoTx = (float) $stP['monto'];
                        @endphp
                        <tr data-id="{{ $p->id }}"
                            data-ps-name="{{ mb_strtolower($nomP . ' ' . $icoP . ' ' . $hexP) }}"
                            data-ps-uso="{{ $cntTx > 0 ? 'con' : 'sin' }}">
                            <td class="sub num fw-bold">#{{ $p->id }}</td>
                            <td class="strong">
                                <span class="st-dot me-1" style="background: {{ $hexP }};"></span>
                                @if($icoP !== '')
                                    <i class="bi {{ $icoP }} me-1" style="color: {{ $hexP }}; font-size: 10px;"></i>
                                @endif
                                {{ $nomP }}
                            </td>
                            <td class="sub">
                                @if($icoP !== '')
                                    <code>{{ $icoP }}</code>
                                @else
                                    <span class="text-muted">bi-credit-card (default)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge border text-dark fw-semibold" style="background: #f8fafc; font-family: ui-monospace, monospace; font-size: 7.5px;">
                                    <span class="st-dot me-1" style="width:7px; height:7px; background: {{ $hexP }};"></span>{{ $hexP }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($cntTx > 0)
                                    <span class="ps-pill ps-pill-ok">{{ $cntTx }} pago(s)</span>
                                @else
                                    <span class="ps-pill ps-pill-muted">0 pagos</span>
                                @endif
                            </td>
                            <td class="text-end num strong">
                                ${{ number_format($mtoTx, 0) }} <span class="sub">COP</span>
                            </td>
                            <td class="text-end">
                                @if($psBase)
                                    <button type="button" class="btn-mini" data-bs-toggle="modal" data-bs-target="#psEditModal-{{ $p->id }}">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                    <button type="button" class="btn-mini text-danger" data-bs-toggle="modal" data-bs-target="#psDelModal-{{ $p->id }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                @else
                                    <span class="sub">Solo lectura</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" style="font-size: 8.5px; color: #94a3b8;">
                                <i class="bi bi-credit-card d-block mb-1" style="font-size: 18px;"></i>
                                Sin pasarelas de pago definidas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="st-total-label">
                            <i class="bi bi-calculator me-1"></i>
                            MOSTRANDO <span id="psFootVisible">{{ $psItems->count() }}</span> DE {{ $psItems->count() }} PASARELA(S)
                        </td>
                        <td class="text-center">{{ $psTotalTxGlobal }} tx</td>
                        <td class="text-end num">${{ number_format($psTotalMontoGlobal, 0) }} COP</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    {{-- MODAL CREAR NUEVA PASARELA --}}
    @if($psStore)
    <div class="modal fade reservas-soft-modal" id="psModalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.92rem;">
                        <i class="bi bi-plus-circle text-success me-1"></i> Nueva Pasarela
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                </div>
                <form action="{{ $psStore }}" method="POST" class="js-ps-ajax">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="st-warn mb-2 d-none ps-err-general"></div>

                        <div class="mb-2">
                            <label class="form-label">Nombre del Canal / Pasarela <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="nombre" maxlength="100" required placeholder="Ej: PSE, Nequi, Wompi, Bancolombia…">
                            <div class="ps-err-msg" data-error-for="nombre"></div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Icono (Bootstrap Icons)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-credit-card-2-front js-ps-icon-preview"></i></span>
                                <input type="text" class="form-control form-control-sm js-ps-icon-input" name="icono" maxlength="60" placeholder="bi-credit-card-2-front" value="bi-credit-card-2-front">
                            </div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach($iconosSugeridos as $icClass => $icLbl)
                                    <button type="button" class="ps-icon-pick js-ps-pick-icon" data-icon="{{ $icClass }}" title="{{ $icLbl }}">
                                        <i class="bi {{ $icClass }}"></i>
                                    </button>
                                @endforeach
                            </div>
                            <div class="ps-err-msg" data-error-for="icono"></div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Color Identificador</label>
                            <input type="color" class="form-control form-control-color form-control-sm w-100" name="color" value="#3D7A5C">
                            <div class="ps-err-msg" data-error-for="color"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 10.5px;">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODALES EDITAR Y ELIMINAR POR CADA PASARELA --}}
    @foreach($psItems as $p)
        @php
            $nomP = $p->nombre ?? $p->name ?? ('Pasarela #' . $p->id);
            $cnt  = (int) ($psStatsMap[$p->id]['cnt'] ?? 0);
        @endphp

        @if($psBase)
        <div class="modal fade reservas-soft-modal" id="psEditModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.92rem;">
                            <i class="bi bi-pencil-square text-success me-1"></i> Editar: {{ $nomP }}
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    </div>
                    <form action="{{ $psBase }}/{{ $p->id }}" method="POST" class="js-ps-ajax">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-3">
                            <div class="st-warn mb-2 d-none ps-err-general"></div>

                            <div class="mb-2">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nombre" maxlength="100" required value="{{ $nomP }}">
                                <div class="ps-err-msg" data-error-for="nombre"></div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Icono (Bootstrap Icons)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light"><i class="bi {{ $p->icono ?: 'bi-credit-card-2-front' }} js-ps-icon-preview"></i></span>
                                    <input type="text" class="form-control form-control-sm js-ps-icon-input" name="icono" maxlength="60" value="{{ $p->icono }}">
                                </div>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach($iconosSugeridos as $icClass => $icLbl)
                                        <button type="button" class="ps-icon-pick js-ps-pick-icon" data-icon="{{ $icClass }}" title="{{ $icLbl }}">
                                            <i class="bi {{ $icClass }}"></i>
                                        </button>
                                    @endforeach
                                </div>
                                <div class="ps-err-msg" data-error-for="icono"></div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color form-control-sm w-100" name="color" value="{{ $psColor($p->color) }}">
                                <div class="ps-err-msg" data-error-for="color"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 10.5px;">
                                <i class="bi bi-check-lg"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade reservas-soft-modal" id="psDelModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body p-3 text-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.3rem;"></i>
                        <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar pasarela</h6>
                        <p class="mb-2" style="font-size: 8.5px; color: #64748b;">
                            Se eliminará <b style="color: #334155;">“{{ $nomP }}”</b> del catálogo financiero.
                        </p>
                        <div class="st-warn mb-2 d-none ps-err-general"></div>

                        @if($cnt > 0)
                            <div class="st-warn mb-2">
                                <i class="bi bi-lock-fill me-1"></i> Protegida por integridad: tiene <b>{{ $cnt }}</b> transacción(es) asociada(s).
                            </div>
                            <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled style="font-size: 10.5px; background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; cursor: not-allowed;">
                                <i class="bi bi-trash3"></i> Eliminación bloqueada
                            </button>
                        @else
                            <form action="{{ $psBase }}/{{ $p->id }}" method="POST" class="js-ps-ajax">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm py-1 px-2 w-100" style="font-size: 10.5px; background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 700;">
                                    <i class="bi bi-trash3"></i> Confirmar eliminación
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 10.5px;" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach

</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL PS-v2.0-DBG (diseño TIP-v1.3) ═══ */
    var DBG = (window.PsDbg && window.PsDbg.alive && window.PsDbg.alive())
        ? window.PsDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function unicarDbg() {
                var ts = document.querySelectorAll('#psDbgToggle');
                for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
                var ps = document.querySelectorAll('#psDbgPanel');
                for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
            }
            function asegurarNodos() {
                unicarDbg();
                var rootDbg = document.querySelector('#sheet-pasarelas');
                if (!rootDbg) { return; }
                if (!$('psDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'psDbgToggle';
                    t.title = 'Debug Pasarelas (PS-v2.0-DBG)';
                    t.innerHTML = '<span class="ps-dbg-led" id="psDbgLed"></span>DBG<span class="ps-dbg-badge" id="psDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('psDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'psDbgPanel';
                    p.innerHTML = '<div class="ps-dbg-header" id="psDbgHeader">' +
                        '<span>DEBUG · Pasarelas de Pago · PS-v2.0-DBG</span>' +
                        '<span class="ps-dbg-controls">' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgCopy">Copiar</button>' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgClear">Limpiar</button>' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgClose">✕</button>' +
                        '</span></div><div id="psDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'finDbgToggle', 'audDbgToggle', 'infDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; }
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('psDbgToggle'), p = $('psDbgPanel');
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
                    var b = $('psDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'ps-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="ps-dbg-time">[' + hora() + ']</span>' +
                        '<span class="ps-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('psDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('psDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('psDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('psDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('psDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('psDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('psDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('psDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('psDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG pasarelas PS-v2.0-DBG · ' + new Date().toISOString() + ' ===\n';
                    for (var i = 0; i < b.children.length; i++) { txt += b.children[i].textContent + '\n'; }
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(txt).then(function () {
                            api.log('log copiado al portapapeles', 'ok');
                        });
                    }
                },
                unicar: unicarDbg,
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('psDbgToggle'); }
            };
            window.PsDbg = api;
            return api;
        })();

    DBG.ensure();
    var psdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    function cerrarBruto(el) {
        if (!el) return;
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
        el.removeAttribute('aria-modal');
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function ocultarModal(el) {
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) {
            window.bootstrap.Modal.getOrCreateInstance(el).hide();
        }
        setTimeout(function () { if (el.classList.contains('show')) cerrarBruto(el); }, 450);
    }

    function limpiarResiduos() {
        if (document.querySelectorAll('.modal.show').length === 0) {
            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    }

    function unicarModales() {
        document.querySelectorAll('#sheet-pasarelas').forEach(function (hoja, idx) {
            if (idx === 0) return;
            hoja.querySelectorAll('.modal[id]').forEach(function (m) {
                if (m.id.indexOf('-c' + (idx + 1)) !== -1) return;
                var viejo = m.id;
                m.id = viejo + '-c' + (idx + 1);
                hoja.querySelectorAll('[data-bs-target="#' + viejo + '"]').forEach(function (b) {
                    b.setAttribute('data-bs-target', '#' + m.id);
                });
            });
        });
    }

    function avisar(mensaje, tipo) {
        if (!window.bootstrap || !window.bootstrap.Toast) { return; }
        var div = document.createElement('div');
        div.className = 'toast align-items-center text-bg-' + (tipo === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed m-3';
        div.setAttribute('role', 'alert');
        div.style.cssText = 'bottom:18px;right:14px;z-index:2100;font-size:10px;';
        div.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">' + mensaje + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>';
        document.body.appendChild(div);
        div.addEventListener('hidden.bs.toast', function () { div.remove(); });
        window.bootstrap.Toast.getOrCreateInstance(div, { delay: 2600 }).show();
    }

    function pintarErrores(form, errores) {
        form.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
        form.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
        var g = form.querySelector('.ps-err-general');
        if (g) { g.classList.add('d-none'); g.textContent = ''; }
        Object.keys(errores || {}).forEach(function (campo) {
            var caja = form.querySelector('[data-error-for="' + campo + '"]');
            if (caja) caja.textContent = [].concat(errores[campo])[0];
            var input = form.querySelector('[name="' + campo + '"]');
            if (input) input.classList.add('is-invalid');
        });
    }

    function errorGeneral(form, msg) {
        var g = form.querySelector('.ps-err-general');
        if (!g) { return; }
        g.textContent = msg;
        g.classList.remove('d-none');
    }

    function resaltarFila(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-pasarelas tr[data-id="' + id + '"]').forEach(function (fila) {
            fila.classList.add('fila-nueva');
            setTimeout(function () { fila.classList.remove('fila-nueva'); }, 1800);
        });
    }

    function aplicarFiltro(origen) {
        var inp = document.getElementById('psSearch');
        var chip = document.querySelector('#sheet-pasarelas .ps-filter-chip.active');
        var val = (inp && inp.value ? inp.value : '').toLowerCase().trim();
        var uso = chip ? (chip.getAttribute('data-uso') || '') : '';
        var visibles = 0;

        document.querySelectorAll('#sheet-pasarelas tr[data-ps-name]').forEach(function (tr) {
            var okQ = !val || (tr.getAttribute('data-ps-name') || '').indexOf(val) !== -1;
            var okU = !uso || (tr.getAttribute('data-ps-uso') || '') === uso;
            var mostrar = okQ && okU;
            tr.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        var footVis = document.getElementById('psFootVisible');
        if (footVis) footVis.textContent = visibles;

        if (origen) {
            psdbg('filtro (' + origen + ') · uso=' + (uso || 'todas') + ' · q="' + val + '" · ' + visibles + ' visible(s)', 'debug');
        }
    }

    /* ═══ RECARGA ASÍNCRONA DEL FRAGMENTO DE PASARELAS (CERO RELOAD DE PÁGINA) ═══ */
    window.psReloadFragment = function (idResaltar, motivo) {
        var root = document.getElementById('sheet-pasarelas');
        if (!root) return Promise.resolve();

        var fragUrl = root.getAttribute('data-frag') || window.location.pathname;
        var inmId   = root.getAttribute('data-inmueble-id') || 'todos';

        var urlObj = new URL(fragUrl, window.location.origin);
        if (!urlObj.searchParams.has('inmueble_id')) {
            urlObj.searchParams.set('inmueble_id', inmId);
        }
        urlObj.searchParams.set('_t', Date.now());

        var t0 = performance.now();
        psdbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                psdbg('Error HTTP ' + res.status + ' al refrescar Pasarelas (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevo = doc.querySelector('#sheet-pasarelas');
            var copias = document.querySelectorAll('#sheet-pasarelas');
            if (!nuevo || copias.length === 0) {
                psdbg('Respuesta sin #sheet-pasarelas (' + res.ms + 'ms)', 'warn');
                return;
            }
            copias.forEach(function (actual) {
                actual.replaceWith(nuevo.cloneNode(true));
            });
            unicarModales();
            DBG.ensure();
            aplicarFiltro();
            if (idResaltar) resaltarFila(idResaltar);
            limpiarResiduos();
            psdbg('Catálogo de Pasarelas actualizado (' + res.ms + 'ms) · copias=' + copias.length + (idResaltar ? ' · id=' + idResaltar : ''), 'ok');
        })
        .catch(function (err) {
            psdbg('Excepción recargando Pasarelas: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]');
        var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }

        var metodoReal = (form.querySelector('input[name="_method"]')?.value || 'POST').toUpperCase();
        var t0 = performance.now();
        psdbg(metodoReal + ' Pasarela → ' + (form.getAttribute('action') || '?'), 'net');

        var tokIn = form.querySelector('input[name="_token"]');
        var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(function (r) {
            var ms = Math.round(performance.now() - t0);
            return r.json()
                .then(function (j) { return { ok: r.ok, status: r.status, ms: ms, json: j }; })
                .catch(function () { return { ok: r.ok, status: r.status, ms: ms, json: null }; });
        })
        .then(function (res) {
            if (res.ok && (!res.json || res.json.success !== false)) {
                var d = (res.json && res.json.data) || {};
                var idNuevo = d.id || (d.data && d.data.id) || null;
                var msgOk = (res.json && res.json.message) || 'Operación realizada correctamente';

                ocultarModal(form.closest('.modal'));
                psdbg('AJAX OK (' + res.ms + 'ms) · ' + msgOk + (idNuevo ? ' · id=' + idNuevo : ''), 'ok');
                avisar(msgOk, 'success');

                setTimeout(function () {
                    window.psReloadFragment(idNuevo, 'post-crud');
                    // Sincronizar también la tabla resumen de Inicio Finanzas si está cargada
                    if (typeof window.finTabReloadFragment === 'function') {
                        window.finTabReloadFragment(null, 'sync-desde-pasarelas');
                    }
                }, 300);
            } else if (res.status === 422 && res.json && res.json.errors) {
                psdbg('422 validación (' + res.ms + 'ms) · ' + Object.keys(res.json.errors || {}).join(', '), 'warn');
                pintarErrores(form, res.json.errors);
            } else {
                var msgErr = (res.json && res.json.message) || 'No se pudo completar la operación (HTTP ' + res.status + ').';
                psdbg('Error AJAX (' + res.ms + 'ms) · ' + msgErr, 'err');
                errorGeneral(form, msgErr);
            }
        })
        .catch(function (err) {
            psdbg('Fallo de red en el envío: ' + (err && err.message ? err.message : 'red'), 'err');
            errorGeneral(form, 'Error de conexión. Intenta de nuevo.');
        })
        .finally(function () {
            if (btn) { btn.disabled = false; btn.innerHTML = txt; }
        });
    }

    if (!window.__jsPasarelasV2Listo) {
        window.__jsPasarelasV2Listo = true;

        document.addEventListener('click', function (e) {
            var t = e.target;
            if (!t || !t.closest) { return; }

            if (t.closest('#psDbgToggle'))  { DBG.togglePanel(); return; }
            if (t.closest('#psDbgClose'))   { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#psDbgClear'))   { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#psDbgCopy'))    { e.stopPropagation(); DBG.copy(); return; }
            if (t.closest('#psDbgRefresh') || t.closest('#psBtnRefrescar')) {
                e.stopPropagation();
                window.psReloadFragment(null, 'manual-refresh');
                return;
            }

            // Chips de filtro de uso (Todas / Con Pagos / Sin Uso)
            var chip = t.closest('.ps-filter-chip');
            if (chip && chip.closest('#sheet-pasarelas')) {
                document.querySelectorAll('#sheet-pasarelas .ps-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                aplicarFiltro('chip');
                return;
            }

            // Picker rápido de icono Bootstrap dentro del modal
            var pickBtn = t.closest('.js-ps-pick-icon');
            if (pickBtn && pickBtn.closest('#sheet-pasarelas')) {
                e.preventDefault();
                var iconCls = pickBtn.getAttribute('data-icon') || 'bi-credit-card';
                var box     = pickBtn.closest('.mb-2') || pickBtn.closest('form');
                if (box) {
                    var inp = box.querySelector('.js-ps-icon-input');
                    var prv = box.querySelector('.js-ps-icon-preview');
                    if (inp) inp.value = iconCls;
                    if (prv) prv.className = 'bi ' + iconCls + ' js-ps-icon-preview';
                }
            }
        });

        document.addEventListener('dblclick', function (e) {
            if (e.target && e.target.closest && e.target.closest('#psDbgHeader')) {
                DBG.close();
            }
        });

        // Submit acotado exclusivamente a formularios de #sheet-pasarelas
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.closest || !form.closest('#sheet-pasarelas')) return;
            if (!form.classList.contains('js-ps-ajax') && !form.classList.contains('js-ajax')) return;
            e.preventDefault();
            enviarFormulario(form);
        });

        document.addEventListener('input', function (e) {
            var t = e.target;
            if (!t) return;
            if (t.id === 'psSearch') {
                aplicarFiltro('búsqueda');
                return;
            }
            if (t.classList && t.classList.contains('js-ps-icon-input')) {
                var grp = t.closest('.input-group');
                var prv = grp ? grp.querySelector('.js-ps-icon-preview') : null;
                if (prv) {
                    prv.className = 'bi ' + (t.value.trim() || 'bi-credit-card') + ' js-ps-icon-preview';
                }
            }
        });

        document.addEventListener('show.bs.modal', function (e) {
            var m = e.target;
            if (!m || !m.closest || !m.closest('#sheet-pasarelas')) return;
            m.querySelectorAll('.ps-err-general').forEach(function (g) { g.classList.add('d-none'); g.textContent = ''; });
            m.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
            m.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
        });

        document.addEventListener('shown.bs.tab', function (e) {
            if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
            var target = e.target ? (e.target.getAttribute('data-bs-target') || '') : '';
            if (target === '#fin-sub-pasarelas' || target === '#single-fin-sub-pasarelas') {
                window.psReloadFragment(null, 'tab-focus');
            }
        });
    }

    var SRVDBG = @json($psDbg);
    psdbg('PS-v2.0-DBG activo · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : 0) +
          ' (con uso=' + (SRVDBG ? SRVDBG.con_uso : 0) + ', sin uso=' + (SRVDBG ? SRVDBG.sin_uso : 0) + ')' +
          ' · tx asociadas=' + (SRVDBG ? SRVDBG.tx_asociadas : 0), 'ok');
    if (SRVDBG && SRVDBG.rutas) {
        psdbg('Rutas · store=' + SRVDBG.rutas.store + ' · base=' + SRVDBG.rutas.base + ' · fragmento=' + SRVDBG.rutas.fragmento, 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        SRVDBG.errores.forEach(function (er) { psdbg('⚠ SERVIDOR: ' + er, 'err'); });
    }

    unicarModales();
    limpiarResiduos();
    aplicarFiltro();
})();
</script>
