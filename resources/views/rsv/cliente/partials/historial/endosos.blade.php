{{-- partials/historial/endosos.blade.php · Cliente · Endosos de mis reservas (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliHistEndRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="historial-endosos"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; }
    .reservas-soft .val { font-size:8.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $endosos = collect();
    try {
        $misIds = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id');
        if ($misIds->isNotEmpty()) {
            $endosos = \App\Models\Rsv\HistorialEndoso::with(['userAnterior', 'userNuevo', 'reserva:id,codigo_reserva'])
                ->whereIn('id_rsv_reservas', $misIds)
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.histEnd: '.$e->getMessage()); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-arrow-left-right me-1" style="font-size:8px; color:#5d8a70;"></i> Endosos de Titularidad
</h6>

@forelse($endosos as $e)
    <div class="list-item-app" style="padding:5px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:4px;">
        <span class="val">
            {{ optional($e->userAnterior)->name ?? 'N/A' }}
            <i class="bi bi-arrow-right mx-1" style="color:#c3ccd6;"></i>
            {{ optional($e->userNuevo)->name ?? 'N/A' }}
        </span>
        <span class="sub d-block">{{ optional($e->reserva)->codigo_reserva ?? '' }} · {{ optional($e->created_at)->format('d/m/Y') }}</span>
    </div>
@empty
    <div class="empty-friendly"><span class="sub">Sin endosos registrados.</span></div>
@endforelse

</div>
