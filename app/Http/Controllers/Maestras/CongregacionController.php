<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\claseCongregacion;
use App\Models\Maestras\Congregacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CongregacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ... (toda la lógica de búsqueda se queda igual)
        $query = Congregacion::query()->with('claseCongregacion');
        $busqueda = trim($request->input('search'));

        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'LIKE', "%{$busqueda}%")
                ->orWhere('codigo', 'LIKE', "%{$busqueda}%")
                ->orWhere('municipio', 'LIKE', "%{$busqueda}%")
                ->orWhere('pastor', 'LIKE', "%{$busqueda}%");
            });
        }

<<<<<<< HEAD:app/Http/Controllers/Creditos/CongregacionController.php
        // CAMBIO: Simplemente elimina .withQueryString() de esta línea
        $congregaciones = $query->paginate(5); 

        return view('creditos.congregaciones.index', compact('congregaciones'));
=======
        return view('maestras.congregaciones.index', compact('congregacion'));
>>>>>>> main:app/Http/Controllers/Maestras/CongregacionController.php
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $claselist = claseCongregacion::all();
        return view('maestras.congregaciones.create',compact('claselist'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:255|unique:Congregaciones,codigo',
            'nombre' => 'required|string|max:255',
            'estado' => 'required|in:1,0', // asumes tinyint: 1 = Activo, 0 = Inactivo
            'clase' => 'nullable|integer',
            'municipio' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:45',
            'celular' => 'nullable|string|max:45',
            'distrito' => 'nullable|string|max:10',
            'apertura' => 'nullable|string|max:45',
            'cierre' => 'nullable|string|max:45',
            'observacion' => 'nullable|string|max:255',
            'pastor' => 'nullable|integer',
        ]);

        Congregacion::create($request->all());

        return redirect()->route('maestras.congregacion.index')
                         ->with('success', '¡Congregación creada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
<<<<<<< HEAD:app/Http/Controllers/Creditos/CongregacionController.php
     *///
 public function edit($codigo)
    {
        // 1. Buscamos la congregación que se va a editar.
        $congregacion = Congregacion::findOrFail($codigo);
        
        // 2. [NUEVO] Obtenemos TODAS las clases de congregación para llenar el menú desplegable.
        $clases = ClaseCongregacion::all();
        
        // 3. [MODIFICADO] Pasamos AMBOS datos a la vista: la congregación y la lista de clases.
        return view('creditos.congregaciones.edit', compact('congregacion', 'clases'));
=======
     */
    public function edit($congregacionid)
    {
        $congregacion = Congregacion::where('codigo', $congregacionid)->first();
        return view('maestras.congregaciones.edit', compact('congregacion'));
>>>>>>> main:app/Http/Controllers/Maestras/CongregacionController.php
    }

    /**
     * Actualiza la congregación en la base de datos.
     */
    public function update(Request $request, $codigo)
    {
        $congregacion = Congregacion::findOrFail($codigo);

        $validatedData = $request->validate([
            'nombre'      => 'required|string|max:255',
            'pastor'      => 'required|string|max:100',
            'clase'       => 'required|string',
            'estado'      => 'required|string|in:A,I', // 'in:A,I' es una mejor validación
            'municipio'   => 'required|string',
            // ...el resto de tus validaciones
            'observacion' => 'nullable|string',
        ]);

        /**
         * ¡AQUÍ ESTÁ LA MAGIA!
         * Traducimos el valor de 'estado' antes de guardarlo.
         */
        $validatedData['estado'] = ($validatedData['estado'] == 'A') ? 1 : 0;

        $congregacion->update($validatedData);

<<<<<<< HEAD:app/Http/Controllers/Creditos/CongregacionController.php
        return redirect()->route('creditos.congregaciones.index')
                        ->with('success', '¡AHORA SÍ! ¡Congregación actualizada exitosamente!');
=======
        return redirect()->route('maestras.congregacion.index')
                         ->with('success', '¡Congregación actualizada exitosamente!');
>>>>>>> main:app/Http/Controllers/Maestras/CongregacionController.php
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $codigo)
    {
        //
    }
}
