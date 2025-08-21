<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use App\Models\Tesoreria\Pago;
use App\Models\Creditos\Credito;
use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\UpdatePagoRequest;

class PagoController extends Controller
{
    /**
     * Muestra una lista de todos los pagos.
     */
    public function index()
    {
        $pagos = Pago::with('credito')->latest()->paginate(15);
        return view('tesoreria.pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para crear un nuevo pago.
     */
    public function create()
    {
        $creditos = Credito::all();
        return view('tesoreria.pagos.create', compact('creditos'));
    }

    /**
     * Guarda el nuevo pago en la base de datos.
     */
    public function store(StorePagoRequest $request)
    {
        Pago::create($request->validated());
        return redirect()->route('pagos.index')->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un pago específico.
     */
    public function show(Pago $pago)
    {
        $pago->load('credito');
        return view('tesoreria.pagos.show', compact('pago'));
    }

    /**
     * Muestra el formulario para editar un pago existente.
     */
    public function edit(Pago $pago)
    {
        $creditos = Credito::all();
        return view('tesoreria.pagos.edit', compact('pago', 'creditos'));
    }

    /**
     * Actualiza el pago en la base de datos.
     */
    public function update(UpdatePagoRequest $request, Pago $pago)
    {
        $pago->update($request->validated());
        return redirect()->route('pagos.index')->with('success', 'Pago actualizado exitosamente.');
    }

    /**
     * Elimina un pago de la base de datos.
     */
    public function destroy(Pago $pago)
    {
        $pago->delete();
        return redirect()->route('pagos.index')->with('success', 'Pago eliminado exitosamente.');
    }
}