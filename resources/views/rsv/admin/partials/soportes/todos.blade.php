{{-- resources/views/rsv/admin/partials/soportes/todos.blade.php · SOP-TD-v2.0-REAL · NIVEL 3 --}}
@php
    $sopTdInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopTdInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopTdInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopTdInmId = (int) request('inmueble_id');
    }

    if (!isset($soportes)) {
        $qMsg = \App\Models\Rsv\Mensaje::with(['reserva.inmueble', 'reserva.user', 'remitente'])
            ->orderBy('created_at', 'asc');
        if ($sopTdInmId) {
            $qMsg->whereHas('reserva', fn($r) => $r->where('id_rsv_catalogo_inmueble', $sopTdInmId));
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
        })->sortByDesc('created_at')->values();
    }

    $itemsTd = collect($soportes)->values();
    $urlResponderTpl = \Illuminate\Support\Facades\Route::has('rsv.soportes.responder')
        ? route('rsv.soportes.responder', ['id' => '__ID__'])
        : url('/rsv/soportes/__ID__/responder');
@endphp

<div id="sopTdRoot" data-responder-tpl="{{ $urlResponderTpl }}">
    <div class="border rounded-3 bg-white overflow-hidden">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size:10px;">TODOS ESCRITOS</span>
                <strong style="font-size:13px; color:#334155;">Bandeja Completa de Soportes ({{ $itemsTd->count() }})</strong>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-dark js-td-filter" data-f="todos" style="font-size:11px;">Todos</button>
                    <button type="button" class="btn btn-outline-danger js-td-filter" data-f="sin_responder" style="font-size:11px;">Sin responder</button>
                    <button type="button" class="btn btn-outline-success js-td-filter" data-f="respondido" style="font-size:11px;">Respondidos</button>
                </div>
                <input type="search" class="form-control form-control-sm js-td-search" placeholder="Buscar…" style="max-width:220px;">
            </div>
        </div>

        @if($itemsTd->isEmpty())
            <div class="p-5 text-center text-muted" style="font-size:12px;">
                No hay mensajes escritos en <code>rsv_mensajes</code>.
            </div>
        @else
            <div class="row g-0">
                <div class="col-12 col-md-5 border-end" style="max-height:440px; overflow-y:auto;">
                    @foreach($itemsTd as $idx => $hilo)
                        <div class="p-3 border-bottom js-td-item {{ $idx === 0 ? 'bg-success-subtle' : '' }}"
                             style="cursor:pointer;"
                             data-reserva-id="{{ $hilo->reserva_id }}"
                             data-estado="{{ !empty($hilo->respondido) ? 'respondido' : 'sin_responder' }}"
                             data-search="{{ mb_strtolower($hilo->usuario_nombre . ' ' . $hilo->codigo_reserva . ' ' . $hilo->inmueble_nombre . ' ' . $hilo->ultimo_mensaje) }}">
                            <div class="d-flex justify-content-between align-items-center gap-1">
                                <strong style="font-size:12px; color:#0f172a;">{{ $hilo->usuario_nombre }}</strong>
                                @if(!empty($hilo->respondido))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:9.5px;">Respondido</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:9.5px;">Sin responder</span>
                                @endif
                            </div>
                            <div class="text-muted mt-1 text-truncate" style="font-size:11.5px;">{{ $hilo->ultimo_mensaje }}</div>
                            <div class="text-muted mt-1" style="font-size:10.5px;">{{ $hilo->codigo_reserva }} · {{ $hilo->inmueble_nombre }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="col-12 col-md-7 p-3 d-flex flex-column" id="sopTdThreadBox">
                    @php $primeroTd = $itemsTd->first(); @endphp
                    <div class="border-bottom pb-2 mb-2">
                        <div class="fw-bold" style="font-size:13px; color:#0f172a;">{{ $primeroTd->usuario_nombre }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $primeroTd->codigo_reserva }} · {{ $primeroTd->inmueble_nombre }}</div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3" style="max-height:240px; overflow-y:auto;">
                        @foreach($primeroTd->mensajes as $m)
                            <div class="p-2 rounded-3 border {{ $m['rol'] === 'Soporte' ? 'bg-success-subtle border-success-subtle ms-3' : 'bg-light me-3' }}" style="font-size:12px;">
                                <div class="d-flex justify-content-between mb-1" style="font-size:10.5px;">
                                    <strong>{{ $m['autor'] }}</strong>
                                    <span class="text-muted">{{ $m['hora'] }}</span>
                                </div>
                                <div>{{ $m['texto'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <form id="sopTdReplyForm" data-reserva-id="{{ $primeroTd->reserva_id }}" class="mt-auto">
                        <textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Responder en este hilo de rsv_mensajes…" required></textarea>
                        <button type="submit" class="btn btn-sm btn-success fw-bold px-3">
                            <i class="bi bi-send me-1"></i> Enviar respuesta
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script type="application/json" id="sopTdDataJson">@json($itemsTd)</script>
    <script>
        (function () {
            var dataEl = document.getElementById('sopTdDataJson');
            var hilos = [];
            try { hilos = JSON.parse((dataEl && dataEl.textContent) || '[]') || []; } catch (e) {}
            var estadoActual = 'todos';

            function filtrarTd() {
                var q = (document.querySelector('#sopTdRoot .js-td-search')?.value || '').toLowerCase().trim();
                document.querySelectorAll('#sopTdRoot .js-td-item').forEach(function (el) {
                    var est = el.getAttribute('data-estado') || '';
                    var blob = el.getAttribute('data-search') || '';
                    var okEst = (estadoActual === 'todos') || (est === estadoActual);
                    var okQ = !q || blob.indexOf(q) !== -1;
                    el.style.display = (okEst && okQ) ? '' : 'none';
                });
            }

            document.addEventListener('input', function (e) {
                if (e.target && e.target.classList.contains('js-td-search')) filtrarTd();
            });

            document.addEventListener('click', function (e) {
                var fBtn = e.target && e.target.closest ? e.target.closest('#sopTdRoot .js-td-filter') : null;
                if (fBtn) {
                    estadoActual = fBtn.getAttribute('data-f') || 'todos';
                    filtrarTd();
                    return;
                }

                var item = e.target && e.target.closest ? e.target.closest('#sopTdRoot .js-td-item') : null;
                if (!item) return;
                var rid = item.getAttribute('data-reserva-id');
                document.querySelectorAll('#sopTdRoot .js-td-item').forEach(function (el) { el.classList.remove('bg-success-subtle'); });
                item.classList.add('bg-success-subtle');

                var h = hilos.find(function (x) { return String(x.reserva_id) === String(rid); });
                var box = document.getElementById('sopTdThreadBox');
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
                    '<div class="d-flex flex-column gap-2 mb-3" style="max-height:240px; overflow-y:auto;">' + msgsHtml + '</div>' +
                    '<form id="sopTdReplyForm" data-reserva-id="' + h.reserva_id + '" class="mt-auto"><textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Responder en este hilo de rsv_mensajes…" required></textarea><button type="submit" class="btn btn-sm btn-success fw-bold px-3"><i class="bi bi-send me-1"></i> Enviar respuesta</button></form>';
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || form.id !== 'sopTdReplyForm') return;
                e.preventDefault();
                var rid = form.getAttribute('data-reserva-id');
                var ta = form.querySelector('textarea');
                var btn = form.querySelector('button[type="submit"]');
                var txt = ta ? ta.value.trim() : '';
                if (!txt) return;

                var root = document.getElementById('sopTdRoot');
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
