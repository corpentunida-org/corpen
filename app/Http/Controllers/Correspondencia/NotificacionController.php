<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Notificacion;
use App\Models\User;
use App\Models\Correspondencia\Proceso;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::with(['procesoOrigen','procesoDestino','usuarioDestino','usuarioEnvia'])->paginate(15);
        return view('correspondencia.notificaciones.index', compact('notificaciones'));
    }

    public function create()
    {
        $usuarios = User::all();
        $procesos = Proceso::all();
        return view('correspondencia.notificaciones.create', compact('usuarios','procesos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proceso_origen_id' => 'required|exists:corr_procesos,id',
            'proceso_destino_id' => 'required|exists:corr_procesos,id',
            'mensaje' => 'required|string',
            'estado' => 'required|string',
            'usuario_destino_id' => 'required|exists:users,id',
            'usuario_envia_id' => 'required|exists:users,id',
        ]);

        Notificacion::create($data);

        return redirect()->route('correspondencia.notificaciones.index')->with('success','Notificación creada');
    }

    public function show(Notificacion $notificacion)
    {
        return view('correspondencia.notificaciones.show', compact('notificacion'));
    }

    public function edit(Notificacion $notificacion)
    {
        $usuarios = User::all();
        $procesos = Proceso::all();
        return view('correspondencia.notificaciones.edit', compact('notificacion','usuarios','procesos'));
    }

    public function update(Request $request, Notificacion $notificacion)
    {
        $data = $request->validate([
            'mensaje' => 'required|string',
            'estado' => 'required|string',
        ]);

        $notificacion->update($data);

        return redirect()->route('correspondencia.notificaciones.index')->with('success','Notificación actualizada');
    }

    public function destroy(Notificacion $notificacion)
    {
        $notificacion->delete();
        return redirect()->route('correspondencia.notificaciones.index')->with('success','Notificación eliminada');
    }

    // Marcar como leída
    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['estado' => 'leida','fecha_leida' => now()]);

        return back()->with('success','Notificación marcada como leída');
    }
}
