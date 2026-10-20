{{-- partials/pagos/transacciones.blade.php · Cliente · Mis pagos (lectura + registrar pago AJAX) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliPagosRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="pagos-transacciones"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-4); }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .btn-mini { border:1px solid var(--grid-2); background:#fff; color:var(--ink-3); font-size:8.5px; padding:1px 8px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:3px; }
    .reservas-soft .btn-mini:hover { color:var(--ink-2); background:#fafcfe; text-decoration:none; }
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:8.5px; font-weight:600; padding:2px 10px; border-radius:3px; cursor:pointer; }
    .reservas-soft .pill { display:inline-flex; padding:1px 8px; border-radius:10px; font-size:7.5px; font-weight:700; border:1px solid; line-height:13px; text-transform:uppercase; }
    .reservas-soft .pill-success { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; }
    .reservas-soft .pill-warning { color:#a3822f; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .pill-danger  { color:#b05c5c; background:#fdf1f1; border-color:#f3d8d8; }
    .reservas-soft .pill-secondary { color:#8a97a5; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
    .rsvshow .tx-anulada .tx-monto { text-decoration:line-through; color:var(--ink-4); }
    .reservas-soft-modal .modal-content { border:1px solid #e9eef4; border-radius:5px; font-size:9.5px; color:#64748b; }
    .reservas-soft-modal .form-label { font-size:7.5px; font-weight:600; text-transform:uppercase; color:#94a3b8; margin-bottom:2px; }
    .rsv-toast { position:fixed; bottom:14px; right:14px; z-index:1090; display:none; font-size:9px; font-weight:600; padding:7px 12px; border-radius:5px; box-shadow:0 4px 14px rgba(0,0,0,.12); border:1px solid; max-width:320px; }
    .rsv-toast.ok { background:#f0f7f2; color:#2e7d4f; border-color:#d5e7dc; }
    .rsv-toast.err { background:#fdf5f4; color:#b05c5c; border-color:#f0dcd8; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $txs = collect(); $misReservas = collect();
    try {
        $misReservas = \App\Models\Rsv\Reserva::with('inmueble')->where('id_user', $uid)->orderByDesc('created_at')->limit(10)->get();
        $misIds = $misReservas->pluck('id');
        if ($misIds->isNotEmpty()) {
            $txs = \App\Models\Rsv\TransaccionFinanciera::with('pasarela')
                ->whereIn('id_rsv_reservas', $misIds)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.pagos: '.$e->getMessage()); }
    $rutaPago = \Illuminate\Support\Facades\Route::has('rsv.transacciones.store') ? route('rsv.transacciones.store') : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:6px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-receipt me-1" style="font-size:8px; color:#5d8a70;"></i> Mis Pagos y Comprobantes
    </h6>
    @if($rutaPago && $misReservas->isNotEmpty())
        <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#cliModalPago" title="Registrar un pago con soporte">
            <i class="bi bi-plus-circle"></i> Registrar Pago
        </button>
    @endif
</div>

@forelse($txs as $tx)
    @php
        $edo = $tx->estado_pago ?? 'Pendiente';
        $edoL = mb_strtolower((string)$edo);
        $anul = str_contains($edoL, 'anulad');
        $pill = $anul ? 'secondary' : ((str_contains($edoL,'complet')||str_contains($edoL,'aprob')) ? 'success' : (str_contains($edoL,'pendien') ? 'warning' : 'danger'));
        $reserva = $misReservas->firstWhere('id', $tx->id_rsv_reservas);
        $soporte = $tx->url_soporte_pago ?? $tx->soporte_pago ?? null;
    @endphp
    <div class="list-item-app d-flex justify-content-between align-items-start flex-wrap {{ $anul ? 'tx-anulada' : '' }}" style="padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; gap:6px;">
        <div style="min-width:0;">
            <div class="d-flex align-items-center flex-wrap" style="gap:5px;">
                <span class="val tx-monto">${{ number_format((float)($tx->monto ?? 0), 2) }} <span class="sub">{{ $tx->moneda ?? 'COP' }}</span></span>
                <span class="pill pill-{{ $pill }}">{{ $edo }}</span>
            </div>
            <span class="sub d-block mt-1">
                #{{ optional($reserva)->codigo_reserva ?? ('Reserva '.$tx->id_rsv_reservas) }}
                · {{ optional($tx->pasarela)->nombre ?? optional($tx->pasarela)->name ?? 'Directo' }}
                @if($tx->referencia_externa) · Ref: {{ $tx->referencia_externa }}@endif
            </span>
        </div>
        <div class="d-flex align-items-center" style="gap:6px;">
            <span class="sub">{{ optional($tx->created_at)->format('d/m/y H:i') }}</span>
            @if($soporte && $soporte !== '#')
                <a href="{{ $soporte }}" target="_blank" class="btn-mini" title="Ver comprobante">
                    <i class="bi bi-file-earmark-pdf" style="color:#c0392b;"></i> Soporte
                </a>
            @endif
            @if(\Illuminate\Support\Facades\Route::has('rsv.reservas.pdf'))
                <a href="{{ route('rsv.reservas.pdf', $tx->id_rsv_reservas) }}" target="_blank" class="btn-mini" title="Comprobante de la reserva">
                    <i class="bi bi-download"></i> PDF
                </a>
            @endif
        </div>
    </div>
@empty
    <div class="empty-friendly">
        <i class="bi bi-receipt d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
        <span class="sub">Aún no tienes pagos registrados.</span>
    </div>
@endforelse

@if($rutaPago && $misReservas->isNotEmpty())
<div class="modal fade reservas-soft-modal" id="cliModalPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:9px 14px;">
                <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Registrar Pago</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
            </div>
            <form id="cliPagoForm" data-send="{{ $rutaPago }}">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2"><label class="form-label">Reserva *</label>
                        <select class="form-select form-select-sm" name="id_rsv_reservas" required>
                            @foreach($misReservas as $r)
                                <option value="{{ $r->id }}">#{{ $r->codigo_reserva }} · {{ optional($r->inmueble)->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-7"><label class="form-label">Monto *</label>
                            <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="monto" required></div>
                        <div class="col-5"><label class="form-label">Moneda</label>
                            <input type="text" class="form-control form-control-sm" name="moneda" value="COP" maxlength="4"></div>
                    </div>
                    <div class="mb-2"><label class="form-label">Método</label>
                        <input type="text" class="form-control form-control-sm" name="metodo_pago" placeholder="Transferencia, Efectivo…"></div>
                    <div class="mb-2"><label class="form-label">Referencia</label>
                        <input type="text" class="form-control form-control-sm" name="referencia_externa"></div>
                    <div class="mb-0"><label class="form-label">Soporte (imagen/PDF)</label>
                        <input type="file" class="form-control form-control-sm" name="soporte_pago" accept="image/*,application/pdf"></div>
                    <div class="mt-2 sub" style="font-size:7.5px;">
                        <i class="bi bi-info-circle me-1"></i> Canal "Directo" se registra automáticamente · tu pago queda Pendiente hasta ser verificado.
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

</div>

<div id="rsvToast" class="rsv-toast"></div>
<script>
(function () {
    var root = document.getElementById('cliPagosRoot');
    if (!root) return;
    var FRAG = root.getAttribute('data-frag') || '';
    var KEY  = root.getAttribute('data-fragkey') || '';
    var CSRF = '{{ csrf_token() }}';

    var toastEl = document.getElementById('rsvToast');
    function toast(m, e) {
        if (!toastEl) return;
        toastEl.className = 'rsv-toast ' + (e ? 'err' : 'ok');
        toastEl.innerHTML = (e ? '<i class="bi bi-exclamation-circle me-1"></i>' : '<i class="bi bi-check-circle me-1"></i>') + m;
        toastEl.style.display = 'block';
        setTimeout(function(){ toastEl.style.display = 'none'; }, 4000);
    }

    function swapFrag() {
        if (!FRAG) { location.reload(); return; }
        fetch(FRAG + '?tab=' + KEY, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nr = doc.getElementById(root.id);
                if (!nr) return;
                try {
                    document.querySelectorAll('.modal.show').forEach(function (m) {
                        var i = bootstrap.Modal.getInstance(m); if (i) i.hide();
                    });
                    document.querySelectorAll('.modal-backdrop').forEach(function (b) { b.remove(); });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                } catch (e) {}
                root.replaceWith(document.importNode(nr, true));
            });
    }

    var form = document.getElementById('cliPagoForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            var fd = new FormData(form);
            fd.append('_token', CSRF);
            fd.append('estado_pago', 'Pendiente');
            fetch(form.getAttribute('data-send'), {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
            .then(function (resp) {
                if (resp.s === 201 || resp.s === 200) { toast('Pago enviado. Queda pendiente de verificación.', false); swapFrag(); }
                else {
                    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Enviar';
                    toast(resp.j.message || 'No se pudo registrar.', true);
                }
            })
            .catch(function () { btn.disabled = false; toast('Error de conexión.', true); });
        });
    }
})();
</script>
