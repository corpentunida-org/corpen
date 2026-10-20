{{-- partials/chat/receptores.blade.php · Cliente · Tipos de receptor (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliRecepRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="chat-receptores"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --ink:#64748b; --ink-2:#475569; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .val { font-size:9px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
</style>

@php
    $tipos = collect();
    try {
        if (class_exists(\App\Models\Rsv\TipoReceptor::class)) {
            $col = \Illuminate\Support\Facades\Schema::hasColumn('rsv_tipo_receptor', 'name') ? 'name' : 'nombre';
            $tipos = \App\Models\Rsv\TipoReceptor::orderBy($col)->get();
        }
    } catch (\Throwable $e) { $tipos = collect(); }
@endphp

<h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">
    <i class="bi bi-person-badge me-1" style="font-size:8px; color:#5d8a70;"></i> Canales de Atención
</h6>

@forelse($tipos as $t)
    <div class="d-flex align-items-center mb-1" style="gap:6px;">
        <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:18px; height:18px; background:var(--accent-bg); color:var(--accent); font-size:8px;">
            <i class="bi bi-check-lg"></i>
        </span>
        <span class="val">{{ $t->name ?? $t->nombre }}</span>
    </div>
@empty
    <div class="empty-friendly"><span class="sub">Sin canales configurados.</span></div>
@endforelse

<span class="sub d-block mt-2">Estos son los tipos de destinatario con los que el equipo gestiona tus consultas.</span>
</div>
