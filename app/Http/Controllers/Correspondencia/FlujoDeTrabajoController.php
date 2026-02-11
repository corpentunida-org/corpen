<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Http\Controllers\AuditoriaController;
use App\Models\User;

class FlujoDeTrabajoController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }
    public function index()
    {
        // Agregamos 'correspondencias_count' de forma eficiente
        $flujos = FlujoDeTrabajo::with('usuario')
            ->withCount('correspondencias') 
            ->paginate(15);

        return view('correspondencia.flujos.index', compact('flujos'));
    }

    public function create()
    {
        $usuarios = User::all();
        return view('correspondencia.flujos.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'detalle' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
        ]);

        $flujoadd = FlujoDeTrabajo::create($data);
        $this->auditoria('ADD FLUJO DE TRABAJO ID ' . $flujoadd->id);

        return redirect()->route('correspondencia.flujos.index')->with('success','Flujo creado correctamente');
    }

    public function show(FlujoDeTrabajo $flujo)
    {
        // Cargamos usuario responsable, conteo de correspondencias 
        // y la lista de procesos con sus usuarios asignados
        $flujo->load(['usuario', 'procesos.usuarios'])
            ->loadCount('correspondencias');
            
        return view('correspondencia.flujos.show', compact('flujo'));
    }
    public function edit(FlujoDeTrabajo $flujo)
    {
        $usuarios = User::all();
        return view('correspondencia.flujos.edit', compact('flujo','usuarios'));
    }

    public function update(Request $request, FlujoDeTrabajo $flujo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'detalle' => 'nullable|string',
            'usuario_id' => 'required|exists:users,id',
        ]);

        $flujo->update($data);
        $this->auditoria('UPDATE FLUJO DE TRABAJO ID ' . $flujo->id);
        return redirect()->route('correspondencia.flujos.index')->with('success','Flujo actualizado correctamente');
    }

    public function destroy(FlujoDeTrabajo $flujo)
    {
        // VALIDACIÓN: Si el flujo tiene correspondencias, no se puede eliminar
        if ($flujo->correspondencias()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: Este flujo está siendo utilizado en registros de correspondencia.');
        }

        $flujo->delete();
        return redirect()->route('correspondencia.flujos.index')->with('success', 'Flujo eliminado correctamente.');
    }
}
