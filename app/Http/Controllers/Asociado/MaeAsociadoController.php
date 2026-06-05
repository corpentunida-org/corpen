<?php

namespace App\Http\Controllers\Asociado;

use App\Http\Controllers\Controller;
use App\Models\Asociado\MaeAsociado;
use App\Models\Maestras\MaeTerceros;
use App\Models\Maestras\MaeDistritos;
use App\Models\Maestras\MaeCongregacion;
use App\Models\Demografia\Ciudad;
use App\Http\Requests\StoreMaeAsociadoRequest;
use App\Exports\Asociado\AsociadosExport;
use App\Imports\Asociado\AsociadosImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaeAsociadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Conteo global para las tarjetas
        $totalExpedientes = MaeAsociado::count();
        $expedientesActivos = MaeAsociado::where('estado', 'Activo')->count();
        $expedientesInactivos = MaeAsociado::where('estado', 'Inactivo')->count();
        $digitalizadosEcm = MaeAsociado::where('cargado_ecm', true)->count();
        
        $pendientesArchivo = MaeAsociado::where(function($q) {
            $q->whereNull('radicado')->orWhere('radicado', '');
        })->count();

        // 2. Consulta base para la tabla
        $query = MaeAsociado::with(['ciudad', 'distrito']);

        // 3. Filtro por Buscador Global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cedula', 'LIKE', "%{$search}%")
                  ->orWhere('nombre1', 'LIKE', "%{$search}%")
                  ->orWhere('apellido1', 'LIKE', "%{$search}%")
                  ->orWhere('distrito_actual', 'LIKE', "%{$search}%")
                  ->orWhereHas('ciudad', function($qCiudad) use ($search) {
                      $qCiudad->where('nombre', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('distrito', function($qDistrito) use ($search) {
                      $qDistrito->where('NOM_DIST', 'LIKE', "%{$search}%");
                  });
            });
        }

        // 4. Filtro por Clic en Tarjetas
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'activos':
                    $query->where('estado', 'Activo');
                    break;
                case 'inactivos':
                    $query->where('estado', 'Inactivo');
                    break;
                case 'ecm':
                    $query->where('cargado_ecm', true);
                    break;
                case 'pendientes':
                    $query->where(function($q) {
                        $q->whereNull('radicado')->orWhere('radicado', '');
                    });
                    break;
            }
        }

        // 5. Paginación
        $asociados = $query->latest()->paginate(15)->withQueryString();
        
        return view('asociados.index', compact(
            'asociados', 
            'totalExpedientes', 
            'expedientesActivos', 
            'expedientesInactivos', 
            'digitalizadosEcm', 
            'pendientesArchivo'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ciudades = Ciudad::with('subregion:id_subregion,nombre')->get();
        $distritos = MaeDistritos::orderBy('NOM_DIST', 'asc')->get();
        $congregaciones = MaeCongregacion::orderBy('nombre', 'asc')->get();
        
        return view('asociados.create', compact('ciudades', 'distritos', 'congregaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMaeAsociadoRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['escaneado']        = $request->boolean('escaneado');
            $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
            $validated['validado_archivo'] = $request->boolean('validado_archivo');
            $validated['estado'] = $request->input('estado', 'Activo');

            $asociado = new MaeAsociado($validated);

            if (!$request->boolean('sincronizar_tercero')) {
                $asociado->skipTerceroSync = true;
            }

            $asociado->save();

            return redirect()->route('asociados.maestro.index')
                ->with('success', 'Expediente del asociado creado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error creando asociado: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Ocurrió un error interno al intentar guardar el expediente. Contacte a soporte si el problema persiste.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaeAsociado $maeAsociado)
    {
        $maeAsociado->load(['ciudad', 'distrito']);
        return view('asociados.show', compact('maeAsociado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaeAsociado $maeAsociado)
    {
        $ciudades = Cache::remember('ciudades_list_cache', now()->addDays(7), function () {
            return Ciudad::with('subregion')->orderBy('nombre', 'asc')->get();
        });

        $distritos = Cache::remember('distritos_list_cache', now()->addDays(7), function () {
            return MaeDistritos::select('COD_DIST', 'NOM_DIST')
                ->orderBy('NOM_DIST', 'asc')
                ->get();
        });

        $congregaciones = Cache::remember('congregaciones_list_cache', now()->addDays(7), function () {
            return MaeCongregacion::select('codigo', 'nombre')
                ->orderBy('nombre', 'asc')
                ->get();
        });

        return view('asociados.edit', compact('maeAsociado', 'ciudades', 'distritos', 'congregaciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaeAsociado $maeAsociado)
    {
        $validated = $request->validate($this->getValidationRules($maeAsociado->id));

        $validated['escaneado']        = $request->boolean('escaneado');
        $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
        $validated['validado_archivo'] = $request->boolean('validado_archivo');

        $maeAsociado->fill($validated);

        if (!$request->boolean('sincronizar_tercero')) {
            $maeAsociado->skipTerceroSync = true;
        }

        $maeAsociado->save();

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Expediente del asociado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaeAsociado $maeAsociado)
    {
        $maeAsociado->delete();

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Asociado eliminado del sistema correctamente.');
    }

    public function buscarCedula($cedula)
    {
        $asociado = MaeAsociado::with(['ciudad', 'distrito'])->where('cedula', $cedula)->first();

        if (!$asociado) {
            return response()->json(['status' => 'error', 'message' => 'No se encontró el asociado en el directorio.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'              => $asociado->id,
                'nombre_completo' => $asociado->nombre_completo,
                'celular'         => $asociado->celular_pastor,
                'distrito_cod'    => $asociado->distrito_actual,
                'distrito_nombre' => $asociado->distrito ? $asociado->distrito->NOM_DIST : null,
                'ciudad'          => $asociado->ciudad ? $asociado->ciudad->nombre : null,
                'estado'          => $asociado->estado
            ]
        ]);
    }

    public function ecmIndex(Request $request)
    {
        $digitalizadosEcm = MaeAsociado::where('cargado_ecm', true)->count();
        $pendientesArchivo = MaeAsociado::whereNull('radicado')->orWhere('radicado', '')->count();
        $validados = MaeAsociado::where('validado_archivo', true)->count();

        $query = MaeAsociado::with(['ciudad', 'distrito']);

        // 1. Búsqueda individual y exacta por Radicado
        if ($request->filled('radicado')) {
            $query->where('radicado', 'LIKE', '%' . trim($request->radicado) . '%');
        }

        // 2. Búsqueda general por Nombre o Cédula
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('cedula', 'LIKE', "%{$search}%")
                  ->orWhere('nombre1', 'LIKE', "%{$search}%")
                  ->orWhere('apellido1', 'LIKE', "%{$search}%");
            });
        }

        // 3. Filtro por Estado de Digitalización
        if ($request->filled('estado_digitalizacion')) {
            $query->where('cargado_ecm', $request->estado_digitalizacion == '1');
        }

        // Retenemos los parámetros en la URL con withQueryString() para que no se pierdan al cambiar de página
        $asociados = $query->latest()->paginate(15)->withQueryString();
        
        return view('asociados.ecm', compact('asociados', 'digitalizadosEcm', 'pendientesArchivo', 'validados'));
    }
    
    public function dashboard()
    {
        $total = MaeAsociado::count();
        
        $estadosActivos = ['Activo', 'Vigente']; 
        $estadosInactivos = ['Inactivo', 'Retirado', 'Suspendido'];

        $activosCount = MaeAsociado::whereIn('estado', $estadosActivos)->count();
        $inactivosCount = MaeAsociado::whereIn('estado', $estadosInactivos)->count();

        $estadosPastor = MaeAsociado::selectRaw('COALESCE(estado_pastor, "Sin Definir") as estado_display, count(*) as cantidad')
            ->groupBy('estado_pastor')
            ->pluck('cantidad', 'estado_display')
            ->toArray();

        $estadosCiviles = MaeAsociado::selectRaw('estado_civil, count(*) as cantidad')
            ->whereNotNull('estado_civil')
            ->where('estado_civil', '!=', '')
            ->groupBy('estado_civil')
            ->pluck('cantidad', 'estado_civil')
            ->toArray();

        $ecmPipeline = [
            'total'      => $total,
            'escaneados' => MaeAsociado::where('escaneado', true)->count(),
            'cargados'   => MaeAsociado::where('cargado_ecm', true)->count(),
            'validados'  => MaeAsociado::where('validado_archivo', true)->count(),
        ];

        $pendientesRadicado = MaeAsociado::whereNull('radicado')->orWhere('radicado', '')->count();
        $ultimosIngresos = MaeAsociado::with(['ciudad', 'distrito'])->latest()->take(5)->get();

        return view('asociados.dashboard', compact(
            'total',
            'activosCount',
            'inactivosCount',
            'estadosPastor',
            'estadosCiviles',
            'ecmPipeline',
            'pendientesRadicado',
            'ultimosIngresos'
        ));
    }

    public function buscarTerceroMaestro($cedula)
    {
        $tercero = MaeTerceros::where('cod_ter', $cedula)->first();

        if ($tercero) {
            return response()->json([
                'status' => 'success',
                'data'   => $tercero
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Tercero no encontrado.'], 404);
    }

    // =========================================================================
    //   NUEVAS INTERFACES DE EXCEL (PROCESAMIENTO MASIVO E INTEGRACIÓN ECM)
    // =========================================================================

    public function excelIndex()
    {
        return view('asociados.excel-sync');
    }

    public function descargarExcel()
    {
        return Excel::download(new AsociadosExport, 'maestro_asociados_' . now()->format('Y_m_d_His') . '.xlsx');
    }

    /**
     * Acción (Paso 1): Sube el archivo Excel, valida las columnas y lo guarda en Sesión.
     * Genera la vista de validación con contadores antes de impactar la BD.
     */
/**
     * Acción (Paso 1): Sube el archivo Excel, valida optimizado y guarda en Caché.
     */
    public function subirExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:51200',
        ]);

        try {
            $import = new AsociadosImport();
            Excel::import($import, $request->file('excel_file'));
            $filasRaw = $import->getRows();

            if (empty($filasRaw)) {
                return back()->with('error', 'El archivo Excel seleccionado se encuentra vacío.');
            }

            // OPTIMIZACIÓN 1: Extraer todas las cédulas y buscar cuáles existen en UNA SOLA consulta
            $cedulasEnExcel = array_column($filasRaw, 'cedula');
            $cedulasExistentes = MaeAsociado::whereIn('cedula', $cedulasEnExcel)->pluck('cedula')->toArray();
            $cedulasMap = array_flip($cedulasExistentes); // Convierte a mapa para búsqueda ultra rápida

            $filasProcesadas = [];
            $errores = [];
            $contadorNuevos = 0;
            $contadorActualizaciones = 0;
            
            // NUEVO: Array para rastrear si hay cédulas repetidas dentro de tu mismo Excel
            $cedulasVistas = []; 

            foreach ($filasRaw as $index => $fila) {
                $numeroLinea = $index + 2; // Línea real en el archivo Excel

                // 1. Limpieza extrema de la Cédula Principal (Extrae ÚNICAMENTE números)
                $cedulaOriginal = $fila['cedula'] ?? '';
                $cedulaLimpia = preg_replace('/[^0-9]/', '', (string)$cedulaOriginal);

                // MODIFICACIÓN: En vez de saltar en silencio, generamos un error visible para avisarte
                if (empty($cedulaLimpia)) {
                    $errores[] = "Línea {$numeroLinea}: La cédula está vacía o tiene texto inválido ('{$cedulaOriginal}').";
                    continue;
                }

                // NUEVO: Detector de duplicados en el Excel
                if (in_array($cedulaLimpia, $cedulasVistas)) {
                    $errores[] = "Línea {$numeroLinea}: La cédula {$cedulaLimpia} está DUPLICADA en otra fila de tu archivo.";
                    continue;
                }
                $cedulasVistas[] = $cedulaLimpia; // Guardamos la cédula para compararla con las siguientes

                // 2. Limpieza de errores de Excel (#VALUE!, #N/A, #REF!)
                foreach ($fila as $key => $value) {
                    if (is_string($value) && str_starts_with(trim($value), '#')) {
                        $fila[$key] = null; 
                    }
                }

                // 3. Limpieza extrema de la Cédula de la Esposa
                // Si viene con guiones o letras (ej. "172380946-1"), las elimina y deja solo números.
                if (!empty($fila['cedula_esposa'])) {
                    $esposaLimpia = preg_replace('/[^0-9]/', '', (string)$fila['cedula_esposa']);
                    $fila['cedula_esposa'] = empty($esposaLimpia) ? null : $esposaLimpia;
                }

                // Asignamos la cédula ya limpia al array
                $fila['cedula'] = $cedulaLimpia;

                // 4. Validación manual ligera
                if (empty($fila['nombre1']) || empty($fila['apellido1'])) {
                    $errores[] = "Línea {$numeroLinea}: Faltan campos obligatorios (nombre1 o apellido1).";
                    continue;
                }

                // 5. Determinar el tipo de operación
                $existe = isset($cedulasMap[$fila['cedula']]);
                $fila['accion'] = $existe ? 'UPDATE' : 'CREATE';
                $fila['linea']  = $numeroLinea;

                if ($existe) {
                    $contadorActualizaciones++;
                } else {
                    $contadorNuevos++;
                }

                $filasProcesadas[] = $fila;
            }

            // Si hay errores (como las filas perdidas), se los mostramos en pantalla y DETENEMOS el proceso
            /* if (count($errores) > 0) {
                return back()->withErrors(array_slice($errores, 0, 30))->with('error', 'Se encontraron problemas en algunas filas del Excel. Revísalas:');
            } */

            // OPTIMIZACIÓN 3: Usar Caché en lugar de Sesión para datos masivos
            $cacheKey = 'import_asociados_' . auth()->id();
            Cache::put($cacheKey, $filasProcesadas, now()->addHours(2));
            session(['import_asociados_key' => $cacheKey]);

            return view('asociados.excel-preview', compact('filasProcesadas', 'contadorNuevos', 'contadorActualizaciones', 'errores'));

        } catch (\Exception $e) {
            Log::error('Error procesando importación masiva: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un problema crítico leyendo el archivo. Verifica el formato. Detalle: ' . $e->getMessage());
        }
    }

    /**
     * Acción (Paso 2): Confirma, realiza transacciones por bloques (chunks).
     */
    public function confirmarSincronizacion(Request $request)
    {
        $cacheKey = session('import_asociados_key');
        $datos = Cache::get($cacheKey);
        // APLICAR CORRECCIONES MANUALES DE LA VISTA
        $correcciones = $request->input('correcciones', []);
        
        if (!empty($correcciones)) {
            foreach ($datos as $key => $fila) {
                // Si esta fila tiene una corrección entrante desde el formulario
                if (isset($correcciones[$key])) {
                    $datos[$key]['cedula']    = preg_replace('/[^0-9]/', '', $correcciones[$key]['cedula'] ?? $fila['cedula']);
                    $datos[$key]['nombre1']   = $correcciones[$key]['nombre1'] ?? $fila['nombre1'];
                    $datos[$key]['apellido1'] = $correcciones[$key]['apellido1'] ?? $fila['apellido1'];
                }
            }
            // Retiramos las filas que aún después de corregir (o si las dejaron vacías) sigan sin cédula
            $datos = array_filter($datos, fn($d) => !empty($d['cedula']));
        }
        if (!$datos || count($datos) === 0) {
            return redirect()->route('asociados.sincronizar.index')
                ->with('error', 'La sesión ha expirado o no se encontraron registros listos para confirmación.');
        }

        $sincronizarTerceros = $request->boolean('sincronizar_terceros_global', true);

        try {
            // Bajamos el tamaño del bloque a 250 para evitar que MySQL agote su memoria (Error 2006)
            $chunks = array_chunk($datos, 250);

            foreach ($chunks as $chunk) {
                
                // MÁS IMPORTANTE: La transacción ahora se abre y se cierra ADENTRO del bloque
                DB::beginTransaction();

                foreach ($chunk as $item) {
                    $asociado = MaeAsociado::firstOrNew(['cedula' => $item['cedula']]);

                    if (!$sincronizarTerceros) {
                        $asociado->skipTerceroSync = true;
                    }

                    $asociado->fill([
                        'cedula'                    => $item['cedula'],
                        'nombre1'                   => $item['nombre1'],
                        'nombre2'                   => $item['nombre2'] ?? null,
                        'apellido1'                 => $item['apellido1'],
                        'apellido2'                 => $item['apellido2'] ?? null,
                        'fecha_nacimiento'          => $this->transformDate($item['fecha_nacimiento'] ?? null),
                        'lugar_expedicion_cedula'   => $item['lugar_expedicion_cedula'] ?? null,
                        'fecha_expedicion'          => $this->transformDate($item['fecha_expedicion'] ?? null),
                        'estado_civil'              => $item['estado_civil'] ?? null,
                        'correo_pastor'             => $item['correo_pastor'] ?? null,
                        
                        // BLINDAJE CONTRA TEXTOS LARGOS EN TELÉFONOS (Máximo 15 caracteres)
                        'celular_pastor'            => substr($item['celular_pastor'] ?? '', 0, 15),
                        'whatsapp'                  => substr($item['whatsapp'] ?? '', 0, 15),
                        'celular_esposa'            => substr($item['celular_esposa'] ?? '', 0, 15),
                        
                        'fecha_afiliacion'          => $this->transformDate($item['fecha_afiliacion'] ?? null),
                        'distrito_actual'           => $item['distrito_actual'] ?? null,
                        'ciudad_distrito'           => $item['ciudad_distrito'] ?? null,
                        'direccion_distrito'        => $item['direccion_distrito'] ?? null,
                        'estado_pastor'             => $item['estado_pastor'] ?? null,
                        'especificacion'            => $item['especificacion'] ?? null,
                        'licencia'                  => $item['licencia'] ?? null,
                        'pais'                      => $item['pais'] ?? 'Colombia',
                        'iglesia_actual'            => $item['iglesia_actual'] ?? null,
                        'cedula_esposa'             => $item['cedula_esposa'] ?? null,
                        'nombre_esposa'             => $item['nombre_esposa'] ?? null,
                        'correo_esposa'             => $item['correo_esposa'] ?? null,
                        'doc_formulario_afiliacion' => $item['doc_formulario_afiliacion'] ?? null,
                        'doc_autorizacion_datos'    => $item['doc_autorizacion_datos'] ?? null,
                        'doc_cedula_pastor'         => $item['doc_cedula_pastor'] ?? null,
                        'doc_cedula_esposa'         => $item['doc_cedula_esposa'] ?? null,
                        'doc_licencia_pastoral'     => $item['doc_licencia_pastoral'] ?? null,
                        'doc_registro_matrimonio'   => $item['doc_registro_matrimonio'] ?? null,
                        'doc_id_hijos'              => $item['doc_id_hijos'] ?? null,
                        'radicado'                  => $item['radicado'] ?? null,
                        'ubicacion_carpeta'         => $item['ubicacion_carpeta'] ?? null,
                        'numero_caja'               => $item['numero_caja'] ?? null,
                        'cantidad_folios'           => $item['cantidad_folios'] ?? 0,
                        'fecha_ingreso_archivo'     => $this->transformDate($item['fecha_ingreso_archivo'] ?? null),
                        'estado_conservacion'       => $item['estado_conservacion'] ?? null,
                        'custodia_actual'           => $item['custodia_actual'] ?? null,
                        'observaciones_archivo'     => $item['observaciones_archivo'] ?? null,
                        'observaciones_generales'   => $item['observaciones_generales'] ?? null,
                        'estado'                    => $item['estado'] ?? 'Activo',
                        'ubicacion_ecm_link'        => $item['ubicacion_ecm_link'] ?? null,
                        'escaneado'                 => $this->parseBoolean($item['escaneado'] ?? (!empty($item['radicado']))),
                        'cargado_ecm'               => $this->parseBoolean($item['cargado_ecm'] ?? (!empty($item['ubicacion_ecm_link']))),
                        'validado_archivo'          => $this->parseBoolean($item['validado_archivo'] ?? false),
                    ]);

                    $asociado->save();
                }

                // Confirmamos a la base de datos SOLO los 250 registros de esta vuelta
                DB::commit();
            }

            // Limpieza de recursos cuando terminan todos los bloques
            Cache::forget($cacheKey);
            session()->forget('import_asociados_key'); 

            return redirect()->route('asociados.maestro.index')
                ->with('success', 'Sincronización masiva finalizada. Se procesaron con éxito los expedientes.');

        } catch (\Exception $e) {
            // Si hay un error, revertimos el bloque actual (MySQL recupera la conexión)
            DB::rollBack();
            Log::critical('Error en confirmación por lotes de Asociados: ' . $e->getMessage());
            return redirect()->route('asociados.sincronizar.index')
                ->with('error', 'Fallo SQL Exacto: ' . $e->getMessage());
        }
    }

    /**
     * Helpers internos para formateo de fechas y booleanos desde arrays en memoria
     */
    private function transformDate($value, $format = 'Y-m-d')
    {
        if (!$value) return null;
        try {
            if (is_numeric($value)) {
                return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value))->format($format);
            }
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Exception $e) {
            return null; 
        }
    }

    private function parseBoolean($value)
    {
        if (is_bool($value)) return $value;
        if (is_string($value)) {
            $value = strtoupper(trim($value));
            if (in_array($value, ['1', 'SI', 'SÍ', 'TRUE', 'VERDADERO', 'V'])) return true;
        }
        if ($value == 1) return true;
        
        return false;
    }

    /**
     * Método privado para centralizar las reglas de validación de los 48 campos.
     */
    private function getValidationRules($id = null)
    {
        return [
            'cedula'                    => 'required|string|max:15|unique:mae_asociados,cedula,' . $id,
            'nombre1'                   => 'required|string|max:100',
            'nombre2'                   => 'nullable|string|max:100',
            'apellido1'                 => 'required|string|max:100',
            'apellido2'                 => 'nullable|string|max:100',
            'fecha_nacimiento'          => 'nullable|date',
            'lugar_expedicion_cedula'   => 'nullable|string|max:50',
            'fecha_expedicion'          => 'nullable|date',
            'estado_civil'              => 'nullable|string|max:20',
            'correo_pastor'             => 'nullable|email|max:100',
            'celular_pastor'            => 'nullable|string|max:15',
            'whatsapp'                  => 'nullable|string|max:15',
            'fecha_afiliacion'          => 'nullable|date',
            'distrito_actual'           => 'nullable|string|max:50|exists:MaeDistritos,COD_DIST',
            'ciudad_distrito'           => 'nullable|string|max:100|exists:geo_ciudades,id_ciudad',
            'direccion_distrito'        => 'nullable|string|max:100',
            'estado_pastor'             => 'nullable|string|max:20',
            'especificacion'            => 'nullable|string|max:50',
            'licencia'                  => 'nullable|string|max:30',
            'pais'                      => 'nullable|string|max:30',
            'iglesia_actual'            => 'nullable|string|max:100',
            'cedula_esposa'             => 'nullable|string|max:15',
            'nombre_esposa'             => 'nullable|string|max:100',
            'correo_esposa'             => 'nullable|email|max:100',
            'celular_esposa'            => 'nullable|string|max:15',
            'doc_formulario_afiliacion' => 'nullable|string|max:20',
            'doc_autorizacion_datos'    => 'nullable|string|max:20',
            'doc_cedula_pastor'         => 'nullable|string|max:20',
            'doc_cedula_esposa'         => 'nullable|string|max:20',
            'doc_licencia_pastoral'     => 'nullable|string|max:20',
            'doc_registro_matrimonio'   => 'nullable|string|max:20',
            'doc_id_hijos'              => 'nullable|string|max:20',
            'ubicacion_ecm_link'        => 'nullable|url|max:255',
            'radicado'                  => 'nullable|string|max:20',
            'ubicacion_carpeta'         => 'nullable|string|max:30',
            'numero_caja'               => 'nullable|string|max:20',
            'cantidad_folios'           => 'nullable|integer|min:0',
            'fecha_ingreso_archivo'     => 'nullable|date',
            'estado_conservacion'       => 'nullable|string|max:30',
            'custodia_actual'           => 'nullable|string|max:50',
            'observaciones_archivo'     => 'nullable|string',
            'observaciones_generales'   => 'nullable|string',
            'estado'                    => 'nullable|string|max:20',
        ];
    }
}