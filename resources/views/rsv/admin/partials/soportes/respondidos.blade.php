{{-- resources/views/rsv/admin/partials/soportes/respondidos.blade.php · SOP-RS-v2.0-REAL · NIVEL 3 --}}
@php
    $sopRsInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopRsInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopRsInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopRsInmId = (int) request('inmueble_id');
    }

    if (!isset($soportes)) {
        $qMsg = \App\Models\Rsv\Mensaje::with(['reserva.inmueble', 'reserva.user', 'remitente'])
            ->orderBy('created_at', 'asc');
        if ($sopRsInmId) {
            $qMsg->whereHas('reserva', fn($r) => $r->where('id_rsv_catalogo_inmueble', $sopRsInmId));
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
                'mensajes'        => $grupo->map(function ($m) use ($clienteId) {
                    $esCliente = ((int) $m->id_user_remitente === (int) $clienteId);
                    return [
                        'autor'       => optional($m->remitente)->name ?? ($esCliente ? 'Cliente' : 'Administración RSV'),
                        'rol'         => $esCliente ? 'Cliente' : 'Soporte',
                        'hora'        => optional($m->created_at)->format('d/m H:i') ?? '',
                        'texto'       => (string) ($m->contenido ?? ''),
                        'url_archivo' => $m->url_archivo,
                    ];
                })->values()->all(),
            ];
        })->filter(fn($h) => $h->respondido)->sortByDesc('created_at')->values();
    }

    $itemsRs = collect($soportes)->filter(fn($h) => !empty($h->respondido))->values();
@endphp

<div id="sopRsRoot">
    <div class="border rounded-3 bg-white overflow-hidden">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle me-1" style="font-size:10px;">RESPONDIDOS</span>
                <strong style="font-size:13px; color:#334155;">Conversaciones Respondidas ({{ $itemsRs->count() }})</strong>
            </div>
            <input type="search" class="form-control form-control-sm js-rs-search" placeholder="Filtrar respondidos…" style="max-width:260px;">
        </div>

        @if($itemsRs->isEmpty())
            <div class="p-5 text-center text-muted" style="font-size:12px;">
                Aún no hay conversaciones respondidas registradas en <code>rsv_mensajes</code>.
            </div>
        @else
            <div class="row g-0">
                <div class="col-12 col-md-5 border-end" style="max-height:440px; overflow-y:auto;">
                    @foreach($itemsRs as $idx => $hilo)
                        <div class="p-3 border-bottom js-rs-item {{ $idx === 0 ? 'bg-success-subtle' : '' }}"
                             style="cursor:pointer;"
                             data-reserva-id="{{ $hilo->reserva_id }}"
                             data-search="{{ mb_strtolower($hilo->usuario_nombre . ' ' . $hilo->codigo_reserva . ' ' . $hilo->inmueble_nombre . ' ' . $hilo->ultimo_mensaje) }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong style="font-size:12px; color:#0f172a;">{{ $hilo->usuario_nombre }}</strong>
                                <span class="text-success fw-semibold" style="font-size:10px;">{{ optional($hilo->created_at)->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-muted mt-1 text-truncate" style="font-size:11.5px;">{{ $hilo->ultimo_mensaje }}</div>
                            <div class="text-muted mt-1" style="font-size:10.5px;">{{ $hilo->codigo_reserva }} · {{ $hilo->inmueble_nombre }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="col-12 col-md-7 p-3" id="sopRsThreadBox">
                    @php $primeroRs = $itemsRs->first(); @endphp
                    <div class="border-bottom pb-2 mb-2">
                        <div class="fw-bold" style="font-size:13px; color:#0f172a;">{{ $primeroRs->usuario_nombre }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $primeroRs->codigo_reserva }} · {{ $primeroRs->inmueble_nombre }}</div>
                    </div>
                    <div class="d-flex flex-column gap-2" style="max-height:310px; overflow-y:auto;">
                        @foreach($primeroRs->mensajes as $m)
                            <div class="p-2 rounded-3 border {{ $m['rol'] === 'Soporte' ? 'bg-success-subtle border-success-subtle ms-3' : 'bg-light me-3' }}" style="font-size:12px;">
                                <div class="d-flex justify-content-between mb-1" style="font-size:10.5px;">
                                    <strong>{{ $m['autor'] }}</strong>
                                    <span class="text-muted">{{ $m['hora'] }}</span>
                                </div>
                                <div>{{ $m['texto'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script type="application/json" id="sopRsDataJson">@json($itemsRs)</script>
    <script>
        (function () {
            var dataEl = document.getElementById('sopRsDataJson');
            var hilos = [];
            try { hilos = JSON.parse((dataEl && dataEl.textContent) || '[]') || []; } catch (e) {}

            document.addEventListener('input', function (e) {
                if (!e.target || !e.target.classList.contains('js-rs-search')) return;
                var q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('#sopRsRoot .js-rs-item').forEach(function (el) {
                    var blob = el.getAttribute('data-search') || '';
                    el.style.display = (!q || blob.indexOf(q) !== -1) ? '' : 'none';
                });
            });

            document.addEventListener('click', function (e) {
                var item = e.target && e.target.closest ? e.target.closest('#sopRsRoot .js-rs-item') : null;
                if (!item) return;
                var rid = item.getAttribute('data-reserva-id');
                document.querySelectorAll('#sopRsRoot .js-rs-item').forEach(function (el) { el.classList.remove('bg-success-subtle'); });
                item.classList.add('bg-success-subtle');

                var h = hilos.find(function (x) { return String(x.reserva_id) === String(rid); });
                var box = document.getElementById('sopRsThreadBox');
                if (!h || !box) return;

                var msgsHtml = (h.mensajes || []).map(function (m) {
                    var cls = m.rol === 'Soporte' ? 'bg-success-subtle border-success-subtle ms-3' : 'bg-light me-3';
                    return '<div class="p-2 rounded-3 border ' + cls + '" style="font-size:12px;">' +
                        '<div class="d-flex justify-content-between mb-1" style="font-size:10.5px;"><strong>' + m.autor + '</strong><span class="text-muted">' + m.hora + '</span></div>' +
                        '<div>' + m.texto + '</div>' +
                    '</div>';
                }).join('');

                box.innerHTML =
                    '<div class="border-bottom pb-2 mb-2"><div class="fw-bold" style="font-size:13px; color:#0f172a;">' + h.usuario_nombre + '</div><div class="text-muted" style="font-size:11px;">' + h.codigo_reserva + ' · ' + h.inmueble_nombre + '</div></div>' +
                    '<div class="d-flex flex-column gap-2" style="max-height:310px; overflow-y:auto;">' + msgsHtml + '</div>';
            });
        })();
    </script>
</div>
