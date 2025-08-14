<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCategoriaDocumento; // 1. Importar el modelo de Categoría
use App\Models\Archivo\GdoTipoDocumento;
use Illuminate\Http\Request;

class GdoTipoDocumentoController extends Controller
{

    /**
     * Mostrar una lista de tipos de documentos, incluyendo los no categorizados.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = GdoTipoDocumento::with('categoria')
            // 1. Cambiamos a LEFT JOIN para incluir TODOS los tipos de documento.
            ->leftJoin('gdo_categoria_documento', 'gdo_tipo_documento.categoria_documento_id', '=', 'gdo_categoria_documento.id')
            // 2. Mantenemos el select() para evitar conflictos de columnas.
            ->select('gdo_tipo_documento.*')
            ->when($search, function($query, $search) {
                // La búsqueda sigue funcionando para ambos campos.
                $query->where('gdo_tipo_documento.nombre', 'like', "%{$search}%")
                      ->orWhere('gdo_categoria_documento.nombre', 'like', "%{$search}%");
            })
            // 3. Usamos una ordenación más robusta para manejar los nulos.
            //    Esto pone los tipos sin categoría (NULL) al principio de la lista.
            ->orderByRaw('gdo_categoria_documento.nombre IS NULL DESC')
            // 4. Luego ordena alfabéticamente por el nombre de la categoría (los que la tienen).
            ->orderBy('gdo_categoria_documento.nombre', 'asc')
            // 5. Y finalmente, por el nombre del tipo de documento.
            ->orderBy('gdo_tipo_documento.nombre', 'asc');

        $tipos = $query->paginate(15)->appends(['search' => $search]); // Aumenté un poco la paginación, siéntete libre de ajustarla.

        return view('archivo.gdotipodocumento.index', compact('tipos', 'search'));
    }

    /**
     * Mostrar el formulario para crear un nuevo tipo de documento.
     */
    public function create()
    {
        $tipoDocumento = new GdoTipoDocumento();
        // 3. Obtener todas las categorías para pasarlas al formulario
        $categorias = GdoCategoriaDocumento::orderBy('nombre')->get();
        return view('archivo.gdotipodocumento.create', compact('tipoDocumento', 'categorias'));
    }

    /**
     * Almacenar un nuevo tipo de documento en la base de datos.
     */
    public function store(Request $request)
    {
        // 4. Añadir validación para el campo de categoría
        $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre',
            'categoria_documento_id' => 'required|exists:gdo_categoria_documento,id',
        ]);

        // 5. Crear el registro incluyendo el ID de la categoría
        GdoTipoDocumento::create($request->all());

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    /**
     * Mostrar un tipo de documento específico.
     */
    public function show(GdoTipoDocumento $tipoDocumento)
    {
        // El 'route model binding' ya carga el tipo de documento.
        // La relación 'categoria' se cargará automáticamente si la usas en la vista.
        return view('archivo.gdotipodocumento.show', compact('tipoDocumento'));
    }

    /**
     * Mostrar el formulario para editar un tipo de documento.
     */
    public function edit(GdoTipoDocumento $tipoDocumento)
    {
        if (!$tipoDocumento->exists) {
            abort(404);
        }
        // 6. Obtener todas las categorías para pasarlas al formulario de edición
        $categorias = GdoCategoriaDocumento::orderBy('nombre')->get();
        return view('archivo.gdotipodocumento.edit', compact('tipoDocumento', 'categorias'));
    }

    /**
     * Actualizar un tipo de documento en la base de datos.
     */
    public function update(Request $request, GdoTipoDocumento $tipoDocumento)
    {
        // 7. Validar los datos, incluyendo la categoría
        $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre,' . $tipoDocumento->id,
            'categoria_documento_id' => 'required|exists:gdo_categoria_documento,id',
        ]);

        // 8. Actualizar el registro
        $tipoDocumento->update($request->all());

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Tipo de documento actualizado correctamente.');
    }

    /**
     * Eliminar un tipo de documento de la base de datos.
     */
    public function destroy(GdoTipoDocumento $tipoDocumento)
    {
        $tipoDocumento->delete();

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Tipo de documento eliminado correctamente.');
    }
}