<li class="nxl-item nxl-hasmenu">
    <a class="nxl-link" href="javascript:void(0)">
        <span class="nxl-micon"><i class="bi bi-chat-dots"></i></span>
        <span class="nxl-mtext">Daytrack</span>
        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
    </a>

    <ul class="nxl-submenu">
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('interactions.create') }}">
                <i class="bi bi-plus-circle me-2"></i> Nueva Interacción
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('interactions.index') }}">
                <i class="bi bi-list-check me-2"></i> Mis Interacciones
            </a>
        </li>
        <li class="nxl-item">
            <a class="nxl-link" href="{{ route('interactions.auditoria') }}">
                <i class="bi bi-search me-2"></i> Auditoría Interacciones
            </a>
        </li>
        <li class="nxl-item">
            {{-- Ya no hace falta forzar agent_id aquí: report() siempre parte de "individual"
                 (lo propio) por defecto, igual que Listado/Auditoría — ver alcanceInformes(). --}}
            <a href="{{ route('interactions.report') }}" class="nxl-link">
                <i class="bi bi-bar-chart-fill me-2"></i> Informe
            </a>
        </li>
        @candirect('interacciones.parametros.index')
        <li class="nxl-item nxl-hasmenu">
            <a class="nxl-link" href="javascript:void(0)">
                <i class="bi bi-sliders me-2"></i>
                <span>Parámetros</span>
                <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
            </a>
            <ul class="nxl-submenu">
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.channels.index') }}">
                        <i class="bi bi-broadcast-pin me-2"></i> Canales
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.types.index') }}">
                        <i class="bi bi-tags me-2"></i> Tipos
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.outcomes.index') }}">
                        <i class="bi bi-check2-circle me-2"></i> Resultados
                    </a>
                </li>
                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.next_actions.index') }}">
                        <i class="bi bi-calendar2-check me-2"></i> Próxima Acción
                    </a>
                </li>

                <li class="nxl-item">
                    <a class="nxl-link" href="{{ route('interactions.chat.index') }}">
                        <i class="bi bi-chat-square-text me-2"></i> Centro de Chat
                    </a>
                </li>
            </ul>
        </li>
        @endcandirect
    </ul>
</li>
