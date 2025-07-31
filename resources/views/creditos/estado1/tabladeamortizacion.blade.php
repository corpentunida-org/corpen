{{-- resources/views/creditos/estado1/tabladeamortizacion.blade.php --}}

<x-base-layout>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .summary-card { background-color: #e0f7f5; border-left: 5px solid #00796b; }
        .table-custom thead { background-color: #004d40; color: white; }
        .table tfoot tr { border-top: 2px solid #004d40; }
    </style>

    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-table me-2"></i>Tabla de Amortización</h4>
            </div>
            <div class="card-body p-4">
                
                <h5 class="mb-4 text-secondary fw-bold">RESUMEN DEL CRÉDITO</h5>
                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="card summary-card p-3 h-100">
                            <div class="fw-bold text-secondary text-uppercase small">Monto Solicitado</div>
                            <div class="fs-4 text-dark">${{ number_format($monto_solicitado, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card summary-card p-3 h-100">
                            <div class="fw-bold text-secondary text-uppercase small">Plazo</div>
                            <div class="fs-4 text-dark">{{ $plazo_meses }} meses</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card summary-card p-3 h-100">
                            <div class="fw-bold text-secondary text-uppercase small">Tasa Interés Mensual</div>
                            <div class="fs-4 text-dark">{{ $tasa_interes }}%</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered table-custom">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center"># Cuota</th>
                                <th scope="col">Valor Cuota Fija</th>
                                <th scope="col">Abono a Interés</th>
                                <th scope="col">Abono a Capital</th>
                                <th scope="col">Saldo Pendiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tabla as $fila)
                            <tr>
                                <td class="text-center fw-bold">{{ $fila['numero_cuota'] }}</td>
                                <td>${{ number_format($fila['valor_cuota'], 0, ',', '.') }}</td>
                                <td class="text-danger">${{ number_format($fila['interes'], 0, ',', '.') }}</td>
                                <td class="text-success">${{ number_format($fila['abono_capital'], 0, ',', '.') }}</td>
                                <td class="fw-bold">${{ number_format($fila['saldo_final'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr class="fw-bold fs-5 bg-light">
                                <td class="text-end">TOTALES:</td>
                                <td>${{ number_format($total_pagar, 0, ',', '.') }}</td>
                                <td class="text-danger">${{ number_format($total_interes, 0, ',', '.') }}</td>
                                <td class="text-success">${{ number_format($monto_solicitado, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-center mt-4">
                    {{-- Este botón te devuelve al formulario usando la ruta que definimos --}}
                    <a href="{{ route('creditos.estado1.form') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Volver y Calcular Otro Crédito
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-base-layout>