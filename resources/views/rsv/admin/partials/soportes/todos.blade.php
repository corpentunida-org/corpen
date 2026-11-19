{{-- resources/views/rsv/admin/partials/soportes/todos.blade.php · SOP-TD-v1.1-DBG · NIVEL 3 --}}
@php
    $sopTdInmId = null;
    if (isset($inmueble) && $inmueble) {
        $sopTdInmId = is_object($inmueble) ? ($inmueble->id ?? null) : $inmueble;
    } elseif (!empty($inmuebleId) && $inmuebleId !== 'todos' && is_numeric($inmuebleId)) {
        $sopTdInmId = (int) $inmuebleId;
    } elseif (request('inmueble_id') && request('inmueble_id') !== 'todos' && is_numeric(request('inmueble_id'))) {
        $sopTdInmId = (int) request('inmueble_id');
    }
@endphp

<div id="sopTdRoot" class="sop-n3-root" data-sop-view="todos" data-sop-inm="{{ $sopTdInmId ?? '' }}">
    <div class="sop-n3-card">
        <div class="sop-n3-head">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size:10px;">TODOS ESCRITOS</span>
                <strong style="font-size:13px; color:#334155;">Bandeja Completa de Mensajes Escritos</strong>
                <div class="text-muted" style="font-size:11px;">Quiénes han escrito, estado de respuesta y contestación directa aquí mismo</div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-dark js-td-f" data-f="todos" style="font-size:11px;">Todos</button>
                    <button type="button" class="btn btn-outline-danger js-td-f" data-f="sin_responder" style="font-size:11px;">Sin responder</button>
                    <button type="button" class="btn btn-outline-success js-td-f" data-f="respondidos" style="font-size:11px;">Respondidos</button>
                </div>
                <input type="search" class="sop-n3-search js-sop-td-q" placeholder="Buscar persona, código, asunto…">
            </div>
        </div>

        <div class="row g-0">
            <div class="col-12 col-md-5 border-end">
                <div class="sop-n3-list js-sop-td-list"></div>
            </div>
            <div class="col-12 col-md-7">
                <div class="sop-n3-thread js-sop-td-thread"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            'use strict';
            var KEY = 'rsv_admin_soportes_v1';
            var filtro = 'todos';
            var qStr = '';
            var selId = null;

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
                var root = document.getElementById('sopTdRoot');
                if (!root) return;
                var inmFilter = root.getAttribute('data-sop-inm') || '';
                var all = readStore();

                var list = all.filter(function (t) {
                    if (inmFilter && t.inmuebleId && String(t.inmuebleId) !== String(inmFilter)) return false;
                    if (filtro !== 'todos' && t.estado !== filtro) return false;
                    if (qStr) {
                        var blob = (t.id + ' ' + t.usuario + ' ' + t.reserva + ' ' + t.inmueble + ' ' + t.asunto).toLowerCase();
                        if (blob.indexOf(qStr.toLowerCase()) === -1) return false;
                    }
                    return true;
                });

                if (!selId && list.length) selId = list[0].id;
                else if (selId && !list.some(function (t) { return t.id === selId; })) selId = list.length ? list[0].id : null;

                var listEl = root.querySelector('.js-sop-td-list');
                var threadEl = root.querySelector('.js-sop-td-thread');
                if (!listEl || !threadEl) return;

                if (!list.length) {
                    listEl.innerHTML = '<div class="p-4 text-center text-muted" style="font-size:12px;">No hay mensajes que coincidan con el filtro.</div>';
                    threadEl.innerHTML = '<div class="p-4 text-center text-muted m-auto" style="font-size:12px;">Sin conversación seleccionada.</div>';
                    return;
                }

                listEl.innerHTML = list.map(function (t) {
                    var isAct = t.id === selId;
                    var badge = t.estado === 'sin_responder'
                        ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:10px;">Sin responder</span>'
                        : '<span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Respondido</span>';
                    return '<div class="sop-n3-item ' + (isAct ? 'active' : '') + '" data-td-id="' + t.id + '">' +
                        '<div class="d-flex justify-content-between align-items-center gap-1">' +
                            '<strong style="font-size:12px; color:#0f172a;">' + t.usuario + '</strong>' +
                            badge +
                        '</div>' +
                        '<div style="font-size:11.5px; color:#334155;" class="text-truncate mt-0.5">' + t.asunto + '</div>' +
                        '<div class="text-muted mt-0.5" style="font-size:10.5px;">' + t.id + ' · ' + t.reserva + ' · ' + t.inmueble + ' · ' + t.actualizado + '</div>' +
                    '</div>';
                }).join('');

                var cur = list.find(function (t) { return t.id === selId; }) || list[0];
                var msgsHtml = (cur.mensajes || []).map(function (m) {
                    var cls = m.rol === 'Soporte' ? 'sop-n3-bubble-a' : 'sop-n3-bubble-u';
                    return '<div class="' + cls + '">' +
                        '<div class="d-flex justify-content-between mb-1" style="font-size:10.5px;"><strong>' + m.autor + '</strong><span class="text-muted">' + m.hora + '</span></div>' +
                        '<div>' + m.texto + '</div>' +
                    '</div>';
                }).join('');

                threadEl.innerHTML =
                    '<div class="border-bottom pb-2 mb-2">' +
                        '<div class="fw-bold" style="font-size:13px; color:#0f172a;">' + cur.usuario + '</div>' +
                        '<div class="text-muted" style="font-size:11px;">' + cur.asunto + ' · <b>' + cur.inmueble + '</b> · ' + cur.reserva + '</div>' +
                    '</div>' +
                    '<div class="sop-n3-msgs">' + msgsHtml + '</div>' +
                    '<form class="js-td-reply mt-auto" data-td-id="' + cur.id + '">' +
                        '<textarea class="form-control form-control-sm mb-2" rows="2" placeholder="Responder en este hilo..." required></textarea>' +
                        '<button type="submit" class="btn btn-sm btn-success fw-bold px-3 sop-n3-btn-send"><i class="bi bi-send me-1"></i>Enviar respuesta</button>' +
                    '</form>';
            }

            document.addEventListener('DOMContentLoaded', paint);

            document.addEventListener('click', function (e) {
                var t = e.target;
                if (!t || !t.closest) return;

                var fBtn = t.closest('#sopTdRoot .js-td-f');
                if (fBtn) {
                    filtro = fBtn.getAttribute('data-f') || 'todos';
                    document.querySelectorAll('#sopTdRoot .js-td-f').forEach(function (b) {
                        var isCur = b.getAttribute('data-f') === filtro;
                        b.classList.toggle('btn-dark', isCur && filtro === 'todos');
                        b.classList.toggle('btn-outline-secondary', !isCur && b.getAttribute('data-f') === 'todos');
                        b.classList.toggle('btn-danger', isCur && filtro === 'sin_responder');
                        b.classList.toggle('btn-outline-danger', !isCur && b.getAttribute('data-f') === 'sin_responder');
                        b.classList.toggle('btn-success', isCur && filtro === 'respondidos');
                        b.classList.toggle('btn-outline-success', !isCur && b.getAttribute('data-f') === 'respondidos');
                    });
                    paint();
                    return;
                }

                var item = t.closest('#sopTdRoot .sop-n3-item');
                if (item) {
                    selId = item.getAttribute('data-td-id');
                    paint();
                }
            });

            document.addEventListener('input', function (e) {
                if (e.target && e.target.classList && e.target.classList.contains('js-sop-td-q')) {
                    qStr = e.target.value || '';
                    paint();
                }
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.classList || !form.classList.contains('js-td-reply')) return;
                e.preventDefault();
                var tid = form.getAttribute('data-td-id');
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
                        it.estado = 'respondido';
                        it.actualizado = 'Hoy · ' + hh;
                    }
                });
                writeStore(all);
                paint();
            });
        })();
    </script>
</div>
