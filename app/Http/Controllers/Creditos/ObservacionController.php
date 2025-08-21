<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Observacion;
use App\Models\Creditos\Credito;
use App\Http\Requests\StoreObservacionRequest;
use App\Http\Requests\UpdateObservacionRequest;
use Illuminate\Support\Facades\Auth;

class ObservacionController extends Controller
{
    /**
     * Muestra una lista de todas las observaciones.
     */
    public function index()
    {
        // Optimizamos la consulta cargando las relaciones para evitar N+1 queries.
        $observaciones = Observacion::with(['credito', 'usuario'])->latest()->paginate(15);
        return view('creditos.observaciones.index', compact('observaciones'));
    }

    /**
     * Muestra el formulario para crear una nueva observación.
     */
    public function create()
    {
        // Obtenemos los créditos para poblar un <select> en el formulario.
        $creditos = Credito::all();
        return view('creditos.observaciones.create', compact('creditos'));
    }

    /**
     * Guarda la nueva observación en la base de datos.
     */
    public function store(StoreObservacionRequest $request)
    {
        $datosValidados = $request->validated();

        // Asignamos automáticamente el ID del usuario que está logueado.
        $datosValidados['user_id'] = Auth::id();

        Observacion::create($datosValidados);

        return redirect()->route('observaciones.index')->with('success', 'Observación creada exitosamente.');
    }

    /**
     * Muestra los detalles de una observación específica.
     */
    public function show(Observacion $observacion)
    {
        $observacion->load(['credito', 'usuario']);
        return view('creditos.observaciones.show', compact('observacion'));
    }

    /**
     * Muestra el formulario para editar una observación existente.
     */
    public function edit(Observacion $observacion)
    {
        $creditos = Credito::all();
        return view('creditos.observaciones.edit', compact('observacion', 'creditos'));
    }

    /**
     * Actualiza la observación en la base de datos.
     */
    public function update(UpdateObservacionRequest $request, Observacion $observacion)
    {
        $observacion->update($request->validated());
        return redirect()->route('observaciones.index')->with('success', 'Observación actualizada exitosamente.');
    }

    /**
     * Elimina una observación de la base de datos.
     */
    public function destroy(Observacion $observacion)
    {
        $observacion->delete();
        return redirect()->route('observaciones.index')->with('success', 'Observación eliminada exitosamente.');
    }
}