<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\Soportes\ScpObservacion;
use App\Models\Maestras\MaeTerceros;
use App\Models\User;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ScpSoporteController extends Controller
{
    public function index()
    {
        $soportes = ScpSoporte::with(['tipo', 'prioridad', 'maeTercero', 'usuario'])->paginate(10);
        return view('soportes.soportes.index', compact('soportes'));
    }

    public function create()
    {
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = MaeTerceros::select('cod_ter','nom_ter')->get();
        $usuarios = User::select('id','name')->get();
        $cargos = GdoCargo::select('id','nombre_cargo')->get();
        $lineas = LineaCredito::select('id','nombre')->get();

        return view('soportes.soportes.create', compact('tipos', 'prioridades', 'terceros', 'usuarios', 'cargos', 'lineas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'detalles_soporte' => 'required|string|max:255',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'cod_ter_maeTercero' => ['nullable', 'string', 'max:20', Rule::exists('MaeTerceros','cod_ter')],
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
        ]);

        ScpSoporte::create([
            'detalles_soporte' => $request->detalles_soporte,
            'timestam' => now(),
            'id_gdo_cargo' => $request->id_gdo_cargo,
            'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
            'cod_ter_maeTercero' => $request->cod_ter_maeTercero,
            'id_scp_tipo' => $request->id_scp_tipo,
            'id_scp_prioridad' => $request->id_scp_prioridad,
            'id_users' => $request->id_users,
        ]);

        return redirect()->route('soportes.soportes.index')->with('success', 'Soporte creado exitosamente.');
    }

    public function show(ScpSoporte $scpSoporte)
    {
        $scpSoporte->load([
            'tipo',
            'prioridad',
            'MaeTercero',
            'usuario',
            'cargo',
            'lineaCredito',
            'observaciones' => function ($query) {
                $query->with(['estado', 'usuario', 'tipoObservacion'])->orderBy('timestam','desc');
            }
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();

        return view('soportes.soportes.show', [
            'soporte' => $scpSoporte,
            'estados' => $estados,
            'tiposObservacion' => $tiposObservacion,
        ]);
    }

    public function edit(ScpSoporte $scpSoporte)
    {
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = MaeTerceros::select('cod_ter','nom_ter')->get();
        $usuarios = User::select('id','name')->get();
        $cargos = GdoCargo::select('id', 'nombre_cargo')->get();
        $lineas = LineaCredito::select('id','nombre')->get();

        $scpSoporte->load([
            'observaciones' => function ($query) {
                $query->with(['estado','usuario','tipoObservacion'])->orderBy('timestam','desc');
            }
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();

        return view('soportes.soportes.edit', compact(
            'scpSoporte','tipos','prioridades','terceros','usuarios','cargos','lineas','estados','tiposObservacion'
        ));
    }

    public function update(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'detalles_soporte' => 'required|string|max:255',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'cod_ter_maeTercero' => ['nullable','string','max:20', Rule::exists('MaeTerceros','cod_ter')],
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
        ]);

        $scpSoporte->update([
            'detalles_soporte' => $request->detalles_soporte,
            'id_gdo_cargo' => $request->id_gdo_cargo,
            'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
            'cod_ter_maeTercero' => $request->cod_ter_maeTercero,
            'id_scp_tipo' => $request->id_scp_tipo,
            'id_scp_prioridad' => $request->id_scp_prioridad,
            'id_users' => $request->id_users,
        ]);

        return redirect()->route('soportes.soportes.show',$scpSoporte)->with('success','Soporte actualizado exitosamente.');
    }

    public function destroy(ScpSoporte $scpSoporte)
    {
        $scpSoporte->delete();
        return redirect()->route('soportes.soportes.index')->with('success','Soporte eliminado exitosamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos para Observaciones
    |--------------------------------------------------------------------------
    */
    public function storeObservacion(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observacions,id',
        ]);

        $scpSoporte->observaciones()->create([
            'observacion' => $request->observacion,
            'timestam' => now(),
            'id_scp_soporte' => $scpSoporte->id,
            'id_scp_estados' => $request->id_scp_estados,
            'id_users' => Auth::id(),
            'id_tipo_observacion' => $request->id_tipo_observacion,
        ]);

        return redirect()->route('soportes.soportes.show',$scpSoporte)->with('success','Observación añadida exitosamente.');
    }

    public function destroyObservacion(ScpSoporte $scpSoporte, ScpObservacion $scpObservacion)
    {
        if($scpObservacion->id_scp_soporte !== $scpSoporte->id){
            return redirect()->back()->with('error','La observación no pertenece a este soporte.');
        }

        $scpObservacion->delete();
        return redirect()->route('soportes.soportes.show',$scpSoporte)->with('success','Observación eliminada exitosamente.');
    }
}
