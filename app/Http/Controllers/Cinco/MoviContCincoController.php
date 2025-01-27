<?php

namespace App\Http\Controllers\Cinco;

use App\Models\Cinco\MoviContCinco;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MoviContCincoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('cinco.movcontables.index');
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
        return view('cinco.movcontables.show');        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MoviContCinco $moviContCinco)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MoviContCinco $moviContCinco)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MoviContCinco $moviContCinco)
    {
        //
    }
}
