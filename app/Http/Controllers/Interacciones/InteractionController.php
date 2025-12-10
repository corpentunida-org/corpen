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

use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use App\Models\Maestras\maeDistritos;

class InteractionController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Interaction::with([
            'client', 'agent', 'channel', 'type', 'outcomeRelation', 'nextAction',
            'area', 'areaDeAsignacion', 'cargo', 'lineaDeObligacion'
        ]);

        if ($request->filled('q')) {
            $q = $request->q;
            $baseQuery->where(function ($sub) use ($q) {
                $sub->where('notes', 'like', "%{$q}%")
                    ->orWhereHas('client', function ($query) use ($q) {
                        $query->where('nom_ter', 'like', "%{$q}%")
                              ->orWhere('apl1', 'like', "%{$q}%")
                              ->orWhere('nom1', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('channel_filter')) {
            $baseQuery->whereHas('channel', function ($q) use ($request) {
                $q->where('name', $request->channel_filter);
            });
        }

        if ($request->filled('type_filter')) {
            $baseQuery->whereHas('type', function ($q) use ($request) {
                $q->where('name', $request->type_filter);
            });
        }

        if ($request->filled('outcome_filter')) {
            $baseQuery->whereHas('outcomeRelation', function ($q) use ($request) {
                $q->where('name', $request->outcome_filter);
            });
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('interaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('interaction_date', '<=', $request->date_to);
        }

        $countQuery = clone $baseQuery;

        $stats = [
            'total' => $countQuery->count(),
            'successful' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->count(),
            'pending' => (clone $countQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->count(),
            'today' => (clone $countQuery)->whereDate('interaction_date', today())->count(),
        ];

        $collectionsForTabs = [
            'successful' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Exitoso'))->get(),
            'pending' => (clone $baseQuery)->whereHas('outcomeRelation', fn($q) => $q->where('name', 'Pendiente'))->get(),
            'today' => (clone $baseQuery)->whereDate('interaction_date', today())->get(),
        ];

        $interactions = $baseQuery->orderByDesc('interaction_date')->paginate(100);
        $interactions->appends($request->query());

        $channels = IntChannel::orderBy('name')->pluck('name');
        $types = IntType::orderBy('name')->pluck('name');
        $outcomes = IntOutcome::orderBy('name')->pluck('name');
        $areas = GdoArea::orderBy('nombre')->pluck('nombre');
        $cargos = GdoCargo::orderBy('nombre_cargo')->pluck('nombre_cargo');
        $lineas = LineaCredito::orderBy('nombre')->pluck('nombre');
        $distrito = maeDistritos::orderBy('NOM_DIST')->pluck('NOM_DIST', 'COD_DIST');

        return view('interactions.index', compact(
            'interactions',
            'stats',
            'collectionsForTabs',
            'channels',
            'types',
            'outcomes',
            'areas',
            'cargos',
            'lineas',
            'distrito'
        ));
    }

    public function show(Interaction $interaction)
    {
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
        $distrito = maeDistritos::orderBy('NOM_DIST')->pluck('NOM_DIST', 'COD_DIST');
        
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
            'areas', 'cargos', 'lineasCredito', 'distrito', 
            'idCargoAgente', 'idAreaAgente'
        ));
    }

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
            'id_area'               => 'nullable|integer|exists:gdo_area,id',
            'id_cargo'              => 'nullable|integer|exists:gdo_cargo,id',
            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_area_de_asignacion' => 'nullable|integer|exists:gdo_area,id',
            'id_distrito_interaccion' => 'nullable|integer|exists:MaeDistritos,COD_DIST',
            'start_time'            => 'nullable|date',
            'duration'              => 'nullable|integer|min:0',
        ]);

        $validatedData['agent_id'] = Auth::id();
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        $validatedData['duration'] = $validatedData['duration'] ?? 0;
        $validatedData['start_time'] = $validatedData['start_time'] ?? null;
        
        if (!isset($validatedData['id_cargo']) || empty($validatedData['id_cargo'])) {
            $agente = Auth::user();
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $validatedData['id_cargo'] = $cargoAgente->id;
            }
        }
        
        if (!isset($validatedData['id_area']) || empty($validatedData['id_area'])) {
            $agente = Auth::user();
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente && $cargoAgente->gdoArea) {
                $validatedData['id_area'] = $cargoAgente->gdoArea->id;
            }
        }

        $archivo = $request->hasFile('attachments');
        
        $interaction = Interaction::create($validatedData);
        
        if ($archivo) {
            $ruta = Storage::disk('s3')->put('corpentunida/daytrack',$request->file('attachments'));
            $interaction->update(['attachment_urls' => $ruta]);
        }

        return redirect()->route('interactions.index')->with('success', 'Interacción creada exitosamente.');
    }

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
            'areas', 'cargos', 'lineasCredito', 'distrito', 
            'idCargoAgente', 'idAreaAgente'
        ));
    }

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
            'id_area'               => 'nullable|integer|exists:gdo_area,id',
            'id_cargo'              => 'nullable|integer|exists:gdo_cargo,id',
            'id_linea_de_obligacion'=> 'nullable|integer|exists:cre_lineas_creditos,id',
            'id_area_de_asignacion' => 'nullable|integer|exists:gdo_area,id',
            'id_distrito_interaccion' => 'nullable|integer|exists:MaeDistritos,COD_DIST',
            'start_time'            => 'nullable|date',
            'duration'              => 'nullable|integer|min:0',
        ]);

        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        $validatedData['duration'] = $validatedData['duration'] ?? 0;
        $validatedData['start_time'] = $validatedData['start_time'] ?? null;
        
        if (!isset($validatedData['id_cargo']) || empty($validatedData['id_cargo'])) {
            $agente = Auth::user();
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente) {
                $validatedData['id_cargo'] = $cargoAgente->id;
            }
        }
        
        if (!isset($validatedData['id_area']) || empty($validatedData['id_area'])) {
            $agente = Auth::user();
            $cargoAgente = $agente->cargoRelation;
            if ($cargoAgente && $cargoAgente->gdoArea) {
                $validatedData['id_area'] = $cargoAgente->gdoArea->id;
            }
        }

        $storedFiles = $interaction->attachment_urls ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
        }

        $interaction->update($validatedData);

        return redirect()->route('interactions.index')
            ->with('success', 'Interacción actualizada exitosamente.');
    }

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

    public function downloadAttachment($fileName)
    {
        $path = "public/interactions/" . $fileName;

        if (!Storage::exists($path)) {
            abort(404, "Archivo no encontrado.");
        }

        return Storage::download($path);
    }

    public function viewAttachment($fileName)
    {
        $path = storage_path("app/interactions/{$fileName}");

        if (!file_exists($path)) {
            abort(404, "Archivo no encontrado: {$fileName}");
        }

        return response()->file($path);
    }
    
    public function getCliente($cod_ter)
    {
        $cliente = maeTerceros::where('cod_ter', $cod_ter)
            ->with(['maeTipos', 'distrito', 'congregaciones'])
            ->first();

        if ($cliente) {
            $history = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation'])
                ->where('client_id', $cod_ter)
                ->orderByDesc('interaction_date')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->interaction_date->format('d/m/Y H:i'),
                        'agent' => $item->agent->name,
                        'type' => $item->type->name,
                        'outcome' => $item->outcomeRelation->name,
                        'notes' => substr($item->notes, 0, 100) . (strlen($item->notes) > 100 ? '...' : ''),
                    ];
                });

            return response()->json([
                'cod_ter' => $cliente->cod_ter,
                'nom_ter' => $cliente->nom_ter,
                'email' => $cliente->email,
                'dir' => $cliente->dir,
                'tel1' => $cliente->tel1,
                'cel1' => $cliente->cel1,
                'ciudad' => $cliente->ciudad,
                'departamento' => $cliente->departamento,
                'pais' => $cliente->pais,
                'cod_dist' => $cliente->cod_dist,
                'barrio' => $cliente->barrio,
                'cod_est' => $cliente->cod_est,
                'congrega' => $cliente->congrega,
                'history' => $history,

                'maeTipos' => $cliente->maeTipos ? [
                    'id' => $cliente->maeTipos->id,
                    'nombre' => $cliente->maeTipos->nombre,
                ] : null,

                'distrito' => $cliente->distrito ? [
                    'COD_DIST' => $cliente->distrito->COD_DIST,
                    'NOM_DIST' => $cliente->distrito->NOM_DIST,
                    'DETALLE' => $cliente->distrito->DETALLE,
                    'COMPUEST' => $cliente->distrito->COMPUEST,
                ] : null,

                'congregaciones' => $cliente->congregaciones ? [
                    'codigo' => $cliente->congregaciones->codigo,
                    'nombre' => $cliente->congregaciones->nombre,
                ] : null,
            ]);
        }

        return response()->json(['error' => 'Cliente no encontrado'], 404);
    }
    
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
}