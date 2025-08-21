<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Estado;
use App\Models\Creditos\Etapa; // Necesitamos el modelo Etapa para los formularios
use App\Http\Requests\StoreEstadoRequest;
use App\Http\Requests\UpdateEstadoRequest;
use Illuminate\Database\QueryException; // Para capturar errores de la base de datos

class EstadoController extends Controller
{
    public function index()
    {
        // Cargamos la relación 'etapa' para mostrar su nombre en la lista.
        $estados = Estado::with('etapa')->latest()->paginate(15);
        return view('creditos.estados.index', compact('estados'));
    }

    public function create()
    {
        // Pasamos todas las etapas a la vista para rellenar un <select>.
        $etapas = Etapa::all();
        return view('creditos.estados.create', compact('etapas'));
    }

    public function store(StoreEstadoRequest $request)
    {
        Estado::create($request->validated());
        return redirect()->route('estados.index')->with('success', 'Estado creado exitosamente.');
    }

    public function show(Estado $estado)
    {
        $estado->load('etapa');
        return view('creditos.estados.show', compact('estado'));
    }

    public function edit(Estado $estado)
    {
        $etapas = Etapa::all();
        return view('creditos.estados.edit', compact('estado', 'etapas'));
    }

    public function update(UpdateEstadoRequest $request, Estado $estado)
    {
        $estado->update($request->validated());
        return redirect()->route('estados.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(Estado $estado)
    {
        try {
            $estado->delete();
            return redirect()->route('estados.index')->with('success', 'Estado eliminado exitosamente.');
        } catch (QueryException $e) {
            // Si el estado está siendo usado por un crédito, la base de datos
            // arrojará un error de restricción de clave foránea.
            return redirect()->route('estados.index')->with('error', 'No se puede eliminar el estado porque está en uso.');
        }
    }
}