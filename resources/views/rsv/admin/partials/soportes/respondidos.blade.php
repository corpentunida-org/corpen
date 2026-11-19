{{-- resources/views/rsv/admin/partials/soportes/respondidos.blade.php · SOP-RS-v1.1-DBG · NIVEL 3 --}}
@php
    $sopRsInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopRsInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopRsInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopRsInmId = (int) request('inmueble_id');
    }
@endphp

<div id="sopRsRoot" class="sop-n3-root" data-sop-view="respondidos" data-sop-inm="{{ $sopRsInmId ?? '' }}">
    <div class="sop-n3-card">
        <div class="sop-n3-head">
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle me-1" style="font-size:10px;">RESPONDIDOS</span>
                <strong style="font-size:13px; color:#334155;">Historial de Mensajes Ya Respondidos</strong>
                <div class="text-muted" style="font-size:11px;">Consulta respuestas enviadas o reabre un hilo si requiere seguimiento</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-success text-white js-sop-rs-count">0 respondidos</span>
                <input type="search" class="sop-n3-search js-sop-rs-q" placeholder="Buscar persona, reserva, asunto…">
            </div>
        </div>

        <div class="row g-0">
            <div class="col-12 col-md-5 border-end">
                <div class="sop-n3-list js-sop-rs-list"></div>
            </div>
            <div class="col-12 col-md-7">
                <div class="sop-n3-thread js-sop-rs-thread"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            'use strict';
            var KEY = 'rsv_admin_soportes_v1';
            var selId = null;
            var qStr = '';

            function readStore() {
                try {
                    var raw = localStorage.getItem(KEY);
                    return raw ? (JSON.parse(raw) || []) : [];
                } catch (e) { return []; }
            }

            function writeStore(arr) {
                try { localStorage.setItem(KEY, JSON.stringify(arr)); } catch (e) {}
            }

            function paint() {
                var root = document.getElementById('sopRsRoot');
                if (!root) return;
                var inmFilter = root.getAttribute('data-sop-inm') || '';
                var all = readStore();

                var done = all.filter(function (t) {
                    if (t.estado !== 'respondido') return false;
                    if (inmFilter && t.inmuebleId && String(t.inmuebleId) !== String(inmFilter)) return false;
                    if (qStr) {
                        var blob = (t.id + ' ' + t.usuario + ' ' + t.reserva + ' ' + t.inmueble + ' ' + t.asunto).toLowerCase();
                        if (blob.indexOf(qStr.toLowerCase()) === -1) return false;
                    }
                    return true;
                });

                var cntEl = root.querySelector('.js-sop-rs-count');
                if (cntEl) cntEl.textContent = done.length + ' respondidos';

                if (!selId && done.length) selId = done[0].id;
                else if (selId && !done.some(function (t) { return t.id === selId; })) selId = done.length ? done[0].id : null;

                var listEl = root.querySelector('.js-sop-rs-list');
                var threadEl = root.querySelector('.js-sop-rs-thread');
                if (!listEl || !threadEl) return;

                if (!done.length) {
                    listEl.innerHTML = '<div class="p-4 text-center text-muted" style="font-size:12px;">No hay mensajes respondidos en este filtro.</div>';
                    threadEl.innerHTML = '<div class="p-4 text-center text-muted m-auto" style="font-size:12px;">Selecciona un registro respondido para ver la conversación.</div>';
                    return;
                }

                listEl.innerHTML = done.map(function (t) {
                    var isAct = t.id === selId;
                    return '<div class="sop-n3-item ' + (isAct ? 'active' : '') + '" data-rs-id="' + t.id + '">' +
                        '<div class="d-flex justify-content-between align-items-center gap-1">' +
                            '<strong style="font-size:12px; color:#0f172a;">' + t.usuario + '</strong>' +
                            '<span class="text-success fw-semibold" style="font-size:10px;">' + t.actualizado + '</span>' +
                        '</div>' +
                        '<div style="font-size:11.5px; color:#334155;" class="text-truncate mt-0.5">' + t.asunto + '</div>' +
                        '<div class="text-muted mt-0.5" style="font-size:10.5px;">' + t.id + ' · ' + t.reserva + ' · ' + t.inmueble + '</div>' +
                    '</div>';
                }).join('');

                var cur = done.find(function (t) { return t.id === selId; }) || done[0];
                var msgsHtml = (cur.mensajes || []).map(function (m) {
                    var cls = m.rol === 'Soporte' ? 'sop-n3-bubble-a' : 'sop-n3-bubble-u';
                    return '<div class="' + cls + '">' +
                        '<div class="d-flex justify-content-between mb-1" style="font-size:10.5px;"><strong>' + m.autor + '</strong><span class="text-muted">' + m.hora + '</span></div>' +
                        '<div>' + m.texto + '</div>' +
                    '</div>';
                }).join('');

                threadEl.innerHTML =
                    '<div class="border-bottom pb-2 mb-2 d-flex justify-content-between align-items-start gap-2 flex-wrap">' +
                        '<div>' +
                            '<div class="fw-bold" style="font-size:13px; color:#0f172a;">' + cur.usuario + '</div>' +
                            '<div class="text-muted" style="font-size:11px;">' + cur.asunto + ' · <b>' + cur.inmueble + '</b> · ' + cur.reserva + '</div>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-warning py-0 px-2 js-rs-reopen" data-rs-id="' + cur.id + '" style="font-size:11px;">Mover a Sin responder</button>' +
                    '</div>' +
                    '<div class="sop-n3-msgs">' + msgsHtml + '</div>' +
                    '<form class="js-rs-followup mt-auto" data-rs-id="' + cur.id + '">' +
                        '<textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Agregar seguimiento adicional en este hilo..." required></textarea>' +
                        '<button type="submit" class="btn btn-sm btn-outline-success fw-bold px-3 sop-n3-btn-send"><i class="bi bi-reply me-1"></i>Agregar mensaje al hilo</button>' +
                    '</form>';
            }

            document.addEventListener('DOMContentLoaded', paint);

            document.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.closest) return;
                var item = t.closest('#sopRsRoot .sop-n3-item');
                if (item) {
                    selId = item.getAttribute('data-rs-id');
                    paint();
                    return;
                }
                var reopen = t.closest('#sopRsRoot .js-rs-reopen');
                if (reopen) {
                    var idR = reopen.getAttribute('data-rs-id');
                    var all = readStore();
                    all.forEach(function (it) {
                        if (it.id === idR) {
                            it.estado = 'sin_responder';
                            it.actualizado = 'Reabierto ahora';
                        }
                    });
                    writeStore(all);
                    paint();
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('js-sop-rs-q')) {
                    qStr = e.target.value || '';
                    paint();
                }
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.classList || !form.classList.contains('js-rs-followup')) return;
                e.preventDefault();
                var tid = form.getAttribute('data-rs-id');
                var ta = form.querySelector('textarea');
                var txt = ta ? ta.value.trim() : '';
                if (!txt) return;
                var now = new Date();
                var hh = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);
                var all = readStore();
                all.forEach(function (it) {
                    if (it.id === tid) {
                        it.mensajes = it.mensajes || [];
                        it.mensajes.push({ autor: 'Administración RSV', rol: 'Soporte', hora: hh, texto: txt });
                        it.actualizado = 'Hoy · ' + hh;
                    }
                });
                writeStore(all);
                paint();
            });
        })();
    </script>
</div>
