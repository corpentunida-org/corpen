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

// Importar los modelos para los catálogos
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
        // --- 1. CONSTRUIMOS LA CONSULTA BASE ---
        $baseQuery = Interaction::with([
            'client', 'agent.cargoRelation.gdoArea', 'channel', 'type', 'outcomeRelation', 'nextAction',
            'lineaDeObligacion', 'usuarioAsignado'
        ]);

        // --- 3. OBTENEMOS LOS DATOS PARA LAS ESTADÍSTICAS Y PESTAÑAS (SIN PAGINAR) ---
        $countQuery = clone $baseQuery;

        $stats = [
            'total' => $countQuery->count(),
            'successful' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->count(),
            'pending' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->count(),
            'today' => (clone $countQuery)->where(function ($q) {$q->whereDate('interaction_date', today())->orWhereDate('updated_at', today());})->count(),
            'overdue' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->where('next_action_date', '<', today()->startOfDay())->count(),
        ];

        $collectionsForTabs = [           
            'successful' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->get(),
            'pending' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->get(),
            'today' => (clone $baseQuery)->where(function ($q) {$q->whereDate('interaction_date', today())->orWhereDate('updated_at', today());})->get(),
            'overdue' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->where('next_action_date', '<', today()->startOfDay())->get(),
        ];
        
        
        // --- 4. OBTENEMOS LA COLECCIÓN PAGINADA PARA LA PESTAÑA "TODOS" ---
        $interactions = $baseQuery->orderBy('id', 'desc')->paginate(100);
        $interactions->appends($request->query());

        // --- 5. DATOS PARA LOS SELECT DE LA VISTA ---
        $channels = IntChannel::orderBy('name')->pluck('name', 'id');
        $types = IntType::orderBy('name')->pluck('name', 'id');
        $outcomes = IntOutcome::orderBy('name')->pluck('name', 'id');
        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineas = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');

        // --- 6. PASAMOS TODAS LAS VARIABLES A LA VISTA ---
        return view('interactions.index', compact(
            'interactions',
            'stats',
            'collectionsForTabs',
            'channels',
            'types',
            'outcomes',
            'areas',
            'cargos',
            'lineas'
        ));
    }

    /**
     * Muestra detalles y estadísticas de una interacción.
     */
    public function show(Interaction $interaction)
    {
        $interaction->load([
            'agent',
            'client',
            'channel',
            'type',
            'outcomeRelation',
            'nextAction',
            'lineaDeObligacion',
            'usuarioAsignado'
        ]);

        $agentId = $interaction->agent_id;
        $range = request()->get('range', 'day');

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

        $clientHistory = collect();
        if ($interaction->client_id) {
            $clientHistory = Interaction::with([
                    'agent',
                    'channel',
                    'type',
                    'outcomeRelation',
                    'nextAction',
                    'lineaDeObligacion',
                    'usuarioAsignado'
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

        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');
        
        $agente = Auth::user();
        $cargoAgente = null;
        $idCargoAgente = null;
        $areaAgente = null;
        $idAreaAgente = null;
        
        if ($agente) {
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $idCargoAgente = $cargoAgente->id;
                
                $areaAgente = $cargoAgente->gdoArea;
                if ($areaAgente) {
                    $idAreaAgente = $areaAgente->id;
                }
            }
        }

        return view('interactions.create', compact(
            'interaction', 'channels', 'types', 'outcomes', 'nextActions',
            'areas', 'cargos', 'lineasCredito', 
            'idCargoAgente', 'idAreaAgente'
        ));
    }

    /**
     * Guarda una nueva interacción en la base de datos.
     */
    public function store(Request $request)
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
            'attachment'         => 'nullable|file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',

            'cedula_quien_llama'   => 'nullable|string|max:50',
            'nombre_quien_llama'   => 'nullable|string|max:255',
            'celular_quien_llama'  => 'nullable|string|max:50',
            'parentezco_quien_llama' => 'nullable|string|max:50',

            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_user_asignacion'    => 'nullable|integer|exists:users,id',
            
            'start_time'            => 'nullable|date',
            'duration'              => 'nullable|integer|min:0',
            'parent_interaction_id' => 'nullable|integer|exists:interactions,id',
        ]);

        $validatedData['agent_id'] = Auth::id();
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;
        $validatedData['duration'] = $validatedData['duration'] ?? 0;
        
        unset($validatedData['start_time']); 

        if ($request->hasFile('attachment')) {
            try {
                $file = $request->file('attachment');
            } catch (\Exception $e) {
                Log::error("Excepción al subir archivo: " . $e->getMessage());
            }
        }

        $interaction = Interaction::create($validatedData);
        
        if ($request->file('attachment')) {
            $ruta = Storage::disk('s3')->put('corpentunida/daytrack/' . $interaction->id, $file);
            $interaction->update(['attachment_urls' => $ruta]);
        }

        return redirect()->route('interactions.index')->with('success', 'Interacción creada exitosamente.');
    }

    /**
     * Formulario para editar una interacción existente.
     */
    public function edit(Interaction $interaction)
    {
        $channels = IntChannel::all(); 
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        $areas = GdoArea::orderBy('nombre')->pluck('nombre', 'id');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo', 'id');
        $lineasCredito = LineaCredito::orderBy('nombre')->pluck('nombre', 'id');
        
        $agente = Auth::user();
        $cargoAgente = null;
        $idCargoAgente = null;
        $areaAgente = null;
        $idAreaAgente = null;
        
        if ($agente) {
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $idCargoAgente = $cargoAgente->id;
                $areaAgente = $cargoAgente->gdoArea;
                if ($areaAgente) {
                    $idAreaAgente = $areaAgente->id;
                }
            }
        }

        return view('interactions.edit', compact(
            'interaction', 'channels', 'types', 'outcomes', 'nextActions',
            'areas', 'cargos', 'lineasCredito', 
            'idCargoAgente', 'idAreaAgente'
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
            'attachment'         => 'nullable|file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',

            'cedula_quien_llama'   => 'nullable|string|max:50',
            'nombre_quien_llama'   => 'nullable|string|max:255',
            'celular_quien_llama'  => 'nullable|string|max:50',
            'parentezco_quien_llama' => 'nullable|string|max:50',

            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_user_asignacion'    => 'nullable|integer|exists:users,id',
            
            'start_time'            => 'nullable|date',
            'duration'              => 'nullable|integer|min:0',
            'parent_interaction_id' => 'nullable|integer|exists:interactions,id',
        ]);

        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;
        $validatedData['duration'] = $validatedData['duration'] ?? 0;
        
        unset($validatedData['start_time']);

        if ($request->hasFile('attachment')) {
            try {
                $file = $request->file('attachment');
                
                if ($file->isValid()) {
                    $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                    
                    $ruta = Storage::disk('s3')->putFileAs(
                        'corpentunida/daytrack', 
                        $file,                  
                        $fileName,              
                        'public'                
                    );
                    
                    if ($ruta) {
                        Log::info("Nuevo archivo guardado exitosamente en S3: " . $ruta);
                        $validatedData['attachment_urls'] = $ruta;
                    } else {
                        Log::error("Fallo al guardar el nuevo archivo en S3: " . $file->getClientOriginalName());
                    }
                }
            } catch (\Exception $e) {
                Log::error("Excepción al subir nuevo archivo: " . $e->getMessage());
            }
        }

        $interaction->update($validatedData);

        return redirect()->route('interactions.index')
            ->with('success', 'Interacción actualizada exitosamente.');
    }

    public function destroy(Interaction $interaction)
    {
        try {
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

    public function downloadAttachment($fileName)
    {
        try {
            $path = 'corpentunida/daytrack/' . $fileName;
            
            if (!Storage::disk('s3')->exists($path)) {
                abort(404, "Archivo no encontrado.");
            }
            
            return Storage::disk('s3')->download($path);
        } catch (Exception $e) {
            Log::error('Error al descargar archivo: ' . $e->getMessage());
            abort(404, "Archivo no encontrado.");
        }
    }

    public function viewAttachment($fileName)
    {
        try {
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
            // Mantenemos distrito aquí SOLO para mostrarlo en la tarjeta de información del cliente
            $cliente = maeTerceros::where('cod_ter', $cod_ter)
                ->with(['maeTipos', 'distrito', 'congregaciones'])
                ->first();

            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }

            $history = Interaction::with([
                    'agent', 
                    'channel', 
                    'type', 
                    'outcomeRelation',
                    'lineaDeObligacion',
                    'usuarioAsignado' 
                ])
                ->where('client_id', $cod_ter)
                ->orderByDesc('interaction_date')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'client_id' => $item->client_id,
                        'agent' => $item->agent ? $item->agent->name : 'No asignado',
                        'date' => $item->interaction_date ? $item->interaction_date->format('d/m/Y H:i') : null,
                        'date_iso' => $item->interaction_date,
                        'duration' => $item->duration ?? 0,
                        'type' => $item->type ? $item->type->name : 'No definido',
                        'channel' => $item->channel ? $item->channel->name : 'No definido',
                        'outcome' => $item->outcomeRelation ? $item->outcomeRelation->name : 'No definido',
                        'notes' => $item->notes,
                        'parent_interaction_id' => $item->parent_interaction_id,
                        
                        'next_action_date' => $item->next_action_date ? $item->next_action_date->format('d/m/Y H:i') : null,
                        'next_action_type' => $item->next_action_type,
                        'next_action_notes' => $item->next_action_notes,

                        'parentezco_quien_llama' => $item->parentezco_quien_llama,
                        'cedula_quien_llama' => $item->cedula_quien_llama,
                        'nombre_quien_llama' => $item->nombre_quien_llama,
                        'celular_quien_llama' => $item->celular_quien_llama,

                        'attachment_urls' => $item->attachment_urls ?? [], 
                        'interaction_url' => $item->interaction_url,

                        'id_linea_de_obligacion' => $item->id_linea_de_obligacion,
                        'linea_obligacion_name' => $item->lineaDeObligacion ? ($item->lineaDeObligacion->nombre ?? $item->lineaDeObligacion->name) : null, 

                        'id_user_asignacion' => $item->id_user_asignacion,
                        'usuario_asignado_name' => $item->usuarioAsignado ? $item->usuarioAsignado->name : null,
                    ];
                });

            $response = [
                'cod_ter' => $cliente->cod_ter,
                'nom_ter' => $cliente->nom_ter ?? 'No registrado',
                'nom1' => $cliente->nom1, 
                'apl1' => $cliente->apl1, 
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
                
                'maeTipos' => $cliente->maeTipos ? [
                    'id' => $cliente->maeTipos->id,
                    'nombre' => $cliente->maeTipos->nombre ?? 'No definido',
                ] : null,
                
                'distrito' => $cliente->distrito ? [
                    'COD_DIST' => $cliente->distrito->COD_DIST,
                    'NOM_DIST' => $cliente->distrito->NOM_DIST ?? 'No definido',
                    'DETALLE' => $cliente->distrito->DETALLE ?? 'No definido',
                    'COMPUEST' => $cliente->distrito->COMPUEST ?? 'No definido',
                ] : null,
                
                'congregaciones' => $cliente->congregaciones ? [
                    'codigo' => $cliente->congregaciones->codigo,
                    'nombre' => $cliente->congregaciones->nombre ?? 'No definido',
                ] : null,
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error al cargar cliente ' . $cod_ter . ': ' . $e->getMessage());
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
     * Buscar usuarios para Select2 AJAX (Delegar a otro)
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q');
        
        $users = \App\Models\User::select('id', 'name', 'email')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(50);
        
        return response()->json([
            'results' => $users->map(function($user) {
                return [
                    'id' => $user->id, 
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            }),
            'pagination' => [
                'more' => $users->hasMorePages()
            ]
        ]);
    }
}