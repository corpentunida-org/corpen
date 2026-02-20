<x-base-layout>
    {{-- Estilos para Select2 --}}
    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <style>
            .bg-pastel-primary { background-color: #e7f0ff !important; color: #0052cc !important; }
            .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; }
        </style>
    @endpush

    <div class="app-container py-4">
        <div class="card border-0 shadow-sm mx-auto" style="max-width: 1000px; border-radius: 20px;">
            <div class="card-body p-5">
                {{-- Alertas de validación --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 mb-5 shadow-sm" style="border-radius: 15px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-50px me-3">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-send fs-2x text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">Generar Comunicación de Salida</h3>
                        <span class="text-muted fw-semibold">Cree respuestas oficiales vinculadas a radicados</span>
                    </div>
                </div>

                {{-- FORMULARIO CON ID PARA CAPTURAR EL SUBMIT --}}
                <form action="{{ route('correspondencia.comunicaciones-salida.store') }}" method="POST" enctype="multipart/form-data" id="formComunicacionSalida">
                    @csrf
                    {{-- Campo de fecha oculto por si no se incluye en el controlador --}}
                    <input type="hidden" name="fecha_generacion" value="{{ date('Y-m-d') }}">

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-primary">Vincular a Radicado Origen (Solo pendientes)</label>
                            <select name="id_correspondencia" id="select_radicado" class="form-select select2-js @error('id_correspondencia') is-invalid @enderror" required>
                                <option value="">Busque por número de radicado o asunto...</option>
                                @foreach($correspondencias as $corr)
                                    <option value="{{ $corr->id_radicado }}" {{ old('id_correspondencia') == $corr->id_radicado ? 'selected' : '' }}>
                                        NRO: {{ $corr->nro_radicado ?? $corr->id_radicado }} | ASUNTO: {{ $corr->asunto }} | REMITENTE: {{ $corr->remitente->nom_ter ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Solo aparecen radicados que no han sido respondidos.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Funcionario que Firma</label>
                            <select name="fk_usuario" id="select_firmante" class="form-select select2-js @error('fk_usuario') is-invalid @enderror" required>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ old('fk_usuario', auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nro. Oficio Salida</label>
                            <input type="text" class="form-control bg-light-secondary border-dashed" placeholder="Se generará: OF-{{ date('Y') }}-XXXX" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Estado del Envío</label>
                            <select name="estado_envio" class="form-select @error('estado_envio') is-invalid @enderror" required>
                                <option value="Generado">Generado (Borrador)</option>
                                <option value="Enviado por Email" selected>Enviado por Email</option>
                                <option value="Notificado Físicamente">Notificado Físicamente</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Plantilla Base</label>
                            <select name="id_plantilla" class="form-select">
                                <option value="">Ninguna (Texto libre)</option>
                                @foreach($plantillas as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->nombre_plantilla }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Cuerpo de la Carta / Respuesta</label>
                            <textarea name="cuerpo_carta" class="form-control" rows="8" placeholder="Escriba el contenido oficial aquí..." required>{{ old('cuerpo_carta') }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <div class="bg-light-warning p-4 rounded border border-warning border-dashed">
                                <label class="form-label fw-bold">Subir Firma Digital (.png, .jpg)</label>
                                <input type="file" name="firma_digital" class="form-control" accept="image/*">
                                <div class="form-text text-dark-emphasis">La firma se estampará sobre la línea de firma en el PDF.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 d-flex justify-content-end gap-3">
                        <a href="{{ route('correspondencia.comunicaciones-salida.index') }}" class="btn btn-light-danger fw-bold px-6 rounded-pill">Cancelar</a>
                        <button type="submit" id="btnGuardarFull" class="btn btn-primary fw-bold px-8 shadow-sm rounded-pill hover-lift">
                            <i class="bi bi-check-circle-fill me-2"></i>Guardar y Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DE CARGA (PROCESANDO) --}}
    <div class="modal fade" id="modalProcesandoFull" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body text-center p-5">
                    <div class="symbol symbol-65px mb-4">
                        <div class="symbol-label bg-pastel-primary">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <h4 class="fw-bolder text-dark mb-2">Generando Comunicación</h4>
                    <p class="text-muted mb-0">Estamos preparando el documento oficial. Esto puede tardar unos segundos dependiendo de los adjuntos.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Configuración de Select2
                $('.select2-js').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Seleccione una opción...'
                });

                // --- LÓGICA DE PROTECCIÓN DE ENVÍO ---
                const form = document.getElementById('formComunicacionSalida');
                const btn = document.getElementById('btnGuardarFull');
                const loadingModal = new bootstrap.Modal(document.getElementById('modalProcesandoFull'));

                form.addEventListener('submit', function(e) {
                    if (form.checkValidity()) {
                        // Deshabilitar botón para evitar múltiples clics
                        btn.disabled = true;
                        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                        
                        // Mostrar modal de carga
                        loadingModal.show();
                    }
                });
            });
        </script>
    @endpush
</x-base-layout>