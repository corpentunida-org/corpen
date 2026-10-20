{{-- partials/mis-reservas/itinerario.blade.php · Cliente · Itinerario (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliItinRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="reservas-itinerario"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-4); }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
    .reservas-soft .st-warn { background:#fdf5f4; border:1px solid #f0dcd8; border-radius:4px; color:#c98a85; font-size:8px; font-weight:600; padding:6px 9px; }
</style>

@php
    $rid = request('reserva_id');
    $reserva = null; $eventos = collect();
    try {
        if (is_numeric($rid)) {
            $reserva = \App\Models\Rsv\Reserva::with('inmueble')->where('id_user', \Illuminate\Support\Facades\Auth::id())->find($rid);
            if ($reserva) {
                $eventos = $reserva->itinerarios()->orderBy('created_at')->get();
            }
        }
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.itin: '.$e->getMessage()); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-list-check me-1" style="font-size:8px; color:#5d8a70;"></i> Itinerario de la Reserva
</h6>

@if(!$reserva)
    <div class="st-warn"><i class="bi bi-info-circle me-1"></i> Selecciona una reserva en “Activas y Pasadas”.</div>
@else
    <div class="sub mb-2">Reserva #{{ $reserva->codigo_reserva }} · {{ optional($reserva->inmueble)->name ?? 'N/D' }}</div>
    @forelse($eventos as $ev)
        <div class="list-item-app">
            <span class="val">{{ $ev->titulo ?? $ev->nombre ?? $ev->evento ?? ('Evento #'.$ev->id) }}</span>
            <span class="sub d-block">
                @if($ev->fecha ?? $ev->fecha_evento ?? null)
                    <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($ev->fecha ?? $ev->fecha_evento)->format('d/m/Y') }}
                @endif
                @if($ev->hora ?? null) · <i class="bi bi-clock me-1"></i>{{ $ev->hora }} @endif
            </span>
            @if($ev->descripcion ?? $ev->detalle ?? null)
                <span class="sub d-block" style="color:var(--ink-2);">{{ $ev->descripcion ?? $ev->detalle }}</span>
            @endif
        </div>
    @empty
        <div class="empty-friendly">
            <i class="bi bi-list-check d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
            <span class="sub">El anfitrión aún no ha publicado actividades para esta reserva.</span>
        </div>
    @endforelse
@endif

</div>
