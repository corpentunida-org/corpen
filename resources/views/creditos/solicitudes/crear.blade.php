<x-base-layout>
    @section('titlepage', 'Nueva Solicitud de Crédito')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Solicitud de Crédito de Alta Cuantía</h5>
                    <div class="card-header-action">
                        <a href="{{ route('creditos.credito.index') }}" class="btn btn-sm btn-secondary">
                            <i class="feather-arrow-left me-1"></i> Volver a la lista
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>Por favor, corrige los siguientes errores:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('creditos.solicitud.store') }}" method="POST" enctype="multipart/form-data" id="formSolicitud">
                        @csrf

                        {{-- ================= SOLICITUD ================= --}}
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Solicitud</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Crédito</label>
                                <select name="tipo_cred" id="tipo_cred" class="form-select @error('tipo_cred') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('tipo_cred') ? '' : 'selected' }}>-- Selecciona --</option>
                                    <option value="LI" {{ old('tipo_cred') == 'LI' ? 'selected' : '' }}>Libre Inversión</option>
                                    <option value="HIP" {{ old('tipo_cred') == 'HIP' ? 'selected' : '' }}>Hipotecario</option>
                                </select>
                                @error('tipo_cred') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-text text-muted">Libre Inversión: hasta 80% del fondo de retiro, 60 meses máx. Hipotecario: hasta $300.000.000, 15 años máx.</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Valor Solicitado</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="1" min="0" name="vr_soli" class="form-control @error('vr_soli') is-invalid @enderror" value="{{ old('vr_soli') }}" required>
                                </div>
                                @error('vr_soli') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo de Cuota</label>
                                <div class="d-flex gap-3 pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_cuota" id="cuotaFija" value="FIJA" {{ old('tipo_cuota') == 'FIJA' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="cuotaFija">Fija</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_cuota" id="cuotaVariable" value="VARIABLE" {{ old('tipo_cuota') == 'VARIABLE' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cuotaVariable">Variable</label>
                                    </div>
                                </div>
                                @error('tipo_cuota') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6" id="plazoLibreInversion" style="display:none;">
                                <label class="form-label">Plazo (Libre Inversión)</label>
                                <select id="plazoLibreInversionSelect" class="form-select">
                                    <option value="12">12 meses</option>
                                    <option value="24">24 meses</option>
                                    <option value="36">36 meses</option>
                                    <option value="48">48 meses</option>
                                    <option value="60">60 meses</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="plazoHipotecario" style="display:none;">
                                <label class="form-label">Plazo (Hipotecario)</label>
                                <select id="plazoHipotecarioSelect" class="form-select">
                                    <option value="10">10 años</option>
                                    <option value="12">12 años</option>
                                    <option value="15">15 años</option>
                                </select>
                            </div>
                            <input type="hidden" name="plazo" id="plazo" value="{{ old('plazo') }}">

                            <div class="col-md-8">
                                <label class="form-label">¿Actualmente tiene un crédito en Corpentunida? ¿Cuál o cuáles?</label>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="tiene_credito_actual" id="tiene_credito_actual" value="1" {{ old('tiene_credito_actual') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tiene_credito_actual">Sí, tiene crédito(s) actual(es)</label>
                                </div>
                                <input type="text" name="cual_credito_actual" id="cual_credito_actual" class="form-control @error('cual_credito_actual') is-invalid @enderror" placeholder="¿Cuál o cuáles?" value="{{ old('cual_credito_actual') }}" style="display:none;">
                                @error('cual_credito_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">¿Para qué necesita el crédito?</label>
                                <textarea name="destino" class="form-control @error('destino') is-invalid @enderror" rows="2" required>{{ old('destino') }}</textarea>
                                @error('destino') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- ================= INFORMACIÓN DEL PASTOR ================= --}}
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Información del Pastor</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Pastor (busca por nombre o cédula)</label>
                                <select name="cod_ter" id="cod_ter" class="form-select @error('cod_ter') is-invalid @enderror" required></select>
                                @error('cod_ter') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="text" id="display_fec_nac" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Edad</label>
                                <input type="text" id="display_edad" class="form-control" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Distrito</label>
                                <input type="text" id="display_cod_dist" class="form-control" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Celular</label>
                                <input type="text" name="cel" id="cel" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Congregación que administra</label>
                                <input type="text" id="display_congrega" class="form-control" readonly>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Dirección Domicilio</label>
                                <input type="text" name="dir" id="dir" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Municipio</label>
                                <input type="text" name="ciudad" id="ciudad" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Departamento</label>
                                <input type="text" name="depa" id="depa" class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fecha Ingreso a Corpentunida</label>
                                <input type="date" name="fec_ing" id="fec_ing" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha Ingreso al Ministerio</label>
                                <input type="date" name="fec_minis" id="fec_minis" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Peso (Kg)</label>
                                <input type="number" step="0.1" name="peso" id="peso" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Estatura (m)</label>
                                <input type="number" step="0.01" name="estatura" id="estatura" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">EPS</label>
                                <input type="text" name="eps" id="eps" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Detalle de enfermedades que ha padecido (año de diagnóstico y tratamiento)</label>
                                <textarea name="detalle_enfermedades" id="detalle_enfermedades" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nombre Cónyuge</label>
                                <input type="text" name="nom_conyug" id="nom_conyug" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">C.C. Cónyuge</label>
                                <input type="text" name="id_conyuge" id="id_conyuge" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Celular Cónyuge</label>
                                <input type="text" name="cel_conyu" id="cel_conyu" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Nº Hijos</label>
                                <input type="number" min="0" name="num_hijos" id="num_hijos" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Personas a Cargo</label>
                                <input type="number" min="0" name="personas_cargo" id="personas_cargo" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Vivienda</label>
                                <div class="d-flex gap-3 pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_vivienda" id="viviendaPropia" value="propia">
                                        <label class="form-check-label" for="viviendaPropia">Casa Propia</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipo_vivienda" id="viviendaPastoral" value="pastoral">
                                        <label class="form-check-label" for="viviendaPastoral">Casa Pastoral</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8" id="congregacionPagaWrap" style="display:none;">
                                <label class="form-label">La congregación paga:</label>
                                <div class="d-flex gap-3 align-items-center pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="congregacion_paga_servicios" id="congregacion_paga_servicios" value="1">
                                        <label class="form-check-label" for="congregacion_paga_servicios">Servicios</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="congregacion_paga_arriendo" id="congregacion_paga_arriendo" value="1">
                                        <label class="form-check-label" for="congregacion_paga_arriendo">Arriendo</label>
                                    </div>
                                    <input type="text" name="congregacion_paga_otros" id="congregacion_paga_otros" class="form-control" placeholder="Otros / especificar" style="max-width: 220px;">
                                </div>
                            </div>
                        </div>

                        {{-- ================= INGRESOS ================= --}}
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Ingresos Mensuales</h6>
                        <div class="row g-3 mb-2">
                            @foreach ($catIngresos as $cat)
                                <div class="col-md-4">
                                    <label class="form-label">{{ $cat->nombre }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" min="0" step="1" name="ingresos[{{ $cat->id }}]" class="form-control monto-ingreso" value="{{ old('ingresos.' . $cat->id) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-end fw-bold mb-4">Total Ingresos: <span id="totalIngresos">$0</span></p>

                        {{-- ================= EGRESOS ================= --}}
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Egresos Mensuales</h6>
                        <div class="row g-3 mb-2">
                            @foreach ($catEgresos as $cat)
                                <div class="col-md-4">
                                    <label class="form-label">{{ $cat->nombre }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" min="0" step="1" name="egresos[{{ $cat->id }}]" class="form-control monto-egreso" value="{{ old('egresos.' . $cat->id) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-end fw-bold mb-4">Total Egresos: <span id="totalEgresos">$0</span></p>

                        {{-- ================= AUTORIZACIÓN Y FIRMA ================= --}}
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Autorización y Soporte</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input @error('protec_dato') is-invalid @enderror" type="checkbox" name="protec_dato" id="protec_dato" value="1" {{ old('protec_dato') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="protec_dato">
                                        Autorizo permanente e irrevocablemente a CORPENTUNIDA para que consulte, procese, reporte, suministre, retire y actualice mis datos personales en las centrales de información de riesgo crediticio, conforme a la política de datos de la entidad.
                                    </label>
                                    @error('protec_dato') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Formulario Físico Firmado (PDF o foto)</label>
                                <input type="file" name="formulario_firmado" class="form-control @error('formulario_firmado') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                @error('formulario_firmado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="form-text text-muted">Diligencia este formulario, hazlo firmar por el pastor y su esposa, y adjunta aquí la foto o escaneo del documento firmado.</small>
                            </div>
                        </div>

                        <div class="card-footer text-end mt-4">
                            <a href="{{ route('creditos.credito.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof $.fn.select2 !== 'function') {
            console.error('Select2 no está disponible.');
            return;
        }

        $('#cod_ter').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Busca un pastor por nombre o cédula --',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('creditos.credito.buscar-tercero') }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term, page: params.page || 1 }),
                processResults: data => ({
                    results: data.results,
                    pagination: { more: data.pagination.more }
                })
            }
        });

        $('#cod_ter').on('change', function () {
            const codTer = $(this).val();
            if (!codTer) return;
            fetch('{{ url('creditos/solicitudes/tercero') }}/' + codTer)
                .then(r => r.json())
                .then(d => {
                    document.getElementById('display_fec_nac').value = d.fec_nac || '';
                    document.getElementById('display_edad').value = d.edad !== null ? d.edad + ' años' : '';
                    document.getElementById('display_cod_dist').value = d.cod_dist || '';
                    document.getElementById('display_congrega').value = d.congrega || '';
                    document.getElementById('cel').value = d.cel || '';
                    document.getElementById('whatsapp').value = d.whatsapp || '';
                    document.getElementById('email').value = d.email || '';
                    document.getElementById('dir').value = d.dir || '';
                    document.getElementById('ciudad').value = d.ciudad || '';
                    document.getElementById('depa').value = d.depa || '';
                    document.getElementById('fec_ing').value = d.fec_ing || '';
                    document.getElementById('fec_minis').value = d.fec_minis || '';
                    document.getElementById('peso').value = d.peso || '';
                    document.getElementById('estatura').value = d.estatura || '';
                    document.getElementById('eps').value = d.eps || '';
                    document.getElementById('detalle_enfermedades').value = d.detalle_enfermedades || '';
                    document.getElementById('nom_conyug').value = d.nom_conyug || '';
                    document.getElementById('id_conyuge').value = d.id_conyuge || '';
                    document.getElementById('cel_conyu').value = d.cel_conyu || '';
                    document.getElementById('num_hijos').value = d.num_hijos || '';
                    document.getElementById('personas_cargo').value = d.personas_cargo || '';
                    if (d.tipo_vivienda) {
                        const radio = document.getElementById(d.tipo_vivienda === 'propia' ? 'viviendaPropia' : 'viviendaPastoral');
                        if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change')); }
                    }
                    document.getElementById('congregacion_paga_servicios').checked = !!d.congregacion_paga_servicios;
                    document.getElementById('congregacion_paga_arriendo').checked = !!d.congregacion_paga_arriendo;
                    document.getElementById('congregacion_paga_otros').value = d.congregacion_paga_otros || '';
                })
                .catch(() => {});
        });

        // Plazo según tipo de crédito
        const tipoCred = document.getElementById('tipo_cred');
        const plazoLI = document.getElementById('plazoLibreInversion');
        const plazoHip = document.getElementById('plazoHipotecario');
        const plazoLISelect = document.getElementById('plazoLibreInversionSelect');
        const plazoHipSelect = document.getElementById('plazoHipotecarioSelect');
        const plazoHidden = document.getElementById('plazo');

        function actualizarPlazo() {
            if (tipoCred.value === 'LI') {
                plazoLI.style.display = '';
                plazoHip.style.display = 'none';
                plazoHidden.value = plazoLISelect.value;
            } else if (tipoCred.value === 'HIP') {
                plazoLI.style.display = 'none';
                plazoHip.style.display = '';
                plazoHidden.value = plazoHipSelect.value;
            } else {
                plazoLI.style.display = 'none';
                plazoHip.style.display = 'none';
                plazoHidden.value = '';
            }
        }
        tipoCred.addEventListener('change', actualizarPlazo);
        plazoLISelect.addEventListener('change', () => plazoHidden.value = plazoLISelect.value);
        plazoHipSelect.addEventListener('change', () => plazoHidden.value = plazoHipSelect.value);
        actualizarPlazo();

        // ¿Tiene crédito actual?
        const tieneCredito = document.getElementById('tiene_credito_actual');
        const cualCredito = document.getElementById('cual_credito_actual');
        function actualizarCualCredito() {
            cualCredito.style.display = tieneCredito.checked ? '' : 'none';
            if (!tieneCredito.checked) cualCredito.value = '';
        }
        tieneCredito.addEventListener('change', actualizarCualCredito);
        actualizarCualCredito();

        // Vivienda: mostrar "quién paga" solo si es pastoral
        document.querySelectorAll('input[name="tipo_vivienda"]').forEach(r => {
            r.addEventListener('change', function () {
                document.getElementById('congregacionPagaWrap').style.display = (this.value === 'pastoral' && this.checked) ? '' : 'none';
            });
        });

        // Totales de ingresos/egresos
        function formatoMoneda(v) {
            return '$' + Number(v || 0).toLocaleString('es-CO');
        }
        function recalcularTotales() {
            let totalIng = 0, totalEgr = 0;
            document.querySelectorAll('.monto-ingreso').forEach(i => totalIng += parseFloat(i.value) || 0);
            document.querySelectorAll('.monto-egreso').forEach(i => totalEgr += parseFloat(i.value) || 0);
            document.getElementById('totalIngresos').textContent = formatoMoneda(totalIng);
            document.getElementById('totalEgresos').textContent = formatoMoneda(totalEgr);
        }
        document.querySelectorAll('.monto-ingreso, .monto-egreso').forEach(i => i.addEventListener('input', recalcularTotales));
        recalcularTotales();
    });
</script>
@endpush
</x-base-layout>
