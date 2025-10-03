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
        $usuarios = ScpUsuario::with('maeTercero')->paginate(); // 👈 usuarios que asignan soporte
        $categorias = ScpCategoria::paginate();
        $tipos = ScpTipo::paginate();
        $subTipos = ScpSubTipo::paginate();
        $estados = ScpEstado::paginate();
        $prioridades = ScpPrioridad::paginate();
        $tiposObservacion = ScpTipoObservacion::paginate();

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
