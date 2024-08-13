<?php

namespace App\Http\Controllers;

use App\Models\auditoria;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    public function create($action){
        $now = Carbon::now();
        auditoria::create([
            'fechaRegistro' => $now->toDateString(),
            'horaRegistro' => $now->toTimeString(),
            'usuario'=> Auth::user()->name,
            'accion'=> $action,
        ]);
    }
}
