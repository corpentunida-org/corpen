<?php

namespace App\Http\Controllers\Recaudo;

use App\Http\Controllers\Controller;
use App\Models\Recaudo\PagoRecaudo;
use App\Models\Creditos\Credito;
use App\Http\Requests\StorePagoRecaudoRequest;
use App\Http\Requests\UpdatePagoRecaudoRequest;

class PagoRecaudoController extends Controller
{
    /**
     * Muestra una lista de todos los pagos recaudados.
     */
    public function index()
    {
        // Optimizamos la consulta cargando la relación 'credito' para evitar N+1 queries.
        $pagos = PagoRecaudo::with('credito')->latest()->paginate(15);
        return view('recaudo.pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para crear un nuevo pago.
     */
    public function create()
    {
        // Pasamos todos los créditos a la vista para rellenar un <select>.
        $creditos = Credito::all();
        return view('recaudo.pagos.create', compact('creditos'));
    }

    /**
     * Guarda el nuevo pago en la base de datos.
     */
    public function store(StorePagoRecaudoRequest $request)
    {
        PagoRecaudo::create($request->validated());
        return redirect()->route('pagos.index')->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un pago específico.
     * Nota: A menudo, para un modelo simple como este, las vistas 'show' y 'edit'
     * son muy similares o incluso la misma.
     */
    public function show(PagoRecaudo $pago)
    {
        $pago->load('credito');
        return view('recaudo.pagos.show', compact('pago'));
    }

    /**
     * Muestra el formulario para editar un pago existente.
     */
    public function edit(PagoRecaudo $pago)
    {
        $creditos = Credito::all();
        return view('recaudo.pagos.edit', compact('pago', 'creditos'));
    }

    /**
     * Actualiza el pago en la base de datos.
     */
    public function update(UpdatePagoRecaudoRequest $request, PagoRecaudo $pago)
    {
        $pago->update($request->validated());
        return redirect()->route('pagos.index')->with('success', 'Pago actualizado exitosamente.');
    }

    /**
     * Elimina un pago de la base de datos.
     */
    public function destroy(PagoRecaudo $pago)
    {
        $pago->delete();
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado exitosamente.');
    }
}