{{-- ============================================================================
     RSV · ADMIN · FINANZAS · PASARELAS · PS-v1-DBG
     Parcial: resources/views/rsv/admin/partials/finanzas/pasarela.blade.php

     · CRUD 100% Bootstrap/Blade (funciona incluso sin JS) + AJAX opcional
     · Refresca TODAS las copias de #sheet-pasarelas (parcial incluido 2+ veces)
     · Colores normalizados (evita warnings de <input type="color">)

     · PANEL DE DEBUG (CAMBIO PS-v1-DBG · diseño TIP-v1.3 aprobado): pestaña
       terminal "DBG" de 16px fija SIEMPRE en la esquina inferior-derecha
       (right:0 · bottom:0, radius 3px solo superior-izquierdo) con LED de
       estado (verde = ok · rojo pulsante + conteo = errores) y panel cajón
       de 380px alineado al mismo borde. La regla exacta de distancias está
       documentada como "SPEC DE DISTANCIAS" dentro del CSS, y el panel
       esquiva a los toggles hermanos de las otras vistas midiendo píxeles
       reales (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Exclusivo de este fragmento: el toggle y el panel nacen y mueren con
       cada copia de la hoja (el refresco AJAX las reemplaza enteras y los
       nodos renacen con el HTML nuevo) y unicarDbg() deduplica dejando UN
       solo toggle/panel por página. Nada se cuelga de document.body.
       Telemetría que reporta: pasarelas cargadas, rutas centralizadas
       (store/base/fragmento), envíos AJAX (crear/editar/eliminar),
       validación 422, refresco del fragmento, filtro de búsqueda y
       errores de red.
     · RUTAS centralizadas en el bloque php del servidor (store/base/fragmento)
       y el script consume la misma variable (una sola fuente de verdad).
     · NOTA: los comentarios nombran las directivas de Blade SIN arroba
       (p.ej. "la directiva json de Blade"): Blade compila a nivel de texto
       aunque estén dentro de comentarios y una arroba pelada rompe la
       compilación (lección BLQ-v1-2).
     ============================================================================ --}}

<div id="sheet-pasarelas">

    {{-- PS-v1-DBG · Tab DBG terminal + LED + Panel LOCAL (diseño TIP-v1.3 · vive y muere con esta copia · unicarDbg deja un solo par por página) --}}
    <div id="psDbgToggle" title="Debug Pasarelas (PS-v1-DBG)"><span class="ps-dbg-led" id="psDbgLed"></span>DBG<span class="ps-dbg-badge" id="psDbgBadge">0</span></div>
    <div id="psDbgPanel" role="dialog" aria-label="Panel de debug de Pasarelas">
        <div class="ps-dbg-header" id="psDbgHeader">
            <span>DEBUG · Pasarelas de Pago · PS-v1-DBG</span>
            <span class="ps-dbg-controls">
                <button type="button" class="ps-dbg-btn" id="psDbgCopy">Copiar</button>
                <button type="button" class="ps-dbg-btn" id="psDbgClear">Limpiar</button>
                <button type="button" class="ps-dbg-btn" id="psDbgClose">✕</button>
            </span>
        </div>
        <div id="psDbgBody"></div>
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
            .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
            .reservas-soft .st-warn { background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px; color: #c98a85; font-size: 8px; font-weight: 600; padding: 6px 9px; }
            .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 5px; font-size: 9.5px; color: #64748b; }
            .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
            .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .ps-err-msg { font-size: 7.5px; color: #c98a85; }

            @keyframes destelloFila { 0%,100% { background-color: transparent; } 35% { background-color: rgba(61,122,92,.16); } }
            .reservas-soft tr.fila-nueva > td { animation: destelloFila 1.5s ease; }

            /* ═════════ SPEC DE DISTANCIAS · PS-v1-DBG (tab terminal TIP-v1.3) ═════════
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
            #psDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #psDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .ps-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .ps-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: psDbgBlink 1s steps(2,start) infinite; }
            @keyframes psDbgBlink { 50% { opacity: .35; } }
            .ps-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #psDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
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

        @php
            $psItems = collect($pasarelas ?? []);

            $psStore = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.store') ? route('rsv.pasarelas.store') : null;
            $psBase  = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.index') ? route('rsv.pasarelas.index') : null;
            $psFragmento = \Illuminate\Support\Facades\Route::has('rsv.admin.pasarelas.fragmento') ? route('rsv.admin.pasarelas.fragmento') : null;

            $psColor = function ($color) {
                $c = strtoupper(trim((string) $color));
                return (preg_match('/^#[0-9A-F]{6}$/', $c) || preg_match('/^#[0-9A-F]{3}$/', $c)) ? $c : '#a3cfb6';
            };

            $psCnt = function ($p) {
                try {
                    if (is_object($p) && method_exists($p, 'transacciones')) {
                        return (int) $p->transacciones()->count();
                    }
                } catch (\Throwable $e) {}
                return null;
            };

            /* ── Colector del panel de debug (PS-v1-DBG) · via al panel vía la directiva json de Blade ── */
            $psDbg = [
                'version'   => 'PS-v1-DBG',
                'vista'     => 'partials/finanzas/pasarela.blade.php',
                'render'    => now()->format('H:i:s'),
                'pasarelas' => $psItems->count(),
                'editable'  => (bool) ($psStore || $psBase),
                'rutas'     => [
                    'store'     => $psStore     ?: 'NO definida',
                    'base'      => $psBase      ?: 'NO definida',
                    'fragmento' => $psFragmento ?: 'NO definida',
                ],
                'errores'   => [],
            ];
        @endphp

        {{-- ALERTAS --}}
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
        @if ($errors->any())
            <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 9px; border-radius: 5px;">
                <i class="bi bi-exclamation-circle-fill me-1"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-credit-card-2-front me-1" style="font-size: 8px; color: #5d8a70;"></i> Pasarelas de Pago
                <span class="badge rounded-pill ms-1" style="font-size: 6.5px; background: #f0f7f2; color: #3d7a5c; border: 1px solid #d5e7dc; vertical-align: middle;">FZ</span>
                <span class="badge rounded-pill ms-1" id="psChipVer" style="font-size: 6.5px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #1e293b; vertical-align: middle; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; letter-spacing: .08em;">PS-v1-DBG</span>
            </h6>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <span class="count-badge" title="Pasarelas registradas en el sistema">
                    <b>{{ $psItems->count() }}</b> pasarela(s)
                </span>
                <input type="text" id="psSearch" class="xls-search" placeholder="Buscar pasarela…" autocomplete="off" style="width: 150px;">
                @if($psStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#psModalCreate">
                        <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Pasarela
                    </button>
                @endif
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 480px;">
                <thead>
                    <tr>
                        <th>Pasarela</th>
                        <th>Icono</th>
                        <th>Color</th>
                        <th class="text-center" style="width: 110px;">·</th>
                    </tr>
                </thead>
                <tbody id="psTbody">
                    @forelse($psItems as $p)
                        <tr data-id="{{ $p->id }}" data-ps-name="{{ strtolower($p->nombre) }}">
                            <td class="strong">
                                <span class="st-dot" style="background: {{ $psColor($p->color) }};"></span> {{ $p->nombre }}
                            </td>
                            <td class="sub">
                                @if(!empty($p->icono))
                                    <i class="{{ $p->icono }} me-1" style="font-size: 9px;"></i>{{ $p->icono }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="sub">{{ $p->color ? $psColor($p->color) : '—' }}</td>
                            <td class="text-center">
                                @if($psBase)
                                    <button type="button" class="btn-mini" data-bs-toggle="modal" data-bs-target="#psEditModal-{{ $p->id }}">Editar</button>
                                @endif
                                @if($psBase)
                                    <button type="button" class="btn-mini" style="color:#c98a85;" data-bs-toggle="modal" data-bs-target="#psDelModal-{{ $p->id }}">Del</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-credit-card me-1"></i> Sin pasarelas definidas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="st-total-label">
                            <i class="bi bi-calculator me-1" style="font-size: 8px;"></i> TOTAL · {{ $psItems->count() }} pasarela(s)
                        </td>
                        <td></td><td></td><td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    {{-- MODAL CREAR --}}
    @if($psStore)
    <div class="modal fade reservas-soft-modal" id="psModalCreate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                    <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Nueva Pasarela</h6>
                </div>
                <form action="{{ $psStore }}" method="POST" class="js-ajax">
                    @csrf
                    <div class="modal-body p-3">
                        <div class="st-warn mb-2 d-none ps-err-general"></div>
                        <div class="mb-2">
                            <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="nombre" maxlength="100" required placeholder="Nequi, Bancolombia, PayPal…" value="{{ old('nombre') }}">
                            <div class="ps-err-msg" data-error-for="nombre"></div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Icono</label>
                            <input type="text" class="form-control form-control-sm" name="icono" maxlength="60" placeholder="bi-credit-card-2-front" value="{{ old('icono') }}">
                            <div class="sub">Opcional · clase de Bootstrap Icons</div>
                            <div class="ps-err-msg" data-error-for="icono"></div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Color (dot en tablas y calendario)</label>
                            <input type="color" class="form-control form-control-color form-control-sm w-100" name="color" value="{{ old('color', '#a3cfb6') }}">
                            <div class="ps-err-msg" data-error-for="color"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- MODALES EDITAR Y ELIMINAR --}}
    @foreach($psItems as $p)
        @php $cnt = $psCnt($p); @endphp

        @if($psBase)
        <div class="modal fade reservas-soft-modal" id="psEditModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Editar: {{ $p->nombre }}</h6>
                    </div>
                    <form action="{{ $psBase }}/{{ $p->id }}" method="POST" class="js-ajax">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-3">
                            <div class="st-warn mb-2 d-none ps-err-general"></div>
                            <div class="mb-2">
                                <label class="form-label">Nombre <span style="color:#c98a85;">*</span></label>
                                <input type="text" class="form-control form-control-sm" name="nombre" maxlength="100" required value="{{ $p->nombre }}">
                                <div class="ps-err-msg" data-error-for="nombre"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Icono</label>
                                <input type="text" class="form-control form-control-sm" name="icono" maxlength="60" value="{{ $p->icono }}">
                                <div class="sub">Opcional · clase de Bootstrap Icons</div>
                                <div class="ps-err-msg" data-error-for="icono"></div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color form-control-sm w-100" name="color" value="{{ $psColor($p->color) }}">
                                <div class="ps-err-msg" data-error-for="color"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade reservas-soft-modal" id="psDelModal-{{ $p->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body p-3 text-center">
                        <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: #c98a85;"></i>
                        <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Eliminar pasarela</h6>
                        <p class="mb-2" style="font-size: 8.5px; color: #94a3b8;">Se eliminará <b style="color: #334155;">“{{ $p->nombre }}”</b> de todo el sistema.</p>
                        <div class="st-warn mb-2 d-none ps-err-general"></div>

                        @if(!is_null($cnt) && $cnt > 0)
                            <div class="st-warn mb-2">
                                <i class="bi bi-lock-fill me-1"></i> Bloqueado: tiene <b>{{ $cnt }}</b> transacción(es) asociada(s).
                            </div>
                            <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled style="font-size: 11px; background: #fcfdfe; color: #d4dae2; border: 1px solid #f1f5f9; cursor: not-allowed;">
                                <i class="bi bi-trash3"></i> Eliminar
                            </button>
                        @else
                            <form action="{{ $psBase }}/{{ $p->id }}" method="POST" class="js-ajax" onsubmit="if(!confirm('¿Eliminar esta pasarela de forma permanente?')){event.stopPropagation();return false;}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm py-1 px-2 w-100" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;">
                                    <i class="bi bi-trash3"></i> Eliminar definitivamente
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
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
    if (window.__jsPasarelasListo) return;
    window.__jsPasarelasListo = true;

    var URL_FRAGMENTO = '{{ $psFragmento ?? "" }}';

    /* ═══ 0) PANEL DE DEBUG LOCAL PS-v1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no toca el resto del script.
       Exclusiva de este fragmento: los nodos son HTML de la hoja (viven
       DENTRO de #sheet-pasarelas y renacen con cada refresco) y el respaldo
       solo re-crea DENTRO de la primera hoja. Cero document.body.
       Con copias múltiples del parcial, unicarDbg() deja un solo par. */
    var DBG = (window.PsDbg && window.PsDbg.alive && window.PsDbg.alive())
        ? window.PsDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven dentro de la primera #sheet-pasarelas y renacen con el
                   HTML del refresco), se re-crean DENTRO de esa hoja. */
                var rootDbg = document.querySelector('#sheet-pasarelas');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                if (!$('psDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'psDbgToggle';
                    t.title = 'Debug Pasarelas (PS-v1-DBG)';
                    t.innerHTML = '<span class="ps-dbg-led" id="psDbgLed"></span>DBG<span class="ps-dbg-badge" id="psDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('psDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'psDbgPanel';
                    p.innerHTML = '<div class="ps-dbg-header" id="psDbgHeader">' +
                        '<span>DEBUG · Pasarelas de Pago · PS-v1-DBG</span>' +
                        '<span class="ps-dbg-controls">' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgCopy">Copiar</button>' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgClear">Limpiar</button>' +
                        '<button type="button" class="ps-dbg-btn" id="psDbgClose">✕</button>' +
                        '</span></div><div id="psDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* PS-v1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                var ids = ['rsvShowDebugToggle', 'rhDebugToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('psDbgToggle'), p = $('psDbgPanel');
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
                    var txt = '=== DEBUG pasarelas PS-v1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('psDbgToggle'); }
            };
            window.PsDbg = api;
            return api;
        })();
    if (!DBG) { return; }
    DBG.ensure(); // re-apila y re-crea nodos si algún swap externo los reemplazó
    var psdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    /* Botones del panel por DELEGACIÓN en document (el guard __jsPasarelasListo
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__psDbgDocHandler) { document.removeEventListener('click', window.__psDbgDocHandler); }
    window.__psDbgDocHandler = function (e) {
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#psDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#psDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#psDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#psDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__psDbgDocHandler);
    if (window.__psDbgDblHandler) { document.removeEventListener('dblclick', window.__psDbgDblHandler); }
    window.__psDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#psDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__psDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($psDbg);
    psdbg('PS-v1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · nodos nacen y mueren con el fragmento', 'ok');
    psdbg('init · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : '?') + ' · editable=' + (SRVDBG && SRVDBG.editable ? 'sí' : 'no') + ' · unicarDbg deja un solo panel por página', 'info');
    if (SRVDBG && SRVDBG.rutas) {
        var _rk = Object.keys(SRVDBG.rutas);
        for (var _ri = 0; _ri < _rk.length; _ri++) {
            if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                psdbg('ruta no definida: rsv…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
            }
        }
        psdbg('Rutas · store=' + SRVDBG.rutas.store + ' · base=' + SRVDBG.rutas.base + ' · fragmento=' + SRVDBG.rutas.fragmento, 'debug');
    }
    if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
        for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
            psdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
        }
    }

    function cerrarBruto(el) {
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
        setTimeout(function () { if (el.classList.contains('show')) cerrarBruto(el); }, 700);
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

    function unicarDbg() {
        /* PS-v1-DBG: el parcial puede renderizarse 2+ veces; el HTML de cada
           copia trae sus propios nodos DBG → se conservan los de la primera
           hoja y se eliminan los duplicados (un solo toggle/panel por página). */
        var ts = document.querySelectorAll('#psDbgToggle');
        for (var i = 1; i < ts.length; i++) { if (ts[i] && ts[i].remove) { ts[i].remove(); } }
        var ps = document.querySelectorAll('#psDbgPanel');
        for (var j = 1; j < ps.length; j++) { if (ps[j] && ps[j].remove) { ps[j].remove(); } }
    }

    function avisar(mensaje, tipo) {
        if (!window.bootstrap || !window.bootstrap.Toast) { window.alert(mensaje); return; }
        var div = document.createElement('div');
        div.className = 'toast align-items-center text-bg-' + (tipo === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed m-3';
        div.setAttribute('role', 'alert');
        div.style.cssText = 'bottom:0;right:0;z-index:2100;';
        div.innerHTML = '<div class="d-flex"><div class="toast-body">' + mensaje + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>';
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
        if (!g) { window.alert(msg); return; }
        g.textContent = msg;
        g.classList.remove('d-none');
    }

    function resaltarFila(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-pasarelas tr[data-id="' + id + '"]').forEach(function (fila) {
            fila.classList.add('fila-nueva');
            setTimeout(function () { fila.classList.remove('fila-nueva'); }, 1600);
        });
    }

    function refrescarTabla(idResaltar) {
        if (!URL_FRAGMENTO) { psdbg('sin ruta de fragmento · fallback reload()', 'warn'); window.location.reload(); return; }
        fetch(URL_FRAGMENTO, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-pasarelas');
                var copias = document.querySelectorAll('#sheet-pasarelas');
                if (!nuevo || copias.length === 0) { psdbg('respuesta sin #sheet-pasarelas · fallback reload()', 'warn'); window.location.reload(); return; }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                unicarModales(); unicarDbg(); aplicarFiltro(); if (idResaltar) resaltarFila(idResaltar); limpiarResiduos();
                if (window.PsDbg && window.PsDbg.ensure) { window.PsDbg.ensure(); }
                psdbg('fragmento refrescado · copias=' + copias.length + (idResaltar ? ' · fila resaltada id=' + idResaltar : ''), 'net');
            })
            .catch(function () { psdbg('fallo al refrescar el fragmento · fallback reload()', 'err'); window.location.reload(); });
    }

    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]');
        var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }
        psdbg('enviando AJAX · ' + (form.getAttribute('action') || '?'), 'net');

        var tokIn = form.querySelector('input[name="_token"]');
        var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, json: j }; }).catch(function () { return { ok: r.ok, status: r.status, json: null }; }); })
        .then(function (res) {
            if (res.ok && res.json && res.json.success) {
                var d = res.json.data || {};
                var idNuevo = d.id || (d.data && d.data.id) || null;
                ocultarModal(form.closest('.modal'));
                psdbg('AJAX ok · ' + (res.json.message || 'Guardado correctamente') + (idNuevo ? ' · id=' + idNuevo : ''), 'ok');
                avisar(res.json.message || 'Guardado correctamente', 'success');
                setTimeout(function () { refrescarTabla(idNuevo); }, 350);
            } else if (res.status === 422 && res.json && res.json.errors) {
                psdbg('422 validación · ' + Object.keys(res.json.errors || {}).join(', '), 'warn');
                pintarErrores(form, res.json.errors);
            } else {
                psdbg('error AJAX · ' + ((res.json && res.json.message) || 'No se pudo completar la operación.'), 'err');
                errorGeneral(form, (res.json && res.json.message) || 'No se pudo completar la operación.');
            }
        })
        .catch(function () { psdbg('fallo de red en el envío', 'err'); errorGeneral(form, 'Error de conexión. Intenta de nuevo.'); })
        .finally(function () { if (btn) { btn.disabled = false; btn.innerHTML = txt; } });
    }

    function aplicarFiltro(inp) {
        inp = inp || document.getElementById('psSearch');
        if (!inp) return;
        var val = (inp.value || '').toLowerCase().trim();
        var visibles = 0;
        document.querySelectorAll('#sheet-pasarelas tr[data-ps-name]').forEach(function (tr) {
            var mostrar = (!val || (tr.getAttribute('data-ps-name') || '').indexOf(val) !== -1);
            tr.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });
        if (val) { psdbg('filtro "' + val + '" · ' + visibles + ' fila(s) visibles', 'debug'); }
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.classList || !form.classList.contains('js-ajax')) return;
        e.preventDefault(); enviarFormulario(form);
    });

    document.addEventListener('input', function (e) {
        if (e.target && e.target.id === 'psSearch') aplicarFiltro(e.target);
    });

    document.addEventListener('show.bs.modal', function (e) {
        var m = e.target;
        if (!m || !m.closest || !m.closest('#sheet-pasarelas')) return;
        m.querySelectorAll('.ps-err-general').forEach(function (g) { g.classList.add('d-none'); g.textContent = ''; });
        m.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
        m.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
    });

    function iniciar() {
        unicarDbg(); unicarModales(); limpiarResiduos(); aplicarFiltro();
        if (window.PsDbg && window.PsDbg.ensure) { window.PsDbg.ensure(); }
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>
