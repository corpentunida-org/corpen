<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpTipo;
use Illuminate\Http\Request;

class ScpSubTipoController extends Controller
{
    // Lista de sub-tipos (opcional si todo va en el tablero)
    public function index()
    {
        return app(ScpTableroParametroController::class)->index();
    }

    // Formulario de creación
    public function create()
    {
        $tipos = ScpTipo::orderBy('nombre')->get();
        return view('soportes.subtipos.create', compact('tipos'));
    }

    // Guardar nuevo sub-tipo
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'scp_tipo_id' => 'required|exists:scp_tipos,id',
            'descripcion' => 'nullable|string',
        ]);

        ScpSubTipo::create($request->all());

        return redirect()->route('soportes.tablero')
                         ->with('success', 'Sub-Tipo creado correctamente.');
    }

    // Formulario de edición
    public function edit(ScpSubTipo $subtipo)
    {
        $tipos = ScpTipo::orderBy('nombre')->get();
        return view('soportes.subtipos.edit', ['scpSubTipo' => $subtipo, 'tipos' => $tipos]);
    }

    public function update(Request $request, ScpSubTipo $subtipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'scp_tipo_id' => 'required|exists:scp_tipos,id',
            'descripcion' => 'nullable|string',
        ]);

        $subtipo->update($request->all());

        return redirect()->route('soportes.tablero')
                        ->with('success', 'Sub-Tipo actualizado correctamente.');
    }


    // Eliminar sub-tipo
    public function destroy(ScpSubTipo $scpSubTipo)
    {
        $scpSubTipo->delete();

        return redirect()->route('soportes.tablero')
                         ->with('success', 'Sub-Tipo eliminado correctamente.');
    }

    // Obtener sub-tipos por tipo (ya existente)
    public function getByTipo($tipoId)
    {
        $subTipos = ScpSubTipo::where('scp_tipo_id', $tipoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($subTipos);
    }
}
