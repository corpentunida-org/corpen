<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Archivo\GdoArea; // Importado para la selección de áreas
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
        // Cargamos la relación 'area' y 'usuario' (jefe) para la tabla principal
        $flujos = FlujoDeTrabajo::with(['usuario', 'area'])
            ->withCount('correspondencias') 
            ->paginate(15);

        return view('correspondencia.flujos.index', compact('flujos'));
    }

    public function create()
    {
        // Cargamos el cargo jefe, el empleado de ese cargo y el usuario de ese empleado
        $areas = GdoArea::where('estado', 'activo')
            ->with(['jefeCargo.user']) 
            ->get();
        
        $usuarios = User::all(); 

        return view('correspondencia.flujos.create', compact('usuarios', 'areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:150',
            'detalle'    => 'nullable|string',
            'id_area'    => 'required|exists:gdo_area,id', // Validación del área seleccionada
            'usuario_id' => 'required|exists:users,id',    // El ID del jefe (jefeCargo)
        ]);

        $flujoadd = FlujoDeTrabajo::create($data);
        
        $this->auditoria('ADD FLUJO DE TRABAJO ID ' . $flujoadd->id);

        return redirect()->route('correspondencia.flujos.index')
            ->with('success', 'Flujo de trabajo creado y asignado al área correctamente.');
    }

    public function show(FlujoDeTrabajo $flujo)
    {
        // Cargamos área, jefe y procesos, pero filtramos los usuarios de los procesos
        $flujo->load([
            'usuario', 
            'area', 
            'procesos.usuarios' => function($query) {
                $query->where('activo', true); // <--- SOLO USUARIOS ACTIVOS
            }
        ])->loadCount('correspondencias');
            
        return view('correspondencia.flujos.show', compact('flujo'));
    }

    public function edit(FlujoDeTrabajo $flujo)
    {
        $areas = GdoArea::where('estado', 'activo')->with(['jefeCargo.user'])->get();
        $usuarios = User::all(); // Opcional, ya que ahora es automático

        return view('correspondencia.flujos.edit', compact('flujo', 'usuarios', 'areas'));
    }

    public function update(Request $request, FlujoDeTrabajo $flujo)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:150',
            'detalle'    => 'nullable|string',
            'id_area'    => 'required|exists:gdo_area,id',
            'usuario_id' => 'required|exists:users,id',
        ]);

        $flujo->update($data);

        $this->auditoria('UPDATE FLUJO DE TRABAJO ID ' . $flujo->id);

        return redirect()->route('correspondencia.flujos.show', $flujo)
            ->with('success', 'Flujo actualizado correctamente.');
    }

    public function destroy(FlujoDeTrabajo $flujo)
    {
        // VALIDACIÓN: Si el flujo tiene correspondencias, no se puede eliminar
        if ($flujo->correspondencias()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar: Este flujo tiene registros de correspondencia vinculados.');
        }

        $flujo->delete();
        $this->auditoria('DELETE FLUJO DE TRABAJO ID ' . $flujo->id);

        return redirect()->route('correspondencia.flujos.index')
            ->with('success', 'Flujo eliminado correctamente.');
    }
}