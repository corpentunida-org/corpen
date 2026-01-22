<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMarca;
use App\Models\Inventario\InvBodega;
use App\Models\Inventario\InvSubgrupo;
use App\Models\Inventario\InvEstado;
use App\Models\User; // Para asignar usuarios
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    // Listado (Almacén de Activos)
    public function index(Request $request)
    {
        // Buscador básico incluido
        $query = InvActivo::with(['marca', 'bodega', 'estado', 'usuarioAsignado']);
        
        if($request->has('search')){
            $query->where('nombre', 'like', '%'.$request->search.'%')
                  ->orWhere('codigo_activo', 'like', '%'.$request->search.'%')
                  ->orWhere('serial', 'like', '%'.$request->search.'%');
        }

        $activos = $query->paginate(10);
        return view('inventario.activos.index', compact('activos'));
    }

    public function create()
    {
        $marcas = InvMarca::all();
        $bodegas = InvBodega::all();
        $subgrupos = InvSubgrupo::all();
        $estados = InvEstado::all();
        $municipios = \DB::table('mae_municipios')->get(); // Asumiendo tabla externa
        
        return view('inventario.activos.create', compact('marcas', 'bodegas', 'subgrupos', 'estados', 'municipios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'codigo_activo' => 'required|unique:inv_activos',
            'id_InvSubGrupos' => 'required',
            'id_InvMarcas' => 'required',
            'id_InvBodegas' => 'required',
            'id_MaeMunicipios' => 'required',
        ]);

        $data = $request->all();
        $data['id_usersRegistro'] = auth()->id(); // Quién lo crea
        
        InvActivo::create($data);

        return redirect()->route('activos.index')->with('success', 'Activo creado correctamente');
    }

    // Hoja de Vida del Activo (Historial completo)
    public function show($id)
    {
        $activo = InvActivo::with([
            'detalleCompra.compra', // Para ver cuándo se compró
            'usuarioAsignado',
            'mantenimientos', // Relación hasMany que agregaremos
            'movimientos'     // Relación hasMany (a través del detalle)
        ])->findOrFail($id);

        return view('inventario.activos.show', compact('activo'));
    }

    public function edit($id)
    {
        $activo = InvActivo::findOrFail($id);
        // Cargar listas (mismas que en create)
        $marcas = InvMarca::all();
        $bodegas = InvBodega::all();
        $subgrupos = InvSubgrupo::all();
        
        return view('inventario.activos.edit', compact('activo', 'marcas', 'bodegas', 'subgrupos'));
    }

    public function update(Request $request, $id)
    {
        $activo = InvActivo::findOrFail($id);
        $activo->update($request->all());
        return redirect()->route('activos.index')->with('success', 'Activo actualizado');
    }

    public function destroy($id)
    {
        InvActivo::destroy($id);
        return redirect()->route('activos.index')->with('success', 'Activo eliminado');
    }
}