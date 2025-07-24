<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\maeTerceros;
use Illuminate\Http\Request;

class MaeTercerosController extends Controller
{
    /**
     * Muestra una lista de los terceros.
     */
    public function index(Request $request)
    {
        // Opcional: filtro por nombre
        $search = $request->input('search');

        $terceros = maeTerceros::query()
            ->when($search, function ($query, $search) {
                $query->where('nom_ter', 'like', "%{$search}%")
                      ->orWhere('razon_soc', 'like', "%{$search}%")
                      ->orWhere('cod_ter', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10); // Ajusta la paginación si deseas más o menos

        return view('maestras.terceros.index', compact('terceros', 'search'));
    }

    public function edit(maeTerceros $tercero)
    {
        return view('maestras.terceros.edit', compact('tercero'));
    }

public function update(Request $request, MaeTerceros $tercero)
{
    // dd($request->all());

    $tercero->update($request->all());

    return redirect()->route('maestras.terceros.index')
                    ->with('success', 'Tercero actualizado correctamente.');
}




}
