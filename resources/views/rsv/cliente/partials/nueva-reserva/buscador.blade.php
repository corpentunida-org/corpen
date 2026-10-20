{{-- partials/nueva-reserva/buscador.blade.php · Cliente · Disponibilidad --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliBuscadorRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="nueva-buscador"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); display:block; margin-bottom:2px; }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .strong { color:var(--ink-2); font-weight:600; }
    .reservas-soft .btn-mini { border:1px solid var(--grid-2); background:#fff; color:var(--ink-3); font-family:inherit; font-size:8.5px; font-weight:500; padding:1px 8px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:3px; }
    .reservas-soft .btn-mini:hover { color:var(--ink-2); border-color:#d7dee6; background:#fafcfe; text-decoration:none; }
    .reservas-soft .btn-green { background:var(--accent-bg); color:var(--accent); border:1px solid var(--accent-bd); font-family:inherit; font-size:8.5px; font-weight:600; padding:2px 10px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
    .reservas-soft .btn-green:hover { background:#e3f1e9; color:#34684e; text-decoration:none; }
    .reservas-soft .xls-date { font-size:9px; color:var(--ink-2); background:#fff; border:1px solid var(--grid-2); border-radius:3px; padding:3px 6px; width:120px; outline:0; }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; }
    .reservas-soft .list-item-app:hover { border-color:var(--accent-bd); background:#fafcfe; }
    .reservas-soft .pill { display:inline-flex; align-items:center; padding:1px 8px; border-radius:10px; font-size:8px; font-weight:700; border:1px solid; letter-spacing:.02em; line-height:13px; }
    .reservas-soft .pill-success { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; }
    .reservas-soft .pill-secondary { color:#8a97a5; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .rsv-toast { position:fixed; bottom:14px; right:14px; z-index:1090; display:none; font-size:9px; font-weight:600; padding:7px 12px; border-radius:5px; box-shadow:0 4px 14px rgba(0,0,0,.12); border:1px solid; max-width:320px; }
    .rsv-toast.ok { background:#f0f7f2; color:#2e7d4f; border-color:#d5e7dc; }
    .rsv-toast.err { background:#fdf5f4; color:#b05c5c; border-color:#f0dcd8; }
</style>

@php
    $hoy = now()->startOfDay();
    $fiReq = request('fi'); 
    $ffReq = request('ff');
    $fi = null; 
    $ff = null;
    
    try { if ($fiReq) $fi = \Carbon\Carbon::parse($fiReq)->startOfDay(); } catch (\Throwable $e) {}
    try { if ($ffReq) $ff = \Carbon\Carbon::parse($ffReq)->startOfDay(); } catch (\Throwable $e) {}
    
    $conFechas = ($fi && $ff && $ff->gt($fi));
    $inmuebles = collect();
    
    try {
        $iq = \App\Models\Rsv\CatalogoInmueble::with(['multimedia', 'latestTarifa'])->where('active', 1);

        if ($conFechas) {
            $idsLibres = \App\Models\Rsv\Status::query()
                ->where(function ($w) {
                    $w->where('name', 'like', '%cancela%')->orWhere('name', 'like', '%anula%');
                })->pluck('id');

            $iq->whereDoesntHave('reservas', function ($r) use ($fi, $ff, $idsLibres) {
                $r->where(function ($w) use ($fi, $ff) {
                    $w->whereDate('fecha_inicio', '<', $ff->toDateString())
                      ->whereDate('fecha_fin',   '>', $fi->toDateString());
                });
                if ($idsLibres->isNotEmpty()) { 
                    $r->whereNotIn('id_rsv_statuses', $idsLibres); 
                }
            });
        }

        $inmuebles = $iq->orderBy('name')->limit(24)->get();
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('cli.buscador: '.$e->getMessage());
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-search me-1" style="font-size:8px; color:#5d8a70;"></i> Busca tu próxima estadía
    </h6>
    <form method="GET" action="{{ url()->current() }}" id="cliBusqForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
        @if(request('inmueble_id'))
            <input type="hidden" name="inmueble_id" value="{{ request('inmueble_id') }}">
        @endif
        <input type="date" name="fi" class="xls-date" value="{{ $fi?->toDateString() }}" title="Llegada">
        <span class="sub">→</span>
        <input type="date" name="ff" class="xls-date" value="{{ $ff?->toDateString() }}" title="Salida">
        <button type="submit" class="btn-green" title="Verificar disponibilidad">
            <i class="bi bi-calendar-check"></i> Consultar
        </button>
    </form>
</div>

@if($conFechas)
    <div class="alert py-1 px-2 mb-2" style="font-size:8px; background:var(--accent-bg); border:1px solid var(--accent-bd); color:var(--accent); border-radius:4px;">
        <i class="bi bi-check-circle me-1"></i> Disponibilidad del <b>{{ $fi->format('d/m') }}</b> al <b>{{ $ff->format('d/m') }}</b> ({{ (int) $fi->diffInDays($ff) }} noches)
    </div>
@else
    <span class="sub d-block mb-2">Opcional: elige fechas para ver solo propiedades disponibles.</span>
@endif

@forelse($inmuebles as $inm)
    @php
        $tarifa = optional($inm->latestTarifa)->precio_noche;
        $noches = $conFechas ? (int) $fi->diffInDays($ff) : null;
        
        $total = null;
        if ($tarifa && $noches) {
            $total = $tarifa * $noches;
        }
        
        $portada = $inm->multimedia->firstWhere('es_portada', 1);
        if (!$portada) {
            $portada = $inm->multimedia->first();
        }
    @endphp
    
    <div class="list-item-app d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
        <div class="d-flex align-items-center" style="gap:8px; min-width:0;">
            @if($portada && ($portada->url ?? $portada->ruta ?? null))
                <img src="{{ $portada->url ?? $portada->ruta }}" alt="" style="width:46px; height:34px; object-fit:cover; border-radius:4px; border:1px solid var(--grid);">
            @else
                <span class="d-flex align-items-center justify-content-center" style="width:46px; height:34px; background:var(--head); border:1px solid var(--grid); border-radius:4px;">
                    <i class="bi bi-house-door" style="font-size:10px; color:#c3ccd6;"></i>
                </span>
            @endif
            
            <div style="min-width:0;">
                <span class="val">{{ $inm->name }}</span>
                <span class="sub d-block">
                    @if(!empty($inm->city))
                        {{ $inm->city }} · 
                    @endif
                    Cap. {{ $inm->capacidad_maxima ?? '—' }} pax
                </span>
            </div>
        </div>
        
        <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
            <span class="pill {{ $conFechas ? 'pill-success' : 'pill-secondary' }}">
                {{ $conFechas ? 'DISPONIBLE' : 'CONSULTAR' }}
            </span>
            <span class="val" style="color:var(--accent);">
                {{ $tarifa ? ('$'.number_format($tarifa, 0).'/noche') : '—' }}
            </span>
            
            @if($total) 
                <span class="sub">total ~${{ number_format($total, 0) }}</span> 
            @endif
            
            <a href="{{ request()->fullUrlWithQuery(['inmueble_id' => $inm->id, 'fi' => $fi?->toDateString(), 'ff' => $ff?->toDateString()]) }}"
               class="btn-green text-decoration-none" title="Ir al checkout con estas fechas">
                Reservar <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
@empty
    <div class="empty-friendly">
        <i class="bi bi-inbox d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
        <span class="sub">{{ $conFechas ? 'Sin propiedades disponibles para esas fechas.' : 'No hay propiedades activas por ahora.' }}</span>
    </div>
@endforelse

</div>

<div id="rsvToast" class="rsv-toast"></div>
<script>
(function () {
    var root = document.getElementById('cliBuscadorRoot');
    if (!root) return;
})();
</script>