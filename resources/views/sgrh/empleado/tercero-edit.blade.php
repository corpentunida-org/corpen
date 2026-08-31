<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Datos del tercero</h2>
            <p class="text-muted mb-0">
                cod_ter: <strong>{{ $tercero->cod_ter }}</strong> ·
                Vista acotada para RR. HH. — solo identificación, datos personales, ubicación y contacto.
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver a colaboradores
            </a>
        </div>
    </div>

    @if ($desactualizado ?? false)
        <div class="alert d-flex align-items-center gap-3 mb-4" style="background-color: #ffe4e6; border: 1px solid #fbb6c2; color: #e11d48; border-radius: 12px;">
            <i class="bi bi-exclamation-triangle-fill fs-3"></i>
            <div>
                <strong>Información de usuario requiere actualizar</strong>
                <span class="d-block small">Última actualización: {{ $tercero->fec_act ? \Illuminate\Support\Carbon::parse($tercero->fec_act)->format('d/m/Y') : 'nunca registrada' }} — revisa y guarda sus datos para que quede al día. Esta alerta desaparece automáticamente al guardar.</span>
            </div>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('sgrh.tercero.update', $tercero->cod_ter) }}" class="card-body" id="formTerceroEdit">
            @csrf
            @method('PUT')

            @include('sgrh.empleado._tercero-campos', ['editable' => true])

            <div class="d-flex flex-row-reverse gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4" id="btnGuardarTercero">
                    <i class="feather-save me-2"></i> Guardar cambios
                </button>
                <a href="{{ route('sgrh.empleado.index') }}" class="btn btn-light">Cancelar</a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            // Se declara una sola vez y se reutiliza tanto para la cascada Departamento→
            // Municipio como para el autocompletar de Lugar de expedición/Lugar de nacimiento
            // — ya viene cargada en la página, no hace falta consultarla de nuevo.
            const municipiosData = @json($municipios);

            // "Dígito de verificación" solo aplica cuando el tipo de documento es NIT (31).
            (function () {
                const campoTdoc = document.getElementById('campo_tdoc');
                const campoDv = document.getElementById('campo_dv');
                if (!campoTdoc || !campoDv) {
                    return;
                }

                function actualizarDv() {
                    const esNit = campoTdoc.value === '31';
                    campoDv.disabled = !esNit;
                    campoDv.required = esNit;
                    if (!esNit) {
                        campoDv.classList.remove('is-invalid');
                    }
                }

                campoTdoc.addEventListener('change', actualizarDv);
                actualizarDv();
            })();

            // Municipio se filtra según el Departamento elegido.
            (function () {
                const campoDpto = document.getElementById('campo_dpto');
                const campoMun = document.getElementById('campo_mun');
                if (!campoDpto || !campoMun) {
                    return;
                }

                function poblarMunicipios(idDepartamento, seleccionar) {
                    const filtrados = municipiosData.filter(function (m) {
                        return String(m.id_departamento) === String(idDepartamento);
                    });
                    let html = '<option value="">Sin definir</option>';
                    filtrados.forEach(function (m) {
                        const marcado = seleccionar && String(m.id) === String(seleccionar) ? 'selected' : '';
                        html += `<option value="${m.id}" ${marcado}>${m.nombre}</option>`;
                    });
                    campoMun.innerHTML = html;
                }

                const valorInicial = campoMun.getAttribute('data-valor-actual');
                if (campoDpto.value) {
                    poblarMunicipios(campoDpto.value, valorInicial);
                }

                campoDpto.addEventListener('change', function () {
                    poblarMunicipios(campoDpto.value, null);
                });
            })();

            // Autocompletar de municipio para Lugar de expedición / Lugar de nacimiento.
            // Sigue siendo texto libre (no obliga a elegir una sugerencia) — solo ayuda a
            // escribir un nombre de municipio consistente. Busca desde 3 caracteres, sobre
            // los mismos 1.125 municipios ya cargados en la página (sin consultas nuevas).
            function activarAutocompleteMunicipio(campoId, sugerenciasId) {
                const campo = document.getElementById(campoId);
                const sugerencias = document.getElementById(sugerenciasId);
                if (!campo || !sugerencias) {
                    return;
                }

                campo.addEventListener('input', function () {
                    const texto = campo.value.trim().toLowerCase();
                    if (texto.length < 3) {
                        sugerencias.innerHTML = '';
                        return;
                    }

                    const coincidencias = municipiosData
                        .filter(function (m) { return m.nombre.toLowerCase().includes(texto); })
                        .slice(0, 8);

                    if (coincidencias.length === 0) {
                        sugerencias.innerHTML = '';
                        return;
                    }

                    sugerencias.innerHTML = coincidencias.map(function (m) {
                        return `<button type="button" class="list-group-item list-group-item-action py-1 px-2 small">${m.nombre}</button>`;
                    }).join('');

                    sugerencias.querySelectorAll('button').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            campo.value = btn.textContent;
                            sugerencias.innerHTML = '';
                        });
                    });
                });

                // Cierra las sugerencias al hacer clic fuera del campo.
                document.addEventListener('click', function (e) {
                    if (e.target !== campo && !sugerencias.contains(e.target)) {
                        sugerencias.innerHTML = '';
                    }
                });
            }

            activarAutocompleteMunicipio('campo_lugar_expcc', 'sugerencias_lugar_expcc');
            activarAutocompleteMunicipio('campo_lugar_naci', 'sugerencias_lugar_naci');

            // Indicador de "procesando" al guardar — la carga puede tardar por la conexión
            // a la base de datos remota, esto evita doble clic y confirma que sí se envió.
            (function () {
                const form = document.getElementById('formTerceroEdit');
                const boton = document.getElementById('btnGuardarTercero');
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
