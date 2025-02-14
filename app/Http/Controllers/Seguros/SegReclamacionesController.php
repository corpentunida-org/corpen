<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use Illuminate\Http\Request;
use App\Models\Seguros\SegAsegurado;

class SegReclamacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("seguros.reclamaciones.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $id = $request->query('a');

        $asegurado = SegAsegurado::where('cedula',$id)->with(['tercero','terceroAF'])->first();
        
        $poliza = SegPoliza::where('seg_asegurado_id', $id)->with(['plan.coberturas'])->first();
        dd($poliza);
        return view("seguros.reclamaciones.create", compact('asegurado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
