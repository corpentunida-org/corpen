<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Trd;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Correspondencia\Estado;
use App\Models\Correspondencia\Proceso;
use App\Models\Maestras\maeTerceros;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;

class CorrespondenciaController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }
    /**
     * Listado general (CRUD estándar)
     */
    public function index(Request $request)
    {
        $estados = Estado::all();

        $query = Correspondencia::with(['trd', 'flujo', 'estado', 'usuario', 'remitente']);

        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        if ($request->filled('search')) {
            $query->where('asunto', 'LIKE', '%' . $request->search . '%')->orWhere('id_radicado', 'LIKE', '%' . $request->search . '%');
        }

        $correspondencias = $query->paginate(15);

        return view('correspondencia.correspondencias.index', compact('correspondencias', 'estados'));
    }

    public function create()
    {
        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = maeTerceros::all();

        $ultimoId = Correspondencia::max('id_radicado');
        $siguienteId = $ultimoId ? (int) $ultimoId + 1 : 1;

        return view('correspondencia.correspondencias.create', compact('trds', 'flujos', 'estados', 'remitentes', 'siguienteId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_radicado' => 'required|string|unique:corr_correspondencia,id_radicado',
            'fecha_solicitud' => 'required',
            'asunto' => 'required|string|max:500',
            'medio_recibido' => 'required|string',
            'remitente_id' => 'required',
            'trd_id' => 'required',
            'flujo_id' => 'required',
            'estado_id' => 'required',
            'observacion_previa' => 'nullable|string',
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data['usuario_id'] = auth()->id();
        $data['es_confidencial'] = $request->boolean('es_confidencial');
        $data['finalizado'] = $request->boolean('finalizado');

        try {
            if ($request->hasFile('documento_arc')) {
                $file = $request->file('documento_arc');
                $fileName = 'rad' . $data['id_radicado'] . '_' . time() . '.' . $file->extension();
                $path = 'corpentunida/correspondencia/' . $fileName;

                Storage::disk('s3')->put($path, file_get_contents($file));
                $data['documento_arc'] = $path;
            }
            Correspondencia::create($data);
            $this->auditoria('ADD CORRESPONDENCIA ID ' . $data->id_radicado);
            return redirect()
                ->route('correspondencia.correspondencias.index')
                ->with('success', 'Radicado ' . $data['id_radicado'] . ' creado con éxito.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al insertar en DB: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el detalle del radicado, carga el Flujo y Procesos específicos
     */
    public function show($id)
    {
        // 1. Buscamos la correspondencia con sus relaciones
        $correspondencia = Correspondencia::with(['procesos.usuario', 'procesos.proceso.flujo', 'trd', 'estado', 'remitente', 'flujo'])->findOrFail($id);

        $flujo = null;
        $procesos_disponibles = collect();

        if ($correspondencia->flujo_id) {
            $flujo = FlujoDeTrabajo::with(['usuario'])->find($correspondencia->flujo_id);

            // 2. CORRECCIÓN: Eliminamos ->orderBy('orden') para evitar el error 1054
            $procesos_disponibles = Proceso::where('flujo_id', $correspondencia->flujo_id)
                ->with(['flujo', 'usuariosAsignados.usuario'])
                ->get(); // Ahora cargará sin errores
        } else {
            $procesos_disponibles = Proceso::with(['flujo', 'usuariosAsignados.usuario'])->get();
        }

        return view('correspondencia.correspondencias.show', compact('correspondencia', 'procesos_disponibles', 'flujo'));
    }

    public function edit(Correspondencia $correspondencia)
    {
        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = maeTerceros::all();

        return view('correspondencia.correspondencias.edit', compact('correspondencia', 'trds', 'flujos', 'estados', 'remitentes'));
    }

    public function update(Request $request, Correspondencia $correspondencia)
    {
        $data = $request->validate([
            'fecha_solicitud' => 'required|date',
            'asunto' => 'required|string|max:500',
            'medio_recibido' => 'required|string',
            'remitente_id' => 'required',
            'trd_id' => 'required',
            'flujo_id' => 'required',
            'estado_id' => 'required',
            'observacion_previa' => 'nullable|string',
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data['es_confidencial'] = $request->boolean('es_confidencial');
        $data['finalizado'] = $request->boolean('finalizado');

        if ($request->hasFile('documento_arc')) {
            if ($correspondencia->documento_arc) {
                Storage::disk('s3')->delete($correspondencia->documento_arc);
            }

            $file = $request->file('documento_arc');
            $fileName = 'rad' . $correspondencia->id_radicado . '_' . time() . '.' . $file->extension();
            $path = 'corpentunida/correspondencia/' . $fileName;

            Storage::disk('s3')->put($path, file_get_contents($file));
            $data['documento_arc'] = $path;
        }

        $correspondencia->update($data);
        $this->auditoria('UPDATE CORRESPONDENCIA ID ' . $correspondencia->id_radicado);

        return redirect()->route('correspondencia.correspondencias.index')->with('success', 'Actualizado correctamente');
    }

    public function destroy(Correspondencia $correspondencia)
    {
        if ($correspondencia->documento_arc) {
            Storage::disk('s3')->delete($correspondencia->documento_arc);
        }
        $correspondencia->delete();
        $this->auditoria('DELETE CORRESPONDENCIA ID ' . $correspondencia->id_radicado);

        return redirect()->route('correspondencia.correspondencias.index')->with('success', 'Correspondencia eliminada correctamente');
    }

    /**
     * Tablero de Control y Dashboard
     */
    public function tablero(Request $request)
    {
        $activeTab = $request->filled('search') || $request->filled('page') || $request->filled('estado_id') || $request->filled('usuario_id') || $request->filled('condicion') ? 'gestion' : 'dashboard';

        $searchWords = $request->filled('search') ? array_filter(explode(' ', $request->search)) : [];

        $query = Correspondencia::with(['trd', 'flujo', 'estado', 'usuario']);

        if (!empty($searchWords)) {
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->orWhere('id_radicado', 'LIKE', '%' . $word . '%')->orWhere('asunto', 'LIKE', '%' . $word . '%');
                }
            });
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('condicion')) {
            if ($request->condicion == 'vencido') {
                $query
                    ->whereHas('trd', function ($q) {
                        $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()');
                    })
                    ->where('finalizado', false);
            } elseif ($request->condicion == 'a_tiempo') {
                $query
                    ->whereHas('trd', function ($q) {
                        $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) >= NOW()');
                    })
                    ->where('finalizado', false);
            }
        }

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(10);

        // KPIs
        $estados = Estado::withCount('correspondencias')->get();
        $totalCorrespondencias = $estados->sum('correspondencias_count');

        // Charts
        $chartDistribucion = [
            'labels' => $estados->pluck('nombre'),
            'data' => $estados->pluck('correspondencias_count'),
        ];

        $usuariosCarga = User::withCount([
            'correspondencias' => function ($q) {
                $q->where('finalizado', false);
            },
        ])
            ->orderBy('correspondencias_count', 'desc')
            ->take(5)
            ->get();

        $chartCarga = [
            'labels' => $usuariosCarga->pluck('name'),
            'data' => $usuariosCarga->pluck('correspondencias_count'),
        ];

        $usuarios = User::orderBy('name')->get();

        return view('correspondencia.tablero.index', compact('correspondencias', 'estados', 'totalCorrespondencias', 'usuarios', 'activeTab', 'chartDistribucion', 'chartCarga'));
    }

    public function getTrdsByFlujo($flujo_id)
    {
        $trds = Trd::where('fk_flujo', $flujo_id)->get(['id_trd', 'serie_documental', 'tiempo_gestion']);
        return response()->json($trds);
    }
}
