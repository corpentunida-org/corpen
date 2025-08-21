<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Notificacion;
use App\Models\Creditos\Credito;
use App\Models\Maestras\maeTerceros;
use App\Enums\NotificacionEstadoEnum;
use App\Http\Requests\StoreNotificacionRequest;
use App\Http\Requests\UpdateNotificacionRequest;

class NotificacionController extends Controller
{
    /**
     * Muestra una lista de todas las notificaciones.
     */
    public function index()
    {
        $notificaciones = Notificacion::with(['credito', 'tercero'])->latest()->paginate(15);
        return view('creditos.notificaciones.index', compact('notificaciones'));
    }

    /**
     * Muestra el formulario para crear una nueva notificación.
     */
    public function create()
    {
        $creditos = Credito::all();
        $terceros = maeTerceros::all();
        // Pasamos todos los casos del Enum a la vista para el <select> de estados.
        $estados = NotificacionEstadoEnum::cases();

        return view('creditos.notificaciones.create', compact('creditos', 'terceros', 'estados'));
    }

    /**
     * Guarda la nueva notificación en la base de datos.
     */
    public function store(StoreNotificacionRequest $request)
    {
        Notificacion::create($request->validated());
        return redirect()->route('notificaciones.index')->with('success', 'Notificación creada exitosamente.');
    }

    /**
     * Muestra los detalles de una notificación específica.
     */
    public function show(Notificacion $notificacion)
    {
        $notificacion->load(['credito', 'tercero']);
        return view('creditos.notificaciones.show', compact('notificacion'));
    }

    /**
     * Muestra el formulario para editar una notificación existente.
     */
    public function edit(Notificacion $notificacion)
    {
        $creditos = Credito::all();
        $terceros = maeTerceros::all();
        $estados = NotificacionEstadoEnum::cases();

        return view('creditos.notificaciones.edit', compact('notificacion', 'creditos', 'terceros', 'estados'));
    }

    /**
     * Actualiza la notificación en la base de datos.
     */
    public function update(UpdateNotificacionRequest $request, Notificacion $notificacion)
    {
        $notificacion->update($request->validated());
        return redirect()->route('notificaciones.index')->with('success', 'Notificación actualizada exitosamente.');
    }

    /**
     * Elimina una notificación de la base de datos.
     */
    public function destroy(Notificacion $notificacion)
    {
        $notificacion->delete();
        return redirect()->route('notificaciones.index')->with('success', 'Notificación eliminada exitosamente.');
    }
}