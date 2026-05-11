<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\ConExtractoTransaccion;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        // 1. Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 2. Definir los encabezados (Fila 1)
        $sheet->setCellValue('A1', 'Fecha');
        $sheet->setCellValue('B1', 'Cedula');
        $sheet->setCellValue('C1', 'Valor');
        $sheet->setCellValue('D1', 'Oficina');

        // Opcional: Poner los encabezados en negrita
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);

        // 3. Insertar datos de ejemplo (Filas 2 y 3)
        $sheet->setCellValue('A2', '2023-10-27');
        $sheet->setCellValue('B2', '12345678');
        $sheet->setCellValue('C2', 50000.00);
        $sheet->setCellValue('D2', 'Oficina Central');

        $sheet->setCellValue('A3', '2023-10-28');
        $sheet->setCellValue('B3', '87654321');
        $sheet->setCellValue('C3', 1250.50);
        $sheet->setCellValue('D3', 'Sucursal Norte');

        // Opcional: Auto-ajustar el ancho de las columnas
        foreach (range('A', 'D') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        // 4. Preparar el escritor para XLSX
        $writer = new Xlsx($spreadsheet);

        // 5. Devolver la respuesta como un archivo descargable
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        // Configurar las cabeceras para Excel
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="plantilla_importacion.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
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
        $idCuenta = session('importacion_cuenta_id');
        $jsonDatos = $request->input('registros_json');
        $registrosEditados = json_decode($jsonDatos, true); 

        if (!$registrosEditados || !$idCuenta) {
            return redirect()->route('contabilidad.extractos.importar')
                             ->withErrors('La sesión expiró o no hay datos para importar. Por favor, sube el archivo de nuevo.');
        }

        $registrosImportados = 0;
        $registrosOmitidos = 0;

        try {
            foreach ($registrosEditados as $row) {
                // 1. Limpieza de montos y strings
                $montoBruto = str_replace(',', '.', $row['valor_ingreso']);
                $monto      = (float) $montoBruto;
                $cedula     = trim($row['referencia_cedula'] ?? '');
                $oficina    = trim($row['referencia_oficina'] ?? '');
                
                // 2. Formateo de fecha estricto para MySQL (Y-m-d)
                $fechaRaw = $row['fecha_movimiento']; // Viene como "20260506"
                $fechaDB  = strlen($fechaRaw) === 8 
                            ? substr($fechaRaw, 0, 4) . '-' . substr($fechaRaw, 4, 2) . '-' . substr($fechaRaw, 6, 2) 
                            : $fechaRaw;

                // 3. Calculamos el Hash (usamos la fechaRaw para que el hash coincida con el Excel original)
                $hashCalculado = "{$idCuenta}-{$fechaRaw}-{$monto}-{$cedula}";

                // 4. Intentamos guardar
                $transaccion = ConExtractoTransaccion::firstOrCreate(
                    ['hash_transaccion' => $hashCalculado], 
                    [
                        'id_con_cuentas_bancaria' => $idCuenta,
                        'fecha_movimiento'        => $fechaDB, // Usamos la fecha formateada aquí
                        'valor_ingreso'           => $monto,
                        'referencia_cedula'       => $cedula,
                        'referencia_oficina'      => $oficina,
                        'estado_conciliacion'     => 'Pendiente',
                    ]
                );

                if ($transaccion->wasRecentlyCreated) {
                    $registrosImportados++;
                } else {
                    $registrosOmitidos++;
                }
            }

            session()->forget(['importacion_temporal', 'importacion_cuenta_id']);

            return redirect()->route('contabilidad.extractos.index')
                             ->with('success', "Proceso completado: Se guardaron $registrosImportados registros nuevos. Se omitieron $registrosOmitidos repetidos.");

        } catch (\Exception $e) {
            // Si hay un error silencioso de base de datos, ¡ahora lo veremos!
            return redirect()->route('contabilidad.extractos.index')
                             ->withErrors("Hubo un error al guardar en la base de datos: " . $e->getMessage());
        }
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