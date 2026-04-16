<x-base-layout>
    <div class="app-container py-5" style="background-color: #f8f9fa;">
        
        {{-- Barra de Título Estilo Documento --}}
        <div class="d-flex align-items-center mb-5 px-3 border-bottom border-gray-300 pb-4">
            <div class="symbol symbol-40px me-3">
                <div class="symbol-label bg-warning text-dark">
                    <i class="fas fa-search-dollar text-dark"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold m-0 text-dark fs-4">Previsualización de Extracto Bancario</h3>
                <div class="d-flex align-items-center gap-3 fs-9 mt-1">
                    <span class="text-muted">Cuenta Destino: <strong>{{ $cuenta->banco }} - {{ $cuenta->numero_cuenta }}</strong></span>
                    <span class="badge badge-light-warning text-warning fw-bold px-2 py-1">DATOS TEMPORALES (NO GUARDADOS)</span>
                </div>
            </div>
            <div class="ms-auto d-flex gap-3">
                <a href="{{ route('contabilidad.extractos.importar') }}" class="btn btn-sm btn-light fw-bold px-4 rounded-1">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                
                {{-- Formulario de Aprobación Final --}}
                <form action="{{ route('contabilidad.extractos.confirmar-importacion') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-4 rounded-1 shadow-sm">
                        <i class="fas fa-check-double me-1"></i> Aprobar y Guardar
                    </button>
                </form>
            </div>
        </div>

        {{-- Contenedor de la Hoja --}}
        <div class="bg-white shadow-sm border border-gray-300 mx-3" style="border-radius: 0px; overflow: hidden;">
            
            <div class="d-flex bg-gray-100 border-bottom border-gray-300">
                <div class="sheet-tab active">
                    <i class="fas fa-table me-2 fs-9"></i> Extracto_{{ date('Ymd') }}.csv
                </div>
            </div>

            <div class="table-responsive" id="resizable-container">
                <table class="table-gsheets" id="main-table">
                    <thead>
                        <tr>
                            <th class="col-index"></th>
                            <th>FECHA MOVIMIENTO</th>
                            <th>HASH TRANSACCIÓN (Único)</th>
                            <th style="min-width: 250px;">DESCRIPCIÓN DEL BANCO</th>
                            <th>VALOR INGRESO</th>
                            {{-- NUEVAS COLUMNAS --}}
                            <th>CÉDULA/REF</th>
                            <th>NOMBRE REFERENCIA</th>
                            <th>OFICINA</th>
                            <th>DISTRITO</th>
                            {{-- FIN NUEVAS --}}
                            <th>ESTADO A ASIGNAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrosPrevia as $index => $fila)
                        <tr>
                            <td class="col-index">{{ $index + 1 }}</td>
                            
                            <td class="text-center">
                                <i class="far fa-calendar-alt text-muted me-1"></i> {{ $fila['fecha_vista'] }}
                            </td>
                            
                            <td class="font-monospace fs-10 text-muted">{{ $fila['hash_transaccion'] }}</td>
                            
                            <td class="text-uppercase text-gray-800">
                                {{ $fila['descripcion_banco'] }}
                            </td>
                            
                            <td class="text-end fw-bold text-success pe-4">
                                $ {{ number_format($fila['valor_ingreso'], 0, ',', '.') }}
                            </td>

                            {{-- NUEVAS COLUMNAS --}}
                            <td class="text-center text-gray-700">{{ $fila['referencia_cedula'] ?? '---' }}</td>
                            <td class="text-uppercase text-gray-700">{{ $fila['referencia_nombre'] ?? '---' }}</td>
                            <td class="text-center text-gray-600">{{ $fila['referencia_oficina'] ?? '---' }}</td>
                            <td class="text-center text-gray-600">{{ $fila['referencia_distrito'] ?? '---' }}</td>
                            {{-- FIN NUEVAS --}}

                            <td class="text-center p-0">
                                <div class="gs-badge gs-warning">PENDIENTE</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-10 text-muted fs-8 italic">El archivo parece estar vacío o no tiene el formato correcto.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Barra de Estado --}}
            <div class="bg-white border-top border-gray-300 p-2 d-flex justify-content-between align-items-center fs-9 text-muted">
                <div>Filas detectadas: <strong>{{ count($registrosPrevia) }}</strong> | Esperando revisión manual...</div>
            </div>
        </div>
    </div>

    <style>
        body, .app-container { font-family: 'Inter', sans-serif !important; }
        
        /* TABLA ESTILO GOOGLE SHEETS */
        .table-gsheets {
            width: auto; min-width: 100%; border-collapse: collapse; font-size: 11px; color: #3c4043; table-layout: auto;
        }
        .table-gsheets thead th {
            background-color: #f8f9fa; border: 1px solid #dadce0; padding: 8px 12px; font-weight: 500; color: #5f6368; text-align: left; white-space: nowrap;
        }
        .table-gsheets tbody td {
            border: 1px solid #dadce0; padding: 4px 12px; height: 32px; vertical-align: middle; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .table-gsheets tbody tr:hover { background-color: #e8f0fe !important; }
        .col-index {
            background-color: #f8f9fa; text-align: center !important; min-width: 40px; color: #5f6368; font-weight: bold; border-right: 2px solid #dadce0 !important;
        }
        .sheet-tab {
            padding: 8px 16px; font-size: 11px; font-weight: 500; color: #5f6368; text-decoration: none; border-right: 1px solid #dadce0; background-color: #f1f3f4;
        }
        .sheet-tab.active { background-color: #fff; color: #188038; border-bottom: 3px solid #188038; }
        .gs-badge {
            width: 100%; height: 31px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 9px;
        }
        .gs-warning { background-color: #fef7e0; color: #b06000; }
    </style>
</x-base-layout>