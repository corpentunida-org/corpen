<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archivo\GdoTipoDocumento;

class GdoTipoDocumentoController extends Controller
{
    /**
     * Mostrar una lista de tipos de documentos.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $tipos = GdoTipoDocumento::when($search, function($query, $search) {
            $query->where('nombre', 'like', "%{$search}%");
        })
        ->orderBy('nombre')
        ->paginate(10)
        ->appends(['search' => $search]);

        return view('archivo.gdotipodocumento.index', compact('tipos', 'search'));
    }

    /**
     * Mostrar el formulario para crear un nuevo tipo de documento.
     */
    public function create()
    {
        $tipoDocumento = new GdoTipoDocumento();
        return view('archivo.gdotipodocumento.create', compact('tipoDocumento'));
    }

    /**
     * Almacenar un nuevo tipo de documento en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre',
        ]);

        GdoTipoDocumento::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()
            ->route('archivo.gdotipodocumento.index')
            ->with('success', 'Tipo de documento creado correctamente.');
    }

    /**
     * Mostrar un tipo de documento específico.
     */
    public function show(GdoTipoDocumento $tipoDocumento)
    {
        return view('archivo.gdotipodocumento.show', compact('tipoDocumento'));
    }

    /**
     * Mostrar el formulario para editar un tipo de documento.
     */
public function edit(GdoTipoDocumento $tipoDocumento)
{
    if (!$tipoDocumento->exists) {
        abort(404); // O redirigir con mensaje de error
    }

    return view('archivo.gdotipodocumento.edit', compact('tipoDocumento'));
}


    /**
     * Actualizar un tipo de documento en la base de datos.
     */
    public function update(Request $request, GdoTipoDocumento $tipoDocumento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:gdo_tipo_documento,nombre,' . $tipoDocumento->id,
        ]);

        $tipoDocumento->update([
            'nombre' => $request->nombre,
        ]);

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
