<x-base-layout>
    @section('titlepage', 'Crédito #' . $credito->id)
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb breadcrumb-dots">
                        <li class="breadcrumb-item"><a href="{{ route('creditos.credito.index') }}" class="text-muted text-decoration-none">Créditos</a></li>
                        <li class="breadcrumb-item active">Crédito #{{ $credito->id }}</li>
                    </ol>
                </nav>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="fw-black text-dark mb-1">{{ $credito->tercero->nom_ter ?? $credito->mae_terceros_cod_ter }}</h3>
                                <p class="text-secondary opacity-75 mb-0">Cédula/Código: {{ $credito->mae_terceros_cod_ter }}</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('creditos.credito.edit', $credito) }}" class="btn btn-dark rounded-pill px-4">
                                    <i class="feather-edit-2 me-2"></i>Editar
                                </a>
                                <form action="{{ route('creditos.credito.destroy', $credito) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este crédito? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                                        <i class="feather-trash-2 me-2"></i>Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Línea de Crédito</small>
                                <span class="fw-semibold text-dark">{{ $credito->lineaCredito->nombre ?? '—' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Estado</small>
                                <span class="fw-semibold text-dark">{{ $credito->estado->nombre ?? '—' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Etapa</small>
                                <span class="fw-semibold text-dark">{{ $credito->estado->etapa->nombre ?? '—' }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Valor</small>
                                <span class="fw-semibold text-dark">${{ number_format($credito->valor, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Cuotas</small>
                                <span class="fw-semibold text-dark">{{ $credito->cuotas }}</span>
                            </div>
                            <div class="col-md-4">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Fecha Desembolso</small>
                                <span class="fw-semibold text-dark">{{ optional($credito->fecha_desembolso)->format('d/m/Y') ?? '—' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Pagaré</small>
                                <span class="fw-semibold text-dark">{{ $credito->pagare ?? '—' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">PR</small>
                                <span class="fw-semibold text-dark">{{ $credito->pr ?? '—' }}</span>
                            </div>
                        </div>

                        @if ($credito->pagareRelacionado || $credito->escritura)
                            <hr class="my-4 opacity-10">
                            <h5 class="fw-bold text-dark mb-3">Documentos</h5>
                            <div class="row g-4">
                                @if ($credito->pagareRelacionado)
                                    <div class="col-md-6">
                                        <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Pagaré Registrado</small>
                                        <span class="fw-semibold text-dark">{{ $credito->pagareRelacionado->id_unico_documento }}</span>
                                    </div>
                                @endif
                                @if ($credito->escritura)
                                    <div class="col-md-6">
                                        <small class="text-uppercase fs-xs fw-bold text-muted d-block mb-1">Escritura Registrada</small>
                                        <span class="fw-semibold text-dark">{{ $credito->escritura->id_unico_documento }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($credito->notificaciones->isNotEmpty())
                            <hr class="my-4 opacity-10">
                            <h5 class="fw-bold text-dark mb-3">Notificaciones</h5>
                            <ul class="list-unstyled mb-0">
                                @foreach ($credito->notificaciones as $n)
                                    <li class="mb-2">
                                        <span class="fw-semibold">{{ optional($n->created_at)->format('d/m/Y') }}</span>
                                        — {{ $n->asunto }}: {{ $n->mensaje }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .fw-black { font-weight: 800; }
        .fs-xs { font-size: 0.7rem; }
        .breadcrumb-dots .breadcrumb-item + .breadcrumb-item::before { content: "•"; color: #ccc; padding: 0 1rem; }
    </style>
</x-base-layout>
