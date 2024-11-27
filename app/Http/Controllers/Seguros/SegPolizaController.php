<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPoliza;
use Illuminate\Http\Request;

use App\Models\Seguros\SegAsegurado;

class SegPolizaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('seguros.polizas.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $titular = SegAsegurado::where('cedula', $id)
            ->where('parentesco', 'AF')
            ->first();
        if($titular) {
            return view('seguros.polizas.show', compact('titular'));
        }
        $titular = null;
        return redirect()->route('poliza.index')->with('warning', 'No se encontró el titular de la póliza');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegPoliza $segPoliza)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPoliza $segPoliza)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegPoliza $segPoliza)
    {
        //
    }
}
