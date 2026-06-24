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
use App\Models\Maestras\MaeTerceros;
use App\Models\User;
// Soporte de Laravel
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Http\Controllers\AuditoriaController;

use Barryvdh\DomPDF\Facade\Pdf; 

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

        // 1. FILTRO DE CATEGORÍAS
        if ($request->filled('flujo_id')) {
            $query->where('flujo_id', $request->flujo_id);
        }

        // 2. FILTRO POR ESTADO
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        // 3. BUSCADOR MEJORADO
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
            $correspondencias = $query
                ->where(function ($q) {
                    $q->where('remitente_id', auth()->user()->nid)->orWhereHas('flujo.procesos.usuariosAsignados', function ($q2) {
                        $q2->where('user_id', auth()->id())->orWhere('usuario_id', auth()->id());
                    });
                })
                ->paginate(15)
                ->appends($request->all());
        }
        
        $flujos_disponibles = $correspondencias
            ->getCollection()
            ->groupBy('flujo_id')
            ->map(function ($items) {
                return ['id' => $items->first()->flujo_id, 'nombre' => optional($items->first()->flujo)->nombre, 'count' => $items->count()];
            })
            ->values();
            
        return view('correspondencia.correspondencias.index', compact('correspondencias', 'estados', 'procesos_disponibles', 'flujos_disponibles'));
    }

    /**
     * Prepara el formulario para crear un nuevo radicado.
     */
    public function create()
    {
        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = MaeTerceros::all();
        $medios = MedioRecepcion::activos()->get();

        $ultimoId = Correspondencia::max('id_radicado');
        $siguienteId = $ultimoId ? (int) $ultimoId + 1 : 1;

        return view('correspondencia.correspondencias.create', compact('trds', 'flujos', 'estados', 'remitentes', 'medios', 'siguienteId'));
    }

    /**
     * Almacena un nuevo registro en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de datos de entrada (se amplió mimes para soportar imágenes)
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
            'final_descripcion' => 'nullable|string',
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:51200', // Modificado a 50MB (51200 KB) para igualar tu JS
        ]);

        // 2. Asignación de valores automáticos y booleanos
        $data['usuario_id'] = auth()->id();
        $data['es_confidencial'] = $request->boolean('es_confidencial');
        $data['finalizado'] = $request->boolean('finalizado');

        try {
            // 3. Gestión del archivo inicial empaquetado dentro de un array
            if ($request->hasFile('documento_arc')) {
                $file = $request->file('documento_arc');
                $fileName = 'rad' . $data['id_radicado'] . '_' . time() . '.' . $file->extension();
                $path = 'corpentunida/correspondencia/' . $fileName;

                Storage::disk('s3')->put($path, file_get_contents($file));
                
                // Guardamos la ruta adentro de un array limpio
                $data['documento_arc'] = [$path];
            } else {
                $data['documento_arc'] = [];
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
        $correspondencia = Correspondencia::with([
            'procesos.usuario',
            'procesos.proceso.flujo',
            'trd',
            'estado',
            'remitente',
            'flujo',
            'medioRecepcion',
            'comunicacionSalida',
        ])->findOrFail($id);

        $flujo = null;
        $procesos_disponibles = collect();

        if ($correspondencia->flujo_id) {
            $flujo = FlujoDeTrabajo::with(['usuario'])->find($correspondencia->flujo_id);
            $procesos_disponibles = Proceso::where('flujo_id', $correspondencia->flujo_id)
                ->with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])
                ->get();
        } else {
            $procesos_disponibles = Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();
        }

        $usuarios = User::orderBy('name', 'asc')->get();
        $plantillas = \App\Models\Correspondencia\Plantilla::all();

        $tiempoGestion = $correspondencia->trd->tiempo_gestion ?? 0;
        $fechaInicio = Carbon::parse($correspondencia->fecha_solicitud);
        $limite = $fechaInicio->copy()->addYears($tiempoGestion);

        $diferenciatrd = CarbonInterval::instance(now()->diff($limite));

        return view(
            'correspondencia.correspondencias.show',
            compact(
                'correspondencia',
                'procesos_disponibles',
                'flujo',
                'diferenciatrd',
                'limite',
                'usuarios',
                'plantillas',
            ),
        );
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Correspondencia $correspondencia)
    {
        $correspondencia->load('medioRecepcion', 'trd', 'flujo', 'estado', 'remitente', 'usuario');

        $trds = Trd::all();
        $flujos = FlujoDeTrabajo::all();
        $estados = Estado::all();
        $remitentes = MaeTerceros::all();
        $medios = MedioRecepcion::activos()->get();

        return view('correspondencia.correspondencias.edit', compact('correspondencia', 'trds', 'flujos', 'estados', 'remitentes', 'medios'));
    }

    /**
     * Actualiza el registro existente acumulando archivos adjuntos y auditando de manera estricta.
     */
    public function update(Request $request, Correspondencia $correspondencia)
    {
        // Validación del archivo nuevo si es enviado en la edición
        $request->validate([
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:51200',
        ]);

        $data['finalizado'] = $request->boolean('finalizado');
        $data['final_descripcion'] = $request->final_descripcion;

        $mensajeAuditoria = 'ACTUALIZACIÓN CORRESPONDENCIA ID ' . $correspondencia->id_radicado;

        // Gestión del archivo en modo edición sin sobreescribir la historia previa
        if ($request->hasFile('documento_arc')) {
            $file = $request->file('documento_arc');
            $fileName = 'rad' . $correspondencia->id_radicado . '_edit_' . time() . '.' . $file->extension();
            $path = 'corpentunida/correspondencia/' . $fileName;

            Storage::disk('s3')->put($path, file_get_contents($file));

            // El accesor de tu modelo garantiza que esto siempre retorna un array de rutas
            $documentosHistorial = $correspondencia->documento_arc;

            // Anexamos el nuevo elemento al final de la colección
            $documentosHistorial[] = $path;

            // Seteamos el nuevo arreglo
            $data['documento_arc'] = $documentosHistorial;

            $mensajeAuditoria .= ' (SE AGREGÓ UN NUEVO DOCUMENTO ADJUNTO)';
        }

        // Modificación del mensaje si la actualización implica el cierre del radicado
        if ($data['finalizado']) {
            $mensajeAuditoria = 'FIN CORRESPONDENCIA ID ' . $correspondencia->id_radicado;
            if ($request->hasFile('documento_arc')) {
                $mensajeAuditoria .= ' CON ANEXO DE ARCHIVO ADICIONAL';
            }
        }

        // 4. ACTUALIZAR BASE DE DATOS
        $correspondencia->update($data);

        // 5. AUDITORÍA DETALLADA
        $this->auditoria($mensajeAuditoria);

        // 6. REDIRECCIÓN CORRECTA AL INDEX
        return redirect()->route('correspondencia.correspondencias.index')->with('success', 'Radicado actualizado correctamente.');
    }

    /**
     * Elimina el registro y todos sus archivos asociados en S3.
     */
    public function destroy(Correspondencia $correspondencia)
    {
        // Al ser un array mapeado por el accesor, iteramos y limpiamos S3 por completo
        if (!empty($correspondencia->documento_arc)) {
            foreach ($correspondencia->documento_arc as $doc) {
                if ($doc && Storage::disk('s3')->exists($doc)) {
                    Storage::disk('s3')->delete($doc);
                }
            }
        }
        
        $correspondencia->delete();
        $this->auditoria('DELETE CORRESPONDENCIA ID ' . $correspondencia->id_radicado);

        return redirect()->route('correspondencia.correspondencias.index')->with('success', 'Correspondencia eliminada correctamente');
    }

    /**
     * Genera el tablero de control (Dashboard) con KPIs y gráficas filtradas por fechas.
     */
    public function tablero(Request $request)
    {
        $query = Correspondencia::with(['trd', 'estado', 'usuario', 'flujo.procesos.usuariosAsignados']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('id_radicado', 'LIKE', "%{$request->search}%")
                  ->orWhere('asunto', 'LIKE', "%{$request->search}%");
            });
        }
        if ($request->filled('flujo_id')) {
            $query->where('flujo_id', $request->flujo_id);
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('fecha_ini')) {
            $query->whereDate('fecha_solicitud', '>=', Carbon::parse($request->fecha_ini));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_solicitud', '<=', Carbon::parse($request->fecha_fin));
        }

        $kpiQuery = clone $query;
        $kpis = [
            'total'       => (clone $kpiQuery)->count(),
            'vencidos'    => (clone $kpiQuery)->where('finalizado', false)->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'))->count(),
            'pendientes'  => (clone $kpiQuery)->where('finalizado', false)->count(),
            'finalizados' => (clone $kpiQuery)->where('finalizado', true)->count(),
        ];

        if ($request->filled('estado_kpi')) {
            switch ($request->estado_kpi) {
                case 'vencidos':
                    $query->where('finalizado', false)->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'));
                    break;
                case 'pendientes':
                    $query->where('finalizado', false);
                    break;
                case 'finalizados':
                    $query->where('finalizado', true);
                    break;
            }
        }

        if ($request->filled('mes_filtro')) {
            $mesesMap = ['Ene' => 1, 'Feb' => 2, 'Mar' => 3, 'Abr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Ago' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dic' => 12];
            $mesNumero = $mesesMap[$request->mes_filtro] ?? null;
            
            if ($mesNumero) {
                $query->whereMonth('fecha_solicitud', $mesNumero)
                      ->whereYear('fecha_solicitud', date('Y'));
            }
        }

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(15)->appends($request->all());

        $flujos = FlujoDeTrabajo::withCount('correspondencias')->get();
        $usuarios = User::orderBy('name')->get();
        $estados = Estado::withCount('correspondencias')->get();

        return view('correspondencia.tablero.index', compact('correspondencias', 'kpis', 'flujos', 'usuarios', 'estados'));
    }

    /**
     * Genera el reporte PDF tomando exactamente los mismos filtros del request.
     */
    public function generarReportePdf(Request $request)
    {
        $query = Correspondencia::with(['trd', 'estado', 'usuario', 'flujo']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_radicado', 'LIKE', "%{$search}%")
                  ->orWhere('asunto', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('flujo_id')) { 
            $query->where('flujo_id', '=', $request->input('flujo_id')); 
        }
        if ($request->filled('usuario_id')) { 
            $query->where('usuario_id', '=', $request->input('usuario_id')); 
        }
        
        if ($request->filled('fecha_ini')) {
            $query->whereDate('fecha_solicitud', '>=', Carbon::parse($request->fecha_ini));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_solicitud', '<=', Carbon::parse($request->fecha_fin));
        }

        if ($request->filled('estado_kpi')) {
            switch ($request->estado_kpi) {
                case 'vencidos':
                    $query->where('finalizado', false)->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'));
                    break;
                case 'pendientes':
                    $query->where('finalizado', false);
                    break;
                case 'finalizados':
                    $query->where('finalizado', true);
                    break;
            }
        }

        if ($request->filled('mes_filtro')) {
            $mesesMap = ['Ene' => 1, 'Feb' => 2, 'Mar' => 3, 'Abr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Ago' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dic' => 12];
            $mesNumero = $mesesMap[$request->mes_filtro] ?? null;
            if ($mesNumero) {
                $query->whereMonth('fecha_solicitud', $mesNumero)->whereYear('fecha_solicitud', date('Y'));
            }
        }

        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->get();

        $kpiQuery = clone $query;
        $resumenKpis = [
            'Total'       => $kpiQuery->count(),
            'Vencidos'    => (clone $kpiQuery)->where('finalizado', false)->whereHas('trd', fn($q) => $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()'))->count(),
            'En Proceso'  => (clone $kpiQuery)->where('finalizado', false)->count(),
            'Completados' => (clone $kpiQuery)->where('finalizado', true)->count(),
        ];

        $filtrosActivos = [
            'desde' => $request->filled('fecha_ini') ? Carbon::parse($request->fecha_ini)->format('d/m/Y') : 'Inicio del sistema',
            'hasta' => $request->filled('fecha_fin') ? Carbon::parse($request->fecha_fin)->format('d/m/Y') : 'Actualidad'
        ];

        $pdf = Pdf::loadView('correspondencia.tablero.pdf', compact('correspondencias', 'resumenKpis', 'filtrosActivos'));
        
        return $pdf->setPaper('a4', 'landscape')->stream('Informe_Gestion_' . now()->format('Ymd') . '.pdf');
    }

    /**
     * API: Obtiene las TRD vinculadas a un flujo específico (para carga dinámica en vistas).
     */
    public function getTrdsByFlujo($flujo_id)
    {
        $trds = Trd::where('fk_flujo', $flujo_id)->get(['id_trd', 'serie_documental', 'tiempo_gestion']);
        return response()->json($trds);
    }

    /**
     * API: Obtiene la información del remitente.
     */
    public function getRemitenteByCodigo($cod_ter)
    {
        $tercero = MaeTerceros::find($cod_ter);
        if (!$tercero) {
            return response()->json(['message' => 'Remitente no encontrado'], 404);
        }
        return response()->json($tercero);
    }
    public function descargarHistorialPdf($id)
    {
        // Eager loading de los procesos, el usuario que gestionó y el medio de recepción
        $correspondencia = Correspondencia::with([
            'remitente', 
            'medioRecepcion', 
            'flujo', 
            'estado',
            'procesos.usuario' // Asegura que cargue el historial y el usuario que comentó
        ])->findOrFail($id);

        $pdf = Pdf::loadView('correspondencia.correspondencias.historial_pdf', [
            'correspondencia' => $correspondencia
        ]);

        return $pdf->download('Historial_Radicado_' . $correspondencia->id_radicado . '.pdf');
    }
}