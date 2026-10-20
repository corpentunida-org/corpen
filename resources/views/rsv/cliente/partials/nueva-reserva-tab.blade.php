{{-- VISOR GENERAL · Nueva Reserva (Buscador + Checkout) --}}
<div class="row g-3">
    <div class="col-12">
        @if(view()->exists('rsv.cliente.partials.nueva-reserva.buscador'))
            @include('rsv.cliente.partials.nueva-reserva.buscador')
        @endif
    </div>
    <div class="col-12">
        @if(view()->exists('rsv.cliente.partials.nueva-reserva.checkout'))
            @include('rsv.cliente.partials.nueva-reserva.checkout')
        @endif
    </div>
</div>
