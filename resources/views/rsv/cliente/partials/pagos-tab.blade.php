{{-- VISOR GENERAL · Pagos --}}
<div class="row g-3">
    <div class="col-12 col-lg-7">
        @if(view()->exists('rsv.cliente.partials.pagos.transacciones')) @include('rsv.cliente.partials.pagos.transacciones') @endif
    </div>
    <div class="col-12 col-lg-5">
        @if(view()->exists('rsv.cliente.partials.pagos.reviews')) @include('rsv.cliente.partials.pagos.reviews') @endif
        @if(view()->exists('rsv.cliente.partials.pagos.pasarelas')) @include('rsv.cliente.partials.pagos.pasarelas') @endif
    </div>
</div>
