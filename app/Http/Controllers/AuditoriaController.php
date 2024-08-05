<?php

namespace App\Http\Controllers;

use App\Models\auditoria;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    public function create($action){
        auditoria::create([
            'fechaRegistro' => Carbon::now()->toDateString(), // Obtiene la fecha actual en formato 'Y-m-d'
            'horaRegistro' => Carbon::now()->toTimeString(),
            'usuario'=> Auth::name(),
            'accion'=> $action,
        ]);
    }
}
