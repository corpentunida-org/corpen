{{-- partials/finanzas/pasarela.blade.php · diseño reservas-soft v2 --}}
{{-- · CRUD 100% Bootstrap/Blade (funciona incluso sin JS) + AJAX opcional --}}
{{-- · Refresca TODAS las copias de #sheet-pasarelas (parcial incluido 2+ veces) --}}
{{-- · Colores normalizados (evita warnings de <input type="color">)         --}}

<div id="sheet-pasarelas">

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
        </style>

        @php
            $psItems = collect($pasarelas ?? []);

            $psStore = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.store') ? route('rsv.pasarelas.store') : null;
            $psBase  = \Illuminate\Support\Facades\Route::has('rsv.pasarelas.index') ? route('rsv.pasarelas.index') : null;

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

    var URL_FRAGMENTO = '{{ Route::has("rsv.admin.pasarelas.fragmento") ? route("rsv.admin.pasarelas.fragmento") : "" }}';

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
        if (!URL_FRAGMENTO) { window.location.reload(); return; }
        fetch(URL_FRAGMENTO, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, cache: 'no-store' })
            .then(function (r) { if (!r.ok) throw new Error('fragmento'); return r.text(); })
            .then(function (html) {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var nuevo = temp.querySelector('#sheet-pasarelas');
                var copias = document.querySelectorAll('#sheet-pasarelas');
                if (!nuevo || copias.length === 0) { window.location.reload(); return; }
                copias.forEach(function (actual) { actual.replaceWith(nuevo.cloneNode(true)); });
                unicarModales(); aplicarFiltro(); if (idResaltar) resaltarFila(idResaltar); limpiarResiduos();
            })
            .catch(function () { window.location.reload(); });
    }

    function enviarFormulario(form) {
        var btn = form.querySelector('button[type="submit"]');
        var txt = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Procesando…'; }

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
                avisar(res.json.message || 'Guardado correctamente', 'success');
                setTimeout(function () { refrescarTabla(idNuevo); }, 350);
            } else if (res.status === 422 && res.json && res.json.errors) {
                pintarErrores(form, res.json.errors);
            } else {
                errorGeneral(form, (res.json && res.json.message) || 'No se pudo completar la operación.');
            }
        })
        .catch(function () { errorGeneral(form, 'Error de conexión. Intenta de nuevo.'); })
        .finally(function () { if (btn) { btn.disabled = false; btn.innerHTML = txt; } });
    }

    function aplicarFiltro(inp) {
        inp = inp || document.getElementById('psSearch');
        if (!inp) return;
        var val = (inp.value || '').toLowerCase().trim();
        document.querySelectorAll('#sheet-pasarelas tr[data-ps-name]').forEach(function (tr) {
            tr.style.display = (!val || (tr.getAttribute('data-ps-name') || '').indexOf(val) !== -1) ? '' : 'none';
        });
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

    function iniciar() { unicarModales(); limpiarResiduos(); aplicarFiltro(); }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
})();
</script>
