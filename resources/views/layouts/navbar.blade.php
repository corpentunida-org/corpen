<div class="sidebar" data-color="blue">
    <div class="logo">
        <a href="/" class="simple-text logo-mini">
            APP
        </a>
        @can('exequial', App\Models\User::class)
            <a href="/" class="simple-text logo-normal">
                EXEQUIAL
            </a>
        @endcan
        @can('creditos', App\Models\User::class)
            <a href="/" class="simple-text logo-normal">
                CREDITOS
            </a>
        @endcan
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
                <a href="/prestarServicio">
                    <i class="now-ui-icons business_chart-bar-32"></i>
                    <p>Servicios</p>
                </a>
            </li>
        @endcan
        @can('creditos', App\Models\User::class)
            <li class="active ">
                <a href="/">
                    <i class="now-ui-icons business_badge"></i>
                    <p>Liquidación</p>
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
