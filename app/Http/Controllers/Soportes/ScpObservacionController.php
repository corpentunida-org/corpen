<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpObservacion;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\User;
use Illuminate\Http\Request;

class ScpObservacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $observaciones = ScpObservacion::with(['soporte', 'estado', 'usuario', 'tipoObservacion'])->paginate(10);
        return view('soportes.observaciones.index', compact('observaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $soportes = ScpSoporte::all();
        $estados = ScpEstado::all();
        $usuarios = User::all();
        $tiposObservacion = ScpTipoObservacion::all();

        return view('soportes.observaciones.create', compact('soportes', 'estados', 'usuarios', 'tiposObservacion'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'id_scp_soporte' => 'required|exists:scp_soportes,id',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_users' => 'required|exists:users,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observaciones,id',
        ]);

        $observacion = ScpObservacion::create([
            'observacion' => $request->observacion,
            'timestam' => now(), // Se puede manejar automáticamente si la columna es un timestamp
            'id_scp_soporte' => $request->id_scp_soporte,
            'id_scp_estados' => $request->id_scp_estados,
            'id_users' => $request->id_users,
            'id_tipo_observacion' => $request->id_tipo_observacion,
        ]);

        return redirect()->route('soportes.observaciones.index')->with('success', 'Observación creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ScpObservacion $scpObservacion)
    {
        $scpObservacion->load(['soporte', 'estado', 'usuario', 'tipoObservacion']);
        return view('soportes.observaciones.show', compact('scpObservacion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ScpObservacion $scpObservacion)
    {
        $soportes = ScpSoporte::all();
        $estados = ScpEstado::all();
        $usuarios = User::all();
        $tiposObservacion = ScpTipoObservacion::all();

        return view('soportes.observaciones.edit', compact('scpObservacion', 'soportes', 'estados', 'usuarios', 'tiposObservacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ScpObservacion $scpObservacion)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'id_scp_soporte' => 'required|exists:scp_soportes,id',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_users' => 'required|exists:users,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observaciones,id',
        ]);

        $scpObservacion->update([
            'observacion' => $request->observacion,
            'id_scp_soporte' => $request->id_scp_soporte,
            'id_scp_estados' => $request->id_scp_estados,
            'id_users' => $request->id_users,
            'id_tipo_observacion' => $request->id_tipo_observacion,
        ]);

        return redirect()->route('soportes.observaciones.index')->with('success', 'Observación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScpObservacion $scpObservacion)
    {
        $scpObservacion->delete();
        return redirect()->route('soportes.observaciones.index')->with('success', 'Observación eliminada exitosamente.');
    }
}