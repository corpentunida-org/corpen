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

            <form method="POST" action="{{ route('sgrh.empleado.store') }}">
                @csrf
                <input type="hidden" name="cod_ter" id="input_cod_ter">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="activo" selected>Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo de sangre</label>
                        <input type="text" name="tipo_sangre" class="form-control" maxlength="5" value="{{ old('tipo_sangre') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">EPS</label>
                        <input type="text" name="eps" class="form-control" value="{{ old('eps') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ARL</label>
                        <input type="text" name="arl" class="form-control" value="{{ old('arl') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fondo de pensión</label>
                        <input type="text" name="fondo_pension" class="form-control" value="{{ old('fondo_pension') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Contacto de emergencia — nombre</label>
                        <input type="text" name="contacto_emergencia_nombre" class="form-control" value="{{ old('contacto_emergencia_nombre') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contacto de emergencia — teléfono</label>
                        <input type="text" name="contacto_emergencia_telefono" class="form-control" value="{{ old('contacto_emergencia_telefono') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Observaciones</label>
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
                    <button type="submit" class="btn btn-primary px-4">
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

            function buscarTerceros() {
                const q = document.getElementById('buscarQ').value.trim();
                const resultadoDiv = document.getElementById('resultadoTercero');
                const seleccionDiv = document.getElementById('seleccionTercero');
                const formColaborador = document.getElementById('formColaborador');

                if (!q) {
                    toastr.error('Escribe un nombre, apellido o cédula para buscar.');
                    return;
                }

                resultadoDiv.innerHTML = '<span class="text-muted">Buscando...</span>';
                seleccionDiv.innerHTML = '';
                formColaborador.style.display = 'none';

                fetch(`{{ route('sgrh.empleado.buscarTercero') }}?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json().then(body => ({ ok: response.ok, body })))
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
                                    ${t.ya_registrado ? 'disabled' : ''}>
                                <span>
                                    <strong>${t.nombre_completo || '(sin nombre registrado)'}</strong><br>
                                    <span class="small text-muted">cod_ter: ${t.cod_ter} · ${t.email || 'sin correo'} · ${t.celular || 'sin celular'}</span>
                                    ${!t.clasificado_empleado && !t.ya_registrado ? '<br><span class="badge bg-warning-subtle text-warning mt-1">Sin clasificar como Empleado</span>' : ''}
                                </span>
                                ${t.ya_registrado ? '<span class="badge bg-secondary">Ya es colaborador</span>' : '<i class="bi bi-chevron-right"></i>'}
                            </button>`).join('');

                        resultadoDiv.innerHTML = `<div class="list-group">${filas}</div>`;

                        resultadoDiv.querySelectorAll('button[data-cod-ter]:not([disabled])').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                const codTer = btn.getAttribute('data-cod-ter');
                                const nombre = btn.getAttribute('data-nombre');
                                const clasificadoEmpleado = btn.getAttribute('data-clasificado') === '1';

                                const enlaceEditar = puedeEditarTercero
                                    ? `<a href="{{ url('sgrh/terceros') }}/${codTer}/edit" target="_blank"
                                           class="btn btn-sm btn-outline-success ms-3">
                                           <i class="bi bi-pencil-square"></i> ¿Datos incorrectos? Editar tercero
                                       </a>`
                                    : '';

                                seleccionDiv.innerHTML = `
                                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-0">
                                        <span>Tercero seleccionado: <strong>${nombre}</strong> (cod_ter: ${codTer})</span>
                                        ${enlaceEditar}
                                    </div>`;

                                document.getElementById('input_cod_ter').value = codTer;
                                formColaborador.style.display = 'block';

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
        </script>
    @endpush
</x-base-layout>
