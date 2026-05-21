<x-base-layout>
    <div class="task-index-wrapper">
        <header class="index-header">
            <div class="header-titles">
                <span class="system-tag">Módulo de Recaudo</span>
                <h1 class="main-title">Expediente de Imputación</h1>
                <p class="main-subtitle">Recibo #{{ $recImputacionContable->id_recibo }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('recaudo.imputaciones.index') }}" class="btn-ghost-corporate">
                    <i class="fas fa-arrow-left"></i> <span>Volver</span>
                </a>
                <a href="{{ route('recaudo.imputaciones.edit', $recImputacionContable->id) }}" class="btn-corporate-black">
                    <i class="fas fa-pen-nib"></i> <span>Editar</span>
                </a>
            </div>
        </header>

        <div class="show-card-corp">
            <div class="show-header">
                <div class="status-badge-large {{ $recImputacionContable->estado_conciliacion == 'Anulado' ? 'st-danger' : ($recImputacionContable->estado_conciliacion == 'Pendiente' ? 'st-pending' : 'st-completed') }}">
                    <i class="fas fa-circle"></i> {{ str_replace('_', ' ', $recImputacionContable->estado_conciliacion) }}
                </div>
                <h2>${{ number_format($recImputacionContable->valor_imputado, 0) }}</h2>
            </div>
            
            <div class="show-body">
                <div class="grid-3">
                    <div class="info-block">
                        <h4>Tipo de Imputación</h4>
                        <p class="text-primary fw-bold">{{ $recImputacionContable->tipo ?? 'N/A' }}</p>
                    </div>
                    <div class="info-block">
                        <h4>Registrado por</h4>
                        <p><i class="fas fa-user-circle"></i> {{ $recImputacionContable->user->name ?? 'Sistema' }}</p>
                    </div>
                </div>

                <div class="info-block mt-3">
                    <h4>Concepto Contable</h4>
                    <p>{{ $recImputacionContable->concepto_contable }}</p>
                </div>

                <div class="grid-3 mt-4">
                    <div class="detail-item">
                        <i class="fas fa-university icon-muted"></i>
                        <div>
                            <span class="label">ID Transacción</span>
                            <strong>{{ $recImputacionContable->id_transaccion }}</strong>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-user-tie icon-muted"></i>
                        <div>
                            <span class="label">Tercero</span>
                            <strong>{{ $recImputacionContable->tercero->nom_ter ?? $recImputacionContable->id_tercero_origen }}</strong>
                        </div>
                    </div>

                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt icon-muted"></i>
                        <div>
                            <span class="label">Distrito</span>
                            <strong>{{ $recImputacionContable->distrito->NOM_DIST ?? $recImputacionContable->id_distrito }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('recaudo.imputaciones.partials.styles')
</x-base-layout>