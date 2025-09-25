<?php

namespace App\Http\Controllers;

use App\Models\Soportes\ScpUsuario;
use App\Models\Maestras\maeTerceros;
use Illuminate\Http\Request;


class ScpUsuarioController extends Controller
{
    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        // Traer usuarios con su maeTercero relacionado
        $usuarios = ScpUsuario::with('maeTercero')->get();
        return response()->json($usuarios);
    }

    /**
     * Guardar un nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'cod_ter' => 'required|exists:MaeTerceros,cod_ter',
            'rol'     => 'nullable|string|max:255',
        ]);

        $usuario = ScpUsuario::create($request->all());

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    /**
     * Mostrar un usuario en específico
     */
    public function show($id)
    {
        $usuario = ScpUsuario::with('maeTercero')->findOrFail($id);
        return response()->json($usuario);
    }

    /**
     * Actualizar un usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = ScpUsuario::findOrFail($id);

        $request->validate([
            'cod_ter' => 'sometimes|exists:MaeTerceros,cod_ter',
            'rol'     => 'nullable|string|max:255',
        ]);

        $usuario->update($request->all());

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }

    /**
     * Eliminar un usuario
     */
    public function destroy($id)
    {
        $usuario = ScpUsuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
