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
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return view('admin.users.index');
        } elseif ($user->hasRole('exequial')) {
            return view('exequial.asociados.index');
        } elseif ($user->hasRole('creditos')) {
            return view('exequial.asociados.index');
        } elseif ($user->hasRole('seguros')) {
            return view('seguros.polizas.index');
        } else {
            return redirect('exequial.prestarServicio.index');
        }
    }

}
