<div class="sidebar" data-color="blue">
    <div class="logo">
        <a href="/" class="simple-text logo-mini">
            APP
        </a>
            <a href="/" class="simple-text logo-normal">
                @yield('titlenav', "{{env('APP_NAME')}}")
            </a>
    </div>
    
    <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
            <li class="active ">
                <a href="/asociados">
                    <i class="now-ui-icons business_badge"></i>
                    <p>Titular</p>
                </a>
            </li>
            <li class="active ">
                <a href="/prestarServicio">
                    <i class="now-ui-icons business_chart-bar-32"></i>
                    <p>Servicios</p>
                </a>
            </li>
        </ul>
    </div>
</div>


    {{-- menu active --}}
    {{-- <li class="{{ Request::is('pagina1') ? 'active' : '' }}"><a href="{{ route('pagina1') }}">Página 1</a></li> --}}
    <!-- :active="request()->routeIs('dashboard)" --> 
