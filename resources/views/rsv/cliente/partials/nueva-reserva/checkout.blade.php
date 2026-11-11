{{-- partials/nueva-reserva/checkout.blade.php · Cliente · Carrito de Compras --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliCheckoutRoot" 
     data-redirect="{{ request()->fullUrlWithoutQuery(['inmueble_id', 'fi', 'ff']) }}"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="nueva-checkout"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .strong { color:var(--ink-2); font-weight:600; }
    
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:8.5px; font-weight:600; padding:4px 12px; border-radius:4px; cursor:pointer; transition: all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px; }
    .reservas-soft .btn-green:hover:not(:disabled) { background:#e3f1e9; color:#34684e; }
    .reservas-soft .btn-green:disabled { opacity: 0.6; cursor: not-allowed; }
    
    .reservas-soft .btn-outline { background:#fff; color:var(--ink-3); border:1px solid var(--grid-2); font-size:8.5px; font-weight:600; padding:4px 12px; border-radius:4px; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; justify-content:center; gap:4px; }
    .reservas-soft .btn-outline:hover { background:var(--head); color:var(--ink-2); border-color:var(--ink-4); }

    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; margin-bottom: 8px;}
    
    .reservas-soft .resume-box { background: var(--head); border: 1px solid var(--grid-2); border-radius: 6px; padding: 12px; }
    .reservas-soft .resume-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 8.5px; }
    .reservas-soft .resume-row:last-child { margin-bottom: 0; }
    .reservas-soft .resume-lbl { color: var(--ink-3); font-weight: 500; }
    .reservas-soft .resume-val { color: var(--ink-2); font-weight: 600; text-align: right; }
    .reservas-soft .resume-divider { border-top: 1px dashed var(--grid-2); margin: 8px 0; }
    .reservas-soft .resume-total { font-size: 11px; color: var(--accent); font-weight: 700; }
    
    .reservas-soft .form-check-input:checked { background-color: var(--accent); border-color: var(--accent); }
    .reservas-soft .terms-box { background: #fff; border: 1px solid var(--grid); border-radius: 4px; padding: 10px; margin-bottom: 12px; transition: border-color 0.2s;}
    .reservas-soft .terms-box:hover { border-color: var(--accent-bd); background: #fafcfe; }
</style>

@php
    $inmId = request('inmueble_id');
    $inm = is_numeric($inmId) ? \App\Models\Rsv\CatalogoInmueble::with('latestTarifa')->find($inmId) : null;
    $tarifa = optional($inm?->latestTarifa)->precio_noche;
    $fi = request('fi') ? \Carbon\Carbon::parse(request('fi'))->startOfDay() : null;
    $ff = request('ff') ? \Carbon\Carbon::parse(request('ff'))->startOfDay() : null;
    $noches = ($fi && $ff && $ff->gt($fi)) ? (int) $fi->diffInDays($ff) : 0;
    
    $subtotal = ($tarifa && $noches > 0) ? $tarifa * $noches : 0;
    $impuestos = $subtotal * 0.19; 
    $total = $subtotal + $impuestos;

    $rutaStore = \Illuminate\Support\Facades\Route::has('rsv.reservas.store-ajax') 
        ? route('rsv.reservas.store-ajax') 
        : (\Illuminate\Support\Facades\Route::has('rsv.reservas.store') ? route('rsv.reservas.store') : '');

    $statusPendienteId = class_exists(\App\Models\Rsv\Status::class) 
        ? (\App\Models\Rsv\Status::whereRaw('LOWER(name) LIKE ?', ['%pendien%'])->first()->id ?? 1) : 1;
    $origenDefectoId = class_exists(\App\Models\Rsv\OrigenReserva::class) 
        ? (\App\Models\Rsv\OrigenReserva::first()->id ?? 1) : 1;
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:6px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-cart-check me-1" style="font-size:8px; color:#5d8a70;"></i> Checkout de Reserva
    </h6>
    <div class="d-flex gap-2">
        {{-- Botón para limpiar los parámetros de la URL y volver a cargar el fragmento vacío --}}
        <button type="button" class="btn-outline" id="btnCancelarCheckout" title="Cancelar y limpiar">
            <i class="bi bi-x-circle"></i> Cancelar
        </button>
        <button type="button" class="btn-green" style="background:#fff; color:var(--ink-3); border-color:var(--grid-2);" onclick="document.querySelector('[data-bs-target=\'#nr-sub-buscador\']').click()">
            <i class="bi bi-arrow-left"></i> Volver al buscador
        </button>
    </div>
</div>

@if(!$inm || !$fi || !$ff)
    <div class="st-warn"><i class="bi bi-info-circle me-1"></i> Selecciona una propiedad y fechas válidas en el buscador para continuar.</div>
@else
    <div class="row g-3 mt-1">
        {{-- COLUMNA IZQUIERDA: RESUMEN --}}
        <div class="col-md-6">
            <div class="resume-box">
                <div class="d-flex align-items-center mb-2" style="gap:6px;">
                    <div style="width:30px; height:24px; background:var(--grid-2); border-radius:3px; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-house-door" style="font-size:10px; color:var(--ink-4);"></i>
                    </div>
                    <div>
                        <div class="val">{{ $inm->name }}</div>
                        <div class="sub">{{ $inm->city ?? 'Ubicación no especificada' }}</div>
                    </div>
                </div>
                
                <div class="resume-divider"></div>
                
                <div class="resume-row">
                    <span class="resume-lbl">Check-in</span>
                    <span class="resume-val">{{ $fi->format('d/m/Y') }}</span>
                </div>
                <div class="resume-row">
                    <span class="resume-lbl">Check-out</span>
                    <span class="resume-val">{{ $ff->format('d/m/Y') }}</span>
                </div>
                <div class="resume-row">
                    <span class="resume-lbl">Duración</span>
                    <span class="resume-val">{{ $noches }} noche(s)</span>
                </div>
                
                <div class="resume-divider"></div>
                
                <div class="resume-row">
                    <span class="resume-lbl">Tarifa Base ({{ $noches }} x ${{ number_format($tarifa, 0) }})</span>
                    <span class="resume-val">${{ number_format($subtotal, 0) }}</span>
                </div>
                <div class="resume-row">
                    <span class="resume-lbl">Impuestos (19%)</span>
                    <span class="resume-val">${{ number_format($impuestos, 0) }}</span>
                </div>
                
                <div class="resume-divider"></div>
                
                <div class="resume-row">
                    <span class="resume-lbl strong" style="color:var(--ink-2);">Total a Pagar</span>
                    <span class="resume-total">${{ number_format($total, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: FORMULARIO --}}
        <div class="col-md-6">
            @if($rutaStore)
                <form id="cliCheckoutForm" data-store="{{ $rutaStore }}" class="d-flex flex-column h-100">
                    @csrf
                    <input type="hidden" name="_ajax" value="1">
                    <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inm->id }}">
                    <input type="hidden" name="id_user" value="{{ \Illuminate\Support\Facades\Auth::id() }}">
                    <input type="hidden" name="fecha_inicio" value="{{ $fi->toDateString() }}">
                    <input type="hidden" name="fecha_fin" value="{{ $ff->toDateString() }}">
                    <input type="hidden" name="monto_total" value="{{ $total }}">
                    <input type="hidden" name="id_rsv_statuses" value="{{ $statusPendienteId }}">
                    <input type="hidden" name="id_rsv_origen_reservas" value="{{ $origenDefectoId }}">
                    <input type="hidden" name="comentario_reserva" value="Reserva solicitada directamente desde el Portal del Cliente.">

                    <div class="sub mb-2">
                        Al confirmar, tu reserva quedará registrada en el sistema. Podrás gestionar el pago posteriormente desde la pestaña correspondiente.
                    </div>
                    
                    <div id="checkoutAlertError" class="st-warn d-none"></div>

                    <div class="terms-box form-check d-flex align-items-center mb-auto" style="gap:6px; margin-left:0; padding-left:10px;">
                        <input class="form-check-input m-0" type="checkbox" id="termsCheck" required style="width:12px; height:12px; cursor:pointer;">
                        <label class="form-check-label sub" for="termsCheck" style="cursor:pointer; margin-top:1px;">
                            He leído y acepto los <a href="#" style="color:var(--accent); font-weight:600; text-decoration:none;">Términos y Condiciones</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn-green w-100 mt-2 py-2" id="btnConfirmarReserva">
                        <i class="bi bi-lock-fill me-1"></i> <span class="btn-txt">Confirmar Reserva</span>
                    </button>
                </form>
            @else
                <div class="st-warn"><i class="bi bi-exclamation-triangle me-1"></i> Sistema no configurado (Ruta de guardado ausente).</div>
            @endif
        </div>
    </div>
@endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var root = document.getElementById('cliCheckoutRoot');
    if (!root) return;

    // --- Funcionalidad Cancelar (Recargar Fragmento AJAX) ---
    var btnCancelar = document.getElementById('btnCancelarCheckout');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function(e) {
            e.preventDefault();
            var FRAG = root.getAttribute('data-frag') || '';
            var KEY  = root.getAttribute('data-fragkey') || '';
            
            if (!FRAG) {
                // Fallback local: Limpiamos la URL y recargamos página completa
                window.location.href = root.getAttribute('data-redirect');
                return;
            }

            // Cambiar URL visible limpiando los parámetros
            var cleanUrl = root.getAttribute('data-redirect');
            window.history.pushState({}, document.title, cleanUrl);

            // Fetch AJAX para re-renderizar solo este partial vacío
            var url = new URL(FRAG, window.location.origin);
            url.searchParams.set('tab', KEY);
            
            // Icono de carga en el botón
            btnCancelar.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:8px; height:8px; border-width:1px;"></span>';

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var nr = doc.getElementById(root.id);
                    if (nr) {
                        root.replaceWith(document.importNode(nr, true));
                        // Redirige la pestaña hacia la vitrina tras cancelar
                        var inicioTab = document.querySelector('[data-bs-target="#nr-sub-inicio"]');
                        if (inicioTab) { inicioTab.click(); }
                    }
                })
                .catch(function () {
                    window.location.href = cleanUrl; // Fallback
                });
        });
    }

    // --- Procesar Reserva (AJAX Submit) ---
    var form = document.getElementById('cliCheckoutForm');
    if (!form) return;
    
    var errorAlert = document.getElementById('checkoutAlertError');
    var CSRF = '{{ csrf_token() }}';

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        var terms = document.getElementById('termsCheck');
        if (!terms.checked) {
            alert('Debes aceptar los términos y condiciones antes de confirmar.');
            return;
        }

        var btn = document.getElementById('btnConfirmarReserva');
        var btnTxt = btn ? btn.querySelector('.btn-txt') : null;
        var originalTxt = btnTxt ? btnTxt.innerHTML : 'Confirmar Reserva';

        if (btn) {
            btn.disabled = true;
            if(btnTxt) btnTxt.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width:8px; height:8px; border-width:1px;"></span> Procesando...';
        }
        
        if(errorAlert) errorAlert.classList.add('d-none');

        var fd = new FormData(form);
        fd.append('_token', CSRF);

        fetch(form.getAttribute('data-store'), {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function (resp) {
            return resp.json().then(function (j) {
                return { ok: resp.ok, status: resp.status, json: j };
            }).catch(function () {
                return { ok: resp.ok, status: resp.status, json: { message: 'Respuesta no válida del servidor (HTTP ' + resp.status + ')' } };
            });
        })
        .then(function(r) {
            var j = r.json || {};

            if (r.ok && (j.ok || j.success || j.reserva)) {
                alert('¡Reserva confirmada con éxito!');
                localStorage.setItem('rsv_cliente_panel_parent', '#cli-reservas');
                window.location.href = root.getAttribute('data-redirect');
                return;
            }

            // Error
            var errorMsg = '<i class="bi bi-exclamation-circle me-1"></i> <b>No se pudo completar la reserva:</b><br>';
            errorMsg += j.message || 'Error del servidor.';
            
            if (j.errors) {
                errorMsg += '<ul style="margin:2px 0 0 0; padding-left:14px;">';
                Object.values(j.errors).forEach(function(errArray) {
                    var msgs = Array.isArray(errArray) ? errArray : [errArray];
                    msgs.forEach(function(m) { errorMsg += '<li>' + m + '</li>'; });
                });
                errorMsg += '</ul>';
            }

            if(errorAlert) {
                errorAlert.innerHTML = errorMsg;
                errorAlert.classList.remove('d-none');
            } else {
                alert('Error:\n' + (j.message || 'Fallo al procesar.'));
            }

            if (btn) { btn.disabled = false; if(btnTxt) btnTxt.innerHTML = originalTxt; }
        })
        .catch(function(err) {
            if(errorAlert) {
                errorAlert.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Fallo de red. Verifica tu conexión a internet.';
                errorAlert.classList.remove('d-none');
            }
            if (btn) { btn.disabled = false; if(btnTxt) btnTxt.innerHTML = originalTxt; }
        });
    });
});
</script>