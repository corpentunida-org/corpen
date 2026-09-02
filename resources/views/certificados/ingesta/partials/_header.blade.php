{{-- LÓGICA BREADCRUMB (Migas de pan de subida) --}}
@php
    $migasAnio = '----';
    $migasMes = '--';
    $migasLote = $bloqueActivo ? 'API-' . str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) : 'Por seleccionar';

    if($bloqueActivo) {
        $bActivo = \App\Models\Certificados\CarSiaBloque::where('numero_bloque', $bloqueActivo)->first();
        if($bActivo) {
            $pActivo = collect($periodos)->firstWhere('id', $bActivo->id_periodo);
            if($pActivo) {
                $migasAnio = $pActivo->anio;
                $migasMes = str_pad($pActivo->mes, 2, '0', STR_PAD_LEFT);
            }
        }
    }
@endphp

{{-- LÓGICA BREADCRUMB --}}
@php
    $migasAnio = '----';
    $migasMes = '--';
    $migasLote = $bloqueActivo ? 'API-' . str_pad($bloqueActivo, 4, '0', STR_PAD_LEFT) : 'Por seleccionar';

    if($bloqueActivo) {
        $bActivo = \App\Models\Certificados\CarSiaBloque::where('numero_bloque', $bloqueActivo)->first();
        if($bActivo) {
            $pActivo = collect($periodos)->firstWhere('id', $bActivo->id_periodo);
            if($pActivo) {
                $migasAnio = $pActivo->anio;
                $migasMes = str_pad($pActivo->mes, 2, '0', STR_PAD_LEFT);
            }
        }
    }
@endphp

{{-- CABECERA COMPACTA --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center justify-content-center shadow-sm"
             style="width:54px;height:54px;border-radius:var(--r-lg);background:var(--c-primary-soft);">
            <i class="fas fa-layer-group fs-4" style="color:var(--c-primary);"></i>
        </div>
        <div>
            <h1 class="h4 fw-bold m-0 text-dark d-flex align-items-center gap-2">
                Carga de datos
                <span data-tooltip="Sistema que reúne y organiza toda la información del mes en un solo lugar."
                      style="text-decoration: underline dotted var(--c-muted); text-underline-offset: 4px;">
                    ERP
                </span>
                <span class="badge-g badge-info"
                      data-tooltip="Entorno del analista"
                      style="font-size:.63rem;">
                    Analista
                </span>
            </h1>

            {{-- Breadcrumb minimalista integrado como subtítulo --}}
            <div class="d-flex align-items-center flex-wrap mt-1" style="font-size:.8rem; color:var(--c-muted); font-weight: 500;">
                <i class="fas fa-file-excel me-1" style="color:#10b981;"></i> Subida de excel
                <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                {{ $migasAnio }}
                <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                Mes {{ $migasMes }}
                <i class="fas fa-chevron-right mx-2" style="font-size:.55rem; opacity:.5;"></i>
                @if($bloqueActivo)
                    <span class="fw-bold" style="color:var(--c-primary);">Lote {{ $migasLote }}</span>
                @else
                    <span class="fw-bold" style="color:#92400e;">Lote {{ $migasLote }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ request()->fullUrl() }}" class="btn-icon-round shadow-sm" title="Actualizar vista">
            <i class="fas fa-sync-alt spin-on-hover"></i>
        </a>

        <a href="{{ route('certificados.operaciones.index', ['bloque' => $bloqueActivo]) }}" class="btn-g btn-outline-g shadow-sm" data-tooltip="Ver las operaciones de este lote">
            <i class="fas fa-list-ul" style="color:var(--c-primary);"></i>
            Ver Operaciones
        </a>

        @if($bloquesDisponibles->count() > 0)
        <form action="{{ route('certificados.ingesta.index') }}" method="GET"
              class="d-flex align-items-center gap-2 bg-light border rounded-pill px-3 py-1 shadow-sm"
              data-tooltip="Cambiar de lote rápidamente"
              style="transition: background 0.2s;">
            @if(request('buscar_cedula')) <input type="hidden" name="buscar_cedula" value="{{ request('buscar_cedula') }}"> @endif
            @if(request('estado'))        <input type="hidden" name="estado"        value="{{ request('estado') }}"> @endif

            <i class="fas fa-exchange-alt text-muted" style="font-size:.75rem;"></i>
            <select name="bloque"
                    class="border-0 bg-transparent text-muted fw-medium shadow-none"
                    style="cursor:pointer;outline:none;font-size:.78rem; padding: .15rem 0;"
                    onchange="this.form.submit()"
                    aria-label="Cambio rápido de bloque">
                {{-- Si $bloqueActivo está vacío, forzamos a que esta sea la opción seleccionada --}}
                <option value="" disabled {{ empty($bloqueActivo) ? 'selected' : '' }}>Cambiar lote...</option>

                @foreach($bloquesDisponibles as $b)
                    <option value="{{ $b }}" {{ $bloqueActivo == $b ? 'selected' : '' }}>
                        Cambiar a API-{{ str_pad($b, 4, '0', STR_PAD_LEFT) }}
                    </option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>
