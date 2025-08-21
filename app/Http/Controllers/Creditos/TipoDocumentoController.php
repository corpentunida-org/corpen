<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\TipoDocumento;
use App\Models\Creditos\Etapa;
use App\Http\Requests\StoreTipoDocumentoRequest;
use App\Http\Requests\UpdateTipoDocumentoRequest;

class TipoDocumentoController extends Controller
{
    public function index()
    {
        $tiposDocumento = TipoDocumento::with('etapa')->latest()->paginate(15);
        return view('creditos.tipos_documento.index', compact('tiposDocumento'));
    }

    public function create()
    {
        $etapas = Etapa::all();
        return view('creditos.tipos_documento.create', compact('etapas'));
    }

    public function store(StoreTipoDocumentoRequest $request)
    {
        TipoDocumento::create($request->validated());
        return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento creado exitosamente.');
    }

    public function show(TipoDocumento $tipo_documento)
    {
        $tipo_documento->load('etapa');
        return view('creditos.tipos_documento.show', compact('tipo_documento'));
    }

    public function edit(TipoDocumento $tipo_documento)
    {
        $etapas = Etapa::all();
        return view('creditos.tipos_documento.edit', compact('tipo_documento', 'etapas'));
    }

    public function update(UpdateTipoDocumentoRequest $request, TipoDocumento $tipo_documento)
    {
        $tipo_documento->update($request->validated());
        return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento actualizado exitosamente.');
    }

    public function destroy(TipoDocumento $tipo_documento)
    {
        // BUENA PRÁCTICA: Verificar si el tipo de documento está en uso.
        // Esto es posible gracias a la relación documentos() que añadimos al modelo.
        if ($tipo_documento->documentos()->count() > 0) {
            return redirect()->route('tipos_documento.index')->with('error', 'No se puede eliminar porque está siendo utilizado por documentos existentes.');
        }

        $tipo_documento->delete();
        return redirect()->route('tipos_documento.index')->with('success', 'Tipo de documento eliminado exitosamente.');
    }
}