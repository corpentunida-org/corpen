<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\FlujoDeTrabajo;
use App\Models\Archivo\GdoArea; // Importado para la selección de áreas
use App\Http\Controllers\AuditoriaController;
use App\Models\User;

class FlujoDeTrabajoController extends Controller
{
    private function auditoria($accion)
    {
        $auditoriaController = app(AuditoriaController::class);
        $auditoriaController->create($accion, 'CORRESPONDENCIA');
    }

    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base
        $query = FlujoDeTrabajo::with(['usuario', 'area'])
            ->withCount('correspondencias');

        // 2. Filtro de Búsqueda (Texto)
        if ($request->filled('search')) {
            $words = explode(' ', $request->search);
            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('nombre', 'LIKE', "%$word%")
                      ->orWhere('detalle', 'LIKE', "%$word%");
                }
            });
        }

        // 3. Filtro por Estado (En Uso / Disponible)
        if ($request->filled('estado')) {
            if ($request->estado == 'en_uso') {
                $query->has('correspondencias');
            } elseif ($request->estado == 'disponible') {
                $query->doesntHave('correspondencias');
            }
        }

        // 4. Filtro por Área (CORREGIDO: la columna en la BD es id_area)
        if ($request->filled('area_id')) {
            $query->where('id_area', $request->area_id);
        }

        // 5. Paginación manteniendo los parámetros de la URL
        $flujos = $query->paginate(15)->appends($request->all());

        // 6. Extraer TODAS las Áreas activas para los Chips horizontales
        $total_flujos = FlujoDeTrabajo::count();
        
        // Obtenemos un diccionario con el conteo de flujos por cada ID de área (CORREGIDO: id_area)
        $conteoPorArea = FlujoDeTrabajo::selectRaw('id_area, COUNT(*) as total')
            ->groupBy('id_area')
            ->pluck('total', 'id_area');

        // Consultamos todas las áreas activas directamente de su modelo
        $areas_disponibles = GdoArea::where('estado', 'activo')
            ->orderBy('nombre')
            ->get()
            ->map(function ($area) use ($conteoPorArea) {
                // Le asignamos la cantidad de flujos que tiene (0 si no tiene ninguno)
                $area->flujos_count = $conteoPorArea->get($area->id, 0);
                return $area;
            });

        return view('correspondencia.flujos.index', compact('flujos', 'areas_disponibles', 'total_flujos'));
    }
    public function create()
    {
        // Cargamos el cargo jefe, el empleado de ese cargo y el usuario de ese empleado
        $areas = GdoArea::where('estado', 'activo')
            ->with(['jefeCargo.user']) 
            ->get();
        
        $usuarios = User::all(); 

        return view('correspondencia.flujos.create', compact('usuarios', 'areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:150',
            'detalle'    => 'nullable|string',
            'id_area'    => 'required|exists:gdo_area,id', // Validación del área seleccionada
            'usuario_id' => 'required|exists:users,id',    // El ID del jefe (jefeCargo)
        ]);

        $flujoadd = FlujoDeTrabajo::create($data);
        
        $this->auditoria('ADD FLUJO DE TRABAJO ID ' . $flujoadd->id);

        // 👇 LÓGICA AGREGADA PARA SOPORTAR AJAX (MODAL DE TRD) 👇
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'flujo_id' => $flujoadd->id // Enviamos el ID recién creado al frontend
            ]);
        }
        // 👆 FIN DE LA LÓGICA AJAX 👆

        return redirect()->route('correspondencia.flujos.index')
            ->with('success', 'Flujo de trabajo creado y asignado al área correctamente.');
    }

    public function show(FlujoDeTrabajo $flujo)
    {
        // 1. Carga de relaciones asegurando el orden de los procesos
        $flujo->load([
            'usuario', 
            'area', 
            'trd',
            'procesos' => function($query) {
                $query->orderBy('id', 'asc'); // Importante para que el flujo sea secuencial
            },
            'procesos.usuarios' => function($query) {
                $query->where('activo', true); 
            }
        ])->loadCount('correspondencias');

        // 2. CÁLCULO DE MÉTRICAS (KPIs)
        $total_activos = $flujo->correspondencias()->where('finalizado', 0)->count();
        $total_finalizados = $flujo->correspondencias()->where('finalizado', 1)->count();
        
        // Tiempo promedio (en días) de los radicados finalizados
        $tiempo_promedio_dias = 0;
        if ($total_finalizados > 0) {
            $tiempo_promedio_dias = $flujo->correspondencias()
                ->where('finalizado', 1)
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as promedio')
                ->value('promedio');
        }

        // 3. DATOS PARA LA GRÁFICA (Cálculo del Cuello de Botella)
        $labelsGrafica = $flujo->procesos->pluck('nombre')->toArray();
        $datosGrafica = array_fill(0, $flujo->procesos->count(), 0); // Llenamos de ceros inicialmente
        
        // Traemos los radicados activos con su historial de procesos
        $activos = $flujo->correspondencias()->where('finalizado', 0)->with('procesos')->get();
        
        foreach($activos as $corr) {
            // Obtenemos los IDs de los procesos que YA fueron completados por este radicado
            $pasosCompletados = $corr->procesos->pluck('id_proceso')->toArray();
            
            // Recorremos la ruta ideal del flujo
            foreach($flujo->procesos as $index => $paso) {
                // El primer paso de la ruta que NO esté en el historial, es donde está atascado
                if(!in_array($paso->id, $pasosCompletados)) {
                    $datosGrafica[$index]++;
                    break; // Cortamos el bucle porque ya encontramos el cuello de botella de ESTE radicado
                }
            }
        }

        return view('correspondencia.flujos.show', compact(
            'flujo', 'total_activos', 'total_finalizados', 'tiempo_promedio_dias', 'labelsGrafica', 'datosGrafica'
        ));
    }

    public function edit(FlujoDeTrabajo $flujo)
    {
        $areas = GdoArea::where('estado', 'activo')->with(['jefeCargo.user'])->get();
        $usuarios = User::all(); // Opcional, ya que ahora es automático

        return view('correspondencia.flujos.edit', compact('flujo', 'usuarios', 'areas'));
    }

    public function update(Request $request, FlujoDeTrabajo $flujo)
    {
        $data = $request->validate([
            'nombre'     => 'required|string|max:150',
            'detalle'    => 'nullable|string',
            'id_area'    => 'required|exists:gdo_area,id',
            'usuario_id' => 'required|exists:users,id',
        ]);

        $flujo->update($data);

        $this->auditoria('UPDATE FLUJO DE TRABAJO ID ' . $flujo->id);

        return redirect()->route('correspondencia.flujos.show', $flujo)
            ->with('success', 'Flujo actualizado correctamente.');
    }

    public function destroy(FlujoDeTrabajo $flujo)
    {
        // VALIDACIÓN: Si el flujo tiene correspondencias, no se puede eliminar
        if ($flujo->correspondencias()->exists()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar: Este flujo tiene registros de correspondencia vinculados.');
        }

        $flujo->delete();
        $this->auditoria('DELETE FLUJO DE TRABAJO ID ' . $flujo->id);

        return redirect()->route('correspondencia.flujos.index')
            ->with('success', 'Flujo eliminado correctamente.');
    }
}