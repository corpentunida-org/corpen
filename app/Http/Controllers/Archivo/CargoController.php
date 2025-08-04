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
        // Empezamos una consulta base
        $query = Cargo::query();

        // Si hay un término de búsqueda en la URL (?search=...), lo aplicamos
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('nombre_cargo', 'LIKE', "%{$searchTerm}%");
            // Puedes añadir más campos a la búsqueda si quieres
            // ->orWhere('correo_corporativo', 'LIKE', "%{$searchTerm}%");
        }

        // Ordenamos por el más reciente y paginamos el resultado de la consulta
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

    // 2. PREPARAMOS LOS DATOS DE TEXTO
    // Tomamos todos los datos del formulario, EXCEPTO el archivo.
    $data = $request->except('manual_funciones');

    // 3. PROCESAMOS EL ARCHIVO (SI EXISTE)
    // Preguntamos: "¿El usuario subió un archivo llamado 'manual_funciones'?"
    if ($request->hasFile('manual_funciones')) {
        
        // Si la respuesta es SÍ:
        // a. Guárdalo en la carpeta 'archivo/cargo' dentro de nuestro disco 'public'.
        // b. La función 'store' nos devuelve la ruta donde lo guardó (ej: "archivo/cargo/xyz.pdf").
        $rutaDelArchivo = $request->file('manual_funciones')->store('archivo/cargo', 'public');
        
        // c. Añadimos esa ruta (que es texto) a nuestros datos.
        $data['manual_funciones'] = $rutaDelArchivo;
    }

    // 4. GUARDAMOS EN LA BASE DE DATOS
    // Ahora sí, creamos el registro del Cargo con los datos correctos.
    // El campo 'manual_funciones' ahora contiene la RUTA, no el archivo.
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

    // 2. PREPARAMOS LOS DATOS DE TEXTO
    $data = $request->except('manual_funciones');

    // 3. PROCESAMOS EL NUEVO ARCHIVO (SI EXISTE)
    if ($request->hasFile('manual_funciones')) {

        // PASO EXTRA: Borrar el archivo anterior para no acumular basura.
        // Preguntamos: "¿Este cargo ya tenía un manual guardado?"
        if ($cargo->manual_funciones) {
            // Si la respuesta es SÍ, bórralo de nuestro disco 'public'.
            Storage::disk('public')->delete($cargo->manual_funciones);
        }

        // Ahora, guardamos el archivo nuevo, igual que en 'store'.
        $rutaDelArchivo = $request->file('manual_funciones')->store('archivo/cargo', 'public');
        $data['manual_funciones'] = $rutaDelArchivo;
    }

    // 4. ACTUALIZAMOS LA BASE DE DATOS
    $cargo->update($data);

    return redirect()->route('archivo.cargo.index')->with('success', 'Cargo actualizado.');
}

    public function destroy(Cargo $cargo)
    {
        // También borramos el archivo si el cargo se elimina
        if ($cargo->manual_funciones) {
            Storage::disk('public')->delete($cargo->manual_funciones);
        }
        
        $cargo->delete();
        // Corregido a singular
        return redirect()->route('archivo.cargo.index')->with('success', 'Cargo eliminado.');
    }
}
