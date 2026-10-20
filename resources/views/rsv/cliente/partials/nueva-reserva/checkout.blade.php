{{-- partials/nueva-reserva/checkout.blade.php --}}
<div class="p-3 bg-white rounded border" id="cliCheckoutRoot" data-redirect="{{ request()->fullUrlWithoutQuery(['inmueble_id', 'fi', 'ff']) }}">
    @php
        $inmId = request('inmueble_id');
        $inm = is_numeric($inmId) ? \App\Models\Rsv\CatalogoInmueble::with('latestTarifa')->find($inmId) : null;
        $tarifa = optional($inm?->latestTarifa)->precio_noche;
        $fi = request('fi') ? \Carbon\Carbon::parse(request('fi'))->startOfDay() : null;
        $ff = request('ff') ? \Carbon\Carbon::parse(request('ff'))->startOfDay() : null;
        $noches = ($fi && $ff && $ff->gt($fi)) ? (int) $fi->diffInDays($ff) : 0;
        $total = ($tarifa && $noches > 0) ? $tarifa * $noches : 0;
        $rutaStore = \Illuminate\Support\Facades\Route::has('rsv.reservas.store') ? route('rsv.reservas.store') : '';
    @endphp

    <h6 class="fw-bold mb-2" style="font-size:9.5px; color:#334155;">Checkout de Reserva</h6>

    @if(!$inm || !$fi || !$ff)
        <div class="alert alert-warning p-2" style="font-size:9px;">Selecciona una propiedad y fechas en el buscador.</div>
    @else
        <div class="mb-3" style="font-size:9px;">
            <div><b>Propiedad:</b> {{ $inm->name }}</div>
            <div><b>Estancia:</b> {{ $fi->format('d/m/Y') }} al {{ $ff->format('d/m/Y') }} ({{ $noches }} noches)</div>
            <div><b>Total:</b> $ {{ number_format($total, 2) }}</div>
        </div>

        @if($rutaStore)
            <form id="cliCheckoutForm" data-store="{{ $rutaStore }}">
                @csrf
                <input type="hidden" name="id_rsv_catalogo_inmueble" value="{{ $inm->id }}">
                <input type="hidden" name="id_user" value="{{ \Illuminate\Support\Facades\Auth::id() }}">
                <input type="hidden" name="fecha_inicio" value="{{ $fi->toDateString() }}">
                <input type="hidden" name="fecha_fin" value="{{ $ff->toDateString() }}">
                <input type="hidden" name="monto_total" value="{{ $total }}">
                
                <button type="submit" class="btn btn-success btn-sm w-100">Confirmar Reserva</button>
            </form>
        @else
            <div class="text-danger" style="font-size:9px;">Ruta de tienda no disponible.</div>
        @endif
    @endif
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var root = document.getElementById('cliCheckoutRoot');
    if (!root) return;
    var form = document.getElementById('cliCheckoutForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        var fd = new FormData(form);
        fetch(form.getAttribute('data-store'), {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            alert('¡Reserva procesada con éxito!');
            window.location.href = root.getAttribute('data-redirect');
        })
        .catch(function() {
            if (btn) btn.disabled = false;
            alert('Error al procesar la reserva.');
        });
    });
});
</script>