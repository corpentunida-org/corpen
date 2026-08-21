<?php

namespace App\Http\Controllers\Certificados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

//AUDITORIA
use App\Models\Certificados\CarSiaOperacionLog;
use App\Models\Certificados\CarSiaOrigenEvento;
use App\Models\Certificados\CarSiaEventoAuditoria;

// =========================================================================
// IMPORTACIÓN DE MODELOS DEL SISTEMA (CORE Y PIVOTES)
// =========================================================================
use App\Models\Certificados\CarSiaApi;
use App\Models\Certificados\CarSiaOperacion;
use App\Models\Certificados\CarSiaEstado;
use App\Models\Certificados\CarSiaEstadoOperacion; // Modelo pivote de estados
use App\Models\Creditos\LineaCredito;

class IngestaController extends Controller
{
    /**
     * =========================================================================
     * 1. LEE LOTES CRUDOS Y APLICA FILTROS (VISTA PRINCIPAL)
     * =========================================================================
     */
    public function index(Request $request)
    {
        try {
            $query = CarSiaApi::query();

            // Filtro de búsqueda por cédula o número de factura
            if ($request->filled('buscar_cedula')) {
                $query->where('tercero', 'LIKE', '%' . trim($request->buscar_cedula) . '%')
                      ->orWhere('id_factura', 'LIKE', '%' . trim($request->buscar_cedula) . '%');
            }

            // Paginación de 50 en 50 para no saturar la vista
            $lotesCrudos = $query->orderBy('fecha_ad', 'desc')->paginate(50);
            $totalPendientes = CarSiaApi::where('estado', '!=', 'PROCESADO')->count();

            // Obtenemos los catálogos para llenar los selects del Modal de Configuración
            $estados = CarSiaEstado::all();
            $tipos = DB::table('car_sia_tipos')->get();

            return view('certificados.ingesta.index', compact('lotesCrudos', 'totalPendientes', 'estados', 'tipos'));

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error al cargar los lotes crudos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al cargar la tabla de staging.');
        }
    }

    /**
     * =========================================================================
     * 2. CARGAR EXCEL (ETL BÁSICO)
     * =========================================================================
     */
    public function cargarExcel(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $datosExcel = Excel::toArray(new \stdClass(), $request->file('archivo_excel'));
            $hoja = $datosExcel[0];

            if (count($hoja) < 2) {
                return redirect()->back()->with('error', 'El archivo está vacío o no tiene registros.');
            }

            // Normalizamos los encabezados pasándolos a minúsculas
            $encabezados = array_map(function($columna) {
                return trim(strtolower($columna));
            }, $hoja[0]);

            array_shift($hoja); // Quitamos la fila de encabezados

            // Mapeo estricto: Lo que lee del Excel vs Lo que guarda en BD
            $mapaColumnas = [
                'id factura'     => 'id_factura',
                'tercero'        => 'tercero',
                'nombre tercero' => 'nombre_tercero',
                'valor'          => 'valor',
                'fecha venc.'    => 'fecha_venci',
                '# documento'    => 'numero_documento',
                'año'            => 'anio',
                'mes'            => 'mes',
                'cuenta'         => 'cuenta',
                'banco'          => 'banco'
            ];

            // Transacción: Si una fila falla, se revierte todo el Excel
            DB::transaction(function () use ($hoja, $encabezados, $mapaColumnas) {
                foreach ($hoja as $fila) {
                    if (empty(array_filter($fila))) {
                        continue;
                    }

                    $datosInsertar = [
                        'estado'   => 'PENDIENTE',
                        'fecha_ad' => now(),
                    ];

                    foreach ($mapaColumnas as $columnaExcel => $campoBD) {
                        $indiceColumna = array_search($columnaExcel, $encabezados);

                        if ($indiceColumna !== false && isset($fila[$indiceColumna])) {
                            $datosInsertar[$campoBD] = $fila[$indiceColumna];
                        }
                    }

                    CarSiaApi::create($datosInsertar);
                }
            });

            return redirect()->back()->with('success', 'El archivo se procesó correctamente. Se omitieron las columnas innecesarias.');

        } catch (\Exception $e) {
            Log::error('CERTIFICADOS Ingesta - Error procesando el Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error leyendo el Excel. Verifique el formato.');
        }
    }

    /**
     * =========================================================================
     * 3. MOTOR DE INYECCIÓN (CREACIÓN DE BLOQUES Y PIVOTES)
     * =========================================================================
     */
    public function inyectarBloques(Request $request)
    {
        $request->validate([
            'id_car_sia_estados' => 'required|integer',
            'id_car_sia_tipos'   => 'required|integer',
        ]);

        try {
            $clientesProcesados = 0;
            $lineasProcesadas = 0;

            DB::transaction(function () use (&$clientesProcesados, &$lineasProcesadas, $request) {

                // 1. Buscamos facturas pendientes
                $cedulasPendientes = CarSiaApi::where('estado', 'PENDIENTE')
                    ->where(function ($query) {
                        $query->whereNull('anular')->orWhere('anular', '!=', 1);
                    })
                    ->distinct()
                    ->pluck('tercero');

                if ($cedulasPendientes->isEmpty()) {
                    return; // Si no hay pendientes, salimos sin hacer nada
                }

                // 2. Preparamos catálogos de Auditoría de forma segura
                $origen = CarSiaOrigenEvento::firstOrCreate(['nombre' => 'Interfaz Web']);
                $evento = CarSiaEventoAuditoria::firstOrCreate(['nombre' => 'Inyección Masiva ERP']);

                $anioActual = date('Y');
                $cantidadActual = CarSiaOperacion::whereYear('created_at', $anioActual)->count();

                $idEstadoReal = $request->id_car_sia_estados;
                $idTipoEvento = $request->id_car_sia_tipos;

                // 3. Iteramos cada cliente para crear su bloque
                foreach ($cedulasPendientes as $cedula) {
                    $lotes = CarSiaApi::where('estado', 'PENDIENTE')
                        ->where(function ($query) {
                            $query->whereNull('anular')->orWhere('anular', '!=', 1);
                        })
                        ->where('tercero', $cedula)
                        ->get();

                    if ($lotes->isEmpty()) continue;

                    // A. CREAR OPERACIÓN (RADICADO)
                    $cantidadActual++;
                    $consecutivo = str_pad($cantidadActual, 4, '0', STR_PAD_LEFT);
                    $numeroBloque = "CER-{$anioActual}-{$consecutivo}";

                    $operacion = CarSiaOperacion::create([
                        'numero_radicado' => $numeroBloque,
                        'numero_bloque'   => $numeroBloque,
                        'id_factura'      => $lotes->first()->id,
                        'id_tercero'      => $cedula,
                    ]);

                    // B. CREAR TABLAS PIVOTE
                    DB::table('car_sia_tipos_evento')->insert([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_tipos'       => $idTipoEvento,
                        'numero_bloque'          => $numeroBloque,
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);

                    CarSiaEstadoOperacion::create([
                        'id_car_sia_operaciones' => null,
                        'id_car_sia_estados'     => $idEstadoReal,
                        'numero_bloque'          => $numeroBloque,
                    ]);

                    $clientesProcesados++;
                    $lineasCliente = 0;

                    // C. CREAR LÍNEAS DE DETALLE (FACTURAS)
                    foreach ($lotes as $lote) {
                        $lineaCredito = LineaCredito::where('cuenta', $lote->cuenta)->first();

                        \App\Models\Certificados\CarSiaOperacionLinea::create([
                            'id_car_sia_operaciones' => $operacion->id,
                            'numero_bloque'          => $numeroBloque,
                            'fecha_venci'            => $lote->fecha_venci,
                            'observacion'            => 'Ingesta masiva ERP. Factura: ' . $lote->id_factura,
                            'id_cre_lineas_creditos' => $lineaCredito ? $lineaCredito->id : 1,
                            'id_car_sia_estados'     => $idEstadoReal,
                        ]);

                        $lote->update(['estado' => 'PROCESADO']);
                        $lineasProcesadas++;
                        $lineasCliente++;
                    }

                    // D. AUDITORÍA BLINDADA
                    // Se usa un Try/Catch interno: Si el log falla, NO tumba la creación de radicados
                    try {
                        CarSiaOperacionLog::create([
                            'numero_bloque'                 => $numeroBloque,
                            'id_car_sia_operaciones_lineas' => null,
                            'id_car_sia_origenes_evento'    => $origen->id,
                            'id_car_sia_eventos_auditoria'  => $evento->id,
                            'id_user'                       => Auth::check() ? Auth::id() : null, // ID de usuario o nulo
                            'ip'                            => $request->ip() ?? '127.0.0.1',
                            'detalles_ejecucion'            => [ // Array directo gracias al 'cast' del modelo
                                'accion'                  => 'Generacion Automatica Masiva',
                                'cliente_tercero'         => $cedula,
                                'cantidad_lineas_creadas' => $lineasCliente,
                                'estado_asignado'         => $idEstadoReal,
                                'tipo_asignado'           => $idTipoEvento
                            ]
                        ]);
                    } catch (\Exception $exLog) {
                        // Registramos el error de auditoría en silencio para el desarrollador, pero el proceso de negocio continúa
                        Log::warning("Fallo log de auditoría para el bloque {$numeroBloque}: " . $exLog->getMessage());
                    }
                }
            }); // FIN DE LA TRANSACCIÓN DB

            // Validamos si realmente se procesó algo
            if ($clientesProcesados === 0) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'No se encontraron lotes válidos pendientes para procesar.'], 400);
                }
                return redirect()->back()->with('error', 'No se encontraron lotes pendientes.');
            }

            // Exito: Guardamos variables para mostrar el modal de resumen verde
            session()->flash('inyeccion_exitosa', true);
            session()->flash('resumen_clientes', $clientesProcesados);
            session()->flash('resumen_lineas', $lineasProcesadas);

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back();

        } catch (\Exception $e) {
            // Error Crítico que sí tumba el sistema
            Log::error('CERTIFICADOS Ingesta - Error crítico en inyección: ' . $e->getMessage());

            if ($request->ajax()) {
                // Devolvemos el mensaje exacto para saber qué falló
                return response()->json(['error' => 'Error SQL: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error SQL: ' . $e->getMessage());
        }
    }
    /**
     * =========================================================================
     * 4. ANULAR LOTE
     * =========================================================================
     */
    public function anularLote(int $id)
    {
        try {
            $lote = CarSiaApi::findOrFail($id);
            $lote->update(['anular' => 1]);
            return redirect()->back()->with('success', 'El lote fue anulado.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo anular el registro.');
        }
    }
}
