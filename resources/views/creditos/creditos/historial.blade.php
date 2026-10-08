<x-base-layout>
    @section('titlepage', 'Historial de Crédito del Asociado')

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Historial de Crédito del Asociado</h5>
                <div class="card-header-action">
                    <a href="{{ route('creditos.credito.index') }}" class="btn btn-sm btn-secondary">
                        <i class="feather-arrow-left me-1"></i> Volver a la lista
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('creditos.credito.historial') }}" method="GET" class="row g-2 align-items-end mb-4">
                    <div class="col-auto">
                        <label for="cedula" class="form-label mb-0">Cédula del asociado</label>
                        <input type="text" name="cedula" id="cedula" class="form-control" value="{{ $cedula }}" placeholder="Ej: 1025523852" autofocus>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Buscar</button>
                    </div>
                </form>

                @if ($cedula !== '')
                    @if (!$tercero)
                        <div class="alert alert-warning">No se encontró ningún tercero con la cédula <strong>{{ $cedula }}</strong>.</div>
                    @else
                        <h5 class="text-dark mb-3">{{ $tercero->nom_ter }} <span class="text-muted fs-8">({{ $tercero->cod_ter }})</span></h5>

                        {{-- Créditos actuales --}}
                        <h6 class="fw-bold text-primary mt-4">Créditos Activos ({{ $creditosActuales->count() }})</h6>
                        @if ($creditosActuales->isEmpty())
                            <p class="text-muted">Sin créditos en el sistema actual.</p>
                        @else
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Línea</th>
                                            <th>Valor</th>
                                            <th>Cuotas</th>
                                            <th>Fecha Desembolso</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($creditosActuales as $c)
                                            <tr>
                                                <td>{{ $c->lineaCredito->nombre ?? '—' }}</td>
                                                <td>${{ number_format($c->valor, 0, ',', '.') }}</td>
                                                <td>{{ $c->cuotas }}</td>
                                                <td>{{ optional($c->fecha_desembolso)->format('d/m/Y') ?? '—' }}</td>
                                                <td>{{ $c->estado->nombre ?? '—' }}</td>
                                                <td><a href="{{ route('creditos.credito.show', $c) }}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- Solicitudes históricas --}}
                        <h6 class="fw-bold text-primary mt-4">Solicitudes Históricas ({{ $solicitudes->total() }})</h6>
                        @if ($solicitudes->isEmpty())
                            <p class="text-muted">Sin solicitudes en el archivo histórico.</p>
                        @else
                            <div class="table-responsive mb-4">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nº Solicitud</th>
                                            <th>Fecha</th>
                                            <th>Tipo Cred.</th>
                                            <th>Valor Solicitado</th>
                                            <th>Valor Aprobado</th>
                                            <th>Destino</th>
                                            <th>Etapa alcanzada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($solicitudes as $s)
                                            <tr>
                                                <td>{{ $s->num_soli }}</td>
                                                <td>{{ $s->fecha }}</td>
                                                <td>{{ $s->tipo_cred }}</td>
                                                <td>${{ number_format($s->vr_soli ?? 0, 0, ',', '.') }}</td>
                                                <td>${{ number_format($s->val_aprobado ?? 0, 0, ',', '.') }}</td>
                                                <td class="text-truncate" style="max-width: 220px;" title="{{ $s->destino }}">{{ $s->destino }}</td>
                                                <td>
                                                    @if ($s->est_desem)
                                                        <span class="badge bg-success">Desembolsado</span>
                                                    @elseif ($s->est_lisdesem)
                                                        <span class="badge bg-info">Lista Desembolso</span>
                                                    @elseif ($s->est_pagenvia)
                                                        <span class="badge bg-info">Pagaré Enviado</span>
                                                    @elseif ($s->est_nega)
                                                        <span class="badge bg-danger">Negado</span>
                                                    @elseif ($s->est_aplaz)
                                                        <span class="badge bg-warning">Aplazado</span>
                                                    @elseif ($s->est_aprob)
                                                        <span class="badge bg-primary">Aprobado</span>
                                                    @elseif ($s->est_recibi)
                                                        <span class="badge bg-secondary">Recibido</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">Sin estado</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mb-4">{{ $solicitudes->links() }}</div>
                        @endif

                        {{-- Pagarés históricos --}}
                        <h6 class="fw-bold text-primary mt-4">Pagarés Históricos ({{ $pagares->total() }})</h6>
                        @if ($pagares->isEmpty())
                            <p class="text-muted">Sin pagarés en el archivo histórico.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Pagaré</th>
                                            <th>Nº Solicitud</th>
                                            <th>Monto Aprobado</th>
                                            <th>Cuota</th>
                                            <th>Plazo</th>
                                            <th>Fecha Aprobación</th>
                                            <th>Fecha Inicio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pagares as $p)
                                            <tr>
                                                <td>{{ $p->cod_pagare }}</td>
                                                <td>{{ $p->n_soli }}</td>
                                                <td>${{ number_format($p->mont_aprob ?? 0, 0, ',', '.') }}</td>
                                                <td>${{ number_format($p->cuota ?? 0, 0, ',', '.') }}</td>
                                                <td>{{ $p->plazo }} meses</td>
                                                <td>{{ $p->fec_apro }}</td>
                                                <td>{{ $p->fec_inicia }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center">{{ $pagares->links() }}</div>
                        @endif
                    @endif
                @else
                    <p class="text-muted">Ingresa una cédula para consultar el historial de crédito del asociado.</p>
                @endif
            </div>
        </div>
    </div>
</x-base-layout>
