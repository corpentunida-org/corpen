<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Editar colaborador</h2>
            <p class="text-muted mb-0">
                {{ $empleado->nombre_completo ?: 'Tercero no encontrado' }} · cod_ter: {{ $empleado->cod_ter }}
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del colaborador</h5>

            <form method="POST" action="{{ route('sgrh.empleado.update', $empleado) }}" id="formEditarColaborador">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-calendar me-1 text-primary"></i>Fecha de ingreso
                        </label>
                        <input type="date" name="fecha_ingreso" class="form-control"
                               value="{{ old('fecha_ingreso', $empleado->fecha_ingreso?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Cargo
                        </label>
                        <select name="cargo_id" id="select_cargo" class="form-select">
                            <option value="" data-salario="" @selected(old('cargo_id', $empleado->cargo_id) === null)>Sin definir</option>
                            @foreach ($cargos as $cargo)
                                <option value="{{ $cargo->id }}" data-salario="{{ $cargo->salario_base }}"
                                        @selected((string) old('cargo_id', $empleado->cargo_id) === (string) $cargo->id)>
                                    {{ $cargo->nombre }}{{ $cargo->area ? " ({$cargo->area->nombre})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-dollar-sign me-1 text-primary"></i>Salario base (del cargo)
                        </label>
                        <input type="text" id="display_salario_base" class="form-control" readonly>
                        <div class="form-text">Se toma automáticamente del cargo seleccionado.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-dollar-sign me-1 text-primary"></i>Salario asignado
                        </label>
                        <input type="number" step="0.01" min="0" name="salario_asignado" class="form-control"
                               value="{{ old('salario_asignado', $empleado->salario_asignado) }}" placeholder="Si difiere del salario base">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-droplet me-1 text-primary"></i>Tipo de sangre
                        </label>
                        <select name="tipo_sangre" class="form-select">
                            <option value="" @selected(old('tipo_sangre', $empleado->tipo_sangre) === null)>Sin definir</option>
                            @foreach (['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $grupo)
                                <option value="{{ $grupo }}" @selected(old('tipo_sangre', $empleado->tipo_sangre) === $grupo)>{{ $grupo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-shield me-1 text-primary"></i>EPS
                        </label>
                        <select id="select_eps" name="eps" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaEps as $nombre)
                                <option value="{{ $nombre }}" @selected(old('eps', $empleado->eps) === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__" @selected(!is_null($empleado->eps) && !$listaEps->contains($empleado->eps))>Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_eps" class="mt-2" style="{{ !is_null($empleado->eps) && !$listaEps->contains($empleado->eps) ? '' : 'display: none;' }}">
                            <input type="text" id="input_otra_eps" class="form-control" placeholder="Escribe el nombre de la EPS"
                                   value="{{ !is_null($empleado->eps) && !$listaEps->contains($empleado->eps) ? $empleado->eps : '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-shield me-1 text-primary"></i>ARL
                        </label>
                        <select id="select_arl" name="arl" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaArl as $nombre)
                                <option value="{{ $nombre }}" @selected(old('arl', $empleado->arl) === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__" @selected(!is_null($empleado->arl) && !$listaArl->contains($empleado->arl))>Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_arl" class="mt-2" style="{{ !is_null($empleado->arl) && !$listaArl->contains($empleado->arl) ? '' : 'display: none;' }}">
                            <input type="text" id="input_otra_arl" class="form-control" placeholder="Escribe el nombre de la ARL"
                                   value="{{ !is_null($empleado->arl) && !$listaArl->contains($empleado->arl) ? $empleado->arl : '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Fondo de pensión
                        </label>
                        <select id="select_fondo_pension" name="fondo_pension" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaFondosPension as $nombre)
                                <option value="{{ $nombre }}" @selected(old('fondo_pension', $empleado->fondo_pension) === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__" @selected(!is_null($empleado->fondo_pension) && !$listaFondosPension->contains($empleado->fondo_pension))>Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_fondo_pension" class="mt-2" style="{{ !is_null($empleado->fondo_pension) && !$listaFondosPension->contains($empleado->fondo_pension) ? '' : 'display: none;' }}">
                            <input type="text" id="input_otra_fondo_pension" class="form-control" placeholder="Escribe el nombre del fondo"
                                   value="{{ !is_null($empleado->fondo_pension) && !$listaFondosPension->contains($empleado->fondo_pension) ? $empleado->fondo_pension : '' }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-user-plus me-1 text-primary"></i>Contacto de emergencia — nombre
                        </label>
                        <input type="text" name="contacto_emergencia_nombre" class="form-control" style="text-transform: uppercase;"
                               value="{{ old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre) }}">
                        <div class="form-text">Se guarda en mayúsculas.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-phone me-1 text-primary"></i>Contacto de emergencia — teléfono
                        </label>
                        <input type="text" name="contacto_emergencia_telefono" class="form-control"
                               value="{{ old('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-message-square me-1 text-primary"></i>Observaciones
                        </label>
                        <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $empleado->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarColaborador">
                        <i class="bi bi-check-circle"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- HISTORIAL DE CONTRATOS --}}
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Historial de contratos</h5>
                <a href="{{ route('sgrh.contrato.create', ['empleado_id' => $empleado->id]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Registrar contrato
                </a>
            </div>

            @if ($empleado->contratos->isEmpty())
                <p class="text-muted small mb-0">Este colaborador no tiene contratos registrados todavía.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tipo</th>
                                <th>Inicio</th>
                                <th>Vencimiento</th>
                                <th class="text-center">Estado</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleado->contratos as $contrato)
                                <tr>
                                    <td class="ps-3 py-2">{{ $contrato->tipoContrato->nombre }}</td>
                                    <td>{{ $contrato->fecha_inicio->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $contrato->fecha_vencimiento?->format('d/m/Y') ?? 'Indefinido' }}
                                        @if ($contrato->estado === 'Activo' && $contrato->estaVencido)
                                            <span class="badge rounded-pill ms-1 px-2 py-1" style="background-color: #ffe4e6; color: #e11d48; font-weight: 600;">
                                                <i class="bi bi-exclamation-triangle"></i> Vencido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @switch($contrato->estado)
                                            @case('Activo')
                                                <span class="badge bg-success-subtle text-success">Activo</span>
                                                @break
                                            @case('Vencido')
                                                <span class="badge bg-danger-subtle text-danger">Vencido</span>
                                                @break
                                            @case('Liquidado')
                                                <span class="badge bg-secondary-subtle text-secondary">Liquidado</span>
                                                @break
                                            @default
                                                <span class="badge bg-warning-subtle text-warning">Renovado</span>
                                        @endswitch
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('sgrh.contrato.edit', $contrato) }}" class="small me-2">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                        @if ($contrato->estado === 'Activo')
                                            <form action="{{ route('sgrh.contrato.renovar', $contrato) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Cerrar este contrato y registrar uno nuevo?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="small border-0 bg-transparent p-0 text-primary">
                                                    <i class="bi bi-arrow-repeat"></i> Renovar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            // Selects con opción "Otra (especificar)": al elegirla, el select deja de
            // enviarse y el texto escrito pasa a ser el valor real del campo.
            function activarOtraOpcion(selectId, wrapperId, inputId, nombreCampo) {
                const select = document.getElementById(selectId);
                const wrapper = document.getElementById(wrapperId);
                const input = document.getElementById(inputId);
                if (!select || !wrapper || !input) {
                    return;
                }

                if (select.value === '__otra__') {
                    select.removeAttribute('name');
                    input.setAttribute('name', nombreCampo);
                }

                select.addEventListener('change', function () {
                    if (select.value === '__otra__') {
                        select.removeAttribute('name');
                        input.setAttribute('name', nombreCampo);
                        wrapper.style.display = 'block';
                        input.focus();
                    } else {
                        input.removeAttribute('name');
                        select.setAttribute('name', nombreCampo);
                        wrapper.style.display = 'none';
                        input.value = '';
                    }
                });
            }

            activarOtraOpcion('select_eps', 'wrapper_otra_eps', 'input_otra_eps', 'eps');
            activarOtraOpcion('select_arl', 'wrapper_otra_arl', 'input_otra_arl', 'arl');
            activarOtraOpcion('select_fondo_pension', 'wrapper_otra_fondo_pension', 'input_otra_fondo_pension', 'fondo_pension');

            // Salario base: solo informativo (no se envía), se lee del data-salario de la
            // opción de cargo elegida. El salario_asignado sigue siendo 100% manual.
            (function () {
                const selectCargo = document.getElementById('select_cargo');
                const displaySalarioBase = document.getElementById('display_salario_base');
                if (!selectCargo || !displaySalarioBase) {
                    return;
                }

                function actualizarSalarioBase() {
                    const salario = selectCargo.options[selectCargo.selectedIndex].getAttribute('data-salario');
                    displaySalarioBase.value = salario ? '$' + Number(salario).toLocaleString('es-CO') : '';
                }

                selectCargo.addEventListener('change', actualizarSalarioBase);
                actualizarSalarioBase();
            })();

            (function () {
                const form = document.getElementById('formEditarColaborador');
                const boton = document.getElementById('btnGuardarColaborador');
                if (!form || !boton) {
                    return;
                }
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) {
                        return;
                    }
                    boton.disabled = true;
                    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Guardando...';
                });
            })();
        </script>
    @endpush
</x-base-layout>
