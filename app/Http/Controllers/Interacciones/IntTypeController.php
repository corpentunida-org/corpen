<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GestionaAreaDeCatalogo;
use App\Models\Interacciones\IntType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntTypeController extends Controller
{
    use GestionaAreaDeCatalogo;

    /**
     * Mostrar lista de tipos de interacción
     */
    public function index(Request $request)
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();

        $types = IntType::withCount('interactions')
            ->when(!$puedeElegirArea, fn ($q) => $q->where(fn ($q2) => $q2->whereNull('area')->orWhere('area', $miArea)))
            // Filtro por área: solo tiene sentido para quien ve más de una (sin listado.todos ya
            // está limitado arriba a la suya + compartidos, no hay nada más que filtrar).
            // "compartido" es un valor especial (no un área real) para pedir area IS NULL.
            ->when($puedeElegirArea && $request->filled('area'), function ($q) use ($request) {
                $request->input('area') === 'compartido'
                    ? $q->whereNull('area')
                    : $q->where('area', $request->input('area'));
            })
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        $areasDisponibles = $puedeElegirArea
            ? DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area')
            : collect();

        return view('interactions.types.index', compact('types', 'puedeElegirArea', 'areasDisponibles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $puedeElegirArea = $this->puedeElegirArea();
        $miArea = $this->areaDelUsuario();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.types.create', compact('puedeElegirArea', 'miArea', 'areas'));
    }

    /**
     * Guardar un nuevo tipo. Sin el permiso para elegir área, queda ligado automáticamente a la
     * del perfil de quien lo crea — así un área gestiona su propio catálogo sola, sin que un
     * desarrollador tenga que crearlo por ella cada vez ("que ella pueda seguir agregando según
     * necesidad").
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = $this->puedeElegirArea()
            ? ($request->input('area') ?: null)
            : $this->areaDelUsuario();

        IntType::create([
            'name' => $request->name,
            'area' => $area,
        ]);

        return redirect()
            ->route('interactions.types.index')
            ->with('success', 'Tipo de interacción creado exitosamente' . ($area ? ' para el área ' . strtoupper($area) : ' (compartido, para todas las áreas)') . '.');
    }

    /** Bloquea tocar el tipo de OTRA área si no se puede administrar cualquiera. */
    private function autorizarTipo(IntType $type): void
    {
        if ($this->puedeElegirArea()) {
            return;
        }
        abort_unless($type->area === null || $type->area === $this->areaDelUsuario(), 404);
    }

    /**
     * Mostrar un tipo específico
     */
    public function show($id)
    {
        $type = IntType::withCount('interactions')->findOrFail($id);
        $this->autorizarTipo($type);

        return view('interactions.types.show', compact('type'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $type = IntType::findOrFail($id);
        $this->autorizarTipo($type);

        $puedeElegirArea = $this->puedeElegirArea();
        $areas = DB::table('roles')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('interactions.types.edit', compact('type', 'puedeElegirArea', 'areas'));
    }

    /**
     * Actualizar un tipo
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = IntType::findOrFail($id);
        $this->autorizarTipo($type);

        $update = ['name' => $request->name];
        // El área solo se puede cambiar con el permiso de administrar cualquiera — quien solo
        // administra la suya no puede "mudar" un tipo a otra área ni volverlo compartido.
        if ($this->puedeElegirArea()) {
            $update['area'] = $request->input('area') ?: null;
        }
        $type->update($update);

        return redirect()
            ->route('interactions.types.index')
            ->with('success', 'Tipo de interacción actualizado correctamente');
    }

    /**
     * Eliminar un tipo
     */
    public function destroy($id)
    {
        $type = IntType::withCount('interactions')->findOrFail($id);
        $this->autorizarTipo($type);

        if ($type->interactions_count > 0) {
            return redirect()->back()->with('error', "El tipo '{$type->name}' no se puede eliminar porque tiene {$type->interactions_count} interacciones asociadas.");
        }

        $type->delete();
        return redirect()->route('interactions.types.index')->with('success', 'Tipo de interacción eliminado correctamente.');
    }
}
