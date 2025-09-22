<li class="nxl-item nxl-hasmenu">
   {{--  @php
        $permisos = auth()->user()->getDirectPermissions()->pluck('name');
        dd($permisos);
    @endphp --}}
    <a class="nxl-link">
        <span class="nxl-micon"><i class="bi bi-bag-heart"></i></span>
        <span class="nxl-mtext">Seguros</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>
    <ul class="nxl-submenu">
        <li class="nxl-item"><a class="nxl-link" href="{{ route('seguros.poliza.index') }}">Pólizas</a></li>
        <li class="nxl-item"><a class="nxl-link" href="{{ route('seguros.novedades.index') }}">Novedades</a></li>
        @candirect('seguros.reclamacion.index')
            <li class="nxl-item"><a class="nxl-link" href="{{ route('seguros.reclamacion.index') }}">Reclamaciones</a></li>
        @endcandirect
        @candirect('seguros.beneficios.index')
            <li class="nxl-item"><a class="nxl-link" href="{{ route('seguros.beneficios.index') }}">Beneficios</a></li>
        @endcandirect
        @candirect('seguros.convenio.index')
            <li class="nxl-item"><a class="nxl-link" href="{{ route('seguros.convenio.index') }}">Convenio</a></li>
        @endcandirect
    </ul>
</li>
