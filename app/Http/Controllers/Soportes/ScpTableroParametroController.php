<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpTipoObservacion;

class ScpTableroParametroController extends Controller
{
    public function index()
    {
        $estados = ScpEstado::paginate(5);
        $prioridades = ScpPrioridad::paginate(3);
        $tipos = ScpTipo::paginate(4); // ya existe
        $subTipos = ScpSubTipo::paginate(4); // 👈 nuevo
        $tiposObservacion = ScpTipoObservacion::paginate(3);

        return view('soportes.tablero-parametros', compact(
            'estados',
            'prioridades',
            'tipos',
            'subTipos', // 👈 pasamos a la vista
            'tiposObservacion'
        ));
    }

}
