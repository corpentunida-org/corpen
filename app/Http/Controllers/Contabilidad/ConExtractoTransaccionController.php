<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\ConExtractoTransaccion;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ConExtractoTransaccionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filtro Obligatorio: Periodo (Año y Mes). Por defecto el mes actual.
        $periodo = $request->input('periodo', date('Y-m'));
        $parts = explode('-', $periodo);
        $year = $parts[0] ?? date('Y');
        $month = $parts[1] ?? date('m');

        // 2. Filtros Opcionales
        $banco_id = $request->input('banco_id');
        $search = $request->input('search');

        // 3. Consulta Base (Obligatorio filtrar por Año y Mes para no saturar la BD)
        $query = ConExtractoTransaccion::with('cuentaBancaria')
                    ->whereYear('fecha_movimiento', $year)
                    ->whereMonth('fecha_movimiento', $month);

        // Aplicar Filtro de Banco si existe
        if ($banco_id) {
            $query->where('id_con_cuentas_bancaria', $banco_id);
        }

        // Aplicar Buscador Global (ajustado sin los campos eliminados)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }

        // 4. Paginación
        $extractos = $query->orderBy('fecha_movimiento', 'desc')->paginate(100)->withQueryString();

        // 5. Datos para llenar los Selects
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();

        return view('contabilidad.extractos.index', compact('extractos', 'cuentas', 'periodo', 'banco_id', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'hash_transaccion'        => 'required|string|unique:con_extractos_transacciones,hash_transaccion',
            'fecha_movimiento'        => 'required|date',
            'referencia_cedula'       => 'nullable|string|max:255',
            'valor_ingreso'           => 'required|numeric',
            'referencia_oficina'      => 'nullable|string|max:255',
            'estado_conciliacion'     => 'required|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
        ]);

        ConExtractoTransaccion::create($validated);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'El movimiento del extracto fue registrado exitosamente.');
    }

    public function show($id)
    {
        $extracto = ConExtractoTransaccion::with('cuentaBancaria')->findOrFail($id);
        return view('contabilidad.extractos.show', compact('extracto'));
    }

    public function update(Request $request, $id)
    {
        $transaccion = ConExtractoTransaccion::findOrFail($id);

        $validated = $request->validate([
            'estado_conciliacion' => 'sometimes|in:Pendiente,Conciliado_Auto,Conciliado_Manual,Anulado',
            'referencia_cedula'   => 'sometimes|nullable|string|max:255',
            'referencia_oficina'  => 'sometimes|nullable|string|max:255',
        ]);

        $transaccion->update($validated);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'El estado del movimiento ha sido actualizado.');
    }

    public function destroy($id)
    {
        $transaccion = ConExtractoTransaccion::findOrFail($id);
        $transaccion->delete();

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', 'Movimiento eliminado correctamente.');
    }

    // ==========================================
    // MÉTODOS NUEVOS PARA LAS VISTAS Y LÓGICA
    // ==========================================

    public function importar()
    {
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();
        return view('contabilidad.extractos.importar', compact('cuentas'));
    }

    /**
     * Genera y descarga la plantilla dinámica para el usuario
     */
    public function descargarPlantilla()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=plantilla_importacion.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columnas = ['Fecha', 'Cedula', 'Valor', 'Oficina'];
        $ejemplo1 = ['2023-10-27', '12345678', '50000.00', 'Oficina Central'];
        $ejemplo2 = ['2023-10-28', '87654321', '1250.50', 'Sucursal Norte'];

        $callback = function() use($columnas, $ejemplo1, $ejemplo2) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM para tildes en Excel
            fputcsv($file, $columnas, ';');
            fputcsv($file, $ejemplo1, ';');
            fputcsv($file, $ejemplo2, ';');
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function conciliacion(Request $request)
    {
        $periodo = $request->input('periodo', date('Y-m'));
        $parts = explode('-', $periodo);
        $year = $parts[0] ?? date('Y');
        $month = $parts[1] ?? date('m');

        $banco_id = $request->input('banco_id');
        $search = $request->input('search');

        // LADO IZQUIERDO: EXTRACTOS BANCO
        $queryBanco = ConExtractoTransaccion::with('cuentaBancaria')
                        ->where('estado_conciliacion', 'Pendiente')
                        ->whereYear('fecha_movimiento', $year)
                        ->whereMonth('fecha_movimiento', $month);

        if ($banco_id) {
            $queryBanco->where('id_con_cuentas_bancaria', $banco_id);
        }
        if ($search) {
            $queryBanco->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }
        $extractosPendientes = $queryBanco->orderBy('fecha_movimiento', 'desc')
                                          ->paginate(100, ['*'], 'banco_page')
                                          ->withQueryString();

        // LADO DERECHO: CARTERA
        $queryCartera = \App\Models\Cartera\CarComprobantePago::where('estado', '!=', 'conciliado')
                        ->whereYear('fecha_pago', $year)
                        ->whereMonth('fecha_pago', $month);

        if ($search) {
            $queryCartera->where(function($q) use ($search) {
                $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$search}%")
                  ->orWhere('ruta_archivo', 'LIKE', "%{$search}%")
                  ->orWhere('monto_pagado', 'LIKE', "%{$search}%");
            });
        }
        $comprobantesCartera = $queryCartera->orderBy('fecha_pago', 'desc')
                                            ->paginate(100, ['*'], 'cartera_page')
                                            ->withQueryString();

        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();

        return view('contabilidad.extractos.conciliacion', compact(
            'extractosPendientes', 'comprobantesCartera', 
            'cuentas', 'periodo', 'banco_id', 'search'
        ));
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'archivo_extracto'        => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
        ]);

        $idCuenta = $request->id_con_cuentas_bancaria;
        $archivo = $request->file('archivo_extracto');
        $registrosPrevia = [];

        try {
            $datosArchivo = Excel::toArray([], $archivo)[0];
            $isHeader = true;

            foreach ($datosArchivo as $row) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                if (!empty($row[0])) {
                    // SE AJUSTARON LOS ÍNDICES PARA LA NUEVA ESTRUCTURA: Fecha(0), Cedula(1), Valor(2), Oficina(3)
                    $fechaRow    = trim($row[0]);
                    $cedula      = isset($row[1]) ? trim($row[1]) : '';
                    
                    $montoBruto  = isset($row[2]) ? trim($row[2]) : '0';
                    $montoBruto  = str_replace(',', '.', $montoBruto);
                    $monto       = (float) $montoBruto;
                    
                    $oficina     = isset($row[3]) ? trim($row[3]) : null;

                    // Formateo de fecha
                    if (is_numeric($fechaRow)) {
                        $fechaFormateada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRow)->format('Ymd');
                        $fechaVista = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRow)->format('d/m/Y');
                    } else {
                        try {
                            $fechaCarbon = \Carbon\Carbon::parse(str_replace('/', '-', $fechaRow));
                            $fechaFormateada = $fechaCarbon->format('Ymd');
                            $fechaVista = $fechaCarbon->format('d/m/Y');
                        } catch (\Exception $e) {
                            $fechaFormateada = str_replace(['-', '/'], '', $fechaRow);
                            $fechaVista = $fechaRow;
                        }
                    }

                    // Autogeneramos el Hash
                    $hashCalculado = "{$idCuenta}-{$fechaFormateada}-{$monto}-{$cedula}";

                    $registrosPrevia[] = [
                        'fecha_movimiento'   => $fechaFormateada,
                        'fecha_vista'        => $fechaVista,
                        'hash_transaccion'   => $hashCalculado,
                        'valor_ingreso'      => $monto,
                        'referencia_cedula'  => $cedula,
                        'referencia_oficina' => $oficina,
                    ];
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error leyendo el archivo. Asegúrate de que el formato sea correcto. Error: ' . $e->getMessage()]);
        }

        $cuenta = ConCuentaBancaria::find($idCuenta);

        session([
            'importacion_temporal' => $registrosPrevia, 
            'importacion_cuenta_id' => $idCuenta
        ]);

        return view('contabilidad.extractos.validar', compact('registrosPrevia', 'cuenta'));
    }

    public function confirmarImportacion(Request $request)
    {
        $registros = session('importacion_temporal');
        $idCuenta = session('importacion_cuenta_id');

        if (!$registros || !$idCuenta) {
            return redirect()->route('contabilidad.extractos.importar')
                             ->withErrors('La sesión expiró o no hay datos para importar. Por favor, sube el archivo de nuevo.');
        }

        $registrosImportados = 0;

        foreach ($registros as $row) {
            ConExtractoTransaccion::updateOrCreate(
                ['hash_transaccion' => $row['hash_transaccion']], 
                [
                    'id_con_cuentas_bancaria' => $idCuenta,
                    'fecha_movimiento'        => $row['fecha_movimiento'],
                    'valor_ingreso'           => $row['valor_ingreso'],
                    'referencia_cedula'       => $row['referencia_cedula'],
                    'referencia_oficina'      => $row['referencia_oficina'],
                    'estado_conciliacion'     => 'Pendiente',
                ]
            );
            $registrosImportados++;
        }

        session()->forget(['importacion_temporal', 'importacion_cuenta_id']);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', "¡Aprobación exitosa! Se procesaron $registrosImportados movimientos bancarios.");
    }

    public function conciliacionAutomatica()
    {
        $extractosPendientes = ConExtractoTransaccion::where('estado_conciliacion', 'Pendiente')->get();
        $contador = 0;

        foreach ($extractosPendientes as $extracto) {
            $comprobante = \App\Models\Cartera\CarComprobantePago::where('hash_transaccion', $extracto->hash_transaccion)
                ->where(function($q) {
                    $q->whereNull('id_transaccion_bancaria')
                      ->orWhere('estado', '!=', 'conciliado');
                })
                ->first();

            if ($comprobante) {
                $comprobante->id_transaccion_bancaria = $extracto->id_transaccion;
                $comprobante->estado = 'conciliado';
                $comprobante->save();

                $extracto->estado_conciliacion = 'Conciliado_Auto';
                $extracto->save();

                $contador++;
            }
        }

        return redirect()->back()->with('success', "¡Proceso completado! Se lograron conciliar $contador registros automáticamente.");
    }

    public function conciliacionManual(Request $request)
    {
        $request->validate([
            'id_transaccion' => 'required|exists:con_extractos_transacciones,id_transaccion',
            'id_comprobante' => 'required|exists:car_comprobantes_pagos,id',
        ]);

        $extracto = ConExtractoTransaccion::findOrFail($request->id_transaccion);
        $comprobante = \App\Models\Cartera\CarComprobantePago::findOrFail($request->id_comprobante);

        $comprobante->id_transaccion_bancaria = $extracto->id_transaccion;
        $comprobante->estado = 'conciliado';
        $comprobante->save();

        $extracto->estado_conciliacion = 'Conciliado_Manual';
        $extracto->save();

        return redirect()->back()->with('success', 'Los registros fueron vinculados manualmente con éxito.');
    }
}