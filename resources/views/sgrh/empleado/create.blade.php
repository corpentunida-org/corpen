<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Registrar colaborador</h2>
            <p class="text-muted mb-0">Busca primero el tercero por su código/cédula (cod_ter) y luego completa los datos propios del colaborador.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    {{-- PASO 1: BUSCAR TERCERO --}}
    <div class="card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">1. Identificar tercero</h5>
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <input type="text" id="buscarQ" class="form-control"
                           placeholder="Nombre, apellido o cédula (cod_ter)">
                </div>
                <div class="col-md-3">
                    <button type="button" id="btnBuscarTercero" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>

            <div id="resultadoTercero" class="mt-3"></div>
            <div id="seleccionTercero" class="mt-3"></div>
        </div>
    </div>

    {{-- PASO 2: DATOS DEL COLABORADOR --}}
    <div class="card" id="formColaborador" style="display: none;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">2. Datos del colaborador</h5>

            <form method="POST" action="{{ route('sgrh.empleado.store') }}" id="formRegistrarColaborador">
                @csrf
                <input type="hidden" name="cod_ter" id="input_cod_ter">

                <div class="alert alert-info small mb-3">
                    <i class="bi bi-info-circle"></i>
                    La fecha de ingreso, el cargo y el salario se registran con el contrato — una vez guardado el
                    colaborador, usa "Registrar contrato" en su ficha para dejarlos definidos. El colaborador nace
                    "Inactivo" y pasa a "Activo" automáticamente al registrarle su primer contrato.
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-droplet me-1 text-primary"></i>Tipo de sangre
                        </label>
                        <select name="tipo_sangre" class="form-select">
                            <option value="" @selected(old('tipo_sangre') === null)>Sin definir</option>
                            @foreach (['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $grupo)
                                <option value="{{ $grupo }}" @selected(old('tipo_sangre') === $grupo)>{{ $grupo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <hr class="my-1">
                        <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: .04em;">Contacto corporativo</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-phone me-1 text-primary"></i>Teléfono
                        </label>
                        <input type="text" name="telefono_corporativo" class="form-control" value="{{ old('telefono_corporativo') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-hash me-1 text-primary"></i>Ext.
                        </label>
                        <input type="text" name="ext_corporativo" class="form-control" value="{{ old('ext_corporativo') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-smartphone me-1 text-primary"></i>Celular
                        </label>
                        <input type="text" name="celular_corporativo" class="form-control" value="{{ old('celular_corporativo') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-mail me-1 text-primary"></i>Correo corporativo
                        </label>
                        <input type="email" name="correo_corporativo" class="form-control @error('correo_corporativo') is-invalid @enderror"
                               value="{{ old('correo_corporativo') }}">
                        @error('correo_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-mail me-1 text-primary"></i>Gmail corporativo
                        </label>
                        <input type="email" name="gmail_corporativo" class="form-control @error('gmail_corporativo') is-invalid @enderror"
                               value="{{ old('gmail_corporativo') }}">
                        @error('gmail_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-shield me-1 text-primary"></i>EPS
                        </label>
                        <select id="select_eps" name="eps" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaEps as $nombre)
                                <option value="{{ $nombre }}" @selected(old('eps') === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__">Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_eps" class="mt-2" style="display: none;">
                            <input type="text" id="input_otra_eps" class="form-control" placeholder="Escribe el nombre de la EPS">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-shield me-1 text-primary"></i>ARL
                        </label>
                        <select id="select_arl" name="arl" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaArl as $nombre)
                                <option value="{{ $nombre }}" @selected(old('arl') === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__">Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_arl" class="mt-2" style="display: none;">
                            <input type="text" id="input_otra_arl" class="form-control" placeholder="Escribe el nombre de la ARL">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Fondo de pensión
                        </label>
                        <select id="select_fondo_pension" name="fondo_pension" class="form-select">
                            <option value="">Sin definir</option>
                            @foreach ($listaFondosPension as $nombre)
                                <option value="{{ $nombre }}" @selected(old('fondo_pension') === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__">Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_fondo_pension" class="mt-2" style="display: none;">
                            <input type="text" id="input_otra_fondo_pension" class="form-control" placeholder="Escribe el nombre del fondo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Fondo de pensión 2
                        </label>
                        <select id="select_fondo_pension_2" name="fondo_pension_2" class="form-select">
                            <option value="">Sin definir</option>
                            <option value="No aplica" @selected(old('fondo_pension_2') === 'No aplica')>No aplica</option>
                            @foreach ($listaFondosPension as $nombre)
                                <option value="{{ $nombre }}" @selected(old('fondo_pension_2') === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__">Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_fondo_pension_2" class="mt-2" style="display: none;">
                            <input type="text" id="input_otra_fondo_pension_2" class="form-control" placeholder="Escribe el nombre del fondo">
                        </div>
                        <div class="form-text">Preparación para la reforma pensional (pilar complementario).</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-user-plus me-1 text-primary"></i>Contacto de emergencia — nombre
                        </label>
                        <input type="text" name="contacto_emergencia_nombre" class="form-control" style="text-transform: uppercase;" value="{{ old('contacto_emergencia_nombre') }}">
                        <div class="form-text">Se guarda en mayúsculas.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-phone me-1 text-primary"></i>Contacto de emergencia — teléfono
                        </label>
                        <input type="text" name="contacto_emergencia_telefono" class="form-control" value="{{ old('contacto_emergencia_telefono') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-message-square me-1 text-primary"></i>Observaciones
                        </label>
                        <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="col-12" id="marcarEmpleadoWrapper" style="display: none;">
                        <div class="form-check bg-light border rounded p-3">
                            <input class="form-check-input" type="checkbox" name="marcar_tip_prv" value="1" id="marcar_tip_prv">
                            <label class="form-check-label" for="marcar_tip_prv">
                                Este tercero no está clasificado como <strong>"Empleado"</strong> en el maestro de
                                terceros (campo "Tipo de Tercero"). Márcalo solo si quieres actualizarlo ahora —
                                si el tercero ya tiene otra clasificación (ej. Pastor), marcar esta casilla la
                                reemplazará.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnRegistrarColaborador">
                        <i class="bi bi-check-circle"></i> Registrar colaborador
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            const puedeEditarTercero = @can('sgrh.tercero.edit') true @else false @endcan;

            // Selects con opción "Otra (especificar)": al elegirla, el select deja de
            // enviarse y el texto escrito pasa a ser el valor real del campo.
            function activarOtraOpcion(selectId, wrapperId, inputId, nombreCampo) {
                const select = document.getElementById(selectId);
                const wrapper = document.getElementById(wrapperId);
                const input = document.getElementById(inputId);
                if (!select || !wrapper || !input) {
                    return;
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
            activarOtraOpcion('select_fondo_pension_2', 'wrapper_otra_fondo_pension_2', 'input_otra_fondo_pension_2', 'fondo_pension_2');

            function buscarTerceros() {
                const q = document.getElementById('buscarQ').value.trim();
                const resultadoDiv = document.getElementById('resultadoTercero');
                const seleccionDiv = document.getElementById('seleccionTercero');
                const formColaborador = document.getElementById('formColaborador');

                if (!q) {
                    toastr.error('Escribe un nombre, apellido o cédula para buscar.');
                    return;
                }

                const boton = document.getElementById('btnBuscarTercero');
                const botonHtmlOriginal = boton.innerHTML;
                boton.disabled = true;
                boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Buscando...';

                resultadoDiv.innerHTML = '<span class="text-muted">Buscando...</span>';
                seleccionDiv.innerHTML = '';
                formColaborador.style.display = 'none';

                fetch(`{{ route('sgrh.empleado.buscarTercero') }}?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json().then(body => ({ ok: response.ok, body })))
                    .finally(function () {
                        boton.disabled = false;
                        boton.innerHTML = botonHtmlOriginal;
                    })
                    .then(({ ok, body }) => {
                        if (!ok || body.status !== 'success') {
                            resultadoDiv.innerHTML = `<div class="alert alert-warning mb-0">${body.message}</div>`;
                            return;
                        }

                        const filas = body.data.map(t => `
                            <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center ${t.ya_registrado ? 'disabled' : ''}"
                                    data-cod-ter="${t.cod_ter}"
                                    data-nombre="${(t.nombre_completo || '').replace(/"/g, '&quot;')}"
                                    data-clasificado="${t.clasificado_empleado ? '1' : '0'}"
                                    data-fecha-actualizacion="${t.fecha_actualizacion || ''}"
                                    data-desactualizado="${t.desactualizado ? '1' : '0'}"
                                    ${t.ya_registrado ? 'disabled' : ''}>
                                <span>
                                    <strong>${t.nombre_completo || '(sin nombre registrado)'}</strong><br>
                                    <span class="small text-muted">cod_ter: ${t.cod_ter} · ${t.email || 'sin correo'} · ${t.celular || 'sin celular'}</span>
                                    ${!t.clasificado_empleado && !t.ya_registrado ? '<br><span class="badge bg-warning-subtle text-warning mt-1">Sin clasificar como Empleado</span>' : ''}
                                    ${t.desactualizado && !t.ya_registrado ? '<br><span class="badge rounded-pill mt-1 px-2 py-1" style="background-color: #ffe4e6; color: #e11d48; font-weight: 600;"><i class="bi bi-exclamation-triangle"></i> Información de usuario requiere actualizar</span>' : ''}
                                </span>
                                ${t.ya_registrado ? '<span class="badge bg-secondary">Ya es colaborador</span>' : '<i class="bi bi-chevron-right"></i>'}
                            </button>`).join('');

                        resultadoDiv.innerHTML = `<div class="list-group">${filas}</div>`;

                        resultadoDiv.querySelectorAll('button[data-cod-ter]:not([disabled])').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                const codTer = btn.getAttribute('data-cod-ter');
                                const nombre = btn.getAttribute('data-nombre');
                                const clasificadoEmpleado = btn.getAttribute('data-clasificado') === '1';
                                const fechaActualizacion = btn.getAttribute('data-fecha-actualizacion');
                                const desactualizado = btn.getAttribute('data-desactualizado') === '1';

                                const fechaTexto = fechaActualizacion
                                    ? fechaActualizacion.split('-').reverse().join('/')
                                    : 'nunca registrada';

                                const enlaceEditar = puedeEditarTercero
                                    ? `<a href="{{ url('sgrh/terceros') }}/${codTer}/edit" target="_blank"
                                           class="btn btn-sm flex-shrink-0 ms-3"
                                           style="${desactualizado ? 'background-color: #e11d48; color: #fff; font-weight: bold;' : 'background: transparent; border: 1px solid #198754; color: #198754;'}">
                                           <i class="bi bi-pencil-square"></i> ACTUALIZAR / EDITAR TERCERO
                                       </a>`
                                    : '';

                                // Cuando está desactualizado, todo el recuadro cambia a rosado en
                                // vez de mezclar el aviso rojo dentro de la caja verde de éxito.
                                const estiloRecuadro = desactualizado
                                    ? 'background-color: #ffe4e6; border: 1px solid #fbb6c2; color: #e11d48;'
                                    : '';
                                const claseRecuadro = desactualizado ? 'alert' : 'alert alert-success';

                                seleccionDiv.innerHTML = `
                                    <div class="${claseRecuadro} d-flex justify-content-between align-items-center mb-0" style="${estiloRecuadro}">
                                        <span>
                                            Tercero seleccionado: <strong>${nombre}</strong> (cod_ter: ${codTer})<br>
                                            <span class="small ${desactualizado ? 'fw-bold' : 'text-muted'}">
                                                ${desactualizado
                                                    ? '<i class="bi bi-exclamation-triangle"></i> Información de usuario requiere actualizar (última actualización: ' + fechaTexto + ')'
                                                    : '<i class="bi bi-calendar-check"></i> Última actualización: ' + fechaTexto}
                                            </span>
                                        </span>
                                        ${enlaceEditar}
                                    </div>
                                    ${desactualizado
                                        ? '<div class="alert alert-danger mt-2 mb-0 small"><i class="bi bi-lock-fill"></i> No puedes registrarlo como colaborador hasta actualizar sus datos. Actualízalos en la pestaña nueva y vuelve a buscarlo aquí para continuar.</div>'
                                        : ''}`;

                                document.getElementById('input_cod_ter').value = codTer;
                                // Bloqueado mientras el tercero esté desactualizado — el
                                // backend también lo rechaza igual si se fuerza el envío.
                                formColaborador.style.display = desactualizado ? 'none' : 'block';

                                const marcarWrapper = document.getElementById('marcarEmpleadoWrapper');
                                const marcarCheckbox = document.getElementById('marcar_tip_prv');
                                if (clasificadoEmpleado) {
                                    marcarWrapper.style.display = 'none';
                                    marcarCheckbox.checked = false;
                                } else {
                                    marcarWrapper.style.display = 'block';
                                }
                            });
                        });
                    })
                    .catch(() => {
                        resultadoDiv.innerHTML = '<div class="alert alert-danger mb-0">Ocurrió un error al buscar el tercero.</div>';
                    });
            }

            document.getElementById('btnBuscarTercero').addEventListener('click', buscarTerceros);
            document.getElementById('buscarQ').addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarTerceros();
                }
            });

            // Indicador de "procesando" al registrar el colaborador.
            (function () {
                const form = document.getElementById('formRegistrarColaborador');
                const boton = document.getElementById('btnRegistrarColaborador');
                if (!form || !boton) {
                    return;
                }
                form.addEventListener('submit', function () {
                    if (!form.checkValidity()) {
                        return;
                    }
                    boton.disabled = true;
                    boton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Registrando...';
                });
            })();
        </script>
    @endpush
</x-base-layout>
