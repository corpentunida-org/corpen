{{-- ============================================================================
     RSV · ADMIN · CALENDARIO · BLOQUEOS
     Parcial: resources/views/rsv/admin/partials/calendario/bloqueos.blade.php

     Variables (OPCIONALES — si el controlador no las pasa, la parcial se
     autoabastece consultando los modelos):
       - $bloqueos   Colección de bloqueos (idealmente con `inmueble` precargada)
       - $inmuebles  Colección de inmuebles para el select del modal

     Rutas que usa (todas por route(), nunca hardcodeadas):
       - rsv.reservas.fechas-ocupadas   GET  /rsv/reservas/fechas-ocupadas/{inmueble}
                                        → { ok, inmueble:{id,name}, ocupadas:[{inicio,fin,tipo}] }
       - rsv.bloqueos-calendario.store    POST /rsv/bloqueos-calendario
       - rsv.bloqueos-calendario.update   PUT  /rsv/bloqueos-calendario/{id}   (spoof _method)
       - rsv.bloqueos-calendario.destroy  DEL /rsv/bloqueos-calendario/{id}
       - rsv.admin.bloqueos.fragmento     GET  /rsv/bloqueos-fragmento (refresco AJAX)
     ============================================================================ --}}

@php
    /*
     * Autoabastecimiento de datos.
     * Si el controlador ya pasó $bloqueos / $inmuebles (bloqueosFragmento()),
     * se respetan tal cual. Si no llegaron (include del dashboard, fragmento
     * sin datos, etc.), se consultan aquí para que la parcial nunca reviente
     * con count(null).
     */
    if (!isset($bloqueos) || $bloqueos === null) {
        $bloqueos = collect();
        foreach ([
            \App\Models\RsvBloqueoCalendario::class,
            \App\Models\BloqueoCalendario::class,
            \App\Models\RsvBloqueo::class,
            \App\BloqueoCalendario::class,
        ] as $claseBloqueo) {
            if (class_exists($claseBloqueo)) {
                try {
                    $bloqueos = $claseBloqueo::with('inmueble')->orderBy('fecha_inicio')->get();
                } catch (\Throwable $e) {
                    // Si la relación 'inmueble' no existiera, degradamos sin eager loading.
                    $bloqueos = $claseBloqueo::orderBy('fecha_inicio')->get();
                }
                break;
            }
        }
    }

    if (!isset($inmuebles) || $inmuebles === null) {
        $inmuebles = collect();
        foreach ([
            \App\Models\RsvCatalogoInmueble::class,
            \App\Models\CatalogoInmueble::class,
            \App\Models\RsvInmueble::class,
            \App\CatalogoInmueble::class,
        ] as $claseInmueble) {
            if (class_exists($claseInmueble)) {
                try {
                    $inmuebles = $claseInmueble::orderBy('name')->get();
                } catch (\Throwable $e) {
                    // Si la columna se llamara 'nombre' u otra, ordenamos por id.
                    $inmuebles = $claseInmueble::orderBy('id')->get();
                }
                break;
            }
        }
    }
@endphp

<div id="bloqueos-panel" data-url-fragmento="{{ route('rsv.admin.bloqueos.fragmento') }}">

    {{-- ─────────────────────────── TABLA ─────────────────────────── --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
            <div>
                <h5 class="mb-0">Bloqueos del calendario</h5>
                <small class="text-muted">Mantenimiento, overbooking u otros impedimentos de reserva.</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill text-bg-light border">{{ count($bloqueos) }} registro(s)</span>
                <button type="button" class="btn btn-primary btn-sm" id="blqBtnNuevo">Nuevo bloqueo</button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Inmueble</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Días</th>
                            <th>Motivo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bloqueos as $bloqueo)
                            @php
                                $inicio = \Illuminate\Support\Carbon::parse($bloqueo->fecha_inicio);
                                $fin    = \Illuminate\Support\Carbon::parse($bloqueo->fecha_fin);
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $bloqueo->id }}</td>
                                <td class="fw-semibold">{{ $bloqueo->inmueble?->name ?? $bloqueo->inmueble?->nombre ?? '—' }}</td>
                                <td>{{ $inicio->format('d/m/Y') }}</td>
                                <td>{{ $fin->format('d/m/Y') }}</td>
                                <td><span class="badge text-bg-secondary">{{ (int) $inicio->diffInDays($fin) + 1 }}</span></td>
                                <td title="{{ $bloqueo->motivo }}">{{ \Illuminate\Support\Str::limit($bloqueo->motivo, 60) }}</td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary blq-btn-editar"
                                            data-id="{{ $bloqueo->id }}"
                                            data-inmueble="{{ $bloqueo->inmueble_id }}"
                                            data-inicio="{{ $inicio->format('Y-m-d') }}"
                                            data-fin="{{ $fin->format('Y-m-d') }}"
                                            data-motivo="{{ $bloqueo->motivo }}">Editar</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger blq-btn-eliminar"
                                            data-id="{{ $bloqueo->id }}">Eliminar</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No hay bloqueos registrados todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────── MODAL ─────────────────────────── --}}
    <div class="modal fade" id="modalBloqueo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="formBloqueo" novalidate>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="blqModalTitulo">Nuevo bloqueo</h5>
                        <button type="button" class="btn btn-sm btn-light border-0"
                                data-bs-dismiss="modal" data-dismiss="modal"
                                aria-label="Cerrar" style="font-size:1.25rem;line-height:1;">&times;</button>
                    </div>

                    <div class="modal-body">
                        {{-- Banner de errores de validación (422 del Form Request) --}}
                        <div id="blqBannerErrores" class="alert alert-danger d-none"></div>

                        <div class="row g-3">
                            {{-- Columna izquierda: datos --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="blqInmueble">Inmueble <span class="text-danger">*</span></label>
                                    <select id="blqInmueble" name="inmueble_id" class="form-select">
                                        <option value="">— Selecciona un inmueble —</option>
                                        @foreach($inmuebles as $inmueble)
                                            <option value="{{ $inmueble->id }}">
                                                {{ $inmueble->name ?? $inmueble->nombre ?? 'Inmueble #'.$inmueble->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Selecciona un inmueble válido.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="blqMotivo">Motivo <span class="text-danger">*</span></label>
                                    <textarea id="blqMotivo" name="motivo" rows="4" maxlength="500"
                                              class="form-control"
                                              placeholder="Ej.: Mantenimiento de aire acondicionado, overbooking, reforma…"></textarea>
                                    <div class="form-text">Entre 5 y 500 caracteres.</div>
                                    <div class="invalid-feedback">El motivo debe tener entre 5 y 500 caracteres.</div>
                                </div>

                                {{-- Aviso no bloqueante por solape (overbooking intencional) --}}
                                <div id="blqAvisoSolape" class="alert alert-warning py-2 small d-none">
                                    El rango seleccionado se solapa con fechas ya ocupadas.
                                    Puedes continuar (por ejemplo, para un overbooking), pero revisa que sea intencional.
                                </div>
                            </div>

                            {{-- Columna derecha: calendario --}}
                            <div class="col-md-6">
                                <label class="form-label">Fechas del bloqueo <span class="text-danger">*</span></label>
                                <div id="blqRangoTexto" class="blq-rango-texto small text-muted mb-2">Sin fechas seleccionadas</div>

                                {{-- Campos reales que recibe el backend --}}
                                <input type="hidden" name="fecha_inicio" id="blqFechaInicio">
                                <input type="hidden" name="fecha_fin" id="blqFechaFin">

                                {{-- Ancla oculta de Flatpickr + contenedor del mini-calendario --}}
                                <input type="text" id="blqAnclaCal" class="d-none">
                                <div id="blqCalendario"></div>

                                <div id="blqCargando" class="d-none small text-muted mt-1">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Cargando ocupación…
                                </div>
                                <div id="blqInfoOcupacion" class="small text-muted mt-1"></div>

                                <div class="blq-leyenda">
                                    <span><i class="blq-dot blq-dot-reserva"></i> Reserva</span>
                                    <span><i class="blq-dot blq-dot-bloqueo"></i> Otro bloqueo</span>
                                </div>
                                <div class="invalid-feedback" id="blqFeedbackFechas">Selecciona la fecha de inicio y fin en el calendario.</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="blqBtnGuardar">Crear bloqueo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Estilos locales de la parcial --}}
<style>
    #bloqueos-panel .blq-cal .flatpickr-calendar { margin: 0 auto; box-shadow: none; border: 1px solid #e9ecef; border-radius: .5rem; }
    #bloqueos-panel .flatpickr-day.blq-ocp-reserva { background: #f8d7da !important; border-color: #f1aeb5 !important; color: #58151c !important; }
    #bloqueos-panel .flatpickr-day.blq-ocp-bloqueo { background: #fff3cd !important; border-color: #ffe69c !important; color: #664d03 !important; }
    .blq-leyenda { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; font-size: .8rem; color: #6c757d; margin-top: .5rem; }
    .blq-dot { display: inline-block; width: 11px; height: 11px; border-radius: 50%; margin-right: .35rem; vertical-align: middle; }
    .blq-dot-reserva { background: #f8d7da; border: 1px solid #f1aeb5; }
    .blq-dot-bloqueo { background: #fff3cd; border: 1px solid #ffe69c; }
    .blq-rango-texto { min-height: 1.25rem; }
    .blq-toast { position: fixed; right: 1rem; bottom: 1rem; z-index: 3000; min-width: 260px;
                 box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); transition: opacity .3s ease, transform .3s ease; }
    .blq-toast-out { opacity: 0; transform: translateY(.5rem); }
</style>

<script>
(function () {
    'use strict';

    // Evita doble binding de eventos cuando el fragmento se re-inyecta vía AJAX.
    if (window.__blqPanelInit) return;
    window.__blqPanelInit = true;

    /* ── URLs generadas con route() (jamás hardcodeadas) ─────────────────── */
    var URLS = {
        ocupadas:  '{{ route('rsv.reservas.fechas-ocupadas', ':inmuebleId') }}',
        store:     '{{ route('rsv.bloqueos-calendario.store') }}',
        update:    '{{ route('rsv.bloqueos-calendario.update', ':bloqueoId') }}',
        destroy:   '{{ route('rsv.bloqueos-calendario.destroy', ':bloqueoId') }}',
        fragmento: '{{ route('rsv.admin.bloqueos.fragmento') }}'
    };

    var modo = 'crear';        // 'crear' | 'editar'
    var idEditando = null;
    var ocupadas = [];         // [{inicio:'Y-m-d', fin:'Y-m-d', tipo}] del inmueble seleccionado
    var fpCalendario = null;   // instancia Flatpickr viva

    function $(sel) { return document.querySelector(sel); }

    function csrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        if (meta && meta.content) return meta.content.trim();
        var hidden = document.querySelector('#formBloqueo [name="_token"]');
        return hidden ? hidden.value : '';
    }

    /* ── Fechas ──────────────────────────────────────────────────────────── */
    function aYmd(d) {
        return d.getFullYear() + '-' +
            String(d.getMonth() + 1).padStart(2, '0') + '-' +
            String(d.getDate()).padStart(2, '0');
    }
    function formatoCorto(ymd) {
        var p = String(ymd).split('-');
        return p[2] + '/' + p[1] + '/' + p[0];
    }
    function textoRango(ini, fin) {
        return ini === fin ? formatoCorto(ini) : formatoCorto(ini) + ' → ' + formatoCorto(fin);
    }
    function enRango(ymd, r) { return ymd >= r.inicio && ymd <= r.fin; }

    /* ── Carga perezosa de Flatpickr (por si el layout no lo trae) ───────── */
    function cargarCss(href) {
        var link = document.createElement('link');
        link.id = 'blq-fp-css';
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }
    function cargarScript(src, id) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.id = id;
            s.src = src;
            s.onload = resolve;
            s.onerror = function () { reject(new Error('No se pudo cargar ' + src)); };
            document.body.appendChild(s);
        });
    }
    async function asegurarFlatpickr() {
        if (window.flatpickr) return;
        if (!document.getElementById('blq-fp-css')) {
            cargarCss('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
        }
        if (!document.getElementById('blq-fp-js')) {
            await cargarScript('https://cdn.jsdelivr.net/npm/flatpickr', 'blq-fp-js');
        }
        if (!document.getElementById('blq-fp-l10n')) {
            await cargarScript('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', 'blq-fp-l10n');
        }
        if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.es) {
            flatpickr.localize(flatpickr.l10ns.es);
        }
    }

    /* ── Modal (compatible Bootstrap 5 y 4) ──────────────────────────────── */
    function abrirModal(el) {
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery) {
            window.jQuery(el).modal('show');
        } else {
            el.style.display = 'block';
            el.classList.add('show');
        }
    }
    function cerrarModal(el) {
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).hide();
        } else if (window.jQuery) {
            window.jQuery(el).modal('hide');
        } else {
            el.style.display = 'none';
            el.classList.remove('show');
        }
    }

    /* ── Mini-calendario ─────────────────────────────────────────────────── */
    function asegurarCalendario() {
        var cont = $('#blqCalendario');
        if (!cont || !window.flatpickr) return;
        if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) return;
        if (fpCalendario) { try { fpCalendario.destroy(); } catch (e) {} fpCalendario = null; }
        cont.innerHTML = ''; // limpia posible fallback previo

        fpCalendario = flatpickr('#blqAnclaCal', {
            mode: 'range',
            inline: true,
            appendTo: cont,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            showMonths: 1,
            onChange: function (fechas) {
                $('#blqFechaInicio').value = fechas[0] ? aYmd(fechas[0]) : '';
                $('#blqFechaFin').value    = fechas[1] ? aYmd(fechas[1]) : '';
                $('#blqRangoTexto').textContent = fechas[0]
                    ? textoRango(aYmd(fechas[0]), aYmd(fechas[1] || fechas[0]))
                    : 'Sin fechas seleccionadas';
                revisarSolape();
            },
            onDayCreate: function (a, b, c, dayElem) {
                var ymd = aYmd(dayElem.dateObj);
                var r = null;
                for (var i = 0; i < ocupadas.length; i++) {
                    if (enRango(ymd, ocupadas[i])) { r = ocupadas[i]; break; }
                }
                if (!r) return;
                var esBloqueo = String(r.tipo || '').toLowerCase().indexOf('bloque') !== -1;
                dayElem.classList.add(esBloqueo ? 'blq-ocp-bloqueo' : 'blq-ocp-reserva');
            }
        });
    }

    // Fallback: si Flatpickr no pudo cargarse, dos inputs de fecha nativos.
    function calendarioFallback() {
        var cont = $('#blqCalendario');
        if (!cont || document.getElementById('blqFallbackInicio')) return;
        cont.innerHTML =
            '<div class="alert alert-warning small py-2 mb-2">No se pudo cargar el calendario visual. Selecciona las fechas manualmente:</div>' +
            '<div class="d-flex gap-2">' +
            '<input type="date" class="form-control" id="blqFallbackInicio">' +
            '<input type="date" class="form-control" id="blqFallbackFin">' +
            '</div>';
        function sincro() {
            $('#blqFechaInicio').value = $('#blqFallbackInicio').value || '';
            $('#blqFechaFin').value    = $('#blqFallbackFin').value || '';
            $('#blqRangoTexto').textContent = ($('#blqFechaInicio').value && $('#blqFechaFin').value)
                ? textoRango($('#blqFechaInicio').value, $('#blqFechaFin').value)
                : 'Sin fechas seleccionadas';
            revisarSolape();
        }
        cont.querySelector('#blqFallbackInicio').addEventListener('change', sincro);
        cont.querySelector('#blqFallbackFin').addEventListener('change', sincro);
    }

    async function abrirCalendarioListo() {
        if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) return;
        try { await asegurarFlatpickr(); }
        catch (e) { console.warn('Flatpickr no disponible:', e); }
        asegurarCalendario();
        if (!fpCalendario) calendarioFallback();
    }

    function repintarCalendario() {
        if (fpCalendario) { try { fpCalendario.redraw(); } catch (e) {} }
    }

    /* ── Ocupación del inmueble (RUTA CORRECTA: rsv.reservas.fechas-ocupadas) ── */
    async function cargarOcupadas(inmuebleId) {
        var info = $('#blqInfoOcupacion');
        var spinner = $('#blqCargando');
        ocupadas = [];
        repintarCalendario();
        if (info) info.textContent = '';
        if (!inmuebleId) return;

        if (spinner) spinner.classList.remove('d-none');
        try {
            var url = URLS.ocupadas.replace(':inmuebleId', encodeURIComponent(inmuebleId));
            var res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.ok === false) {
                throw new Error(data.message || 'No se pudo consultar la ocupación del inmueble.');
            }
            ocupadas = (data.ocupadas || []).map(function (o) {
                return {
                    inicio: String(o.inicio).slice(0, 10),
                    fin:    String(o.fin).slice(0, 10),
                    tipo:   o.tipo || ''
                };
            });
            var nRes = ocupadas.filter(function (o) { return o.tipo.toLowerCase().indexOf('bloque') === -1; }).length;
            var nBlq = ocupadas.length - nRes;
            var nombre = (data.inmueble && (data.inmueble.name || data.inmueble.nombre)) || 'Inmueble';
            info.textContent = nombre + ': ' + ocupadas.length + ' rango(s) ocupado(s) · ' +
                               nRes + ' por reservas · ' + nBlq + ' por bloqueos';
        } catch (e) {
            info.textContent = 'Aviso: ' + e.message;
        } finally {
            if (spinner) spinner.classList.add('d-none');
            repintarCalendario();
            revisarSolape();
        }
    }

    /* ── Solape (aviso no bloqueante) ────────────────────────────────────── */
    function revisarSolape() {
        var aviso = $('#blqAvisoSolape');
        if (!aviso) return;
        var ini = $('#blqFechaInicio').value;
        var fin = $('#blqFechaFin').value;
        if (!ini || !fin || !ocupadas.length) { aviso.classList.add('d-none'); return; }
        var choca = ocupadas.some(function (o) { return ini <= o.fin && fin >= o.inicio; });
        aviso.classList.toggle('d-none', !choca);
    }

    /* ── Errores de validación (422 del Form Request) ────────────────────── */
    var INPUT_POR_CAMPO = { inmueble_id: '#blqInmueble', motivo: '#blqMotivo' };

    function limpiarErrores() {
        var banner = $('#blqBannerErrores');
        if (banner) { banner.classList.add('d-none'); banner.innerHTML = ''; }
        ['#blqInmueble', '#blqMotivo'].forEach(function (sel) {
            var el = $(sel);
            if (el) el.classList.remove('is-invalid');
        });
    }
    function pintarErrores(data) {
        limpiarErrores();
        var banner = $('#blqBannerErrores');
        var errores = (data && data.errors) ? data.errors : {};
        var lista = [];
        if (Array.isArray(errores)) {
            lista = errores.map(String);
        } else {
            Object.keys(errores).forEach(function (campo) {
                var arr = Array.isArray(errores[campo]) ? errores[campo] : [errores[campo]];
                arr.forEach(function (m) { lista.push(String(m)); });
                var input = $(INPUT_POR_CAMPO[campo] || '');
                if (input) input.classList.add('is-invalid');
            });
        }
        if (!lista.length && data && data.message) lista.push(String(data.message));
        if (!lista.length) lista.push('Revisa los datos del formulario.');
        banner.innerHTML = '<strong>No se pudo guardar:</strong><ul class="mb-0 mt-1">' +
            lista.map(function (m) { return '<li>' + m.replace(/</g, '&lt;') + '</li>'; }).join('') +
            '</ul>';
        banner.classList.remove('d-none');
    }

    /* ── Toast ───────────────────────────────────────────────────────────── */
    function mostrarToast(mensaje, tipo) {
        var t = document.createElement('div');
        t.className = 'alert alert-' + (tipo || 'success') + ' blq-toast';
        t.textContent = mensaje;
        document.body.appendChild(t);
        setTimeout(function () { t.classList.add('blq-toast-out'); }, 3200);
        setTimeout(function () { t.remove(); }, 3600);
    }

    /* ── Refresco AJAX del panel (mismo patrón que reservas-fragmento) ───── */
    async function refrescarPanel() {
        var actual = $('#bloqueos-panel');
        if (!actual) { location.reload(); return; }
        var res = await fetch(URLS.fragmento, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
        });
        if (!res.ok) throw new Error('Fragmento no disponible');
        var html = await res.text();
        var contenedor = document.createElement('div');
        contenedor.innerHTML = html;
        var nuevo = contenedor.querySelector('#bloqueos-panel');
        if (!nuevo) throw new Error('Fragmento inválido');
        actual.replaceWith(nuevo);
    }

    function cerrarYRefrescar() {
        var modalEl = $('#modalBloqueo');
        function alOcultar() {
            refrescarPanel().catch(function () { location.reload(); });
        }
        if (window.bootstrap && window.bootstrap.Modal) {
            modalEl.addEventListener('hidden.bs.modal', alOcultar, { once: true });
            window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        } else if (window.jQuery) {
            window.jQuery(modalEl).one('hidden.bs.modal', alOcultar).modal('hide');
        } else {
            cerrarModal(modalEl);
            alOcultar();
        }
    }

    /* ── Limpieza del formulario ─────────────────────────────────────────── */
    function limpiarFormulario() {
        var form = $('#formBloqueo');
        if (form) form.reset();
        $('#blqFechaInicio').value = '';
        $('#blqFechaFin').value = '';
        $('#blqRangoTexto').textContent = 'Sin fechas seleccionadas';
        $('#blqInmueble').value = '';
        ocupadas = [];
        $('#blqInfoOcupacion').textContent = '';
        $('#blqAvisoSolape').classList.add('d-none');
        limpiarErrores();
        if (fpCalendario && fpCalendario.element && fpCalendario.element.isConnected) {
            fpCalendario.clear();
            try { fpCalendario.set('minDate', 'today'); } catch (e) {}
            repintarCalendario();
        }
    }

    /* ── Eventos delegados en document (sobreviven al refresco del fragmento) ── */

    document.addEventListener('click', async function (e) {
        var btnNuevo    = e.target.closest('#blqBtnNuevo');
        var btnEditar   = e.target.closest('.blq-btn-editar');
        var btnEliminar = e.target.closest('.blq-btn-eliminar');

        if (btnNuevo) {
            modo = 'crear';
            idEditando = null;
            $('#blqModalTitulo').textContent = 'Nuevo bloqueo';
            $('#blqBtnGuardar').textContent = 'Crear bloqueo';
            limpiarFormulario();
            await abrirCalendarioListo();      // el calendario SIEMPRE se dibuja al abrir
            abrirModal($('#modalBloqueo'));
            var sel = $('#blqInmueble');
            if (sel) sel.focus();
            return;
        }

        if (btnEditar) {
            modo = 'editar';
            idEditando = btnEditar.dataset.id;
            $('#blqModalTitulo').textContent = 'Editar bloqueo #' + idEditando;
            $('#blqBtnGuardar').textContent = 'Guardar cambios';
            limpiarFormulario();
            await abrirCalendarioListo();
            abrirModal($('#modalBloqueo'));

            $('#blqInmueble').value = btnEditar.dataset.inmueble || '';
            $('#blqMotivo').value = btnEditar.dataset.motivo || '';
            var inicio = btnEditar.dataset.inicio || '';
            var fin = btnEditar.dataset.fin || '';
            $('#blqFechaInicio').value = inicio;
            $('#blqFechaFin').value = fin;

            if (inicio && fin) {
                if (fpCalendario) {
                    try { fpCalendario.set('minDate', null); } catch (err) {} // permite editar bloqueos ya iniciados
                    fpCalendario.setDate([inicio, fin], false);
                } else {
                    var fi = document.getElementById('blqFallbackInicio');
                    var ff = document.getElementById('blqFallbackFin');
                    if (fi) fi.value = inicio;
                    if (ff) ff.value = fin;
                }
                $('#blqRangoTexto').textContent = textoRango(inicio, fin);
            }
            if ($('#blqInmueble').value) cargarOcupadas($('#blqInmueble').value);
            return;
        }

        if (btnEliminar) {
            if (!window.confirm('¿Eliminar este bloqueo? Esta acción no se puede deshacer.')) return;
            btnEliminar.disabled = true;
            try {
                var url = URLS.destroy.replace(':bloqueoId', encodeURIComponent(btnEliminar.dataset.id));
                var res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                var data = await res.json().catch(function () { return {}; });
                if (!res.ok || data.ok === false) {
                    mostrarToast(data.message || 'No se pudo eliminar el bloqueo.', 'danger');
                    return;
                }
                mostrarToast(data.message || 'Bloqueo eliminado correctamente.', 'success');
                await refrescarPanel();
            } catch (err) {
                mostrarToast('Error de red al eliminar el bloqueo.', 'danger');
            } finally {
                btnEliminar.disabled = false;
            }
            return;
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'blqInmueble') {
            cargarOcupadas(e.target.value);   // ← aquí ocurre la llamada CORRECTA a fechas-ocupadas
        }
    });

    document.addEventListener('submit', async function (e) {
        if (!e.target || e.target.id !== 'formBloqueo') return;
        e.preventDefault();
        limpiarErrores();

        var form = e.target;
        var fd = new FormData(form);
        if (modo === 'editar') fd.append('_method', 'PUT');

        // Validación mínima en cliente (el Form Request valida igual en el backend)
        if (!fd.get('inmueble_id')) {
            pintarErrores({ errors: { inmueble_id: ['Selecciona un inmueble.'] } });
            return;
        }
        if (!fd.get('fecha_inicio') || !fd.get('fecha_fin')) {
            pintarErrores({ errors: { fecha_inicio: ['Selecciona la fecha de inicio y fin en el calendario.'] } });
            return;
        }

        var url = (modo === 'crear')
            ? URLS.store
            : URLS.update.replace(':bloqueoId', encodeURIComponent(idEditando));

        var btn = $('#blqBtnGuardar');
        var textoOriginal = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Guardando…';

        try {
            var res = await fetch(url, {
                method: 'POST', // spoofing con _method para PUT
                headers: {
                    'X-CSRF-TOKEN': csrf(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            });
            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.ok === false) {
                pintarErrores(data);
                return;
            }
            mostrarToast(data.message || 'Bloqueo guardado correctamente.', 'success');
            cerrarYRefrescar();
        } catch (err) {
            pintarErrores({ message: 'Error de red al guardar el bloqueo.' });
        } finally {
            btn.disabled = false;
            btn.textContent = textoOriginal;
        }
    });
})();
</script>
