<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\LineaCredito;
use App\Models\Creditos\Garantia;
use App\Models\Creditos\TipoCredito;
use App\Http\Requests\StoreLineaCreditoRequest;
use App\Http\Requests\UpdateLineaCreditoRequest;

class LineaCreditoController extends Controller
{
    public function index()
    {
        // Optimizamos cargando las relaciones para evitar N+1 queries en la vista.
        $lineasCredito = LineaCredito::with(['garantia', 'tipoCredito'])->latest()->paginate(15);
        return view('creditos.lineas_credito.index', compact('lineasCredito'));
    }

    public function create()
    {
        // Necesitamos las garantías y tipos de crédito para los <select> del formulario.
        $garantias = Garantia::all();
        $tiposCredito = TipoCredito::all();
        return view('creditos.lineas_credito.create', compact('garantias', 'tiposCredito'));
    }

    public function store(StoreLineaCreditoRequest $request)
    {
        LineaCredito::create($request->validated());
        return redirect()->route('lineas_credito.index')->with('success', 'Línea de crédito creada exitosamente.');
    }

    public function show(LineaCredito $lineas_credito)
    {
        $lineas_credito->load(['garantia', 'tipoCredito']);
        return view('creditos.lineas_credito.show', compact('lineas_credito'));
    }

    public function edit(LineaCredito $lineas_credito)
    {
        $garantias = Garantia::all();
        $tiposCredito = TipoCredito::all();
        return view('creditos.lineas_credito.edit', compact('lineas_credito', 'garantias', 'tiposCredito'));
    }

    public function update(UpdateLineaCreditoRequest $request, LineaCredito $lineas_credito)
    {
        $lineas_credito->update($request->validated());
        return redirect()->route('lineas_credito.index')->with('success', 'Línea de crédito actualizada exitosamente.');
    }

    public function destroy(LineaCredito $lineas_credito)
    {
        // BUENA PRÁCTICA: Verificar si la línea de crédito tiene créditos asociados.
        // Esto es posible gracias a la relación creditos() que añadimos al modelo.
        if ($lineas_credito->creditos()->count() > 0) {
            return redirect()->route('lineas_credito.index')->with('error', 'No se puede eliminar la línea porque está siendo utilizada en créditos existentes.');
        }

        $lineas_credito->delete();
        return redirect()->route('lineas_credito.index')->with('success', 'Línea de crédito eliminada exitosamente.');
    }
}