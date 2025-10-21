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

class InteractionController extends Controller
{
    /**
     * Lista todas las interacciones con filtros de búsqueda.
     */
    public function index(Request $request)
    {
        $query = Interaction::with([
            'client',
            'agent',
            'channel',
            'type',
            'outcomeRelation',
            'nextAction'
        ]);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('interaction_type', 'like', "%$q%")
                    ->orWhere('outcome', 'like', "%$q%")
                    ->orWhereHas('agent', function ($a) use ($q) {
                        $a->where('name', 'like', "%$q%");
                    })
                    ->orWhereHas('client', function ($c) use ($q) {
                        $c->where('cod_ter', 'like', "%$q%")
                          ->orWhere('apl1', 'like', "%$q%")
                          ->orWhere('apl2', 'like', "%$q%")
                          ->orWhere('nom1', 'like', "%$q%")
                          ->orWhere('nom2', 'like', "%$q%");
                    });
            });
        }

        $interactions = $query->paginate();
        return view('interactions.index', compact('interactions'));
    }

    /**
     * Muestra detalles y estadísticas de una interacción.
     */
    public function show(Interaction $interaction)
    {
        $agentId = $interaction->agent_id;
        $range = request()->get('range', 'day');

        $query = Interaction::where('agent_id', $agentId);

        switch ($range) {
            case 'day':
                $query->selectRaw('DATE(interaction_date) as label, COUNT(*) as total')
                      ->groupBy('label')->orderBy('label');
                break;
            case 'month':
                $query->selectRaw('DATE_FORMAT(interaction_date, "%Y-%m") as label, COUNT(*) as total')
                      ->groupBy('label')->orderBy('label');
                break;
            case 'year':
                $query->selectRaw('YEAR(interaction_date) as label, COUNT(*) as total')
                      ->groupBy('label')->orderBy('label');
                break;
        }

        $chartData = $query->get();
        $labels = $chartData->pluck('label');
        $totals = $chartData->pluck('total');

        $clientHistory = collect();
        if ($interaction->client_id) {
            $clientHistory = Interaction::with(['agent', 'channel', 'type', 'outcomeRelation', 'nextAction'])
                ->where('client_id', $interaction->client_id)
                ->orderBy('interaction_date', 'desc')
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
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'cod_dist', 'congrega' )
            ->where('estado', 1)
            ->orderBy('nom_ter')
            ->get();

        $channels = IntChannel::all();
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        return view('interactions.create', compact(
            'interaction', 'clientes', 'channels', 'types', 'outcomes', 'nextActions'
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
            'attachments.*'      => 'file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',
        ]);

        // 👇 Forzar el agente autenticado (independiente del formulario)
        $validatedData['agent_id'] = Auth::id();

        // 👇 Asignar un valor por defecto a next_action_type si viene vacío o null
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        // 👇 Manejo de archivos adjuntos
        if ($request->hasFile('attachments')) {
            $storedFiles = [];
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
            $validatedData['attachment_urls'] = $storedFiles;
        }

        // 👇 Crear la interacción
        $interaction = Interaction::create($validatedData);

        // 👇 Calcular duración de la interacción
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
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2')
            ->where('estado', 1)
            ->orderBy('nom_ter')
            ->get();
        
        $channels = IntChannel::all(); 
        $types = IntType::all();
        $outcomes = IntOutcome::all();
        $nextActions = IntNextAction::all();

        return view('interactions.edit', compact(
            'interaction', 'clientes', 'channels', 'types', 'outcomes', 'nextActions'
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
        ]);

        // 👇 Asignar valor por defecto si el campo viene vacío o null
        $validatedData['next_action_type'] = $request->input('next_action_type') ?? 1;

        // 👇 Mantener archivos existentes y agregar nuevos si los hay
        $storedFiles = $interaction->attachment_urls ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
        }
        $validatedData['attachment_urls'] = $storedFiles;

        // 👇 Actualizar la interacción
        $interaction->update($validatedData);

        // 👇 Recalcular duración
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->created_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')
            ->with('success', 'Interacción actualizada exitosamente.');
    }


    /**
     * Elimina una interacción y sus archivos adjuntos.
     */
    public function destroy(Interaction $interaction)
    {
        try {
            if ($interaction->attachment_urls) {
                foreach ($interaction->attachment_urls as $url) {
                    $path = str_replace('/storage', 'public', $url);
                    Storage::delete($path);
                }
            }
            $interaction->delete();

            return redirect()->route('interactions.index')
                ->with('success', 'Interacción eliminada exitosamente.');
        } catch (Exception $e) {
            Log::error('Error al eliminar interacción ' . $interaction->id . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al eliminar la interacción.');
        }
    }

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
