<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\TipoCredito;
use App\Http\Requests\StoreTipoCreditoRequest;
use App\Http\Requests\UpdateTipoCreditoRequest;

class TipoCreditoController extends Controller
{
    public function index()
    {
        // Optimizamos la consulta contando las líneas de crédito asociadas.
        $tiposCredito = TipoCredito::withCount('lineasCredito')->latest()->paginate(15);
        return view('creditos.tipos_credito.index', compact('tiposCredito'));
    }

    public function create()
    {
        return view('creditos.tipos_credito.create');
    }

    public function store(StoreTipoCreditoRequest $request)
    {
        TipoCredito::create($request->validated());
        return redirect()->route('tipos_credito.index')->with('success', 'Tipo de crédito creado exitosamente.');
    }

    public function show(TipoCredito $tipo_credito)
    {
        // Cargamos la relación para mostrar las líneas de crédito asociadas.
        $tipo_credito->load('lineasCredito');
        return view('creditos.tipos_credito.show', compact('tipo_credito'));
    }

    public function edit(TipoCredito $tipo_credito)
    {
        return view('creditos.tipos_credito.edit', compact('tipo_credito'));
    }

    public function update(UpdateTipoCreditoRequest $request, TipoCredito $tipo_credito)
    {
        $tipo_credito->update($request->validated());
        return redirect()->route('tipos_credito.index')->with('success', 'Tipo de crédito actualizado exitosamente.');
    }

    public function destroy(TipoCredito $tipo_credito)
    {
        // BUENA PRÁCTICA: Verificar si el tipo de crédito está en uso.
        if ($tipo_credito->lineasCredito()->count() > 0) {
            return redirect()->route('tipos_credito.index')->with('error', 'No se puede eliminar el tipo de crédito porque está en uso.');
        }

        $tipo_credito->delete();
        return redirect()->route('tipos_credito.index')->with('success', 'Tipo de crédito eliminado exitosamente.');
    }
}