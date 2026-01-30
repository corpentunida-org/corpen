<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCategoriaDocumento;
use App\Models\Archivo\GdoTipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GdoTipoDocumentoController extends Controller
{
    /**
     * Listado de tipos de documentos con filtros avanzados.
     */
    public function index(Request $request)
    {
        // 1. Captura de inputs de filtros
        $search = $request->input('search');
        $categoriaId = $request->input('categoria_id');

        // 2. Query optimizada con Eager Loading y Joins para ordenamiento
        $tipos = GdoTipoDocumento::with('categoria')
            ->leftJoin('gdo_categoria_documento', 'gdo_tipo_documento.categoria_documento_id', '=', 'gdo_categoria_documento.id')
            ->select('gdo_tipo_documento.*')
            
            // Filtro de búsqueda (Nombre del tipo o Nombre de la categoría)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('gdo_tipo_documento.nombre', 'like', "%{$search}%")
                      ->orWhere('gdo_categoria_documento.nombre', 'like', "%{$search}%");
                });
            })
            
            // Filtro específico por ID de categoría
            ->when($categoriaId, function($query) use ($categoriaId) {
                $query->where('gdo_tipo_documento.categoria_documento_id', $categoriaId);
            })
            
            // Ordenamiento corporativo: Categorías nulas al final, luego alfabético
            ->orderByRaw('gdo_categoria_documento.nombre IS NULL ASC')
            ->orderBy('gdo_categoria_documento.nombre', 'asc')
            ->orderBy('gdo_tipo_documento.nombre', 'asc')
            ->paginate(15)
            ->appends($request->all());

        // 3. Datos para el selector de filtros en la vista
        $todasCategorias = GdoCategoriaDocumento::orderBy('nombre')->get();

        return view('archivo.gdotipodocumento.index', compact('tipos', 'todasCategorias'));
    }

    /**
     * Formulario de creación.
     */
    public function create()
    {
        $tipoDocumento = new GdoTipoDocumento();
        $categorias = GdoCategoriaDocumento::orderBy('nombre')->get();
        
        return view('archivo.gdotipodocumento.create', compact('tipoDocumento', 'categorias'));
    }

    /**
     * Almacenamiento con validación estricta.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre',
            'categoria_documento_id' => 'nullable|exists:gdo_categoria_documento,id',
        ]);

        GdoTipoDocumento::create($validated);

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Tipo de documento registrado exitosamente.');
    }

    /**
     * Vista de detalle (Show).
     */
    public function show(GdoTipoDocumento $tipoDocumento)
    {
        // Cargamos el conteo de documentos reales vinculados para la UX del Show
        $tipoDocumento->loadCount('documentos');
        
        return view('archivo.gdotipodocumento.show', compact('tipoDocumento'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(GdoTipoDocumento $tipoDocumento)
    {
        $categorias = GdoCategoriaDocumento::orderBy('nombre')->get();
        return view('archivo.gdotipodocumento.edit', compact('tipoDocumento', 'categorias'));
    }

    /**
     * Actualización de datos.
     */
    public function update(Request $request, GdoTipoDocumento $tipoDocumento)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre,' . $tipoDocumento->id,
            'categoria_documento_id' => 'nullable|exists:gdo_categoria_documento,id',
        ]);

        $tipoDocumento->update($validated);

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Información actualizada correctamente.');
    }

    /**
     * Eliminación con protección de integridad.
     */
    public function destroy(GdoTipoDocumento $tipoDocumento)
    {
        // UX Pro: No permitir borrar si tiene documentos reales asociados
        if ($tipoDocumento->documentos()->exists()) {
            return redirect()
                ->route('archivo.gdotipodocumento.index')
                ->with('error', 'No se puede eliminar: Existen archivos físicos vinculados a este tipo.');
        }

        $tipoDocumento->delete();

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Registro eliminado del sistema.');
    }
}