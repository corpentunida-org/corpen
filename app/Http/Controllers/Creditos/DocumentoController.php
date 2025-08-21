<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Documento;
use App\Models\Creditos\Credito;
use App\Models\Creditos\TipoDocumento;
use App\Http\Requests\StoreDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use Illuminate\Support\Facades\Storage; // ¡MUY IMPORTANTE para manejar archivos!

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with(['credito', 'tipoDocumento'])->latest()->paginate(15);
        return view('creditos.documentos.index', compact('documentos'));
    }

    public function create()
    {
        $creditos = Credito::all();
        $tiposDocumento = TipoDocumento::all();
        return view('creditos.documentos.create', compact('creditos', 'tiposDocumento'));
    }

    public function store(StoreDocumentoRequest $request)
    {
        $datosValidados = $request->validated();
        
        // 1. Manejar la subida del archivo
        if ($request->hasFile('archivo')) {
            // El método store() guarda el archivo con un nombre único y devuelve la ruta.
            $rutaArchivo = $request->file('archivo')->store('public/documentos');
            // 2. Guardamos la RUTA en el campo de la base de datos.
            $datosValidados['ruta_archivo'] = $rutaArchivo;
        }

        // 3. Creamos el registro en la base de datos.
        Documento::create($datosValidados);

        return redirect()->route('documentos.index')->with('success', 'Documento subido exitosamente.');
    }

    public function show(Documento $documento)
    {
        $documento->load(['credito', 'tipoDocumento']);
        return view('creditos.documentos.show', compact('documento'));
    }

    public function edit(Documento $documento)
    {
        $creditos = Credito::all();
        $tiposDocumento = TipoDocumento::all();
        return view('creditos.documentos.edit', compact('documento', 'creditos', 'tiposDocumento'));
    }

    public function update(UpdateDocumentoRequest $request, Documento $documento)
    {
        $datosValidados = $request->validated();

        // 1. Verificar si se está subiendo un nuevo archivo
        if ($request->hasFile('archivo')) {
            // 2. Borrar el archivo antiguo para no dejar basura en el servidor.
            if ($documento->ruta_archivo) {
                Storage::delete($documento->ruta_archivo);
            }

            // 3. Guardar el nuevo archivo y actualizar la ruta.
            $rutaArchivo = $request->file('archivo')->store('public/documentos');
            $datosValidados['ruta_archivo'] = $rutaArchivo;
        }

        $documento->update($datosValidados);

        return redirect()->route('documentos.index')->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Documento $documento)
    {
        // 1. Borrar el archivo físico del almacenamiento. ¡Paso crucial!
        if ($documento->ruta_archivo) {
            Storage::delete($documento->ruta_archivo);
        }

        // 2. Borrar el registro de la base de datos.
        $documento->delete();

        return redirect()->route('documentos.index')->with('success', 'Documento eliminado exitosamente.');
    }
}