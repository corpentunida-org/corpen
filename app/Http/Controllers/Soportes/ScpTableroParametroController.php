<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpUsuario;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\Soportes\ScpCategoria;

class ScpTableroParametroController extends Controller
{
    public function index()
    {
        $usuarios = ScpUsuario::with('maeTercero')->paginate(5); // 👈 usuarios que asignan soporte
        $categorias = ScpCategoria::paginate(5);
        $tipos = ScpTipo::paginate(4);
        $subTipos = ScpSubTipo::paginate(4);
        $estados = ScpEstado::paginate(5);
        $prioridades = ScpPrioridad::paginate(3);
        $tiposObservacion = ScpTipoObservacion::paginate(3);

        return view('soportes.tablero-parametros', compact(
            'usuarios',
            'categorias',
            'tipos',
            'subTipos',
            'estados',
            'prioridades',
            'tiposObservacion'
        ));
    }
}
