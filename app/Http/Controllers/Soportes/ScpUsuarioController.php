<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpUsuario;
use App\Models\Maestras\maeTerceros;
use App\Models\User;
use Illuminate\Http\Request;

class ScpUsuarioController extends Controller
{
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    public function create()
    {
        //$terceros = maeTerceros::where("tip_prv","9")->get();
        $terceros = User::all();
        // PASAR una instancia vacía para que $usuario exista en la vista
        $usuario = new ScpUsuario();

        return view('soportes.usuarios.create', compact('terceros', 'usuario'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'cod_ter' => 'required|exists:MaeTerceros,cod_ter',
            'rol'     => 'nullable|string|max:255',
        ]);

        ScpUsuario::create($request->all());

        return redirect()->route('soportes.usuarios.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit($hash)
    {
        $terceros = maeTerceros::all();

        // Buscamos el usuario por hash
        $usuario = ScpUsuario::all()->first(fn($u) => md5($u->id . 'clave-secreta') === $hash);

        if (!$usuario) {
            abort(404); // si no se encuentra, lanzamos error
        }

        return view('soportes.usuarios.edit', [
            'usuario' => $usuario,
            'terceros' => $terceros
        ]);
    }

    public function update(Request $request, $hash)
    {
        // Buscamos el usuario por hash
        $scpUsuario = ScpUsuario::all()->first(fn($u) => md5($u->id . 'clave-secreta') === $hash);

        if (!$scpUsuario) {
            abort(404);
        }

        $request->validate([
            'cod_ter' => 'required|exists:MaeTerceros,cod_ter',
            'usuario' => 'required|string|max:255',
            'estado'  => 'required|in:Activo,Inactivo',
        ]);

        $scpUsuario->fill($request->only(['cod_ter', 'usuario', 'estado']));
        $scpUsuario->updated_at = now();
        $scpUsuario->save();

        return redirect()->route('soportes.usuarios.index')
                        ->with('success', 'Usuario actualizado correctamente');
    }



    public function destroy(ScpUsuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('soportes.usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}
