{{-- ============================================================================
     RSV · ADMIN · FINANZAS · TRANSACCIONES · TRX-v3.1-DBG
     Parcial: resources/views/rsv/admin/partials/finanzas/transacciones.blade.php

     · DISEÑO UNIFICADO: estética "reservas-soft" (variables CSS, tabla
       compacta, btn-mini/btn-green, count-badge, xls-search y modales
       Bootstrap modal-sm idénticos al resto de pestañas del admin).
     · CRUD AJAX sin recargar: alta / edición / anulación lógica por fetch
       (estado_pago = Anulado, nunca borra · igual que el controlador);
       tras guardar refresca SOLO #sheet-transacciones (todas las copias).
     · MULTI-COPIA: la hoja puede aparecer más de una vez en la página;
       los id de modales se "unican" por copia (-c2, -c3…) y el refresco
       AJAX reemplaza todas las copias con el fragmento fresco.
     · RUTAS RESILIENTES: resueltas con route() y candidatos por si el
       nombre del resource cambia; si no hay ruta, la acción se oculta.
     · PANEL DE DEBUG (CAMBIO TRX-v1 · diseño TIP-v1.3 aprobado): pestaña
       terminal "DBG" de 16px fija SIEMPRE en la esquina inferior-derecha
       (right:0 · bottom:0, radius 3px solo superior-izquierdo) con LED de
       estado (verde = ok · rojo pulsante + conteo = errores) y panel cajón
       de 380px alineado al mismo borde. La regla exacta de distancias está
       documentada como "SPEC DE DISTANCIAS" dentro del CSS, y el panel
       esquiva a los toggles hermanos de las otras vistas midiendo píxeles
       reales (getBoundingClientRect · bottom = max(20, vecino + 4)).
       Exclusivo de esta vista: el toggle y el panel nacen y mueren con el
       parcial (hermanos de #sheet-transacciones, fuera del contenedor que
       intercambia el refresco AJAX, así que sobreviven a crear/editar/
       anular) y el panel deduplica copias: un solo debug por vista.
       Telemetría que reporta: totales del servidor, página y desglose de
       estados, envíos AJAX (crear/editar/anular) con método real (PUT/
       DELETE por spoofing), errores 422 campo a campo, refresco del
       fragmento con tiempos, copias detectadas y apertura de detalle.
     · TRX-v3 (mejora de estructura): botones "Actualizar" (recarga AJAX del
       fragmento con icono girando y pulso verde de confirmación, id
       trxRefresh) y "Exportar" (CSV compatible con Excel · BOM UTF-8 ·
       separador ; de TODO lo filtrado en pantalla, id trxExport), montados
       por delegación en document para sobrevivir a los refrescos. IDs con
       prefijo trx* a propósito: NO reutilizan rhRefresh/rhExport de la
       pestaña Huéspedes, que convive en la misma página (ids duplicados se
       pisarían entre pestañas).
     · FIX MODO SINGLE (un solo inmueble seleccionado): el contexto ahora se
       resuelve en el SERVIDOR (modelo inmueble · variable inmuebleId ·
       request('inmueble_id')) y viaja SIEMPRE en la URL del fragmento
       (inmueble_id=todos|id). Antes solo se leía de la querystring del
       navegador: si la página llegaba sin ese parámetro, cada refresco AJAX
       (crear/editar/anular/Actualizar) devolvía el listado global y el
       filtro "no funcionaba". Además la vista es autosuficiente: si no
       llega transacciones ni la relación del inmueble, consulta la BD ella
       misma (whereHas reserva por inmueble · paginado 15 · reintento sin
       eager), igual que hace la vista de Huéspedes.
     · Badge de contexto en el header: con un inmueble seleccionado se
       muestra su nombre junto al contador, para ver el filtro activo.
     · Apilado del panel: el vecino de Huéspedes es rhDbgToggle (antes se
       apilaba contra "rhDebugToggle", id inexistente → podían solaparse).
     · TRX-v3 (fechas + eliminación lógica · base: tu TRX-v2-DBG):
       (1) FILTROS POR RANGO DE FECHAS en el header: #trxFechaDesde y
       #trxFechaHasta con botones Aplicar y Este mes, y aplicación
       automática al cambiar la fecha; todo por delegación en document
       (sobrevive a los refrescos). El rango viaja SIEMPRE en la URL del
       fragmento (fecha_desde / fecha_hasta) y se aplica en SERVIDOR con
       whereBetween sobre created_at · badge con el rango activo.
       (2) CARGA SOLO LOS DEL MES: sin fechas en la URL se carga
       únicamente el mes en curso (día 1 00:00 → último día 23:59:59);
       la vista consulta ella misma el modelo confirmado
       (TransaccionFinanciera · SoftDeletes) con prioridad sobre los
       datos del controlador, así la paginación del rango es real; el
       dataset del controlador queda de respaldo con el rango aplicado
       en memoria (avisando en el panel DBG).
       (3) VALIDACIÓN DE LA ELIMINACIÓN LÓGICA: el panel DBG reporta si
       el modelo usa SoftDeletes (deleted_at), el camino de anulación
       (DELETE con confirm() → estado_pago=Anulado + deleted_at · nunca
       borra físico ni el soporte de S3) y las filas ANULADAS se ven
       atenuadas (clase trx-anulada).
     · TRX-v3.1 (buscador instantáneo): el buscador filtra AL ESCRIBIR
       (cada tecla, sin Enter ni botones) en dos capas: la capa CLIENTE
       filtra al instante las filas ya cargadas y, 320ms después de la
       última tecla, el término q viaja en la URL del fragmento y el
       SERVIDOR busca en TODAS las páginas del rango (referencia, ID,
       código de reserva, pasarela, método, estado y monto · los mismos
       campos de data-trx-search). El intercambio de la hoja no pierde
       el foco ni el texto del buscador, Enter busca de inmediato,
       Actualizar y la paginación heredan q, y el panel DBG reporta
       cada búsqueda con su término y su tiempo.
     ============================================================================ --}}
@php
    $trxDbgErrores = []; // TRX-v3.1-DBG · colector de errores del servidor para el panel

    /* TRX-v3 · FIX MODO SINGLE: el contexto de inmueble se resuelve en el
       SERVIDOR con la misma cadena que la vista de Huéspedes: primero el
       modelo inmueble, luego la variable inmuebleId y por último la
       querystring. Ese id viaja SIEMPRE en la URL del fragmento. */
    $trxInm = null;
    $trxInmId = $inmuebleId ?? null;
    if (isset($inmueble) && $inmueble) {
        $trxInm = $inmueble;
        $trxInmId = is_object($trxInm) ? ($trxInm->id ?? null) : $trxInm;
    } elseif (empty($trxInmId) && request('inmueble_id') && is_numeric(request('inmueble_id'))) {
        $trxInmId = request('inmueble_id');
    }
    $trxGlobal = empty($trxInmId);

    /* ── TRX-v3 · FILTRO POR RANGO DE FECHAS · por defecto SOLO el mes en curso ──
       fecha_desde / fecha_hasta llegan por querystring (formato Y-m-d)
       desde los inputs #trxFechaDesde/#trxFechaHasta del header. Sin
       parámetros se carga únicamente el mes actual: día 1 00:00:00 →
       último día 23:59:59. El rango se aplica sobre created_at con
       whereBetween en SERVIDOR (paginación real del rango). */
    $trxHoy = now();
    $trxReqDesde = trim((string) request('fecha_desde', ''));
    $trxReqHasta = trim((string) request('fecha_hasta', ''));
    try { $trxDesde = $trxReqDesde !== '' ? \Carbon\Carbon::parse($trxReqDesde)->startOfDay() : $trxHoy->copy()->startOfMonth(); }
    catch (\Throwable $e) { $trxDesde = $trxHoy->copy()->startOfMonth(); }
    try { $trxHasta = $trxReqHasta !== '' ? \Carbon\Carbon::parse($trxReqHasta)->endOfDay() : $trxHoy->copy()->endOfMonth(); }
    catch (\Throwable $e) { $trxHasta = $trxHoy->copy()->endOfMonth(); }
    if ($trxHasta->lt($trxDesde)) { $trxTmp = $trxDesde; $trxDesde = $trxHasta->copy()->startOfDay(); $trxHasta = $trxTmp->copy()->endOfDay(); }
    $trxMesPorDefecto = ($trxReqDesde === '' && $trxReqHasta === '');

    /* ── TRX-v3.1 · BUSCADOR INSTANTÁNEO · término q en SERVIDOR ──
       Llega por querystring desde el buscador en vivo (#trxSearch) y
       filtra TODAS las páginas del rango, no solo la visible. Mismos
       campos que data-trx-search en cliente: referencia, ID, código
       de reserva, pasarela, método, estado y monto. */
    $trxQ = trim((string) request('q', ''));
    if (function_exists('mb_strlen') && mb_strlen($trxQ) > 100) { $trxQ = trim(mb_substr($trxQ, 0, 100)); }
    $trxQWhere = null;     // cierre completo (incluye pasarela por nombre)
    $trxQWhereSin = null;  // cierre de respaldo (sin pasarela, por si la columna no existe)
    if ($trxQ !== '') {
        $trxQLike = '%' . $trxQ . '%';
        $trxQEsId = ctype_digit($trxQ) ? (int) $trxQ : null;
        $trxQWhere = function ($qq) use ($trxQLike, $trxQEsId) {
            $qq->where(function ($w) use ($trxQLike, $trxQEsId) {
                $w->where('referencia_externa', 'like', $trxQLike)
                    ->orWhere('metodo_pago', 'like', $trxQLike)
                    ->orWhere('estado_pago', 'like', $trxQLike)
                    ->orWhere('monto', 'like', $trxQLike);
                if ($trxQEsId !== null) { $w->orWhere('id', $trxQEsId); }
                $w->orWhereHas('reserva', function ($r) use ($trxQLike) { $r->where('codigo_reserva', 'like', $trxQLike); })
                    ->orWhereHas('pasarela', function ($p) use ($trxQLike) { $p->where('nombre', 'like', $trxQLike); });
            });
        };
        $trxQWhereSin = function ($qq) use ($trxQLike, $trxQEsId) {
            $qq->where(function ($w) use ($trxQLike, $trxQEsId) {
                $w->where('referencia_externa', 'like', $trxQLike)
                    ->orWhere('metodo_pago', 'like', $trxQLike)
                    ->orWhere('estado_pago', 'like', $trxQLike)
                    ->orWhere('monto', 'like', $trxQLike);
                if ($trxQEsId !== null) { $w->orWhere('id', $trxQEsId); }
                $w->orWhereHas('reserva', function ($r) use ($trxQLike) { $r->where('codigo_reserva', 'like', $trxQLike); });
            });
        };
    }

    /* TRX-v3 · FUENTE DE DATOS con prioridad invertida respecto a v2: la
       vista consulta SIEMPRE por sí misma el modelo confirmado
       (TransaccionFinanciera · SoftDeletes) y ahí aplica el rango de
       fechas con whereBetween sobre created_at, así "solo los del mes"
       es exacto y la paginación es real. Si el modelo no existe o la
       consulta falla: relación del inmueble (también con fechas) y, de
       último, los datos del controlador con el rango aplicado EN MEMORIA. */
    $listadoTransacciones = null;
    $trxFuente = 'consulta propia (vista)';
    $trxModelo = class_exists('\App\Models\Rsv\TransaccionFinanciera') ? '\App\Models\Rsv\TransaccionFinanciera' : null;
    if (!$trxModelo) {
        foreach (['\\App\\Models\\Rsv\\RsvTransaccionFinanciera', '\\App\\Models\\Rsv\\TransaccionesFinancieras', '\\App\\Models\\TransaccionFinanciera', '\\App\\Models\\Rsv\\Transaccion'] as $trxClase) {
            if (class_exists($trxClase)) { $trxModelo = $trxClase; break; }
        }
    }
    if ($trxModelo) {
        try {
            $qTrx = $trxModelo::query()->with(['reserva:id,codigo_reserva', 'pasarela']);
            if (!$trxGlobal && is_numeric($trxInmId)) {
                $qTrx->whereHas('reserva', function ($qq) use ($trxInmId) {
                    $qq->where('id_rsv_catalogo_inmueble', $trxInmId);
                });
            }
            $qTrx->whereBetween('created_at', [$trxDesde, $trxHasta]); // TRX-v3 · rango de fechas en servidor
            if ($trxQWhere) { $qTrx->where($trxQWhere); } // TRX-v3.1 · búsqueda en todas las páginas
            $listadoTransacciones = $qTrx->orderByDesc('created_at')->paginate(15)->withQueryString();
            $trxFuente = 'consulta propia (vista · con fechas' . ($trxQ !== '' ? ' + búsqueda' : '') . ')';
        } catch (\Throwable $e) {
            $trxDbgErrores[] = 'consulta propia con fechas: ' . $e->getMessage();
            try {
                $qTrx2 = $trxModelo::query();
                if (!$trxGlobal && is_numeric($trxInmId)) {
                    $qTrx2->whereHas('reserva', function ($qq) use ($trxInmId) {
                        $qq->where('id_rsv_catalogo_inmueble', $trxInmId);
                    });
                }
                $qTrx2->whereBetween('created_at', [$trxDesde, $trxHasta]);
                if ($trxQWhereSin) { $qTrx2->where($trxQWhereSin); } // TRX-v3.1 · búsqueda (respaldo sin pasarela)
                $listadoTransacciones = $qTrx2->orderByDesc('created_at')->paginate(15)->withQueryString();
                $trxFuente = 'consulta propia (sin eager · con fechas' . ($trxQ !== '' ? ' + búsqueda' : '') . ')';
            } catch (\Throwable $e2) {
                $trxDbgErrores[] = 'dataset transacciones (reintento sin eager): ' . $e2->getMessage();
                $listadoTransacciones = null;
            }
        }
    }
    if ($listadoTransacciones === null && isset($inmueble) && is_object($inmueble) && method_exists($inmueble, 'transacciones')) {
        try {
            $qRelTrx = $inmueble->transacciones()->whereBetween('created_at', [$trxDesde, $trxHasta]);
            if ($trxQWhere) { $qRelTrx->where($trxQWhere); } // TRX-v3.1 · búsqueda en todas las páginas
            $listadoTransacciones = $qRelTrx->orderByDesc('created_at')->paginate(15)->withQueryString();
            $trxFuente = 'relación del inmueble (con fechas' . ($trxQ !== '' ? ' + búsqueda' : '') . ')';
        } catch (\Throwable $e) {
            $trxDbgErrores[] = 'relación inmueble-transacciones: ' . $e->getMessage();
            $listadoTransacciones = null;
        }
    }
    if ($listadoTransacciones === null && isset($transacciones) && $transacciones !== null) {
        /* Respaldo: dataset del controlador · el rango se aplica EN MEMORIA
           sobre la página recibida (el total del paginador puede incluir
           registros fuera del rango; el panel DBG lo reporta). */
        $listadoTransacciones = $transacciones;
        try {
            $trxEsPaginador = method_exists($listadoTransacciones, 'getCollection');
            $trxColeccion = $trxEsPaginador ? $listadoTransacciones->getCollection() : $listadoTransacciones;
            $trxFueraRango = 0;
            $trxFiltrada = $trxColeccion->filter(function ($t) use ($trxDesde, $trxHasta, &$trxFueraRango, $trxQ) {
                $trxQOk = true;
                if ($trxQ !== '') {
                    $trxPaja = strtolower(($t->referencia_externa ?? '') . ' ' . ($t->id ?? '') . ' ' . ($t->reserva->codigo_reserva ?? $t->id_rsv_reservas ?? '') . ' ' . ($t->pasarela->nombre ?? $t->pasarela->name ?? '') . ' ' . ($t->metodo_pago ?? '') . ' ' . ($t->estado_pago ?? '') . ' ' . ($t->monto ?? ''));
                    $trxQOk = strpos($trxPaja, strtolower($trxQ)) !== false;
                }
                $f = $t->created_at ?? null;
                if (!$f) { return $trxQOk; }
                $enRango = $f->between($trxDesde, $trxHasta);
                if (!$enRango) { $trxFueraRango++; }
                return $enRango && $trxQOk;
            })->values();
            if ($trxEsPaginador) { $listadoTransacciones->setCollection($trxFiltrada); } else { $listadoTransacciones = $trxFiltrada; }
            $trxFuente = 'controlador (fechas' . ($trxQ !== '' ? ' + búsqueda' : '') . ' aplicadas en memoria)';
            if ($trxFueraRango > 0) { $trxDbgErrores[] = 'el controlador entregó ' . $trxFueraRango . ' registro(s) fuera del rango en esta página (filtrados en memoria)'; }
        } catch (\Throwable $e) {
            $trxDbgErrores[] = 'filtro de fechas en memoria: ' . $e->getMessage();
        }
    }
    if ($listadoTransacciones === null) {
        $trxDbgErrores[] = 'dataset transacciones: sin modelo, sin relación y sin variable del controlador';
        $listadoTransacciones = collect();
    }
    $trxRuta = function (array $cands, $param = null) {
        foreach ($cands as $c) {
            if (\Illuminate\Support\Facades\Route::has($c)) {
                return $param !== null ? route($c, $param) : route($c);
            }
        }
        return null;
    };

    $trxStore = $trxRuta([ 'rsv.transacciones-financieras.store', 'rsv.transacciones.store', 'rsv.transaccion-financiera.store', 'rsv.transaccionesfinancieras.store' ]);
    $trxBase = $trxRuta([ 'rsv.transacciones-financieras.index', 'rsv.transacciones.index', 'rsv.transaccion-financiera.index', 'rsv.transaccionesfinancieras.index' ]);

    try { $trxPasarelas = $pasarelas ?? \App\Models\Rsv\Pasarela::orderBy('nombre')->get(); }
    catch (\Throwable $e) { $trxPasarelas = collect(); }

    $trxEstados = ['Pendiente', 'Aprobado', 'Rechazado', 'Anulado'];
    $trxTotal = method_exists($listadoTransacciones, 'total') ? $listadoTransacciones->total() : $listadoTransacciones->count();

    /* ── Colector del panel de debug (TRX-v3.1-DBG) · via al panel vía la directiva json de Blade ── */
    $trxFragmentoUrl = \Illuminate\Support\Facades\Route::has('rsv.admin.transacciones.fragmento')
        ? route('rsv.admin.transacciones.fragmento', ['inmueble_id' => $trxInmId ?? 'todos']) : null;
    /* TRX-v3 · la URL del fragmento lleva SIEMPRE el inmueble resuelto en
       servidor (inmueble_id); en modo global viaja 'todos' */

    $trxDbgSoporte = 0;
    $trxDbgEstados = [];
    foreach ($listadoTransacciones as $trxDbgItem) {
        if (!empty($trxDbgItem->soporte_pago) && !empty($trxDbgItem->url_soporte_pago) && $trxDbgItem->url_soporte_pago !== '#') { $trxDbgSoporte++; }
        $trxDbgClave = strtolower((string) ($trxDbgItem->estado_pago ?? 'pendiente'));
        $trxDbgEstados[$trxDbgClave] = ($trxDbgEstados[$trxDbgClave] ?? 0) + 1;
    }

    /* TRX-v3 · VALIDACIÓN DE LA ELIMINACIÓN LÓGICA: la vista confirma
       que el modelo real usa SoftDeletes (deleted_at) y lo reporta en
       el panel. Flujo de anulación configurado en esta vista: modal con
       confirm() → DELETE por spoofing → el controlador marca
       estado_pago='Anulado' y borra lógicamente (nunca físico · el
       soporte de S3 se conserva) · botón deshabilitado si ya está
       ANULADA · las filas anuladas se ven atenuadas (trx-anulada). */
    $trxSoftDeletes = false;
    if (!empty($trxModelo)) {
        try { $trxSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($trxModelo)); }
        catch (\Throwable $e) { $trxSoftDeletes = false; }
    }

    $trxDbg = [
        'version'       => 'TRX-v3.1-DBG',
        'vista'         => 'partials/finanzas/transacciones.blade.php',
        'render'        => now()->format('H:i:s'),
        'modo'          => $trxGlobal ? 'global' : ('inmueble #' . $trxInmId),
        'fuente_datos'  => $trxFuente,
        'rango_fechas'  => ['desde' => $trxDesde->format('Y-m-d'), 'hasta' => $trxHasta->format('Y-m-d'), 'modo' => $trxMesPorDefecto ? 'mes-por-defecto' : 'personalizado'],
        'busqueda'      => ['q' => $trxQ !== '' ? $trxQ : null, 'ambito' => 'todas las páginas del rango'],
        'eliminacion_logica' => [
            'modelo_soft_deletes'  => $trxSoftDeletes,
            'anular_via'           => 'DELETE (spoofing) · estado_pago=Anulado + deleted_at',
            'modal_anular'         => true,
            'bloqueado_si_anulada' => true,
        ],
        'transacciones' => $trxTotal,
        'en_pagina'     => $listadoTransacciones->count(),
        'pagina'        => method_exists($listadoTransacciones, 'currentPage') ? $listadoTransacciones->currentPage() : 1,
        'por_pagina'    => method_exists($listadoTransacciones, 'perPage') ? $listadoTransacciones->perPage() : 'todo',
        'pasarelas'     => $trxPasarelas->count(),
        'con_soporte'   => $trxDbgSoporte,
        'estados'       => $trxDbgEstados,
        'editable'      => (bool) ($trxStore || $trxBase),
        'rutas'         => [
            'store'     => $trxStore     ?: 'NO definida',
            'index'     => $trxBase      ?: 'NO definida',
            'fragmento' => $trxFragmentoUrl ?: 'NO definida',
        ],
        'errores'       => $trxDbgErrores,
    ];
@endphp

{{-- TRX-v3.1-DBG · Tab DBG terminal + LED + Panel LOCAL (hermano de la hoja: vive y muere con el parcial · NO viaja en el refresco AJAX interno) --}}
<div id="trxDbgToggle" title="Debug Transacciones (TRX-v3.1-DBG)"><span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span></div>
<div id="trxDbgPanel" role="dialog" aria-label="Panel de debug de Transacciones Financieras">
    <div class="trx-dbg-header" id="trxDbgHeader">
        <span>DEBUG · Finanzas · Transacciones · TRX-v3.1-DBG</span>
        <span class="trx-dbg-controls">
            <button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>
            <button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>
        </span>
    </div>
    <div id="trxDbgBody"></div>
</div>

<div id="sheet-transacciones" class="sheet-pane">

    <div class="reservas-soft p-3 bg-white rounded-3 border" style="border-color: #f1f5f9 !important;">

        <style>
            .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
            .reservas-soft .table > :not(caption) > * > * { padding: 2px 7px; height: 20px; line-height: 1.1; vertical-align: middle; }
            .reservas-soft .table thead th { font-size: 9px; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--ink-3); background-color: var(--head); border-bottom: 1px solid var(--grid) !important; }
            .reservas-soft .table tbody td { font-size: 8px; color: var(--ink); border-bottom: 1px solid var(--grid); border-top: 0; white-space: nowrap; }
            .reservas-soft .table tbody tr:hover > * { background-color: #fafcfe; --bs-table-accent-bg: transparent; }
            .reservas-soft .strong { color: var(--ink-2); font-weight: 600; }
            .reservas-soft .sub { font-size: 7.5px; color: var(--ink-4); }
            .reservas-soft .btn-mini { border: 1px solid var(--grid-2); background: #fff; color: var(--ink-3); font-family: inherit; font-size: 8.5px; font-weight: 500; padding: 1px 8px; border-radius: 3px; cursor: pointer; }
            .reservas-soft .btn-mini:hover { color: var(--ink-2); border-color: #d7dee6; background: #fafcfe; }
            .reservas-soft .btn-mini:disabled { color: #d4dae2; background: #fcfdfe; border-color: var(--grid); cursor: default; }
            .reservas-soft .btn-green { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-bd); font-family: inherit; font-size: 8.5px; font-weight: 600; padding: 2px 10px; border-radius: 3px; }
            .reservas-soft .xls-search { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 2px 8px; box-shadow: none !important; outline: 0; min-width: 200px; }
            .reservas-soft .xls-search:focus { border-color: #c8d6cd; }
            .reservas-soft .xls-fecha { font-size: 9px; color: var(--ink-2); background: #fff; border: 1px solid var(--grid-2); border-radius: 3px; padding: 1px 5px; box-shadow: none !important; outline: 0; height: 22px; font-family: inherit; color-scheme: light; }
            .reservas-soft .xls-fecha:focus { border-color: #c8d6cd; }
            .reservas-soft .count-badge { display: inline-flex; align-items: center; gap: 4px; font-size: 8px; font-weight: 600; font-variant-numeric: tabular-nums; color: var(--ink-2); background: var(--head); border: 1px solid var(--grid-2); border-radius: 8px; padding: 1px 8px; white-space: nowrap; }
            .reservas-soft .count-badge b { color: var(--accent); }
            .reservas-soft .st-dot { display: inline-block; width: 9px; height: 9px; border-radius: 3px; border: 1px solid rgba(0,0,0,.08); vertical-align: middle; }
            .reservas-soft .st-warn { background: #fdf5f4; border: 1px solid #f0dcd8; border-radius: 4px; color: #c98a85; font-size: 8px; font-weight: 600; padding: 6px 9px; }
            .reservas-soft-modal .modal-content { border: 1px solid #e9eef4; border-radius: 5px; font-family: Calibri, "Segoe UI", sans-serif; font-size: 9.5px; color: #64748b; }
            .reservas-soft-modal .form-label { font-size: 7.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-bottom: 2px; }
            .reservas-soft-modal .modal-header { border-bottom: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .modal-footer { border-top: 1px solid #f1f5f9; padding: 9px 14px; }
            .reservas-soft-modal .ps-err-msg { font-size: 7.5px; color: #c98a85; }

            @keyframes destelloFila { 0%,100% { background-color: transparent; } 35% { background-color: rgba(61,122,92,.16); } }
            .reservas-soft tr.fila-nueva > td { animation: destelloFila 1.5s ease; }
            .reservas-soft tr.trx-anulada > td { opacity: .55; }

            /* TRX-v3 · giro del icono del botón Actualizar mientras recarga */
            @keyframes trxSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
            .reservas-soft .trx-spin { animation: trxSpin .8s linear infinite; }
            /* TRX-v3 · pulso verde de confirmación tras cada refresco */
            @keyframes trxPulseFade { 0% { background-color: #e3f2e8; } 100% { background-color: transparent; } }
            .reservas-soft.trx-pulse { animation: trxPulseFade .9s ease-out 1; }

            /* ═════════ SPEC DE DISTANCIAS · TRX-v3.1-DBG (tab terminal TIP-v1.3) ═════════
               TAB DBG   · right:0px · bottom:0px → fundida SIEMPRE en la esquina
                           inferior-derecha (nunca sube: con 16px de alto cabe bajo
                           cualquier otro toggle sin tocarlo)
                         · altura fija 16px · padding lateral 8px
                         · radius 3px solo sup-izq · borde 1px arriba/izq
                         · monospace 8px · LED 5px (verde ok / rojo pulsante = error)
               PANEL     · right:0px · bottom = max(20, vecino + 4px) medido en px
                           reales con getBoundingClientRect (los toggles ocultos 0×0
                           no cuentan) · (20 = 16 de tab + 4 de aire mínimo)
                         · ancho 380px · radius 6px 0 0 0 (cajón pegado al borde) */
            #trxDbgToggle { position: fixed; right: 0; bottom: 0; z-index: 1090; height: 16px; padding: 0 8px; border-radius: 3px 0 0 0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; background: rgba(2,6,23,.94); color: #64748b; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 8px; font-weight: 700; letter-spacing: .18em; line-height: 1; cursor: pointer; display: flex; align-items: center; gap: 5px; user-select: none; -webkit-user-select: none; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
            #trxDbgToggle:hover { color: #38bdf8; border-color: #334155; background: rgba(8,15,30,.97); box-shadow: 0 0 12px rgba(56,189,248,.12); }
            .trx-dbg-led { width: 5px; height: 5px; border-radius: 1px; flex-shrink: 0; background: #10b981; box-shadow: 0 0 5px rgba(16,185,129,.75); }
            .trx-dbg-led.err { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,.85); animation: trxDbgBlink 1s steps(2,start) infinite; }
            @keyframes trxDbgBlink { 50% { opacity: .35; } }
            .trx-dbg-badge { display: none; color: #f87171; font-size: 8px; font-weight: 700; line-height: 1; letter-spacing: 0; }
            #trxDbgPanel { position: fixed; right: 0; bottom: 20px; z-index: 1091; width: 380px; max-width: calc(100vw - 16px); max-height: 60vh; display: none; flex-direction: column; background: rgba(2,6,23,.97); color: #e2e8f0; border: 1px solid #1e293b; border-right: 0; border-bottom: 0; border-radius: 6px 0 0 0; box-shadow: 0 12px 32px rgba(0,0,0,.55); overflow: hidden; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
            .trx-dbg-header { display: flex; justify-content: space-between; align-items: center; gap: 6px; padding: 5px 8px; background: #0b1220; border-bottom: 1px solid #1e293b; font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #38bdf8; cursor: pointer; }
            .trx-dbg-controls { display: flex; gap: 4px; }
            .trx-dbg-btn { background: #334155; color: #e2e8f0; border: 0; border-radius: 4px; font-size: 9px; padding: 2px 7px; cursor: pointer; font-family: inherit; }
            .trx-dbg-btn:hover { background: #475569; }
            #trxDbgBody { overflow-y: auto; padding: 6px 8px; font-size: 9.5px; line-height: 1.55; }
            .trx-dbg-line { white-space: pre-wrap; word-break: break-word; padding: 2px 0; border-bottom: 1px dashed #1e293b; }
            .trx-dbg-time { color: #64748b; margin-right: 4px; }
            .trx-dbg-info { color: #93c5fd; } .trx-dbg-ok { color: #86efac; }
            .trx-dbg-warn { color: #fde68a; } .trx-dbg-err { color: #fca5a5; }
            .trx-dbg-net { color: #c4b5fd; } .trx-dbg-debug { color: #94a3b8; }
        </style>

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

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap: 8px;">
            <h6 class="fw-bold mb-0" style="font-size: 9.5px; color: #334155;">
                <i class="bi bi-wallet2 me-1" style="font-size: 8px; color: #5d8a70;"></i> Transacciones Financieras
            </h6>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                @if(!$trxGlobal)
                    <span class="count-badge" title="Filtro activo: transacciones de un solo inmueble">
                        <i class="bi bi-house-door" style="font-size: 7.5px; color: #b3bcc7;"></i>
                        {{ is_object($trxInm) ? ($trxInm->name ?? ('Inmueble #' . $trxInmId)) : ('Inmueble #' . $trxInmId) }}
                    </span>
                @endif
                <span class="count-badge" title="Transacciones registradas (total del contexto)">
                    <b>{{ $trxTotal }}</b> transacción(es)
                </span>
                <input type="text" id="trxSearch" class="xls-search" placeholder="Buscar transacción…" autocomplete="off" enterkeyhint="search" title="Búsqueda instantánea · filtra al escribir y busca en TODAS las páginas del rango (Enter = buscar ya)" style="width: 170px;">
                <span class="count-badge" title="Rango de fechas activo · por defecto solo se carga el mes en curso">
                    <i class="bi bi-calendar3" style="font-size: 7.5px; color: #b3bcc7;"></i>
                    {{ $trxDesde->format('d/m/Y') }} – {{ $trxHasta->format('d/m/Y') }}
                </span>
                <input type="date" id="trxFechaDesde" class="xls-fecha" value="{{ $trxDesde->format('Y-m-d') }}" title="Desde · fecha de creación de la transacción" autocomplete="off">
                <input type="date" id="trxFechaHasta" class="xls-fecha" value="{{ $trxHasta->format('Y-m-d') }}" title="Hasta · fecha de creación de la transacción" autocomplete="off">
                <button type="button" id="trxAplicarFechas" class="btn-mini d-inline-flex align-items-center gap-1" title="Aplicar el rango de fechas al listado (vuelve a la página 1)">
                    <i class="bi bi-funnel" style="font-size: 8px;"></i> Aplicar
                </button>
                <button type="button" id="trxMesActual" class="btn-mini d-inline-flex align-items-center gap-1" title="Restablecer el rango al mes en curso">
                    <i class="bi bi-calendar-week" style="font-size: 8px;"></i> Este mes
                </button>
                <button type="button" id="trxRefresh" class="btn-mini d-inline-flex align-items-center gap-1" title="Recargar transacciones por AJAX (sin F5)">
                    <i class="bi bi-arrow-clockwise" style="font-size: 8px;"></i> Actualizar
                </button>
                <button type="button" id="trxExport" class="btn-mini d-inline-flex align-items-center gap-1" title="Excel (CSV · BOM UTF-8 · separador ;) de TODO lo filtrado en pantalla">
                    <i class="bi bi-download" style="font-size: 8px;"></i> Exportar
                </button>
                @if($trxStore)
                    <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#trxModalCreate">
                        <i class="bi bi-plus-lg" style="font-size: 8px;"></i> Nueva Transacción
                    </button>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0" style="min-width: 860px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 25px;">#</th>
                        <th>Referencia</th>
                        <th>Reserva</th>
                        <th>Pasarela / Método</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center">Soporte</th>
                        <th class="text-center" style="width: 130px;">·</th>
                    </tr>
                </thead>
                <tbody id="tbodyTransacciones">

                    @forelse($listadoTransacciones as $trx)
                        @php
                            $estado    = strtolower($trx->estado_pago ?? 'pendiente');
                            $colorEst  = match($estado) {
                                'aprobado', 'completado', 'pagado'  => '#a3cfb6',
                                'rechazado', 'cancelado', 'anulado' => '#c98a85',
                                default                             => '#f5d38c',
                            };
                            $conSoporte = !empty($trx->soporte_pago) && !empty($trx->url_soporte_pago) && $trx->url_soporte_pago !== '#';
                            $trxBuscar = strtolower(trim(
                                ($trx->referencia_externa ?? '') . ' ' .
                                ($trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? '') . ' ' .
                                ($trx->pasarela->nombre ?? $trx->pasarela->name ?? '') . ' ' .
                                ($trx->metodo_pago ?? '') . ' ' .
                                ($trx->estado_pago ?? '')
                            ));
                        @endphp

                        <tr class="trx-row{{ strtolower((string) ($trx->estado_pago ?? '')) === 'anulado' ? ' trx-anulada' : '' }}" data-id="{{ $trx->id }}" data-trx-search="{{ $trxBuscar }}">
                            <td class="text-center sub">{{ $loop->iteration }}</td>
                            <td class="strong">
                                {{ $trx->referencia_externa ?? 'N/A' }}
                                <div class="sub">ID #{{ $trx->id }}</div>
                            </td>
                            <td>#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</td>
                            <td>
                                <i class="bi bi-shield-check me-1" style="font-size: 7.5px; color: #a3cfb6;"></i>{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Pasarela Directa' }}
                                <div class="sub">{{ $trx->metodo_pago ?? 'General' }}</div>
                            </td>
                            <td class="text-end strong">
                                ${{ number_format((float) ($trx->monto ?? 0), 2) }}
                                <span class="sub" style="display: inline;">{{ $trx->moneda ?? 'COP' }}</span>
                            </td>
                            <td class="strong">
                                <span class="st-dot" style="background: {{ $colorEst }};"></span> {{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}
                            </td>
                            <td class="sub">
                                {{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="text-center">
                                @if($conSoporte)
                                    <a href="{{ $trx->url_soporte_pago }}" target="_blank" rel="noopener noreferrer" style="font-size: 8.5px; font-weight: 600; color: #5d8a70; text-decoration: none;">
                                        <i class="bi bi-file-earmark-text me-1"></i>Ver
                                    </a>
                                @else
                                    <span style="color: #d4dae2;">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-mini js-ver-trx"
                                        data-id="{{ $trx->id }}"
                                        data-referencia="{{ $trx->referencia_externa ?? 'N/A' }}"
                                        data-reserva="#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}"
                                        data-pasarela="{{ $trx->pasarela->nombre ?? $trx->pasarela->name ?? 'Directa' }}"
                                        data-metodo="{{ $trx->metodo_pago ?? 'N/A' }}"
                                        data-monto="{{ number_format((float) ($trx->monto ?? 0), 2) }}"
                                        data-moneda="{{ $trx->moneda ?? 'COP' }}"
                                        data-estado="{{ strtoupper($trx->estado_pago ?? 'PENDIENTE') }}"
                                        data-fecha="{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i:s') : 'N/A' }}"
                                        data-soporte="{{ $conSoporte ? 1 : 0 }}"
                                        data-soporte-url="{{ $trx->url_soporte_pago ?? '#' }}">Ver</button>
                                @if($trxBase)
                                    <button type="button" class="btn-mini" data-bs-toggle="modal" data-bs-target="#trxEditModal-{{ $trx->id }}">Editar</button>
                                    <button type="button" class="btn-mini" style="color:#c98a85;" data-bs-toggle="modal" data-bs-target="#trxDelModal-{{ $trx->id }}">Del</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                                <i class="bi bi-inbox me-1"></i> {{ $trxQ !== '' ? 'Ninguna transacción coincide con «' . $trxQ . '» en el rango seleccionado.' : 'No hay transacciones en el rango de fechas seleccionado (por defecto, solo el mes en curso).' }}
                            </td>
                        </tr>
                    @endforelse

                    <tr class="trx-sin-res" style="display:none;">
                        <td colspan="9" class="text-center py-4" style="font-size: 8.5px; color: #b3bcc7;">
                            <i class="bi bi-search me-1"></i> Ninguna transacción coincide con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        @if(method_exists($listadoTransacciones, 'links'))
            <div class="mt-2 text-center" style="font-size: 8.5px;">
                {{ $listadoTransacciones->appends(request()->query())->links() }}
            </div>
        @endif

        {{-- MODAL CREAR --}}
        @if($trxStore)
        <div class="modal fade reservas-soft-modal" id="trxModalCreate" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                        <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Nueva Transacción</h6>
                    </div>
                    <form action="{{ $trxStore }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="st-warn mb-2 d-none ps-err-general"></div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label">Reserva (ID) <span style="color:#c98a85;">*</span></label>
                                    <input type="number" min="1" class="form-control form-control-sm" name="id_rsv_reservas" required placeholder="Ej: 15">
                                    <div class="ps-err-msg" data-error-for="id_rsv_reservas"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Monto <span style="color:#c98a85;">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required placeholder="0.00">
                                    <div class="ps-err-msg" data-error-for="monto"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Pasarela</label>
                                    <select class="form-select form-select-sm" name="id_rsv_pasarela">
                                        <option value="">— Directo (auto) —</option>
                                        @foreach($trxPasarelas as $gp)
                                            <option value="{{ $gp->id }}">{{ $gp->nombre ?? $gp->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="id_rsv_pasarela"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Método</label>
                                    <input type="text" class="form-control form-control-sm" name="metodo_pago" maxlength="60" placeholder="Transferencia, Efectivo…">
                                    <div class="ps-err-msg" data-error-for="metodo_pago"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select form-select-sm" name="estado_pago">
                                        @foreach($trxEstados as $est)
                                            <option value="{{ $est }}" {{ $est === 'Pendiente' ? 'selected' : '' }}>{{ strtoupper($est) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="ps-err-msg" data-error-for="estado_pago"></div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Moneda</label>
                                    <input type="text" class="form-control form-control-sm" name="moneda" maxlength="5" value="COP">
                                    <div class="ps-err-msg" data-error-for="moneda"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Referencia externa</label>
                                    <input type="text" class="form-control form-control-sm" name="referencia_externa" maxlength="100" placeholder="Nequi 1234, Credi…">
                                    <div class="ps-err-msg" data-error-for="referencia_externa"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Soporte de pago</label>
                                    <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                    <div class="sub">Opcional · imagen o PDF</div>
                                    <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size: 11px;"><i class="bi bi-check-lg"></i> Registrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- MODALES EDITAR Y ELIMINAR POR FILA --}}
        @foreach($listadoTransacciones as $trx)
            @if($trxBase)
            <div class="modal fade reservas-soft-modal" id="trxEditModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                            <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Editar #{{ $trx->id }}</h6>
                        </div>
                        <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" enctype="multipart/form-data" data-trx-id="{{ $trx->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body p-3">
                                <div class="st-warn mb-2 d-none ps-err-general"></div>
                                <div class="mb-2" style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; color: #64748b;">
                                    Reserva <b>#{{ $trx->reserva->codigo_reserva ?? $trx->id_rsv_reservas ?? 'S/R' }}</b> · Creada {{ $trx->created_at ? $trx->created_at->format('d/m/Y') : '—' }}
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label">Monto <span style="color:#c98a85;">*</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required value="{{ $trx->monto }}">
                                        <div class="ps-err-msg" data-error-for="monto"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Moneda</label>
                                        <input type="text" class="form-control form-control-sm" name="moneda" maxlength="5" value="{{ $trx->moneda ?? 'COP' }}">
                                        <div class="ps-err-msg" data-error-for="moneda"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Pasarela</label>
                                        <select class="form-select form-select-sm" name="id_rsv_pasarela">
                                            @foreach($trxPasarelas as $gp)
                                                <option value="{{ $gp->id }}" {{ $trx->id_rsv_pasarela == $gp->id ? 'selected' : '' }}>{{ $gp->nombre ?? $gp->name }}</option>
                                            @endforeach
                                            @if($trx->id_rsv_pasarela && !$trxPasarelas->contains('id', $trx->id_rsv_pasarela))
                                                <option value="{{ $trx->id_rsv_pasarela }}" selected>#{{ $trx->id_rsv_pasarela }}</option>
                                            @endif
                                        </select>
                                        <div class="ps-err-msg" data-error-for="id_rsv_pasarela"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Método</label>
                                        <input type="text" class="form-control form-control-sm" name="metodo_pago" maxlength="60" value="{{ $trx->metodo_pago ?? '' }}">
                                        <div class="ps-err-msg" data-error-for="metodo_pago"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select form-select-sm" name="estado_pago">
                                            @foreach($trxEstados as $est)
                                                <option value="{{ $est }}" {{ strtolower($est) === strtolower((string) ($trx->estado_pago ?? 'Pendiente')) ? 'selected' : '' }}>{{ strtoupper($est) }}</option>
                                            @endforeach
                                        </select>
                                        <div class="ps-err-msg" data-error-for="estado_pago"></div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Referencia</label>
                                        <input type="text" class="form-control form-control-sm" name="referencia_externa" maxlength="100" value="{{ $trx->referencia_externa ?? '' }}">
                                        <div class="ps-err-msg" data-error-for="referencia_externa"></div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Reemplazar soporte</label>
                                        <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf">
                                        <div class="sub">Opcional · deja vacío para conservar el actual</div>
                                        <div class="ps-err-msg" data-error-for="soporte_pago"></div>
                                    </div>
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

            <div class="modal fade reservas-soft-modal" id="trxDelModal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-body p-3 text-center">
                            <i class="bi bi-slash-circle" style="font-size: 1.2rem; color: #c98a85;"></i>
                            <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.9rem;">Anular transacción</h6>
                            <p class="mb-2" style="font-size: 8.5px; color: #94a3b8;">
                                Se marcará <b style="color: #334155;">#{{ $trx->referencia_externa ?? $trx->id }}</b> por <b style="color: #334155;">${{ number_format((float) ($trx->monto ?? 0), 2) }}</b> como <b>ANULADA</b>.
                            </p>
                            <div class="st-warn mb-2 d-none ps-err-general"></div>

                            @if(strtolower((string) ($trx->estado_pago ?? '')) === 'anulado')
                                <div class="st-warn mb-2">
                                    <i class="bi bi-lock-fill me-1"></i> Esta transacción ya está ANULADA.
                                </div>
                                <button type="button" class="btn btn-sm py-1 px-2 w-100" disabled style="font-size: 11px; background: #fcfdfe; color: #d4dae2; border: 1px solid #f1f5f9; cursor: not-allowed;">
                                    <i class="bi bi-slash-circle"></i> Anular
                                </button>
                            @else
                                <form action="{{ $trxBase }}/{{ $trx->id }}" method="POST" class="js-ajax-trx" data-trx-id="{{ $trx->id }}" onsubmit="if(!confirm('¿Anular esta transacción?')){event.stopPropagation();return false;}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm py-1 px-2 w-100" style="font-size: 11px; background: #fdf5f4; color: #c98a85; border: 1px solid #f0dcd8;">
                                        <i class="bi bi-slash-circle"></i> Anular definitivamente
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
</div>

{{-- MODAL DETALLE COMPARTIDO (REDUCIDO A DISEÑO RESERVAS-SOFT) --}}
<div class="modal fade reservas-soft-modal" id="modalTransaccionDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size: 9px;"></button>
                <h6 class="modal-title mb-0 fw-bold" style="font-size: 0.95rem;">Detalle de transacción</h6>
            </div>
            <div class="modal-body p-3">
                <div style="background: #fafbfd; border: 1px solid #e9eef4; border-radius: 4px; padding: 6px 9px; font-size: 8.5px; color: #64748b; margin-bottom: 8px;">
                    Referencia <b id="fdRef" style="color:#334155;">—</b><br>
                    <span id="fdId">ID interno #—</span>
                </div>
                <table class="table table-sm table-borderless mb-0" style="font-size: 8px;">
                    <tr><td class="text-uppercase fw-bold text-muted" style="width:70px;">Reserva</td><td id="fdReserva" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Pasarela</td><td id="fdPasarela" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Método</td><td id="fdMetodo" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Monto</td><td id="fdMonto" class="strong text-end" style="color:#3d7a5c;">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Estado</td><td id="fdEstado" class="strong text-end">—</td></tr>
                    <tr><td class="text-uppercase fw-bold text-muted">Fecha</td><td id="fdFecha" class="text-end text-muted">—</td></tr>
                </table>
                <div id="fdSoporteCaja" class="mt-2 text-center" style="border-top: 1px solid #f1f5f9; padding-top: 8px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3" style="font-size: 11px;" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    if (window.__jsTransaccionesListo) return;
    window.__jsTransaccionesListo = true;

    /* TRX-v3 · URL del fragmento resuelta en servidor: ya lleva inmueble_id
       (el del contexto Blade). El cliente solo agrega página y _t. */
    var TRX_FRAG_URL = '{!! $trxFragmentoUrl ?? "" !!}';
    var PAGINA_ACTUAL = 1;

    var TRX_Q = '';         // TRX-v3.1 · término de búsqueda activo (viaja en la URL del fragmento)
    var TRX_Q_ENVIADO = ''; // TRX-v3.1 · último término consultado al servidor (evita repetir)
    var TRX_Q_SEQ = 0;      // TRX-v3.1 · guarda anti-carrera: solo gana la última búsqueda
    var TRX_Q_TIMER = null; // TRX-v3.1 · temporizador del debounce (320ms tras la última tecla)

    /* ═══ 0) PANEL DE DEBUG LOCAL TRX-v3.1-DBG (diseño TIP-v1.3 aprobado) ═══
       IIFE propia con $ por id: no interfiere con el resto del script.
       Exclusiva de esta vista: los nodos son HTML del parcial (hermanos de
       #sheet-transacciones, FUERA del contenedor que intercambia el refresco
       AJAX, así que sobreviven a crear/editar/anular) y el respaldo solo
       re-crea DENTRO de la primera hoja montada. Cero document.body.
       Como la hoja puede renderizarse en varias copias, el panel también
       DEDUPLICA nodos sobrantes: un solo debug por vista. */
    var DBG = (window.TrxDbg && window.TrxDbg.alive && window.TrxDbg.alive())
        ? window.TrxDbg
        : (function () {
            var MAX = 250, numErr = 0;
            function $(id) { return document.getElementById(id); }
            function asegurarNodos() {
                /* Respaldo: si un swap externo borrara los nodos (normalmente
                   viven como hermanos de #sheet-transacciones y sobreviven al
                   refresco AJAX interno), se re-crean DENTRO de la primera
                   hoja. Cero document.body (exclusividad total). */
                var rootDbg = document.getElementById('sheet-transacciones');
                if (!rootDbg) { return; } // sin vista montada → cero nodos
                /* Dedup multi-copia: la hoja puede existir varias veces en la
                   página; el panel de debug debe ser ÚNICO (se conserva el 1º) */
                var dups = document.querySelectorAll('#trxDbgToggle');
                for (var d = 1; d < dups.length; d++) { if (dups[d].parentNode) { dups[d].parentNode.removeChild(dups[d]); } }
                dups = document.querySelectorAll('#trxDbgPanel');
                for (var d2 = 1; d2 < dups.length; d2++) { if (dups[d2].parentNode) { dups[d2].parentNode.removeChild(dups[d2]); } }
                if (!$('trxDbgToggle')) {
                    var t = document.createElement('div');
                    t.id = 'trxDbgToggle';
                    t.title = 'Debug Transacciones (TRX-v3.1-DBG)';
                    t.innerHTML = '<span class="trx-dbg-led" id="trxDbgLed"></span>DBG<span class="trx-dbg-badge" id="trxDbgBadge">0</span>';
                    rootDbg.insertBefore(t, rootDbg.firstChild);
                }
                if (!$('trxDbgPanel')) {
                    var p = document.createElement('div');
                    p.id = 'trxDbgPanel';
                    p.innerHTML = '<div class="trx-dbg-header" id="trxDbgHeader">' +
                        '<span>DEBUG · Finanzas · Transacciones · TRX-v3.1-DBG</span>' +
                        '<span class="trx-dbg-controls">' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgCopy">Copiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClear">Limpiar</button>' +
                        '<button type="button" class="trx-dbg-btn" id="trxDbgClose">✕</button>' +
                        '</span></div><div id="trxDbgBody"></div>';
                    rootDbg.appendChild(p);
                }
                apilar();
            }
            /* TRX-v3.1-DBG: la TAB se queda SIEMPRE abajo (bottom:0, nunca se
               apila: con 16px de alto cabe bajo cualquier otro toggle sin
               tocarlo). Solo el PANEL esquiva a los vecinos de las otras
               vistas: bottom = max(20, vecino + 4) en píxeles reales. */
            function apilar() {
                /* TRX-v3 · vecino de Huéspedes corregido: es rhDbgToggle (antes
                   se apilaba contra rhDebugToggle, id que no existe) */
                var ids = ['rsvShowDebugToggle', 'rhDbgToggle', 'rvDebugToggle', 'inmDbgToggle', 'catDbgToggle', 'tarDbgToggle', 'gmDbgToggle', 'tipDbgToggle', 'calDbgToggle', 'blqDbgToggle', 'psDbgToggle', 'audDbgToggle', 'infDbgToggle', 'finDbgToggle']; // serie completa de hermanos
                var ocupado = 0;
                for (var i = 0; i < ids.length; i++) {
                    var el = $(ids[i]);
                    if (!el) { continue; }
                    var r = el.getBoundingClientRect();
                    if (!r.width && !r.height) { continue; } // oculto 0×0 no ocupa sitio
                    var desdeAbajo = window.innerHeight - r.top; // cuánto sube ese vecino desde el fondo
                    if (desdeAbajo > ocupado) { ocupado = desdeAbajo; }
                }
                var t = $('trxDbgToggle'), p = $('trxDbgPanel');
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
                    var b = $('trxDbgBody'); if (!b) { return; }
                    var lv = nivel || 'info';
                    var ln = document.createElement('div');
                    ln.className = 'trx-dbg-line';
                    var seguro = String(txt).replace(/[<>&]/g, function (c2) {
                        return c2 === '<' ? '&lt;' : (c2 === '>' ? '&gt;' : '&amp;');
                    });
                    ln.innerHTML = '<span class="trx-dbg-time">[' + hora() + ']</span>' +
                        '<span class="trx-dbg-' + lv + '">' + seguro + '</span>';
                    b.appendChild(ln);
                    while (b.children.length > MAX) { b.removeChild(b.firstChild); }
                    b.scrollTop = b.scrollHeight;
                    if (lv === 'err') {
                        numErr++;
                        var bd = $('trxDbgBadge');
                        if (bd) { bd.textContent = numErr; bd.style.display = 'inline'; }
                        var led = $('trxDbgLed');
                        if (led) { led.classList.add('err'); }
                    }
                },
                open: function () {
                    var p = $('trxDbgPanel'); if (!p) { return; }
                    p.style.display = 'flex';
                    apilar();
                    var bd = $('trxDbgBadge');
                    if (bd) { bd.style.display = 'none'; }
                    var led = $('trxDbgLed');
                    if (led) { led.classList.remove('err'); }
                    numErr = 0;
                },
                close: function () { var p = $('trxDbgPanel'); if (p) { p.style.display = 'none'; } },
                togglePanel: function () {
                    var p = $('trxDbgPanel');
                    if (p) { (p.style.display === 'flex') ? api.close() : api.open(); }
                },
                clear: function () { var b = $('trxDbgBody'); if (b) { b.innerHTML = ''; } },
                copy: function () {
                    var b = $('trxDbgBody'); if (!b) { return; }
                    var txt = '=== DEBUG transacciones TRX-v3.1-DBG · ' + new Date().toISOString() + ' ===\n';
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
                alive: function () { return !!$('trxDbgToggle'); }
            };
            window.TrxDbg = api;
            return api;
        })();
    if (DBG) { DBG.ensure(); } // re-apila, deduplica y re-crea nodos si algún swap externo los reemplazó
    var trxdbg = function (txt, nivel) { if (DBG && DBG.log) { DBG.log(txt, nivel); } };

    /* Botones del panel por DELEGACIÓN en document (el guard __jsTransaccionesListo
       garantiza que se registran UNA sola vez; sobreviven a los swaps) */
    if (window.__trxDbgDocHandler) { document.removeEventListener('click', window.__trxDbgDocHandler); }
    window.__trxDbgDocHandler = function (e) {
        if (!DBG) { return; }
        var t = e.target;
        if (!t || !t.closest) { return; }
        if (t.closest('#trxDbgToggle')) { DBG.togglePanel(); return; }
        if (t.closest('#trxDbgClose')) { e.stopPropagation(); DBG.close(); return; }
        if (t.closest('#trxDbgClear')) { e.stopPropagation(); DBG.clear(); return; }
        if (t.closest('#trxDbgCopy')) { e.stopPropagation(); DBG.copy(); return; }
    };
    document.addEventListener('click', window.__trxDbgDocHandler);
    if (window.__trxDbgDblHandler) { document.removeEventListener('dblclick', window.__trxDbgDblHandler); }
    window.__trxDbgDblHandler = function (e) {
        if (e.target && e.target.closest && e.target.closest('#trxDbgHeader')) { DBG.close(); }
    };
    document.addEventListener('dblclick', window.__trxDbgDblHandler);

    /* Al mostrarse la pestaña que contiene este parcial, re-apila (los
       toggles ocultos 0×0 no cuentan hasta que la pestaña es visible) */
    document.addEventListener('shown.bs.tab', function () {
        if (DBG && DBG.alive && DBG.alive()) { DBG.ensure(); }
    });

    /* ═══ 0b) BOOT · contexto del servidor (SRVDBG · JSON inyectado por Blade) ═══ */
    var SRVDBG = @json($trxDbg);
    if (DBG) {
        trxdbg('TRX-v3.1-DBG activo · tab DBG terminal fija en la esquina (0,0) · LED de estado · exclusiva (muere con el parcial)', 'ok');
        trxdbg('init · modo=' + (SRVDBG ? SRVDBG.modo : '?') + ' · fuente=' + (SRVDBG ? SRVDBG.fuente_datos : '?'), 'info');
        trxdbg('init · total=' + (SRVDBG ? SRVDBG.transacciones : '?') + ' transacción(es) · en página=' + (SRVDBG ? SRVDBG.en_pagina : '?') +
            ' · página=' + (SRVDBG ? SRVDBG.pagina : '?') + '/' + (SRVDBG ? SRVDBG.por_pagina : '?') +
            ' · pasarelas=' + (SRVDBG ? SRVDBG.pasarelas : '?') + ' · editable=' + !!(SRVDBG && SRVDBG.editable), 'info');
        if (SRVDBG && SRVDBG.rango_fechas) {
            trxdbg('fechas · rango cargado ' + SRVDBG.rango_fechas.desde + ' → ' + SRVDBG.rango_fechas.hasta + ' · modo=' + SRVDBG.rango_fechas.modo + ' · por defecto SOLO el mes en curso', 'ok');
        }
        if (SRVDBG && SRVDBG.eliminacion_logica) {
            var _elg = SRVDBG.eliminacion_logica;
            trxdbg('eliminación lógica · SoftDeletes en el modelo=' + (_elg.modelo_soft_deletes ? 'SÍ' : 'NO') + ' · anular=' + _elg.anular_via + (_elg.modelo_soft_deletes ? '' : ' · ⚠ REVISAR: el modelo no reporta SoftDeletes'), _elg.modelo_soft_deletes ? 'ok' : 'warn');
        }
        if (SRVDBG && SRVDBG.estados) {
            var _ek = Object.keys(SRVDBG.estados), _partes = [];
            for (var _ei = 0; _ei < _ek.length; _ei++) { _partes.push(_ek[_ei] + '=' + SRVDBG.estados[_ek[_ei]]); }
            if (_partes.length) { trxdbg('estados · ' + _partes.join(' · '), 'debug'); }
        }
        if (SRVDBG && typeof SRVDBG.con_soporte !== 'undefined') {
            trxdbg('soporte · ' + SRVDBG.con_soporte + ' de ' + SRVDBG.en_pagina + ' en página con soporte adjunto', 'debug');
        }
        if (SRVDBG && SRVDBG.rutas) {
            var _rk = Object.keys(SRVDBG.rutas);
            for (var _ri = 0; _ri < _rk.length; _ri++) {
                if (SRVDBG.rutas[_rk[_ri]] === 'NO definida') {
                    trxdbg('ruta no definida: rsv…' + _rk[_ri] + ' (la acción se deshabilita sola)', 'warn');
                }
            }
            trxdbg('Rutas · store=' + SRVDBG.rutas.store + ' · index=' + SRVDBG.rutas.index + ' · fragmento=' + SRVDBG.rutas.fragmento, 'debug');
        }
        if (SRVDBG && SRVDBG.errores && SRVDBG.errores.length) {
            for (var _di = 0; _di < SRVDBG.errores.length; _di++) {
                trxdbg('⚠ SERVIDOR capturó: ' + SRVDBG.errores[_di], 'err');
            }
        }
    }

    function cerrarBruto(el) {
        el.classList.remove('show'); el.style.display = 'none'; el.setAttribute('aria-hidden', 'true'); el.removeAttribute('aria-modal');
        document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); }); document.body.classList.remove('modal-open');
    }
    function ocultarModal(el) {
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) { window.bootstrap.Modal.getOrCreateInstance(el).hide(); }
        setTimeout(function () { if (el.classList.contains('show')) cerrarBruto(el); }, 700);
    }
    function limpiarResiduos() {
        if (document.querySelectorAll('.modal.show').length === 0) {
            document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); }); document.body.classList.remove('modal-open');
        }
    }
    function unicarModales() {
        var hojas = document.querySelectorAll('#sheet-transacciones');
        if (hojas.length > 1 && window.TrxDbg && window.TrxDbg.log) { window.TrxDbg.log('multi-copia · ' + hojas.length + ' hojas #sheet-transacciones · unificando ids de modales', 'warn'); }
        hojas.forEach(function (hoja, idx) {
            if (idx === 0) return;
            hoja.querySelectorAll('.modal[id]').forEach(function (m) {
                if (m.id.indexOf('-c' + (idx + 1)) !== -1) return;
                var viejo = m.id; m.id = viejo + '-c' + (idx + 1);
                hoja.querySelectorAll('[data-bs-target="#' + viejo + '"]').forEach(function (b) { b.setAttribute('data-bs-target', '#' + m.id); });
            });
        });
    }

    function avisar(mensaje, tipo) {
        trxdbg((tipo === 'danger' ? 'aviso · ' : 'resultado · ') + mensaje, tipo === 'danger' ? 'err' : 'ok');
        if (!window.bootstrap || !window.bootstrap.Toast) { window.alert(mensaje); return; }
        var div = document.createElement('div');
        div.className = 'toast align-items-center text-bg-' + (tipo === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed m-3';
        div.setAttribute('role', 'alert'); div.style.cssText = 'bottom:0;right:0;z-index:2100;';
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
        var sobrantes = [];
        Object.keys(errores || {}).forEach(function (campo) {
            var msg = [].concat(errores[campo])[0];
            var caja = form.querySelector('[data-error-for="' + campo + '"]');
            if (caja) { caja.textContent = msg; } else { sobrantes.push(campo + ': ' + msg); }
            var input = form.querySelector('[name="' + campo + '"]');
            if (input) input.classList.add('is-invalid');
        });
        if (sobrantes.length) errorGeneral(form, sobrantes.join(' · '));
    }
    function errorGeneral(form, msg) {
        var g = form.querySelector('.ps-err-general');
        if (!g) { window.alert(msg); return; }
        g.textContent = msg; g.classList.remove('d-none');
    }
    function resaltarFila(id) {
        if (!id) return;
        document.querySelectorAll('#sheet-transacciones tr[data-id="' + id + '"]').forEach(function (fila) {
            fila.classList.add('fila-nueva');
            setTimeout(function () { fila.classList.remove('fila-nueva'); }, 1600);
        });
    }
    /* TRX-v3 · inmueble_id: manda el de la querystring del navegador (por si
       el usuario acaba de cambiar el filtro) y si no viene, el que resolvió
       el servidor en TRX_FRAG_URL · así el refresco NUNCA pierde el filtro
       de un solo inmueble aunque la página no lo lleve en la URL */
    /* ═══ TRX-v3 · FILTROS POR RANGO DE FECHAS ═══
       Los inputs #trxFechaDesde/#trxFechaHasta viven DENTRO de la hoja;
       igual que Actualizar/Exportar, todo por delegación en document
       para sobrevivir a los refrescos. El rango viaja SIEMPRE en la URL
       del fragmento (fecha_desde / fecha_hasta) y en servidor se aplica
       con whereBetween sobre created_at. Sin fechas → solo el mes en curso. */
    function leerRangoFechas() {
        var d = document.querySelector('#sheet-transacciones #trxFechaDesde');
        var h = document.querySelector('#sheet-transacciones #trxFechaHasta');
        return { desde: d ? (d.value || '') : '', hasta: h ? (h.value || '') : '' };
    }
    function aplicarRangoFechas(origen) {
        var r = leerRangoFechas();
        if (r.desde && r.hasta && r.hasta < r.desde) { trxdbg('fechas · rango invertido (hasta < desde) → el servidor lo intercambia', 'warn'); }
        PAGINA_ACTUAL = 1;
        trxdbg('fechas · ' + (origen || 'botón Aplicar') + ' · rango ' + (r.desde || '(vacío → inicio de mes)') + ' → ' + (r.hasta || '(vacío → fin de mes)') + ' · recargando página 1', 'net');
        refrescarTabla(null, 1);
    }
    function ponerMesActual() {
        var hoy = new Date(), y = hoy.getFullYear(), m = hoy.getMonth();
        function fm(n) { return (n < 10 ? '0' : '') + n; }
        var primero = y + '-' + fm(m + 1) + '-01';
        var ult = new Date(y, m + 1, 0);
        var fin = ult.getFullYear() + '-' + fm(ult.getMonth() + 1) + '-' + fm(ult.getDate());
        document.querySelectorAll('#sheet-transacciones #trxFechaDesde').forEach(function (i) { i.value = primero; });
        document.querySelectorAll('#sheet-transacciones #trxFechaHasta').forEach(function (i) { i.value = fin; });
        trxdbg('fechas · restablecido el mes en curso ' + primero + ' → ' + fin, 'info');
        PAGINA_ACTUAL = 1;
        refrescarTabla(null, 1);
    }
    function urlFragmento(pagina) {
        if (!TRX_FRAG_URL) return '';
        var base = TRX_FRAG_URL.split('?')[0];
        var inmBase = new URLSearchParams(TRX_FRAG_URL.split('?')[1] || '').get('inmueble_id');
        var inmLoc = new URLSearchParams(window.location.search).get('inmueble_id');
        var qs = new URLSearchParams();
        qs.set('inmueble_id', inmLoc || inmBase || 'todos');
        var rango = leerRangoFechas(); // TRX-v3 · el rango de fechas SIEMPRE en la URL
        if (rango.desde) { qs.set('fecha_desde', rango.desde); }
        if (rango.hasta) { qs.set('fecha_hasta', rango.hasta); }
        if (TRX_Q) { qs.set('q', TRX_Q); } // TRX-v3.1 · la búsqueda viaja SIEMPRE en la URL del fragmento
        var pg = pagina || PAGINA_ACTUAL || 1;
        if (pg > 1) qs.set('page_finanzas', pg);
        qs.set('_t', Date.now());
        return base + '?' + qs.toString();
    }
    function refrescarTabla(idResaltar, pagina) {
        TRX_Q_SEQ++; // TRX-v3.1 · invalida búsquedas en vuelo (gana el refresco más nuevo)
        var url = urlFragmento(pagina);
        if (!url) { trxdbg('refresco · sin fragmento → recarga nativa', 'warn'); window.location.reload(); return; }
        var __t0 = Date.now();
        trxdbg('refresco · fetch fragmento (página ' + (pagina || PAGINA_ACTUAL) + ')…', 'net');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                var temp = document.createElement('div'); temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-transacciones');
                var copias = document.querySelectorAll('#sheet-transacciones');
                if (!nuevo || copias.length === 0) { trxdbg('refresco · fragmento sin hoja → recarga nativa', 'warn'); window.location.reload(); return; }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                if (TRX_Q) { document.querySelectorAll('#sheet-transacciones #trxSearch').forEach(function (i2) { i2.value = TRX_Q; }); } // TRX-v3.1 · el clone llega vacío → re-sincroniza el buscador
                unicarModales(); aplicarFiltro(); if (idResaltar) resaltarFila(idResaltar); limpiarResiduos();
                document.querySelectorAll('#sheet-transacciones > .reservas-soft').forEach(function (t) {
                    t.classList.remove('trx-pulse'); void t.offsetWidth; t.classList.add('trx-pulse');
                });
                if (DBG && DBG.ensure) { DBG.ensure(); }
                trxdbg('fragmento refrescado en ' + (Date.now() - __t0) + 'ms · copias actualizadas=' + copias.length + (idResaltar ? ' · fila #' + idResaltar + ' resaltada' : ''), 'ok');
            })
            .catch(function () { trxdbg('refresco falló · fallback recarga nativa', 'err'); window.location.reload(); });
    }
    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]'); var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }
        var tokIn = form.querySelector('input[name="_token"]'); var tokMt = document.querySelector('meta[name="csrf-token"]');
        var token = (tokIn && tokIn.value) || (tokMt && tokMt.content) || '';
        var mIn = form.querySelector('input[name="_method"]');
        var metodoReal = (mIn && mIn.value) ? String(mIn.value).toUpperCase() : 'POST';
        trxdbg('AJAX · ' + metodoReal + ' ' + form.getAttribute('action') + (metodoReal !== 'POST' ? ' (spoofing _method)' : ''), 'net');
        fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, json: j }; }).catch(function () { return { ok: r.ok, status: r.status, json: null }; }); })
        .then(function (res) {
            if (res.ok && res.json && res.json.success) {
                var d = res.json.data || {};
                var idFila = d.id || (d.data && d.data.id) || form.getAttribute('data-trx-id') || null;
                ocultarModal(form.closest('.modal'));
                trxdbg('AJAX OK · ' + metodoReal + ' · ' + (res.json.message || 'guardado'), 'ok');
                trxdbg('refresco del fragmento programado (350ms)', 'debug');
                avisar(res.json.message || 'Guardado correctamente', 'success');
                setTimeout(function () { refrescarTabla(idFila); }, 350);
            } else if (res.status === 422 && res.json && res.json.errors) {
                trxdbg('AJAX 422 · validación falló · ' + Object.keys(res.json.errors).length + ' campo(s) con error', 'err');
                pintarErrores(form, res.json.errors);
            }
            else {
                trxdbg('AJAX falló · ' + ((res.json && res.json.message) || 'sin mensaje del servidor'), 'err');
                errorGeneral(form, (res.json && res.json.message) || 'No se pudo completar la operación.');
            }
        })
        .catch(function () { trxdbg('AJAX error de conexión', 'err'); errorGeneral(form, 'Error de conexión. Intenta de nuevo.'); })
        .finally(function () { if (btn) { btn.disabled = false; btn.innerHTML = txt; } });
    }
    function aplicarFiltro(inp) {
        inp = inp || document.getElementById('trxSearch');
        if (!inp) return;
        var val = (inp.value || '').toLowerCase().trim();
        var visibles = 0;
        document.querySelectorAll('#sheet-transacciones tr[data-trx-search]').forEach(function (tr) {
            var mostrar = !val || (tr.getAttribute('data-trx-search') || '').indexOf(val) !== -1;
            tr.style.display = mostrar ? '' : 'none';
            if(mostrar) visibles++;
        });
        document.querySelectorAll('#sheet-transacciones tr.trx-sin-res').forEach(function (tr) {
            tr.style.display = (val && visibles === 0) ? '' : 'none';
        });
    }
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form || !form.classList || !form.classList.contains('js-ajax-trx')) return;
        e.preventDefault(); enviarFormulario(form);
    });
    /* ═══ TRX-v3.1 · BUSCADOR INSTANTÁNEO (dos capas) ═══
       Capa 1 · CLIENTE: cada tecla filtra al instante las filas ya
       cargadas. Capa 2 · SERVIDOR: 320ms después de la última tecla,
       q viaja en la URL del fragmento y el servidor busca en TODAS
       las páginas del rango. El intercambio de la hoja re-pone el
       texto y el foco en el buscador (parece que nunca se movió) y
       la paginación y el botón Actualizar heredan q desde la URL. ═══ */
    function restaurarBusqueda() {
        if (!TRX_Q) return;
        document.querySelectorAll('#sheet-transacciones #trxSearch').forEach(function (i2) { i2.value = TRX_Q; });
    }
    function buscarServidor(valor) {
        if (valor === TRX_Q_ENVIADO) { return; }
        TRX_Q_ENVIADO = valor; TRX_Q = valor;
        var seq = ++TRX_Q_SEQ;
        PAGINA_ACTUAL = 1;
        try { var u = new URL(window.location.href); if (valor) { u.searchParams.set('q', valor); } else { u.searchParams.delete('q'); } window.history.replaceState(null, '', u); } catch (e0) {}
        var url = urlFragmento(1);
        aplicarFiltro(); // la capa cliente ya responde mientras llega el servidor
        if (!url) { trxdbg('búsqueda en vivo · sin fragmento → queda el filtro de la página visible', 'warn'); return; }
        var __tq = Date.now();
        trxdbg('búsqueda en vivo · "' + (valor || '(vacía)') + '" · consultando TODAS las páginas del rango…', 'net');
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                if (seq !== TRX_Q_SEQ) { return; } // llegó tarde: ya hay una búsqueda más nueva
                var temp = document.createElement('div'); temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-transacciones');
                var copias = document.querySelectorAll('#sheet-transacciones');
                if (!nuevo || copias.length === 0) { trxdbg('búsqueda en vivo · fragmento sin hoja → se mantiene el filtro de la página visible', 'warn'); return; }
                var activo = document.activeElement, idxCopia = -1, pos = valor.length;
                if (activo && activo.id === 'trxSearch') {
                    var hojas = Array.prototype.slice.call(document.querySelectorAll('#sheet-transacciones'));
                    idxCopia = hojas.indexOf(activo.closest('#sheet-transacciones'));
                    try { pos = (typeof activo.selectionStart === 'number') ? activo.selectionStart : valor.length; } catch (e1) {}
                }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                var hojasNuevas = document.querySelectorAll('#sheet-transacciones');
                hojasNuevas.forEach(function (h2) { var i2 = h2.querySelector('#trxSearch'); if (i2) { i2.value = valor; } });
                if (idxCopia >= 0 && hojasNuevas[idxCopia]) {
                    var inp2 = hojasNuevas[idxCopia].querySelector('#trxSearch');
                    if (inp2) { inp2.focus(); try { inp2.setSelectionRange(pos, pos); } catch (e2) {} }
                }
                unicarModales(); aplicarFiltro(); limpiarResiduos();
                if (DBG && DBG.ensure) { DBG.ensure(); }
                var enPagina = nuevo.querySelectorAll('tr[data-trx-search]').length;
                trxdbg('búsqueda en vivo · "' + (valor || '(vacía)') + '" · ' + enPagina + ' fila(s) en ' + (Date.now() - __tq) + 'ms · copias=' + copias.length + ' · paginación hereda q', 'ok');
            })
            .catch(function () { if (seq === TRX_Q_SEQ) { trxdbg('búsqueda en vivo falló · se mantiene el filtro de la página visible', 'err'); } });
    }
    document.addEventListener('input', function (e) {
        if (!e.target || e.target.id !== 'trxSearch') return;
        aplicarFiltro(e.target); // capa 1 · instantánea con lo ya cargado
        var val = (e.target.value || '').toLowerCase().trim();
        if (TRX_Q_TIMER) { clearTimeout(TRX_Q_TIMER); }
        TRX_Q_TIMER = setTimeout(function () { TRX_Q_TIMER = null; buscarServidor(val); }, 320); // capa 2 · todas las páginas
    });
    document.addEventListener('keydown', function (e) {
        if (!e.target || e.target.id !== 'trxSearch' || e.key !== 'Enter') return;
        e.preventDefault(); // Enter = buscar YA, sin esperar el debounce
        if (TRX_Q_TIMER) { clearTimeout(TRX_Q_TIMER); TRX_Q_TIMER = null; }
        var val = (e.target.value || '').toLowerCase().trim();
        buscarServidor(val);
    });
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var a = e.target.closest('#sheet-transacciones .pagination a');
        if (!a || !a.getAttribute('href')) return;
        e.preventDefault();
        try {
            var u = new URL(a.getAttribute('href'), window.location.origin);
            var pg = parseInt(u.searchParams.get('page_finanzas') || u.searchParams.get('page') || '1', 10);
            PAGINA_ACTUAL = isNaN(pg) ? 1 : pg;
            trxdbg('paginación → página ' + PAGINA_ACTUAL, 'net');
            refrescarTabla(null, PAGINA_ACTUAL);
        } catch (err) { trxdbg('paginación · URL inválida → navegación nativa', 'warn'); window.location.href = a.getAttribute('href'); }
    });

    /* ═══ TRX-v3 · BOTONES ACTUALIZAR / EXPORTAR (delegación en document:
       los botones viven DENTRO de la hoja y mueren con cada refresco; el
       listener en document sobrevive y sigue funcionando tras el swap). ═══ */
    function exportarTrx() {
        var inp = document.getElementById('trxSearch');
        var val = inp ? (inp.value || '').toLowerCase().trim() : '';
        var vistos = {}; var filas = [];
        document.querySelectorAll('#sheet-transacciones tr[data-trx-search]').forEach(function (tr) {
            var mostrar = !val || (tr.getAttribute('data-trx-search') || '').indexOf(val) !== -1;
            if (!mostrar) return;
            var idFila = tr.getAttribute('data-id');
            if (idFila) { if (vistos[idFila]) return; vistos[idFila] = 1; } // dedup multi-copia
            var b = tr.querySelector('.js-ver-trx');
            if (!b) return;
            var d = b.dataset;
            filas.push([d.referencia || 'N/A', 'ID ' + (d.id || '—'), d.reserva || '', d.pasarela || '', d.metodo || '', d.monto || '0.00', d.moneda || 'COP', d.estado || '', d.fecha || '', (d.soporte === '1' ? 'SÍ' : 'NO')]);
        });
        if (!filas.length) {
            trxdbg('Exportar: no hay transacciones que exportar con el filtro actual', 'warn');
            avisar('No hay transacciones que exportar con el filtro actual.', 'danger');
            return;
        }
        var celda = function (v) { return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"'; };
        var lineas = [['#', 'Referencia', 'ID interno', 'Reserva', 'Pasarela', 'Método', 'Monto', 'Moneda', 'Estado', 'Fecha', 'Soporte'].map(celda).join(';')];
        filas.forEach(function (r, i) { lineas.push(([i + 1].concat(r)).map(celda).join(';')); });
        var d0 = new Date();
        function f2(n) { return (n < 10 ? '0' : '') + n; }
        var nombre = 'transacciones_' + d0.getFullYear() + f2(d0.getMonth() + 1) + f2(d0.getDate()) + '_' + f2(d0.getHours()) + f2(d0.getMinutes()) + '.csv';
        try {
            var blob = new Blob(['\ufeff' + lineas.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = nombre;
            document.body.appendChild(a); a.click(); a.remove();
            var __rg = leerRangoFechas();
            trxdbg('Exportado ' + nombre + ' · ' + filas.length + ' fila(s) · filtro: ' + (val || 'sin filtro') + ' · fechas: ' + (__rg.desde || 'inicio-mes') + '→' + (__rg.hasta || 'fin-mes'), 'ok');
            avisar('Exportadas ' + filas.length + ' transacción(es) a ' + nombre, 'success');
        } catch (e2) {
            trxdbg('Exportar falló: ' + e2.message, 'err');
        }
    }
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        var btnR = e.target.closest('#trxRefresh');
        if (btnR) {
            var ico = btnR.querySelector('i.bi');
            try { btnR.disabled = true; if (ico) ico.classList.add('trx-spin'); } catch (e2) {}
            trxdbg('Actualización manual solicitada · recargando fragmento (página ' + PAGINA_ACTUAL + ')…', 'info');
            try { refrescarTabla(null, PAGINA_ACTUAL); }
            catch (e3) { trxdbg('Actualizar falló: ' + e3.message, 'err'); }
            setTimeout(function () {
                try { btnR.disabled = false; if (ico) ico.classList.remove('trx-spin'); } catch (e4) {}
            }, 1200);
            return;
        }
        if (e.target.closest('#trxExport')) { exportarTrx(); return; }
    });
    /* ═══ TRX-v3 · botones de fecha y cambio de fecha (delegación en document) ═══ */
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) return;
        if (e.target.closest('#trxAplicarFechas')) { aplicarRangoFechas(); return; }
        if (e.target.closest('#trxMesActual')) { ponerMesActual(); return; }
    });
    document.addEventListener('change', function (e) {
        if (e.target && (e.target.id === 'trxFechaDesde' || e.target.id === 'trxFechaHasta')) {
            aplicarRangoFechas('cambio de fecha');
        }
    });
    document.addEventListener('show.bs.modal', function (e) {
        var m = e.target;
        if (!m || !m.closest || !m.closest('#sheet-transacciones')) return;
        trxdbg('modal · ' + (m.id || 'sin id') + (m.id.indexOf('trxEditModal') === 0 ? ' · modo editar' : (m.id.indexOf('trxDelModal') === 0 ? ' · anulación' : (m.id === 'trxModalCreate' ? ' · modo crear' : ''))), 'info');
        m.querySelectorAll('.ps-err-general').forEach(function (g) { g.classList.add('d-none'); g.textContent = ''; });
        m.querySelectorAll('[data-error-for]').forEach(function (c) { c.textContent = ''; });
        m.querySelectorAll('.is-invalid').forEach(function (i) { i.classList.remove('is-invalid'); });
    });
    function iniciar() {
        var p = parseInt(new URLSearchParams(window.location.search).get('page_finanzas') || '1', 10);
        PAGINA_ACTUAL = isNaN(p) ? 1 : Math.max(1, p);
        var q0 = (new URLSearchParams(window.location.search).get('q') || '').trim();
        if (q0) { TRX_Q = q0; TRX_Q_ENVIADO = q0; restaurarBusqueda(); trxdbg('buscador · término activo "' + q0 + '" · el servidor ya filtró TODAS las páginas del rango', 'info'); } // TRX-v3.1 · F5 mantiene la búsqueda
        unicarModales(); limpiarResiduos(); aplicarFiltro();
        trxdbg('DOM listo · página=' + PAGINA_ACTUAL + ' · copias de la hoja=' + document.querySelectorAll('#sheet-transacciones').length + ' · búsqueda #trxSearch · botones trxRefresh (AJAX) y trxExport (CSV) por delegación', 'debug');
        trxdbg('fechas · controles #trxFechaDesde/#trxFechaHasta + Aplicar/Este mes · rango activo ' + (SRVDBG && SRVDBG.rango_fechas ? SRVDBG.rango_fechas.desde + ' → ' + SRVDBG.rango_fechas.hasta : 'n/d'), 'debug');
    }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>

<script>
(function () {
    'use strict';
    if (window.__jsTrxDetalleListo) return;
    window.__jsTrxDetalleListo = true;

    function abrirDetalle(btn) {
        var d = btn.dataset;
        if (window.TrxDbg && window.TrxDbg.log) { window.TrxDbg.log('detalle · transacción #' + (d.id || '—') + ' · ' + (d.referencia || 'N/A') + ' · $' + (d.monto || '0.00') + ' ' + (d.moneda || 'COP') + ' · ' + (d.estado || ''), 'info'); }
        function txt(id, val) { var el = document.getElementById(id); if (el) el.textContent = val || '—'; }

        txt('fdRef', d.referencia);
        txt('fdId', 'ID interno #' + (d.id || '—'));
        txt('fdReserva', d.reserva);
        txt('fdPasarela', d.pasarela);
        txt('fdMetodo', d.metodo);
        txt('fdMonto', '$' + (d.monto || '0.00') + ' ' + (d.moneda || 'COP'));
        txt('fdFecha', d.fecha);

        var est = document.getElementById('fdEstado');
        if (est) {
            est.textContent = d.estado || 'PENDIENTE';
            var n = (d.estado || '').toLowerCase();
            est.style.color = (n === 'aprobado' || n === 'completado' || n === 'pagado') ? '#3d7a5c' :
                              (n === 'rechazado' || n === 'anulado' || n === 'cancelado') ? '#c98a85' : '#b45309';
        }

        var caja = document.getElementById('fdSoporteCaja');
        if (caja) {
            caja.innerHTML = '';
            if (d.soporte === '1' && d.soporteUrl && d.soporteUrl !== '#') {
                var a = document.createElement('a');
                a.href = d.soporteUrl; a.target = '_blank'; a.rel = 'noopener noreferrer';
                a.className = 'btn-mini';
                a.innerHTML = '<i class="bi bi-file-earmark-text"></i> Ver soporte adjunto';
                caja.appendChild(a);
            } else {
                var vacio = document.createElement('span');
                vacio.style.cssText = 'font-size: 7.5px; color: #94a3b8; font-weight: 600;';
                vacio.textContent = 'Sin soporte de pago adjunto';
                caja.appendChild(vacio);
            }
        }

        var modal = document.getElementById('modalTransaccionDetalle');
        if (!modal) return;
        if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Modal.getOrCreateInstance) {
            window.bootstrap.Modal.getOrCreateInstance(modal).show();
        } else {
            modal.classList.add('show'); modal.style.display = 'block'; modal.removeAttribute('aria-hidden');
            var bd = document.createElement('div'); bd.className = 'modal-backdrop show';
            document.body.appendChild(bd); document.body.classList.add('modal-open');
        }
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('.js-ver-trx') : null;
        if (btn) { abrirDetalle(btn); return; }

        if (!(window.bootstrap && window.bootstrap.Modal)) {
            var dis = e.target.closest ? e.target.closest('[data-bs-dismiss="modal"]') : null;
            if (dis) {
                var m = dis.closest('.modal');
                if (m) {
                    m.classList.remove('show'); m.style.display = 'none'; m.setAttribute('aria-hidden', 'true');
                    document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                    document.body.classList.remove('modal-open');
                }
            }
        }
    });
})();
</script>
