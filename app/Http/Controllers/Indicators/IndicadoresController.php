<?php

namespace App\Http\Controllers\Indicators;

use App\Http\Controllers\Controller;
use App\Models\Indicators\IndIndicadores;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IndicadoresController extends Controller
{
    public function index()
    {
        $indicators = IndIndicadores::all();
        return view('indicators.index', compact('indicators'));
    }
}