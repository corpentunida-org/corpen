{{-- partials/mis-reservas/huespedes.blade.php · Cliente · Huéspedes de MI reserva (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliHuesRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="reservas-huespedes"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
</style>

@php
    $rid = request('reserva_id');
    $reserva = null; $huespedes = collect();
    try {
        if (is_numeric($rid)) {
            $reserva = \App\Models\Rsv\Reserva::where('id_user', \Illuminate\Support\Facades\Auth::id())->find($rid);
            if ($reserva) { $huespedes = $reserva->huespedes()->orderBy('es_titular', 'desc')->get(); }
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.huesp: '.$e->getMessage()); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-people me-1" style="font-size:8px; color:#5d8a70;"></i> Huéspedes Registrados
</h6>

@if(!$reserva)
    <div class="st-warn"><i class="bi bi-info-circle me-1"></i> Selecciona una reserva en “Activas y Pasadas”.</div>
@else
    @forelse($huespedes as $h)
        <div class="list-item-app d-flex justify-content-between align-items-center" style="gap:6px;">
            <span class="val">
                {{ $h->nombre ?? $h->nombres ?? '' }} {{ $h->apellidos ?? '' }}
                @if($h->es_titular ?? false) <i class="bi bi-star-fill" style="font-size:7px; color:#e0b453;" title="Principal"></i>@endif
            </span>
            <span class="sub">{{ $h->tipo_documento ?? 'Doc.' }} {{ $h->numero_documento ?? 'N/A' }}</span>
        </div>
    @empty
        <div class="empty-friendly">
            <i class="bi bi-person-plus d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
            <span class="sub">Sin huéspedes adicionales registrados en esta reserva.</span>
        </div>
    @endforelse
@endif

</div>
