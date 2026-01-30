<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Archivo\GdoDocsEmpleados;
use App\Models\Archivo\GdoTipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Importar la clase Log para mejor manejo de errores

class GdoEmpleadoController extends Controller
{
    /**
     * Muestra la lista de empleados y el detalle del seleccionado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $empleadoId = $request->input('id');
        
        $empleados = GdoEmpleado::when($search, function ($query, $search) {
                $query->where('cedula', 'like', "%{$search}%")
                    ->orWhere('nombre1', 'like', "%{$search}%")
                    ->orWhere('nombre2', 'like', "%{$search}%")
                    ->orWhere('apellido1', 'like', "%{$search}%")
                    ->orWhere('apellido2', 'like', "%{$search}%");
            })
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->paginate(1)
            ->appends(['search' => $search]);
        
        $empleadoSeleccionado = null;
        $documentosDelEmpleado = null;
        
        if ($empleadoId) {
            // NOTA IMPORTANTE: Asegúrate de que el modelo GdoCargo tenga la relación 'gdoArea' definida.
            // Si no existe, o para evitar errores si un cargo no tiene área, cambia 'cargo.gdoArea' por 'cargo'.
            $empleadoSeleccionado = GdoEmpleado::with('cargo.gdoArea')->find($empleadoId);
            
            if ($empleadoSeleccionado) {
                $documentosDelEmpleado = GdoDocsEmpleados::where('empleado_id', $empleadoSeleccionado->cedula)
                    ->with('tipoDocumento')
                    ->latest('fecha_subida')
                    ->paginate(10, ['*'], 'docs_page');
            }
        }
        
        $tiposDocumento = GdoTipoDocumento::orderBy('nombre')->get();
        
        return view('archivo.empleado.index', compact(
            'empleados', 
            'search', 
            'empleadoSeleccionado', 
            'documentosDelEmpleado', 
            'tiposDocumento'
        ));
    }

    /**
     * Crea un nuevo empleado y sube su foto a S3.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cedula'           => 'required|string|max:20|unique:gdo_empleados,cedula',
            'nombre1'          => 'required|string|max:50',
            'nombre2'          => 'nullable|string|max:50',
            'apellido1'        => 'required|string|max:50',
            'apellido2'        => 'nullable|string|max:50',
            'nacimiento'       => 'nullable|date',
            'lugar'            => 'nullable|string|max:100',
            'sexo'             => 'nullable|in:M,F',
            'correo_personal'  => 'nullable|email|max:100',
            'celular_personal' => 'nullable|string|max:20',
            'celular_acudiente'=> 'nullable|string|max:20',
            'ubicacion_foto'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cargo_id'         => 'nullable|exists:gdo_cargos,id',
        ]);
        
        if ($request->hasFile('ubicacion_foto')) {
            $file = $request->file('ubicacion_foto');
            $filename = time() . '_' . Str::slug($request->cedula) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("archivo/empleados/fotos/" . now()->format('Y/m'), $filename, 's3');
            $validated['ubicacion_foto'] = $path;
        }
        
        $empleado = GdoEmpleado::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente.',
                'empleado' => $empleado->load('cargo')
            ]);
        }
        
        return redirect()->route('archivo.empleado.index', ['id' => $empleado->id]);
    }

    /**
     * Actualiza los datos de un empleado y su foto en S3.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Archivo\GdoEmpleado  $empleado
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, GdoEmpleado $empleado)
    {
        $validated = $request->validate([
            'cedula'             => 'required|string|max:20|unique:gdo_empleados,cedula,'.$empleado->id,
            'fecha_expedida'     => 'nullable|date', // Agregado
            'lugar_exp'          => 'nullable|string|max:100', // Agregado
            'nombre1'            => 'required|string|max:50',
            'nombre2'            => 'nullable|string|max:50',
            'apellido1'          => 'required|string|max:50',
            'apellido2'          => 'nullable|string|max:50',
            'nacimiento'         => 'nullable|date',
            'lugar'              => 'nullable|string|max:100',
            'sexo'               => 'nullable|in:M,F',
            'direccion_residencia' => 'nullable|string|max:255', // Agregado
            'entidad_eps'        => 'nullable|string|max:100', // Agregado
            'correo_personal'    => 'nullable|email|max:100',
            'celular_personal'   => 'nullable|string|max:20',
            'celular_acudiente'  => 'nullable|string|max:20',
            'ubicacion_foto'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cargo_id'           => 'nullable|exists:gdo_cargos,id',
        ]);
        
        if ($request->hasFile('ubicacion_foto')) {
            // Limpieza de S3
            if ($empleado->ubicacion_foto && Storage::disk('s3')->exists($empleado->ubicacion_foto)) {
                Storage::disk('s3')->delete($empleado->ubicacion_foto);
            }
            
            $file = $request->file('ubicacion_foto');
            $filename = time() . '_' . Str::slug($request->cedula) . '.' . $file->getClientOriginalExtension();
            // Carpeta organizada por año/mes para no saturar un solo directorio
            $path = $file->storeAs("archivo/empleados/fotos/" . now()->format('Y/m'), $filename, 's3');
            $validated['ubicacion_foto'] = $path;
        }
        
        // El método update solo tomará los campos que estén en el array $validated 
        // y que existan en el $fillable del modelo.
        $empleado->update($validated);
        
        return response()->json([
            'success' => true, 
            'message' => 'Perfil de ' . $empleado->nombre1 . ' actualizado correctamente.'
        ]);
    }

    /**
     * Elimina un empleado, su foto y todos sus documentos de S3 y la BD.
     *
     * @param  \App\Models\Archivo\GdoEmpleado  $empleado
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(GdoEmpleado $empleado)
    {
        // Borrar foto de perfil de S3
        if ($empleado->ubicacion_foto && Storage::disk('s3')->exists($empleado->ubicacion_foto)) {
            Storage::disk('s3')->delete($empleado->ubicacion_foto);
        }

        // Borrar archivos físicos de documentos adicionales de S3
        $documentos = GdoDocsEmpleados::where('empleado_id', $empleado->cedula)->get();
        foreach($documentos as $doc) {
            if ($doc->ruta_archivo && Storage::disk('s3')->exists($doc->ruta_archivo)) {
                Storage::disk('s3')->delete($doc->ruta_archivo);
            }
            $doc->delete(); // Eliminar registro de la BD
        }
        
        $empleado->delete(); // Eliminar al empleado
        
        return response()->json(['success' => true, 'message' => 'Empleado y todos sus archivos han sido eliminados.']);
    }

    /**
     * Genera una URL temporal para ver la foto de perfil del empleado.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function verFoto($id)
    {
        $empleado = GdoEmpleado::findOrFail($id);
        if (!$empleado->ubicacion_foto || !Storage::disk('s3')->exists($empleado->ubicacion_foto)) {
            // Devolver una imagen por defecto si no existe la foto
            return response()->file(public_path('assets/media/avatars/blank.png'));
        }
        // Redirigir a la URL temporal de S3
        return redirect(Storage::disk('s3')->temporaryUrl($empleado->ubicacion_foto, now()->addMinutes(20)));
    }

    /**
     * Sube un documento adicional para un empleado a S3.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDocumento(Request $request)
    {
        $request->validate([
            'empleado_id'       => 'required|exists:gdo_empleados,cedula',
            'tipo_documento_id' => 'required|exists:gdo_tipo_documento,id',
            'fecha_subida'      => 'required|date',
            'archivo'           => 'required|file|mimes:pdf,jpg,jpeg,png,docx|max:5120', 
        ]);

        try {
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $filename = 'DOC_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                
                // Ruta estructurada por cédula del empleado
                $path = $file->storeAs(
                    "archivo/empleados/documentos/{$request->empleado_id}", 
                    $filename, 
                    's3'
                );

                GdoDocsEmpleados::create([
                    'empleado_id'       => $request->empleado_id,
                    // CORRECCIÓN APLICADA: Se usa el nombre correcto del campo del formulario.
                    'tipo_documento_id' => $request->tipo_documento_id,
                    'fecha_subida'      => $request->fecha_subida,
                    'ruta_archivo'      => $path,
                ]);

                return response()->json(['success' => true, 'message' => 'Documento subido a AWS S3 con éxito.']);
            }
        } catch (\Exception $e) {
            // Mejor práctica: registrar el error real para depuración y mostrar un mensaje genérico al usuario.
            Log::error('Error subiendo documento a S3: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error en el servidor al subir el archivo.'], 500);
        }

        return response()->json(['success' => false, 'message' => 'No se detectó ningún archivo en la solicitud.'], 400);
    }

    /**
     * Genera una URL temporal para ver un documento en el navegador.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verDocumento($id)
    {
        $documento = GdoDocsEmpleados::findOrFail($id);
        
        if (!$documento->ruta_archivo || !Storage::disk('s3')->exists($documento->ruta_archivo)) {
            abort(404, 'Documento no encontrado en el almacenamiento.');
        }
        
        // Genera una URL temporal segura para ver el archivo en el navegador
        $url = Storage::disk('s3')->temporaryUrl(
            $documento->ruta_archivo, 
            now()->addMinutes(15), // 15 minutos es suficiente para ver
            [
                'ResponseContentType' => 'application/pdf', // Fuerza la vista previa en PDF si es posible
                'ResponseContentDisposition' => 'inline; filename="' . basename($documento->ruta_archivo) . '"'
            ]
        );
        
        return redirect($url);
    }

    /**
     * Genera una URL temporal para descargar un documento.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadDocumento($id)
    {
        $documento = GdoDocsEmpleados::with(['empleado', 'tipoDocumento'])->findOrFail($id);
        
        if (!$documento->ruta_archivo || !Storage::disk('s3')->exists($documento->ruta_archivo)) {
            abort(404, 'Documento no encontrado en el almacenamiento.');
        }
        
        // Genera un nombre de archivo descriptivo para el usuario
        $nombreArchivo = Str::slug($documento->tipoDocumento->nombre . '_' . $documento->empleado->nombre_completo) . '.' . pathinfo($documento->ruta_archivo, PATHINFO_EXTENSION);
        
        // Genera una URL temporal para la descarga
        $url = Storage::disk('s3')->temporaryUrl(
            $documento->ruta_archivo, 
            now()->addMinutes(15),
            [
                'ResponseContentDisposition' => 'attachment; filename="' . $nombreArchivo . '"'
            ]
        );
        
        return redirect($url);
    }

    /**
     * Elimina un documento individual de S3 y la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDocumento($id)
    {
        $documento = GdoDocsEmpleados::findOrFail($id);

        // Eliminar el archivo físico de S3
        if ($documento->ruta_archivo && Storage::disk('s3')->exists($documento->ruta_archivo)) {
            Storage::disk('s3')->delete($documento->ruta_archivo);
        }

        // Eliminar el registro de la base de datos
        $documento->delete();

        return response()->json(['success' => true, 'message' => 'Archivo eliminado correctamente.']);
    }
}