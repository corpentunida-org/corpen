<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-building-check"></i></span>
        <span class="nxl-mtext">Reservas</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    
    {{-- @candirect('reservas.Reserva.historico')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.dashboard') }}">Dashboard</a></li>
    </ul>
    @endcandirect --}}

    @candirect('reservas.Inmueble.create')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.crudinmuebles.index') }}">Inmuebles</a></li>
    </ul>
    @endcandirect
    
    @candirect('reservas.Reserva.cartera')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.reservas.sinsoporte') }}">Reservadas</a></li>
    </ul>
    @endcandirect
    
    @candirect('reservas.Reserva.pagos')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.reservas.pago') }}">Verificar Pago</a></li>
    </ul>
    @endcandirect
    
    @candirect('reservas.Reserva.lista')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.inmueble.confirmacion') }}">Confirmadas</a></li>
    </ul>
    @endcandirect
    
    @candirect('reservas.Reserva.historico')
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('reserva.inmueble.historico') }}">Histórico</a></li>
    </ul>
    @endcandirect
</li>