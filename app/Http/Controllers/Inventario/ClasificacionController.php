<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvGrupo;
use App\Models\Inventario\InvLinea;
use App\Models\Inventario\InvTipo;
use Illuminate\Http\Request;

class ClasificacionController extends Controller
{
    public function index()
    {
        // Listamos subgrupos con sus padres
        $subgrupos = InvSubgrupo::with(['grupo', 'linea', 'tipo'])->paginate(10);
        
        // Listas para el modal de creación
        $grupos = InvGrupo::all();
        $lineas = InvLinea::all();
        $tipos = InvTipo::all();

        return view('inventario.catalogos.clasificacion.index', compact('subgrupos', 'grupos', 'lineas', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'id_InvGrupos' => 'required',
            'id_InvLineas' => 'required',
            'id_InvTipos' => 'required',
        ]);

        InvSubgrupo::create($request->all());
        return back()->with('success', 'Subgrupo clasificado correctamente');
    }

    public function destroy($id)
    {
        InvSubgrupo::destroy($id);
        return back()->with('success', 'Eliminado');
    }
}