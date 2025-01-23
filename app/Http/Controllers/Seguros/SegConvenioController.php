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
        $convenio = SegConvenio::where('idConvenio', $idConvenio)
            ->with(['plan.condicion'])->first();
        return view('seguros.convenio.show', compact('convenio'));
    }
}