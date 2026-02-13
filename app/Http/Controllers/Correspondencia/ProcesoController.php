<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Proceso;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Correspondencia\ProcesoUsuario;
use App\Models\Correspondencia\Estado; // <--- AGREGADO
use App\Models\Correspondencia\EstadoProceso; // <--- AGREGADO
use App\Http\Controllers\AuditoriaController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProcesoController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }

    public function index()
    {
        $procesos = Proceso::with(['flujo', 'creador', 'usuariosAsignados.usuario'])
            ->latest()
            ->paginate(15);
            
        return view('correspondencia.procesos.index', compact('procesos'));
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
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'   => 'required|string|max:255',
            'detalle'  => 'nullable|string',
            'activo'   => 'nullable|boolean',
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
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'   => 'required|string|max:255',
            'detalle'  => 'nullable|string',
            'activo'   => 'required|boolean',
        ]);

        $proceso->update($data);
        $this->auditoria('UPDATE PROCESO ID ' . $proceso->id);
        
        return redirect()->route('correspondencia.procesos.index')
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

        $proceso->usuarios()->syncWithoutDetaching([
            $request->user_id => [
                'detalle' => $request->detalle,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $this->auditoria('ASIGNAR USUARIO ID ' . $request->user_id . ' A PROCESO ' . $proceso->id);

        return back()->with('success', 'Responsable asignado correctamente');
    }

    public function removerUsuario($proceso_id, $user_id)
    {
        $proceso = Proceso::findOrFail($proceso_id);
        $proceso->usuarios()->detach($user_id);
        
        $this->auditoria('REMOVER USUARIO ID ' . $user_id . ' DE PROCESO ' . $proceso_id);
        
        return back()->with('success', 'Responsable removido del equipo');
    }

    // =========================================================================
    // GESTIÓN DE ESTADOS / PERMISOS (Modelo EstadoProceso)
    // =========================================================================

    /**
     * Registra una acción o estado permitido para este paso del proceso.
     */
    public function guardarEstado(Request $request, Proceso $proceso)
    {
        $request->validate([
            'id_estado' => 'required|exists:corr_estados,id',
            'detalle'   => 'nullable|string|max:255'
        ]);

        EstadoProceso::create([
            'id_proceso' => $proceso->id,
            'id_estado'  => $request->id_estado,
            'detalle'    => $request->detalle
        ]);

        $this->auditoria('ADD ESTADO ID ' . $request->id_estado . ' A PROCESO ' . $proceso->id);

        return back()->with('success', 'Configuración de estado guardada.');
    }

    /**
     * Elimina una configuración de estado específica.
     */
    public function eliminarEstado($id)
    {
        $estado = EstadoProceso::findOrFail($id);
        $proceso_id = $estado->id_proceso;
        $estado->delete();

        $this->auditoria('DELETE ESTADO-PROCESO ID ' . $id);

        return back()->with('success', 'Estado/Permiso removido del proceso.');
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