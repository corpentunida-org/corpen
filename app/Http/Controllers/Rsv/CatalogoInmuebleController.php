<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rsv\StoreCatalogoInmuebleRequest;
use App\Http\Requests\Rsv\UpdateCatalogoInmuebleRequest;
use App\Models\Rsv\AuditLog;
use App\Models\Rsv\CatalogoInmueble;
use App\Models\Rsv\Reserva;
use App\Models\Rsv\TransaccionFinanciera;
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

            // Carga ansiosa de relaciones para evitar el problema N+1.
            if (method_exists(CatalogoInmueble::class, 'multimedia')) {
                $query->with(['multimedia' => function ($q) {
                    $q->where('es_portada', true);
                }]);
            }

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

            // Ordenamiento dinámico, por defecto ID descendente (los más nuevos primero)
            $sortField = $request->input('sort_by', 'id');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            // Paginación personalizable
            $inmuebles = $query->paginate($request->input('per_page', 15));

            // Respuesta Dual: API/AJAX
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catálogo de inmuebles recuperado exitosamente.',
                    'data'    => $inmuebles
                ]);
            }

            // Respuesta Dual: Web View
            return view('rsv.admin.partials.tab-inmuebles', compact('inmuebles'));

        } catch (\Throwable $e) {
            Log::error('Error al listar catálogo de inmuebles: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el catálogo de inmuebles.',
                    'error'   => env('APP_DEBUG') ? $e->getMessage() : null // Solo muestra error técnico en entorno de desarrollo
                ], 500);
            }

            // Fallback seguro: Evita el colapso de la vista pasando un paginador vacío
            $inmuebles = new LengthAwarePaginator([], 0, 15);
            return view('rsv.admin.partials.tab-inmuebles', compact('inmuebles'))
                ->with('error', 'Error al cargar los datos. Por favor, intente nuevamente.');
        }
    }

    /**
     * Muestra el formulario para crear un recurso. (Solo Web, no soportado en API).
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Almacena un inmueble recién creado en la base de datos.
     * Utiliza StoreCatalogoInmuebleRequest para delegar y limpiar la validación.
     */
    public function store(StoreCatalogoInmuebleRequest $request): RedirectResponse|JsonResponse
    {
        try {
            // Obtenemos únicamente los datos que pasaron la validación en el Form Request
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
                return response()->json(['success' => false, 'message' => 'Error interno al guardar el inmueble.'], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al registrar el inmueble.')->withInput();
        }
    }

    /**
     * Muestra los detalles de un inmueble específico y sus relaciones,
     * inyectándolo en el dashboard general para la vista (Edición / Ver detalles).
     */
    public function show(Request $request, string $id): View|JsonResponse
    {
        try {
            // Carga el inmueble con sus tarifas activas y vigentes
            $inmueble = CatalogoInmueble::with([
                'multimedia',
                'tarifasTemporadas' => function ($q) {
                    $q->where('active', true)->where('fecha_fin', '>=', now());
                }
            ])->findOrFail($id);

            // Carga de datos auxiliares para el dashboard global
            $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
            $reservas  = Reserva::paginate(10, ['*'], 'page_reservas');
            $finanzas  = TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');
            $auditoria = AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            // Retorno directo para API
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $inmueble
                ]);
            }

            return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria', 'inmueble'));

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

    /**
     * Método auxiliar privado para retornar la vista del dashboard de forma segura
     * en caso de errores en el método show(). Evita duplicar código.
     */
    private function fallbackDashboardView(string $errorMessage): View
    {
        $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
        $reservas  = clone $inmuebles; // Reemplazar por el modelo real \App\Models\Rsv\Reserva::paginate
        $finanzas  = clone $inmuebles; // Reemplazar por el modelo real \App\Models\Rsv\TransaccionFinanciera::paginate
        $auditoria = clone $inmuebles; // Reemplazar por el modelo real \App\Models\Rsv\AuditLog::paginate

        return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria'))
            ->with('error', $errorMessage);
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Actualiza el inmueble especificado en la base de datos.
     * Utiliza UpdateCatalogoInmuebleRequest para la validación.
     */
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

    /**
     * Elimina el inmueble especificado de la base de datos.
     * Incluye validación de integridad referencial (no eliminar si tiene reservas).
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::withCount('reservas')->findOrFail($id);

            // Bloqueo de eliminación por seguridad si hay reservas asociadas (integridad de datos)
            if ($inmueble->reservas_count > 0) {
                $msg = 'No se puede eliminar el inmueble porque tiene reservas asociadas. Considere desactivarlo.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->with('error', $msg);
            }

            // Eliminación en cascada manual segura dentro de una transacción
            DB::transaction(function () use ($inmueble) {
                if (method_exists($inmueble, 'multimedia')) $inmueble->multimedia()->delete();
                if (method_exists($inmueble, 'bloqueosCalendario')) $inmueble->bloqueosCalendario()->delete();
                if (method_exists($inmueble, 'tarifasTemporadas')) $inmueble->tarifasTemporadas()->delete();

                $inmueble->delete();
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Inmueble eliminado exitosamente.']);
            }

            return redirect()->back()->with('success', 'Inmueble eliminado exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El inmueble a eliminar no existe.'], 404);
            }
            return redirect()->back()->with('error', 'El inmueble a eliminar no existe.');

        } catch (\Throwable $e) {
            Log::error('Error al eliminar inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error interno al intentar eliminar.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el inmueble.');
        }
    }

    /**
     * Cambiar el estado activo/inactivo del inmueble mediante un "Toggle".
     */
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
