<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegCondicion;
use Illuminate\Http\Request;

class SegCondicionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'condicion_descripcion' => 'required|string|max:255',
        ]);
        SegCondicion::create([
            'descripcion' => $request->condicion_descripcion,
        ]);
        return redirect()->back()->with('success', 'Condición creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(SegCondicion $segCondicion)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegCondicion $segCondicion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegCondicion $segCondicion)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegCondicion $segCondicion)
    {
        //
    }
}
