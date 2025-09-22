<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Archivo\GdoDocsEmpleados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importante para manejar archivos

class GdoEmpleadoController extends Controller
{
    /**
     * Mostrar listado de empleados con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $empleados = GdoEmpleado::when($search, function ($query, $search) {
                $query->where('cedula', 'like', "%{$search}%")
                    ->orWhere('nombre1', 'like', "%{$search}%")
                    ->orWhere('nombre2', 'like', "%{$search}%")
                    ->orWhere('apellido1', 'like', "%{$search}%")
                    ->orWhere('apellido2', 'like', "%{$search}%");
            })
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->paginate(7)
            ->appends(['search' => $search]);

        return view('archivo.empleado.index', compact('empleados', 'search'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $empleado = new GdoEmpleado();
        return view('archivo.empleado.create', compact('empleado'));
    }

    /**
     * Guardar un nuevo empleado.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cedula' => 'required|string|max:20|unique:gdo_empleados,cedula',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'nombre1' => 'required|string|max:50',
            'nombre2' => 'nullable|string|max:50',
            'nacimiento' => 'nullable|date',
            'lugar' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F',
            'correo_personal' => 'nullable|email|max:100',
            'celular_personal' => 'nullable|string|max:20',
            'celular_acudiente' => 'nullable|string|max:20',
            // Se mantiene la validación de la imagen
            'ubicacion_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ubicacion_foto')) {
            // CORREGIDO: Guardar en el disco local (privado) en la carpeta especificada
            $path = $request->file('ubicacion_foto')->store('gestion/fotosempleados');
            $validatedData['ubicacion_foto'] = $path;
        }

        GdoEmpleado::create($validatedData);

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    /**
     * Mostrar un empleado específico.
     */
    public function show(GdoEmpleado $empleado)
    {
        $empleado->load('cargo');
        $documentosDelEmpleado = GdoDocsEmpleados::where('empleado_id', $empleado->cedula)
                                                ->with('tipoDocumento')
                                                ->latest('fecha_subida')
                                                ->paginate(10, ['*'], 'docs_page');

        return view('archivo.empleado.show', [
            'empleado' => $empleado,
            'documentosDelEmpleado' => $documentosDelEmpleado,
        ]);
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(GdoEmpleado $empleado)
    {
        return view('archivo.empleado.edit', compact('empleado'));
    }

    /**
     * Actualizar un empleado.
     */
    public function update(Request $request, GdoEmpleado $empleado)
    {
        
        $validatedData = $request->validate([
            'cedula' => 'required|string|max:20|unique:gdo_empleados,cedula,' . $empleado->id,
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'nombre1' => 'required|string|max:50',
            'nombre2' => 'nullable|string|max:50',
            'nacimiento' => 'nullable|date',
            'lugar' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F',
            'correo_personal' => 'nullable|email|max:100',
            'celular_personal' => 'nullable|string|max:20',
            'celular_acudiente' => 'nullable|string|max:20',
            'ubicacion_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('ubicacion_foto')) {
            // 1. CORREGIDO: Borrar la foto anterior del disco local.
            if ($empleado->ubicacion_foto) {
                Storage::delete($empleado->ubicacion_foto);
            }
            // 2. CORREGIDO: Guardar la nueva foto en el disco local.
            $path = $request->file('ubicacion_foto')->store('gestion/fotosempleados');
            $validatedData['ubicacion_foto'] = $path;
        }

        $empleado->update($validatedData);

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Eliminar un empleado.
     */
    public function destroy(GdoEmpleado $empleado)
    {
        // CORREGIDO: Borrar la foto asociada del disco local.
        if ($empleado->ubicacion_foto) {
            Storage::delete($empleado->ubicacion_foto);
        }

        $empleado->delete();

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }

    /**
     * NUEVO: Método para servir la imagen de forma segura.
     * Este método se encarga de leer el archivo del storage privado y enviarlo al navegador.
     *
     * @param  \App\Models\Archivo\GdoEmpleado  $empleado
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function verFoto(GdoEmpleado $empleado)
    {
        // Validamos que el empleado realmente tenga una foto y que el archivo exista en el disco.
        if (!$empleado->ubicacion_foto || !Storage::exists($empleado->ubicacion_foto)) {
            // Si no se encuentra, abortamos con un error 404 (Not Found).
            abort(404, 'Imagen no encontrada.');
        }

        // Retornamos la respuesta del archivo. Laravel se encarga de los encabezados correctos.
        return Storage::response($empleado->ubicacion_foto);
    }
}