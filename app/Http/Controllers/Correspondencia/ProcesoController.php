<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Proceso;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Correspondencia\ProcesoUsuario;
use App\Models\Correspondencia\Estado;
use App\Models\Correspondencia\EstadoProceso;
use App\Http\Controllers\AuditoriaController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ProcesoController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }

    // En ProcesoController.php

    public function index(Request $request)
    {
        // Caché de Estadísticas
        $stats = Cache::remember('procesos_stats', 600, function () {
            return [
                'total' => Proceso::count(),
                'activos' => Proceso::where('activo', true)->count(),
                'inactivos' => Proceso::where('activo', false)->count(),
                'completados_hoy' => Proceso::whereDate('updated_at', Carbon::today())->count(),
            ];
        });

        // Eager Loading 
        $query = Proceso::query()
            ->select(['id', 'nombre', 'activo', 'flujo_id', 'created_at'])
            ->with([
                'flujo.area', // <-- TRUCO: Así traemos el flujo y su área al mismo tiempo
                'usuariosAsignados.usuario:id,name'
            ]);

        // Filtros
        $query->when($request->search, fn($q, $search) => 
            $q->where('nombre', 'like', "%{$search}%")->orWhere('id', $search)
        );

        $query->when($request->filled('estado'), fn($q) => 
            $q->where('activo', $request->estado === 'activo' ? 1 : 0)
        );

        $query->when($request->filled('fecha_desde'), fn($q) => 
            $q->whereDate('created_at', '>=', $request->fecha_desde)
        );

        // Paginación
        $procesos = $query->latest('id')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'table' => view('correspondencia.procesos._table', compact('procesos'))->render(),
                'pagination' => (string) $procesos->links()
            ]);
        }

        return view('correspondencia.procesos.index', compact('procesos', 'stats'));
    }

    // Controlador para Acciones Masivas (Sin opción de eliminar)
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            // Validamos que solo existan acciones de activar o desactivar
            'action' => 'required|in:activate,deactivate'
        ]);

        switch ($request->action) {
            case 'activate':
                Proceso::whereIn('id', $request->ids)->update(['activo' => true]);
                break;
            case 'deactivate':
                Proceso::whereIn('id', $request->ids)->update(['activo' => false]);
                break;
        }

        Cache::forget('procesos_stats');
        return back()->with('success', 'Estados actualizados correctamente.');
    }

    /**
     * Formulario de creación de un nuevo paso/proceso.
     */
    public function create()
    {
        $flujos = FlujoDeTrabajo::all();
        $usuarios = User::all(); 
        return view('correspondencia.procesos.create', compact('flujos', 'usuarios'));
    }

    /**
     * Guarda el proceso básico en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'flujo_id'              => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'                => 'required|string|max:255',
            'detalle'               => 'nullable|string',
            'activo'                => 'nullable|boolean',
            'numero_archivos'       => 'nullable|string|max:255',
            'tipos_archivos'        => 'nullable',
            'tiempo_respuesta_dias' => 'nullable|integer|min:0', // <-- AGREGADO
        ]);

        $data['activo'] = $request->has('activo') ? $request->activo : 1;
        $data['usuario_creador_id'] = Auth::id();

        $proceso = Proceso::create($data);
        $this->auditoria('ADD PROCESO ID ' . $proceso->id . ' AL FLUJO ' . $request->flujo_id);

        // REDIRECCIÓN HACIA ATRÁS: Mantiene al usuario en la vista del detalle del flujo
        return back()->with('success', 'Nuevo paso agregado correctamente al flujo.');
    }
    
    /**
     * Vista de detalle: Gestión de usuarios y estados del proceso.
     */
    public function show(Proceso $proceso)
    {
        // Cargamos todas las relaciones necesarias
        $proceso->load([
            'flujo', 
            'creador', 
            'usuariosAsignados.usuario', 
            'usuarios', 
            'estadosProcesos.estado' 
        ]);
        
        // Usuarios que aún no forman parte de este equipo de trabajo
        $usuarios_disponibles = User::whereNotIn('id', $proceso->usuarios->pluck('id'))->get();

        // VARIABLE PARA EL MODAL: Traemos los estados disponibles en el catálogo maestro
        // Asegúrate que el modelo Estado exista en App\Models\Correspondencia
        $estados_catalogo = Estado::orderBy('nombre')->get();
        
        return view('correspondencia.procesos.show', compact('proceso', 'usuarios_disponibles', 'estados_catalogo'));
    }

    /**
     * Edición de datos básicos del proceso.
     */
    public function edit(Proceso $proceso)
    {
        $flujos = FlujoDeTrabajo::all();
        $usuarios = User::all();
        return view('correspondencia.procesos.edit', compact('proceso', 'flujos', 'usuarios'));
    }

    /**
     * Actualiza la información del proceso.
     */
    public function update(Request $request, Proceso $proceso)
    {
        $data = $request->validate([
            'flujo_id'              => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'                => 'required|string|max:255',
            'detalle'               => 'nullable|string',
            'activo'                => 'required|boolean',
            'numero_archivos'       => 'nullable|string|max:255',
            'tipos_archivos'        => 'nullable',
            'tiempo_respuesta_dias' => 'nullable|integer|min:0', // <-- AGREGADO
        ]);

        $proceso->update($data);
        $this->auditoria('UPDATE PROCESO ID ' . $proceso->id);
        
        return redirect()->route('correspondencia.procesos.show', $proceso)
            ->with('success', 'Proceso actualizado correctamente');
    }

    /**
     * Elimina el proceso y sus dependencias.
     */
    public function destroy(Proceso $proceso)
    {
        $proceso->delete();
        $this->auditoria('DELETE PROCESO ID ' . $proceso->id);
        
        return redirect()->route('correspondencia.procesos.index')
            ->with('success', 'Proceso eliminado correctamente');
    }

    // =========================================================================
    // GESTIÓN DE USUARIOS (RESPONSABLES)
    // =========================================================================

    public function asignarUsuario(Request $request, Proceso $proceso)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'detalle' => 'nullable|string|max:255'
        ]);

        /**
         * syncWithoutDetaching:
         * Si el usuario no está, lo inserta con activo = 1.
         * Si ya existe, actualiza el detalle y asegura que vuelva a estar activo = 1.
         */
        $proceso->usuarios()->syncWithoutDetaching([
            $request->user_id => [
                'detalle'    => $request->detalle,
                'activo'     => 1, // Siempre que se asigna o re-asigna, se marca como activo
                'updated_at' => now()
            ]
        ]);

        $this->auditoria('ASIGNAR/ACTIVAR USUARIO ID ' . $request->user_id . ' EN PROCESO ' . $proceso->id);

        return back()->with('success', 'Responsable asignado y activado correctamente');
    }

    public function removerUsuario($proceso_id, $user_id)
    {
        $proceso = Proceso::findOrFail($proceso_id);
        
        /**
         * NO USAMOS detach() para no borrar la evidencia de auditoría.
         * updateExistingPivot cambia el estado a 0 (inactivo).
         */
        $proceso->usuarios()->updateExistingPivot($user_id, [
            'activo'     => 0,
            'updated_at' => now()
        ]);
        
        $this->auditoria('DESACTIVAR USUARIO ID ' . $user_id . ' DE PROCESO ' . $proceso_id);
        
        return back()->with('success', 'El responsable ha sido marcado como inactivo para este proceso');
    }


    // =========================================================================
    // GESTIÓN DE ESTADOS / PERMISOS (Modelo EstadoProceso)
    // =========================================================================

    /**
     * Registra un estado permitido para este paso o reactiva uno inactivo.
     */
    public function guardarEstado(Request $request, Proceso $proceso)
    {
        $request->validate([
            'id_estado' => 'required|exists:corr_estados,id',
            'detalle'   => 'nullable|string|max:255'
        ]);

        /**
         * updateOrCreate:
         * Busca si ya existe este estado vinculado a este proceso.
         * Si no existe, lo crea. Si existe (incluso si estaba inactivo), 
         * le actualiza el detalle y lo vuelve a poner activo = 1.
         */
        EstadoProceso::updateOrCreate(
            [
                'id_proceso' => $proceso->id,
                'id_estado'  => $request->id_estado,
            ],
            [
                'detalle' => $request->detalle,
                'activo'  => 1 // Siempre que se guarda o re-asigna, se activa
            ]
        );

        $this->auditoria('ADD/ACTIVAR ESTADO ID ' . $request->id_estado . ' A PROCESO ' . $proceso->id);

        return back()->with('success', 'Configuración de estado guardada y activada.');
    }

    /**
     * Desactiva una configuración de estado específica (Pasa al historial)
     */
    public function eliminarEstado($id)
    {
        $estado = EstadoProceso::findOrFail($id);
        
        // En lugar de $estado->delete(), lo desactivamos
        $estado->update([
            'activo' => 0
        ]);

        $this->auditoria('DESACTIVAR ESTADO-PROCESO ID ' . $id);

        return back()->with('success', 'Estado removido y pasado al historial.');
    }

    // =========================================================================
    // CONSULTAS API / AJAX
    // =========================================================================

    public function getUsuariosByProceso($proceso_id)
    {
        $usuarios = ProcesoUsuario::with('usuario')
            ->where('proceso_id', $proceso_id)
            ->get();
            
        return response()->json($usuarios);
    }
}