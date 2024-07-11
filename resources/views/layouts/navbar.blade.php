<div class="sidebar" data-color="blue">
    <div class="logo">
        <a href="/" class="simple-text logo-mini">
            APP
        </a>
        <a href="/" class="simple-text logo-normal">
            en desarrollo
        </a>
    </div>
    
    <div class="sidebar-wrapper" id="sidebar-wrapper">
        <ul class="nav">
        @can('exequial', App\Models\User::class)
            <li class="active ">
                <a href="/asociados">
                    <i class="now-ui-icons business_badge"></i>
                    <p>Titular</p>
                </a>
            </li>
            <li class="active ">
                <a href="/monitoria">
                    <i class="now-ui-icons business_chart-bar-32"></i>
                    <p>Servicios</p>
                </a>
            </li>
        @endcan
            <li class="active-pro">                
                <x-authentication-card-logo />                
            </li>
        </ul>

    </div>
    
</div>


    {{-- menu active --}}
    {{-- <li class="{{ Request::is('pagina1') ? 'active' : '' }}"><a href="{{ route('pagina1') }}">Página 1</a></li> --}}
