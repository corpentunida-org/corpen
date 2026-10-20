{{-- partials/pagos/pasarelas.blade.php · Cliente · Pasarelas disponibles (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliPasarelasRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="pagos-pasarelas"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .val { font-size:9px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
</style>

@php
    $pasarelas = collect();
    try {
        if (class_exists(\App\Models\Rsv\Pasarela::class)) { $pasarelas = \App\Models\Rsv\Pasarela::all(); }
    } catch (\Throwable $e) { $pasarelas = collect(); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-credit-card me-1" style="font-size:8px; color:#5d8a70;"></i> Medios de Pago
</h6>

@forelse($pasarelas as $p)
    <div class="d-flex align-items-center justify-content-between mb-1" style="gap:6px;">
        <span class="val"><i class="bi bi-bank me-1" style="color:#c3ccd6;"></i>{{ $p->nombre ?? $p->name }}</span>
        <i class="bi bi-shield-check" style="font-size:9px; color:var(--accent);" title="Pasarela autorizada"></i>
    </div>
@empty
    <div class="empty-friendly"><span class="sub">Sin pasarelas configuradas.</span></div>
@endforelse

<span class="sub d-block mt-2">Todos los pagos quedan registrados con soporte y son verificables.</span>
</div>
