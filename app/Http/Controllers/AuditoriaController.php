<?php

namespace App\Http\Controllers;

use App\Models\auditoria;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    public function create($action,$area){
        $now = Carbon::now();
        auditoria::create([
            'fechaRegistro' => $now->toDateString(),
            'horaRegistro' => $now->toTimeString(),
            'usuario'=> Auth::user()->name,
            'accion'=> $action,
            'area'=> $area
        ]);
    }

    public function index(){
        $registros = auditoria::orderBy('id', 'desc')->get();
        return view('admin.users.usuarios', compact('registros'));
    }
}
