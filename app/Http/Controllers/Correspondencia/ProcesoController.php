<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Proceso;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Correspondencia\ProcesoUsuario;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProcesoController extends Controller
{
    public function index()
    {
        // Cargamos relaciones para evitar el problema de consultas N+1
        $procesos = Proceso::with(['flujo', 'creador', 'usuarios'])->paginate(15);
        return view('correspondencia.procesos.index', compact('procesos'));
    }

    public function create()
    {
        $flujos = FlujoDeTrabajo::all();
        // Ya no necesitamos enviar todos los usuarios para el creador, 
        // pero los enviamos por si la vista los usa para otra cosa.
        $usuarios = User::all(); 
        return view('correspondencia.procesos.create', compact('flujos', 'usuarios'));
    }

    public function store(Request $request)
    {
        // Eliminamos 'usuario_creador_id' de la validación porque se asigna internamente
        $data = $request->validate([
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'detalle'  => 'nullable|string',
        ]);

        // Asignación automática del usuario logueado
        $data['usuario_creador_id'] = Auth::id();

        $proceso = Proceso::create($data);

        // MEJORA: Redirigimos al SHOW para obligar a asignar usuarios inmediatamente
        return redirect()->route('correspondencia.procesos.show', $proceso)
            ->with('success', 'Proceso creado. Por favor, asigne el personal responsable ahora.');
    }

    public function show(Proceso $proceso)
    {
        // Cargamos los usuarios asignados para la lista en el show
        $proceso->load('usuarios', 'flujo', 'creador');
        return view('correspondencia.procesos.show', compact('proceso'));
    }

    public function edit(Proceso $proceso)
    {
        $flujos = FlujoDeTrabajo::all();
        $usuarios = User::all();
        return view('correspondencia.procesos.edit', compact('proceso', 'flujos', 'usuarios'));
    }

    public function update(Request $request, Proceso $proceso)
    {
        // Protegemos el usuario_creador_id: no se valida ni se actualiza desde el request
        $data = $request->validate([
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'detalle'  => 'nullable|string',
        ]);

        $proceso->update($data);

        return redirect()->route('correspondencia.procesos.index')
            ->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy(Proceso $proceso)
    {
        // Nota: Al eliminar el proceso, la tabla pivote corr_procesos_users 
        // debería borrarse si configuraste 'onDelete cascade' en la migración.
        $proceso->delete();
        return redirect()->route('correspondencia.procesos.index')
            ->with('success', 'Proceso eliminado correctamente');
    }

    /**
     * Gestión de Usuarios del Proceso
     */

    public function asignarUsuario(Request $request, Proceso $proceso)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'detalle' => 'nullable|string|max:255'
        ]);

        // Usamos la relación definida en el modelo Proceso para adjuntar
        // syncWithoutDetaching evita duplicados si se envía dos veces
        $proceso->usuarios()->syncWithoutDetaching([
            $request->user_id => ['detalle' => $request->detalle]
        ]);

        return back()->with('success', 'Usuario asignado al equipo correctamente');
    }

    public function removerUsuario($proceso_id, $user_id)
    {
        $proceso = Proceso::findOrFail($proceso_id);
        $proceso->usuarios()->detach($user_id);
        
        return back()->with('success', 'Usuario removido del equipo');
    }

    /**
     * Consultas AJAX
     */
    public function getUsuariosByProceso($proceso_id)
    {
        $usuarios = ProcesoUsuario::with('usuario')
            ->where('proceso_id', $proceso_id)
            ->get();
            
        return response()->json($usuarios);
    }
}