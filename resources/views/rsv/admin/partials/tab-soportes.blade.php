{{-- resources/views/rsv/admin/partials/tab-soportes.blade.php · SOP-v2.0-REAL --}}
@php
    $__sopDbgInmId = null;
    if (isset($inmueble) && $inmueble) {
        $__sopDbgInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $__sopDbgInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $__sopDbgInmId = (int) request('inmueble_id');
    }
    $sopGlobal = empty($__sopDbgInmId);

    // Cargar hilos reales desde rsv_mensajes si no vinieron inyectados
    if (!isset($soportes)) {
        $qMsg = \App\Models\Rsv\Mensaje::with(['reserva.inmueble', 'reserva.user', 'remitente'])
            ->orderBy('created_at', 'asc');
        if ($__sopDbgInmId) {
            $qMsg->whereHas('reserva', fn($r) => $r->where('id_rsv_catalogo_inmueble', $__sopDbgInmId));
        }
        $soportes = $qMsg->get()->groupBy('id_rsv_reservas')->map(function ($grupo, $reservaId) {
            $ultimo    = $grupo->last();
            $primero   = $grupo->first();
            $reserva   = $ultimo->reserva;
            $clienteId = optional($reserva)->id_user ?? optional($primero)->id_user_remitente;
            $esUltimoCliente = ((int) $ultimo->id_user_remitente === (int) $clienteId);
            $clienteObj = optional($reserva)->user ?? $primero->remitente;

            return (object) [
                'id'              => (int) $reservaId,
                'reserva_id'      => (int) $reservaId,
                'codigo_reserva'  => optional($reserva)->codigo_reserva ?? ('RSV-' . $reservaId),
                'inmueble_nombre' => optional(optional($reserva)->inmueble)->name ?? 'Sin inmueble',
                'usuario_nombre'  => optional($clienteObj)->name ?? ('Usuario #' . $clienteId),
                'ultimo_mensaje'  => (string) ($ultimo->contenido ?? ''),
                'respondido'      => !$esUltimoCliente,
                'created_at'      => $ultimo->created_at,
            ];
        })->sortByDesc('created_at')->values();
    }

    $sopColSafe    = collect($soportes);
    $sopCntTotal   = $sopColSafe->count();
    $sopCntSinResp = $sopColSafe->filter(fn ($m) => empty($m->respondido))->count();
    $sopCntResp    = max(0, $sopCntTotal - $sopCntSinResp);
@endphp

<div class="p-3 bg-white rounded-3 border" id="sopRoot" style="border-color: #f1f5f9 !important;">

    {{-- HERO BANNER --}}
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 11px; padding: 18px 22px; color: #fff; margin-bottom: 12px;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div style="max-width: 660px;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-success text-white fw-bold" style="font-size: 7.5px; letter-spacing: .5px; text-transform: uppercase;">
                        <i class="bi bi-chat-left-text me-1"></i> Soportes Reales · rsv_mensajes
                    </span>
                    <span class="badge rounded-pill" style="font-size: 7px; background: rgba(2,6,23,.94); color: #38bdf8; border: 1px solid #334155; font-family: ui-monospace, monospace;">
                        SOP-v2.0-REAL
                    </span>
                </div>
                <h6 class="fw-bold text-white mb-1" style="font-size: 14px;">
                    Centro de Mensajes Escritos · Quiénes Escribieron y Sin Responder
                </h6>
                <p class="mb-2 text-light opacity-75" style="font-size: 9.5px;">
                    Visualiza los hilos reales de <code>rsv_mensajes</code> agrupados por reserva y responde directamente en la plataforma sin WhatsApp ni llamadas.
                </p>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-success fw-bold px-3 js-sop-jump" data-sub="#sop-sub-sin-responder" data-single-sub="#single-sop-sub-sin-responder" style="font-size: 9px;">
                        <i class="bi bi-envelope-exclamation me-1"></i> Sin Responder ({{ $sopCntSinResp }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light fw-bold px-3 js-sop-jump" data-sub="#sop-sub-respondidos" data-single-sub="#single-sop-sub-respondidos" style="font-size: 9px;">
                        <i class="bi bi-check2-all me-1"></i> Respondidos ({{ $sopCntResp }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-light fw-bold px-3 js-sop-jump" data-sub="#sop-sub-todos" data-single-sub="#single-sop-sub-todos" style="font-size: 9px;">
                        <i class="bi bi-chat-left-text me-1"></i> Todos ({{ $sopCntTotal }})
                    </button>
                </div>
            </div>

            <div class="text-md-end">
                <div style="font-size: 7.5px; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8;">Pendientes de Respuesta</div>
                <div class="fw-bold text-warning" style="font-size: 20px; line-height: 1.15;">
                    {{ $sopCntSinResp }} <span style="font-size: 9px; color: #94a3b8;">HILOS</span>
                </div>
                <div style="font-size: 8px; color: #cbd5e1;">
                    {{ $sopGlobal ? 'Todas las propiedades' : ('Propiedad #' . $__sopDbgInmId) }}
                </div>
            </div>
        </div>
    </div>

    {{-- TARJETAS KPI --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="p-2 px-3 border rounded-3 bg-white h-100 js-sop-jump" style="cursor:pointer;" data-sub="#sop-sub-sin-responder" data-single-sub="#single-sop-sub-sin-responder">
                <div style="font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Sin Responder</div>
                <div class="fw-bold text-danger" style="font-size: 14px;">{{ $sopCntSinResp }}</div>
                <div style="font-size: 8px; color: #64748b;">esperando respuesta</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 px-3 border rounded-3 bg-white h-100 js-sop-jump" style="cursor:pointer;" data-sub="#sop-sub-respondidos" data-single-sub="#single-sop-sub-respondidos">
                <div style="font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Respondidos</div>
                <div class="fw-bold text-success" style="font-size: 14px;">{{ $sopCntResp }}</div>
                <div style="font-size: 8px; color: #64748b;">atendidos en portal</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 px-3 border rounded-3 bg-white h-100 js-sop-jump" style="cursor:pointer;" data-sub="#sop-sub-todos" data-single-sub="#single-sop-sub-todos">
                <div style="font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Total Conversaciones</div>
                <div class="fw-bold text-primary" style="font-size: 14px;">{{ $sopCntTotal }}</div>
                <div style="font-size: 8px; color: #64748b;">reservas con mensajes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2 px-3 border rounded-3 bg-white h-100">
                <div style="font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #94a3b8;">Tabla Origen</div>
                <div class="fw-bold text-dark" style="font-size: 12px;">rsv_mensajes</div>
                <div style="font-size: 8px; color: #3d7a5c;">sincronización directa BD</div>
            </div>
        </div>
    </div>

    {{-- TABLA RESUMEN DE HILOS REALES --}}
    <div class="border rounded-3 overflow-hidden">
        @if($sopCntTotal > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 8.5px;">
                    <thead style="background:#fafbfd; color:#94a3b8; font-size:8px; text-transform:uppercase;">
                        <tr>
                            <th>Quien escribió</th>
                            <th>Reserva / Propiedad</th>
                            <th>Último mensaje</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sopColSafe->take(12) as $hilo)
                            <tr>
                                <td class="fw-bold text-dark">{{ $hilo->usuario_nombre }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $hilo->codigo_reserva }}</span> · {{ $hilo->inmueble_nombre }}</td>
                                <td class="text-muted text-truncate" style="max-width:280px;">{{ $hilo->ultimo_mensaje }}</td>
                                <td class="text-center">
                                    @if(!empty($hilo->respondido))
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:7px;">Respondido</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:7px;">Sin responder</span>
                                    @endif
                                </td>
                                <td class="text-end text-muted" style="font-size:7.5px;">{{ optional($hilo->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted" style="font-size: 8.5px;">
                <i class="bi bi-inbox me-1"></i> No hay mensajes registrados en <code>rsv_mensajes</code> para este alcance.
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        document.addEventListener('click', function (e) {
            var btn = e.target && e.target.closest ? e.target.closest('.js-sop-jump') : null;
            if (!btn) return;
            var esGlobal = !!document.getElementById('globalAdminTabs');
            var sel = esGlobal ? btn.getAttribute('data-sub') : btn.getAttribute('data-single-sub');
            var tab = sel ? document.querySelector('[data-bs-target="' + sel + '"]') : null;
            if (tab) tab.click();
        });
    })();
</script>
