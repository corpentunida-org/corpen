<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMantenimiento;
use App\Models\Inventario\InvActivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    public function index()
    {
        $mantenimientos = InvMantenimiento::with('activo')->paginate(10);
        return view('inventario.mantenimientos.index', compact('mantenimientos'));
    }

    public function create()
    {
        // Solo activos que NO estén dados de baja
        $activos = InvActivo::where('id_Estado', '!=', 3)->get(); // Asumimos 3 = Baja
        return view('inventario.mantenimientos.create', compact('activos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_InvActivos' => 'required',
            'costo_mantenimiento' => 'required|numeric',
            'detalle' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // 1. Guardar registro histórico
            InvMantenimiento::create([
                'id_InvActivos' => $request->id_InvActivos,
                'costo_mantenimiento' => $request->costo_mantenimiento,
                'detalle' => $request->detalle,
                'id_usersRegistro' => auth()->id()
            ]);

            // 2. Actualizar estado del activo a "En Reparación" (id 4 por ejemplo)
            $activo = InvActivo::find($request->id_InvActivos);
            $activo->id_Estado = 4; // Asegúrate de saber qué ID es "En Reparación" en tu DB
            $activo->save();

            DB::commit();
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento registrado. Activo puesto en reparación.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}