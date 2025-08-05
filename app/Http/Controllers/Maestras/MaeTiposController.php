<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeTipos;
use Illuminate\Http\Request;

class MaeTiposController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Inicia la consulta con tu modelo correcto
        $query = MaeTipos::query();

        // 2. Lógica de Búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('codigo', 'like', "%{$searchTerm}%")
                  ->orWhere('nombre', 'like', "%{$searchTerm}%")
                  ->orWhere('grupo', 'like', "%{$searchTerm}%");
            });
        }

        // 3. Lógica de Filtro por Estado
        if ($request->filled('status')) {
            $query->where('activo', $request->input('status'));
        }

        // 4. Lógica de Ordenamiento Dinámico
        $sortColumn = $request->get('sort', 'orden');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);

        if ($sortColumn !== 'nombre') {
            $query->orderBy('nombre', 'asc');
        }

        // 5. Paginación (VERSIÓN COMPATIBLE)
        // Usamos appends() para mantener los filtros y el orden al paginar.
        $tipos = $query->paginate(15)->appends($request->query());

        // 6. Devolver la vista con los datos
        return view('maestras.tipos.index', compact('tipos'));
    }


    /**
     * Muestra el formulario para crear un nuevo tipo.
     */
    public function create()
    {
        return view('maestras.tipos.create');
    }

    /**
     * Guarda un nuevo tipo en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:MaeTipos,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'grupo' => 'nullable|string|max:50',
            'categoria' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
        ]);

        // Manejar el checkbox 'activo'
        $validated['activo'] = $request->has('activo') ? 1 : 0;
        
        MaeTipos::create($validated);

        return redirect()->route('maestras.tipos.index')
                         ->with('success', 'Tipo creado correctamente.');
    }

    /**
     * Muestra los detalles de un tipo específico.
     */
    // En MaeTiposController.php
    public function show(MaeTipos $tipo)
    {
        // ¡AÑADE ESTA LÍNEA!
        // Esto carga el conteo de la relación que definiste en el modelo.
        $tipo->loadCount('maeTerceros');

        return view('maestras.tipos.show', compact('tipo'));
    }

    /**
     * Muestra el formulario para editar un tipo existente.
     */
    public function edit(MaeTipos $tipo)
    {
        return view('maestras.tipos.edit', compact('tipo'));
    }

    /**
     * Actualiza un tipo existente en la base de datos.
     */
    public function update(Request $request, MaeTipos $tipo)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:10|unique:MaeTipos,codigo,' . $tipo->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'grupo' => 'nullable|string|max:50',
            'categoria' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
        ]);

        // Manejar el checkbox 'activo'
        $validated['activo'] = $request->has('activo') ? 1 : 0;

        $tipo->update($validated);

        return redirect()->route('maestras.tipos.index')
                         ->with('success', 'Tipo actualizado correctamente.');
    }

    /**
     * Elimina un tipo de la base de datos (soft delete).
     */
    public function destroy(MaeTipos $tipo)
    {
        $tipo->delete();
        
        return redirect()->route('maestras.tipos.index')
                         ->with('success', 'Tipo eliminado correctamente.');
    }
}