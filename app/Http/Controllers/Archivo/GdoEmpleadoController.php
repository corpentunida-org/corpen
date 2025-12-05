<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoEmpleado;
use App\Models\Archivo\GdoCargo;
use App\Models\Archivo\GdoArea;
use App\Models\Archivo\GdoDocsEmpleados;
use Illuminate\Http\Request;

class GdoEmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $empleadoId = $request->input('id');
        
        // Obtener la lista de empleados
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
        
        // Obtener el empleado seleccionado si se proporciona un ID
        $empleadoSeleccionado = null;
        $documentosDelEmpleado = null;
        
        if ($empleadoId) {
            $empleadoSeleccionado = GdoEmpleado::with('cargo')->find($empleadoId);
            
            if ($empleadoSeleccionado) {
                $documentosDelEmpleado = GdoDocsEmpleados::where('empleado_id', $empleadoSeleccionado->cedula)
                                                    ->with('tipoDocumento')
                                                    ->latest('fecha_subida')
                                                    ->paginate(10, ['*'], 'docs_page');
            }
        }
        
        return view('archivo.empleado.index', [
            'empleados' => $empleados,
            'search' => $search,
            'empleadoSeleccionado' => $empleadoSeleccionado,
            'documentosDelEmpleado' => $documentosDelEmpleado,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'cedula' => 'required|string|max:20|unique:gdo_empleados,cedula',
            'nombre1' => 'required|string|max:50',
            'nombre2' => 'nullable|string|max:50',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'nacimiento' => 'nullable|date',
            'lugar' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F',
            'correo_personal' => 'nullable|email|max:100',
            'celular_personal' => 'nullable|string|max:20',
            'celular_acudiente' => 'nullable|string|max:20',
            'ubicacion_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cargo_id' => 'nullable|exists:gdo_cargos,id',
        ]);
        
        // Manejo de la foto
        if ($request->hasFile('ubicacion_foto')) {
            $path = $request->file('ubicacion_foto')->store('empleados_fotos', 'public');
            $validated['ubicacion_foto'] = $path;
        }
        
        // Crear el empleado
        $empleado = GdoEmpleado::create($validated);
        
        // Si es una petición AJAX, devolver respuesta JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente.',
                'empleado' => $empleado->load('cargo')
            ]);
        }
        
        return redirect()->route('archivo.empleado.index', ['id' => $empleado->id])
            ->with('success', 'Empleado creado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GdoEmpleado $empleado)
    {
        // Validación de datos
        $validated = $request->validate([
            'cedula' => 'required|string|max:20|unique:gdo_empleados,cedula,'.$empleado->id,
            'nombre1' => 'required|string|max:50',
            'nombre2' => 'nullable|string|max:50',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'nacimiento' => 'nullable|date',
            'lugar' => 'nullable|string|max:100',
            'sexo' => 'nullable|in:M,F',
            'correo_personal' => 'nullable|email|max:100',
            'celular_personal' => 'nullable|string|max:20',
            'celular_acudiente' => 'nullable|string|max:20',
            'ubicacion_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cargo_id' => 'nullable|exists:gdo_cargos,id',
        ]);
        
        // Manejo de la foto
        if ($request->hasFile('ubicacion_foto')) {
            // Eliminar foto anterior si existe
            if ($empleado->ubicacion_foto) {
                //Storage::disk('public')->delete($empleado->ubicacion_foto);
            }
            
            $path = $request->file('ubicacion_foto')->store('empleados_fotos', 'public');
            $validated['ubicacion_foto'] = $path;
        }
        
        // Actualizar el empleado
        $empleado->update($validated);
        
        // Si es una petición AJAX, devolver respuesta JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado exitosamente.',
                'empleado' => $empleado->load('cargo')
            ]);
        }
        
        return redirect()->route('archivo.empleado.index', ['id' => $empleado->id])
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GdoEmpleado $empleado)
    {
        // Eliminar documentos asociados si es necesario
        GdoDocsEmpleados::where('empleado_id', $empleado->cedula)->delete();
        
        // Eliminar foto si existe
        if ($empleado->ubicacion_foto) {
            //Storage::disk('public')->delete($empleado->ubicacion_foto);
        }
        
        // Eliminar el empleado
        $empleado->delete();
        
        // Si es una petición AJAX, devolver respuesta JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Empleado eliminado exitosamente.',
                'redirect' => route('archivo.empleado.index')
            ]);
        }
        
        return redirect()->route('archivo.empleado.index')
            ->with('success', 'Empleado eliminado exitosamente.');
    }

    /**
     * Mostrar la foto de un empleado.
     */
    public function verFoto(GdoEmpleado $empleado)
    {
        // Lógica para mostrar la foto del empleado
        // ...
    }
}