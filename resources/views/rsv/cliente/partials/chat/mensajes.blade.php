{{-- partials/chat/mensajes.blade.php · Cliente · Soporte por reserva (AJAX) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliChatRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="chat-mensajes"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .val { font-size:9px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-size:8.5px; font-weight:600; padding:2px 10px; border-radius:3px; cursor:pointer; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
    .rsvshow .chat-bg { background-color:#eef1f3; }
    .rsvshow .msg-bubble { max-width:85%; padding:4px 8px; border-radius:7px; font-size:8.5px; margin-bottom:5px; box-shadow:0 1px 1px rgba(0,0,0,.06); color:var(--ink-2); }
    .rsvshow .msg-sent { background-color:#dcf3d8; align-self:flex-end; border-bottom-right-radius:0; }
    .rsvshow .msg-received { background-color:#fff; align-self:flex-start; border-bottom-left-radius:0; }
    .rsvshow .msg-time { font-size:6.5px; color:#8fa0a8; text-align:right; margin-top:1px; }
    .rsv-toast { position:fixed; bottom:14px; right:14px; z-index:1090; display:none; font-size:9px; font-weight:600; padding:7px 12px; border-radius:5px; box-shadow:0 4px 14px rgba(0,0,0,.12); border:1px solid; max-width:320px; }
    .rsv-toast.ok { background:#f0f7f2; color:#2e7d4f; border-color:#d5e7dc; }
    .rsv-toast.err { background:#fdf5f4; color:#b05c5c; border-color:#f0dcd8; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $rid = request('reserva_id');
    $mis = collect(); $reserva = null; $mensajes = collect();
    try {
        $mis = \App\Models\Rsv\Reserva::with('inmueble')->where('id_user', $uid)->orderByDesc('created_at')->limit(10)->get();
        if (is_numeric($rid)) {
            $reserva = $mis->firstWhere('id', (int) $rid) ?? \App\Models\Rsv\Reserva::where('id_user', $uid)->find($rid);
            if ($reserva) { $mensajes = $reserva->mensajes()->orderBy('created_at')->get(); }
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.chat: '.$e->getMessage()); }
    $rutaSend = \Illuminate\Support\Facades\Route::has('rsv.mensajes.store') ? route('rsv.mensajes.store') : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:6px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-chat-dots me-1" style="font-size:8px; color:#5d8a70;"></i> Soporte por Reserva
    </h6>
    <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center" style="gap:5px;">
        @foreach(request()->query() as $k => $v) @if(!in_array($k, ['reserva_id'])) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif @endforeach
        <select name="reserva_id" class="form-select form-select-sm" style="font-size:8.5px; width:auto;" onchange="this.form.submit()">
            <option value="">— Elige reserva —</option>
            @foreach($mis as $r)
                <option value="{{ $r->id }}" {{ $rid == $r->id ? 'selected' : '' }}>#{{ $r->codigo_reserva }} · {{ optional($r->inmueble)->name ?? '' }}</option>
            @endforeach
        </select>
    </form>
</div>

@if(!$reserva)
    <div class="st-warn"><i class="bi bi-info-circle me-1"></i> Selecciona una reserva para abrir su conversación con el equipo.</div>
@else
    <div style="height:260px;" class="overflow-auto p-2 chat-bg d-flex flex-column rounded" id="cliChatBox">
        @forelse($mensajes as $m)
            @php $mio = (($m->id_user_remitente ?? null) == $uid); @endphp
            <div class="d-flex flex-column msg-bubble {{ $mio ? 'msg-sent' : 'msg-received' }}">
                @if(!$mio) <strong style="font-size:7.5px; color:var(--accent);">Equipo RSV</strong> @endif
                <span>{{ $m->contenido ?? $m->mensaje ?? '' }}</span>
                @if($m->url_archivo ?? null)
                    <a href="{{ $m->url_archivo }}" target="_blank" style="font-size:7.5px; color:var(--accent);"><i class="bi bi-paperclip"></i> Adjunto</a>
                @endif
                <span class="msg-time">{{ optional($m->created_at)->format('H:i') }}</span>
            </div>
        @empty
            <div class="text-center mt-auto mb-auto bg-white rounded p-2 mx-auto sub" style="max-width:80%;">
                <i class="bi bi-lock-fill me-1"></i> Inicia la conversación sobre esta reserva.
            </div>
        @endforelse
    </div>

    @if($rutaSend)
        <form id="cliChatForm" data-send="{{ $rutaSend }}" class="d-flex align-items-center mt-2" style="gap:5px;">
            @csrf
            <input type="hidden" name="id_rsv_reservas" value="{{ $reserva->id }}">
            <input type="hidden" name="id_user_remitente" value="{{ $uid }}">
            <input type="hidden" name="id_user_destinatario" value="{{ $uid }}">
            <input type="text" name="mensaje" class="form-control form-control-sm" placeholder="Escribe tu consulta…" required autocomplete="off" style="border-radius:14px;">
            <button type="submit" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0"
                    style="width:26px; height:26px; background:#5d8a70; color:#fff; border:none;">
                <i class="bi bi-send-fill" style="font-size:10px;"></i>
            </button>
        </form>
    @endif
@endif

</div>

<div id="rsvToast" class="rsv-toast"></div>
<script>
(function () {
    var root = document.getElementById('cliChatRoot');
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

    var box = root.querySelector('#cliChatBox');
    if (box) { box.scrollTop = box.scrollHeight; }

    function swapFrag() {
        if (!FRAG) { location.reload(); return; }
        var url = new URL(FRAG, window.location.origin);
        url.searchParams.set('tab', KEY);
        /* preserva reserva_id para no perder el chat abierto */
        var rid = new URLSearchParams(window.location.search).get('reserva_id');
        if (rid) { url.searchParams.set('reserva_id', rid); }
        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nr = doc.getElementById(root.id);
                if (nr) { root.replaceWith(document.importNode(nr, true)); }
            });
    }

    var form = root.querySelector('#cliChatForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            var fd = new FormData(form);
            fd.append('_token', CSRF);
            fetch(form.getAttribute('data-send'), {
                method: 'POST', body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json().then(function (j) { return { s: r.status, j: j }; }); })
            .then(function (resp) {
                if (resp.s === 201 || resp.s === 200) { toast('Mensaje enviado.', false); swapFrag(); }
                else { btn.disabled = false; toast(resp.j.message || 'No se pudo enviar.', true); }
            })
            .catch(function () { btn.disabled = false; toast('Error de conexión.', true); });
        });
    }

    /* Auto-refresh del chat cada 12s (solo si no hay modal ni foco) */
    setInterval(function () {
        if (document.hidden || document.querySelector('.modal.show')) return;
        var ae = document.activeElement;
        if (ae && root.contains(ae) && (ae.tagName === 'INPUT' || ae.tagName === 'TEXTAREA')) return;
        swapFrag();
    }, 12000);
})();
</script>
