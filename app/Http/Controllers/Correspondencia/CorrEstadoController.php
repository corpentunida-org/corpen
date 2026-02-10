<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Estado;
use Illuminate\Database\QueryException;

class CorrEstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::withCount('correspondencias')->paginate(15);
        return view('correspondencia.estados.index', compact('estados'));
    }

    public function create()
    {
        return view('correspondencia.estados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:corr_estados,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.unique' => 'Ya existe un estado con este nombre.'
        ]);

        try {
            Estado::create($data);
            return redirect()->route('correspondencia.estados.index')
                ->with('success', 'Estado creado correctamente.');
        } catch (QueryException $e) {
            // Captura errores de duplicidad que la validación se saltó por milisegundos
            return back()->withInput()->with('error', 'Error de duplicidad: El registro ya existe.');
        }
    }

    public function show(Estado $estado)
    {
        $estado->load('correspondencias');
        return view('correspondencia.estados.show', compact('estado'));
    }

    public function edit(Estado $estado)
    {
        return view('correspondencia.estados.edit', compact('estado'));
    }

    public function update(Request $request, Estado $estado)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:corr_estados,nombre,' . $estado->id,
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.unique' => 'Este nombre de estado ya está en uso por otro registro.'
        ]);

        try {
            $estado->update($data);
            return redirect()->route('correspondencia.estados.index')
                ->with('success', 'Estado actualizado correctamente.');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Error al actualizar: Verifique los datos.');
        }
    }
    /* 
    public function destroy(Estado $estado)
    {
        if ($estado->correspondencias()->count() > 0) {
            return redirect()->route('correspondencia.estados.index')
                ->with('error', 'No se puede eliminar: El estado tiene documentos asociados.');
        }

        $estado->delete();
        return redirect()->route('correspondencia.estados.index')
            ->with('success', 'Estado eliminado correctamente.');
    }
    */
}