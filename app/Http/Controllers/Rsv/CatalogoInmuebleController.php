<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreCatalogoInmuebleRequest;
use App\Http\Requests\Rsv\UpdateCatalogoInmuebleRequest;
use App\Models\Rsv\AuditLog;
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\TransaccionFinanciera;
use App\Models\Rsv\TipoInmueble; // <--- Importación añadida para los tipos de inmueble
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Controlador principal para la gestión del Catálogo de Inmuebles.
 * Implementa un enfoque "Dual" respondiendo JSON para APIs/AJAX y Vistas Blade para navegación Web.
 */
class CatalogoInmuebleController extends Controller
{
    /**
     * Muestra el listado de inmuebles.
     * Implementa paginación, carga ansiosa (eager loading) segura y filtros dinámicos.
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            $query = CatalogoInmueble::query();

            // Carga ansiosa de relaciones inyectadas desde el controlador en vez de la vista
            $withRelations = [];
            if (method_exists(CatalogoInmueble::class, 'multimedia')) {
                $withRelations['multimedia'] = function ($q) {
                    $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc');
                };
            }
            if (method_exists(CatalogoInmueble::class, 'tipoInmueble')) {
                $withRelations[] = 'tipoInmueble';
            }
            if (method_exists(CatalogoInmueble::class, 'latestTarifa')) {
                $withRelations[] = 'latestTarifa';
            }

            $query->with($withRelations);

            // Aplicación de filtros de búsqueda dinámicos
            $query->when($request->filled('city'), function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->city . '%');
            })->when($request->filled('active'), function ($q) use ($request) {
                $q->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            })->when($request->filled('capacidad_minima'), function ($q) use ($request) {
                $q->where('capacidad_maxima', '>=', $request->capacidad_minima);
            })->when($request->filled('tipo_inmueble_id'), function ($q) use ($request) {
                $q->where('tipo_inmueble_id', $request->tipo_inmueble_id);
            });

            // Ordenamiento dinámico
            $sortField = $request->input('sort_by', 'id');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Paginación personalizable
            $inmuebles = $query->paginate($request->input('per_page', 15));

            // Variables para la Vista (Lógica previamente en Blade)
            $modoGlobal = true;
            $propiedadesCollection = $inmuebles;
            $tiposInmueblesOpt = TipoInmueble::orderBy('nombre')->get();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catálogo de inmuebles recuperado exitosamente.',
                    'data'    => $inmuebles
                ]);
            }

            return view('rsv.admin.partials.tab-inmuebles', compact(
                'inmuebles', 'modoGlobal', 'propiedadesCollection', 'tiposInmueblesOpt'
            ));

        } catch (\Throwable $e) {
            Log::error('Error al listar catálogo de inmuebles: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el catálogo de inmuebles.',
                    'error'   => env('APP_DEBUG') ? $e->getMessage() : null
                ], 500);
            }

            $inmuebles = new LengthAwarePaginator([], 0, 15);
            $modoGlobal = true;
            $propiedadesCollection = $inmuebles;
            $tiposInmueblesOpt = collect([]);

            return view('rsv.admin.partials.tab-inmuebles', compact(
                'inmuebles', 'modoGlobal', 'propiedadesCollection', 'tiposInmueblesOpt'
            ))->with('error', 'Error al cargar los datos. Por favor, intente nuevamente.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    public function store(StoreCatalogoInmuebleRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $validatedData = $request->validated();

            DB::transaction(function () use ($validatedData) {
                CatalogoInmueble::create($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Inmueble creado exitosamente.'], 201);
            }

            return redirect()->back()->with('success', 'Inmueble creado exitosamente.');

        } catch (\Throwable $e) {
            Log::error('Error al crear inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error interno al guardar el inmueble: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al registrar el inmueble: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, string $id): View|JsonResponse
    {
        try {
            // Preparamos la consulta con relaciones
            $query = CatalogoInmueble::with([
                'tarifasTemporadas' => function ($q) {
                    $q->where('active', true)->where('fecha_fin', '>=', now());
                }
            ]);

            if (method_exists(CatalogoInmueble::class, 'tipoInmueble')) {
                $query->with('tipoInmueble');
            }
            if (method_exists(CatalogoInmueble::class, 'latestTarifa')) {
                $query->with('latestTarifa');
            }
            if (method_exists(CatalogoInmueble::class, 'multimedia')) {
                $query->with(['multimedia' => function ($q) {
                    $q->orderBy('es_portada', 'desc')->orderBy('orden', 'asc');
                }]);
            }
            if (method_exists(CatalogoInmueble::class, 'reservas')) {
                $query->withCount('reservas');
            }
            if (method_exists(CatalogoInmueble::class, 'bloqueosCalendario')) {
                $query->withCount('bloqueosCalendario');
            }

            $inmueble = $query->findOrFail($id);

            // Carga de datos auxiliares para el dashboard global
            $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
            $reservas  = Reserva::paginate(10, ['*'], 'page_reservas');
            $finanzas  = TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');
            $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            // Variables para la Vista inyectadas directamente
            $modoGlobal = false;
            $propiedadesCollection = collect([$inmueble]);
            $tiposInmueblesOpt = TipoInmueble::orderBy('nombre')->get();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $inmueble
                ]);
            }

            return view('rsv.admin.dashboard', compact(
                'inmuebles', 'reservas', 'finanzas', 'auditoria', 'inmueble',
                'modoGlobal', 'propiedadesCollection', 'tiposInmueblesOpt'
            ));

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El inmueble solicitado no existe.'], 404);
            }
            return $this->fallbackDashboardView('El inmueble solicitado no existe.');

        } catch (\Throwable $e) {
            Log::error('Error al mostrar inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error interno.'], 500);
            }
            return $this->fallbackDashboardView('Ocurrió un error interno al intentar cargar la vista del inmueble.');
        }
    }

    private function fallbackDashboardView(string $errorMessage): View
    {
        $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
        $reservas  = clone $inmuebles;
        $finanzas  = clone $inmuebles;
        $auditoria = clone $inmuebles;

        $modoGlobal = true;
        $propiedadesCollection = $inmuebles;
        $tiposInmueblesOpt = collect([]);

        return view('rsv.admin.dashboard', compact(
            'inmuebles', 'reservas', 'finanzas', 'auditoria',
            'modoGlobal', 'propiedadesCollection', 'tiposInmueblesOpt'
        ))->with('error', $errorMessage);
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    public function update(UpdateCatalogoInmuebleRequest $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::findOrFail($id);
            $validatedData = $request->validated();

            DB::transaction(function () use ($inmueble, $validatedData) {
                $inmueble->update($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inmueble actualizado exitosamente.',
                    'data'    => $inmueble->fresh()
                ]);
            }

            return redirect()->back()->with('success', 'Inmueble actualizado exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El inmueble a actualizar no existe.'], 404);
            }
            return redirect()->back()->with('error', 'El inmueble a actualizar no existe.');

        } catch (\Throwable $e) {
            Log::error('Error al actualizar inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error interno al actualizar.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el inmueble.');
        }
    }

    public function cambiarEstado(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::findOrFail($id);

            $inmueble->active = !$inmueble->active;
            $inmueble->save();

            $estado = $inmueble->active ? 'activado' : 'desactivado';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "El inmueble ha sido {$estado} exitosamente.",
                    'data'    => $inmueble
                ]);
            }

            return redirect()->back()->with('success', "El inmueble ha sido {$estado} exitosamente.");

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El inmueble solicitado no existe.'], 404);
            }
            return redirect()->back()->with('error', 'El inmueble solicitado no existe.');

        } catch (\Throwable $e) {
            Log::error('Error al cambiar estado de inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error interno al cambiar el estado.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al cambiar el estado del inmueble.');
        }
    }
}
