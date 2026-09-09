<x-base-layout>
    @section('titlepage', 'CRM Corpentunida')
    <x-success />
    <x-error />

    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm stretch stretch-full rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-3 shadow-sm icon">
                        <i class="bi bi-hdd-network fs-3"></i>
                    </div>
                    <div>
                        <h2 class="fs-4 fw-bold text-dark mb-1">API Corpentunida CRM</h2>
                        <span class="text-muted fs-13">
                            Credenciales usadas para sincronizar el estado de terceros (ej. retiros/reafiliaciones de
                            Exequiales) con este CRM. Se guardan cifradas en la base de datos, no en el servidor.
                        </span>
                    </div>
                </div>

                @if ($config)
                    <div class="alert alert-light border d-flex align-items-center gap-2 fs-13">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span>
                            Configurado por última vez por <strong>{{ $config->actualizadoPor?->name ?? 'desconocido' }}</strong>
                            el {{ $config->updated_at->format('d/m/Y H:i') }}.
                            Client Secret actual: <strong>{{ $config->client_secret ? 'configurado (oculto)' : 'sin configurar' }}</strong>.
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('integraciones.corpentunida-crm.update') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small text-uppercase">URL base</label>
                            <input type="text" name="url" class="form-control @error('url') is-invalid @enderror"
                                value="{{ old('url', $config->url ?? '') }}" placeholder="https://crm.corpentunida.org.co">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Client ID</label>
                            <input type="text" name="client_id" class="form-control @error('client_id') is-invalid @enderror"
                                value="{{ old('client_id', $config->client_id ?? '') }}">
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small text-uppercase">Client Secret</label>
                            <input type="password" name="client_secret" class="form-control" autocomplete="new-password"
                                placeholder="{{ $config && $config->client_secret ? 'Dejar en blanco para no cambiarlo' : 'Sin configurar' }}">
                            <small class="text-muted">Por seguridad nunca se muestra el valor guardado — solo se sobrescribe si escribes uno nuevo aquí.</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i> Guardar configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-base-layout>
