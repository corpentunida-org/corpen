<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpUsuario;
use App\Models\Maestras\MaeTerceros;
use Illuminate\Http\Request;

class ScpUsuarioController extends Controller
{
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    public function create()
    {
        $terceros = MaeTerceros::all();
        return view('soportes.usuarios.create', compact('terceros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cod_ter' => 'required|exists:mae_terceros,cod_ter',
            'rol'     => 'nullable|string|max:255',
        ]);

        ScpUsuario::create($request->all());

        return redirect()->route('soportes.usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(ScpUsuario $usuario)
    {
        $terceros = MaeTerceros::all();
        return view('soportes.usuarios.edit', compact('usuario', 'terceros'));
    }

    public function update(Request $request, ScpUsuario $usuario)
    {
        $request->validate([
            'cod_ter' => 'required|exists:mae_terceros,cod_ter',
            'rol'     => 'nullable|string|max:255',
        ]);

        $usuario->update($request->all());

        return redirect()->route('soportes.usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(ScpUsuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('soportes.usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
