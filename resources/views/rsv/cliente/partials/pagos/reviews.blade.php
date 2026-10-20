{{-- partials/pagos/reviews.blade.php · Cliente · Mis reseñas + dejar nueva (AJAX) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliRevRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="pagos-reviews"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:8.5px; font-weight:600; padding:2px 10px; border-radius:3px; cursor:pointer; }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
    .star-mini i { font-size:9px; color:#e4e5e9; }
    .star-mini i.filled { color:#e0b453; }
    .reservas-soft-modal .modal-content { border:1px solid #e9eef4; border-radius:5px; font-size:9.5px; color:#64748b; }
    .reservas-soft-modal .form-label { font-size:7.5px; font-weight:600; text-transform:uppercase; color:#94a3b8; margin-bottom:2px; }
    .rsv-toast { position:fixed; bottom:14px; right:14px; z-index:1090; display:none; font-size:9px; font-weight:600; padding:7px 12px; border-radius:5px; box-shadow:0 4px 14px rgba(0,0,0,.12); border:1px solid; max-width:320px; }
    .rsv-toast.ok { background:#f0f7f2; color:#2e7d4f; border-color:#d5e7dc; }
    .rsv-toast.err { background:#fdf5f4; color:#b05c5c; border-color:#f0dcd8; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $misReviews = collect(); $elegibles = collect();
    try {
        $misReviews = \App\Models\Rsv\Review::with('reserva:id,codigo_reserva')
            ->where('id_user_autor', $uid)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        /* Reservas finalizadas sin reseña aún = elegibles para calificar */
        $conReview = $misReviews->pluck('id_rsv_reservas');
        $elegibles = \App\Models\Rsv\Reserva::with('inmueble')
            ->where('id_user', $uid)
            ->whereDate('fecha_fin', '<', now()->toDateString())
            ->whereNotIn('id', $conReview)
            ->orderByDesc('fecha_fin')
            ->limit(10)
            ->get();
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.reviews: '.$e->getMessage()); }
    $rutaRev = \Illuminate\Support\Facades\Route::has('rsv.reviews.store') ? route('rsv.reviews.store') : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:6px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-star-fill me-1" style="font-size:8px; color:#e0b453;"></i> Mis Reseñas
    </h6>
    @if($rutaRev && $elegibles->isNotEmpty())
        <button type="button" class="btn-green" data-bs-toggle="modal" data-bs-target="#cliModalRev">
            <i class="bi bi-star-half"></i> Calificar estadía
        </button>
    @endif
</div>

@forelse($misReviews as $rev)
    <div class="list-item-app">
        <div class="d-flex justify-content-between align-items-center">
            <span class="star-mini" title="{{ $rev->puntuacion ?? 0 }}/5">
                @php $p = (int)($rev->puntuacion ?? 0); @endphp
                @for($s=1;$s<=5;$s++)<i class="bi bi-star-fill {{ $s <= $p ? 'filled' : '' }}"></i>@endfor
            </span>
            <span class="sub">{{ optional($rev->created_at)->format('d/m/Y') }}</span>
        </div>
        <span class="sub d-block">{{ optional($rev->reserva)->codigo_reserva ?? '' }}</span>
        @if($rev->comentario)
            <span class="sub d-block" style="color:var(--ink-2);">"{{ $rev->comentario }}"</span>
        @endif
    </div>
@empty
    <div class="empty-friendly">
        <i class="bi bi-star d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
        <span class="sub">Aún no has dejado reseñas.</span>
        @if($elegibles->isNotEmpty()) <span class="sub d-block" style="color:var(--accent);">Tienes {{ $elegibles->count() }} estadía(s) por calificar ⭐</span> @endif
    </div>
@endforelse

@if($rutaRev && $elegibles->isNotEmpty())
<div class="modal fade reservas-soft-modal" id="cliModalRev" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:9px 14px;">
                <h6 class="modal-title mb-0 fw-bold" style="font-size:0.95rem;">Calificar tu estadía</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:9px;"></button>
            </div>
            <form id="cliRevForm" data-send="{{ $rutaRev }}">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-2"><label class="form-label">Reserva *</label>
                        <select class="form-select form-select-sm" name="id_rsv_reservas" required>
                            @foreach($elegibles as $r)
                                <option value="{{ $r->id }}">#{{ $r->codigo_reserva }} · {{ optional($r->inmueble)->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mb-2">
                        <label class="form-label">Puntuación</label>
                        <style>
                            .rsv-rating-css { direction: rtl; display: inline-flex; }
                            .rsv-rating-css input { display: none; }
                            .rsv-rating-css label { color: #e4e5e9; cursor: pointer; font-size: 20px; padding: 0 3px; }
                            .rsv-rating-css label:hover, .rsv-rating-css label:hover ~ label,
                            .rsv-rating-css input:checked ~ label { color: #e0b453; }
                        </style>
                        <div class="rsv-rating-css">
                            <input type="radio" name="calificacion" id="crS5" value="5"><label for="crS5" class="bi bi-star-fill"></label>
                            <input type="radio" name="calificacion" id="crS4" value="4"><label for="crS4" class="bi bi-star-fill"></label>
                            <input type="radio" name="calificacion" id="crS3" value="3"><label for="crS3" class="bi bi-star-fill"></label>
                            <input type="radio" name="calificacion" id="crS2" value="2"><label for="crS2" class="bi bi-star-fill"></label>
                            <input type="radio" name="calificacion" id="crS1" value="1" checked><label for="crS1" class="bi bi-star-fill"></label>
                        </div>
                    </div>
                    <div class="mb-0"><label class="form-label">Comentario (opcional)</label>
                        <textarea class="form-control form-control-sm" name="comentario" rows="3" placeholder="¿Cómo fue tu experiencia?"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:9px 14px;">
                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size:11px;" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-dark py-1 px-2" style="font-size:11px;"><i class="bi bi-check-lg"></i> Publicar</button>
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
    var root = document.getElementById('cliRevRoot');
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

    var form = document.getElementById('cliRevForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            var fd = new FormData(form);
            fd.append('_token', CSRF);
            fd.append('id_user', '{{ \Illuminate\Support\Facades\Auth::id() }}');
            fetch(form.getAttribute('data-send'), {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
            .then(function (resp) {
                if (resp.s === 201 || resp.s === 200) { toast('¡Gracias por tu reseña!', false); swapFrag(); }
                else {
                    btn.disabled = false; btn.innerHTML = '<i class="bi bi-check-lg"></i> Publicar';
                    toast(resp.j.message || 'No se pudo publicar.', true);
                }
            })
            .catch(function () { btn.disabled = false; toast('Error de conexión.', true); });
        });
    }
})();
</script>
