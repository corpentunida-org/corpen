<?php

namespace App\Http\Controllers\Interacciones;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Maestras\maeTerceros;
use App\Models\Interacciones\Interaction;
use App\Models\Interacciones\IntChannel;

use Carbon\Carbon;
use Exception;

class InteractionController extends Controller
{
    /**
     * Lista todas las interacciones con filtros de búsqueda.
     */
    public function index(Request $request)
    {
        $query = Interaction::with(['client', 'agent']);

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
            $clientHistory = Interaction::with('agent')
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

        $channels = IntChannel::all();  // obtiene todos los canales disponibles

            return view('interactions.create', compact('interaction', 'clientes', 'channels'));
    }

    /**
     * Guarda una nueva interacción en la base de datos.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:MaeTerceros,cod_ter',
            'agent_id' => 'required|exists:users,id',
            'interaction_date' => 'required|date',
            'interaction_channel' => 'required|exists:int_channels,id', // 👈 validación actualizada
            'interaction_type' => 'required|string|max:255',
            'outcome' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'next_action_type' => 'nullable|string|max:255',
            'next_action_notes' => 'nullable|string',
            'interaction_url' => 'nullable|url',
            'attachments.*' => 'file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',
        ]);

        // Asigna automáticamente el agente autenticado
        $validatedData['agent_id'] = Auth::id();

        // Guarda archivos adjuntos si hay
        if ($request->hasFile('attachments')) {
            $storedFiles = [];
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
            $validatedData['attachment_urls'] = $storedFiles;
        }

        // Crea la interacción
        $interaction = Interaction::create($validatedData);

        // Calcula duración
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->created_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')
            ->with('success', 'Interacción creada exitosamente.');
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
            
        return view('interactions.edit', compact('interaction', 'clientes', 'channels'));
    }

    /**
     * Actualiza una interacción existente.
     */
    public function update(Request $request, Interaction $interaction)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:MaeTerceros,cod_ter',
            'agent_id' => 'required|exists:users,id',
            'interaction_date' => 'required|date',
            'interaction_channel' => 'required|string|max:255',
            'interaction_type' => 'required|string|max:255',
            'outcome' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'next_action_type' => 'nullable|string|max:255',
            'next_action_notes' => 'nullable|string',
            'interaction_url' => 'nullable|url',
            'attachments.*' => 'file|mimes:jpeg,png,pdf,jpg,doc,docx|max:10240',
        ]);

        $storedFiles = $interaction->attachment_urls ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFiles[] = $file->store('interactions');
            }
        }
        $validatedData['attachment_urls'] = $storedFiles;

        $interaction->update($validatedData);

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
