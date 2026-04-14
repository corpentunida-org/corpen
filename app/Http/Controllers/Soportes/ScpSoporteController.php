<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use App\Models\Archivo\GdoCargo;
use App\Models\Creditos\LineaCredito;
use App\Models\Maestras\MaeTerceros;
use App\Models\Soportes\ScpCategoria;
use App\Models\Soportes\ScpEstado;
use App\Models\Soportes\ScpObservacion;
use App\Models\Soportes\ScpPrioridad;
use App\Models\Soportes\ScpSoporte;
use App\Models\Soportes\ScpSubTipo;
use App\Models\Soportes\ScpTipo;
use App\Models\Soportes\ScpTipoObservacion;
use App\Models\Soportes\ScpUsuario;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Cinco\Terceros;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;
use App\Mail\SoporteEscaladoMail;

class ScpSoporteController extends Controller
{
    public function index()
    {
        $this->cerrarTicketAutomatico();
        $categoriaActivaPorDefecto = 'SinAsignar'; // Puedes cambiar esto a 'Pendiente', 'Cerrado', etc.

        $soportes = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'maeTercero', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte'])
            ->orderBy('created_at', 'desc')
            ->get();

        $categorias = $soportes->sortBy('id')->groupBy(function ($soporte) {
            return $soporte->estadoSoporte->nombre ?? 'Sin Categoría';
        });

        if (!$categorias->has($categoriaActivaPorDefecto)) {
            $categoriaActivaPorDefecto = $categorias->keys()->first();
        }

        return view('soportes.soportes.index', compact('categorias', 'categoriaActivaPorDefecto', 'soportes'));
    }
    public function cerrarTicketAutomatico()
    {
        $fechaLimite = Carbon::now()->subDays(5);
        $soportes = ScpSoporte::where('scp_soportes.estado', 3)
            ->whereExists(function ($query) use ($fechaLimite) {
                $query
                    ->selectRaw(1)
                    ->from('scp_observaciones as obs')
                    ->whereColumn('obs.id_scp_soporte', 'scp_soportes.id')
                    ->where('obs.id_scp_estados', 3)
                    ->whereRaw(
                        'obs.created_at = (
                SELECT MAX(created_at)
                FROM scp_observaciones
                WHERE id_scp_soporte = scp_soportes.id
                AND id_scp_estados = 3
            )',
                    )
                    ->whereDate('obs.created_at', '<=', $fechaLimite);
            })
            ->get();
        foreach ($soportes as $soporte) {
            $soporte->estado = 4;
            $soporte->save();
            $observacionData = [
                'observacion' => 'Cierre automático del soporte después de 5 días en estado "En Revisión".',
                'timestam' => now(),
                'id_scp_soporte' => $soporte->id,
                'id_scp_estados' => 4,
                'id_tipo_observacion' => 1,
                'calcification' => 5,
            ];
            ScpObservacion::create($observacionData);
        }
    }
    public function create()
    {
        $categorias = ScpCategoria::all();
        $tipos = ScpTipo::all();
        $prioridades = ScpPrioridad::all();
        $terceros = MaeTerceros::select('cod_ter', 'nom_ter')->get();
        $usuarios = User::select('id', 'name')->get();
        $cargos = GdoCargo::select('id', 'nombre_cargo')->get();
        $lineas = LineaCredito::select('id', 'nombre')->get();

        $usuario = User::find(Auth::id());

        // 🔹 Calcular el próximo ID de soporte
        $ultimo = ScpSoporte::max('id');
        $proximoId = $ultimo ? $ultimo + 1 : 1;

        // 🔹 Enviar $proximoId a la vista
        return view(
            'soportes.soportes.create',
            compact(
                'categorias',
                'tipos',
                'prioridades',
                'terceros',
                'usuarios',
                'cargos',
                'lineas',
                'usuario',
                'proximoId', // 👈 este es nuevo
            ),
        );
    }

    // ==========================
    // VER SOPORTE (form)
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'detalles_soporte' => 'required|string',
            'id_gdo_cargo' => 'nullable|integer|exists:gdo_cargo,id',
            'id_cre_lineas_creditos' => 'nullable|integer|exists:cre_lineas_creditos,id',
            'cod_ter_maeTercero' => ['nullable', 'string', 'max:20', Rule::exists('MaeTerceros', 'cod_ter')],
            'id_categoria' => 'required|exists:scp_categorias,id',
            'id_scp_tipo' => 'required|exists:scp_tipos,id',
            'id_scp_prioridad' => 'required|exists:scp_prioridads,id',
            'id_users' => 'required|exists:users,id',
            'id_scp_sub_tipo' => 'required|exists:scp_sub_tipos,id',
            'soporte' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:10240',
            'usuario_escalado' => 'nullable|string|max:100',
        ]);

        $data = [
            'detalles_soporte' => $request->detalles_soporte,
            'timestam' => now(),
            'id_gdo_cargo' => $request->id_gdo_cargo,
            'id_cre_lineas_creditos' => $request->id_cre_lineas_creditos,
            'cod_ter_maeTercero' => $request->cod_ter_maeTercero,
            'id_categoria' => $request->id_categoria,
            'id_scp_tipo' => $request->id_scp_tipo,
            'id_scp_prioridad' => $request->id_scp_prioridad,
            'id_users' => $request->id_users,
            'id_scp_sub_tipo' => $request->id_scp_sub_tipo,
            'estado' => 1,
            'usuario_escalado' => $request->usuario_escalado,
        ];

        // ✅ Subir archivo directamente a S3 con ruta estructurada
        if ($request->hasFile('soporte')) {
            $file = $request->file('soporte');

            // Limpiar nombre del archivo
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $cleanName = Str::slug($originalName, '-'); // elimina espacios y caracteres raros

            // Estructura de carpeta (ejemplo: corpentunida/soportes/usuario_5/2025/10/)
            $userId = Auth::id();
            $year = now()->year;
            $month = now()->format('m');
            $filename = time() . '_' . $cleanName . '.' . $extension;
            $folderPath = "corpentunida/soportes/usuario_{$userId}/{$year}/{$month}";

            // Guardar en S3 (privado)
            $path = Storage::disk('s3')->putFileAs($folderPath, $file, $filename);

            // Guardamos solo el path en la BD
            $data['soporte'] = $path;
        }

        ScpSoporte::create($data);

        return redirect()->route('soportes.soportes.index')->with('success', 'Soporte creado exitosamente.');
    }

    // ==========================
    // 👀 VER SOPORTE (solo abrir)
    // ==========================
    public function verSoporte($id)
    {
        $soporte = ScpSoporte::findOrFail($id);

        if (!$soporte->soporte || !Storage::disk('s3')->exists($soporte->soporte)) {
            return back()->with('error', 'Archivo no disponible o no existe en S3.');
        }

        try {
            /** @var \Illuminate\Filesystem\AwsS3V3Adapter|\Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('s3');

            $urlTemporal = $disk->temporaryUrl($soporte->soporte, now()->addMinutes(5));

            return redirect($urlTemporal);
        } catch (\Exception $e) {
            // 🔴 Capturar error de permisos o región y mostrar mensaje
            return back()->with('error', 'No se pudo acceder al archivo en S3: ' . $e->getMessage());
        }
    }

    // ============================
    // ⬇️ DESCARGAR SOPORTE PRIVADO
    // ============================
    public function descargarSoporte($id)
    {
        $soporte = ScpSoporte::findOrFail($id);

        if (!$soporte->soporte) {
            abort(404, 'Archivo no encontrado.');
        }

        /** @var \Illuminate\Filesystem\AwsS3V3Adapter|\Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        if (!$disk->exists($soporte->soporte)) {
            return back()->with('error', 'Archivo no disponible.');
        }

        // ✅ Genera URL temporal válida 2 minutos para descargar
        $urlTemporal = $disk->temporaryUrl($soporte->soporte, now()->addMinutes(2));

        return redirect($urlTemporal);
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
                $query->with(['estado', 'usuario', 'tipoObservacion', 'scpUsuarioAsignado.maeTercero'])->orderBy('timestam', 'desc'); // 👈 asegúrate que el campo exista
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
        $terceros = MaeTerceros::select('cod_ter', 'nom_ter')->get();
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

        return view('soportes.soportes.edit', compact('scpSoporte', 'categorias', 'tipos', 'prioridades', 'terceros', 'usuarios', 'cargos', 'lineas', 'estados', 'tiposObservacion', 'usuario'));
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
        return redirect()->route('soportes.soportes.index')->with('success', 'Prioridad del soporte actualizada correctamente.');
    }

    /* REVISAR - dd($request->all()); */
    public function storeObservacion(Request $request, ScpSoporte $scpSoporte)
    {
        $request->validate([
            'observacion' => 'required|string',
            'id_scp_estados' => 'required|exists:scp_estados,id',
            'id_tipo_observacion' => 'required|exists:scp_tipo_observacions,id',
            'id_scp_usuario_asignado' => ['nullable', 'integer', 'exists:scp_usuarios,id'],
            'calcification' => ['nullable', 'integer', 'min:1', 'max:5'], // Validación para la calificación
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

        if ($request->has('calcification')) {
            $observacionData['calcification'] = $request->calcification;
        }

        $scpSoporte->observaciones()->create($observacionData);

        $updateData = [
            'estado' => $request->id_scp_estados,
        ];

        if ($request->filled('id_scp_usuario_asignado') && $request->input('id_scp_usuario_asignado') != '0') {
            $updateData['usuario_escalado'] = $request->input('id_scp_usuario_asignado');
        }

        $scpSoporte->update($updateData);
        $esEscalamiento = $request->id_tipo_observacion == 3;

        // ================================
        //   LÓGICA DE ENVÍO DE CORREOS
        // ================================c

        if ($esEscalamiento) {
            $tipoObservacion = ScpTipoObservacion::find($request->id_tipo_observacion);
            if ($tipoObservacion && strtolower(trim($tipoObservacion->nombre)) === 'escalamiento') {
                if ($request->filled('id_scp_usuario_asignado') && $request->id_scp_usuario_asignado != 0) {
                    $usuarioEscalado = ScpUsuario::with('UserApp')->find($request->id_scp_usuario_asignado);
                    if ($usuarioEscalado && $usuarioEscalado->UserApp && !empty($usuarioEscalado->UserApp->email)) {
                        Mail::to($usuarioEscalado->UserApp->email)->send(new SoporteEscaladoMail($scpSoporte, 'escalado'));
                    }
                }
                if ($scpSoporte->usuario && !empty($scpSoporte->usuario->email)) {
                    Mail::to($scpSoporte->usuario->email)->send(new SoporteEscaladoMail($scpSoporte, 'creador'));
                }
            }
        }
        // ================================

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

    // ==========================
    // NOTIFICACIONES
    // ==========================
    public function getNotificaciones()
    {
        $userId = Auth::id();
        $usuarioEscalado = ScpUsuario::where('usuario', $userId)->first();

        // Solo contar soportes que no están cerrados para la campana
        $total = ScpSoporte::query()
            ->where('estado', '!=', '4') // Excluir cerrados (ID=4)
            ->when(
                $usuarioEscalado,
                function ($q) use ($usuarioEscalado, $userId) {
                    $q->where('id_users', $userId)->orWhere('usuario_escalado', $usuarioEscalado->id);
                },
                function ($q) use ($userId) {
                    $q->where('id_users', $userId);
                },
            )
            ->count();

        return response()->json(['total' => $total]);
    }

    public function getNotificacionesDetalladas()
    {
        if (!request()->ajax()) {
            abort(403);
        }

        $userId = Auth::id();

        $usuarioEscaladoId = ScpUsuario::where('usuario', $userId)->value('id');

        $query = ScpSoporte::query()->where(function ($q) use ($userId, $usuarioEscaladoId) {
            $q->where('id_users', $userId);

            if ($usuarioEscaladoId) {
                $q->orWhere('usuario_escalado', $usuarioEscaladoId);
            }
        });

        // SOLO contadores (muy rápido)
        $counts = ScpSoporte::where(function ($q) use ($userId, $usuarioEscaladoId) {
            $q->where('id_users', $userId);

            if ($usuarioEscaladoId) {
                $q->orWhere('usuario_escalado', $usuarioEscaladoId);
            }
        })
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // SOLO últimos soportes
        $soportes = $query
            ->with(['estadoSoporte:id,nombre', 'prioridad:id,nombre', 'usuario:id,name'])
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'id_users', 'detalles_soporte', 'estado', 'id_scp_prioridad', 'updated_at'])
            ->groupBy('estado');

        return response()->json([
            'sinAsignar_count' => $counts[1] ?? 0,
            'enProceso_count' => $counts[2] ?? 0,
            'revision_count' => $counts[3] ?? 0,
            'cerrados_count' => $counts[4] ?? 0,
            'total' => ($counts[1] ?? 0) + ($counts[2] ?? 0) + ($counts[3] ?? 0),

            'sinAsignar' => isset($soportes[1]) ? $soportes[1]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'enProceso' => isset($soportes[2]) ? $soportes[2]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'revision' => isset($soportes[3]) ? $soportes[3]->map(fn($s) => $this->formatearSoporte($s)) : [],
            'cerrados' => isset($soportes[4]) ? $soportes[4]->map(fn($s) => $this->formatearSoporte($s)) : [],
        ]);
    }

    // Método auxiliar para formatear soportes
    private function formatearSoporte($soporte)
    {
        $prioridad = $soporte->prioridad->nombre ?? 'Baja';
        $color = match ($prioridad) {
            'Alta' => 'danger',
            'Media' => 'warning',
            'Baja' => 'primary',
            default => 'gray',
        };

        // Determinar el nombre del estado según el ID
        $estadoNombre = match ($soporte->estado) {
            '1' => 'Sin Asignar',
            '2' => 'En Proceso',
            '3' => 'En Revisión',
            '4' => 'Cerrado',
            default => $soporte->estadoSoporte->nombre ?? 'Sin Estado',
        };

        return [
            'id' => $soporte->id,
            'usuario_nombre' => $soporte->usuario->nombre_corto ?? '',
            'detalles_soporte' => Str::limit($soporte->detalles_soporte, 80),
            'prioridad' => $prioridad,
            'estado' => $estadoNombre,
            'estado_id' => $soporte->estado,
            'prioridad_color' => $color,
            'fecha_creacion' => Carbon::parse($soporte->updated_at)->diffForHumans(),
            'cerrado' => $soporte->estado == '4',
        ];
    }

    // ==========================
    // POR REVISAR
    // ==========================

    public function pendientes()
    {
        $soportesPendientes = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'maeTercero', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte'])
            ->whereHas('estadoSoporte', function ($q) {
                $q->where('nombre', 'Pendiente');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.pendientes', compact('soportesPendientes'));
    }
    public function sinAsignar()
    {
        $soportesSinAsignar = ScpSoporte::with(['tipo', 'subTipo', 'prioridad', 'maeTercero', 'usuario', 'cargo.gdoArea', 'lineaCredito', 'scpUsuarioAsignado.maeTercero', 'estadoSoporte', 'categoria'])
            ->whereHas('categoria', function ($q) {
                $q->where('nombre', 'SinAsignar');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('soportes.soportes.categorias.sinAsignar', compact('soportesSinAsignar'));
    }
}
