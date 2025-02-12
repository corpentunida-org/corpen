<?php

namespace App\Http\Controllers\Seguros;

use App\Http\Controllers\Controller;
use App\Models\Seguros\SegConvenio;
use Illuminate\Http\Request;

class SegConvenioController extends Controller
{
    public function index()
    {
        $convenios = SegConvenio::all();
        return view('seguros.convenio.index', compact('convenios'));
    }
    public function show($id)
    {
        $c = SegConvenio::findOrFail($id);
        $idConvenio = $c->idConvenio;
        $convenio = SegConvenio::with(['plan.condicion'])
            ->where('idConvenio', $idConvenio)
            ->first();
        $planes = $convenio->plan->groupBy('condicion');
        /* dd($convenio); */
        return view('seguros.convenio.show', compact('convenio', 'planes'));
    }

    public function create()
    {
        return view('seguros.convenio.create');
    }
}