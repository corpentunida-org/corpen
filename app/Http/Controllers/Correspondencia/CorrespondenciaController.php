<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Modelos de Correspondencia
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Trd;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Correspondencia\Estado;
use App\Models\Correspondencia\Proceso;
use App\Models\Correspondencia\MedioRecepcion;
// Otros Modelos
use App\Models\Maestras\maeTerceros;
use App\Models\User;
// Soporte de Laravel
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Http\Controllers\AuditoriaController;

class CorrespondenciaController extends Controller
{
    /**
     * Registra acciones en la tabla de auditoría del sistema.
     */
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }

    /**
     * Muestra el listado principal de correspondencias con filtros.
     */
    public function index(Request $request)
    {
        $estados = Estado::all();
        $procesos_disponibles = Proceso::with(['estadosProcesos.estado'])->get();

        $query = Correspondencia::with(['trd', 'flujo', 'estado', 'usuario', 'remitente', 'medioRecepcion'])->latest();

        // 1. FILTRO DE CATEGORÍAS (Esto era lo que te faltaba)
        if ($request->filled('flujo_id')) {
            $query->where('flujo_id', $request->flujo_id);
        }

        // 2. FILTRO POR ESTADO
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        // 3. BUSCADOR MEJORADO (Ahora coincide con la lógica de tu Blade)
        if ($request->filled('search')) {
            $words = explode(' ', $request->search);
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->where(function ($subQ) use ($word) {
                        $subQ->orWhere('id_radicado', 'LIKE', "%$word%")->orWhere('asunto', 'LIKE', "%$word%");
                    });
                }
            });
        }
        if (auth()->user()->hasPermission('correspondencia.lista.todos')) {
            $correspondencias = $query->paginate(15)->appends($request->all());
        } else {
            //$correspondencias = $query->where('remitente_id',auth()->user()->nid)->paginate(15)->appends($request->all());
            $correspondencias = $query
                ->where(function ($q) {
                    $q->where('remitente_id', auth()->user()->nid)->orWhereHas('flujo.procesos.usuariosAsignados', function ($q2) {
                        $q2->where('user_id', auth()->id())->orWhere('usuario_id', auth()->id());
                    });
                })
                ->paginate(15)
                ->appends($request->all());
        }
        $flujos_disponibles = $flujos = $correspondencias->getCollection()->groupBy('flujo_id')->map(function ($items) {
            return ['id' => $items->first()->flujo_id,'nombre' => optional($items->first()->flujo)->nombre,'count' => $items->count(),];})->values();
        return view('correspondencia.correspondencias.index', compact('correspondencias', 'estados', 'procesos_disponibles','flujos_disponibles'));
    }

    /**
     * Prepara el formulario para crear un nuevo radicado.
     */
    public function create()
    {
        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = maeTerceros::all();
        $medios = MedioRecepcion::activos()->get(); // Solo medios con estado activo

        // Generación sugerida del siguiente ID de radicado
        $ultimoId = Correspondencia::max('id_radicado');
        $siguienteId = $ultimoId ? (int) $ultimoId + 1 : 1;

        return view('correspondencia.correspondencias.create', compact('trds', 'flujos', 'estados', 'remitentes', 'medios', 'siguienteId'));
    }

    /**
     * Almacena un nuevo registro en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de datos de entrada
        $data = $request->validate([
            'id_radicado' => 'required|string|unique:corr_correspondencia,id_radicado',
            'fecha_solicitud' => 'required|date',
            'asunto' => 'required|string|max:500',
            'medio_recibido' => 'required|exists:corr_medio_recepcion,id',
            'remitente_id' => 'required',
            'trd_id' => 'required',
            'flujo_id' => 'required',
            'estado_id' => 'required',
            'observacion_previa' => 'nullable|string',
            'final_descripcion' => 'nullable|string', // Validación del nuevo campo
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // 2. Asignación de valores automáticos y booleanos
        $data['usuario_id'] = auth()->id();
        $data['es_confidencial'] = $request->boolean('es_confidencial');
        $data['finalizado'] = $request->boolean('finalizado');

        try {
            // 3. Gestión de archivo en Amazon S3
            if ($request->hasFile('documento_arc')) {
                $file = $request->file('documento_arc');
                $fileName = 'rad' . $data['id_radicado'] . '_' . time() . '.' . $file->extension();
                $path = 'corpentunida/correspondencia/' . $fileName;

                Storage::disk('s3')->put($path, file_get_contents($file));
                $data['documento_arc'] = $path;
            }

            // 4. Creación del registro
            Correspondencia::create($data);

            // 5. Auditoría
            $this->auditoria('ADD CORRESPONDENCIA ID ' . $data['id_radicado']);

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
     * Muestra el detalle completo de un radicado, sus procesos y tiempos.
     */
    public function show($id)
    {
        // 1. Carga la correspondencia con relaciones anidadas, incluyendo 'comunicacionSalida'
        $correspondencia = Correspondencia::with([
            'procesos.usuario',
            'procesos.proceso.flujo',
            'trd',
            'estado',
            'remitente',
            'flujo',
            'medioRecepcion',
            'comunicacionSalida', // <-- RELACIÓN CLAVE: Para ver si ya hay respuesta
        ])->findOrFail($id);

        $flujo = null;
        $procesos_disponibles = collect();

        // 2. Lógica de procesos según el flujo asignado
        if ($correspondencia->flujo_id) {
            $flujo = FlujoDeTrabajo::with(['usuario'])->find($correspondencia->flujo_id);

            $procesos_disponibles = Proceso::where('flujo_id', $correspondencia->flujo_id)
                ->with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])
                ->get();
        } else {
            $procesos_disponibles = Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();
        }

        // 3. DATOS ADICIONALES PARA EL MODAL DE COMUNICACIÓN DE SALIDA
        // Necesitamos la lista de usuarios para elegir el firmante y las plantillas de texto
        $usuarios = User::orderBy('name', 'asc')->get();
        $plantillas = \App\Models\Correspondencia\Plantilla::all();

        // 4. Cálculos de Tiempos de Gestión (KPIs) basados en la TRD
        $tiempoGestion = $correspondencia->trd->tiempo_gestion ?? 0;
        $fechaInicio = Carbon::parse($correspondencia->fecha_solicitud);
        $limite = $fechaInicio->copy()->addYears($tiempoGestion);

        // Evitamos error si la fecha actual ya superó el límite (vencido)
        $diferenciatrd = CarbonInterval::instance(now()->diff($limite));

        // 5. Retornamos la vista con las nuevas variables 'usuarios' y 'plantillas'
        return view(
            'correspondencia.correspondencias.show',
            compact(
                'correspondencia',
                'procesos_disponibles',
                'flujo',
                'diferenciatrd',
                'limite',
                'usuarios', // <-- Para el select de firmantes en el modal
                'plantillas', // <-- Para el select de plantillas en el modal
            ),
        );
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Correspondencia $correspondencia)
    {
        // Cargar ansiosamente la relación 'medioRecepcion'
        $correspondencia->load('medioRecepcion', 'trd', 'flujo', 'estado', 'remitente', 'usuario');

        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = maeTerceros::all();
        $medios = MedioRecepcion::activos()->get();

        return view('correspondencia.correspondencias.edit', compact('correspondencia', 'trds', 'flujos', 'estados', 'remitentes', 'medios'));
    }

    /**
     * Actualiza el registro existente.
     */
    public function update(Request $request, Correspondencia $correspondencia)
    {
        $data['finalizado'] = $request->boolean('finalizado');
        $data['final_descripcion'] = $request->final_descripcion;

        // 4. ACTUALIZAR BASE DE DATOS
        $correspondencia->update($data);

        // 5. AUDITORÍA
        $this->auditoria('FIN CORRESPONDENCIA ID ' . $correspondencia->id_radicado);

        // 6. REDIRECCIÓN CORRECTA AL SHOW
        return redirect()->route('correspondencia.correspondencias.index')->with('success', 'Radicado finalizado correctamente.');
    }

    /**
     * Elimina el registro y su archivo asociado en la nube.
     */
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
     * Genera el tablero de control (Dashboard) con KPIs y gráficas.
     * Corregido para soportar la gestión rápida desde el tablero.
     */
    public function tablero(Request $request)
    {
        // Carga base con relaciones necesarias
        $query = Correspondencia::with(['trd', 'estado', 'usuario', 'flujo.procesos.usuariosAsignados']);

        // Filtros dinámicos
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('id_radicado', 'LIKE', "%{$request->search}%")->orWhere('asunto', 'LIKE', "%{$request->search}%");
            });
        }
        if ($request->filled('flujo_id')) {
            $query->where('flujo_id', $request->flujo_id);
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }
        if ($request->filled('condicion') && $request->condicion == 'vencido') {
            $query->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'))->where('finalizado', false);
        }

        // KPIs (Calculados sobre la base filtrada para coherencia visual)
        $kpis = [
            'total' => (clone $query)->count(),
            'vencidos' => (clone $query)->where('finalizado', false)->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'))->count(),
            'pendientes' => (clone $query)->where('finalizado', false)->count(),
            'finalizados' => (clone $query)->where('finalizado', true)->count(),
        ];

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(15)->appends($request->all());

        // Data para filtros y gráficas
        $flujos = FlujoDeTrabajo::withCount('correspondencias')->get();
        $usuarios = User::orderBy('name')->get();
        $estados = Estado::withCount('correspondencias')->get();

        return view('correspondencia.tablero.index', compact('correspondencias', 'kpis', 'flujos', 'usuarios', 'estados'));
    }

    /**
     * API: Obtiene las TRD vinculadas a un flujo específico (para carga dinámica en vistas).
     */
    public function getTrdsByFlujo($flujo_id)
    {
        $trds = Trd::where('fk_flujo', $flujo_id)->get(['id_trd', 'serie_documental', 'tiempo_gestion']);
        return response()->json($trds);
    }

    public function getRemitenteByCodigo($cod_ter)
    {
        $tercero = maeTerceros::find($cod_ter);
        if (!$tercero) {
            return response()->json(['message' => 'Remitente no encontrado'], 404);
        }
        return response()->json($tercero);
    }
}
