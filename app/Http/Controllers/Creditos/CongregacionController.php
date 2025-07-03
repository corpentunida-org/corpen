<?php

namespace App\Http\Controllers\Creditos;

use App\Http\Controllers\Controller;
use App\Models\Creditos\Congregacion;
use Illuminate\Http\Request;

class CongregacionController extends Controller
{
    /**
     * Display a listing of the resource. Listar (Mostrar datos) 
     */
    public function index()
    {
        $cajon_de_congregaciones = Congregacion::all(); //()::) iguales de asignacion.
        return view("creditos.congregacion.index");
    }

    /**
     * Show the form for creating a new resource. Crear
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage. Almacenar
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource. Ver individualmente
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource. Editar (Vista)
     */ 
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage. Actualiuzar (Metodo)
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage. Eliminar
     */
    public function destroy(string $id)
    {
        //
    }
}
