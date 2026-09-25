<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GestionaAreaDeCatalogo;
use App\Models\Interacciones\IntMotivoNoEfectivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntMotivoNoEfectivoController extends Controller
{
    use GestionaAreaDeCatalogo;

    public function index(Request $request)
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();

        $motivos = IntMotivoNoEfectivo::withCount('seguimientos')
            ->when(!$puedeElegirArea, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('area')->orWhere('area', $miArea)))
            ->when($puedeElegirArea && $request->filled('area'), function ($q) use ($request) {
                $request->input('area') === 'compartido'
                    ? $q->whereNull('area')
                    : $q->where('area', $request->input('area'));
            })
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        $areasDisponibles = $puedeElegirArea
            ? DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area')
            : collect();

        return view('interactions.motivos_no_efectivo.index', compact('motivos', 'puedeElegirArea', 'areasDisponibles'));
    }

    public function create()
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.motivos_no_efectivo.create', compact('puedeElegirArea', 'miArea', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = $this->puedeElegirArea()
            ? ($request->input('area') ?: null)
            : $this->areaDelUsuario();

        IntMotivoNoEfectivo::create([
            'name' => $request->name,
            'area' => $area,
        ]);

        return redirect()
            ->route('interactions.motivos_no_efectivo.index')
            ->with('success', 'Motivo creado exitosamente' . ($area ? ' para el área ' . strtoupper($area) : ' (compartido, para todas las áreas)') . '.');
    }

    /** Bloquea tocar el motivo de OTRA área si no se puede administrar cualquiera. */
    private function autorizarAccion(IntMotivoNoEfectivo $motivo): void
    {
        if ($this->puedeElegirArea()) {
            return;
        }
        abort_unless($motivo->area === null || $motivo->area === $this->areaDelUsuario(), 404);
    }

    public function show($id)
    {
        $motivo = IntMotivoNoEfectivo::withCount('seguimientos')->findOrFail($id);
        $this->autorizarAccion($motivo);

        return view('interactions.motivos_no_efectivo.show', compact('motivo'));
    }

    public function edit($id)
    {
        $motivo = IntMotivoNoEfectivo::findOrFail($id);
        $this->autorizarAccion($motivo);

        $puedeElegirArea = $this->puedeElegirArea();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.motivos_no_efectivo.edit', compact('motivo', 'puedeElegirArea', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $motivo = IntMotivoNoEfectivo::findOrFail($id);
        $this->autorizarAccion($motivo);

        $update = ['name' => $request->name];
        if ($this->puedeElegirArea()) {
            $update['area'] = $request->input('area') ?: null;
        }
        $motivo->update($update);

        return redirect()
            ->route('interactions.motivos_no_efectivo.index')
            ->with('success', 'Motivo actualizado correctamente');
    }

    public function destroy($id)
    {
        $motivo = IntMotivoNoEfectivo::withCount('seguimientos')->findOrFail($id);
        $this->autorizarAccion($motivo);

        if ($motivo->seguimientos_count > 0) {
            return redirect()->back()->with('error', "No se puede eliminar: este motivo está usado en {$motivo->seguimientos_count} seguimientos.");
        }

        $motivo->delete();

        return redirect()->route('interactions.motivos_no_efectivo.index')->with('success', 'Eliminado correctamente.');
    }
}
