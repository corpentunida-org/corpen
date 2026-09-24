<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GestionaAreaDeCatalogo;
use App\Models\Interacciones\IntLinea;
use App\Models\Interacciones\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * "Línea de Obligación" propia de Interacciones, por área (ver IntLinea). Mismo patrón que
 * IntTypeController/IntOutcomeController/IntNextActionController, con una diferencia: el uso no
 * se puede contar con withCount() normal porque interactions.id_linea_de_obligacion es un
 * arreglo JSON (una interacción puede tocar varias líneas a la vez), no una FK simple — hace
 * falta whereJsonContains() (ver contarUso()).
 */
class IntLineaController extends Controller
{
    use GestionaAreaDeCatalogo;

    /** Cuántas interacciones usan esta línea — whereJsonContains(), no una FK simple. */
    private function contarUso(int $id): int
    {
        return Interaction::whereJsonContains('id_linea_de_obligacion', (string) $id)->count();
    }

    public function index(Request $request)
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();

        $lineas = IntLinea::query()
            ->when(!$puedeElegirArea, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('area')->orWhere('area', $miArea)))
            ->when($puedeElegirArea && $request->filled('area'), function ($q) use ($request) {
                $request->input('area') === 'compartido'
                    ? $q->whereNull('area')
                    : $q->where('area', $request->input('area'));
            })
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $lineas->getCollection()->transform(function ($linea) {
            $linea->interactions_count = $this->contarUso($linea->id);

            return $linea;
        });

        $areasDisponibles = $puedeElegirArea
            ? DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area')
            : collect();

        return view('interactions.lineas.index', compact('lineas', 'puedeElegirArea', 'areasDisponibles'));
    }

    public function create()
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.lineas.create', compact('puedeElegirArea', 'miArea', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $area = $this->puedeElegirArea()
            ? ($request->input('area') ?: null)
            : $this->areaDelUsuario();

        IntLinea::create(['name' => $request->name, 'area' => $area]);

        return redirect()
            ->route('interactions.lineas.index')
            ->with('success', 'Línea creada exitosamente'.($area ? ' para el área '.strtoupper($area) : ' (compartida, para todas las áreas)').'.');
    }

    private function autorizarLinea(IntLinea $linea): void
    {
        if ($this->puedeElegirArea()) {
            return;
        }
        abort_unless($linea->area === null || $linea->area === $this->areaDelUsuario(), 404);
    }

    public function show($id)
    {
        $linea = IntLinea::findOrFail($id);
        $this->autorizarLinea($linea);
        $linea->interactions_count = $this->contarUso($linea->id);

        return view('interactions.lineas.show', compact('linea'));
    }

    public function edit($id)
    {
        $linea = IntLinea::findOrFail($id);
        $this->autorizarLinea($linea);

        $puedeElegirArea = $this->puedeElegirArea();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.lineas.edit', compact('linea', 'puedeElegirArea', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $linea = IntLinea::findOrFail($id);
        $this->autorizarLinea($linea);

        $update = ['name' => $request->name];
        if ($this->puedeElegirArea()) {
            $update['area'] = $request->input('area') ?: null;
        }
        $linea->update($update);

        return redirect()->route('interactions.lineas.index')->with('success', 'Línea actualizada correctamente');
    }

    public function destroy($id)
    {
        $linea = IntLinea::findOrFail($id);
        $this->autorizarLinea($linea);

        $usos = $this->contarUso($linea->id);
        if ($usos > 0) {
            return redirect()->back()->with('error', "La línea '{$linea->name}' no se puede eliminar porque está en uso en {$usos} interacciones.");
        }

        $linea->delete();

        return redirect()->route('interactions.lineas.index')->with('success', 'Línea eliminada correctamente.');
    }
}
