<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Estado;
use App\Models\Archivo\GdoArea; 
use Illuminate\Database\QueryException;

class CorrEstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::with('area')
            ->withCount('correspondencias')
            ->paginate(15);
            
        return view('correspondencia.estados.index', compact('estados'));
    }

    public function create()
    {
        // Traemos las áreas para poder iterarlas en un <select> en tu vista create
        $areas = GdoArea::all(); 
        return view('correspondencia.estados.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:corr_estados,nombre',
            'descripcion' => 'nullable|string|max:255', // Ajustado a 255 para coincidir con varchar(255)
            'id_area'     => 'required|integer', // Puede agregar |exists:gdo_areas,id si deseas validación estricta
            'activo'      => 'nullable|boolean',
        ], [
            'nombre.unique' => 'Ya existe un estado con este nombre.',
            'id_area.required' => 'Debe seleccionar un área responsable.'
        ]);

        // Si usas un checkbox en HTML, si no se marca no envía nada. Forzamos el booleano.
        $data['activo'] = $request->has('activo') ? 1 : 0;

        try {
            Estado::create($data);
            return redirect()->route('correspondencia.estados.index')
                ->with('success', 'Estado creado correctamente.');
        } catch (QueryException $e) {
            // Captura errores de duplicidad que la validación se saltó por milisegundos
            return back()->withInput()->with('error', 'Error de duplicidad: El registro ya existe.');
        }
    }

public function show(Estado $estado)
{
    // 1. Traemos todas las correspondencias para el conteo real y el gráfico
    $todasCorrespondencias = $estado->correspondencias()->get();

    // 2. Paginamos para la tabla
    $correspondencias = $estado->correspondencias()->paginate(5);

    // 3. Procesamos el histórico agrupado de forma limpia
    $historico = $todasCorrespondencias->groupBy(function($item) {
        // Evaluamos las columnas posibles de fecha de tu modelo
        $fechaRaw = $item->fec_ing ?? $item->fecha_aded ?? $item->created_at ?? null;
        
        if ($fechaRaw) {
            try {
                return \Carbon\Carbon::parse($fechaRaw)->format('Y-m');
            } catch (\Exception $e) {
                return now()->format('Y-m');
            }
        }
        return now()->format('Y-m');
    })->map(fn($group) => $group->count())->sortKeys();

    // 4. Formateamos los ejes de ApexCharts
    $mesesEjeX = $historico->keys()->map(function($anoMes) {
        $partes = explode('-', $anoMes);
        $meses = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
        return ($meses[$partes[1]] ?? 'Mes') . ' ' . $partes[0];
    })->toArray();

    $valoresEjeY = $historico->values()->toArray();

    return view('correspondencia.estados.show', compact(
        'estado', 
        'correspondencias', 
        'mesesEjeX', 
        'valoresEjeY', 
        'todasCorrespondencias'
    ));
}

    public function edit(Estado $estado)
    {
        // Traemos las áreas para preseleccionar la actual en tu vista edit
        $areas = GdoArea::all();
        return view('correspondencia.estados.edit', compact('estado', 'areas'));
    }

    public function update(Request $request, Estado $estado)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:corr_estados,nombre,' . $estado->id,
            'descripcion' => 'nullable|string|max:255', // Ajustado a 255
            'id_area'     => 'required|integer',
            'activo'      => 'nullable|boolean',
        ], [
            'nombre.unique' => 'Este nombre de estado ya está en uso por otro registro.',
            'id_area.required' => 'Debe seleccionar un área responsable.'
        ]);

        // Control del checkbox para la actualización
        $data['activo'] = $request->has('activo') ? 1 : 0;

        try {
            $estado->update($data);
            return redirect()->route('correspondencia.estados.index')
                ->with('success', 'Estado actualizado correctamente.');
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Error al actualizar: Verifique los datos.');
        }
    }
    /* public function destroy(Estado $estado)
    {
        if ($estado->correspondencias()->count() > 0) {
            return redirect()->route('correspondencia.estados.index')
                ->with('error', 'No se puede eliminar: El estado tiene documentos asociados.');
        }

        $estado->delete();
        return redirect()->route('correspondencia.estados.index')
            ->with('success', 'Estado eliminado correctamente.');
    }
    */
}