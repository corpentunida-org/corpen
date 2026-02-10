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
        // Cargamos relaciones incluyendo usuariosAsignados para ver participantes desde el index si es necesario
        $procesos = Proceso::with(['flujo', 'creador', 'usuariosAsignados.usuario'])
            ->latest()
            ->paginate(15);
            
        return view('correspondencia.procesos.index', compact('procesos'));
    }

    public function create()
    {
        $flujos = FlujoDeTrabajo::all();
        $usuarios = User::all(); 
        return view('correspondencia.procesos.create', compact('flujos', 'usuarios'));
    }

    public function store(Request $request)
    {
        // Agregamos 'nombre' a la validación
        $data = $request->validate([
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'   => 'required|string|max:255',
            'detalle'  => 'nullable|string',
        ]);

        // Asignación automática del usuario logueado
        $data['usuario_creador_id'] = Auth::id();

        $proceso = Proceso::create($data);

        // Redirigimos al SHOW para obligar a asignar usuarios inmediatamente
        return redirect()->route('correspondencia.procesos.show', $proceso)
            ->with('success', 'Etapa creada correctamente. Ahora puede asignar los responsables.');
    }

    public function show(Proceso $proceso)
    {
        // Cargamos los usuarios asignados a través de la relación de modelo y la de muchos a muchos
        $proceso->load(['flujo', 'creador', 'usuariosAsignados.usuario', 'usuarios']);
        
        // Usuarios disponibles para asignar (que no estén ya en el proceso)
        $usuarios_disponibles = User::whereNotIn('id', $proceso->usuarios->pluck('id'))->get();
        
        return view('correspondencia.procesos.show', compact('proceso', 'usuarios_disponibles'));
    }

    public function edit(Proceso $proceso)
    {
        $flujos = FlujoDeTrabajo::all();
        $usuarios = User::all();
        return view('correspondencia.procesos.edit', compact('proceso', 'flujos', 'usuarios'));
    }

    public function update(Request $request, Proceso $proceso)
    {
        // Validamos incluyendo el campo 'nombre'
        $data = $request->validate([
            'flujo_id' => 'required|exists:corr_flujo_de_trabajo,id',
            'nombre'   => 'required|string|max:255',
            'detalle'  => 'nullable|string',
        ]);

        $proceso->update($data);

        return redirect()->route('correspondencia.procesos.index')
            ->with('success', 'Proceso actualizado correctamente');
    }

    public function destroy(Proceso $proceso)
    {
        // Se eliminan las relaciones en cascada si está definido en la BD
        $proceso->delete();
        
        return redirect()->route('correspondencia.procesos.index')
            ->with('success', 'Proceso eliminado correctamente');
    }

    /**
     * Gestión de Usuarios del Proceso (Participantes)
     */
    public function asignarUsuario(Request $request, Proceso $proceso)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'detalle' => 'nullable|string|max:255'
        ]);

        // syncWithoutDetaching evita duplicados y mantiene la integridad de la tabla corr_procesos_users
        $proceso->usuarios()->syncWithoutDetaching([
            $request->user_id => [
                'detalle' => $request->detalle,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        return back()->with('success', 'Responsable asignado correctamente');
    }

    public function removerUsuario($proceso_id, $user_id)
    {
        $proceso = Proceso::findOrFail($proceso_id);
        $proceso->usuarios()->detach($user_id);
        
        return back()->with('success', 'Responsable removido del equipo');
    }

    /**
     * Consultas AJAX / API
     */
    public function getUsuariosByProceso($proceso_id)
    {
        // Retornamos los usuarios asignados con su detalle (pivot)
        $usuarios = ProcesoUsuario::with('usuario')
            ->where('proceso_id', $proceso_id)
            ->get();
            
        return response()->json($usuarios);
    }
}