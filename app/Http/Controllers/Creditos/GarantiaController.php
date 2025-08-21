<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Garantia;
use App\Http\Requests\StoreGarantiaRequest;
use App\Http\Requests\UpdateGarantiaRequest;

class GarantiaController extends Controller
{
    public function index()
    {
        // Usamos withCount para saber cuántas líneas de crédito usan cada garantía
        // sin necesidad de cargar toda la relación. Es muy eficiente.
        $garantias = Garantia::withCount('lineasCredito')->latest()->paginate(15);
        return view('creditos.garantias.index', compact('garantias'));
    }

    public function create()
    {
        return view('creditos.garantias.create');
    }

    public function store(StoreGarantiaRequest $request)
    {
        Garantia::create($request->validated());
        return redirect()->route('garantias.index')->with('success', 'Garantía creada exitosamente.');
    }

    public function show(Garantia $garantia)
    {
        // Cargamos la relación para mostrar las líneas de crédito asociadas en la vista de detalle.
        $garantia->load('lineasCredito');
        return view('creditos.garantias.show', compact('garantia'));
    }

    public function edit(Garantia $garantia)
    {
        return view('creditos.garantias.edit', compact('garantia'));
    }

    public function update(UpdateGarantiaRequest $request, Garantia $garantia)
    {
        $garantia->update($request->validated());
        return redirect()->route('garantias.index')->with('success', 'Garantía actualizada exitosamente.');
    }

    public function destroy(Garantia $garantia)
    {
        // BUENA PRÁCTICA: Verificar si la garantía está siendo utilizada.
        // Esto es posible gracias a la relación lineasCredito() que añadimos al modelo.
        if ($garantia->lineasCredito()->count() > 0) {
            return redirect()->route('garantias.index')->with('error', 'No se puede eliminar la garantía porque está en uso por una o más líneas de crédito.');
        }

        $garantia->delete();
        return redirect()->route('garantias.index')->with('success', 'Garantía eliminada exitosamente.');
    }
}