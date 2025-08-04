<?php

namespace App\Http\Controllers\Archivo;

use App\Http\Controllers\Controller;
use App\Models\Archivo\Cargo;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage; // <--- Archivo

class CargoController extends Controller
{
        
    public function index(Request $request)
    {
        $query = Cargo::query();

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('nombre_cargo', 'LIKE', "%{$searchTerm}%");
        }

        $cargos = $query->latest()->paginate(10);

        return view('archivo.cargo.index', compact('cargos'));
    }

    public function create()
    {
        return view('archivo.cargo.create');
    }

    public function store(Request $request)
    {
        // 1. CAMBIAMOS LA VALIDACIÓN
        $request->validate([
            'nombre_cargo'         => 'required|string|max:255',
            'salario_base'         => 'nullable|numeric|min:0',
            'jornada'              => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo'  => 'nullable|string|max:50',
            'ext_corporativo'      => 'nullable|string|max:20',
            'correo_corporativo'   => 'nullable|email|max:255',
            'gmail_corporativo'    => 'nullable|email|max:255',
            // Le decimos: "El manual es opcional, pero si viene, DEBE SER un archivo PDF y pesar máximo 2MB"
            'manual_funciones'     => 'nullable|file|mimes:pdf|max:2048', 
            'empleado_cedula'      => 'nullable|string|max:50',
            'estado'               => 'nullable|boolean',
            'observacion'          => 'nullable|string',
        ]);

        $data = $request->except('manual_funciones');

        if ($request->hasFile('manual_funciones')) {
            // <-- CAMBIO CLAVE: Guardamos en la carpeta privada 'gestion/cargos', sin 'public'.
            $rutaDelArchivo = $request->file('manual_funciones')->store('gestion/cargos');
            $data['manual_funciones'] = $rutaDelArchivo;
        }

        Cargo::create($data);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo creado correctamente.');
    }

    public function show(Cargo $cargo)
    {
        return view('archivo.cargo.show', compact('cargo'));
    }

    public function edit(Cargo $cargo)
    {
        return view('archivo.cargo.edit', compact('cargo'));
    }

    public function update(Request $request, Cargo $cargo)
    {
        // 1. LA VALIDACIÓN ES LA MISMA QUE EN 'store'
        $request->validate([
            'nombre_cargo' => 'required|string|max:255',
            'salario_base' => 'nullable|numeric',
            'jornada' => 'nullable|string|max:100',
            'telefono_corporativo' => 'nullable|string|max:50',
            'celular_corporativo' => 'nullable|string|max:50',
            'ext_corporativo' => 'nullable|string|max:20',
            'correo_corporativo' => 'nullable|email|max:255',
            'gmail_corporativo' => 'nullable|email|max:255',
            'manual_funciones' => 'nullable|file|mimes:pdf|max:2048', // Debe ser un archivo
            'empleado_cedula' => 'nullable|string|max:50',
            'estado' => 'nullable|boolean',
            'observacion' => 'nullable|string',
        ]);

        $data = $request->except('manual_funciones');

        if ($request->hasFile('manual_funciones')) {
            // <-- CAMBIO CLAVE: Borramos el archivo antiguo del disco por defecto (privado).
            if ($cargo->manual_funciones) {
                Storage::delete($cargo->manual_funciones);
            }

            // <-- CAMBIO CLAVE: Guardamos el nuevo archivo en la carpeta privada 'gestion/cargos'.
            $rutaDelArchivo = $request->file('manual_funciones')->store('gestion/cargos');
            $data['manual_funciones'] = $rutaDelArchivo;
        }

        $cargo->update($data);

        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo actualizado.');
    }

    public function destroy(Cargo $cargo)
    {
        if ($cargo->manual_funciones) {
            // <-- CAMBIO CLAVE: Borramos el archivo del disco por defecto (privado).
            Storage::delete($cargo->manual_funciones);
        }
        
        $cargo->delete();
        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo eliminado.');
    }

    // <-- CAMBIO CLAVE: Nuevo método "guardia de seguridad" para servir los archivos.
    public function verManual(Cargo $cargo)
    {
        // 1. Verificamos que el registro en la BD tenga una ruta de archivo.
        if (!$cargo->manual_funciones) {
            abort(404, 'El cargo no tiene un manual asociado.');
        }

        // 2. Verificamos que el archivo físico exista en nuestro almacenamiento privado.
        if (!Storage::exists($cargo->manual_funciones)) {
            abort(404, 'Archivo no encontrado en el servidor.');
        }

        // 3. Si todo está bien, le entregamos el archivo al navegador.
        return Storage::response($cargo->manual_funciones);
    }

}
