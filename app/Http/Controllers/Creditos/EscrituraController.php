<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Escritura;
use App\Models\Creditos\Credito;
use App\Http\Requests\StoreEscrituraRequest;
use App\Http\Requests\UpdateEscrituraRequest;

class EscrituraController extends Controller
{
    public function index()
    {
        // Optimizamos la consulta cargando la relación 'credito' para evitar N+1 queries.
        $escrituras = Escritura::with('credito')->latest()->paginate(15);

        return view('creditos.escrituras.index', compact('escrituras'));
    }

    public function create()
    {
        // Obtenemos todos los créditos para pasarlos al formulario y popular un <select>.
        $creditos = Credito::all();
        
        return view('creditos.escrituras.create', compact('creditos'));
    }

    public function store(StoreEscrituraRequest $request)
    {
        // La validación se ejecuta automáticamente.
        Escritura::create($request->validated());

        return redirect()->route('escrituras.index')->with('success', 'Escritura creada exitosamente.');
    }

    public function show(Escritura $escritura)
    {
        // El Route Model Binding ya nos entrega la escritura.
        // Nos aseguramos de cargar la relación para mostrarla en la vista.
        $escritura->load('credito');

        return view('creditos.escrituras.show', compact('escritura'));
    }

    public function edit(Escritura $escritura)
    {
        $creditos = Credito::all();

        return view('creditos.escrituras.edit', compact('escritura', 'creditos'));
    }

    public function update(UpdateEscrituraRequest $request, Escritura $escritura)
    {
        $escritura->update($request->validated());

        return redirect()->route('escrituras.index')->with('success', 'Escritura actualizada exitosamente.');
    }

    public function destroy(Escritura $escritura)
    {
        $escritura->delete();

        return redirect()->route('escrituras.index')->with('success', 'Escritura eliminada exitosamente.');
    }
}