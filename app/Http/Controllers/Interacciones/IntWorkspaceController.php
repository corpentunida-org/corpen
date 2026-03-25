<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interacciones\IntWorkspace;

class IntWorkspaceController extends Controller
{
    /**
     * Caso de Uso 1: Crear un nuevo Workspace de Equipo.
     */
    public function store(Request $request)
    {
        // 1. Validar los campos manuales que envía el usuario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area_id' => 'required|exists:gdo_area,id', // Valida que el área exista
        ]);

        // 2. Crear el Workspace (status y timestamps son automáticos)
        $workspace = IntWorkspace::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'area_id' => $validated['area_id'],
            'status' => 'active', // Automático
        ]);

        // 3. Comentamos la respuesta JSON para que no se muestre texto crudo en la pantalla
        /*
        return response()->json([
            'message' => 'Workspace creado exitosamente.',
            'data' => $workspace
        ], 201);
        */

        // 4. Redireccionamos de vuelta a la vista del chat, 
        // pasándole el ID del workspace recién creado para que se abra automáticamente
        return redirect()->route('interactions.chat.index', ['workspace_id' => $workspace->id])
                         ->with('success', 'Workspace creado exitosamente.');
    }
}