<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archivo\GdoDocsEmpleados;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Archivo\GdoTipoDocumento;
use Illuminate\Support\Facades\Storage;

class GdoDocsEmpleadosController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = GdoDocsEmpleados::with(['empleado', 'tipoDocumento']);

        if ($search) {
            $query->where('ruta_archivo', 'like', "%{$search}%")
                  ->orWhere('observaciones', 'like', "%{$search}%")
                  ->orWhere('fecha_subida', 'like', "%{$search}%");
        }

        $collection = $query->orderBy('fecha_subida', 'desc')
                            ->paginate(10)
                            ->appends(['search' => $search]);

        return view('archivo.gdodocsempleados.index', [
            'gdodocsempleados' => $collection,
            'docsEmpleados'    => $collection,
            'documentos'       => $collection,
            'search'           => $search,
        ]);
    }

    public function create()
    {
        $empleados = GdoEmpleado::orderBy('apellido1')->get();
        $tiposDocumento = GdoTipoDocumento::orderBy('nombre')->get();

        return view('archivo.gdodocsempleados.create', [
            'empleados'        => $empleados,
            'tiposDocumento'   => $tiposDocumento,
            'docsEmpleados'    => null,
            'gdodocsempleados' => null,
            'documentos'       => null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id'        => 'nullable|exists:gdo_empleados,cedula',
            'tipo_documento_id'  => 'nullable|exists:gdo_tipo_documento,id',
            'archivo'            => 'nullable|file|max:10240',
            'ruta_archivo'       => 'nullable|file|max:10240',
            'fecha_subida'       => 'nullable|date',
            'observaciones'      => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['empleado_id', 'tipo_documento_id', 'observaciones']);
        $data['fecha_subida'] = $request->fecha_subida;

        if ($request->hasFile('archivo') || $request->hasFile('ruta_archivo')) {
            $file = $request->file('archivo') ?? $request->file('ruta_archivo');
            $data['ruta_archivo'] = $file->store('gestion/docsempleados'); // disco local por defecto
        }

        GdoDocsEmpleados::create($data);

        return redirect()->route('archivo.gdodocsempleados.index')
                         ->with('success', 'Documento creado correctamente.');
    }

    public function show(GdoDocsEmpleados $gdodocsempleado)
    {
        $gdodocsempleado->load(['empleado', 'tipoDocumento']);

        return view('archivo.gdodocsempleados.show', [
            'gdodocsempleado'   => $gdodocsempleado,
            'docsEmpleado'      => $gdodocsempleado,
            'docsEmpleadoSingle'=> $gdodocsempleado,
        ]);
    }

    public function edit(GdoDocsEmpleados $gdodocsempleado)
    {
        $empleados = GdoEmpleado::orderBy('apellido1')->get();
        $tiposDocumento = GdoTipoDocumento::orderBy('nombre')->get();

        return view('archivo.gdodocsempleados.edit', [
            'gdodocsempleado' => $gdodocsempleado,
            'docsEmpleado'    => $gdodocsempleado,
            'docsEmpleados'   => $gdodocsempleado,
            'empleados'       => $empleados,
            'tiposDocumento'  => $tiposDocumento,
        ]);
    }

    public function update(Request $request, GdoDocsEmpleados $gdodocsempleado)
    {
        $request->validate([
            'empleado_id'        => 'nullable|exists:gdo_empleados,cedula',
            'tipo_documento_id'  => 'nullable|exists:gdo_tipo_documento,id',
            'archivo'            => 'nullable|file|max:10240',
            'ruta_archivo'       => 'nullable|file|max:10240',
            'fecha_subida'       => 'nullable|date',
            'observaciones'      => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['empleado_id', 'tipo_documento_id', 'observaciones']);
        $data['fecha_subida'] = $request->fecha_subida;

        if ($request->hasFile('archivo') || $request->hasFile('ruta_archivo')) {
            if ($gdodocsempleado->ruta_archivo && Storage::disk('public')->exists($gdodocsempleado->ruta_archivo)) {
                Storage::disk('public')->delete($gdodocsempleado->ruta_archivo);
            }
            $file = $request->file('archivo') ?? $request->file('ruta_archivo');
            $data['ruta_archivo'] = $file->store('gestion/docsempleados'); // disco local por defecto
        }

        $gdodocsempleado->update($data);

        return redirect()->route('archivo.gdodocsempleados.index')
                         ->with('success', 'Documento actualizado correctamente.');
    }

    public function destroy(GdoDocsEmpleados $gdodocsempleado)
    {
        if ($gdodocsempleado->ruta_archivo && Storage::disk('public')->exists($gdodocsempleado->ruta_archivo)) {
            Storage::disk('public')->delete($gdodocsempleado->ruta_archivo);
        }

        $gdodocsempleado->delete();

        return redirect()->route('archivo.gdodocsempleados.index')
                         ->with('success', 'Documento eliminado correctamente.');
    }

public function verArchivo($id)
{
    $gdodocsempleado = GdoDocsEmpleados::findOrFail($id);

    // Archivo privado en storage/app/...
    $ruta = $gdodocsempleado->ruta_archivo;

    if (!$ruta || !Storage::exists($ruta)) {
        abort(404, 'Archivo no encontrado.');
    }

    // Esto sirve el archivo para verlo en el navegador
    return response()->file(storage_path('app/' . $ruta));
}




public function download($id)
{
    $gdodocsempleado = GdoDocsEmpleados::findOrFail($id);

    if (!$gdodocsempleado->ruta_archivo || !Storage::exists($gdodocsempleado->ruta_archivo)) {
        abort(404, 'Archivo no encontrado.');
    }

    return response()->download(storage_path('app/' . $gdodocsempleado->ruta_archivo));
}




}
