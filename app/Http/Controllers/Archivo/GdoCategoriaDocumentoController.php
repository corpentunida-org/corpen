<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCategoriaDocumento;
use Illuminate\Http\Request;

class GdoCategoriaDocumentoController extends Controller
{
    /**
     * Mostrar un listado de categorías.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $query = GdoCategoriaDocumento::query();

        if ($searchTerm) {
            $query->where('nombre', 'LIKE', "%{$searchTerm}%");
        }

        $categorias = $query->latest()->paginate(10)->appends($request->query());
        return view('archivo.categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario para crear una nueva categoría.
     */
public function create()
{
    // 1. PRIMERO, le damos un valor a la variable $categoria.
    $categoria = new GdoCategoriaDocumento();

    // 2. SEGUNDO, ahora que la variable ya existe, la podemos mostrar con dd().
    /* dd($categoria); */

    // 3. El resto del código no se ejecutará.
    return view('archivo.categorias.create', compact('categoria'));
}

    /**
     * Guardar una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:gdo_categoria_documento,nombre',
        ]);

        GdoCategoriaDocumento::create($request->only('nombre'));

        return redirect()->route('archivo.categorias.index')
                         ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Muestra los detalles de una categoría (redirige a editar).
     */
    public function show(GdoCategoriaDocumento $categoria)
    {
        return redirect()->route('archivo.categorias.edit', $categoria);
    }

    /**
     * Muestra el formulario para editar una categoría.
     */
    public function edit(GdoCategoriaDocumento $categoria)
    {
        // Este método ya estaba correcto, pasa la categoría encontrada a la vista.
        return view('archivo.categorias.edit', compact('categoria'));
    }

    /**
     * Actualizar una categoría existente.
     */
    public function update(Request $request, GdoCategoriaDocumento $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:gdo_categoria_documento,nombre,' . $categoria->id,
        ]);

        $categoria->update($request->only('nombre'));

        return redirect()->route('archivo.categorias.index')
                         ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy(GdoCategoriaDocumento $categoria)
    {
        $categoria->delete();

        return redirect()->route('archivo.categorias.index')
                         ->with('success', 'Categoría eliminada exitosamente.');
    }
}