<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegPlan;
use Illuminate\Http\Request;

class SegPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planes = SegPlan::with(['condicion'])
            ->get()
            ->groupBy('condicion');
        return view('seguros.planes.index', compact('planes'));
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
    public function show(SegPlan $segPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SegPlan $segPlan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SegPlan $segPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SegPlan $segPlan)
    {
        //
    }
}
