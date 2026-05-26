<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\InvMovimiento;
use App\Models\Inventario\InvMovimientoDetalle;
use App\Models\Inventario\InvActivo;
use App\Models\User;
use App\Models\Inventario\InvEstado;
use App\Models\Inventario\InvBodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimientoController extends Controller
{
    /**
     * Muestra el historial de movimientos realizados.
     */
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base (agregamos 'creador' para optimizar)
        $query = InvMovimiento::with(['responsable', 'tipoRegistro', 'creador']);

        // --- APLICAR FILTROS A LA CONSULTA ---
        
        // Filtro por Búsqueda (Código de Acta o Nombre del Responsable)
        $query->when($request->search, function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('codigo_acta', 'like', "%{$search}%")
                        ->orWhereHas('responsable', function ($resQuery) use ($search) {
                            $resQuery->where('name', 'like', "%{$search}%");
                        });
            });
        });

        // Filtro por Rango de Fechas (usamos created_at)
        $query->when($request->fecha_inicio, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->fecha_inicio);
        });

        $query->when($request->fecha_fin, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->fecha_fin);
        });

        // Filtro por Tipo de Registro
        $query->when($request->tipo, function ($q) use ($request) {
            $tipo = $request->tipo;
            $q->whereHas('tipoRegistro', function ($tipoQuery) use ($tipo) {
                $tipoQuery->where('nombre', 'like', "%{$tipo}%");
            });
        });

        // 2. Clonar la consulta filtrada PARA EL DASHBOARD
        $queryDashboard = clone $query;

        // Calcular totales para las tarjetas
        $totalRegistros = $queryDashboard->count();
        
        // Calcular cuántas actas ya tienen archivo adjunto (están firmadas)
        $queryFirmadas = clone $queryDashboard;
        $totalFirmadas = $queryFirmadas->whereNotNull('acta_archivo')->count();

        // Calcular datos para la gráfica (Cantidad de movimientos agrupados por fecha)
        $datosGrafica = (clone $queryDashboard)
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->take(15) // Limitamos a 15 días en la gráfica
            ->get();

        // Separar en labels (eje X) y data (eje Y) para Chart.js
        $chartLabels = $datosGrafica->pluck('fecha')->toArray();
        $chartData = $datosGrafica->pluck('total')->toArray();

        // 3. Ejecutamos la consulta para la TABLA y paginamos
        $movimientos = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('inventario.movimientos.index', compact(
            'movimientos', 
            'totalRegistros', 
            'totalFirmadas', 
            'chartLabels', 
            'chartData'
        ));
    }

    /**
     * Muestra el formulario para crear un nuevo acta/movimiento.
     */
    public function create()
    {
        $usuarios = User::all();
        $tipos = InvEstado::all(); // Estados que actúan como "Tipo de Movimiento"
        $bodegas = InvBodega::all(); // Bodegas para el filtro visual de la vista

        // CAMBIO: Ahora cargamos TODOS los activos (no solo los disponibles)
        // para poder hacer devoluciones, mantenimientos o reasignaciones.
        $activosDisponibles = InvActivo::with(['referencia.marca', 'estado', 'usuarioAsignado'])
            ->get();

        return view('inventario.movimientos.create', compact('usuarios', 'tipos', 'activosDisponibles', 'bodegas'));
    }

    /**
     * Procesa y almacena el acta (Cabecera + Detalles) y actualiza los activos.
     */
    public function store(Request $request)
    {
        // 1. Validación estricta de los datos entrantes
        $validator = Validator::make($request->all(), [
            'codigo_acta' => 'required|unique:inv_movimientos,codigo_acta',
            'id_InvTiposRegistros' => 'required|exists:inv_estados,id',
            'id_usersAsignado' => 'required|exists:users,id',
            'activos_seleccionados' => 'required|array|min:1'
        ], [
            'codigo_acta.unique' => 'El código de acta ya ha sido registrado previamente.',
            'activos_seleccionados.required' => 'Debes seleccionar al menos un activo para generar el movimiento.',
            'id_InvTiposRegistros.required' => 'El tipo de movimiento/estado es obligatorio.'
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Validación: ' . $validator->errors()->first())->withInput();
        }

        try {
            // Iniciamos transacción para asegurar la integridad de los datos
            DB::beginTransaction();

            // 2. Crear Cabecera del Movimiento (Modelo InvMovimiento)
            $movimiento = InvMovimiento::create([
                'codigo_acta' => $request->codigo_acta,
                'observacion_general' => $request->observacion_general,
                'id_InvTiposRegistros' => $request->id_InvTiposRegistros, // Llave foránea en Cabecera
                'id_usersAsignado' => $request->id_usersAsignado,
                'id_usersRegistro' => auth()->id()
            ]);

            // Obtenemos el objeto estado para usar su nombre en el detalle (auditoría histórica)
            $estadoObj = InvEstado::find($request->id_InvTiposRegistros);
            $nombreEstado = strtolower($estadoObj->nombre ?? '');

            // Determinamos si el movimiento es una devolución (vuelve a estar disponible)
            $esDevolucion = str_contains($nombreEstado, 'disponible') || str_contains($nombreEstado, 'devuel');

            // 3. Crear Detalles y actualizar los Activos físicamente
            foreach ($request->activos_seleccionados as $activo_id) {

                // Registro en el modelo Detalle
                InvMovimientoDetalle::create([
                    'id_InvMovimientos' => $movimiento->id,
                    'id_InvActivos' => $activo_id,
                    'id_estado' => $request->id_InvTiposRegistros, // Llave foránea en Detalle
                    'id_usersDelActivo' => $request->id_usersAsignado,
                    'estado_individual' => $estadoObj->nombre ?? 'N/A'
                ]);

                // Actualización del estado y responsable en la tabla de activos
                $activo = InvActivo::find($activo_id);
                if ($activo) {
                    // Actualizamos el estado del activo al nuevo seleccionado
                    $activo->id_Estado = $request->id_InvTiposRegistros;

                    // Si es devolución, limpiamos el responsable. Si es asignación, lo vinculamos.
                    $activo->id_ultimo_usuario_asignado = $esDevolucion ? null : $request->id_usersAsignado;
                    $activo->save();
                }
            }

            DB::commit();
            return redirect()->route('inventario.movimientos.index')->with('success', '¡Acta ' . $request->codigo_acta . ' generada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Retornamos el error exacto para debugging técnico
            return back()->with('error', 'Fallo técnico en la base de datos: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Genera y descarga el PDF del Acta de Movimiento (Sistema)
     */
    public function generarPdf($id)
    {
        // 1. Buscamos el movimiento con todas sus relaciones cargadas
        $movimiento = InvMovimiento::with([
            'responsable',
            'creador',
            'tipoRegistro',
            'detalles.activo.referencia.marca'
        ])->findOrFail($id);

        // 2. Cargamos una vista HTML y le pasamos los datos del movimiento
        $pdf = Pdf::loadView('inventario.movimientos.pdf', compact('movimiento'));

        // 3. Forzamos la descarga del archivo con el nombre del acta
        return $pdf->download($movimiento->codigo_acta . '.pdf');
    }

    /**
     * Sube el acta firmada a AWS S3 y actualiza el registro en la BD
     */
    public function uploadActaFirmada(Request $request, $id)
    {
        $request->validate([
            // Validamos que sea un PDF y no pese más de 5MB (5120 KB)
            'acta_pdf' => 'required|mimes:pdf|max:5120',
        ]);
        if ($request->hasFile('acta_pdf')) {
            $movimiento = InvMovimiento::findOrFail($id);

            $archivo = $request->file('acta_pdf');
            $directory = "corpentunida/inventario";
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            $path = Storage::disk('s3')->putFileAs($directory,$archivo,$nombreArchivo,);

            $movimiento->acta_archivo = $path;
            $movimiento->save();

            return back()->with('success', '¡Acta firmada subida y vinculada correctamente.!');
        }

        return back()->with('error', 'No se detectó ningún archivo para subir.');
    }
}