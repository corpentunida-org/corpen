<?php

namespace App\Http\Controllers\Maestras;

use App\Http\Controllers\Controller;
use App\Models\Maestras\MaeMunicipios;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaeMunicipiosController extends Controller
{
    public function listadepartamentos($regionId)
    {
        return DB::table('MaeDepartamentos')->where('id_region', $regionId)->orderBy('nombre')->get();
    }

    public function listamunicipios($departamentoId)
    {        
        return DB::table('MaeMunicipios')->where('id_departamento', $departamentoId)->orderBy('nombre')->get();
    }
}
