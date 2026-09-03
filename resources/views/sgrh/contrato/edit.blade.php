<x-base-layout>

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1">Editar contrato</h2>
            <p class="text-muted mb-0">{{ $contrato->empleado->nombre_completo }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('sgrh.empleado.edit', $contrato->empleado_id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del contrato</h5>

            <form method="POST" action="{{ route('sgrh.contrato.update', $contrato) }}" id="formEditarContrato">
                @csrf
                @method('PUT')
                @include('sgrh.contrato._form', ['contrato' => $contrato])

                <hr class="my-1">
                <p class="text-muted small fw-bold text-uppercase mb-3 mt-3" style="letter-spacing: .04em;">Motivo de esta modificación</p>
                <div class="alert alert-info d-flex align-items-start gap-2 py-2 px-3 mb-3" role="alert">
                    <i class="bi bi-info-circle mt-1"></i>
                    <div class="small mb-0">
                        Esta modificación quedará registrada en la trazabilidad del historial de contratos
                        del colaborador, sin importar si se editó desde aquí o desde su ficha.
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-edit-3 me-1 text-primary"></i>Causal
                        </label>
                        <select name="causal_modificacion" class="form-select @error('causal_modificacion') is-invalid @enderror" required>
                            <option value="">Selecciona una causal</option>
                            @foreach ($causalesModificacion as $causal)
                                <option value="{{ $causal }}" @selected(old('causal_modificacion') === $causal)>{{ $causal }}</option>
                            @endforeach
                        </select>
                        @error('causal_modificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Queda registrada junto con la observación en el historial de este contrato.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-message-square me-1 text-primary"></i>Observación
                        </label>
                        <textarea name="observacion_modificacion" class="form-control @error('observacion_modificacion') is-invalid @enderror" rows="2">{{ old('observacion_modificacion') }}</textarea>
                        @error('observacion_modificacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4" id="btnGuardarContrato">
                        <i class="bi bi-check-circle"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- HISTORIAL DE MODIFICACIONES --}}
    <div class="card mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Historial de eventos</h5>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Fecha</th>
                            <th>Evento</th>
                            <th>Observación</th>
                            <th>Usuario</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-3 py-2">{{ $contrato->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-success-subtle text-success">Creación</span></td>
                            <td class="text-muted small">—</td>
                            <td class="text-muted small">—</td>
                            <td class="text-end pe-3"></td>
                        </tr>
                        @foreach ($contrato->modificaciones as $modificacion)
                            <tr>
                                <td class="ps-3 py-2">{{ $modificacion->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($modificacion->causal === 'Renovación')
                                        <span class="badge bg-warning-subtle text-warning">Renovación</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info">{{ $modificacion->causal }}</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $modificacion->observacion ?: '—' }}</td>
                                <td class="text-muted small">{{ $modificacion->usuario->name ?? '—' }}</td>
                                <td class="text-end pe-3">
                                    @can('sgrh.contrato.destroy')
                                        <form action="{{ route('sgrh.contrato.modificacion.destroy', $modificacion) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar este registro del historial? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="small border-0 bg-transparent p-0 text-danger">
                                                <i class="bi bi-trash3"></i> Eliminar
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            (function () {
                const form = document.getElementById('formEditarContrato');
                const boton = document.getElementById('btnGuardarContrato');
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

            // Botón "Ver": abre el enlace al gestor documental en otra pestaña, para que el
            // usuario mismo verifique que apunta al documento correcto.
            (function () {
                const input = document.getElementById('input_documento_url');
                const botonVer = document.getElementById('btn_ver_documento');
                if (!input || !botonVer) {
                    return;
                }
                input.addEventListener('input', function () {
                    botonVer.disabled = input.value.trim() === '';
                });
                botonVer.addEventListener('click', function () {
                    if (input.value.trim() !== '') {
                        window.open(input.value.trim(), '_blank', 'noopener');
                    }
                });
            })();
        </script>
    @endpush
</x-base-layout>
