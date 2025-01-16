<?php

namespace App\Http\Controllers\Cinco;

use App\Http\Controllers\Controller;
use App\Models\Cinco\Terceros;
use Illuminate\Http\Request;

class TercerosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $terceros = Terceros::paginate(6);
        return view('cinco.terceros.index', compact('terceros'));
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
    public function show($id)
    {
        $tercero = Terceros::where('Cod_Ter', $id)->first();
        dd($tercero);
        if (!$tercero) {
            return redirect()->route('cinco.tercero.index')->with('warning', 'No existe esa cédula en la lista de terceros cinco');
        }
        return view('cinco.terceros.show', compact('tercero'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Terceros $terceros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Terceros $terceros)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Terceros $terceros)
    {
        //
    }
}
