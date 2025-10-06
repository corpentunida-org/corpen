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
use App\Models\Soportes\ScpCategoria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ScpSoporteController extends Controller
{
    public function index()
    {       
        $categoriaActivaPorDefecto = 'SinAsignar'; // Puedes cambiar esto a 'Pendiente', 'Cerrado', etc.

        $soportes = ScpSoporte::with([
            'tipo',
            'subTipo',
            'prioridad',
            'maeTercero',
            'usuario',
            'cargo.gdoArea',
            'lineaCredito',
            'scpUsuarioAsignado.maeTercero',
            'estadoSoporte'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $categorias = $soportes
            ->sortBy('id')
            ->groupBy(function ($soporte) {
                return $soporte->estadoSoporte->nombre ?? 'Sin Categoría';
            });
        
        
            
            //dd($categorias);
            if (!$categorias->has($categoriaActivaPorDefecto)) {
            $categoriaActivaPorDefecto = $categorias->keys()->first();
        }
        
        return view('soportes.soportes.index', compact('categorias', 'categoriaActivaPorDefecto', 'soportes'));
    }


    public function create()
    {
        $categorias = ScpCategoria::all();
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = maeTerceros::select('cod_ter', 'nom_ter')->get();
        $usuarios = User::select('id', 'name')->get();
        $cargos = GdoCargo::select('id', 'nombre_cargo')->get();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $usuario = User::find(Auth::id());

        return view('soportes.soportes.create', compact('categorias', 'tipos', 'prioridades', 'terceros', 'usuarios', 'cargos', 'lineas', 'usuario'));
    }

public function store(Request $request)
{
    $request->validate([
        'detalles_soporte'       => 'required|string|max:255',
        'id_gdo_cargo'           => 'nullable|integer|exists:gdo_cargo,id',
        'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
        'cod_ter_maeTercero'     => ['nullable', 'string', 'max:20', Rule::exists('MaeTerceros', 'cod_ter')],
        'id_categoria'           => 'required|exists:scp_categorias,id',
        'id_scp_tipo'            => 'required|exists:scp_tipos,id',
        'id_scp_prioridad'       => 'required|exists:scp_prioridads,id',
        'id_users'               => 'required|exists:users,id',
        'id_scp_sub_tipo'        => 'required|exists:scp_sub_tipos,id',
        'estado'                 => 'nullable|string|max:50',
        'soporte'                => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240', // <── Aquí validamos el archivo
        'usuario_escalado'       => 'nullable|string|max:100',
    ]);

    $data = [
        'detalles_soporte'       => $request->detalles_soporte,
        'timestam'               => now(),
        'id_gdo_cargo'           => $request->id_gdo_cargo,
        'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
        'cod_ter_maeTercero'     => $request->cod_ter_maeTercero,
        'id_categoria'           => $request->id_categoria,
        'id_scp_tipo'            => $request->id_scp_tipo,
        'id_scp_prioridad'       => $request->id_scp_prioridad,
        'id_users'               => $request->id_users,
        'id_scp_sub_tipo'        => $request->id_scp_sub_tipo,
        'estado'                 => 1,
        'usuario_escalado'       => $request->usuario_escalado,
    ];

    // ✅ GUARDAR ARCHIVO EN storage/app/soportes
    if ($request->hasFile('soporte')) {
        $file = $request->file('soporte');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('soportes', $filename); // guarda en storage/app/soportes
        $data['soporte'] = basename($path); // guarda solo el nombre del archivo en la BD
    }

    ScpSoporte::create($data);

    return redirect()->route('soportes.soportes.index')->with('success', 'Soporte creado exitosamente.');
}


    public function show(ScpSoporte $scpSoporte)
    {
        $scpSoporte->load([
            'tipo',
            'subTipo',
            'prioridad',
            'maeTercero',
            'usuario',
            'cargo',
            'lineaCredito',
            'observaciones' => function ($query) {
                $query
                    ->with([
                        'estado',
                        'usuario',
                        'tipoObservacion',
                        'scpUsuarioAsignado.maeTercero',
                    ])
                    ->orderBy('timestam', 'desc');
            },
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
        $categorias = ScpCategoria::all(); // ← agrega esto
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

        return view('soportes.soportes.edit', compact(
            'scpSoporte', 'categorias', 'tipos', 'prioridades', 'terceros', 
            'usuarios', 'cargos', 'lineas', 'estados', 'tiposObservacion', 'usuario'
        ));
    }
    
    public function update(Request $request, $id)
    {
        // Validar únicamente la prioridad
        $request->validate([
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
        ]);

        // Buscar el soporte
        $soporte = ScpSoporte::findOrFail($id);

        // Actualizar la prioridad
        $soporte->id_scp_prioridad = $request->id_scp_prioridad;
        $soporte->save();

        // Redirigir con mensaje
        return redirect()
            ->route('soportes.soportes.index')
            ->with('success', 'Prioridad del soporte actualizada correctamente.');
    }

    public function storeObservacion(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observacions,id',
            'id_scp_usuario_asignado' => ['nullable', 'integer', 'exists:scp_usuarios,id'],
        ]);

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

        $updateData = [
            'estado' => $request->id_scp_estados,
        ];

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

    public function getSubTipos($tipoId)
    {
        $subTipos = ScpSubTipo::where('scp_tipo_id', $tipoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($subTipos);
    }

    public function getTiposByCategoria($categoriaId)
    {
        try {
            $tipos = ScpTipo::where('id_categoria', $categoriaId)
                ->orderBy('nombre')
                ->get(['id', 'nombre']);

            return response()->json($tipos);
        } catch (\Exception $e) {
            Log::error('Error en getTiposByCategoria: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }


public function verSoporte($id)
{
    $soporte = ScpSoporte::findOrFail($id);

    $ruta = 'soportes/' . $soporte->soporte;

    if (!$soporte->soporte || !Storage::exists($ruta)) {
        abort(404, 'Archivo no encontrado.');
    }

    return response()->file(storage_path('app/' . $ruta));
}

public function descargarSoporte($id)
{
    $soporte = ScpSoporte::findOrFail($id);

    $ruta = 'soportes/' . $soporte->soporte;

    if (!$soporte->soporte || !Storage::exists($ruta)) {
        abort(404, 'Archivo no encontrado.');
    }

    return response()->download(storage_path('app/' . $ruta));
}











    public function pendientes()
    {
        $soportesPendientes = ScpSoporte::with([
            'tipo',
            'subTipo',
            'prioridad',
            'maeTercero',
            'usuario',
            'cargo.gdoArea',
            'lineaCredito',
            'scpUsuarioAsignado.maeTercero',
            'estadoSoporte'
        ])
            ->whereHas('estadoSoporte', function ($q) {
                $q->where('nombre', 'Pendiente');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.pendientes', compact('soportesPendientes'));
    }
    public function sinAsignar()
    {
        $soportesSinAsignar = ScpSoporte::with([
            'tipo',
            'subTipo',
            'prioridad',
            'maeTercero',
            'usuario',
            'cargo.gdoArea',
            'lineaCredito',
            'scpUsuarioAsignado.maeTercero',
            'estadoSoporte',
            'categoria'
        ])
            ->whereHas('categoria', function ($q) {
                $q->where('nombre', 'SinAsignar');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.sinAsignar', compact('soportesSinAsignar'));
    }
}
