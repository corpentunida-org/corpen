<x-base-layout>
    @section('titlepage', 'Configuración de Alertas de Interacciones')
    <x-success />
    <x-error />

    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-text avatar-lg bg-soft-danger text-danger rounded-3 shadow-sm icon">
                        <i class="feather-alert-triangle fs-3"></i>
                    </div>
                    <div>
                        <h2 class="fs-4 fw-bold text-dark mb-1">Configuración de Alertas de Interacciones (Daytrack)</h2>
                        <span class="text-muted fs-13">
                            Controla cada cuánto se le exige a un agente decidir sobre sus vencidas, cuándo se
                            escala al admon del área, y los horarios de los correos programados.
                        </span>
                    </div>
                </div>

                @if ($config->exists)
                    <div class="alert alert-light border d-flex align-items-center gap-2 fs-13">
                        <i class="feather-info text-primary"></i>
                        <span>
                            Configurado por última vez por <strong>{{ $config->actualizadoPor?->name ?? 'desconocido' }}</strong>
                            el {{ $config->updated_at?->format('d/m/Y H:i') ?? '—' }}.
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.alertas-interacciones.config.update') }}">
                    @csrf
                    @method('PUT')

                    <h6 class="fw-bold text-dark mt-2 mb-3"><i class="feather-bell me-1"></i>Aviso forzado en pantalla</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Cada cuántas horas avisar</label>
                            <div class="input-group">
                                <input type="number" name="aviso_intervalo_horas" min="1" max="24"
                                    class="form-control @error('aviso_intervalo_horas') is-invalid @enderror"
                                    value="{{ old('aviso_intervalo_horas', $config->aviso_intervalo_horas) }}">
                                <span class="input-group-text">horas</span>
                                @error('aviso_intervalo_horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Mientras tenga vencidas, cada tantas horas se le abre una ventana que debe responder (no se puede ignorar sin elegir).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Días seguidos posponiendo para escalar</label>
                            <div class="input-group">
                                <input type="number" name="dias_posponer_para_escalar" min="1" max="30"
                                    class="form-control @error('dias_posponer_para_escalar') is-invalid @enderror"
                                    value="{{ old('dias_posponer_para_escalar', $config->dias_posponer_para_escalar) }}">
                                <span class="input-group-text">días</span>
                                @error('dias_posponer_para_escalar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Si el agente elige "Posponer" todos los días sin responder ninguno, al llegar a este número se avisa al admon de su área (y de nuevo cada tantos días adicionales si sigue sin responder).</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3"><i class="feather-mail me-1"></i>Correos programados</h6>
                    <div class="alert alert-warning fs-13 d-flex align-items-center gap-2">
                        <i class="feather-alert-circle"></i>
                        <span>Estos horarios solo se aplican cuando el servidor tenga configurado un cron/supervisor llamando <code>php artisan schedule:run</code> cada minuto — pregúntale a tu equipo de infraestructura si ya está activo.</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">Correo diario de vencidas</label>
                            <input type="time" name="correo_diario_hora"
                                class="form-control @error('correo_diario_hora') is-invalid @enderror"
                                value="{{ old('correo_diario_hora', \Carbon\Carbon::parse($config->correo_diario_hora)->format('H:i')) }}">
                            <small class="text-muted">Lunes a viernes, a cada agente con vencidas.</small>
                            @error('correo_diario_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">Informe semanal — día</label>
                            <select name="informe_semanal_dia" class="form-select @error('informe_semanal_dia') is-invalid @enderror">
                                @foreach (['1' => 'Lunes', '2' => 'Martes', '3' => 'Miércoles', '4' => 'Jueves', '5' => 'Viernes', '6' => 'Sábado', '7' => 'Domingo'] as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected(old('informe_semanal_dia', $config->informe_semanal_dia) == $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            @error('informe_semanal_dia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">Informe semanal — hora</label>
                            <input type="time" name="informe_semanal_hora"
                                class="form-control @error('informe_semanal_hora') is-invalid @enderror"
                                value="{{ old('informe_semanal_hora', \Carbon\Carbon::parse($config->informe_semanal_hora)->format('H:i')) }}">
                            <small class="text-muted">A los admon de cada área, con TODOS sus agentes (incluidos los que no registraron nada).</small>
                            @error('informe_semanal_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small text-uppercase">Alerta de inactividad — hora</label>
                            <input type="time" name="inactividad_hora"
                                class="form-control @error('inactividad_hora') is-invalid @enderror"
                                value="{{ old('inactividad_hora', \Carbon\Carbon::parse($config->inactividad_hora)->format('H:i')) }}">
                            <small class="text-muted">Lunes a viernes, a los admon de área si algún agente no ha registrado nada en el día.</small>
                            @error('inactividad_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-save me-2"></i> Guardar configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Soportes (Centro de Soportes) — mismo mecanismo de aviso forzado que Interacciones, ver
         AlertasSoportesService. No tiene correos diarios/semanales/inactividad como Interacciones
         (esos son propios de Daytrack), solo el aviso forzado y su umbral de escalación. --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 shadow-sm icon">
                        <i class="feather-headphones fs-3"></i>
                    </div>
                    <div>
                        <h2 class="fs-4 fw-bold text-dark mb-1">Configuración de Alertas de Soportes</h2>
                        <span class="text-muted fs-13">
                            Controla cada cuánto se le exige a un usuario decidir sobre sus soportes asignados
                            sin cerrar, y cuándo se escala a superadmin/admindesarrollo.
                        </span>
                    </div>
                </div>

                @if ($configSoportes->exists)
                    <div class="alert alert-light border d-flex align-items-center gap-2 fs-13">
                        <i class="feather-info text-primary"></i>
                        <span>
                            Configurado por última vez por <strong>{{ $configSoportes->actualizadoPor?->name ?? 'desconocido' }}</strong>
                            el {{ $configSoportes->updated_at?->format('d/m/Y H:i') ?? '—' }}.
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.alertas-soportes.config.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Cada cuántas horas avisar</label>
                            <div class="input-group">
                                <input type="number" name="aviso_intervalo_horas" min="1" max="24"
                                    class="form-control @error('aviso_intervalo_horas') is-invalid @enderror"
                                    value="{{ old('aviso_intervalo_horas', $configSoportes->aviso_intervalo_horas) }}">
                                <span class="input-group-text">horas</span>
                                @error('aviso_intervalo_horas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Mientras tenga soportes asignados sin cerrar, cada tantas horas se le abre una ventana que debe responder.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Días seguidos posponiendo para escalar</label>
                            <div class="input-group">
                                <input type="number" name="dias_posponer_para_escalar" min="1" max="30"
                                    class="form-control @error('dias_posponer_para_escalar') is-invalid @enderror"
                                    value="{{ old('dias_posponer_para_escalar', $configSoportes->dias_posponer_para_escalar) }}">
                                <span class="input-group-text">días</span>
                                @error('dias_posponer_para_escalar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Si elige "Posponer" todos los días sin responder ninguno, al llegar a este número se avisa a superadmin/admindesarrollo.</small>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="feather-save me-2"></i> Guardar configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
