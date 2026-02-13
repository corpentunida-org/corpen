<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use App\Models\Correspondencia\MedioRecepcion;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Http\Request;

class MedioRecepcionController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }

    public function index()
    {
        $medios = MedioRecepcion::latest()->paginate(15);
        return view('correspondencia.medios_recepcion.index', compact('medios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'      => 'required|string|max:50|unique:corr_medio_recepcion,codigo',
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'activo'      => 'nullable', 
        ]);

        // Si no viene el check, por defecto es 1 en creación
        $data['activo'] = $request->has('activo') ? 1 : 1;

        $medio = MedioRecepcion::create($data);
        $this->auditoria('ADD MEDIO RECEPCION ID ' . $medio->id);

        return back()->with('success', 'Medio de recepción creado correctamente.');
    }

    /**
     * El parámetro debe llamarse $medio_recepcion o $medio 
     * según como esté definido en el Route::resource
     */
    public function update(Request $request, $id)
    {
        // Buscamos el registro directamente por su ID
        $medio = MedioRecepcion::findOrFail($id);

        $data = $request->validate([
            'codigo'      => 'required|string|max:50|unique:corr_medio_recepcion,codigo,' . $id,
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        // LÓGICA DE ACTUALIZACIÓN DE ESTADO:
        // Si el checkbox 'activo' viene en el request, ponemos 1. Si no viene, ponemos 0.
        $data['activo'] = $request->has('activo') ? 1 : 0;

        $medio->update($data);
        
        $this->auditoria('UPDATE MEDIO RECEPCION ID ' . $id);

        return back()->with('success', 'Medio de recepción actualizado correctamente.');
    }

    public function destroy($id)
    {
        $medio = MedioRecepcion::findOrFail($id);
        $this->auditoria('DELETE MEDIO RECEPCION ID ' . $medio->id);
        $medio->delete();

        return back()->with('success', 'Medio de recepción eliminado.');
    }
}