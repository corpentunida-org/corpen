<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\ConExtractoTransaccion;
use App\Models\Contabilidad\ConCuentaBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Añadido para inserción masiva
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ConExtractoTransaccionController extends Controller
{
    /**
     * Muestra la vista de mantenimiento ultra-profesional.
     */
    public function mantenimiento()
    {
        return view('contabilidad.mantenimiento');
    }
    /**
     * Activa o desactiva el modo mantenimiento en la caché del sistema.
     */
    public function toggleMantenimiento(Request $request)
    {
        Cache::forever('contabilidad_mantenimiento_active', $request->estado);
        return response()->json(['success' => true, 'estado' => $request->estado]);
    }

  /**
     * Muestra el listado de extractos bancarios.
     * Incluye lógica de rastreo mensual y global unificada.
     */
    public function index(Request $request)
    {
        // 1. Recibir todas las variables del formulario unificado
        $periodo   = $request->input('periodo', date('Y-m'));
        $banco_id  = $request->input('banco_id');
        $search    = $request->input('search');
        $estado    = $request->input('estado');
        $is_global = $request->has('is_global'); // Switch: ¿Buscar en toda la historia (ignorar mes)?

        // Inicializar la consulta base con su relación (Eager Loading para optimizar memoria)
        $query = ConExtractoTransaccion::with('cuentaBancaria');

        // ==========================================
        // 2. LÓGICA DE PERIODO (Local vs Global)
        // ==========================================
        // Si el switch de "Rastreo Global" NO está activo, limitamos la carga al mes seleccionado
        if (!$is_global) {
            $parts = explode('-', $periodo);
            $year  = $parts[0] ?? date('Y');
            $month = $parts[1] ?? date('m');

            $query->whereYear('fecha_movimiento', $year)
                  ->whereMonth('fecha_movimiento', $month);
        }

        // ==========================================
        // 3. APLICACIÓN DE FILTROS COMPARTIDOS
        // (Estos aplican tanto si buscas en el mes como si buscas en toda la BD)
        // ==========================================
        
        // Filtro A: Por Cuenta Bancaria específica
        if (!empty($banco_id)) {
            $query->where('id_con_cuentas_bancaria', $banco_id);
        }

        // Filtro B: Por Estado de Conciliación
        if (!empty($estado)) {
            if ($estado === 'Conciliados') {
                // Agrupamos las dos variantes de éxito bajo la misma etiqueta
                $query->whereIn('estado_conciliacion', ['Conciliado_Auto', 'Conciliado_Manual']);
            } else {
                // Busca la coincidencia exacta ('Pendiente' o 'Anulado')
                $query->where('estado_conciliacion', $estado);
            }
        }

        // Filtro C: Búsqueda Libre Multicolumna (Cédula, Hash o Valor Exacto)
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }

        // ==========================================
        // 4. EJECUCIÓN Y RETORNO DE DATOS
        // ==========================================
        
        // Ejecutar consulta con paginación de alto rendimiento (100 por página)
        // withQueryString() garantiza que al cambiar de página (2, 3...) no se borren los filtros
        $extractos = $query->orderBy('fecha_movimiento', 'desc')
                           ->paginate(100)
                           ->withQueryString();

        // Obtener solo las cuentas activas para el select del formulario
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();

        return view('contabilidad.extractos.index', compact(
            'extractos', 
            'cuentas', 
            'periodo', 
            'banco_id', 
            'search', 
            'estado', 
            'is_global'
        ));
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


    /**
     * PASO 1: Procesa el archivo y envía los hashes existentes a la vista para validación dinámica.
     */
    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'archivo_extracto'        => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
        ]);

        $idCuenta = $request->id_con_cuentas_bancaria;
        $archivo = $request->file('archivo_extracto');
        $registrosPrevia = [];

        // 1. Cargamos hashes de la BD para validación contra registros antiguos
        $hashesExistentes = ConExtractoTransaccion::where('id_con_cuentas_bancaria', $idCuenta)
                            ->pluck('hash_transaccion')
                            ->toArray();

        // 2. Rastreador para detectar duplicados DENTRO del mismo archivo
        $hashesVistosEnArchivo = [];

        try {
            $datosArchivo = Excel::toArray(new class {}, $archivo)[0];
            $isHeader = true;

            foreach ($datosArchivo as $row) {
                if ($isHeader) { $isHeader = false; continue; }

                if (!empty($row[0])) {
                    $fechaRow    = trim($row[0]);
                    $cedula      = isset($row[1]) ? trim($row[1]) : '';
                    $montoBruto  = isset($row[2]) ? trim($row[2]) : '0';
                    $montoBruto  = str_replace(',', '.', $montoBruto);
                    $monto       = (float) $montoBruto;
                    $oficina     = isset($row[3]) ? trim($row[3]) : null;

                    // Formateo de fecha
                    if (is_numeric($fechaRow)) {
                        $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fechaRow);
                        $fechaFormateada = $excelDate->format('YmdHis');
                        $fechaVista = $excelDate->format('Y-m-d\TH:i');
                    } else {
                        try {
                            $fechaCarbon = Carbon::parse(str_replace('/', '-', $fechaRow));
                            $fechaFormateada = $fechaCarbon->format('YmdHis');
                            $fechaVista = $fechaCarbon->format('Y-m-d\TH:i');
                        } catch (\Exception $e) {
                            $fechaFormateada = str_replace(['-', '/', ':', ' '], '', $fechaRow);
                            $fechaVista = $fechaRow;
                        }
                    }

                    $hashCalculado = "{$idCuenta}-{$fechaFormateada}-{$monto}-{$cedula}";

                    // --- LÓGICA DE DETECCIÓN MEJORADA ---
                    // Es duplicado si: YA está en la BD  O  YA apareció en una fila anterior de este archivo
                    $esDuplicado = in_array($hashCalculado, $hashesExistentes) || isset($hashesVistosEnArchivo[$hashCalculado]);

                    $registrosPrevia[] = [
                        'fecha_movimiento'   => $fechaFormateada,
                        'fecha_vista'        => $fechaVista,
                        'hash_transaccion'   => $hashCalculado,
                        'valor_ingreso'      => $monto,
                        'referencia_cedula'  => $cedula,
                        'referencia_oficina' => $oficina,
                        'es_duplicado'       => $esDuplicado,
                    ];

                    // Importante: Marcamos este hash como "ya visto" para las siguientes filas del ciclo
                    $hashesVistosEnArchivo[$hashCalculado] = true;
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error leyendo el archivo: ' . $e->getMessage()]);
        }

        $cuenta = ConCuentaBancaria::find($idCuenta);
        session(['importacion_cuenta_id' => $idCuenta]);

        return view('contabilidad.extractos.validar', compact('registrosPrevia', 'cuenta', 'hashesExistentes'));
    }

    /**
     * PASO 2: Confirmación TURBO (Bulk Insert) optimizado para alto volumen.
     */
    public function confirmarImportacion(Request $request)
    {
        // 1. Aumentamos recursos para evitar Timeouts
        ini_set('max_execution_time', 600); 
        ini_set('memory_limit', '512M');

        $idCuenta = session('importacion_cuenta_id');
        $jsonDatos = $request->input('registros_json');
        $registrosEditados = json_decode($jsonDatos, true); 

        if (!$registrosEditados || !$idCuenta) {
            return redirect()->route('contabilidad.extractos.importar')
                             ->withErrors('La sesión expiró o los datos están vacíos.');
        }

        $dataParaInsertar = [];
        $detallesOmitidos = []; 
        $ahora = now();

        try {
            // 2. Traemos todos los hashes existentes de una sola vez para búsqueda instantánea
            $hashesExistentes = ConExtractoTransaccion::where('id_con_cuentas_bancaria', $idCuenta)
                                ->pluck('hash_transaccion')
                                ->toArray();

            $hashesLookup = array_flip($hashesExistentes);

            foreach ($registrosEditados as $row) {
                $monto = (float) $row['valor_ingreso'];
                $cedula = trim($row['referencia_cedula'] ?? '');
                
                $fechaCarbon = Carbon::parse($row['fecha_movimiento']);
                $fechaDB = $fechaCarbon->format('Y-m-d H:i:s');
                $fechaParaHash = $fechaCarbon->format('YmdHis');

                $hashCalculado = "{$idCuenta}-{$fechaParaHash}-{$monto}-{$cedula}";

                if (!isset($hashesLookup[$hashCalculado])) {
                    // Preparamos para Bulk Insert
                    $dataParaInsertar[] = [
                        'hash_transaccion'        => $hashCalculado,
                        'id_con_cuentas_bancaria' => $idCuenta,
                        'fecha_movimiento'        => $fechaDB,
                        'valor_ingreso'           => $monto,
                        'referencia_cedula'       => $cedula,
                        'referencia_oficina'      => trim($row['referencia_oficina'] ?? ''),
                        'estado_conciliacion'     => 'Pendiente',
                        'created_at'              => $ahora,
                        'updated_at'              => $ahora,
                    ];
                    
                    $hashesLookup[$hashCalculado] = true; 
                } else {
                    $detallesOmitidos[] = [
                        'fecha'  => $fechaDB,
                        'cedula' => $cedula,
                        'valor'  => $monto,
                        'hash'   => $hashCalculado
                    ];
                }
            }

            // 3. INSERCIÓN TURBO: Insertamos en bloques de 1,000 registros
            if (count($dataParaInsertar) > 0) {
                $chunks = array_chunk($dataParaInsertar, 1000);
                foreach ($chunks as $chunk) {
                    DB::table('con_extractos_transacciones')->insert($chunk);
                }
            }

            session()->forget(['importacion_cuenta_id']);

            $reporteLimitado = array_slice($detallesOmitidos, 0, 100);

            return redirect()->route('contabilidad.extractos.index')->with([
                'success' => "Proceso completado. Se guardaron " . count($dataParaInsertar) . " registros nuevos.",
                'reporte_importacion' => [
                    'nuevos'            => count($dataParaInsertar),
                    'omitidos_count'    => count($detallesOmitidos),
                    'detalles_omitidos' => $reporteLimitado,
                    'tiene_exceso'      => count($detallesOmitidos) > 100
                ]
            ]);

        } catch (\Exception $e) {
            return redirect()->route('contabilidad.extractos.index')
                             ->withErrors("Error crítico en la importación: " . $e->getMessage());
        }
    }
    

    public function conciliacion(Request $request)
    {
        $periodo   = $request->input('periodo', date('Y-m'));
        $banco_id  = $request->input('banco_id');
        $search    = $request->input('search');
        $is_global = $request->has('is_global'); // Switch: ¿Buscar en toda la historia (ignorar mes)?

        // LADO IZQUIERDO: EXTRACTOS BANCO (Siempre pendientes en la mesa de conciliación)
        $queryBanco = ConExtractoTransaccion::with('cuentaBancaria')
                        ->where('estado_conciliacion', 'Pendiente');

        // LADO DERECHO: CARTERA (Siempre no conciliados en la mesa de conciliación)
        $queryCartera = \App\Models\Cartera\CarComprobantePago::with(['tercero', 'obligacion', 'banco', 'user'])
                ->where('estado', '!=', 'conciliado');

        // ==========================================
        // LÓGICA DE PERIODO (Local vs Global)
        // ==========================================
        if (!$is_global) {
            $parts = explode('-', $periodo);
            $year  = $parts[0] ?? date('Y');
            $month = $parts[1] ?? date('m');

            $queryBanco->whereYear('fecha_movimiento', $year)
                       ->whereMonth('fecha_movimiento', $month);

            $queryCartera->whereYear('fecha_pago', $year)
                         ->whereMonth('fecha_pago', $month);
        }

        // ==========================================
        // FILTROS ESPECÍFICOS LADO IZQUIERDO (BANCO)
        // ==========================================
        if (!empty($banco_id)) {
            $queryBanco->where('id_con_cuentas_bancaria', $banco_id);
        }
        if (!empty($search)) {
            $queryBanco->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }
        
        $extractosPendientes = $queryBanco->orderBy('fecha_movimiento', 'desc')
                                          ->paginate(100, ['*'], 'banco_page')
                                          ->withQueryString();

        // ==========================================
        // FILTROS ESPECÍFICOS LADO DERECHO (CARTERA)
        // ==========================================
        if (!empty($search)) {
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
            'cuentas', 'periodo', 'banco_id', 'search', 'is_global'
        ));
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
                $comprobante->id_transaccion_bancaria = [$extracto->id_transaccion];
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
        // 1. Validar que recibimos un texto (JSON) con los IDs y los datos de la interacción
        $request->validate([
            'id_transacciones' => 'required|string', 
            'id_comprobante'   => 'required|exists:car_comprobantes_pagos,id',
            'id_interaccion'   => 'nullable|integer',
            'temp_token'       => 'nullable|string'
        ]);

        // 2. Decodificar el JSON a un array de PHP
        $idsTransacciones = json_decode($request->id_transacciones, true);

        if (!is_array($idsTransacciones) || empty($idsTransacciones)) {
            // Si es petición del modal (AJAX)
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Debes seleccionar al menos un movimiento bancario.']);
            }
            return redirect()->back()->withErrors('Debes seleccionar al menos un movimiento bancario.');
        }

        try {
            // 3. Actualizar el comprobante de cartera
            $comprobante = \App\Models\Cartera\CarComprobantePago::findOrFail($request->id_comprobante);
            
            // Asignamos el array (Laravel lo convierte a JSON en BD por el $casts del modelo)
            $comprobante->id_transaccion_bancaria = $idsTransacciones;
            $comprobante->estado = 'conciliado';

            // Vinculamos la interacción si el modal la envía
            if ($request->has('id_interaccion') && $request->id_interaccion > 0) {
                $comprobante->id_interaccion = $request->id_interaccion;
            }

            $comprobante->save();

            // 4. Actualizar masivamente TODOS los extractos seleccionados a 'Conciliado_Manual'
            ConExtractoTransaccion::whereIn('id_transaccion', $idsTransacciones)
                ->update(['estado_conciliacion' => 'Conciliado_Manual']);

            // 5. RESPUESTA JSON PARA EL MODAL (ÉXITO)
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'success' => true, 
                    'message' => 'Los ' . count($idsTransacciones) . ' registros fueron vinculados manualmente con éxito.'
                ]);
            }

            // Fallback por si se envía desde un formulario normal sin AJAX
            return redirect()->back()->with('success', 'Los ' . count($idsTransacciones) . ' registros fueron vinculados manualmente con éxito.');

        } catch (\Exception $e) {
            \Log::error("Error en conciliacionManual: " . $e->getMessage());
            
            // Respuesta de error para el Modal
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors('Error crítico: ' . $e->getMessage());
        }
    }

    public function buscarModalApi(Request $request)
    {
        try {
            $search = $request->input('search');

            if (empty($search)) {
                return response()->json(['bancos' => [], 'cartera' => []]);
            }

            // LADO IZQUIERDO: BANCOS PENDIENTES
            $bancos = ConExtractoTransaccion::with('cuentaBancaria')
                ->where('estado_conciliacion', 'Pendiente')
                ->where(function($q) use ($search) {
                    $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                      ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                      ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
                })
                ->orderBy('fecha_movimiento', 'desc')
                ->limit(50)
                ->get()
                ->map(function($item) {
                    try {
                        $fechaBanco = Carbon::parse($item->fecha_movimiento)->format('d/m/Y');
                    } catch (\Exception $e) {
                        $fechaBanco = substr((string)$item->fecha_movimiento, 0, 10);
                    }

                    return [
                        'id' => $item->id_transaccion ?? $item->id,
                        'fecha' => $fechaBanco,
                        'oficina' => $item->referencia_oficina ?? '---',
                        'banco' => optional($item->cuentaBancaria)->banco ?? 'Banco',
                        'valor' => number_format((float)($item->valor_ingreso ?? 0), 0, ',', '.')
                    ];
                });

            // LADO DERECHO: CARTERA NO CONCILIADA (CERO TERCEROS AQUÍ)
            $cartera = \App\Models\Cartera\CarComprobantePago::with(['obligacion', 'banco']) 
                ->where('estado', '!=', 'conciliado')
                ->where(function($q) use ($search) {
                    $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$search}%")
                      ->orWhere('monto_pagado', 'LIKE', "%{$search}%")
                      ->orWhere('ruta_archivo', 'LIKE', "%{$search}%");
                })
                ->orderBy('fecha_pago', 'desc')
                ->limit(50)
                ->get()
                ->map(function($item) {
                    try {
                        // 1. Convertir a string para manipular los dígitos
                        $valorFecha = (string) $item->fecha_pago;
                        
                        // 2. Si tiene ese "2" extra al inicio (o cualquier otro prefijo)
                        // y supera los 14 dígitos de un formato YmdHis normal, cortamos los últimos 14.
                        if (strlen($valorFecha) > 14) {
                            $valorFecha = substr($valorFecha, -14);
                        }

                        // 3. Leemos el formato exacto YmdHis (AñoMesDiaHoraMinutoSegundo)
                        $fecha = Carbon::createFromFormat('YmdHis', $valorFecha)->format('d/m/Y');
                        
                    } catch (\Exception $e) {
                        // Si por algún motivo llega un dato corrupto que no cumple el formato
                        $fecha = (string) $item->fecha_pago;
                    }

                    return [
                        'id' => $item->id,
                        'fecha' => $fecha,
                        'obligacion' => optional($item->obligacion)->nombre ?? 'N/A',
                        'cuota' => $item->numero_cuota ?? '-',
                        'valor' => number_format((float)($item->monto_pagado ?? 0), 0, ',', '.'),
                        'url_archivo' => (isset($item->url_archivo) && $item->url_archivo != '#') ? $item->url_archivo : null,
                    ];
                });

            return response()->json([
                'bancos' => $bancos,
                'cartera' => $cartera
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'bancos' => [],
                'cartera' => [],
                'ERROR_LARAVEL' => $e->getMessage()
            ]);
        }
    }
}