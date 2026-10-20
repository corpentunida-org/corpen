{{-- partials/mis-reservas/activas-pasadas.blade.php · Cliente · Mis reservas (solo lectura) --}}
<div class="reservas-soft p-3 bg-white rounded-3 border" id="cliMisResRoot"
     data-frag="{{ route('rsv.cliente.fragmento') }}" data-fragkey="reservas-activas"
     style="border-color:#f1f5f9 !important;">

<style>
    .reservas-soft { --grid:#f1f5f9; --grid-2:#e9eef4; --head:#fafbfd; --ink:#64748b; --ink-2:#475569; --ink-3:#94a3b8; --ink-4:#b3bcc7; --accent:#3d7a5c; --accent-bg:#f0f7f2; --accent-bd:#d5e7dc; }
    .reservas-soft .lbl { font-size:7.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:var(--ink-3); }
    .reservas-soft .val { font-size:9.5px; color:var(--ink-2); font-weight:600; }
    .reservas-soft .sub { font-size:7.5px; color:var(--ink-4); }
    .reservas-soft .list-item-app { padding:6px 8px; background:#fff; border:1px solid var(--grid); border-radius:5px; margin-bottom:5px; }
    .reservas-soft .list-item-app:hover { border-color:var(--accent-bd); background:#fafcfe; }
    .reservas-soft .btn-mini { border:1px solid var(--grid-2); background:#fff; color:var(--ink-3); font-family:inherit; font-size:8.5px; padding:1px 8px; border-radius:3px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:3px; }
    .reservas-soft .btn-mini:hover { color:var(--ink-2); background:#fafcfe; text-decoration:none; }
    .reservas-soft .pill { display:inline-flex; align-items:center; padding:1px 8px; border-radius:10px; font-size:8px; font-weight:700; border:1px solid; line-height:13px; text-transform:uppercase; }
    .reservas-soft .pill-success { color:#2e7d4f; background:#f0f7f2; border-color:#d5e7dc; }
    .reservas-soft .pill-warning { color:#a3822f; background:#fdf8ec; border-color:#f3e7c6; }
    .reservas-soft .pill-danger  { color:#b05c5c; background:#fdf1f1; border-color:#f3d8d8; }
    .reservas-soft .pill-primary { color:#4a6fa5; background:#f2f6fc; border-color:#dbe7f5; }
    .reservas-soft .pill-secondary { color:#8a97a5; background:#fafbfd; border-color:#eef2f6; }
    .reservas-soft .count-badge { display:inline-flex; gap:4px; font-size:8px; font-weight:600; color:var(--ink-2); background:var(--head); border:1px solid var(--grid-2); border-radius:8px; padding:1px 8px; }
    .reservas-soft .count-badge b { color:var(--accent); }
    .reservas-soft .empty-friendly { text-align:center; padding:12px; background:var(--head); border:1px dashed var(--grid-2); border-radius:6px; }
</style>

@php
    $uid = \Illuminate\Support\Facades\Auth::id();
    $mis = collect();
    try {
        $mis = \App\Models\Rsv\Reserva::with(['inmueble', 'status'])
            ->where('id_user', $uid)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    } catch (\Throwable $e) { \Illuminate\Support\Facades\Log::warning('cli.misres: '.$e->getMessage()); }

    $pillSt = function ($name) {
        $s = mb_strtolower((string) $name);
        if (str_contains($s, 'confirm') || str_contains($s, 'activ')) return 'success';
        if (str_contains($s, 'pendien')) return 'warning';
        if (str_contains($s, 'cancela') || str_contains($s, 'anula')) return 'secondary';
        if (str_contains($s, 'final') || str_contains($s, 'complet')) return 'primary';
        return 'danger';
    };
@endphp

<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap" style="gap:8px;">
    <h6 class="fw-bold mb-0" style="font-size:9.5px; color:#334155;">
        <i class="bi bi-calendar2-check me-1" style="font-size:8px; color:#5d8a70;"></i> Mis Reservas
    </h6>
    <span class="count-badge"><b>{{ $mis->count() }}</b> reserva(s)</span>
</div>
<span class="sub d-block mb-2">Selecciona una reserva para ver su itinerario, huéspedes, historial y pagos.</span>

@forelse($mis as $r)
    @php
        $fi = $r->fecha_inicio ? \Carbon\Carbon::parse($r->fecha_inicio) : null;
        $ff = $r->fecha_fin ? \Carbon\Carbon::parse($r->fecha_fin) : null;
        $st = optional($r->status)->name ?? 'Pendiente';
        $sel = request('reserva_id') == $r->id;
    @endphp
    <a href="{{ request()->fullUrlWithQuery(['reserva_id' => $r->id]) }}"
       class="list-item-app text-decoration-none d-flex justify-content-between align-items-center flex-wrap {{ $sel ? 'border-2' : '' }}"
       style="gap:6px; {{ $sel ? 'border-color:var(--accent) !important; background:var(--accent-bg);' : '' }}">
        <span class="d-flex align-items-center" style="gap:6px; min-width:0;">
            <i class="bi bi-journal-text" style="font-size:9px; color:#c3ccd6;"></i>
            <span class="val">#{{ $r->codigo_reserva }}</span>
            <span class="sub">{{ optional($r->inmueble)->name ?? 'N/D' }}</span>
        </span>
        <span class="d-flex align-items-center flex-wrap" style="gap:6px;">
            <span class="sub">{{ $fi?->format('d/m') ?? '—' }} → {{ $ff?->format('d/m/y') ?? '—' }}</span>
            <span class="val" style="color:var(--accent);">${{ number_format((float)($r->monto_total ?? 0),0) }}</span>
            <span class="pill pill-{{ $pillSt($st) }}">{{ $st }}</span>
            @if(\Illuminate\Support\Facades\Route::has('rsv.reservas.pdf'))
                <span class="btn-mini" title="Comprobante PDF (abre en la pestaña Pagos)">
                    <i class="bi bi-file-earmark-pdf"></i>
                </span>
            @endif
        </span>
    </a>
@empty
    <div class="empty-friendly">
        <i class="bi bi-inbox d-block mb-1" style="font-size:14px; color:#c3ccd6;"></i>
        <span class="sub">Aún no tienes reservas. ¡Crea la primera en “Nueva Reserva”!</span>
    </div>
@endforelse

</div>
