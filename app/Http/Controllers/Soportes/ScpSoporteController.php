<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\Soportes\ScpObservacion;
use App\Models\Maestras\maeTerceros;
use App\Models\User;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpUsuario;

class ScpSoporteController extends Controller
{
    public function index()
    {
        $soportes = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'maeTercero', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte'])
            ->orderBy('created_at', 'desc')
            ->get(); 
        
        $categorias = $soportes->groupBy(function ($soporte) {
            return $soporte->estadoSoporte->nombre ?? 'Pendiente';
        });

        return view('soportes.soportes.index', compact('categorias'));
    }

    public function create()
    {
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = maeTerceros::select('cod_ter', 'nom_ter')->get();
        $usuarios = User::select('id', 'name')->get();
        $cargos = GdoCargo::select('id', 'nombre_cargo')->get();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $usuario = User::find(Auth::id());

        return view('soportes.soportes.create', compact('tipos', 'prioridades', 'terceros', 'usuarios', 'cargos', 'lineas', 'usuario'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'detalles_soporte' => 'required|string|max:255',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'cod_ter_maeTercero' => ['nullable', 'string', 'max:20', Rule::exists('MaeTerceros', 'cod_ter')],
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
            'id_scp_sub_tipo' => 'required|exists:scp_sub_tipos,id',
            'estado' => 'nullable|string|max:50',
            'soporte' => 'nullable|string|max:255',
            'usuario_escalado' => 'nullable|string|max:100',
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
            'id_scp_sub_tipo' => $request->id_scp_sub_tipo,
            'estado' => $request->estado,
            'soporte' => $request->soporte,
            'usuario_escalado' => $request->usuario_escalado,
        ]);

        return redirect()->route('soportes.soportes.index')->with('success', 'Soporte creado exitosamente.');
    }

    public function show(ScpSoporte $scpSoporte)
    {
        $scpSoporte->load([
            'tipo',
            'subTipo',
            'prioridad',
            'maeTercero', // relación en minúscula como la definiste en el modelo
            'usuario',
            'cargo',
            'lineaCredito',
            'observaciones' => function ($query) {
                $query
                    ->with([
                        'estado',
                        'usuario',
                        'tipoObservacion',
                        'usuarioAsignado', // <-- corregido
                    ])
                    ->orderBy('timestam', 'desc');
            },
            'scpUsuarioAsignado', // este es el escalado guardado en el soporte
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();
        $usuariosEscalamiento = ScpUsuario::with('maeTercero')->get();

        return view('soportes.soportes.show', [
            'soporte' => $scpSoporte,
            'estados' => $estados,
            'tiposObservacion' => $tiposObservacion,
            'usuariosEscalamiento' => $usuariosEscalamiento,
        ]);
    }


    public function edit(ScpSoporte $scpSoporte)
    {
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = maeTerceros::select('cod_ter', 'nom_ter')->get();
        $usuarios = User::select('id', 'name')->get();
        $cargos = GdoCargo::select('id', 'nombre_cargo')->get();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $scpSoporte->load([
            'observaciones' => function ($query) {
                $query->with(['estado', 'usuario', 'tipoObservacion'])->orderBy('timestam', 'desc');
            },
        ]);

        $estados = ScpEstado::all();
        $tiposObservacion = ScpTipoObservacion::all();

        $usuario = User::find(Auth::id());

        return view('soportes.soportes.edit', compact('scpSoporte', 'tipos', 'prioridades', 'terceros', 'usuarios', 'cargos', 'lineas', 'estados', 'tiposObservacion', 'usuario'));
    }

    public function update(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'detalles_soporte' => 'required|string|max:255',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'cod_ter_maeTercero' => ['nullable', 'string', 'max:20', Rule::exists('MaeTerceros', 'cod_ter')],
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
            'id_scp_sub_tipo' => 'required|exists:scp_sub_tipos,id',
            'estado' => 'nullable|string|max:50',
            'soporte' => 'nullable|string|max:255',
            'usuario_escalado' => 'nullable|string|max:100',
        ]);

        $scpSoporte->update([
            'detalles_soporte' => $request->detalles_soporte,
            'id_gdo_cargo' => $request->id_gdo_cargo,
            'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
            'cod_ter_maeTercero' => $request->cod_ter_maeTercero,
            'id_scp_tipo' => $request->id_scp_tipo,
            'id_scp_prioridad' => $request->id_scp_prioridad,
            'id_users' => $request->id_users,
            'id_scp_sub_tipo' => $request->id_scp_sub_tipo,
            'estado' => $request->estado,
            'soporte' => $request->soporte,
            'usuario_escalado' => $request->usuario_escalado,
        ]);

        return redirect()->route('soportes.soportes.show', $scpSoporte)->with('success', 'Soporte actualizado exitosamente.');
    }

    public function destroy(ScpSoporte $scpSoporte)
    {
        $scpSoporte->delete();
        return redirect()->route('soportes.soportes.index')->with('success', 'Soporte eliminado exitosamente.');
    }

    ///////////////////////////////////////////////////////////////////////////////

    public function storeObservacion(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observacions,id',
            'id_scp_usuario_asignado' => ['nullable', 'integer', 'exists:scp_usuarios,id'],
        ]);

        // Guardar la observación
        $observacionData = [
            'observacion' => $request->observacion,
            'timestam' => now(),
            'id_scp_soporte' => $scpSoporte->id,
            'id_scp_estados' => $request->id_scp_estados,
            'id_users' => Auth::id(),
            'id_users_asignado' => $request->id_scp_usuario_asignado ?? null,
            'id_tipo_observacion' => $request->id_tipo_observacion,
        ];

        $scpSoporte->observaciones()->create($observacionData);

        // Siempre actualizamos el estado
        $updateData = [
            'estado' => $request->id_scp_estados,
        ];

        // Solo actualizamos el usuario escalado si viene en la request
        if ($request->filled('id_scp_usuario_asignado')) {
            $updateData['usuario_escalado'] = $request->id_scp_usuario_asignado;
        }

        $scpSoporte->update($updateData);

        return redirect()->route('soportes.soportes.show', $scpSoporte)->with('success', 'Observación añadida y soporte actualizado exitosamente.');
    }


    public function destroyObservacion(ScpSoporte $scpSoporte, ScpObservacion $scpObservacion)
    {
        if ($scpObservacion->id_scp_soporte !== $scpSoporte->id) {
            return redirect()->back()->with('error', 'La observación no pertenece a este soporte.');
        }

        $scpObservacion->delete();
        return redirect()->route('soportes.soportes.show', $scpSoporte)->with('success', 'Observación eliminada exitosamente.');
    }

    ///////////////////////////////////////////////////////////////////////////////
    public function getSubTipos($tipoId)
    {
        $subTipos = ScpSubTipo::where('scp_tipo_id', $tipoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($subTipos);
    }
}
