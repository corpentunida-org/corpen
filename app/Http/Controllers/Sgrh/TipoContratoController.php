<?php

namespace App\Http\Controllers\Sgrh;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\Sgrh\TipoContrato;
use Illuminate\Http\Request;

class TipoContratoController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'SGRH');
    }

    public function index(Request $request)
    {
        $query = TipoContrato::withCount('contratos');

        if ($request->filled('search')) {
            $query->where('nombre', 'like', "%{$request->search}%");
        }

        $tiposContrato = $query->orderBy('nombre')->paginate(20)->appends($request->query());

        return view('sgrh.tipo-contrato.index', compact('tiposContrato'));
    }

    public function create()
    {
        return view('sgrh.tipo-contrato.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sgrh_tipos_contrato,nombre',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $tipoContrato = TipoContrato::create($validated);

        $this->auditoria("Tipo de contrato creado: {$tipoContrato->nombre} (id {$tipoContrato->id})");

        return redirect()->route('sgrh.tipo-contrato.index')->with('success', 'Tipo de contrato creado correctamente.');
    }

    public function edit(TipoContrato $tipo_contrato)
    {
        return view('sgrh.tipo-contrato.edit', ['tipoContrato' => $tipo_contrato]);
    }

    public function update(Request $request, TipoContrato $tipo_contrato)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:sgrh_tipos_contrato,nombre,' . $tipo_contrato->id,
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $tipo_contrato->update($validated);

        $this->auditoria("Tipo de contrato actualizado: {$tipo_contrato->nombre} (id {$tipo_contrato->id})");

        return redirect()->route('sgrh.tipo-contrato.index')->with('success', 'Tipo de contrato actualizado correctamente.');
    }

    public function destroy(TipoContrato $tipo_contrato)
    {
        if ($tipo_contrato->contratos()->exists()) {
            return back()->with('error', 'No se puede eliminar el tipo de contrato porque tiene contratos vinculados.');
        }

        $nombre = $tipo_contrato->nombre;
        $tipo_contrato->delete();

        $this->auditoria("Tipo de contrato eliminado: {$nombre}");

        return redirect()->route('sgrh.tipo-contrato.index')->with('success', 'Tipo de contrato eliminado correctamente.');
    }
}
