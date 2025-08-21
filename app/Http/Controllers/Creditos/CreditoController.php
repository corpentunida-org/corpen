<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Credito;
use App\Models\Creditos\Estado;
use App\Models\Creditos\LineaCredito;
use App\Models\Maestras\maeTerceros; // Asegúrate que la ruta a tu modelo Tercero sea correcta
use App\Http\Requests\StoreCreditoRequest;
use App\Http\Requests\UpdateCreditoRequest;

class CreditoController extends Controller
{
    /**
     * Muestra una lista de todos los créditos.
     */
    public function index()
    {
        // Optimizamos la consulta cargando las relaciones principales para evitar el problema N+1.
        $creditos = Credito::with(['estado', 'lineaCredito', 'tercero'])->latest()->paginate(15);

        return view('creditos.index', compact('creditos'));
    }

    /**
     * Muestra el formulario para crear un nuevo crédito.
     */
    public function create()
    {
        // Pasamos a la vista todos los datos necesarios para los menús desplegables del formulario.
        $estados = Estado::all();
        $lineasCredito = LineaCredito::all();
        $terceros = maeTerceros::all();

        return view('creditos.create', compact('estados', 'lineasCredito', 'terceros'));
    }

    /**
     * Guarda el nuevo crédito en la base de datos.
     */
    public function store(StoreCreditoRequest $request)
    {
        // La validación se ejecuta automáticamente gracias al Form Request.
        Credito::create($request->validated());

        return redirect()->route('creditos.index')->with('success', 'Crédito creado exitosamente.');
    }

    /**
     * Muestra los detalles de un crédito específico y todas sus relaciones.
     */
    public function show(Credito $credito)
    {
        // Cargamos todas las relaciones del crédito para mostrarlas en la vista de detalle.
        $credito->load(['estado', 'lineaCredito', 'tercero', 'pagareRelacionado', 'escritura', 'notificaciones']);

        return view('creditos.show', compact('credito'));
    }

    /**
     * Muestra el formulario para editar un crédito existente.
     */
    public function edit(Credito $credito)
    {
        // Al igual que en create, necesitamos los datos para los menús desplegables.
        $estados = Estado::all();
        $lineasCredito = LineaCredito::all();
        $terceros = maeTerceros::all();

        return view('creditos.edit', compact('credito', 'estados', 'lineasCredito', 'terceros'));
    }

    /**
     * Actualiza el crédito en la base de datos.
     */
    public function update(UpdateCreditoRequest $request, Credito $credito)
    {
        $credito->update($request->validated());

        return redirect()->route('creditos.index')->with('success', 'Crédito actualizado exitosamente.');
    }

    /**
     * Elimina un crédito de la base de datos.
     */
    public function destroy(Credito $credito)
    {
        $credito->delete();

        return redirect()->route('creditos.index')->with('success', 'Crédito eliminado exitosamente.');
    }
}