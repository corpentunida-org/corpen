<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoEmpleado;
use Illuminate\Http\Request;

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
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('archivo.empleado.index', compact('empleados', 'search'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $empleado = null;
        return view('archivo.empleado.create', compact('empleado'));
    }

    /**
     * Guardar un nuevo empleado.
     */
    public function store(Request $request)
    {
        $request->validate([
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
        ]);

        GdoEmpleado::create($request->all());

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    /**
     * Mostrar un empleado específico.
     */
    public function show(GdoEmpleado $empleado)
    {
        return view('archivo.empleado.show', compact('empleado'));
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
        $request->validate([
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
        ]);

        $empleado->update($request->all());

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Eliminar un empleado.
     */
    public function destroy(GdoEmpleado $empleado)
    {
        $empleado->delete();

        return redirect()
            ->route('archivo.empleado.index')
            ->with('success', 'Empleado eliminado correctamente.');
    }
}
