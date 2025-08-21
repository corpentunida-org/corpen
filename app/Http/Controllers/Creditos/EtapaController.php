<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Etapa;
use App\Http\Requests\StoreEtapaRequest;
use App\Http\Requests\UpdateEtapaRequest;

class EtapaController extends Controller
{
    /**
     * Muestra una lista de todas las etapas.
     */
    public function index()
    {
        // withCount('estados') es una optimización que añade una columna 'estados_count'
        // a cada etapa, permitiéndonos saber cuántos estados tiene sin cargar todos los modelos.
        $etapas = Etapa::withCount('estados')->latest()->paginate(15);

        return view('creditos.etapas.index', compact('etapas'));
    }

    /**
     * Muestra el formulario para crear una nueva etapa.
     */
    public function create()
    {
        return view('creditos.etapas.create');
    }

    /**
     * Guarda la nueva etapa en la base de datos.
     */
    public function store(StoreEtapaRequest $request)
    {
        Etapa::create($request->validated());

        return redirect()->route('etapas.index')->with('success', 'Etapa creada exitosamente.');
    }

    /**
     * Muestra los detalles de una etapa específica, incluyendo sus estados.
     */
    public function show(Etapa $etapa)
    {
        // Cargamos la relación 'estados' para poder listarlos en la vista de detalle.
        $etapa->load('estados');

        return view('creditos.etapas.show', compact('etapa'));
    }

    /**
     * Muestra el formulario para editar una etapa existente.
     */
    public function edit(Etapa $etapa)
    {
        return view('creditos.etapas.edit', compact('etapa'));
    }

    /**
     * Actualiza la etapa en la base de datos.
     */
    public function update(UpdateEtapaRequest $request, Etapa $etapa)
    {
        $etapa->update($request->validated());

        return redirect()->route('etapas.index')->with('success', 'Etapa actualizada exitosamente.');
    }

    /**
     * Elimina una etapa de la base de datos.
     */
    public function destroy(Etapa $etapa)
    {
        // BUENA PRÁCTICA: Verificar si la etapa tiene estados asociados antes de borrar.
        if ($etapa->estados()->count() > 0) {
            return redirect()->route('etapas.index')->with('error', 'No se puede eliminar la etapa porque tiene estados asociados.');
        }

        $etapa->delete();

        return redirect()->route('etapas.index')->with('success', 'Etapa eliminada exitosamente.');
    }
}