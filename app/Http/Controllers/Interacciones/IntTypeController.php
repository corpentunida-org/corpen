<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntType;
use Illuminate\Http\Request;

class IntTypeController extends Controller
{
    /**
     * Mostrar lista de tipos de interacción
     */
    public function index()
    {
        $types = IntType::paginate(10);
        return view('interactions.types.index', compact('types'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('interactions.types.create');
    }

    /**
     * Guardar un nuevo tipo
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        IntType::create([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.types.index')
            ->with('success', 'Tipo de interacción creado exitosamente');
    }

    /**
     * Mostrar un tipo específico
     */
    public function show($id)
    {
        $type = IntType::findOrFail($id);
        return view('interactions.types.show', compact('type'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $type = IntType::findOrFail($id);
        return view('interactions.types.edit', compact('type'));
    }

    /**
     * Actualizar un tipo
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = IntType::findOrFail($id);
        $type->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('interactions.types.index')
            ->with('success', 'Tipo de interacción actualizado correctamente');
    }

    /**
     * Eliminar un tipo
     */
    public function destroy($id)
    {
        $type = IntType::findOrFail($id);
        $type->delete();

        return redirect()
            ->route('interactions.types.index')
            ->with('success', 'Tipo de interacción eliminado correctamente');
    }
}
