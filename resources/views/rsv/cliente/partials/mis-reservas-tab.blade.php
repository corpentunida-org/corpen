{{-- VISOR GENERAL · Mis Reservas --}}
@if(view()->exists('rsv.cliente.partials.mis-reservas.activas-pasadas'))
    @include('rsv.cliente.partials.mis-reservas.activas-pasadas')
@endif
