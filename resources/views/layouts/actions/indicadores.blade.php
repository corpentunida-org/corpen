  @candirect('indicators.indicadores.index')
  <li class="nxl-item nxl-hasmenu">
      <a class="nxl-link" href="javascript:void(0)">
          <span>Indicadores</span>
          <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
      </a>
      <ul class="nxl-submenu">
          <li class="nxl-item">
              <a class="nxl-link" href="{{ route('indicators.indicadores.index') }}">Indicadores</a>
          </li>
          @endcandirect
          @candirect('indicators.indicadores.create')
          <li class="nxl-item">
              <a class="nxl-link" href="{{ route('indicators.indicadores.create') }}">Parametros Indicadores</a>
          </li>
          @endcandirect

          @candirect('indicators.encuestas.dashboard')
          <li class="nxl-item">
              <a class="nxl-link" href="{{ route('indicators.quizes.index') }}">Encuestas Dashboard</a>
          </li>
          @endcandirect
          @candirect('indicators.indicadores.index')
      </ul>
  </li>
  @endcandirect
