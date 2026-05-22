<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMantenimiento;
use App\Models\Inventario\InvActivo;
use App\Models\Inventario\InvMovimiento;
use App\Models\Inventario\InvMovimientoDetalle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base con sus relaciones
        $query = InvMantenimiento::with(['activo', 'creador']);

        // --- APLICAR FILTROS A LA CONSULTA ---
        
        // Filtro por Búsqueda (Nombre/Código del Activo o Detalle)
        $query->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('detalle', 'like', "%{$search}%")
                        ->orWhereHas('activo', function ($actQuery) use ($search) {
                            $actQuery->where('nombre', 'like', "%{$search}%")
                                    ->orWhere('codigo_activo', 'like', "%{$search}%");
                        });
            });
        });

        // Filtro por Rango de Fechas
        $query->when($request->fecha_inicio, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->fecha_inicio);
        });

        $query->when($request->fecha_fin, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->fecha_fin);
        });

        // 2. Clonar la consulta filtrada PARA EL DASHBOARD
        $queryDashboard = clone $query;

        // Calcular totales para las tarjetas
        $totalRegistros = $queryDashboard->count();
        $totalCosto = $queryDashboard->sum('costo_mantenimiento');

        // Calcular datos para la gráfica (Costo agrupado por fecha)
        $datosGrafica = (clone $queryDashboard)
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(costo_mantenimiento) as total_costo'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->take(15) // Limitamos a 15 días en la gráfica
            ->get();

        // Separar en labels (eje X) y data (eje Y) para Chart.js
        $chartLabels = $datosGrafica->pluck('fecha')->toArray();
        $chartData = $datosGrafica->pluck('total_costo')->toArray();

        // 3. Ejecutamos la consulta para la TABLA y paginamos
        $mantenimientos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('inventario.mantenimientos.index', compact(
            'mantenimientos', 
            'totalRegistros', 
            'totalCosto', 
            'chartLabels', 
            'chartData'
        ));
    }

    public function create()
    {
        // Solo activos que NO estén dados de baja
        $activos = InvActivo::where('id_Estado', '!=', 3)->get(); 
        
        // Traemos a todos los usuarios para llenar los selects en la vista
        $usuarios = User::all(); 

        return view('inventario.mantenimientos.create', compact('activos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_InvActivos' => 'required|exists:inv_activos,id',
            'id_usersRegistro' => 'required|exists:users,id', // <-- Validación del usuario que registra
            'id_usersAsignado' => 'required|exists:users,id', // <-- Validación a quién se le asigna
            'costo_mantenimiento' => 'required|numeric|min:0',
            'detalle' => 'required|string',
            'acta_archivo' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120' 
        ]);

        $path = null; 

        try {
            DB::beginTransaction();

            $activo = InvActivo::findOrFail($request->id_InvActivos);

            if ($request->hasFile('acta_archivo')) {
                $archivo = $request->file('acta_archivo');
                $directory = "corpentunida/inventario/mantenimientos"; 
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $path = Storage::disk('s3')->putFileAs($directory, $archivo, $nombreArchivo);
            }

            // 3. Guardar registro histórico (Usamos el request en lugar de auth()->id())
            $mantenimiento = InvMantenimiento::create([
                'id_InvActivos' => $request->id_InvActivos,
                'costo_mantenimiento' => $request->costo_mantenimiento,
                'detalle' => $request->detalle,
                'acta' => $path, 
                'id_usersRegistro' => $request->id_usersRegistro // <-- DATO MANUAL
            ]);

            // 4. Crear el Movimiento principal
            $movimiento = InvMovimiento::create([
                'codigo_acta' => 'MANT-' . date('Ymd') . '-' . $mantenimiento->id, 
                'acta_archivo' => null, 
                'observacion_general' => 'Salida a mantenimiento: ' . $request->detalle,
                'id_InvTiposRegistros' => 12, // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
                'id_usersAsignado' => $request->id_usersAsignado, // <-- DATO MANUAL
                'id_usersRegistro' => $request->id_usersRegistro, // <-- DATO MANUAL
                'id_mantenimiento' => $mantenimiento->id 
            ]);

            // 5. Crear el Detalle del Movimiento
            InvMovimientoDetalle::create([
                'estado_individual' => 'Enviado a revisión', 
                'id_InvMovimientos' => $movimiento->id, 
                'id_InvActivos' => $activo->id,
                'id_estado' => 12, // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
                'id_usersDelActivo' => $request->id_usersAsignado // <-- DATO MANUAL
            ]);

            // 6. Actualizar estado del activo
            $activo->id_Estado = 12; // Este esta unicamente para mantenimiento tegnologica falta anidarlo y acondicionarlo.
            $activo->save();

            DB::commit();
            return redirect()->route('inventario.mantenimientos.index')->with('success', 'Mantenimiento registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($path) { Storage::disk('s3')->delete($path); }

            return back()->withInput()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }
    
    /**
     * Sube el soporte/factura a AWS S3 desde la tabla (Index)
     */
    public function uploadActa(Request $request, $id)
    {
        $request->validate([
            'acta_archivo' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('acta_archivo')) {
            $mantenimiento = InvMantenimiento::findOrFail($id);

            $archivo = $request->file('acta_archivo');
            $directory = "corpentunida/inventario/mantenimientos"; 
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            $path = Storage::disk('s3')->putFileAs($directory, $archivo, $nombreArchivo);

            $mantenimiento->acta = $path;
            $mantenimiento->save();

            return back()->with('success', '¡Factura o acta subida y vinculada correctamente!');
        }

        return back()->with('error', 'No se detectó ningún archivo para subir.');
    }
}