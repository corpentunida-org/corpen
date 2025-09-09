<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Maestras\maeTerceros;
use App\Models\Interaction;
use Carbon\Carbon; // Asegúrate de importar Carbon

use Exception;



class InteractionController extends Controller
{
    /**
     * Muestra una lista de todas las interacciones.
     */
    public function index(Request $request)
    {
        $query = Interaction::with(['client', 'agent']);

        if ($request->filled('q')) {
            $q = $request->q;

            $query->where(function($sub) use ($q) {
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

        $interactions = $query->paginate(2);

        return view('interactions.index', compact('interactions'));
    }

    public function show(Interaction $interaction)
    {
        $agentId = $interaction->agent_id;

        // Por defecto: mostrar datos diarios
        $range = request()->get('range', 'day'); // 'day', 'month', 'year'

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

        // Formatear los datos para Chart.js
        $labels = $chartData->pluck('label');
        $totals = $chartData->pluck('total');

        // ===== NUEVO: Histórico de interacciones del cliente =====
        $clientHistory = collect(); // Por defecto vacío
        if ($interaction->client_id) {
            $clientHistory = Interaction::with('agent')
                ->where('client_id', $interaction->client_id)
                ->orderBy('interaction_date', 'desc')
                ->get();
        }

        return view('interactions.show', compact('interaction', 'labels', 'totals', 'range', 'clientHistory'));
    }


    /**
     * Muestra el formulario para crear una nueva interacción.
     */
    public function create()
    {
        // Pasamos una variable 'interaction' vacía para que el formulario no de error
        $interaction = new Interaction();

        // Traer todos los clientes (solo los campos que necesitas)
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2')
            ->where('estado', 1) // opcional, si solo activos
            ->orderBy('nom_ter')
            ->get();

        // Enviamos $interaction y $clientes a la vista
        return view('interactions.create', compact('interaction', 'clientes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:MaeTerceros,cod_ter', // Ajusta según tu BD
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

        // Asignar automáticamente el agente logueado (opcional)
        $validatedData['agent_id'] = Auth::id();

        // Guardar archivos adjuntos
        if ($request->hasFile('attachments')) {
            $storedFiles = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('interactions'); // Se guarda en storage/app/interactions
                $storedFiles[] = $path;
            }
            $validatedData['attachment_urls'] = $storedFiles;
        }

        // Crear la interacción
        $interaction = Interaction::create($validatedData);

        // Calcular la duración automáticamente
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->created_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')->with('success', 'Interacción creada exitosamente.');
    }

    
    /**
     * Muestra el formulario para editar una interacción específica.
     */
    public function edit(Interaction $interaction)
    {
        // Traer todos los clientes (solo los campos que necesitas)
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2')
            ->where('estado', 1) // opcional: solo clientes activos
            ->orderBy('nom_ter')
            ->get();

        return view('interactions.edit', compact('interaction', 'clientes'));
    }


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

        // Manejar archivos: conservar los existentes y agregar nuevos
        $storedFiles = $interaction->attachment_urls ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('interactions'); // storage/app/interactions
                $storedFiles[] = $path;
            }
        }
        $validatedData['attachment_urls'] = $storedFiles;

        // Actualizar la interacción
        $interaction->update($validatedData);

        // Recalcular duración automáticamente
        $inicio = Carbon::parse($interaction->interaction_date);
        $fin = Carbon::parse($interaction->created_at);
        $interaction->duration = $fin->diffInMinutes($inicio);
        $interaction->save();

        return redirect()->route('interactions.index')->with('success', 'Interacción actualizada exitosamente.');
    }

    /**
     * Elimina una interacción de la base de datos y sus adjuntos.
     */
    public function destroy(Interaction $interaction)
    {
        try {
            // Elimina los archivos físicos asociados
            if ($interaction->attachment_urls) {
                foreach ($interaction->attachment_urls as $url) {
                    // Convierte la URL de vuelta a una ruta de storage
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
     * Permite descargar un archivo adjunto.
     */
    public function downloadAttachment($fileName)
    {
        // Se asume que el archivo está en 'public/interactions'
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

}