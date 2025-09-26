<?php

namespace App\Http\Controllers\Vistas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vistas\VisitaCorpen;
use Illuminate\Support\Facades\DB; // <- Importación necesaria para DB::raw

class VisitaCorpenController extends Controller
{
    /**
     * Listar todas las visitas con paginación y filtros opcionales
     */
    public function index(Request $request)
    {
        $query = VisitaCorpen::with('cliente')->orderBy('fecha', 'desc');

        // Filtro por ciudad (antes 'banco')
        if ($request->has('ciudad')) {
            $query->where('banco', $request->ciudad); // campo sigue siendo 'banco' en la DB
        }

        // Filtro por fecha
        if ($request->has('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $visitas = $query->paginate(15); // 15 visitas por página

        return view('visitas.index', compact('visitas'));
    }

    /**
     * Registrar nueva visita
     */
    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string',
            'banco' => 'required|string|max:255',
            'motivo' => 'nullable|string',
            'registrado_por' => 'required|string|max:255',
        ]);



        $visita = VisitaCorpen::registrar(
            $request->cedula,
            $request->ciudad, // renombrado para mayor claridad
            $request->motivo,
            $request->registrado_por
        );

        return response()->json([
            'message' => 'Visita registrada correctamente',
            'data' => $visita
        ], 201);
    }

    /**
     * Mostrar una visita específica
     */
    public function show($id)
    {
        $visita = VisitaCorpen::with('cliente')->findOrFail($id);
        return response()->json($visita);
    }

    /**
     * Actualizar una visita
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ciudad' => 'nullable|string|max:255',
            'motivo' => 'nullable|string',
            'registrado_por' => 'nullable|string|max:255',
        ]);

        $visita = VisitaCorpen::findOrFail($id);
        $visita->update($request->only(['banco', 'motivo', 'registrado_por'])); // banco sigue en la DB

        return response()->json([
            'message' => 'Visita actualizada correctamente',
            'data' => $visita
        ]);
    }

    /**
     * Eliminar una visita
     */
    public function destroy($id)
    {
        $visita = VisitaCorpen::findOrFail($id);
        $visita->delete();

        return response()->json([
            'message' => 'Visita eliminada correctamente'
        ]);
    }

    /**
     * Buscar visitas por cliente
     */
    public function search(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string'
        ]);

        $visitas = VisitaCorpen::with('cliente')
            ->where('cliente_id', $request->cedula)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($visitas);
    }

    /**
     * Estadísticas: visitas por ciudad y por usuario
     */
    public function estadisticas()
    {
        $porCiudad = VisitaCorpen::select('banco', DB::raw('count(*) as total'))
            ->groupBy('banco')
            ->get();

        $porUsuario = VisitaCorpen::select('registrado_por', DB::raw('count(*) as total'))
            ->groupBy('registrado_por')
            ->get();

        return response()->json([
            'por_ciudad' => $porCiudad,
            'por_usuario' => $porUsuario
        ]);
    }
    public function buscarCliente(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        $clientes = \App\Models\Maestras\maeTerceros::where('cod_ter', 'like', "%{$request->query}%")
            ->orWhere('nom_ter', 'like', "%{$request->query}%")
            ->limit(10)
            ->get(['cod_ter', 'nom_ter']); // Solo devuelve lo necesario

        return response()->json($clientes);
    }

}
