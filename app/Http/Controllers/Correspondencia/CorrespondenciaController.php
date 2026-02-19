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
        
        // --- NUEVO: Carga de procesos para el MODAL DE GESTIÓN RÁPIDA ---
        // Necesitamos traer los procesos y sus estados relacionados para llenar los selects y botones del modal
        $procesos_disponibles = Proceso::with(['estadosProcesos.estado'])->get();

        // Carga ansiosa (Eager Loading) de relaciones para optimizar consultas
        $query = Correspondencia::with(['trd', 'flujo', 'estado', 'usuario', 'remitente', 'medioRecepcion'])
            ->latest(); // Ordenar por fecha de creación (los más nuevos primero)

        // Filtro por Estado
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        // Buscador por Asunto o ID de Radicado
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('asunto', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('id_radicado', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Paginación (con appends para mantener filtros en los links)
        $correspondencias = $query->paginate(15)->appends($request->all());

        // Pasamos $procesos_disponibles a la vista
        return view('correspondencia.correspondencias.index', compact('correspondencias', 'estados', 'procesos_disponibles'));
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
            'id_radicado'        => 'required|string|unique:corr_correspondencia,id_radicado',
            'fecha_solicitud'    => 'required|date',
            'asunto'             => 'required|string|max:500',
            'medio_recibido'     => 'required|exists:corr_medio_recepcion,id',
            'remitente_id'       => 'required',
            'trd_id'             => 'required',
            'flujo_id'           => 'required',
            'estado_id'          => 'required',
            'observacion_previa' => 'nullable|string',
            'final_descripcion'  => 'nullable|string', // Validación del nuevo campo
            'documento_arc'      => 'nullable|file|mimes:pdf,doc,docx|max:10240',
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
        // Carga la correspondencia y sus relaciones anidadas
        $correspondencia = Correspondencia::with([
            'procesos.usuario', 
            'procesos.proceso.flujo', 
            'trd', 
            'estado', 
            'remitente', 
            'flujo',
            'medioRecepcion'
        ])->findOrFail($id);

        $flujo = null;
        $procesos_disponibles = collect();

        // Determina qué procesos se pueden ejecutar según el flujo asignado
        if ($correspondencia->flujo_id) {
            $flujo = FlujoDeTrabajo::with(['usuario'])->find($correspondencia->flujo_id);
            
            $procesos_disponibles = Proceso::where('flujo_id', $correspondencia->flujo_id)
                ->with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado']) 
                ->get();
        } else {
            $procesos_disponibles = Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();
        }

        // Cálculos de Tiempos de Gestión (KPIs) basados en la TRD
        $tiempoGestion = $correspondencia->trd->tiempo_gestion ?? 0;
        $fechaInicio = Carbon::parse($correspondencia->fecha_solicitud);
        $limite = $fechaInicio->copy()->addYears($tiempoGestion);
        $diferenciatrd = CarbonInterval::instance(now()->diff($limite));

        return view('correspondencia.correspondencias.show', compact('correspondencia', 'procesos_disponibles', 'flujo', 'diferenciatrd','limite'));
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
        // 1. VALIDACIÓN
        $data = $request->validate([
            'fecha_solicitud'    => 'required|date',
            'asunto'             => 'required|string|max:500',
            'medio_recibido'     => 'required|exists:corr_medio_recepcion,id',
            'remitente_id'       => 'required',
            'trd_id'             => 'required',
            'flujo_id'           => 'required',
            'estado_id'          => 'required',
            'observacion_previa' => 'nullable|string',
            
            // LÓGICA CLAVE: Si se marca 'finalizado', la descripción es OBLIGATORIA
            'final_descripcion'  => 'nullable|string|required_if:finalizado,1', 
            
            'documento_arc'      => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ], [
            // Mensaje personalizado por si intentan finalizar sin escribir
            'final_descripcion.required_if' => 'Debe escribir una descripción de cierre si va a finalizar el radicado.'
        ]);

        // 2. BOOLEANOS
        $data['es_confidencial'] = $request->boolean('es_confidencial');
        $data['finalizado'] = $request->boolean('finalizado');

        // 3. GESTIÓN DE ARCHIVOS (S3)
        if ($request->hasFile('documento_arc')) {
            // Borrar anterior si existe
            if ($correspondencia->documento_arc) {
                Storage::disk('s3')->delete($correspondencia->documento_arc);
            }

            // Subir nuevo
            $file = $request->file('documento_arc');
            $fileName = 'rad' . $correspondencia->id_radicado . '_' . time() . '.' . $file->extension();
            $path = 'corpentunida/correspondencia/' . $fileName;

            Storage::disk('s3')->put($path, file_get_contents($file));
            $data['documento_arc'] = $path;
        }

        // 4. ACTUALIZAR BASE DE DATOS
        $correspondencia->update($data);
        
        // 5. AUDITORÍA
        $this->auditoria('UPDATE CORRESPONDENCIA ID ' . $correspondencia->id_radicado);

        // 6. REDIRECCIÓN CORRECTA AL SHOW
        return redirect()
            ->route('correspondencia.correspondencias.show', $correspondencia)
            ->with('success', 'Radicado actualizado correctamente.');
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
        // 1. CARGA DE PROCESOS (Obligatorio para el Modal de Gestión Rápida)
        // Traemos procesos con sus estados y usuarios asignados para los mapas JS
        $procesos_disponibles = Proceso::with(['flujo', 'usuariosAsignados.usuario', 'estadosProcesos.estado'])->get();

        // 2. DETERMINAR PESTAÑA ACTIVA
        // Si hay búsqueda o filtros específicos, abrimos en la pestaña 'gestión'
        $activeTab = $request->filled('search') || 
                     $request->filled('page') || 
                     $request->filled('estado_id') || 
                     $request->filled('usuario_id') || 
                     $request->filled('condicion') ? 'gestion' : 'dashboard';

        // 3. CONSTRUCCIÓN DE LA CONSULTA DE CORRESPONDENCIAS
        $query = Correspondencia::with([
            'trd', 
            'flujo.procesos.usuariosAsignados', // Para validar el lápiz de gestión
            'estado', 
            'usuario', 
            'procesos.proceso', 
            'medioRecepcion',
            'remitente'
        ]);

        // Búsqueda por palabras clave (Asunto o Radicado)
        if ($request->filled('search')) {
            $searchWords = array_filter(explode(' ', $request->search));
            $query->where(function ($q) use ($searchWords) {
                foreach ($searchWords as $word) {
                    $q->orWhere('id_radicado', 'LIKE', '%' . $word . '%')
                      ->orWhere('asunto', 'LIKE', '%' . $word . '%');
                }
            });
        }

        // Filtro por Estado
        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        // Filtro por Usuario Creador
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        // Filtro por Condición de Tiempo (KPI TRD)
        if ($request->filled('condicion')) {
            if ($request->condicion == 'vencido') {
                $query->whereHas('trd', function ($q) {
                        $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) < NOW()');
                    })
                    ->where('finalizado', false);
            } elseif ($request->condicion == 'a_tiempo') {
                $query->whereHas('trd', function ($q) {
                        $q->whereRaw('DATE_ADD(corr_correspondencia.fecha_solicitud, INTERVAL corr_trd.tiempo_gestion DAY) >= NOW()');
                    })
                    ->where('finalizado', false);
            }
        }

        // Ejecutar paginación
        $correspondencias = $query->orderBy('fecha_solicitud', 'desc')->paginate(10)->appends($request->all());

        // 4. CÁLCULO DE DATOS PARA INDICADORES (DASHBOARD)
        
        // Distribución por Estados (Donut Chart)
        $estados = Estado::withCount('correspondencias')->get();
        $totalCorrespondencias = $estados->sum('correspondencias_count');

        $chartDistribucion = [
            'labels' => $estados->pluck('nombre'),
            'data'   => $estados->pluck('correspondencias_count'),
        ];

        // Carga de trabajo por Usuario - Top 5 (Bar Chart)
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
            'data'   => $usuariosCarga->pluck('correspondencias_count'),
        ];

        // Lista de usuarios para el filtro del tablero
        $usuarios = User::orderBy('name')->get();

        // 5. RETORNO DE VISTA CON TODOS LOS COMPONENTES
        return view('correspondencia.tablero.index', compact(
            'correspondencias', 
            'estados', 
            'totalCorrespondencias', 
            'usuarios', 
            'activeTab', 
            'chartDistribucion', 
            'chartCarga',
            'procesos_disponibles' // <-- Variable indispensable enviada
        ));
    }

    /**
     * API: Obtiene las TRD vinculadas a un flujo específico (para carga dinámica en vistas).
     */
    public function getTrdsByFlujo($flujo_id)
    {
        $trds = Trd::where('fk_flujo', $flujo_id)->get(['id_trd', 'serie_documental', 'tiempo_gestion']);
        return response()->json($trds);
    }
}