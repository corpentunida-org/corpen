<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpTipoObservacion;

class ScpTableroParametroController extends Controller
{
    public function index()
    {
        $estados = ScpEstado::paginate(4);
        $prioridades = ScpPrioridad::paginate(3);
        $tipos = ScpTipo::paginate(4);
        $tiposObservacion = ScpTipoObservacion::paginate(3);

        return view('soportes.tablero-parametros', compact(
            'estados',
            'prioridades',
            'tipos',
            'tiposObservacion'
        ));
    }
}
