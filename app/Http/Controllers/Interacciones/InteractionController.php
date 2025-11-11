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
        $clientes = maeTerceros::select('cod_ter', 'nom_ter', 'apl1', 'apl2', 'nom1', 'nom2', 'cod_dist', 'congrega' )
            ->where('estado', 1)
            ->orderBy('nom_ter')
            ->get();

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
            $validatedData['attachment_urls'] = $storedFiles;
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
        $validatedData['attachment_urls'] = $storedFiles;

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