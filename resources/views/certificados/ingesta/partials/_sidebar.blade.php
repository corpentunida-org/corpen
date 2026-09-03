<style>
    /* Animación del icono desplegable para los meses */
    .month-toggle-btn.is-open .chevron-month { transform: rotate(90deg); color: var(--c-primary) !important; }
    .month-toggle-btn:hover { background-color: var(--c-bg); border-radius: var(--r-md) var(--r-md) 0 0; }
</style>

<div class="card-g p-4 sticky-sidebar">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark d-flex align-items-center gap-2 m-0" style="font-size: 1.1rem;">
            <i class="far fa-calendar-alt text-muted" style="font-size:.9rem;"></i> Periodos y Lotes
        </h5>
        <!-- Indicador visual del mes/año actual -->
        <div class="text-end">
            <span class="badge bg-primary text-white shadow-sm" style="font-size:.7rem;">
                <i class="far fa-clock me-1"></i> {{ date('m/Y') }}
            </span>
        </div>
    </div>

    {{-- ENVOLTORIO FLIP --}}
    <div class="flip-wrapper">
        <div class="flip-card" id="sidebarFlipCard">

            {{-- CARA FRONTAL: HISTORIAL ANIDADO POR AÑO -> MES -> BLOQUES --}}
            <div class="flip-face flip-front">
                <p class="text-muted mb-3 pb-2 border-bottom" style="font-size:.8rem;">Historial contable y lotes asociados.</p>

                <div class="flex-grow-1 overflow-auto custom-scrollbar pe-2 mb-3">
                    {{-- Agrupamos la colección de periodos por 'anio' --}}
                    @php
                        $periodosAgrupados = collect($periodos)->groupBy('anio')->sortKeysDesc();
                    @endphp

                    @forelse($periodosAgrupados as $anio => $meses)
                        <div class="mb-3">
                            {{-- Encabezado del Año --}}
                            <div class="year-toggle-btn d-flex align-items-center gap-2 mb-2" id="year-toggle-{{ $anio }}" data-target="year-content-{{ $anio }}">
                                <span class="badge bg-light text-dark border shadow-sm w-100 d-flex justify-content-between align-items-center py-2 px-3">
                                    <span><i class="fas fa-folder text-muted me-1"></i> Año {{ $anio }}</span>
                                    <i class="fas fa-chevron-down text-muted chevron-icon"></i>
                                </span>
                            </div>

                            {{-- Lista de Meses anidados debajo del año (Inicia oculto) --}}
                            <div class="year-content flex-column gap-2 ps-2 ms-2 mb-3" id="year-content-{{ $anio }}" style="border-left: 2px solid var(--c-border); display: none;">
                                @foreach($meses->sortByDesc('mes') as $p)
                                    @php
                                        // Buscamos los bloques pertenecientes a este periodo
                                        $bloquesDelPeriodo = \App\Models\Certificados\CarSiaBloque::where('id_periodo', $p->id)
                                            ->orderBy('numero_bloque', 'desc')
                                            ->get();

                                        // Validaciones de estado
                                        $esMesActual = ($anio == date('Y') && $p->mes == date('n'));
                                        $mesTieneActivo = $bloquesDelPeriodo->contains('numero_bloque', $bloqueActivo);
                                    @endphp

                                    <div class="d-flex flex-column rounded mb-1 shadow-sm {{ $esMesActual ? 'mes-actual-highlight' : '' }}" style="background: var(--c-surface); border: 1px solid var(--c-border);">

                                        {{-- CABECERA DEL MES (BOTÓN DESPLEGABLE) --}}
                                        <div class="month-toggle-btn p-2 d-flex justify-content-between align-items-center {{ $mesTieneActivo ? 'is-open' : '' }}"
                                             data-target="month-content-{{ $p->id }}"
                                             style="cursor: pointer; transition: background 0.2s;">
                                            <div>
                                                <div class="fw-bold {{ $esMesActual ? 'text-primary' : 'text-dark' }}" style="font-size:.8rem; line-height:1.2;">
                                                    <i class="fas fa-chevron-right chevron-month text-muted me-1" style="font-size:.65rem; transition: transform 0.3s;"></i>
                                                    {{ $p->nombre }}
                                                    @if($esMesActual)
                                                        <span class="badge bg-primary text-white ms-1 shadow-sm" style="font-size:.55rem; vertical-align:middle;">ACTUAL</span>
                                                    @endif
                                                </div>
                                                <div class="ms-3 ps-1 mt-1" style="font-size:.65rem; color:var(--c-muted); font-family:monospace;">
                                                    {{ $bloquesDelPeriodo->count() }} lotes registrados
                                                </div>
                                            </div>
                                            <div>
                                                @if($p->abierto)
                                                    <span style="color:var(--c-success); font-size:.7rem; font-weight:600;"><i class="fas fa-circle" style="font-size:.4rem; vertical-align:middle; margin-right:3px;"></i> Abierto</span>
                                                @else
                                                    <span style="color:var(--c-muted); font-size:.7rem; font-weight:600;"><i class="fas fa-circle" style="font-size:.4rem; vertical-align:middle; margin-right:3px;"></i> Cerrado</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- CONTENIDO DEL MES (Lotes/Bloques) - Oculto por defecto a menos que tenga el lote activo --}}
                                        <div class="month-content flex-column gap-1 p-2 pt-0 mt-1 border-top" id="month-content-{{ $p->id }}" style="display: {{ $mesTieneActivo ? 'flex' : 'none' }};">
                                            @if($bloquesDelPeriodo->count() > 0)
                                                <div class="mt-2">
                                                    @foreach($bloquesDelPeriodo as $bloque)
                                                        @php
                                                            $esActivo = ($bloqueActivo == $bloque->numero_bloque);
                                                        @endphp
                                                        <a href="{{ route('certificados.ingesta.index', ['bloque' => $bloque->numero_bloque]) }}"
                                                           onclick="mostrarCargandoLote()"
                                                           class="block-link d-flex align-items-center justify-content-between text-decoration-none px-2 py-1 rounded {{ $esActivo ? 'active-block' : '' }}"
                                                           style="font-size: .75rem;"
                                                           title="{{ $bloque->descripcion ?? 'Lote #'.$bloque->numero_bloque }}">
                                                            <span class="fw-semibold d-flex align-items-center gap-2">
                                                                <i class="fas fa-cube ico-cube" style="font-size: .65rem;"></i>
                                                                Lote API-{{ str_pad($bloque->numero_bloque, 4, '0', STR_PAD_LEFT) }}
                                                            </span>
                                                            @if($esActivo)
                                                                <i class="fas fa-check" style="font-size: .65rem; color: #fff;"></i>
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="mt-1 text-center" style="font-size: .65rem; color: var(--c-muted);">
                                                    Sin lotes asignados
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4 mb-0" style="font-size:.8rem;"><i class="fas fa-inbox d-block mb-2 fs-3 opacity-50"></i>No hay periodos registrados.</p>
                    @endforelse
                </div>

                <button type="button" id="btnFlipToForm" class="btn-g btn-outline-g w-100 justify-content-center mt-auto" style="padding:.6rem 0;">
                    <i class="fas fa-plus" style="color:var(--c-primary);"></i> Nuevo Periodo
                </button>
            </div>

            {{-- CARA TRASERA: FORMULARIO DE CREACIÓN --}}
            <div class="flip-face flip-back">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <button type="button" id="btnFlipToList" class="btn btn-sm btn-light border-0 shadow-none d-flex align-items-center justify-content-center" title="Volver al historial" style="width:32px;height:32px;border-radius:50%;background:var(--c-bg);">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </button>
                    <span class="fw-bold text-dark m-0" style="font-size: .95rem;">Crear Periodo</span>
                </div>

                <p class="text-muted mb-4" style="font-size:.8rem;">Abre un nuevo mes contable para asignar operaciones.</p>

                <form action="{{ route('certificados.periodos.store') }}" method="POST" class="d-flex flex-column flex-grow-1">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Año</label>
                            <input type="number" name="anio" class="form-control form-control-sm border-0 bg-light" value="{{ date('Y') }}" required min="2020" max="2099" style="box-shadow:none;">
                        </div>
                        <div class="col-6">
                            <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Mes</label>
                            <select name="mes" class="form-select form-select-sm border-0 bg-light" required style="box-shadow:none; cursor:pointer;">
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fw-semibold mb-1" style="font-size:.7rem; color:var(--c-muted); text-transform:uppercase;">Nombre del Periodo</label>
                        <input type="text" name="nombre" class="form-control form-control-sm border-0 bg-light" placeholder="Ej: AGOSTO 2026" required style="box-shadow:none;">
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px dashed var(--c-border);">
                        <span class="fw-medium text-dark" style="font-size:.85rem;">Mantener Abierto</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="abierto" id="switchAbierto" checked style="cursor:pointer; width:35px; height:18px;">
                        </div>
                    </div>

                    <button type="submit" class="btn-g btn-primary-g w-100 justify-content-center mt-auto" style="padding:.6rem 0;">
                        <i class="fas fa-save me-1"></i> Guardar Mes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Lógica para el acordeón de los meses
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.month-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('is-open');
                const targetId = this.getAttribute('data-target');
                const content = document.getElementById(targetId);

                if (content.style.display === 'none') {
                    content.style.display = 'flex';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    });
</script>
