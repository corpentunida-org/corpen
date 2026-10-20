{{-- VISOR GENERAL · Historial --}}
<div class="row g-3">
    <div class="col-12 col-lg-6">
        @if(view()->exists('rsv.cliente.partials.historial.estados')) @include('rsv.cliente.partials.historial.estados') @endif
    </div>
    <div class="col-12 col-lg-6">
        @if(view()->exists('rsv.cliente.partials.historial.endosos')) @include('rsv.cliente.partials.historial.endosos') @endif
    </div>
</div>
