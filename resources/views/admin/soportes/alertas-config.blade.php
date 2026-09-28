<x-base-layout>
    @section('titlepage', 'Configuración de Alertas de Soportes')
    <x-success />
    <x-error />

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
                            sin cerrar, cuándo se escala a superadmin/admindesarrollo, y después de cuántos días
                            sin movimiento en "En Revisión" un soporte se cierra solo.
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

                    <h6 class="fw-bold text-dark mt-2 mb-3"><i class="feather-bell me-1"></i>Aviso forzado en pantalla</h6>
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
                            <small class="text-muted">Mientras tenga soportes asignados sin cerrar, cada tantas horas se le abre una ventana que debe responder (no se puede ignorar sin elegir).</small>
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
                            <small class="text-muted">Si elige "Posponer" todos los días sin responder ninguno, al llegar a este número se avisa a superadmin/admindesarrollo (y de nuevo cada tantos días adicionales si sigue sin responder).</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Pulso de atención en el ícono</label>
                            <div class="input-group">
                                <input type="number" name="pulso_intervalo_minutos" min="1" max="120"
                                    class="form-control @error('pulso_intervalo_minutos') is-invalid @enderror"
                                    value="{{ old('pulso_intervalo_minutos', $configSoportes->pulso_intervalo_minutos) }}">
                                <span class="input-group-text">minutos</span>
                                @error('pulso_intervalo_minutos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Cada cuánto el ícono de Soportes cambia de tamaño/color y suena, mientras haya soportes asignados sin cerrar.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3"><i class="feather-check-circle me-1"></i>Cierre automático por vencimiento</h6>
                    <div class="alert alert-warning fs-13 d-flex align-items-center gap-2">
                        <i class="feather-alert-circle"></i>
                        <span>El cierre automático se dispara al abrir la lista de Soportes (con caché de 1 hora) y por el comando <code>soportes:cerrar-automatico</code>, que solo corre solo si el servidor tiene un cron/supervisor llamando <code>php artisan schedule:run</code> cada minuto.</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Días sin movimiento en "En Revisión"</label>
                            <div class="input-group">
                                <input type="number" name="dias_cierre_automatico" min="1" max="60"
                                    class="form-control @error('dias_cierre_automatico') is-invalid @enderror"
                                    value="{{ old('dias_cierre_automatico', $configSoportes->dias_cierre_automatico) }}">
                                <span class="input-group-text">días</span>
                                @error('dias_cierre_automatico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                Un soporte que lleve este número de días seguidos en "En Revisión" sin ninguna
                                gestión nueva se cierra solo, y se avisa por correo tanto a quien lo tenía
                                asignado para revisar como a quien lo reportó (aclarando que fue un cierre
                                automático por vencimiento de tiempos, no una solución confirmada).
                            </small>
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

    {{-- Agentes Omitidos (correo/pantalla) es una lista compartida con Interacciones — se
         administra desde una sola pantalla para no tener dos puntos de edición sobre la misma
         tabla (ver comentario en AlertasInteraccionesConfigController). --}}
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-secondary text-secondary rounded-3 shadow-sm icon">
                        <i class="feather-user-x fs-3"></i>
                    </div>
                    <div class="flex-fill">
                        <h2 class="fs-6 fw-bold text-dark mb-1">Agentes Omitidos (correo / pantalla)</h2>
                        <span class="text-muted fs-13">
                            Esta lista es compartida con Interacciones (Daytrack) — se administra desde una sola
                            pantalla para evitar inconsistencias.
                        </span>
                    </div>
                    <a href="{{ route('admin.alertas-interacciones.config.edit') }}" class="btn btn-sm btn-outline-secondary text-nowrap">
                        <i class="feather-external-link me-1"></i> Administrar
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-base-layout>
