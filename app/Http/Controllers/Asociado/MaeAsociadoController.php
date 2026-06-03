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
        // 1. Conteo global para las tarjetas (siempre muestran el total general, sin importar el filtro)
        $totalExpedientes = MaeAsociado::count();
        $expedientesActivos = MaeAsociado::where('estado', 'Activo')->count();
        $expedientesInactivos = MaeAsociado::where('estado', 'Inactivo')->count();
        $digitalizadosEcm = MaeAsociado::where('cargado_ecm', true)->count();
        
        // Mejor forma de contar nulos o vacíos
        $pendientesArchivo = MaeAsociado::where(function($q) {
            $q->whereNull('radicado')->orWhere('radicado', '');
        })->count();

        // 2. Consulta base para la tabla (Eager Loading optimizado para las relaciones de ciudad y distrito)
        $query = MaeAsociado::with(['ciudad', 'distrito']);

        // 3. Filtro por Buscador Global (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cedula', 'LIKE', "%{$search}%")
                  ->orWhere('nombre1', 'LIKE', "%{$search}%")
                  ->orWhere('apellido1', 'LIKE', "%{$search}%")
                  ->orWhere('distrito_actual', 'LIKE', "%{$search}%")
                  // Búsqueda en la relación de ciudad por el nombre
                  ->orWhereHas('ciudad', function($qCiudad) use ($search) {
                      $qCiudad->where('nombre', 'LIKE', "%{$search}%");
                  })
                  // Búsqueda en la relación de distrito por el nombre oficial
                  ->orWhereHas('distrito', function($qDistrito) use ($search) {
                      $qDistrito->where('NOM_DIST', 'LIKE', "%{$search}%");
                  });
            });
        }

        // 4. Filtro por Clic en Tarjetas (Filter)
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
                // 'total' muestra todos por defecto, no requiere condicional
            }
        }

        // 5. Paginación (withQueryString preserva los filtros al cambiar de página)
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
        // Traer ciudades y distritos organizados para los selects de la vista
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
            // Los datos ya vienen validados
            $validated = $request->validated();

            // Manejo nativo y seguro de campos booleanos en Laravel
            $validated['escaneado']        = $request->boolean('escaneado');
            $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
            $validated['validado_archivo'] = $request->boolean('validado_archivo');

            // Asignación de estado por defecto si no viene en el request
            $validated['estado'] = $request->input('estado', 'Activo');

            // Instanciamos el modelo en lugar de usar create() para poder inyectar propiedades
            $asociado = new MaeAsociado($validated);

            // VERIFICACIÓN DEL MODAL / FORMULARIO:
            // Si el frontend envía 'sincronizar_tercero' como falso o no lo envía, omitimos sincronización
            if (!$request->boolean('sincronizar_tercero')) {
                $asociado->skipTerceroSync = true;
            }

            $asociado->save();

            return redirect()->route('asociados.maestro.index')
                ->with('success', 'Expediente del asociado creado correctamente.');

        } catch (\Exception $e) {
            // Registro del error para debugging sin mostrárselo al usuario
            Log::error('Error creando asociado: ' . $e->getMessage());

            // Redirección con los datos ingresados para que el usuario no pierda su trabajo
            return back()->withInput()
                ->with('error', 'Ocurrió un error interno al intentar guardar el expediente. Contacte a soporte si el problema persiste.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaeAsociado $maeAsociado)
    {
        // Cargar relaciones para mostrar los nombres de la ciudad y el distrito en la vista
        $maeAsociado->load(['ciudad', 'distrito']);
        return view('asociados.show', compact('maeAsociado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaeAsociado $maeAsociado)
    {
        // Traemos las ciudades con su subregión y las guardamos en caché por 7 días
        $ciudades = Cache::remember('ciudades_list_cache', now()->addDays(7), function () {
            return Ciudad::with('subregion')->orderBy('nombre', 'asc')->get();
        });

        // Distritos optimizados
        $distritos = Cache::remember('distritos_list_cache', now()->addDays(7), function () {
            return MaeDistritos::select('COD_DIST', 'NOM_DIST')
                ->orderBy('NOM_DIST', 'asc')
                ->get();
        });

        // Congregaciones optimizadas
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
        // Validación centralizada ignorando el ID actual para la cédula
        $validated = $request->validate($this->getValidationRules($maeAsociado->id));

        // Manejo nativo y seguro de campos booleanos
        $validated['escaneado']        = $request->boolean('escaneado');
        $validated['cargado_ecm']      = $request->boolean('cargado_ecm');
        $validated['validado_archivo'] = $request->boolean('validado_archivo');

        // Llenamos el modelo con los nuevos datos validados
        $maeAsociado->fill($validated);

        // VERIFICACIÓN DEL MODAL:
        // Si el frontend envía 'sincronizar_tercero' como falso, omitimos sincronización
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
        // En sistemas ECM de alto nivel, se recomienda SoftDeletes. 
        // Si usas eliminación física, se mantiene el delete().
        $maeAsociado->delete();

        return redirect()->route('asociados.maestro.index')
            ->with('success', 'Asociado eliminado del sistema correctamente.');
    }

    /**
     * Buscar un asociado por cédula (Ejemplo para peticiones AJAX/API)
     */
    public function buscarCedula($cedula)
    {
        // Cargamos las relaciones para tener la ciudad y el distrito disponibles en el JSON
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

    /**
     * Display a listing of the resource specifically for ECM and Physical Archive.
     */
    public function ecmIndex()
    {
        $digitalizadosEcm = MaeAsociado::where('cargado_ecm', true)->count();
        $pendientesArchivo = MaeAsociado::whereNull('radicado')->orWhere('radicado', '')->count();
        $validados = MaeAsociado::where('validado_archivo', true)->count();

        // Se listan los asociados ordenados por los más recientes, cargando ciudad y distrito
        $asociados = MaeAsociado::with(['ciudad', 'distrito'])->latest()->paginate(15);
        
        return view('asociados.ecm', compact('asociados', 'digitalizadosEcm', 'pendientesArchivo', 'validados'));
    }
    
    /**
     * Display the analytics dashboard.
     */
    public function dashboard()
    {
        // 1. Población General
        $total = MaeAsociado::count();
        
        // 2. Definimos qué estados son ACTIVOS y cuáles son INACTIVOS
        // Ajusta estos arrays si en tu BD los estados se llaman diferente (ej: 'Vigente', 'Fallecido')
        $estadosActivos = ['Activo', 'Vigente']; 
        $estadosInactivos = ['Inactivo', 'Retirado', 'Suspendido'];

        // 3. Calculamos los totales usando whereIn
        $activosCount = MaeAsociado::whereIn('estado', $estadosActivos)->count();
        $inactivosCount = MaeAsociado::whereIn('estado', $estadosInactivos)->count();

        // 4. Estado Ministerial (Para el gráfico de dona)
        $estadosPastor = MaeAsociado::selectRaw('COALESCE(estado_pastor, "Sin Definir") as estado_display, count(*) as cantidad')
            ->groupBy('estado_pastor')
            ->pluck('cantidad', 'estado_display')
            ->toArray();

        // 5. Demografía
        $estadosCiviles = MaeAsociado::selectRaw('estado_civil, count(*) as cantidad')
            ->whereNotNull('estado_civil')
            ->where('estado_civil', '!=', '')
            ->groupBy('estado_civil')
            ->pluck('cantidad', 'estado_civil')
            ->toArray();

        // 6. Embudo ECM
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
            'activosCount',     // Pasamos este nuevo valor
            'inactivosCount',   // Pasamos este nuevo valor
            'estadosPastor',
            'estadosCiviles',
            'ecmPipeline',
            'pendientesRadicado',
            'ultimosIngresos'
        ));
    }

    /**
     * Busca en la tabla global MaeTerceros para el autollenado del formulario
     */
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

    /**
     * Panel de Control Principal de Sincronización Excel.
     */
    public function excelIndex()
    {
        return view('asociados.excel-sync');
    }

    /**
     * Acción: Descarga la base completa estructurada actual a un archivo .xlsx
     */
    public function descargarExcel()
    {
        return Excel::download(new AsociadosExport, 'maestro_asociados_' . now()->format('Y_m_d_His') . '.xlsx');
    }

    /**
     * Acción (Paso 1): Sube el archivo Excel, valida las columnas y lo guarda en Sesión.
     * Genera la vista de validación con contadores antes de impactar la BD.
     */
    public function subirExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new AsociadosImport();
            Excel::import($import, $request->file('excel_file'));
            $filasRaw = $import->getRows();

            if (empty($filasRaw)) {
                return back()->with('error', 'El archivo Excel seleccionado se encuentra vacío.');
            }

            $filasProcesadas = [];
            $errores = [];
            $contadorNuevos = 0;
            $contadorActualizaciones = 0;

            foreach ($filasRaw as $index => $fila) {
                // Limpiar espacios y omitir si la columna clave (cédula) viene en blanco
                if (!isset($fila['cedula']) || empty(trim($fila['cedula']))) {
                    continue;
                }

                $numeroLinea = $index + 2; // +2 considerando el encabezado en el Excel

                // Validación interna por fila en memoria
                $validator = Validator::make($fila, [
                    'cedula'             => 'required|max:15',
                    'nombre1'            => 'required|string|max:100',
                    'apellido1'          => 'required|string|max:100',
                    'correo_pastor'      => 'nullable|email',
                    'cantidad_folios'    => 'nullable|integer',
                    'ubicacion_ecm_link' => 'nullable|url'
                ]);

                if ($validator->fails()) {
                    $errores[] = "Línea {$numeroLinea}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Determinar el tipo de operación dinámicamente mediante Upsert lógico
                $existe = MaeAsociado::where('cedula', $fila['cedula'])->exists();
                $fila['accion'] = $existe ? 'UPDATE' : 'CREATE';
                $fila['linea']  = $numeroLinea;

                if ($existe) {
                    $contadorActualizaciones++;
                } else {
                    $contadorNuevos++;
                }

                $filasProcesadas[] = $fila;
            }

            // Si hay fallas estructurales o correos mal escritos, se retorna sin guardar nada
            if (count($errores) > 0) {
                return back()->withErrors($errores)->with('error', 'El archivo contiene errores de validación estructural en filas.');
            }

            // Persistencia temporal en Sesión segura
            session(['import_asociados_datos' => $filasProcesadas]);

            return view('asociados.excel-preview', compact('filasProcesadas', 'contadorNuevos', 'contadorActualizaciones'));

        } catch (\Exception $e) {
            Log::error('Error procesando importación masiva de asociados: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un problema crítico leyendo el archivo. Verifique que los encabezados correspondan al formato.');
        }
    }

    /**
     * Acción (Paso 2): Confirma, realiza transacciones atómicas y ejecuta el volcado masivo.
     */
    public function confirmarSincronizacion(Request $request)
    {
        $datos = session('import_asociados_datos');

        if (!$datos || count($datos) === 0) {
            return redirect()->route('asociados.sincronizar.index')
                ->with('error', 'La sesión ha expirado o no se encontraron registros listos para confirmación.');
        }

        // Permite decidir desde el frontend si ejecutar o suspender el observer de MaeTerceros por lote
        $sincronizarTerceros = $request->boolean('sincronizar_terceros_global', true);

        DB::beginTransaction();
        try {
            foreach ($datos as $item) {
                $asociado = MaeAsociado::where('cedula', $item['cedula'])->first() ?? new MaeAsociado();

                if (!$sincronizarTerceros) {
                    $asociado->skipTerceroSync = true;
                }

                // Mapeo masivo inyectando valores por defecto sanitizados
                $asociado->fill([
                    'cedula'                    => $item['cedula'],
                    'nombre1'                   => $item['nombre1'],
                    'nombre2'                   => $item['nombre2'] ?? null,
                    'apellido1'                 => $item['apellido1'],
                    'apellido2'                 => $item['apellido2'] ?? null,
                    'fecha_nacimiento'          => $item['fecha_nacimiento'] ?? null,
                    'lugar_expedicion_cedula'   => $item['lugar_expedicion_cedula'] ?? null,
                    'fecha_expedicion'          => $item['fecha_expedicion'] ?? null,
                    'estado_civil'              => $item['estado_civil'] ?? null,
                    'correo_pastor'             => $item['correo_pastor'] ?? null,
                    'celular_pastor'            => $item['celular_pastor'] ?? null,
                    'whatsapp'                  => $item['whatsapp'] ?? null,
                    'fecha_afiliacion'          => $item['fecha_afiliacion'] ?? null,
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
                    'celular_esposa'            => $item['celular_esposa'] ?? null,
                    'radicado'                  => $item['radicado'] ?? null,
                    'ubicacion_carpeta'         => $item['ubicacion_carpeta'] ?? null,
                    'numero_caja'               => $item['numero_caja'] ?? null,
                    'cantidad_folios'           => $item['cantidad_folios'] ?? 0,
                    'fecha_ingreso_archivo'     => $item['fecha_ingreso_archivo'] ?? null,
                    'estado_conservacion'       => $item['estado_conservacion'] ?? null,
                    'custodia_actual'           => $item['custodia_actual'] ?? null,
                    'observaciones_archivo'     => $item['observaciones_archivo'] ?? null,
                    'observaciones_generales'   => $item['observaciones_generales'] ?? null,
                    'estado'                    => $item['estado'] ?? 'Activo',
                    
                    // Columnas de Gestión Documental Inteligente (ECM)
                    'ubicacion_ecm_link'        => $item['ubicacion_ecm_link'] ?? null,
                    'escaneado'                 => !empty($item['radicado']),
                    'cargado_ecm'               => !empty($item['ubicacion_ecm_link']),
                    'validado_archivo'          => false,
                ]);

                $asociado->save();
            }

            DB::commit();
            session()->forget('import_asociados_datos'); // Limpieza de búfer

            return redirect()->route('asociados.maestro.index')
                ->with('success', 'Sincronización masiva finalizada. Se procesaron con éxito ' . count($datos) . ' expedientes.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::critical('Error en confirmación por lotes de Asociados: ' . $e->getMessage());
            return redirect()->route('asociados.sincronizar.index')
                ->with('error', 'Fallo de integridad transaccional en la base de datos. Ningún registro sufrió modificaciones.');
        }
    }

    /**
     * Método privado para centralizar las reglas de validación de los 48 campos.
     * @param int|null $id ID del registro a excluir en reglas unique.
     * @return array
     */
    private function getValidationRules($id = null)
    {
        return [
            // Identidad y Demografía
            'cedula'                    => 'required|string|max:15|unique:mae_asociados,cedula,' . $id,
            'nombre1'                   => 'required|string|max:100',
            'nombre2'                   => 'nullable|string|max:100',
            'apellido1'                 => 'required|string|max:100',
            'apellido2'                 => 'nullable|string|max:100',
            'fecha_nacimiento'          => 'nullable|date',
            'lugar_expedicion_cedula'   => 'nullable|string|max:50',
            'fecha_expedicion'          => 'nullable|date',
            'estado_civil'              => 'nullable|string|max:20',

            // Contacto
            'correo_pastor'             => 'nullable|email|max:100',
            'celular_pastor'            => 'nullable|string|max:15',
            'whatsapp'                  => 'nullable|string|max:15',

            // Información Ministerial
            'fecha_afiliacion'          => 'nullable|date',
            
            // Verificación relacional de Distrito (Asegura existencia en MaeDistritos)
            'distrito_actual'           => 'nullable|string|max:50|exists:MaeDistritos,COD_DIST',
            
            // Verificación relacional de Ciudad (Asegura existencia en geo_ciudades)
            'ciudad_distrito'           => 'nullable|string|max:100|exists:geo_ciudades,id_ciudad',
            
            'direccion_distrito'        => 'nullable|string|max:100',
            'estado_pastor'             => 'nullable|string|max:20',
            'especificacion'            => 'nullable|string|max:50',
            'licencia'                  => 'nullable|string|max:30',
            'pais'                      => 'nullable|string|max:30',
            'iglesia_actual'            => 'nullable|string|max:100',

            // Familia (Cónyuge)
            'cedula_esposa'             => 'nullable|string|max:15',
            'nombre_esposa'             => 'nullable|string|max:100',
            'correo_esposa'             => 'nullable|email|max:100',
            'celular_esposa'            => 'nullable|string|max:15',

            // Soportes Documentales
            'doc_formulario_afiliacion' => 'nullable|string|max:20',
            'doc_autorizacion_datos'    => 'nullable|string|max:20',
            'doc_cedula_pastor'         => 'nullable|string|max:20',
            'doc_cedula_esposa'         => 'nullable|string|max:20',
            'doc_licencia_pastoral'     => 'nullable|string|max:20',
            'doc_registro_matrimonio'   => 'nullable|string|max:20',
            'doc_id_hijos'              => 'nullable|string|max:20',

            // Gestión de Archivo ECM y Físico
            'ubicacion_ecm_link'        => 'nullable|url|max:255',
            'radicado'                  => 'nullable|string|max:20',
            'ubicacion_carpeta'         => 'nullable|string|max:30',
            'numero_caja'               => 'nullable|string|max:20',
            'cantidad_folios'           => 'nullable|integer|min:0',
            'fecha_ingreso_archivo'     => 'nullable|date',
            'estado_conservacion'       => 'nullable|string|max:30',
            'custodia_actual'           => 'nullable|string|max:50',
            'observaciones_archivo'     => 'nullable|string',

            // Metadatos Adicionales
            'observaciones_generales'   => 'nullable|string',
            'estado'                    => 'nullable|string|max:20',
        ];
    }
}