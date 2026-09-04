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

    @if ($desactualizado)
        <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <strong>Los datos del tercero llevan más de un año sin actualizarse.</strong>
                No se pueden modificar los datos del colaborador hasta actualizarlos.
            </div>
            @can('sgrh.tercero.edit')
                <a href="{{ route('sgrh.tercero.edit', $empleado->cod_ter) }}" class="btn btn-sm btn-danger">
                    <i class="bi bi-pencil-square"></i> Actualizar tercero
                </a>
            @endcan
        </div>
    @endif

    <div class="card">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Datos del colaborador</h5>

            <form method="POST" action="{{ route('sgrh.empleado.update', $empleado) }}" id="formEditarColaborador">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-calendar me-1 text-primary"></i>Fecha de ingreso
                        </label>
                        <input type="text" class="form-control" disabled
                               value="{{ $empleado->fecha_ingreso?->format('d/m/Y') ?? 'Sin contrato activo' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Cargo actual
                        </label>
                        <input type="text" class="form-control" disabled
                               value="{{ $empleado->cargo->nombre ?? 'Sin contrato activo' }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-dollar-sign me-1 text-primary"></i>Salario actual
                        </label>
                        <input type="text" class="form-control" disabled
                               value="{{ $empleado->salario_asignado ? '$' . number_format($empleado->salario_asignado, 0, ',', '.') : 'Sin contrato activo' }}">
                    </div>
                    <div class="col-12">
                        <div class="form-text mb-0">Fecha de ingreso, cargo y salario vienen del contrato activo — se editan desde "Registrar contrato"/"Editar contrato" abajo.</div>
                    </div>
                </div>

                <hr class="my-1">
                <p class="text-muted small fw-bold text-uppercase mb-3 mt-2" style="letter-spacing: .04em;">Datos del colaborador</p>

                <div class="row g-3">
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
                    @php
                        $fp2 = $empleado->fondo_pension_2;
                        $fp2EsOtra = !is_null($fp2) && $fp2 !== 'No aplica' && !$listaFondosPension->contains($fp2);
                    @endphp
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-briefcase me-1 text-primary"></i>Fondo de pensión 2
                        </label>
                        <select id="select_fondo_pension_2" name="fondo_pension_2" class="form-select">
                            <option value="">Sin definir</option>
                            <option value="No aplica" @selected(old('fondo_pension_2', $fp2) === 'No aplica')>No aplica</option>
                            @foreach ($listaFondosPension as $nombre)
                                <option value="{{ $nombre }}" @selected(old('fondo_pension_2', $fp2) === $nombre)>{{ $nombre }}</option>
                            @endforeach
                            <option value="__otra__" @selected($fp2EsOtra)>Otra (especificar)</option>
                        </select>
                        <div id="wrapper_otra_fondo_pension_2" class="mt-2" style="{{ $fp2EsOtra ? '' : 'display: none;' }}">
                            <input type="text" id="input_otra_fondo_pension_2" class="form-control" placeholder="Escribe el nombre del fondo"
                                   value="{{ $fp2EsOtra ? $fp2 : '' }}">
                        </div>
                        <div class="form-text">Preparación para la reforma pensional (pilar complementario).</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-user-plus me-1 text-primary"></i>Contacto de emergencia — nombre
                        </label>
                        <input type="text" name="contacto_emergencia_nombre" class="form-control" style="text-transform: uppercase;"
                               value="{{ old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre) }}">
                        <div class="form-text">Se guarda en mayúsculas.</div>
                    </div>
                    <div class="col-md-4">
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

                    <div class="col-12">
                        <hr class="my-1">
                        <p class="text-muted small fw-bold text-uppercase mb-0" style="letter-spacing: .04em;">Contacto corporativo</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-phone me-1 text-primary"></i>Teléfono
                        </label>
                        <input type="text" name="telefono_corporativo" class="form-control"
                               value="{{ old('telefono_corporativo', $empleado->telefono_corporativo) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-hash me-1 text-primary"></i>Ext.
                        </label>
                        <input type="text" name="ext_corporativo" class="form-control"
                               value="{{ old('ext_corporativo', $empleado->ext_corporativo) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-smartphone me-1 text-primary"></i>Celular
                        </label>
                        <input type="text" name="celular_corporativo" class="form-control"
                               value="{{ old('celular_corporativo', $empleado->celular_corporativo) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-mail me-1 text-primary"></i>Correo corporativo
                        </label>
                        <input type="email" name="correo_corporativo" class="form-control @error('correo_corporativo') is-invalid @enderror"
                               value="{{ old('correo_corporativo', $empleado->correo_corporativo) }}">
                        @error('correo_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">
                            <i class="feather-mail me-1 text-primary"></i>Gmail corporativo
                        </label>
                        <input type="email" name="gmail_corporativo" class="form-control @error('gmail_corporativo') is-invalid @enderror"
                               value="{{ old('gmail_corporativo', $empleado->gmail_corporativo) }}">
                        @error('gmail_corporativo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @can('sgrh.empleado.update')
                    @unless ($desactualizado)
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4" id="btnGuardarColaborador">
                                <i class="bi bi-check-circle"></i> Guardar cambios
                            </button>
                        </div>
                    @endunless
                @endcan
            </form>
        </div>
    </div>

    {{-- DEPENDIENTES ECONÓMICOS --}}
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Dependientes económicos</h5>
                @can('sgrh.empleado.update')
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDependiente"
                            onclick="abrirModalDependiente()">
                        <i class="bi bi-plus-circle"></i> Agregar dependiente
                    </button>
                @endcan
            </div>

            @if ($empleado->dependientes->isEmpty())
                <p class="text-muted small mb-0">Este colaborador no tiene dependientes económicos registrados todavía.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nombre completo</th>
                                <th>Documento</th>
                                <th>Fecha de nacimiento</th>
                                <th>Género</th>
                                <th>Parentesco</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleado->dependientes as $dependiente)
                                <tr>
                                    <td class="ps-3 py-2">{{ $dependiente->nombre_completo }}</td>
                                    <td>
                                        @if ($dependiente->documento_identificacion)
                                            <span class="text-muted small">{{ $tiposDocumento[$dependiente->tipo_documento] ?? '' }}</span>
                                            {{ $dependiente->documento_identificacion }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $dependiente->fecha_nacimiento->format('d/m/Y') }}</td>
                                    <td>{{ $dependiente->genero === 'V' ? 'Varón' : ($dependiente->genero === 'H' ? 'Hembra' : '—') }}</td>
                                    <td>{{ $parentescos[$dependiente->parentesco] ?? '—' }}</td>
                                    <td class="text-end pe-3">
                                        @can('sgrh.empleado.update')
                                            <a href="javascript:void(0)" class="small me-2" data-bs-toggle="modal" data-bs-target="#modalDependiente"
                                               onclick='abrirModalDependiente(@json($dependiente))'>
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <form action="{{ route('sgrh.dependiente.destroy', $dependiente) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar a {{ $dependiente->nombre_completo }} como dependiente?');">
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
            @endif
        </div>
    </div>

    {{-- ESTUDIOS --}}
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Estudios</h5>
                @can('sgrh.empleado.update')
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEstudio"
                            onclick="abrirModalEstudio()">
                        <i class="bi bi-plus-circle"></i> Agregar estudio
                    </button>
                @endcan
            </div>

            @if ($empleado->estudios->isEmpty())
                <p class="text-muted small mb-0">Este colaborador no tiene estudios registrados todavía.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Programa</th>
                                <th>Tipo de formación</th>
                                <th>Nivel de formación</th>
                                <th class="text-center">Graduado</th>
                                <th>Fecha terminación</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleado->estudios as $estudio)
                                <tr>
                                    <td class="ps-3 py-2">
                                        {{ $estudio->programa }}
                                        @if ($estudio->institucion_educativa)
                                            <div class="text-muted small">{{ $estudio->institucion_educativa }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $estudio->tipo_formacion }}</td>
                                    <td>{{ $estudio->nivel_formacion }}</td>
                                    <td class="text-center">
                                        @if ($estudio->graduado)
                                            <span class="badge bg-success-subtle text-success">Sí</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $estudio->fecha_terminacion?->format('d/m/Y') ?? 'En curso' }}</td>
                                    <td class="text-end pe-3">
                                        @can('sgrh.empleado.update')
                                            <a href="javascript:void(0)" class="small me-2" data-bs-toggle="modal" data-bs-target="#modalEstudio"
                                               onclick='abrirModalEstudio(@json($estudio))'>
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            <form action="{{ route('sgrh.estudio.destroy', $estudio) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar el estudio {{ addslashes($estudio->programa) }}?');">
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
            @endif
        </div>
    </div>

    {{-- HISTORIAL DE CONTRATOS --}}
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Historial de contratos</h5>
                <div class="d-flex gap-2">
                    @can('sgrh.contrato.index')
                        <a href="{{ route('sgrh.contrato.historial.imprimir', $empleado) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="bi bi-printer"></i> Imprimir historial
                        </a>
                    @endcan
                    @can('sgrh.contrato.store')
                        <a href="{{ route('sgrh.contrato.create', ['empleado_id' => $empleado->id]) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Registrar contrato
                        </a>
                    @endcan
                </div>
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
                                <th class="text-center">Origen</th>
                                <th>Última modificación</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empleado->contratos as $contrato)
                                <tr>
                                    <td class="ps-3 py-2">{{ $contrato->tipoContrato->nombre }}</td>
                                    <td>{{ $contrato->fecha_inicio?->format('d/m/Y') ?? 'Sin definir' }}</td>
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
                                    <td class="text-center">
                                        @php
                                            // Null-safe a propósito: modificaciones() debería
                                            // traer siempre al menos el evento de Creación, pero
                                            // un contrato huérfano (fallo a mitad de camino antes
                                            // de la transacción en store()) no debe tumbar toda
                                            // la ficha del colaborador.
                                            $ultimaCausal = $contrato->modificaciones->first()?->causal ?? 'Sin eventos';
                                            $totalModificacionesReales = $contrato->modificaciones->where('causal', '!=', 'Creación')->count();
                                        @endphp
                                        <button type="button"
                                                class="badge border-0 {{ $ultimaCausal === 'Creación' ? 'bg-success-subtle text-success' : ($ultimaCausal === 'Renovación' ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info') }}"
                                                data-bs-toggle="modal" data-bs-target="#modalEventos{{ $contrato->id }}">
                                            {{ $ultimaCausal }}
                                            @if ($ultimaCausal !== 'Creación' && $totalModificacionesReales > 1)
                                                (+{{ $totalModificacionesReales - 1 }})
                                            @endif
                                        </button>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $contrato->updated_at->gt($contrato->created_at) ? $contrato->updated_at->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        @can('sgrh.contrato.update')
                                            <a href="{{ route('sgrh.contrato.edit', $contrato) }}" class="small me-2">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                            @if ($contrato->estado === 'Activo')
                                                <form action="{{ route('sgrh.contrato.renovar', $contrato) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Cerrar este contrato y registrar uno nuevo?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="small border-0 bg-transparent p-0 text-primary me-2">
                                                        <i class="bi bi-arrow-repeat"></i> Renovar
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                        @can('sgrh.contrato.destroy')
                                            <form action="{{ route('sgrh.contrato.destroy', $contrato) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar este contrato de forma permanente, junto con su historial de modificaciones? Esta acción no se puede deshacer.');">
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
            @endif
        </div>
    </div>

    {{-- MODALES: listado de eventos (creación + modificaciones) por cada contrato --}}
    @foreach ($empleado->contratos as $contrato)
        <div class="modal fade" id="modalEventos{{ $contrato->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Eventos del contrato — {{ $contrato->tipoContrato->nombre }} ({{ $contrato->fecha_inicio?->format('d/m/Y') ?? 'sin fecha de inicio' }})
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Más reciente primero (es la modificación vigente); la Creación
                             queda siempre al final, como el origen del historial — ambas son
                             filas reales de sgrh_contrato_modificaciones. --}}
                        <ul class="list-group list-group-flush">
                            @foreach ($contrato->modificaciones as $modificacion)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        @if ($loop->first)
                                            <span class="badge bg-primary-subtle text-primary mb-1">Vigente</span>
                                        @endif
                                        @if ($modificacion->causal === 'Creación')
                                            <span class="badge bg-success-subtle text-success mb-1">Creación</span>
                                        @elseif ($modificacion->causal === 'Renovación')
                                            <span class="badge bg-warning-subtle text-warning mb-1">Renovación</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info mb-1">{{ $modificacion->causal }}</span>
                                        @endif
                                        @if ($modificacion->observacion)
                                            <div class="small">{{ $modificacion->observacion }}</div>
                                        @endif
                                        <div class="small text-muted">
                                            {{ $modificacion->created_at->format('d/m/Y H:i') }} — {{ $modificacion->usuario->name ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="text-nowrap">
                                        <a href="{{ route('sgrh.contrato.modificacion.ver', $modificacion) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="Ver/imprimir">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        @can('sgrh.contrato.destroy')
                                            @if ($modificacion->causal !== 'Creación')
                                                <form action="{{ route('sgrh.contrato.modificacion.destroy', $modificacion) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('¿Eliminar este registro del historial? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar registro">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        @can('sgrh.contrato.update')
                            <a href="{{ route('sgrh.contrato.edit', $contrato) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar contrato
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- MODAL: agregar/editar dependiente --}}
    <div class="modal fade" id="modalDependiente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formDependiente" action="{{ route('sgrh.dependiente.store', $empleado) }}">
                    @csrf
                    <input type="hidden" name="_method" id="dependiente_method" value="POST">
                    <input type="hidden" name="dependiente_id" id="dependiente_id" value="{{ old('dependiente_id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dependiente_titulo">Agregar dependiente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Tipo de documento</label>
                                <select name="tipo_documento" id="dependiente_tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror">
                                    <option value="">Sin definir</option>
                                    @foreach ($tiposDocumento as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected(old('tipo_documento') === $codigo)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_documento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Número de documento</label>
                                <input type="text" name="documento_identificacion" id="dependiente_documento_identificacion"
                                       class="form-control @error('documento_identificacion') is-invalid @enderror" value="{{ old('documento_identificacion') }}">
                                @error('documento_identificacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Primer nombre</label>
                                <input type="text" name="nombre1" id="dependiente_nombre1"
                                       class="form-control @error('nombre1') is-invalid @enderror" value="{{ old('nombre1') }}" required>
                                @error('nombre1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Segundo nombre</label>
                                <input type="text" name="nombre2" id="dependiente_nombre2" class="form-control" value="{{ old('nombre2') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Primer apellido</label>
                                <input type="text" name="apellido1" id="dependiente_apellido1"
                                       class="form-control @error('apellido1') is-invalid @enderror" value="{{ old('apellido1') }}" required>
                                @error('apellido1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Segundo apellido</label>
                                <input type="text" name="apellido2" id="dependiente_apellido2" class="form-control" value="{{ old('apellido2') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" id="dependiente_fecha_nacimiento"
                                       class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento') }}" required>
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Género</label>
                                <select name="genero" id="dependiente_genero" class="form-select">
                                    <option value="">Sin definir</option>
                                    <option value="V" @selected(old('genero') === 'V')>Varón</option>
                                    <option value="H" @selected(old('genero') === 'H')>Hembra</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Parentesco</label>
                                <select name="parentesco" id="dependiente_parentesco" class="form-select">
                                    <option value="">Sin definir</option>
                                    @foreach ($parentescos as $codigo => $nombre)
                                        <option value="{{ $codigo }}" @selected(old('parentesco') === $codigo)>{{ $nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarDependiente">
                            <i class="bi bi-check-circle"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: agregar/editar estudio --}}
    <div class="modal fade" id="modalEstudio" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEstudio" action="{{ route('sgrh.estudio.store', $empleado) }}">
                    @csrf
                    <input type="hidden" name="_method" id="estudio_method" value="POST">
                    <input type="hidden" name="estudio_id" id="estudio_id" value="{{ old('estudio_id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="estudio_titulo">Agregar estudio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Programa</label>
                                <input type="text" name="programa" id="estudio_programa"
                                       class="form-control @error('programa') is-invalid @enderror" value="{{ old('programa') }}" required>
                                @error('programa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Graduado</label>
                                <select name="graduado" id="estudio_graduado" class="form-select">
                                    <option value="0" @selected(old('graduado', '0') === '0')>No</option>
                                    <option value="1" @selected(old('graduado') === '1')>Sí</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Institución educativa</label>
                                <input type="text" name="institucion_educativa" id="estudio_institucion_educativa" class="form-control"
                                       value="{{ old('institucion_educativa') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Tipo de formación</label>
                                <select name="tipo_formacion" id="estudio_tipo_formacion" class="form-select @error('tipo_formacion') is-invalid @enderror" required>
                                    <option value="">Selecciona un tipo</option>
                                    @foreach ($tiposFormacion as $tipo)
                                        <option value="{{ $tipo }}" @selected(old('tipo_formacion') === $tipo)>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_formacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Nivel de formación</label>
                                <select name="nivel_formacion" id="estudio_nivel_formacion" class="form-select @error('nivel_formacion') is-invalid @enderror" required>
                                    <option value="">Selecciona un nivel</option>
                                    @foreach ($nivelesFormacion as $nivel)
                                        <option value="{{ $nivel }}" @selected(old('nivel_formacion') === $nivel)>{{ $nivel }}</option>
                                    @endforeach
                                </select>
                                @error('nivel_formacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: .04em;">Fecha de terminación</label>
                                <input type="date" name="fecha_terminacion" id="estudio_fecha_terminacion" class="form-control" value="{{ old('fecha_terminacion') }}">
                                <div class="form-text">Déjala en blanco si el estudio sigue en curso.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnGuardarEstudio">
                            <i class="bi bi-check-circle"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            @if ($errors->any())
                toastr.error("{{ $errors->first() }}");
            @endif

            // Un solo modal para agregar y editar dependientes: sin argumento = modo alta
            // (POST a la ruta del colaborador); con un dependiente = modo edición (PUT a la
            // ruta de ese dependiente, formulario precargado).
            function abrirModalDependiente(dependiente) {
                const form = document.getElementById('formDependiente');
                const metodo = document.getElementById('dependiente_method');
                const titulo = document.getElementById('dependiente_titulo');

                form.reset();

                if (dependiente) {
                    titulo.textContent = 'Editar dependiente';
                    form.action = `{{ url('sgrh/dependientes') }}/${dependiente.id}`;
                    metodo.value = 'PUT';
                    document.getElementById('dependiente_id').value = dependiente.id;
                    document.getElementById('dependiente_tipo_documento').value = dependiente.tipo_documento || '';
                    document.getElementById('dependiente_documento_identificacion').value = dependiente.documento_identificacion || '';
                    document.getElementById('dependiente_nombre1').value = dependiente.nombre1 || '';
                    document.getElementById('dependiente_nombre2').value = dependiente.nombre2 || '';
                    document.getElementById('dependiente_apellido1').value = dependiente.apellido1 || '';
                    document.getElementById('dependiente_apellido2').value = dependiente.apellido2 || '';
                    document.getElementById('dependiente_fecha_nacimiento').value = (dependiente.fecha_nacimiento || '').substring(0, 10);
                    document.getElementById('dependiente_genero').value = dependiente.genero || '';
                    document.getElementById('dependiente_parentesco').value = dependiente.parentesco || '';
                } else {
                    titulo.textContent = 'Agregar dependiente';
                    form.action = "{{ route('sgrh.dependiente.store', $empleado) }}";
                    metodo.value = 'POST';
                    document.getElementById('dependiente_id').value = '';
                }
            }

            @php
                $camposDependiente = ['nombre1', 'nombre2', 'apellido1', 'apellido2', 'tipo_documento', 'documento_identificacion', 'fecha_nacimiento', 'genero', 'parentesco'];
                $hayErrorDependiente = collect($camposDependiente)->contains(fn ($campo) => $errors->has($campo));
            @endphp
            @if ($hayErrorDependiente)
                // El formulario del modal sí falló (ej. fecha futura, nombre vacío) — sin esto,
                // el envío recarga la página, cierra el modal y el usuario solo ve el toastr
                // genérico de arriba, sin saber a qué campo corresponde el error.
                document.addEventListener('DOMContentLoaded', function () {
                    const depId = document.getElementById('dependiente_id').value;
                    const titulo = document.getElementById('dependiente_titulo');
                    const form = document.getElementById('formDependiente');
                    const metodo = document.getElementById('dependiente_method');
                    if (depId) {
                        titulo.textContent = 'Editar dependiente';
                        form.action = `{{ url('sgrh/dependientes') }}/${depId}`;
                        metodo.value = 'PUT';
                    } else {
                        titulo.textContent = 'Agregar dependiente';
                        form.action = "{{ route('sgrh.dependiente.store', $empleado) }}";
                        metodo.value = 'POST';
                    }
                    new bootstrap.Modal(document.getElementById('modalDependiente')).show();
                });
            @endif

            function abrirModalEstudio(estudio) {
                const form = document.getElementById('formEstudio');
                const metodo = document.getElementById('estudio_method');
                const titulo = document.getElementById('estudio_titulo');

                form.reset();

                if (estudio) {
                    titulo.textContent = 'Editar estudio';
                    form.action = `{{ url('sgrh/estudios') }}/${estudio.id}`;
                    metodo.value = 'PUT';
                    document.getElementById('estudio_id').value = estudio.id;
                    document.getElementById('estudio_programa').value = estudio.programa || '';
                    document.getElementById('estudio_institucion_educativa').value = estudio.institucion_educativa || '';
                    document.getElementById('estudio_tipo_formacion').value = estudio.tipo_formacion || '';
                    document.getElementById('estudio_nivel_formacion').value = estudio.nivel_formacion || '';
                    document.getElementById('estudio_graduado').value = estudio.graduado ? '1' : '0';
                    document.getElementById('estudio_fecha_terminacion').value = (estudio.fecha_terminacion || '').substring(0, 10);
                } else {
                    titulo.textContent = 'Agregar estudio';
                    form.action = "{{ route('sgrh.estudio.store', $empleado) }}";
                    metodo.value = 'POST';
                    document.getElementById('estudio_id').value = '';
                }
            }

            @php
                $camposEstudio = ['programa', 'institucion_educativa', 'tipo_formacion', 'nivel_formacion', 'graduado', 'fecha_terminacion'];
                $hayErrorEstudio = collect($camposEstudio)->contains(fn ($campo) => $errors->has($campo));
            @endphp
            @if ($hayErrorEstudio)
                document.addEventListener('DOMContentLoaded', function () {
                    const estId = document.getElementById('estudio_id').value;
                    const titulo = document.getElementById('estudio_titulo');
                    const form = document.getElementById('formEstudio');
                    const metodo = document.getElementById('estudio_method');
                    if (estId) {
                        titulo.textContent = 'Editar estudio';
                        form.action = `{{ url('sgrh/estudios') }}/${estId}`;
                        metodo.value = 'PUT';
                    } else {
                        titulo.textContent = 'Agregar estudio';
                        form.action = "{{ route('sgrh.estudio.store', $empleado) }}";
                        metodo.value = 'POST';
                    }
                    new bootstrap.Modal(document.getElementById('modalEstudio')).show();
                });
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
            activarOtraOpcion('select_fondo_pension_2', 'wrapper_otra_fondo_pension_2', 'input_otra_fondo_pension_2', 'fondo_pension_2');

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

            // Evita registros duplicados por doble clic en "Guardar" (el modal hace un submit
            // normal con recarga de página, así que el botón vuelve a su estado inicial solo).
            (function () {
                const form = document.getElementById('formDependiente');
                const boton = document.getElementById('btnGuardarDependiente');
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

            (function () {
                const form = document.getElementById('formEstudio');
                const boton = document.getElementById('btnGuardarEstudio');
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
