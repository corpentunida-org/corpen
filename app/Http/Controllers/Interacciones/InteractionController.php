<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

use App\Models\Maestras\maeTerceros;
use App\Models\Interacciones\Interaction;
use App\Models\Interacciones\IntChannel;
use App\Models\Interacciones\IntType;
use App\Models\Interacciones\IntOutcome;
use App\Models\Interacciones\IntNextAction;

// --- NUEVO: Importar los modelos para las nuevas relaciones ---
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;

class InteractionController extends Controller
{
    /**
     * Muestra la lista de interacciones con filtros y búsqueda.
     */
    public function index(Request $request)
    {
        // --- 1. CONSTRUIMOS LA CONSULTA BASE (sin paginar aún) ---
        $baseQuery = Interaction::with([
            'client', 'agent', 'channel', 'type', 'outcomeRelation', 'nextAction',
            'area', 'areaDeAsignacion', 'cargo', 'lineaDeObligacion'
        ]);

        // --- 2. APLICAMOS TODOS LOS FILTROS (BÚSQUEDA Y FILTROS ADICIONALES) ---
        // (Tu lógica de filtros está bien, la aplicamos a la consulta base)
        if ($request->filled('q')) {
            // ... todo tu bloque de where para 'q' ...
            // Asegúrate de aplicarlo a $baseQuery, no a $query
            $q = $request->q;
            $baseQuery->where(function ($sub) use ($q) {
                // ... todo tu código de búsqueda ...
            });
        }

        if ($request->filled('channel_filter')) {
            $baseQuery->whereHas('channel', function ($q) use ($request) {
                $q->where('name', $request->channel_filter);
            });
        }
        // ... todos los demás filtros (tipo, outcome, fecha, etc.) ...
        // (Asegúrate de que tus otros filtros también se apliquen a $baseQuery)

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('interaction_date', '>=', $request->date_from);
        }

        // --- 3. OBTENEMOS LOS DATOS PARA LAS ESTADÍSTICAS Y PESTAÑAS (SIN PAGINAR) ---
        // Hacemos una copia de la consulta para obtener los conteos totales
        $countQuery = clone $baseQuery;

        $stats = [
            'total' => $countQuery->count(),
            'successful' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->count(),
            'pending' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->count(),
            'today' => (clone $countQuery)->whereDate('interaction_date', today())->count(),
        ];

        // Hacemos otra copia para obtener las colecciones completas de las pestañas
        $collectionsForTabs = [
            'successful' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->get(),
            'pending' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->get(),
            'today' => (clone $baseQuery)->whereDate('interaction_date', today())->get(),
        ];


        // --- 4. OBTENEMOS LA COLECCIÓN PAGINADA PARA LA PESTAÑA "TODOS" ---
        $interactions = $baseQuery->orderByDesc('interaction_date')->paginate(10);
        // La paginación preserva los filtros de la URL
        $interactions->appends($request->query());


        // --- 5. DATOS PARA LOS SELECT DE LA VISTA (CORREGIDO) ---
        $channels = IntChannel::orderBy('name')->pluck('name');
        $types = IntType::orderBy('name')->pluck('name');
        
        // ¡AQUÍ ESTÁ LA CORRECCIÓN! 
        // Definimos las variables que faltaban para los filtros.
        $outcomes = IntOutcome::orderBy('name')->pluck('name');
        $areas = GdoArea::orderBy('nombre')->pluck('nombre');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo');
        $lineas = LineaCredito::orderBy('nombre')->pluck('nombre');


        // --- 6. PASAMOS TODAS LAS VARIABLES A LA VISTA ---
        return view('interactions.index', compact(
            'interactions', // Para la paginación principal
            'stats',        // El array de estadísticas
            'collectionsForTabs', // Las colecciones para las otras pestañas
            'channels',
            'types',
            'outcomes',     // Ahora sí existe
            'areas',        // Ahora sí existe
            'cargos',       // Ahora sí existe
            'lineas'        // Ahora sí existe
        ));
    }
    /**
     * Muestra detalles y estadísticas de una interacción.
     */
    public function show(Interaction $interaction)
    {
        // --- NUEVO: Cargar todas las relaciones necesarias ---
        $interaction->load([
            'agent',
            'client',
            'channel',
            'type',
            'outcomeRelation',
            'nextAction',
            'area',
            'areaDeAsignacion',
            'cargo',
            'lineaDeObligacion'
        ]);

        $agentId = $interaction->agent_id;
        $range = request()->get('range', 'day');

        // --- CONSULTA DE ESTADÍSTICAS ---
        $query = Interaction::where('agent_id', $agentId);

        switch ($range) {
            case 'day':
                $query->selectRaw('DATE(interaction_date) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;
            case 'month':
                $query->selectRaw('DATE_FORMAT(interaction_date, "%Y-%m") as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;
            case 'year':
                $query->selectRaw('YEAR(interaction_date) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;
        }

        $chartData = $query->get();
        $labels = $chartData->pluck('label');
        $totals = $chartData->pluck('total');

        // --- HISTORIAL DEL CLIENTE ---
        $clientHistory = collect();
        if ($interaction->client_id) {
            $clientHistory = Interaction::with([
                    'agent',
                    'channel',
                    'type',
                    'outcomeRelation',
                    'nextAction',
                    'area',
                    'areaDeAsignacion',
                    'cargo',
                    'lineaDeObligacion'
                ])
                ->where('client_id', $interaction->client_id)
                ->orderByDesc('interaction_date')
                ->get();
        }

        return view('interactions.show', compact(
            'interaction', 'labels', 'totals', 'range', 'clientHistory'
        ));
    }

    /**
     * Formulario para crear una nueva interacción.
     */
    public function create()
    {
        $interaction = new Interaction();

        $channels = IntChannel::all();
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        // --- NUEVO: Obtener datos para los nuevos campos select ---
        // Usamos pluck para generar un array ideal para los selects de Blade: [id => nombre]
        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        return view('interactions.create', compact(
            'interaction', 'clientes', 'channels', 'types', 'outcomes', 'nextActions',
            // --- NUEVO: Pasar las nuevas variables a la vista ---
            'areas', 'cargos', 'lineasCredito'
        ));
    }

    /**
     * Guarda una nueva interacción en la base de datos.
     */
    public function store(Request $request)
    {        
        /* dd($request->all()); */
        $validatedData = $request->validate([
            'client_id'          => 'required|exists:MaeTerceros,cod_ter',
            'agent_id'           => 'required|exists:users,id',
            'interaction_date'   => 'required|date',
            'interaction_channel'=> 'required|exists:int_channels,id',
            'interaction_type'   => 'required|exists:int_types,id',
            'outcome'            => 'required|exists:int_outcomes,id',
            'notes'              => 'nullable|string',
            'next_action_date'   => 'nullable|date',
            'next_action_type'   => 'nullable|exists:int_next_actions,id',
            'next_action_notes'  => 'nullable|string',
            'interaction_url'    => 'nullable|url',
            'attachments.*'      => 'file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',

            // --- NUEVO: Reglas de validación para los nuevos campos ---
            'id_area'               => 'nullable|integer|exists:gdo_area,id',
            'id_cargo'              => 'nullable|integer|exists:gdo_cargo,id',
            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_area_de_asignacion' => 'nullable|integer|exists:gdo_area,id',
            // 'id_funciones' se comenta porque la relación está comentada en el modelo.
            // Si la activas, descomenta esta línea y asegúrate que la tabla 'gdo_funciones' exista.
            // 'id_funciones'          => 'nullable|integer|exists:gdo_funciones,id',
        ]);

        // Forzar el agente autenticado
        $validatedData['agent_id'] = Auth::id();

        // Asignar un valor por defecto a next_action_type si viene vacío
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        // Manejo de archivos adjuntos
        if ($request->hasFile('attachments')) {
            $storedFiles = [];
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
        }

        // Crear la interacción (los nuevos campos se guardarán gracias a $fillable en el modelo)
        $interaction = Interaction::create($validatedData);

        // Calcular duración de la interacción
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->created_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')->with('success', 'Interacción creada exitosamente.');
    }


    /**
     * Formulario para editar una interacción existente.
     */
    public function edit(Interaction $interaction)
    {
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'nom2', 'cod_dist', 'congrega')
            ->where('estado', 1)
            ->orderBy('nom_ter')
            ->get();
        
        $channels = IntChannel::all(); 
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        // --- NUEVO: Obtener datos para los nuevos campos select (igual que en create) ---
        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        return view('interactions.edit', compact(
            'interaction', 'clientes', 'channels', 'types', 'outcomes', 'nextActions',
            // --- NUEVO: Pasar las nuevas variables a la vista ---
            'areas', 'cargos', 'lineasCredito'
        ));
    }

    /**
     * Actualiza una interacción existente.
     */
    public function update(Request $request, Interaction $interaction)
    {
        $validatedData = $request->validate([
            'client_id'          => 'required|exists:MaeTerceros,cod_ter',
            'agent_id'           => 'required|exists:users,id',
            'interaction_date'   => 'required|date',
            'interaction_channel'=> 'required|exists:int_channels,id',
            'interaction_type'   => 'required|exists:int_types,id',
            'outcome'            => 'required|exists:int_outcomes,id',
            'notes'              => 'nullable|string',
            'next_action_date'   => 'nullable|date',
            'next_action_type'   => 'nullable|exists:int_next_actions,id',
            'next_action_notes'  => 'nullable|string',
            'interaction_url'    => 'nullable|url',
            'attachments.*'      => 'file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',

            // --- NUEVO: Reglas de validación consistentes con store ---
            'id_area'               => 'nullable|integer|exists:gdo_area,id',
            'id_cargo'              => 'nullable|integer|exists:gdo_cargo,id',
            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_area_de_asignacion' => 'nullable|integer|exists:gdo_area,id',
            // 'id_funciones'          => 'nullable|integer|exists:gdo_funciones,id',
        ]);

        // Asignar valor por defecto si el campo viene vacío
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        // Mantener archivos existentes y agregar nuevos si los hay
        $storedFiles = $interaction->attachment_urls ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
        }

        // Actualizar la interacción (los nuevos campos se actualizan gracias a $fillable)
        $interaction->update($validatedData);

        // Recalcular duración
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->updated_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')
            ->with('success', 'Interacción actualizada exitosamente.');
    }

    /**
     * Visualiza un archivo adjunto en el navegador.
     */
    public function viewAttachment($fileName)
    {
        // --- CORRECCIÓN: Visualización desde S3 ---
        try {
            // Buscar el archivo en S3
            $path = 'corpentunida/daytrack/' . $fileName;
            
            if (!Storage::disk('s3')->exists($path)) {
                abort(404, "Archivo no encontrado.");
            }
            
            $file = Storage::disk('s3')->get($path);
            $mimeType = Storage::disk('s3')->mimeType($path);
            
            return response($file)->header('Content-Type', $mimeType);
        } catch (Exception $e) {
            Log::error('Error al visualizar archivo: ' . $e->getMessage());
            abort(404, "Archivo no encontrado: {$fileName}");
        }
    }
    
    /**
     * Obtener datos del cliente para AJAX
     */
    public function getCliente($cod_ter)
    {
        try {
            // Mejoramos la consulta para asegurar que se carguen todas las relaciones necesarias
            $cliente = maeTerceros::where('cod_ter', $cod_ter)
                ->with(['maeTipos', 'distrito', 'congregaciones'])
                ->first();

            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            // Obtener historial de interacciones del cliente con más información
            $history = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation'])
                ->where('client_id', $cod_ter)
                ->orderByDesc('interaction_date')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'date' => $item->interaction_date->format('d/m/Y H:i'),
                        'agent' => $item->agent ? $item->agent->name : 'No asignado',
                        'type' => $item->type ? $item->type->name : 'No definido',
                        'outcome' => $item->outcomeRelation ? $item->outcomeRelation->name : 'No definido',
                        'notes' => $item->notes ? substr($item->notes, 0, 100) . (strlen($item->notes) > 100 ? '...' : '') : '',
                        'channel' => $item->channel ? $item->channel->name : 'No definido',
                        'duration' => $item->duration ?? 0,
                    ];
                });

            // Preparamos la respuesta con todos los datos necesarios
            $response = [
                'cod_ter' => $cliente->cod_ter,
                'nom_ter' => $cliente->nom_ter ?? 'No registrado',
                'email' => $cliente->email ?? 'No registrado',
                'dir' => $cliente->dir ?? 'No registrado',
                'tel1' => $cliente->tel1 ?? 'No registrado',
                'cel1' => $cliente->cel1 ?? 'No registrado',
                'ciudad' => $cliente->ciudad ?? 'No registrado',
                'departamento' => $cliente->departamento ?? 'No registrado',
                'pais' => $cliente->pais ?? 'No registrado',
                'cod_dist' => $cliente->cod_dist ?? 'No registrado',
                'barrio' => $cliente->barrio ?? 'No registrado',
                'cod_est' => $cliente->cod_est ?? 'No registrado',
                'congrega' => $cliente->congrega ?? 'No registrado',
                'history' => $history,
                // Tipo de cliente
                'maeTipos' => $cliente->maeTipos ? [
                    'id' => $cliente->maeTipos->id,
                    'nombre' => $cliente->maeTipos->nombre ?? 'No definido',
                ] : null,
                // Distrito
                'distrito' => $cliente->distrito ? [
                    'COD_DIST' => $cliente->distrito->COD_DIST,
                    'NOM_DIST' => $cliente->distrito->NOM_DIST ?? 'No definido',
                    'DETALLE' => $cliente->distrito->DETALLE ?? 'No definido',
                    'COMPUEST' => $cliente->distrito->COMPUEST ?? 'No definido',
                ] : null,
                // Congregacion
                'congregaciones' => $cliente->congregaciones ? [
                    'codigo' => $cliente->congregaciones->codigo,
                    'nombre' => $cliente->congregaciones->nombre ?? 'No definido',
                ] : null,
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            // Registramos el error para depuración
            \Log::error('Error al cargar cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Buscar clientes para Select2 AJAX
     */
    public function searchClients(Request $request)
    {
        $search = $request->get('q');
        
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'cod_dist', 'congrega' )
            ->where('estado', 1)
            ->where(function($query) use ($search) {
                $query->where('nom_ter', 'like', "%{$search}%")
                      ->orWhere('apl1', 'like', "%{$search}%")
                      ->orWhere('apl2', 'like', "%{$search}%")
                      ->orWhere('nom1', 'like', "%{$search}%")
                      ->orWhere('nom2', 'like', "%{$search}%")
                      ->orWhere('cod_ter', 'like', "%{$search}%");
            })
            ->orderBy('nom_ter')
            ->paginate(50);
        
        return response()->json([
            'results' => $clientes->items(),
            'pagination' => [
                'more' => $clientes->hasMorePages()
            ]
        ]);
    }

    /**
     * 🆕 Obtener el COD_DIST de un cliente para AJAX.
     *
     * @param  int  $client_id  (Este es el cod_ter)
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClientDistrict($client_id)
    {
        // Buscamos el cliente usando su cod_ter
        $client = maeTerceros::find($client_id);

        if (!$client) {
            // Si no se encuentra el cliente, devolvemos un error 404
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Devolvemos el código del distrito en formato JSON.
        // Usamos 'district_id' como clave para mayor claridad en el frontend.
        return response()->json([
            'district_id' => $client->cod_dist,
        ]);
    }

    /**
     * 🆕 Actualizar el COD_DIST de un cliente.
     *
     * @param  int  $client_id  (Este es el cod_ter)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClientDistrict($client_id, Request $request)
    {
        // Validar que se proporcionó un district_id
        $request->validate([
            'district_id' => 'required|string|max:255',
        ]);

        // Buscamos el cliente usando su cod_ter
        $client = maeTerceros::find($client_id);

        if (!$client) {
            // Si no se encuentra el cliente, devolvemos un error 404
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        // Actualizamos el código del distrito
        $client->cod_dist = $request->input('district_id');
        $client->save();

        // Devolvemos una respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Distrito actualizado correctamente',
            'district_id' => $client->cod_dist,
        ]);
    }

    /**
     * Formulario para editar una interacción existente.
     */
/*
    public function edit(Interaction $interaction)
    {
        $channels = IntChannel::all(); 
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');
        $distrito = maeDistritos::orderBy('NOM_DIST')->pluck('NOM_DIST', 'COD_DIST');
        
        // --- NUEVO: Obtener el cargo y área del agente logueado ---
        $agente = Auth::user();
        $cargoAgente = null;
        $idCargoAgente = null;
        $areaAgente = null;
        $idAreaAgente = null;
        
        if ($agente) {
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $idCargoAgente = $cargoAgente->id;
                
                // Obtener el área a través del cargo
                $areaAgente = $cargoAgente->gdoArea;
                if ($areaAgente) {
                    $idAreaAgente = $areaAgente->id;
                }
            }
        }

        return view('interactions.edit', compact(
            'interaction', 'channels', 'types', 'outcomes', 'nextActions',
            'areas', 'cargos', 'lineasCredito', 'distrito', 
            'idCargoAgente', 'idAreaAgente'
        ));
    }
*/
    /**
     * Elimina una interacción y sus archivos adjuntos.
     */
/*    
    public function destroy(Interaction $interaction)
    {
        try {
            // --- CORRECCIÓN: Manejo correcto de archivo adjunto ---
            if ($interaction->attachment_urls) {
                Storage::disk('s3')->delete($interaction->attachment_urls);
            }

            $interaction->delete();

            return redirect()->route('interactions.index')
                ->with('success', 'Interacción eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar interacción ' . $interaction->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al eliminar la interacción.');
        }
    } 
*/

    /**
     * Descarga un archivo adjunto.
     */
    public function downloadAttachment($fileName)
    {
        $path = "public/interactions/" . $fileName;

        if (!Storage::exists($path)) {
            abort(404, "Archivo no encontrado.");
        }

        return Storage::download($path);
    }

    /**
     * Visualiza un archivo adjunto en el navegador.
     */
    public function viewAttachment($fileName)
    {
        $path = storage_path("app/interactions/{$fileName}");

        if (!file_exists($path)) {
            abort(404, "Archivo no encontrado: {$fileName}");
        }

        return response()->file($path);
    }

}