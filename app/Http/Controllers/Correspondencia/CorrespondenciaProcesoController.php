<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\CorrespondenciaProceso;

class CorrespondenciaProcesoController extends Controller
{
    public function index()
    {
        $procesos = CorrespondenciaProceso::with(['correspondencia','proceso','usuario'])->paginate(15);
        return view('correspondencia.correspondencias_procesos.index', compact('procesos'));
    }

    public function create()
    {
        return view('correspondencia.correspondencias_procesos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'observacion' => 'nullable|string',
            'estado' => 'required|string',
            'id_proceso' => 'required|exists:corr_procesos,id',
            'notificado_email' => 'boolean',
            'fecha_gestion' => 'nullable|date',
            'documento_arc' => 'nullable|string',
            'fk_usuario' => 'required|exists:users,id',
        ]);

        CorrespondenciaProceso::create($data);

        return redirect()->route('correspondencia.correspondencias-procesos.index')->with('success','Registro de proceso creado');
    }

    public function show(CorrespondenciaProceso $correspondenciaProceso)
    {
        return view('correspondencia.correspondencias_procesos.show', compact('correspondenciaProceso'));
    }

    public function edit(CorrespondenciaProceso $correspondenciaProceso)
    {
        return view('correspondencia.correspondencias_procesos.edit', compact('correspondenciaProceso'));
    }

    public function update(Request $request, CorrespondenciaProceso $correspondenciaProceso)
    {
        $data = $request->validate([
            'observacion' => 'nullable|string',
            'estado' => 'required|string',
            'notificado_email' => 'boolean',
            'fecha_gestion' => 'nullable|date',
            'documento_arc' => 'nullable|string',
        ]);

        $correspondenciaProceso->update($data);

        return redirect()->route('correspondencia.correspondencias-procesos.index')->with('success','Registro de proceso actualizado');
    }

    public function destroy(CorrespondenciaProceso $correspondenciaProceso)
    {
        $correspondenciaProceso->delete();
        return redirect()->route('correspondencia.correspondencias-procesos.index')->with('success','Registro de proceso eliminado');
    }

    // Marcar notificado por email
    public function marcarNotificado($id)
    {
        $registro = CorrespondenciaProceso::findOrFail($id);
        $registro->update(['notificado_email' => true]);
        return back()->with('success','Correo de notificación marcado');
    }
}
