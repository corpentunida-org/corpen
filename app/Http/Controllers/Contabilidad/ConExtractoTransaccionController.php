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
        $distrito = $request->input('distrito');
        $search = $request->input('search');

        // 3. Consulta Base (Obligatorio filtrar por Año y Mes para no saturar la BD)
        $query = ConExtractoTransaccion::with('cuentaBancaria')
                    ->whereYear('fecha_movimiento', $year)
                    ->whereMonth('fecha_movimiento', $month);

        // Aplicar Filtro de Banco si existe
        if ($banco_id) {
            $query->where('id_con_cuentas_bancaria', $banco_id);
        }

        // Aplicar Filtro de Distrito si existe
        if ($distrito) {
            $query->where('referencia_distrito', 'LIKE', "%{$distrito}%");
        }

        // Aplicar Buscador Global
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_nombre', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion_banco', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }

        // 4. Paginación (100 registros por página en lugar de traer todo)
        // El withQueryString() mantiene los filtros aplicados al cambiar de página
        $extractos = $query->orderBy('fecha_movimiento', 'desc')->paginate(100)->withQueryString();

        // 5. Datos para llenar los Selects
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();
        
        // Obtener distritos únicos que existan en la tabla para el filtro
        $distritos = ConExtractoTransaccion::select('referencia_distrito')
                        ->whereNotNull('referencia_distrito')
                        ->where('referencia_distrito', '!=', '')
                        ->distinct()
                        ->pluck('referencia_distrito');

        return view('contabilidad.extractos.index', compact('extractos', 'cuentas', 'distritos', 'periodo', 'banco_id', 'distrito', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'hash_transaccion'        => 'required|string|unique:con_extractos_transacciones,hash_transaccion',
            'fecha_movimiento'        => 'required|date',
            'referencia_cedula'       => 'nullable|string|max:255',
            'referencia_nombre'       => 'nullable|string|max:255',
            'valor_ingreso'           => 'required|numeric',
            'referencia_oficina'      => 'nullable|string|max:255',
            'referencia_distrito'     => 'nullable|string|max:255',
            'descripcion_banco'       => 'required|string',
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
            'descripcion_banco'   => 'sometimes|string',
            'referencia_cedula'   => 'sometimes|nullable|string|max:255',
            'referencia_nombre'   => 'sometimes|nullable|string|max:255',
            'referencia_oficina'  => 'sometimes|nullable|string|max:255',
            'referencia_distrito' => 'sometimes|nullable|string|max:255',
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
        // Traemos las cuentas activas para el select del formulario
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();
        
        return view('contabilidad.extractos.importar', compact('cuentas'));
    }

    public function conciliacion(Request $request)
    {
        // 1. Filtro Obligatorio: Periodo (Año y Mes). Por defecto el mes actual.
        $periodo = $request->input('periodo', date('Y-m'));
        $parts = explode('-', $periodo);
        $year = $parts[0] ?? date('Y');
        $month = $parts[1] ?? date('m');

        // 2. Filtros Opcionales
        $banco_id = $request->input('banco_id');
        $distrito = $request->input('distrito');
        $search = $request->input('search');

        // =========================================================
        // 3. CONSULTA LADO IZQUIERDO: EXTRACTOS BANCO (PENDIENTES)
        // =========================================================
        $queryBanco = ConExtractoTransaccion::with('cuentaBancaria')
                        ->where('estado_conciliacion', 'Pendiente')
                        ->whereYear('fecha_movimiento', $year)
                        ->whereMonth('fecha_movimiento', $month);

        if ($banco_id) {
            $queryBanco->where('id_con_cuentas_bancaria', $banco_id);
        }
        if ($distrito) {
            $queryBanco->where('referencia_distrito', 'LIKE', "%{$distrito}%");
        }
        if ($search) {
            $queryBanco->where(function($q) use ($search) {
                $q->where('hash_transaccion', 'LIKE', "%{$search}%")
                  ->orWhere('referencia_cedula', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion_banco', 'LIKE', "%{$search}%")
                  ->orWhere('valor_ingreso', 'LIKE', "%{$search}%");
            });
        }
        // Paginador exclusivo para el Banco
        $extractosPendientes = $queryBanco->orderBy('fecha_movimiento', 'desc')
                                          ->paginate(100, ['*'], 'banco_page')
                                          ->withQueryString();

        // =========================================================
        // 4. CONSULTA LADO DERECHO: SOPORTES CARTERA (SIN CRUZAR)
        // =========================================================
        // Filtramos directo en BD para no traer pagos ya conciliados
        $queryCartera = \App\Models\Cartera\CarComprobantePago::where('estado', '!=', 'conciliado')
                        ->whereYear('fecha_pago', $year)
                        ->whereMonth('fecha_pago', $month);

        // El buscador global también aplica a la cartera (monto o tercero)
        if ($search) {
            $queryCartera->where(function($q) use ($search) {
                $q->where('cod_ter_MaeTerceros', 'LIKE', "%{$search}%")
                  ->orWhere('ruta_archivo', 'LIKE', "%{$search}%")
                  ->orWhere('monto_pagado', 'LIKE', "%{$search}%");
            });
        }
        // Paginador exclusivo para la Cartera
        $comprobantesCartera = $queryCartera->orderBy('fecha_pago', 'desc')
                                            ->paginate(100, ['*'], 'cartera_page')
                                            ->withQueryString();

        // =========================================================
        // 5. DATOS PARA LLENAR LOS SELECTS DE LOS FILTROS
        // =========================================================
        $cuentas = ConCuentaBancaria::where('estado', 'Activa')->get();
        $distritos = ConExtractoTransaccion::select('referencia_distrito')
                        ->whereNotNull('referencia_distrito')
                        ->where('referencia_distrito', '!=', '')
                        ->distinct()
                        ->pluck('referencia_distrito');

        return view('contabilidad.extractos.conciliacion', compact(
            'extractosPendientes', 'comprobantesCartera', 
            'cuentas', 'distritos', 'periodo', 'banco_id', 'distrito', 'search'
        ));
    }

    /**
     * PASO 1: Recibe el archivo (CSV o Excel), autogenera el Hash y envía a previsualización.
     */
    public function procesarImportacion(Request $request)
    {
        // 1. Ampliamos la validación para aceptar xls y xlsx
        $request->validate([
            'id_con_cuentas_bancaria' => 'required|exists:con_cuentas_bancarias,id',
            'archivo_extracto'        => 'required|file|mimes:csv,txt,xls,xlsx|max:5120',
        ]);

        $idCuenta = $request->id_con_cuentas_bancaria;
        $archivo = $request->file('archivo_extracto');
        $registrosPrevia = [];

        try {
            // 2. Usamos la librería Excel para leer todo en un Array.
            $datosArchivo = Excel::toArray([], $archivo)[0];

            $isHeader = true;

            foreach ($datosArchivo as $row) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                // Aseguramos que la fila tenga al menos fecha para procesarla
                if (!empty($row[0])) {
                    
                    // 3. Extraemos los valores clave en el NUEVO ORDEN SECUENCIAL
                    $fechaRow    = trim($row[0]);
                    $cedula      = isset($row[1]) ? trim($row[1]) : '';
                    $nombre      = isset($row[2]) ? trim($row[2]) : null;
                    
                    // --- CORRECCIÓN PARA DECIMALES ---
                    $montoBruto  = isset($row[3]) ? trim($row[3]) : '0';
                    $montoBruto  = str_replace(',', '.', $montoBruto); // Limpia formato latino
                    $monto       = (float) $montoBruto; // Convierte a flotante conservando decimales
                    // ---------------------------------
                    
                    $oficina     = isset($row[4]) ? trim($row[4]) : null;
                    $distrito    = isset($row[5]) ? trim($row[5]) : null;
                    $descripcion = isset($row[6]) ? trim($row[6]) : '';

                    // 4. Formateo de fecha robusto (Excel a veces manda números seriales)
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

                    // 5. Autogeneramos el Hash
                    $hashCalculado = "{$idCuenta}-{$fechaFormateada}-{$monto}-{$cedula}";

                    // 6. Guardamos en el arreglo temporal
                    $registrosPrevia[] = [
                        'fecha_movimiento'    => $fechaFormateada,
                        'fecha_vista'         => $fechaVista,
                        'descripcion_banco'   => $descripcion,
                        'hash_transaccion'    => $hashCalculado,
                        'valor_ingreso'       => $monto,
                        'referencia_cedula'   => $cedula,
                        'referencia_nombre'   => $nombre,
                        'referencia_oficina'  => $oficina,
                        'referencia_distrito' => $distrito,
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

    /**
     * PASO 2: Guarda definitivamente los datos validados desde la sesión a la BD.
     */
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
                    'descripcion_banco'       => $row['descripcion_banco'],
                    'valor_ingreso'           => $row['valor_ingreso'],
                    'referencia_cedula'       => $row['referencia_cedula'],
                    'referencia_nombre'       => $row['referencia_nombre'],
                    'referencia_oficina'      => $row['referencia_oficina'],
                    'referencia_distrito'     => $row['referencia_distrito'],
                    'estado_conciliacion'     => 'Pendiente',
                ]
            );
            $registrosImportados++;
        }

        session()->forget(['importacion_temporal', 'importacion_cuenta_id']);

        return redirect()->route('contabilidad.extractos.index')
                         ->with('success', "¡Aprobación exitosa! Se procesaron $registrosImportados movimientos bancarios.");
    }

    /**
     * PASO 3: Cruza automáticamente los extractos y la cartera que compartan el mismo Hash.
     */
    public function conciliacionAutomatica()
    {
        // 1. Buscamos solo los movimientos bancarios que estén Pendientes
        $extractosPendientes = ConExtractoTransaccion::where('estado_conciliacion', 'Pendiente')->get();
        $contador = 0;

        foreach ($extractosPendientes as $extracto) {
            
            // 2. Buscamos en Cartera si hay un pago con el mismo Hash exacto y que no esté conciliado
            $comprobante = \App\Models\Cartera\CarComprobantePago::where('hash_transaccion', $extracto->hash_transaccion)
                ->where(function($q) {
                    $q->whereNull('id_transaccion_bancaria')
                      ->orWhere('estado', '!=', 'conciliado');
                })
                ->first();

            // 3. Si lo encuentra, ¡Hacemos el Match!
            if ($comprobante) {
                // Actualizamos Cartera
                $comprobante->id_transaccion_bancaria = $extracto->id_transaccion; // Conecta el banco con la cartera
                $comprobante->estado = 'conciliado';
                $comprobante->save();

                // Actualizamos el Banco
                $extracto->estado_conciliacion = 'Conciliado_Auto';
                $extracto->save();

                $contador++;
            }
        }

        return redirect()->back()->with('success', "¡Proceso completado! Se lograron conciliar $contador registros automáticamente.");
    }

    /**
     * PASO 4: Vincula manualmente un extracto con un comprobante de cartera.
     */
    public function conciliacionManual(Request $request)
    {
        $request->validate([
            'id_transaccion' => 'required|exists:con_extractos_transacciones,id_transaccion',
            'id_comprobante' => 'required|exists:car_comprobantes_pagos,id',
        ]);

        $extracto = ConExtractoTransaccion::findOrFail($request->id_transaccion);
        $comprobante = \App\Models\Cartera\CarComprobantePago::findOrFail($request->id_comprobante);

        // Vinculamos forzadamente
        $comprobante->id_transaccion_bancaria = $extracto->id_transaccion;
        $comprobante->estado = 'conciliado';
        $comprobante->save();

        $extracto->estado_conciliacion = 'Conciliado_Manual';
        $extracto->save();

        return redirect()->back()->with('success', 'Los registros fueron vinculados manualmente con éxito.');
    }
}