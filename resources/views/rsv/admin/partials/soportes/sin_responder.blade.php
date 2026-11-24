{{-- resources/views/rsv/admin/partials/soportes/sin_responder.blade.php · SOP-SR-v2.0-REAL · NIVEL 3 --}}
@php
    $sopSrInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopSrInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopSrInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopSrInmId = (int) request('inmueble_id');
    }

    if (!isset($soportes)) {
        $qMsg = \App\Models\Rsv\Mensaje::with(['reserva.inmueble', 'reserva.user', 'remitente'])
            ->orderBy('created_at', 'asc');
        if ($sopSrInmId) {
            $qMsg->whereHas('reserva', fn($r) => $r->where('id_rsv_catalogo_inmueble', $sopSrInmId));
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
                'usuario_doc'     => optional($clienteObj)->nit ?? optional($clienteObj)->nid ?? optional($clienteObj)->email ?? '',
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
        })->filter(fn($h) => !$h->respondido)->sortByDesc('created_at')->values();
    }

    $itemsSr = collect($soportes)->filter(fn($h) => empty($h->respondido))->values();
    $urlResponderTpl = \Illuminate\Support\Facades\Route::has('rsv.soportes.responder')
        ? route('rsv.soportes.responder', ['id' => '__ID__'])
        : url('/rsv/soportes/__ID__/responder');
@endphp

<div id="sopSrRoot" data-responder-tpl="{{ $urlResponderTpl }}">
    <div class="border rounded-3 bg-white overflow-hidden">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1" style="font-size:10px;">SIN RESPONDER</span>
                <strong style="font-size:13px; color:#334155;">Conversaciones Pendientes de Respuesta ({{ $itemsSr->count() }})</strong>
            </div>
            <input type="search" class="form-control form-control-sm js-sr-search" placeholder="Filtrar por usuario, código RSV o inmueble…" style="max-width:260px;">
        </div>

        @if($itemsSr->isEmpty())
            <div class="p-5 text-center text-muted" style="font-size:12px;">
                <i class="bi bi-check2-circle text-success fs-3 d-block mb-2"></i>
                No hay mensajes sin responder en <code>rsv_mensajes</code>.
            </div>
        @else
            <div class="row g-0">
                <div class="col-12 col-md-5 border-end" style="max-height:440px; overflow-y:auto;">
                    @foreach($itemsSr as $idx => $hilo)
                        <div class="p-3 border-bottom js-sr-item {{ $idx === 0 ? 'bg-success-subtle' : '' }}"
                             style="cursor:pointer;"
                             data-reserva-id="{{ $hilo->reserva_id }}"
                             data-search="{{ mb_strtolower($hilo->usuario_nombre . ' ' . $hilo->codigo_reserva . ' ' . $hilo->inmueble_nombre . ' ' . $hilo->ultimo_mensaje) }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong style="font-size:12px; color:#0f172a;">{{ $hilo->usuario_nombre }}</strong>
                                <span class="text-danger fw-bold" style="font-size:10px;">{{ optional($hilo->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted mt-1 text-truncate" style="font-size:11.5px;">{{ $hilo->ultimo_mensaje }}</div>
                            <div class="text-muted mt-1" style="font-size:10.5px;">{{ $hilo->codigo_reserva }} · {{ $hilo->inmueble_nombre }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="col-12 col-md-7 p-3 d-flex flex-column" id="sopSrThreadBox">
                    @php $primeroSr = $itemsSr->first(); @endphp
                    <div class="border-bottom pb-2 mb-2">
                        <div class="fw-bold" style="font-size:13px; color:#0f172a;">{{ $primeroSr->usuario_nombre }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $primeroSr->codigo_reserva }} · {{ $primeroSr->inmueble_nombre }}</div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3" style="max-height:240px; overflow-y:auto;" id="sopSrMsgsList">
                        @foreach($primeroSr->mensajes as $m)
                            <div class="p-2 rounded-3 border {{ $m['rol'] === 'Soporte' ? 'bg-success-subtle border-success-subtle ms-3' : 'bg-light me-3' }}" style="font-size:12px;">
                                <div class="d-flex justify-content-between mb-1" style="font-size:10.5px;">
                                    <strong>{{ $m['autor'] }}</strong>
                                    <span class="text-muted">{{ $m['hora'] }}</span>
                                </div>
                                <div>{{ $m['texto'] }}</div>
                                @if(!empty($m['url_archivo']))
                                    <a href="{{ $m['url_archivo'] }}" target="_blank" class="d-inline-block mt-1 small"><i class="bi bi-paperclip"></i> Ver adjunto</a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <form id="sopSrReplyForm" data-reserva-id="{{ $primeroSr->reserva_id }}" class="mt-auto">
                        <textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Escribe la respuesta oficial para guardar en rsv_mensajes…" required></textarea>
                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3 w-100 w-md-auto">
                            <i class="bi bi-send me-1"></i> Enviar respuesta en plataforma
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script type="application/json" id="sopSrDataJson">@json($itemsSr)</script>
    <script>
        (function () {
            var dataEl = document.getElementById('sopSrDataJson');
            var hilos = [];
            try { hilos = JSON.parse((dataEl && dataEl.textContent) || '[]') || []; } catch (e) {}

            document.addEventListener('input', function (e) {
                if (!e.target || !e.target.classList.contains('js-sr-search')) return;
                var q = e.target.value.toLowerCase().trim();
                document.querySelectorAll('#sopSrRoot .js-sr-item').forEach(function (el) {
                    var blob = el.getAttribute('data-search') || '';
                    el.style.display = (!q || blob.indexOf(q) !== -1) ? '' : 'none';
                });
            });

            document.addEventListener('click', function (e) {
                var item = e.target && e.target.closest ? e.target.closest('#sopSrRoot .js-sr-item') : null;
                if (!item) return;
                var rid = item.getAttribute('data-reserva-id');
                document.querySelectorAll('#sopSrRoot .js-sr-item').forEach(function (el) { el.classList.remove('bg-success-subtle'); });
                item.classList.add('bg-success-subtle');

                var h = hilos.find(function (x) { return String(x.reserva_id) === String(rid); });
                var box = document.getElementById('sopSrThreadBox');
                if (!h || !box) return;

                var msgsHtml = (h.mensajes || []).map(function (m) {
                    var cls = m.rol === 'Soporte' ? 'bg-success-subtle border-success-subtle ms-3' : 'bg-light me-3';
                    var adj = m.url_archivo ? '<a href="' + m.url_archivo + '" target="_blank" class="d-inline-block mt-1 small"><i class="bi bi-paperclip"></i> Ver adjunto</a>' : '';
                    return '<div class="p-2 rounded-3 border ' + cls + '" style="font-size:12px;">' +
                        '<div class="d-flex justify-content-between mb-1" style="font-size:10.5px;"><strong>' + m.autor + '</strong><span class="text-muted">' + m.hora + '</span></div>' +
                        '<div>' + m.texto + '</div>' + adj +
                    '</div>';
                }).join('');

                box.innerHTML =
                    '<div class="border-bottom pb-2 mb-2"><div class="fw-bold" style="font-size:13px; color:#0f172a;">' + h.usuario_nombre + '</div><div class="text-muted" style="font-size:11px;">' + h.codigo_reserva + ' · ' + h.inmueble_nombre + '</div></div>' +
                    '<div class="d-flex flex-column gap-2 mb-3" style="max-height:240px; overflow-y:auto;" id="sopSrMsgsList">' + msgsHtml + '</div>' +
                    '<form id="sopSrReplyForm" data-reserva-id="' + h.reserva_id + '" class="mt-auto"><textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Escribe la respuesta oficial para guardar en rsv_mensajes…" required></textarea><button type="submit" class="btn btn-sm btn-success fw-bold px-3"><i class="bi bi-send me-1"></i> Enviar respuesta en plataforma</button></form>';
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || form.id !== 'sopSrReplyForm') return;
                e.preventDefault();
                var rid = form.getAttribute('data-reserva-id');
                var ta = form.querySelector('textarea');
                var btn = form.querySelector('button[type="submit"]');
                var txt = ta ? ta.value.trim() : '';
                if (!txt) return;

                var root = document.getElementById('sopSrRoot');
                var url = (root ? root.getAttribute('data-responder-tpl') : '').replace('__ID__', encodeURIComponent(rid));
                var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                if (btn) btn.disabled = true;
                fetch(url, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ respuesta: txt })
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.success) {
                        var row = document.querySelector('#sopSrRoot .js-sr-item[data-reserva-id="' + rid + '"]');
                        if (row) row.remove();
                        form.innerHTML = '<div class="alert alert-success py-2 px-3 mb-0 small"><i class="bi bi-check-circle me-1"></i>Respuesta guardada en <code>rsv_mensajes</code>.</div>';
                    } else {
                        alert((res && res.message) || 'No se pudo enviar la respuesta.');
                    }
                })
                .finally(function () { if (btn) btn.disabled = false; });
            });
        })();
    </script>
</div>
