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
        $usuarios = ScpUsuario::with('maeTercero')->paginate(5, ['*'], 'usuarios_page');
        $categorias = ScpCategoria::paginate(5, ['*'], 'categorias_page');
        $tipos = ScpTipo::paginate(5, ['*'], 'tipos_page');
        $subTipos = ScpSubTipo::paginate(5, ['*'], 'subtipos_page');
        $estados = ScpEstado::paginate(5, ['*'], 'estados_page');
        $prioridades = ScpPrioridad::paginate(5, ['*'], 'prioridades_page');
        $tiposObservacion = ScpTipoObservacion::paginate(5, ['*'], 'observaciones_page');

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
