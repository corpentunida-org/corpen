<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Solicitar vacaciones</h2>
            <p class="text-muted mb-0">Selecciona el rango de fechas en el calendario.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.vacacion.solicitud.mis') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Mis solicitudes
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="text-muted small text-uppercase mb-1">Tu saldo actual</p>
                    @if ($saldo === null)
                        <h4 class="fw-bold text-secondary mb-0">Sin determinar</h4>
                        <p class="small text-muted mt-1 mb-0">Contacta a RRHH: tu fecha de ingreso no está registrada.</p>
                    @else
                        <h3 class="fw-bold {{ $saldo < 0 ? 'text-danger' : 'text-success' }} mb-0">{{ $saldo }}</h3>
                        <p class="small text-muted mb-0">días disponibles</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($saldo !== null)
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('sgrh.vacacion.solicitud.store') }}" id="formSolicitud">
                    @csrf
                    <p class="small text-muted mb-2">
                        <span class="badge bg-light text-dark border me-1">&nbsp;&nbsp;</span> Hábil
                        <span class="badge ms-2 me-1" style="background-color: #fde68a;">&nbsp;&nbsp;</span> Fin de semana
                        <span class="badge ms-2 me-1" style="background-color: #fca5a5;">&nbsp;&nbsp;</span> Festivo
                    </p>
                    <div id="calendarSeleccion" class="mb-4" style="max-width: 760px;"></div>

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">Fecha de inicio</label>
                            <input type="date" name="fecha_inicio" id="input_fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio') }}" required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">Fecha de fin</label>
                            <input type="date" name="fecha_fin" id="input_fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin') }}" required>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-dark small text-uppercase">Días hábiles</label>
                            <input type="text" id="resumen_dias" class="form-control" disabled value="—">
                        </div>

                        @can('sgrh.vacacion.solicitud.rrhh')
                            <div class="col-md-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="es_adelantada" id="es_adelantada" class="form-check-input" value="1" {{ old('es_adelantada') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="es_adelantada">Autorizar como adelantada</label>
                                </div>
                            </div>
                        @endcan

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small text-uppercase">Observaciones (opcional)</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle"></i> Enviar solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('style')
        <style>
            #calendarSeleccion .fc-day-sat,
            #calendarSeleccion .fc-day-sun {
                background-color: #fef3c7;
            }
            #calendarSeleccion .dia-festivo {
                background-color: #fecaca !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const inputInicio = document.getElementById('input_fecha_inicio');
                const inputFin = document.getElementById('input_fecha_fin');
                const resumenDias = document.getElementById('resumen_dias');

                function actualizarResumen() {
                    if (!inputInicio.value || !inputFin.value) {
                        resumenDias.value = '—';
                        return;
                    }
                    const inicio = new Date(inputInicio.value + 'T00:00:00');
                    const fin = new Date(inputFin.value + 'T00:00:00');
                    if (fin < inicio) {
                        resumenDias.value = '—';
                        return;
                    }
                    let dias = 0;
                    const cursor = new Date(inicio);
                    while (cursor <= fin) {
                        const diaSemana = cursor.getDay();
                        if (diaSemana !== 0 && diaSemana !== 6) {
                            dias++;
                        }
                        cursor.setDate(cursor.getDate() + 1);
                    }
                    resumenDias.value = dias + ' día(s) hábiles (aproximado, festivos no incluidos)';
                }

                inputInicio.addEventListener('change', actualizarResumen);
                inputFin.addEventListener('change', actualizarResumen);
                actualizarResumen();

                const calendarEl = document.getElementById('calendarSeleccion');
                if (calendarEl && typeof FullCalendar !== 'undefined') {
                    // Cache de festivos por año — un año ya cargado no se vuelve a pedir al
                    // servidor cada vez que el calendario re-renderiza (cambio de mes dentro del
                    // mismo año, selección, etc.).
                    const festivosPorAnio = {};

                    function cargarFestivosDelAnio(anio) {
                        if (festivosPorAnio[anio]) {
                            return Promise.resolve(festivosPorAnio[anio]);
                        }
                        return fetch(`{{ route('sgrh.vacacion.festivo.fechas') }}?anio=${anio}`)
                            .then(r => r.json())
                            .then(fechas => {
                                festivosPorAnio[anio] = fechas;
                                return fechas;
                            })
                            .catch(() => (festivosPorAnio[anio] = []));
                    }

                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        locale: 'es',
                        initialView: 'dayGridMonth',
                        selectable: true,
                        height: 'auto',
                        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                        select: function (info) {
                            inputInicio.value = info.startStr;
                            // FullCalendar entrega 'end' exclusivo en selección de días completos.
                            const finInclusive = new Date(info.end);
                            finInclusive.setDate(finInclusive.getDate() - 1);
                            inputFin.value = finInclusive.toISOString().slice(0, 10);
                            actualizarResumen();
                        },
                        dayCellClassNames: function (arg) {
                            const anio = arg.date.getFullYear();
                            const fechaStr = arg.date.toISOString().slice(0, 10);
                            return (festivosPorAnio[anio] || []).includes(fechaStr) ? ['dia-festivo'] : [];
                        },
                        datesSet: function (info) {
                            // Se dispara al cargar y cada vez que cambia el rango visible (mes
                            // anterior/siguiente) — carga los festivos de los años que entren en
                            // ese rango (normalmente 1, hasta 2 si el mes visible cruza el año)
                            // y vuelve a pintar una vez lleguen.
                            const anios = new Set([info.start.getFullYear(), info.end.getFullYear()]);
                            Promise.all([...anios].map(cargarFestivosDelAnio)).then(() => calendar.render());
                        },
                    });
                    calendar.render();
                }

                @if ($errors->any())
                    toastr.error("{{ $errors->first() }}");
                @endif
            });
        </script>
    @endpush
</x-base-layout>
