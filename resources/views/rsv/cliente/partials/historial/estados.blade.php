{{-- partials/historial/estados.blade.php · Cliente · Mi historial de estados (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliHistEstRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="historial-estados"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-4); }
    .reservas-soft .val { font-size:9px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $movs = collect();
    try {
        $misIds = \App\Models\Rsv\Reserva::where('id_user', $uid)->pluck('id');
        if ($misIds->isNotEmpty()) {
            $movs = \App\Models\Rsv\HistorialEstado::with(['estadoNuevo', 'reserva:id,codigo_reserva'])
                ->whereIn('id_rsv_reservas', $misIds)
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.histEst: '.$e->getMessage()); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-flag me-1" style="font-size:8px; color:#5d8a70;"></i> Historial de Estados
</h6>

@forelse($movs as $h)
    <div class="list-item-app d-flex justify-content-between align-items-center" style="padding:5px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:4px;">
        <span class="val">{{ optional($h->estadoNuevo)->name ?? ('#'.$h->id_rsv_statuses_nuevo) }}
            <span class="sub">· {{ optional($h->reserva)->codigo_reserva ?? '' }}</span>
        </span>
        <span class="sub">{{ optional($h->created_at)->format('d/m/y H:i') }}</span>
    </div>
@empty
    <div class="empty-friendly"><span class="sub">Sin movimientos registrados todavía.</span></div>
@endforelse

</div>
