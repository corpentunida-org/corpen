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
                    <i class="fas fa-pen-nib"></i> <span>Editar Registro</span>
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
                <div class="info-block">
                    <h4>Concepto Contable</h4>
                    <p>{{ $recImputacionContable->concepto_contable }}</p>
                </div>

                <div class="grid-3 mt-4">
                    <div class="detail-item">
                        <i class="fas fa-university icon-muted"></i>
                        <div>
                            <span class="label">ID Transacción (Extracto)</span>
                            <strong>{{ $recImputacionContable->id_transaccion }}</strong>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-user-tie icon-muted"></i>
                        <div>
                            <span class="label">Tercero Relacionado</span>
                            <strong>{{ $recImputacionContable->tercero->nom_ter ?? $recImputacionContable->id_tercero_origen }}</strong>
                            <small class="d-block text-muted">ID: {{ $recImputacionContable->id_tercero_origen }}</small>
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

                @if($recImputacionContable->link_ecm)
                <div class="ecm-attachment-box mt-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf file-icon"></i>
                        <div class="ml-3">
                            <strong class="d-block">Soporte Documental Indexado</strong>
                            <a href="{{ $recImputacionContable->link_ecm }}" target="_blank" class="text-info">Abrir documento en el Gestor (ECM) <i class="fas fa-external-link-alt"></i></a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('recaudo.imputaciones.partials.styles')
</x-base-layout>