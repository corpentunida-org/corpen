{{-- resources/views/rsv/admin/partials/soportes/sin_responder.blade.php · SOP-SR-v1.1-DBG · NIVEL 3 --}}
@php
    $sopSrInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopSrInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopSrInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopSrInmId = (int) request('inmueble_id');
    }

    $srvSrItems = collect($soportes ?? [])->filter(fn ($m) => empty($m->respondido))->map(function ($m) {
        $inmObj = $m->inmueble ?? null;
        return [
            'id'         => (string) ($m->id ?? uniqid('sop-')),
            'esDb'       => is_numeric($m->id ?? null),
            'usuario'    => (string) ($m->remitente_nombre ?? $m->autor ?? $m->nombre ?? 'Usuario'),
            'documento'  => (string) ($m->documento ?? 'Portal RSV'),
            'reserva'    => (string) ($m->codigo_reserva ?? $m->reserva_codigo ?? 'GENERAL'),
            'inmuebleId' => (string) ($m->id_rsv_catalogo_inmueble ?? optional($inmObj)->id ?? ''),
            'inmueble'   => (string) (optional($inmObj)->name ?? $m->inmueble_nombre ?? 'General'),
            'asunto'     => (string) ($m->asunto ?? 'Consulta escrita'),
            'estado'     => 'sin_responder',
            'actualizado'=> optional($m->created_at)->diffForHumans() ?? 'Reciente',
            'mensajes'   => [[
                'autor' => (string) ($m->remitente_nombre ?? $m->autor ?? $m->nombre ?? 'Usuario'),
                'rol'   => 'Usuario',
                'hora'  => optional($m->created_at)->format('H:i') ?? now()->format('H:i'),
                'texto' => (string) ($m->mensaje ?? $m->contenido ?? $m->cuerpo ?? $m->asunto ?? ''),
            ]],
        ];
    })->values()->all();

    $urlResponderTpl = \Illuminate\Support\Facades\Route::has('rsv.soportes.responder')
        ? route('rsv.soportes.responder', ['id' => '__ID__'])
        : url('/rsv/soportes/__ID__/responder');
@endphp

<div id="sopSrRoot" class="sop-n3-root" data-sop-view="sin_responder" data-sop-inm="{{ $sopSrInmId ?? '' }}" data-responder-tpl="{{ $urlResponderTpl }}">
    <div class="sop-n3-card">
        <div class="sop-n3-head">
            <div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1" style="font-size:10px;">SIN RESPONDER</span>
                <strong style="font-size:13px; color:#334155;">Quiénes Escribieron y Están Pendientes</strong>
                <div class="text-muted" style="font-size:11px;">Responde aquí mismo sin enviar a WhatsApp ni llamadas</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-danger text-white js-sop-sr-count">0 sin responder</span>
                <input type="search" class="sop-n3-search js-sop-sr-q" placeholder="Buscar persona, reserva, asunto…">
            </div>
        </div>

        <div class="row g-0">
            <div class="col-12 col-md-5 border-end">
                <div class="sop-n3-list js-sop-sr-list"></div>
            </div>
            <div class="col-12 col-md-7">
                <div class="sop-n3-thread js-sop-sr-thread"></div>
            </div>
        </div>
    </div>

    <script type="application/json" class="js-sop-sr-seed">@json($srvSrItems)</script>
    <script>
        (function () {
            'use strict';
            var KEY = 'rsv_admin_soportes_v1';
            var selId = null;
            var qStr = '';

            function getStore() {
                try {
                    var raw = localStorage.getItem(KEY);
                    if (raw) {
                        var arr = JSON.parse(raw);
                        if (Array.isArray(arr) && arr.length) return arr;
                    }
                } catch (e) {}
                var seedEl = document.querySelector('#sopSrRoot .js-sop-sr-seed');
                var extra = [];
                try { extra = JSON.parse((seedEl && seedEl.textContent) || '[]') || []; } catch (e2) {}
                try { localStorage.setItem(KEY, JSON.stringify(extra)); } catch (e3) {}
                return extra;
            }

            function saveStore(arr) {
                try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {}
            }

            function paint() {
                var root = document.getElementById('sopSrRoot');
                if (!root) return;
                var inmFilter = root.getAttribute('data-sop-inm') || '';
                var all = getStore();

                var pending = all.filter(function (t) {
                    if (t.estado !== 'sin_responder') return false;
                    if (inmFilter && t.inmuebleId && String(t.inmuebleId) !== String(inmFilter)) return false;
                    if (qStr) {
                        var blob = (t.id + ' ' + t.usuario + ' ' + t.reserva + ' ' + t.inmueble + ' ' + t.asunto).toLowerCase();
                        if (blob.indexOf(qStr.toLowerCase()) === -1) return false;
                    }
                    return true;
                });

                var cntBadge = root.querySelector('.js-sop-sr-count');
                if (cntBadge) cntBadge.textContent = pending.length + ' sin responder';

                if (!selId && pending.length) selId = pending[0].id;
                else if (selId && !pending.some(function (t) { return t.id === selId; })) selId = pending.length ? pending[0].id : null;

                var listEl = root.querySelector('.js-sop-sr-list');
                var threadEl = root.querySelector('.js-sop-sr-thread');
                if (!listEl || !threadEl) return;

                if (!pending.length) {
                    listEl.innerHTML = '<div class="p-4 text-center text-muted" style="font-size:12px;"><i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>No hay mensajes sin responder en este alcance.</div>';
                    threadEl.innerHTML = '<div class="p-4 text-center text-muted m-auto" style="font-size:12px;">Sin mensajes pendientes por responder.</div>';
                    return;
                }

                listEl.innerHTML = pending.map(function (t) {
                    var isAct = t.id === selId;
                    return '<div class="sop-n3-item ' + (isAct ? 'active' : '') + '" data-sr-id="' + t.id + '">' +
                        '<div class="d-flex justify-content-between align-items-center gap-1">' +
                            '<strong style="font-size:12px; color:#0f172a;">' + t.usuario + '</strong>' +
                            '<span class="text-danger fw-bold" style="font-size:10px;">' + t.actualizado + '</span>' +
                        '</div>' +
                        '<div style="font-size:11.5px; color:#334155;" class="text-truncate mt-0.5">' + t.asunto + '</div>' +
                        '<div class="text-muted mt-0.5" style="font-size:10.5px;">' + t.id + ' · ' + t.reserva + ' · ' + t.inmueble + '</div>' +
                    '</div>';
                }).join('');

                var cur = pending.find(function (t) { return t.id === selId; }) || pending[0];
                var msgsHtml = (cur.mensajes || []).map(function (m) {
                    var cls = m.rol === 'Soporte' ? 'sop-n3-bubble-a' : 'sop-n3-bubble-u';
                    return '<div class="' + cls + '">' +
                        '<div class="d-flex justify-content-between mb-1" style="font-size:10.5px;"><strong>' + m.autor + '</strong><span class="text-muted">' + m.hora + '</span></div>' +
                        '<div>' + m.texto + '</div>' +
                    '</div>';
                }).join('');

                threadEl.innerHTML =
                    '<div class="border-bottom pb-2 mb-2">' +
                        '<div class="fw-bold" style="font-size:13px; color:#0f172a;">' + cur.usuario + ' <span class="text-muted fw-normal" style="font-size:11px;">(' + cur.documento + ')</span></div>' +
                        '<div class="text-muted" style="font-size:11px;">' + cur.asunto + ' · <b>' + cur.inmueble + '</b> · ' + cur.reserva + '</div>' +
                    '</div>' +
                    '<div class="sop-n3-msgs">' + msgsHtml + '</div>' +
                    '<div class="d-flex flex-wrap gap-1 mb-2">' +
                        '<button type="button" class="sop-n3-chip js-sr-tpl" data-tpl="Hola, hemos recibido tu mensaje en el portal y tu solicitud quedó atendida.">+ Solicitud atendida</button>' +
                        '<button type="button" class="sop-n3-chip js-sr-tpl" data-tpl="Hola, ya validamos tu soporte en el sistema y tu reserva continúa activa.">+ Soporte validado</button>' +
                    '</div>' +
                    '<form class="js-sr-reply mt-auto" data-sr-id="' + cur.id + '" data-es-db="' + (cur.esDb ? '1' : '0') + '">' +
                        '<textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Escribe aquí la respuesta directa en la plataforma..." required></textarea>' +
                        '<button type="submit" class="btn btn-sm btn-success fw-bold px-3 sop-n3-btn-send"><i class="bi bi-send me-1"></i>Enviar respuesta y marcar respondido</button>' +
                    '</form>';
            }

            document.addEventListener('DOMContentLoaded', paint);

            document.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.closest) return;
                var item = t.closest('#sopSrRoot .sop-n3-item');
                if (item) {
                    selId = item.getAttribute('data-sr-id');
                    paint();
                    return;
                }
                var tpl = t.closest('#sopSrRoot .js-sr-tpl');
                if (tpl) {
                    var ta = document.querySelector('#sopSrRoot .js-sr-reply textarea');
                    if (ta) {
                        ta.value = ta.value ? (ta.value.trim() + ' ' + tpl.getAttribute('data-tpl')) : tpl.getAttribute('data-tpl');
                        ta.focus();
                    }
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('js-sop-sr-q')) {
                    qStr = e.target.value || '';
                    paint();
                }
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.classList || !form.classList.contains('js-sr-reply')) return;
                e.preventDefault();
                var root = document.getElementById('sopSrRoot');
                var tid = form.getAttribute('data-sr-id');
                var esDb = form.getAttribute('data-es-db') === '1';
                var ta = form.querySelector('textarea');
                var txt = ta ? ta.value.trim() : '';
                if (!txt) return;

                var now = new Date();
                var hh = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);
                var all = getStore();
                all.forEach(function (item) {
                    if (item.id === tid) {
                        item.mensajes = item.mensajes || [];
                        item.mensajes.push({ autor: 'Administración RSV', rol: 'Soporte', hora: hh, texto: txt });
                        item.estado = 'respondido';
                        item.actualizado = 'Hoy · ' + hh;
                    }
                });
                saveStore(all);
                paint();

                var tplUrl = root ? (root.getAttribute('data-responder-tpl') || '') : '';
                if (esDb && tplUrl && tid) {
                    var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch(tplUrl.replace('__ID__', encodeURIComponent(tid)), {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ respuesta: txt })
                    }).catch(function () {});
                }
            });
        })();
    </script>
</div>
