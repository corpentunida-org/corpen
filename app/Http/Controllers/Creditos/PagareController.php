<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Pagare;
use App\Models\Creditos\Credito;
use App\Http\Requests\StorePagareRequest;
use App\Http\Requests\UpdatePagareRequest;

class PagareController extends Controller
{
    /**
     * Muestra una lista de todos los pagarés.
     */
    public function index()
    {
        $pagares = Pagare::with('credito')->latest()->paginate(15);
        return view('creditos.pagares.index', compact('pagares'));
    }

    /**
     * Muestra el formulario para crear un nuevo pagaré.
     */
    public function create()
    {
        // Pasamos los créditos a la vista para un <select>.
        // Opcional: Podrías filtrar para mostrar solo créditos que aún no tienen pagaré.
        $creditos = Credito::all();
        return view('creditos.pagares.create', compact('creditos'));
    }

    /**
     * Guarda el nuevo pagaré en la base de datos.
     */
    public function store(StorePagareRequest $request)
    {
        Pagare::create($request->validated());
        return redirect()->route('pagares.index')->with('success', 'Pagaré creado exitosamente.');
    }

    /**
     * Muestra los detalles de un pagaré específico.
     */
    public function show(Pagare $pagare)
    {
        $pagare->load('credito');
        return view('creditos.pagares.show', compact('pagare'));
    }

    /**
     * Muestra el formulario para editar un pagaré existente.
     */
    public function edit(Pagare $pagare)
    {
        $creditos = Credito::all();
        return view('creditos.pagares.edit', compact('pagare', 'creditos'));
    }

    /**
     * Actualiza el pagaré en la base de datos.
     */
    public function update(UpdatePagareRequest $request, Pagare $pagare)
    {
        $pagare->update($request->validated());
        return redirect()->route('pagares.index')->with('success', 'Pagaré actualizado exitosamente.');
    }

    /**
     * Elimina un pagaré de la base de datos.
     */
    public function destroy(Pagare $pagare)
    {
        $pagare->delete();
        return redirect()->route('pagares.index')->with('success', 'Pagaré eliminado exitosamente.');
    }
}