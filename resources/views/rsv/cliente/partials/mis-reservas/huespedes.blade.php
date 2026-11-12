{{-- partials/mis-reservas/huespedes.blade.php · Cliente · Gestión Reactiva sin Reload (HU-v3-DBG) --}}
<div id="sheet-huespedes-cliente">

    {{-- HU-v3-DBG · Tab DBG terminal + LED + Panel LOCAL Persistente (diseño TIP-v1.3) --}}
    <div id="huDbgToggle" title="Debug Huéspedes (HU-v3-DBG)">
        <span class="hu-dbg-led" id="huDbgLed"></span>DBG<span class="hu-dbg-badge" id="huDbgBadge">0</span>
    </div>
    <div id="huDbgPanel" role="dialog" aria-label="Panel de debug de Huéspedes">
        <div class="hu-dbg-header" id="huDbgHeader">
            <span>DEBUG · Huéspedes Cliente · HU-v3-DBG</span>
            <span class="hu-dbg-controls">
                <button type="button" class="hu-dbg-btn" id="huDbgRefresh" title="Refrescar fragmento desde ClientePortalController">↻ Frag</button>
                <button type="button" class="hu-dbg-btn" id="huDbgCopy">Copiar</button>
                <button type="button" class="hu-dbg-btn" id="huDbgClear">Limpiar</button>
                <button type="button" class="hu-dbg-btn" id="huDbgClose">✕</button>
            </span>
        </div>
        <div id="huDbgBody"></div>
    </div>

    @php
        $authUser = \Illuminate\Support\Facades\Auth::user();
        $uid = $authUser ? $authUser->id : null;
        $rid = request('reserva_id') ?? request('id_reserva');
        $huErrores = [];

        // Datos del Usuario para pre-llenar Paso 1 (Titular)
        $userFullName  = trim((string) ($authUser->name ?? ''));
        $userFirstName = $authUser->nombres ?? $authUser->first_name ?? $authUser->nombre ?? '';
        $userLastName  = $authUser->apellidos ?? $authUser->last_name ?? $authUser->apellido ?? '';

        if (!$userFirstName && $userFullName !== '') {
            $partes = preg_split('/\s+/', $userFullName);
            if (count($partes) === 1) {
                $userFirstName = $partes[0];
                $userLastName  = 'Titular';
            } elseif (count($partes) === 2) {
                $userFirstName = $partes[0];
                $userLastName  = $partes[1];
            } elseif (count($partes) === 3) {
                $userFirstName = $partes[0];
                $userLastName  = $partes[1] . ' ' . $partes[2];
            } else {
                $userFirstName = $partes[0] . ' ' . $partes[1];
                $userLastName  = implode(' ', array_slice($partes, 2));
            }
        }

        $userTipoDoc = $authUser->tipo_documento ?? $authUser->document_type ?? 'CC';
        $userNumDoc  = $authUser->numero_documento ?? $authUser->documento ?? $authUser->cedula ?? $authUser->identification ?? $authUser->dni ?? '';

        // 1. Aprovechar las colecciones enviadas por ClientePortalController@misReservasHuespedesFragmento
        $colActivas = isset($activas) && $activas instanceof \Illuminate\Support\Collection ? $activas : collect();
        $colPasadas = isset($pasadas) && $pasadas instanceof \Illuminate\Support\Collection ? $pasadas : collect();
        $misReservas = $colActivas->concat($colPasadas)->unique('id')->values();

        // Fallback si la vista fue incluida directamente con @include sin pasar por el método del controlador
        if ($misReservas->isEmpty() && $uid) {
            try {
                $misReservas = \App\Models\Rsv\Reserva::with(['inmueble', 'status'])
                    ->where('id_user', $uid)
                    ->orderByDesc('fecha_inicio')
                    ->limit(25)
                    ->get();
            } catch (\Throwable $e) {
                $huErrores[] = 'Reserva query: ' . $e->getMessage();
            }
        }

        // Selección de la reserva activa actual
        $reserva = null;
        if ($rid && is_numeric($rid)) {
            $reserva = $misReservas->firstWhere('id', (int) $rid);
            if (!$reserva && $uid) {
                try {
                    $reserva = \App\Models\Rsv\Reserva::with(['inmueble', 'status'])
                        ->where('id_user', $uid)
                        ->find((int) $rid);
                } catch (\Throwable $e) {}
            }
        }

        if (!$reserva && $misReservas->isNotEmpty()) {
            $reserva = $misReservas->first(function ($r) {
                $stName = mb_strtolower(optional($r->status)->name ?? '');
                return !str_contains($stName, 'cancela') && !str_contains($stName, 'anula');
            }) ?? $misReservas->first();
        }

        // 2. Filtrar los huéspedes de la reserva seleccionada (usando $huespedes del controlador o relación)
        $huespedesReserva = collect();
        if ($reserva) {
            if (isset($huespedes) && $huespedes instanceof \Illuminate\Support\Collection && $huespedes->isNotEmpty()) {
                $huespedesReserva = $huespedes->filter(function ($h) use ($reserva) {
                    $fk = $h->id_rsv_reservas ?? $h->id_rsv_reserva ?? $h->reserva_id ?? null;
                    return (int) $fk === (int) $reserva->id;
                })->sortByDesc('es_titular')->values();
            }
            // Consultamos siempre la relación fresca de esa reserva para garantizar exactitud tras insertar
            try {
                $huespedesReserva = $reserva->huespedes()
                    ->with('registrador:id,name')
                    ->orderByDesc('es_titular')
                    ->orderBy('id')
                    ->get();
            } catch (\Throwable $e) {
                $huErrores[] = 'Huespedes query: ' . $e->getMessage();
            }
        }

        // ¿Ya tiene Titular registrado esta reserva?
        $titularRegistro = $huespedesReserva->first(function ($h) use ($userNumDoc) {
            if ((bool) $h->es_titular) return true;
            if ($userNumDoc !== '' && (string) $h->numero_documento === (string) $userNumDoc) return true;
            return false;
        });
        $tieneTitular = !is_null($titularRegistro);

        // Rutas de ClientePortalController (fragmentos) y CRUD de ReservaHuesped
        $rutaFragmento = '';
        foreach (['rsv.cliente.fragmento', 'rsv.cliente.mis-reservas.huespedes.fragmento'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) { $rutaFragmento = route($rn); break; }
        }

        $rutaStoreHuesped = '';
        foreach (['rsv.reserva-huespedes.store', 'rsv.huespedes.store-ajax', 'rsv.huespedes.store', 'rsv.cliente.huespedes.store', 'huespedes.store'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) { $rutaStoreHuesped = route($rn); break; }
        }
        if (!$rutaStoreHuesped) {
            $rutaStoreHuesped = url('/rsv/reserva-huespedes');
        }

        $nombreRutaUpdate = '';
        foreach (['rsv.reserva-huespedes.update', 'rsv.huespedes.update', 'rsv.cliente.huespedes.update', 'huespedes.update'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) { $nombreRutaUpdate = $rn; break; }
        }

        $nombreRutaDestroy = '';
        foreach (['rsv.reserva-huespedes.destroy', 'rsv.huespedes.destroy', 'rsv.cliente.huespedes.destroy', 'huespedes.destroy'] as $rn) {
            if (\Illuminate\Support\Facades\Route::has($rn)) { $nombreRutaDestroy = $rn; break; }
        }

        $inm = $reserva?->inmueble;
        $capMaxima   = (int) ($inm->capacidad_maxima ?? 6);
        $ocupados    = $huespedesReserva->count();
        $disponibles = max(0, $capMaxima - $ocupados);
        $pctCupo     = $capMaxima > 0 ? min(100, round(($ocupados / $capMaxima) * 100)) : 0;

        $stNombre = optional($reserva?->status)->name ?? 'Pendiente';
        $stLower  = mb_strtolower($stNombre);
        $bloqueadaPorEstado = str_contains($stLower, 'cancela') || str_contains($stLower, 'anula') || str_contains($stLower, 'final');

        $huDbgData = [
            'version'       => 'HU-v3-DBG',
            'controlador'   => 'ClientePortalController@misReservasHuespedesFragmento',
            'vista'         => 'partials/mis-reservas/huespedes.blade.php',
            'reserva_id'    => $reserva ? $reserva->id : null,
            'reserva_cod'   => $reserva ? $reserva->codigo_reserva : null,
            'tiene_titular' => $tieneTitular,
            'ocupados'      => $ocupados,
            'cap_maxima'    => $capMaxima,
            'disponibles'   => $disponibles,
            'frag_url'      => $rutaFragmento ?: 'fallback window.location.href',
            'store_url'     => $rutaStoreHuesped,
            'errores'       => $huErrores,
        ];
    @endphp

    <div class="reservas-soft p-3 bg-white rounded-3 border h-100" id="cliHuesRoot"
         data-frag="{{ $rutaFragmento }}"
         data-fragkey="reservas-huespedes"
         data-reserva-id="{{ $reserva ? $reserva->id : '' }}"
         data-reserva-cod="{{ $reserva ? $reserva->codigo_reserva : '' }}"
         data-titular="{{ $tieneTitular ? 'SI' : 'PENDIENTE' }}"
         data-cupo="{{ $ocupados }}/{{ $capMaxima }}"
         style="border-color:#f1f5f9 !important;">

        <style>
            .reservas-soft {
                --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd;
                --ink:#64748b; --ink-2:#334155; --ink-3:#94a3b8; --ink-4:#cbd5e1;
                --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc;
            }
            .reservas-soft .lbl { font-size:7.5px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
            .reservas-soft .val { font-size:10.5px; color:var(--ink-2); font-weight:700; letter-spacing:-0.2px; }
            .reservas-soft .sub { font-size:8.5px; color:var(--ink-3); line-height:1.4; }

            .hu-select-res {
                font-size:9px; font-weight:600; color:var(--ink-2); background:var(--head);
                border:1px solid var(--grid-2); border-radius:6px; padding:4px 10px;
                outline:0; cursor:pointer; max-width:260px;
            }
            .hu-select-res:focus { border-color:var(--accent-bd); background:#fff; }

            .hu-quota-card {
                background:#fff; border:1px solid var(--grid-2); border-radius:10px;
                padding:12px 16px; margin-bottom:14px;
                display:flex; align-items:center; justify-content:space-between;
                flex-wrap:wrap; gap:12px;
            }

            .hu-titular-gate {
                background:linear-gradient(135deg, #f0f7f2 0%, #ffffff 100%);
                border:1px solid var(--accent-bd); border-radius:10px;
                padding:16px 18px; margin-bottom:14px;
                box-shadow:0 4px 14px rgba(61,122,92,0.06);
            }
            .hu-step-badge {
                display:inline-flex; align-items:center; gap:5px;
                background:var(--accent); color:#fff; font-size:8px; font-weight:800;
                padding:3px 9px; border-radius:20px; text-transform:uppercase; letter-spacing:.4px;
            }

            .hu-avatar {
                width:34px; height:34px; border-radius:8px; background:var(--head);
                border:1px solid var(--grid-2); display:flex; align-items:center;
                justify-content:center; font-size:11px; font-weight:800; color:var(--accent);
                flex-shrink:0;
            }
            .hu-avatar.is-titular { background:var(--accent-bg); border-color:var(--accent-bd); }

            .hu-guest-card {
                background:#fff; border:1px solid var(--grid); border-radius:8px;
                margin-bottom:6px; transition:all .15s ease; overflow:hidden;
            }
            .hu-guest-card:hover { border-color:var(--accent-bd); background:#fafdfc; }
            .hu-guest-row {
                padding:9px 12px; display:flex; align-items:center;
                justify-content:space-between; gap:10px; cursor:pointer;
            }
            .hu-guest-edit {
                display:none; padding:10px 12px; background:var(--head);
                border-top:1px dashed var(--grid-2);
            }
            .hu-guest-card.open { border-color:var(--accent-bd); }
            .hu-guest-card.open .hu-guest-edit { display:block; }
            .hu-guest-card.open .hu-chevron { transform:rotate(90deg); color:var(--accent); }
            .hu-chevron { font-size:11px; color:var(--ink-4); transition:transform .15s; }

            .hu-input {
                font-size:9px; color:var(--ink-2); background:#fff;
                border:1px solid var(--grid-2); border-radius:5px;
                padding:5px 8px; outline:0; width:100%;
            }
            .hu-input:focus { border-color:var(--accent-bd); }

            .hu-btn {
                height:28px; padding:0 12px; border-radius:6px; border:1px solid var(--grid-2);
                background:#fff; color:var(--ink-2); display:inline-flex;
                align-items:center; gap:5px; font-size:8.5px; font-weight:700;
                cursor:pointer; transition:all .15s; text-decoration:none;
            }
            .hu-btn:hover { background:var(--head); border-color:var(--accent-bd); color:var(--accent); }
            .hu-btn-accent { background:var(--accent); color:#fff; border-color:var(--accent); }
            .hu-btn-accent:hover { background:#2e5c43; color:#fff; border-color:#2e5c43; }
            .hu-btn-danger { color:#b91c1c; border-color:#fecaca; background:#fef2f2; }
            .hu-btn-danger:hover { background:#fee2e2; color:#991b1b; }

            .reservas-soft .pill {
                display:inline-flex; align-items:center; padding:2px 8px;
                border-radius:12px; font-size:7.5px; font-weight:700; border:1px solid;
                text-transform:uppercase; letter-spacing:.3px;
            }
            .reservas-soft .pill-success { color:#15803d; background:#f0fdf4; border-color:#dcfce7; }
            .reservas-soft .pill-warning { color:#b45309; background:#fffbeb; border-color:#fef3c7; }
            .reservas-soft .pill-secondary { color:#64748b; background:#f8fafc; border-color:#e2e8f0; }

            .hu-alert-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:6px; padding:8px 10px; font-size:8.5px; margin-bottom:10px; }
            .hu-alert-ok  { background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; border-radius:6px; padding:8px 10px; font-size:8.5px; margin-bottom:10px; }

            /* ── PANEL DEBUG (TIP-v1.3) ── */
            #huDbgToggle { position:fixed; right:0; bottom:0; z-index:1090; height:16px; padding:0 8px; border-radius:3px 0 0 0; border:1px solid #1e293b; border-right:0; border-bottom:0; background:rgba(2,6,23,.94); color:#64748b; font-family:ui-monospace, monospace; font-size:8px; font-weight:700; letter-spacing:.18em; line-height:1; cursor:pointer; display:flex; align-items:center; gap:5px; user-select:none; }
            #huDbgToggle:hover { color:#38bdf8; border-color:#334155; background:rgba(8,15,30,.97); }
            .hu-dbg-led { width:5px; height:5px; border-radius:1px; background:#10b981; box-shadow:0 0 5px rgba(16,185,129,.75); }
            .hu-dbg-led.err { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,.85); animation:huDbgBlink 1s steps(2,start) infinite; }
            @keyframes huDbgBlink { 50% { opacity:.35; } }
            .hu-dbg-badge { display:none; color:#f87171; font-size:8px; font-weight:700; }
            #huDbgPanel { position:fixed; right:0; bottom:20px; z-index:1091; width:400px; max-width:calc(100vw - 16px); max-height:60vh; display:none; flex-direction:column; background:rgba(2,6,23,.97); color:#e2e8f0; border:1px solid #1e293b; border-right:0; border-bottom:0; border-radius:6px 0 0 0; box-shadow:0 12px 32px rgba(0,0,0,.55); overflow:hidden; font-family:ui-monospace, monospace; }
            .hu-dbg-header { display:flex; justify-content:space-between; align-items:center; padding:5px 8px; background:#0b1220; border-bottom:1px solid #1e293b; font-size:9px; font-weight:700; color:#38bdf8; cursor:pointer; }
            .hu-dbg-controls { display:flex; gap:4px; }
            .hu-dbg-btn { background:#334155; color:#e2e8f0; border:0; border-radius:4px; font-size:9px; padding:2px 7px; cursor:pointer; }
            .hu-dbg-btn:hover { background:#475569; }
            #huDbgBody { overflow-y:auto; padding:6px 8px; font-size:9.5px; line-height:1.55; }
            .hu-dbg-line { white-space:pre-wrap; word-break:break-word; padding:2px 0; border-bottom:1px dashed #1e293b; }
            .hu-dbg-time { color:#64748b; margin-right:4px; }
            .hu-dbg-info { color:#93c5fd; } .hu-dbg-ok { color:#86efac; } .hu-dbg-warn { color:#fde68a; } .hu-dbg-err { color:#fca5a5; } .hu-dbg-net { color:#c4b5fd; } .hu-dbg-ev { color:#f9a8d4; }
        </style>

        {{-- BARRA SUPERIOR: TÍTULO + SELECTOR REACTIVO DE RESERVA --}}
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap" style="gap:8px; border-color:var(--grid) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="val" style="font-size:11px;">
                    <i class="bi bi-people-fill text-success me-1"></i> Gestión de Huéspedes y Cupos
                </span>
                @if($tieneTitular)
                    <span class="pill pill-success"><i class="bi bi-person-check-fill me-1"></i>Titular incluido</span>
                @elseif($reserva)
                    <span class="pill pill-warning"><i class="bi bi-exclamation-circle me-1"></i>Falta incluir Titular</span>
                @endif
            </div>

            @if($misReservas->count() > 0)
                <div class="d-flex align-items-center gap-2">
                    <span class="sub d-none d-sm-inline">Reserva:</span>
                    <select class="hu-select-res" id="huSelectReserva">
                        @foreach($misReservas as $mr)
                            @php $mrFi = $mr->fecha_inicio ? \Carbon\Carbon::parse($mr->fecha_inicio)->format('d/m/y') : '—'; @endphp
                            <option value="{{ $mr->id }}"
                                    data-cod="{{ $mr->codigo_reserva }}"
                                    {{ ($reserva && $reserva->id === $mr->id) ? 'selected' : '' }}>
                                #{{ $mr->codigo_reserva }} · {{ optional($mr->inmueble)->name ?? 'Inmueble' }} ({{ $mrFi }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @if(!$reserva)
            <div class="text-center py-5 bg-light border rounded-3" style="border-style:dashed !important;">
                <i class="bi bi-people text-muted d-block mb-2" style="font-size:24px;"></i>
                <span class="sub d-block mb-2">Aún no tienes reservas activas para registrar huéspedes.</span>
                <button type="button" class="hu-btn hu-btn-accent"
                        onclick="window.HuDbg && window.HuDbg.log('Click: Ir a Crear Reserva', 'ev'); var t = document.querySelector('[data-bs-target=\'#cli-nueva\']'); if(t) t.click();">
                    Crear Reserva
                </button>
            </div>
        @else

            <div id="huMainAlert" class="d-none"></div>

            {{-- ==================================================================== --}}
            {{-- PASO 1 OBLIGATORIO: INCLUIR PRIMERO AL USUARIO TITULAR DE LA RESERVA --}}
            {{-- ==================================================================== --}}
            @if(!$tieneTitular && !$bloqueadaPorEstado)
                <div class="hu-titular-gate" id="huTitularGate">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                        <div>
                            <span class="hu-step-badge mb-1"><i class="bi bi-1-circle-fill"></i> Paso 1 obligatorio</span>
                            <div class="val mt-1" style="font-size:12px;">
                                Primero debes incluirte como Huésped Titular de la reserva #{{ $reserva->codigo_reserva }}
                            </div>
                            <div class="sub">
                                Hemos cargado tus datos de usuario (<b>{{ $userFullName }}</b>). Confirma tu documento y oprime el botón para ocupar el primer cupo y habilitar los <b>{{ max(0, $capMaxima - 1) }}</b> acompañantes restantes.
                            </div>
                        </div>
                    </div>

                    <form action="{{ $rutaStoreHuesped }}" method="POST" class="js-hu-ajax-form mt-2" data-op="incluir-titular">
                        @csrf
                        <input type="hidden" name="_ajax" value="1">
                        <input type="hidden" name="id_rsv_reservas" value="{{ $reserva->id }}">
                        <input type="hidden" name="id_rsv_reserva" value="{{ $reserva->id }}">
                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <input type="hidden" name="id_user_registrador" value="{{ $uid }}">
                        <input type="hidden" name="id_user" value="{{ $uid }}">
                        <input type="hidden" name="es_titular" value="1">

                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-3">
                                <label class="lbl d-block mb-1">Tus Nombres</label>
                                <input type="text" name="nombre" class="hu-input" value="{{ $userFirstName }}" required maxlength="120">
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="lbl d-block mb-1">Tus Apellidos</label>
                                <input type="text" name="apellidos" class="hu-input" value="{{ $userLastName }}" required maxlength="120">
                            </div>
                            <div class="col-4 col-sm-2">
                                <label class="lbl d-block mb-1">Tipo Doc.</label>
                                <select name="tipo_documento" class="hu-input" required>
                                    @foreach(['CC','CE','PAS','TI'] as $td)
                                        <option value="{{ $td }}" {{ ($userTipoDoc === $td) ? 'selected' : '' }}>{{ $td }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8 col-sm-2">
                                <label class="lbl d-block mb-1">Tu N° Documento</label>
                                <input type="text" name="numero_documento" class="hu-input" value="{{ $userNumDoc }}" placeholder="Ingresa tu C.C." required maxlength="40">
                            </div>
                            <div class="col-12 col-sm-2">
                                <button type="submit" class="hu-btn hu-btn-accent w-100 justify-content-center" style="height:30px;">
                                    <i class="bi bi-person-check-fill"></i> Incluirme como Titular
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ==================================================================== --}}
            {{-- BARRA DE CUPO + BOTÓN PARA ACOMPAÑANTES (SOLO SI YA ESTÁ EL TITULAR) --}}
            {{-- ==================================================================== --}}
            <div class="hu-quota-card">
                <div style="min-width:220px; flex:1;">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="val" style="font-size:11.5px;">{{ $inm->name ?? 'Propiedad' }}</span>
                        <span class="sub">#{{ $reserva->codigo_reserva }}</span>
                        <span class="pill {{ $disponibles > 0 ? 'pill-success' : 'pill-warning' }}">
                            Ocupados: {{ $ocupados }} de {{ $capMaxima }} cupos
                        </span>
                        @if($disponibles > 0)
                            <span class="pill pill-secondary">Faltan {{ $disponibles }} por completar</span>
                        @endif
                    </div>
                    <div class="progress my-1" style="height:5px; max-width:300px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctCupo }}%;"></div>
                    </div>
                    <span class="sub">
                        @if(!$tieneTitular)
                            <i class="bi bi-lock-fill text-warning me-1"></i> Confirma primero tu registro como <b>Titular</b> arriba para desbloquear los acompañantes.
                        @elseif($disponibles > 0)
                            Ya estás incluido como Titular. Puedes agregar los <b>{{ $disponibles }}</b> acompañantes restantes ahora o el día de la reserva.
                        @else
                            Cupo completo ({{ $capMaxima }}/{{ $capMaxima }}). Haz clic en cualquier fila abajo para actualizar datos.
                        @endif
                    </span>
                </div>

                @if($tieneTitular && !$bloqueadaPorEstado && $disponibles > 0)
                    <button type="button" class="hu-btn hu-btn-accent" id="huBtnToggleAdd">
                        <i class="bi bi-person-plus-fill"></i> Añadir Acompañante ({{ $disponibles }} disponibles)
                    </button>
                @endif
            </div>

            {{-- FORMULARIO DESPLEGABLE PARA ACOMPAÑANTES RESTANTES (PASO 2) --}}
            @if($tieneTitular && !$bloqueadaPorEstado && $disponibles > 0)
                <div id="huNewGuestBox" class="p-3 mb-3 rounded-3 border d-none" style="background:var(--head); border-color:var(--accent-bd) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="val" style="color:var(--accent);">
                            <i class="bi bi-person-plus me-1"></i> Registrar Acompañante #{{ $ocupados + 1 }} de {{ $capMaxima }}
                        </span>
                        <button type="button" class="btn-close" style="font-size:8px;" id="huBtnCloseAdd"></button>
                    </div>

                    <form action="{{ $rutaStoreHuesped }}" method="POST" class="js-hu-ajax-form" data-op="crear-acompanante">
                        @csrf
                        <input type="hidden" name="_ajax" value="1">
                        <input type="hidden" name="id_rsv_reservas" value="{{ $reserva->id }}">
                        <input type="hidden" name="id_rsv_reserva" value="{{ $reserva->id }}">
                        <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                        <input type="hidden" name="id_user_registrador" value="{{ $uid }}">
                        <input type="hidden" name="id_user" value="{{ $uid }}">
                        <input type="hidden" name="es_titular" value="0">

                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-sm-3">
                                <label class="lbl d-block mb-1">Nombres</label>
                                <input type="text" name="nombre" class="hu-input" placeholder="Nombres del acompañante" required maxlength="120">
                            </div>
                            <div class="col-12 col-sm-3">
                                <label class="lbl d-block mb-1">Apellidos</label>
                                <input type="text" name="apellidos" class="hu-input" placeholder="Apellidos" required maxlength="120">
                            </div>
                            <div class="col-5 col-sm-2">
                                <label class="lbl d-block mb-1">Tipo Doc.</label>
                                <select name="tipo_documento" class="hu-input" required>
                                    <option value="CC">CC</option>
                                    <option value="TI">TI</option>
                                    <option value="RC">RC</option>
                                    <option value="CE">CE</option>
                                    <option value="PAS">PAS</option>
                                </select>
                            </div>
                            <div class="col-7 col-sm-2">
                                <label class="lbl d-block mb-1">N° Documento</label>
                                <input type="text" name="numero_documento" class="hu-input" placeholder="Documento" required maxlength="40">
                            </div>
                            <div class="col-12 col-sm-2">
                                <button type="submit" class="hu-btn hu-btn-accent w-100 justify-content-center">
                                    <i class="bi bi-check2"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- LISTA DE HUÉSPEDES YA AGREGADOS --}}
            @forelse($huespedesReserva as $idx => $h)
                @php
                    $iniciales  = mb_strtoupper(mb_substr($h->nombre ?? 'H', 0, 1) . mb_substr($h->apellidos ?? '', 0, 1));
                    $urlUpdate  = $nombreRutaUpdate ? route($nombreRutaUpdate, $h->id) : ($rutaStoreHuesped . '/' . $h->id);
                    $urlDestroy = $nombreRutaDestroy ? route($nombreRutaDestroy, $h->id) : ($rutaStoreHuesped . '/' . $h->id);
                @endphp

                <div class="hu-guest-card" id="huGuestCard_{{ $h->id }}" data-guest-id="{{ $h->id }}" data-guest-name="{{ $h->nombre }} {{ $h->apellidos }}">
                    <div class="hu-guest-row js-hu-toggle-row">
                        <div class="d-flex align-items-center gap-3" style="min-width:0;">
                            <div class="hu-avatar {{ $h->es_titular ? 'is-titular' : '' }}">
                                {{ $iniciales }}
                            </div>
                            <div style="min-width:0;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="val text-truncate">{{ $h->nombre }} {{ $h->apellidos }}</span>
                                    @if($h->es_titular)
                                        <span class="pill pill-success"><i class="bi bi-star-fill me-1" style="font-size:6.5px;"></i>Titular (Tú)</span>
                                    @else
                                        <span class="pill pill-secondary">Cupo #{{ $idx + 1 }} · Acompañante</span>
                                    @endif
                                </div>
                                <div class="sub mt-1">
                                    {{ $h->tipo_documento ?? 'CC' }} · <b>{{ $h->numero_documento ?? 'S/N' }}</b>
                                    @if($h->registrador)
                                        <span class="mx-1">·</span> Reg: {{ $h->registrador->name }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="sub d-none d-sm-inline">Actualizar</span>
                            <i class="bi bi-chevron-right hu-chevron"></i>
                        </div>
                    </div>

                    {{-- Formulario Inline para Actualizar Datos --}}
                    <div class="hu-guest-edit">
                        @if(!$bloqueadaPorEstado)
                            <form action="{{ $urlUpdate }}" method="POST" class="js-hu-ajax-form" data-op="actualizar-huesped-#{{ $h->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_ajax" value="1">
                                <input type="hidden" name="id_rsv_reservas" value="{{ $reserva->id }}">
                                <input type="hidden" name="id_rsv_reserva" value="{{ $reserva->id }}">
                                <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                                <input type="hidden" name="id_user_registrador" value="{{ $uid }}">
                                <input type="hidden" name="id_user" value="{{ $uid }}">

                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-sm-3">
                                        <label class="lbl d-block mb-1">Nombres</label>
                                        <input type="text" name="nombre" class="hu-input" value="{{ $h->nombre }}" required>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <label class="lbl d-block mb-1">Apellidos</label>
                                        <input type="text" name="apellidos" class="hu-input" value="{{ $h->apellidos }}" required>
                                    </div>
                                    <div class="col-4 col-sm-2">
                                        <label class="lbl d-block mb-1">Tipo Doc.</label>
                                        <select name="tipo_documento" class="hu-input" required>
                                            @foreach(['CC','TI','RC','CE','PAS'] as $td)
                                                <option value="{{ $td }}" {{ ($h->tipo_documento === $td) ? 'selected' : '' }}>{{ $td }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-8 col-sm-2">
                                        <label class="lbl d-block mb-1">N° Documento</label>
                                        <input type="text" name="numero_documento" class="hu-input" value="{{ $h->numero_documento }}" required>
                                    </div>
                                    <div class="col-12 col-sm-2 d-flex gap-1 justify-content-end">
                                        <input type="hidden" name="es_titular" value="{{ $h->es_titular ? 1 : 0 }}">
                                        <button type="submit" class="hu-btn hu-btn-accent flex-grow-1 justify-content-center">
                                            <i class="bi bi-check2"></i> Guardar
                                        </button>
                                        @if(!$h->es_titular)
                                            <button type="button" class="hu-btn hu-btn-danger js-hu-del-btn"
                                                    data-url="{{ $urlDestroy }}"
                                                    data-id="{{ $h->id }}"
                                                    data-nombre="{{ $h->nombre }} {{ $h->apellidos }}"
                                                    title="Retirar acompañante">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="sub">Esta reserva no permite modificaciones adicionales.</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 bg-light border rounded-3" style="border-style:dashed !important;">
                    <span class="sub d-block">Oprime arriba <b>"Incluirme como Titular"</b> para registrar el primer cupo de esta reserva.</span>
                </div>
            @endforelse

        @endif

    </div>
</div>

<script>
(function () {
    'use strict';

    /* ═══ PANEL DE DEBUG LOCAL HU-v3-DBG PERSISTENTE (diseño TIP-v1.3) ═══ */
    var DBG = (window.HuDbg && window.HuDbg.alive && window.HuDbg.alive())
        ? window.HuDbg
        : (function () {
            var MAX = 300, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                var rootDbg = document.querySelector('#sheet-huespedes-cliente') || document.body;
                if (!$('huDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'huDbgToggle'; t.title = 'Debug Huéspedes (HU-v3-DBG)';
                    t.innerHTML = '<span class="hu-dbg-led" id="huDbgLed"></span>DBG<span class="hu-dbg-badge" id="huDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('huDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'huDbgPanel';
                    p.innerHTML = '<div class="hu-dbg-header" id="huDbgHeader">' +
                        '<span>DEBUG · Huéspedes Cliente · HU-v3-DBG</span>' +
                        '<span class="hu-dbg-controls">' +
                        '<button type="button" class="hu-dbg-btn" id="huDbgRefresh">↻ Frag</button>' +
                        '<button type="button" class="hu-dbg-btn" id="huDbgCopy">Copiar</button>' +
                        '<button type="button" class="hu-dbg-btn" id="huDbgClear">Limpiar</button>' +
                        '<button type="button" class="hu-dbg-btn" id="huDbgClose">✕</button>' +
                        '</span></div><div id="huDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            function apilar() {
                var ids = ['psDbgToggle', 'chkDbgToggle', 'nrDbgToggle', 'rvDebugToggle', 'mrDbgToggle', 'mrIniDbgToggle', 'apDbgToggle', 'itDbgToggle'];
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) continue;
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) continue;
                    var desdeAbajo = window.innerHeight - r.top;
                    if (desdeAbajo > ocupado) ocupado = desdeAbajo;
                }
                var t = $('huDbgToggle'), p = $('huDbgPanel');
                if (t) t.style.bottom = '0px';
                if (p) p.style.bottom = Math.max(20, Math.ceil(ocupado + 4)) + 'px';
            }
            function hora() {
                var d = new Date(); function f(n) { return (n < 10 ? '0' : '') + n; }
                return f(d.getHours()) + ':' + f(d.getMinutes()) + ':' + f(d.getSeconds());
            }
            var api = {
                log: function (txt, nivel) {
                    var b = $('huDbgBody'); if (!b) return;
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'hu-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c) { return c === '<' ? '&lt;' : (c === '>' ? '&gt;' : '&amp;'); });
                    ln.innerHTML = '<span class="hu-dbg-time">[' + hora() + ']</span><span class="hu-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) b.removeChild(b.firstChild);
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('huDbgBadge'); if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('huDbgLed'); if (led) led.classList.add('err');
                    }
                },
                open: function () {
                    var p = $('huDbgPanel'); if (!p) return;
                    p.style.display = 'flex'; apilar();
                    var bd = $('huDbgBadge'); if (bd) bd.style.display = 'none';
                    var led = $('huDbgLed'); if (led) led.classList.remove('err');
                    numErr = 0;
                },
                close: function () { var p = $('huDbgPanel'); if (p) p.style.display = 'none'; },
                togglePanel: function () { var p = $('huDbgPanel'); if (p) (p.style.display === 'flex') ? api.close() : api.open(); },
                clear: function () { var b = $('huDbgBody'); if (b) b.innerHTML = ''; },
                copy: function () {
                    var b = $('huDbgBody'); if (!b) return;
                    var txt = '=== DEBUG Huéspedes HU-v3-DBG ===\n';
                    for (var i = 0; i < b.children.length; i++) txt += b.children[i].textContent + '\n';
                    if (navigator.clipboard) navigator.clipboard.writeText(txt);
                },
                ensure: function () { asegurarNodos(); },
                alive: function () { return !!$('huDbgToggle'); }
            };
            window.HuDbg = api;
            return api;
        })();

    DBG.ensure();
    var hudbg = function (t, l) { if (DBG && DBG.log) DBG.log(t, l); };

    // Eliminar duplicados de barras DBG si el fragmento fue re-inyectado
    function limpiarDbgDuplicados() {
        var ts = document.querySelectorAll('#huDbgToggle');
        for (var i = 1; i < ts.length; i++) ts[i].remove();
        var ps = document.querySelectorAll('#huDbgPanel');
        for (var j = 1; j < ps.length; j++) ps[j].remove();
    }
    limpiarDbgDuplicados();

    // ═══ RECARGA DEL FRAGMENTO VÍA ClientePortalController@misReservasHuespedesFragmento (SIN RECARGAR PÁGINA) ═══
    window.huReloadFragment = function (reservaIdOpcional, motivo) {
        var root = document.getElementById('cliHuesRoot');
        if (!root) return Promise.resolve();

        var fragBase = root.getAttribute('data-frag') || window.location.pathname;
        var fragKey  = root.getAttribute('data-fragkey') || 'reservas-huespedes';
        var resId    = reservaIdOpcional || root.getAttribute('data-reserva-id') || '';

        var urlObj = new URL(fragBase, window.location.origin);
        urlObj.searchParams.set('tab', fragKey);
        urlObj.searchParams.set('fragmento', fragKey);
        if (resId) urlObj.searchParams.set('reserva_id', resId);
        urlObj.searchParams.set('_t', Date.now());

        // Sincronizar URL del navegador sin recargar la página
        try {
            var navUrl = new URL(window.location.href);
            if (resId) navUrl.searchParams.set('reserva_id', resId);
            window.history.replaceState({}, document.title, navUrl.toString());
        } catch (e) {}

        var t0 = performance.now();
        hudbg('GET Fragmento (' + (motivo || 'sync') + ') → ' + urlObj.pathname + urlObj.search, 'net');

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
                hudbg('Error al cargar fragmento HTTP ' + res.status + ' (' + res.ms + 'ms)', 'err');
                return;
            }
            var doc = new DOMParser().parseFromString(res.html, 'text/html');
            var nuevoRoot = doc.getElementById('cliHuesRoot');
            if (nuevoRoot) {
                root.replaceWith(nuevoRoot);
                limpiarDbgDuplicados();
                var cod  = nuevoRoot.getAttribute('data-reserva-cod') || '?';
                var tit  = nuevoRoot.getAttribute('data-titular') || '?';
                var cupo = nuevoRoot.getAttribute('data-cupo') || '0/0';
                hudbg('Fragmento actualizado (' + res.ms + 'ms) · reserva=#' + cod + ' · titular=' + tit + ' · cupo=' + cupo, 'ok');
            } else {
                hudbg('Respuesta recibida (' + res.ms + 'ms), pero no contenía #cliHuesRoot', 'warn');
            }
        })
        .catch(function (err) {
            hudbg('Excepción recargando fragmento: ' + (err && err.message ? err.message : 'red'), 'err');
        });
    };

    // Registrar handlers globales una sola vez
    if (!window.__jsHuespedesClienteV3Listo) {
        window.__jsHuespedesClienteV3Listo = true;

        // 1. Delegación de clics (DBG, Acordeones, Botón Añadir, Eliminar)
        document.addEventListener('click', function (e) {
            var t = e.target; if (!t || !t.closest) return;

            if (t.closest('#huDbgToggle')) { DBG.togglePanel(); return; }
            if (t.closest('#huDbgClose'))  { e.stopPropagation(); DBG.close(); return; }
            if (t.closest('#huDbgClear'))  { e.stopPropagation(); DBG.clear(); return; }
            if (t.closest('#huDbgCopy'))   { e.stopPropagation(); DBG.copy(); hudbg('Logs copiados al portapapeles', 'info'); return; }
            if (t.closest('#huDbgRefresh')) {
                e.stopPropagation();
                window.huReloadFragment(null, 'manual-btn-dbg');
                return;
            }

            // Abrir/cerrar formulario de nuevo acompañante
            if (t.closest('#huBtnToggleAdd')) {
                var box = document.getElementById('huNewGuestBox');
                if (box) {
                    var abriendo = box.classList.contains('d-none');
                    box.classList.toggle('d-none');
                    hudbg('Click: ' + (abriendo ? 'Abrir' : 'Cerrar') + ' formulario Añadir Acompañante', 'ev');
                }
                return;
            }
            if (t.closest('#huBtnCloseAdd')) {
                var boxC = document.getElementById('huNewGuestBox');
                if (boxC) boxC.classList.add('d-none');
                hudbg('Click: Cerrar formulario Añadir Acompañante', 'ev');
                return;
            }

            // Abrir/cerrar fila de edición inline de un huésped
            var rowToggle = t.closest('.js-hu-toggle-row');
            if (rowToggle) {
                var card = rowToggle.closest('.hu-guest-card');
                if (card) {
                    var abrir = !card.classList.contains('open');
                    card.classList.toggle('open');
                    hudbg('Click: ' + (abrir ? 'Expandir' : 'Colapsar') + ' edición de huésped #' +
                          (card.getAttribute('data-guest-id') || '?') + ' (' + (card.getAttribute('data-guest-name') || '') + ')', 'ev');
                }
                return;
            }

            // Eliminar acompañante (SoftDelete) sin recargar página
            var delBtn = t.closest('.js-hu-del-btn');
            if (delBtn) {
                e.preventDefault();
                var url = delBtn.getAttribute('data-url');
                var gid = delBtn.getAttribute('data-id') || '?';
                var nom = delBtn.getAttribute('data-nombre') || 'acompañante';
                hudbg('Click: Solicitud retirar acompañante #' + gid + ' (' + nom + ')', 'warn');

                if (!url || !confirm('¿Deseas retirar a ' + nom + ' de esta reserva?')) {
                    hudbg('Cancelado por usuario retiro de #' + gid, 'info');
                    return;
                }

                var tokMt = document.querySelector('meta[name="csrf-token"]');
                var tokIn = document.querySelector('input[name="_token"]');
                var token = (tokMt && tokMt.content) || (tokIn && tokIn.value) || '';

                var fd = new FormData();
                fd.append('_method', 'DELETE');
                fd.append('_token', token);

                var t0 = performance.now();
                hudbg('DELETE (retirar-acompanante #' + gid + ') → ' + url, 'net');

                fetch(url, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token, 'Accept': 'application/json, text/html' },
                    body: fd
                })
                .then(function (r) {
                    var ms = Math.round(performance.now() - t0);
                    if (r.ok) {
                        hudbg('Acompañante #' + gid + ' retirado con éxito (HTTP ' + r.status + ' · ' + ms + 'ms)', 'ok');
                        window.huReloadFragment(null, 'post-delete-#' + gid);
                    } else {
                        hudbg('Error al retirar #' + gid + ' (HTTP ' + r.status + ' · ' + ms + 'ms)', 'err');
                    }
                })
                .catch(function (err) {
                    hudbg('Fallo de red al retirar #' + gid + ': ' + (err.message || ''), 'err');
                });
            }
        });

        // 2. Delegación de cambios en inputs y selector de reserva
        document.addEventListener('change', function (e) {
            var t = e.target; if (!t) return;

            // Selector de Reserva (carga por fragmento AJAX sin recargar toda la vista)
            if (t.id === 'huSelectReserva') {
                var opt = t.options[t.selectedIndex];
                var cod = opt ? opt.getAttribute('data-cod') : t.value;
                hudbg('Selector: Cambio a reserva #' + cod + ' (id=' + t.value + ')', 'ev');
                window.huReloadFragment(t.value, 'cambio-reserva');
                return;
            }

            // Trazabilidad cuando el usuario edita un campo dentro de #cliHuesRoot
            if (t.closest && t.closest('#cliHuesRoot') && t.name) {
                hudbg('Campo modificado [' + t.name + '] = "' + t.value + '"', 'info');
            }
        });

        // 3. Envío AJAX de Formularios (Incluir Titular, Crear Acompañante, Actualizar Huésped)
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.classList || !form.classList.contains('js-hu-ajax-form')) return;
            e.preventDefault();

            var op  = form.getAttribute('data-op') || 'guardar';
            var btn = form.querySelector('button[type="submit"]');
            var oldHtml = btn ? btn.innerHTML : '';
            var alertBox = document.getElementById('huMainAlert');
            if (alertBox) { alertBox.className = 'd-none'; alertBox.innerHTML = ''; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Guardando…'; }

            var fd = new FormData(form);
            var resumenPayload = (fd.get('nombre') || '') + ' ' + (fd.get('apellidos') || '') +
                                 ' (' + (fd.get('tipo_documento') || '') + ' ' + (fd.get('numero_documento') || '') +
                                 ' · titular=' + (fd.get('es_titular') || '0') + ')';

            var tokIn = form.querySelector('input[name="_token"]');
            var tokMt = document.querySelector('meta[name="csrf-token"]');
            var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';

            var t0 = performance.now();
            hudbg('Enviando (' + op + ') → ' + form.getAttribute('action') + ' · payload: ' + resumenPayload, 'net');

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
                    hudbg('Operación exitosa (' + op + ') · HTTP ' + res.status + ' (' + res.ms + 'ms)', 'ok');
                    // Refrescar el fragmento desde ClientePortalController sin recargar la página
                    return window.huReloadFragment(null, 'post-' + op).then(function () {
                        var nuevoAlert = document.getElementById('huMainAlert');
                        if (nuevoAlert) {
                            nuevoAlert.className = 'hu-alert-ok';
                            nuevoAlert.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' +
                                (op === 'incluir-titular'
                                    ? '¡Listo! Quedaste incluido como Titular. Ya puedes agregar los acompañantes restantes.'
                                    : 'Datos de huéspedes actualizados correctamente.');
                        }
                    });
                }

                // Errores de validación 422 o servidor
                hudbg('Respuesta con error (' + op + ') · HTTP ' + res.status + ' (' + res.ms + 'ms): ' + (j.message || 'Verifica los datos'), 'err');
                var listaErrores = [];
                if (j.errors && typeof j.errors === 'object') {
                    Object.keys(j.errors).forEach(function (campo) {
                        [].concat(j.errors[campo]).forEach(function (m) {
                            listaErrores.push('<li><b>' + campo + ':</b> ' + m + '</li>');
                            hudbg('422 [' + campo + ']: ' + m, 'err');
                        });
                    });
                }
                if (alertBox) {
                    alertBox.className = 'hu-alert-err';
                    alertBox.innerHTML = '<b>' + (j.message || 'No se pudo guardar el huésped') + '</b>' +
                        (listaErrores.length ? '<ul class="mb-0 mt-1 ps-3">' + listaErrores.join('') + '</ul>' : '');
                }
            })
            .catch(function (err) {
                hudbg('Error de red en (' + op + '): ' + (err && err.message ? err.message : 'desconocido'), 'err');
                if (alertBox) { alertBox.className = 'hu-alert-err'; alertBox.innerHTML = 'Error de conexión.'; }
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = oldHtml; }
            });
        });
    }

    var SRV = @json($huDbgData);
    hudbg('HU-v3-DBG activo · reserva=' + (SRV && SRV.reserva_cod ? '#' + SRV.reserva_cod : 'ninguna') +
          ' · titular=' + (SRV && SRV.tiene_titular ? 'SÍ' : 'PENDIENTE') +
          ' · cupo=' + (SRV ? SRV.ocupados + '/' + SRV.cap_maxima : '0/0') +
          ' · disp=' + (SRV ? SRV.disponibles : 0), 'ok');
    if (SRV && SRV.errores && SRV.errores.length) {
        SRV.errores.forEach(function (er) { hudbg('SQL/PHP: ' + er, 'err'); });
    }
})();
</script>
