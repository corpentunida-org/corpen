<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\CatalogoInmueble;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogoInmuebleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Implementa paginación, carga ansiosa segura, filtros y soporte Dual (Web/JSON).
     */
    public function index(Request $request): View|JsonResponse
    {
        try {
            $query = CatalogoInmueble::query();

            // Validación de seguridad: Solo intentamos cargar 'multimedia' si la relación existe en el modelo
            if (method_exists(CatalogoInmueble::class, 'multimedia')) {
                $query->with(['multimedia' => function ($q) {
                    $q->where('es_portada', true);
                }]);
            }

            // Filtros estratégicos
            if ($request->filled('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            if ($request->filled('active')) {
                $query->where('active', filter_var($request->active, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->filled('capacidad_minima')) {
                $query->where('capacidad_maxima', '>=', $request->capacidad_minima);
            }

            if ($request->filled('tipo_inmueble_id')) {
                $query->where('tipo_inmueble_id', $request->tipo_inmueble_id);
            }

            $sortField = $request->input('sort_by', 'id');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $inmuebles = $query->paginate($request->input('per_page', 15));

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Catálogo de inmuebles recuperado exitosamente.',
                    'data' => $inmuebles
                ]);
            }

            // Si es una petición del navegador web, renderizamos la vista Blade
            return view('rsv.admin.partials.tab-inmuebles', compact('inmuebles'));

        } catch (\Throwable $e) {
            Log::error('Error al listar catálogo de inmuebles: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el catálogo de inmuebles: ' . $e->getMessage(),
                ], 500);
            }

            // Fallback seguro: Evita el bucle infinito de redirect()->back() creando un paginador vacío
            $inmuebles = new LengthAwarePaginator([], 0, 15);

            return view('rsv.admin.partials.tab-inmuebles', compact('inmuebles'))
                ->with('error', 'Error al cargar los datos: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'capacidad_maxima' => 'required|integer|min:1',
            'precio_base_noche' => 'required|numeric|min:0',
            'tipo_inmueble_id' => 'required|integer',
        ]);

        try {
            $validated['active'] = $request->has('active') ? 1 : 0;

            DB::transaction(function () use ($validated) {
                CatalogoInmueble::create($validated);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Inmueble creado exitosamente.']);
            }

            return redirect()->back()->with('success', 'Inmueble creado exitosamente.');

        } catch (\Throwable $e) {
            Log::error('Error al crear inmueble: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error al guardar.'], 500);
            }

            return redirect()->back()->with('error', 'Ocurrió un error al registrar el inmueble.')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): View|JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::with([
                'multimedia',
                'tarifasTemporadas' => function ($q) {
                    $q->where('active', true)->where('fecha_fin', '>=', now());
                }
            ])->findOrFail($id);

            $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
            $reservas = \App\Models\Rsv\Reserva::paginate(10, ['*'], 'page_reservas');
            $finanzas = \App\Models\Rsv\TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');
            $auditoria = \App\Models\Rsv\AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria', 'inmueble'));

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'El inmueble solicitado no existe.'], 404);
            }

            // Para peticiones web devolvemos la misma vista del dashboard sin el inmueble
            $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
            $reservas = \App\Models\Rsv\Reserva::paginate(10, ['*'], 'page_reservas');
            $finanzas = \App\Models\Rsv\TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');
            $auditoria = \App\Models\Rsv\AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria'))
                ->with('error', 'El inmueble solicitado no existe.');

        } catch (\Throwable $e) {
            Log::error('Error al mostrar inmueble: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error interno.'], 500);
            }

            $inmuebles = CatalogoInmueble::paginate(10, ['*'], 'page_inmuebles');
            $reservas = \App\Models\Rsv\Reserva::paginate(10, ['*'], 'page_reservas');
            $finanzas = \App\Models\Rsv\TransaccionFinanciera::paginate(10, ['*'], 'page_finanzas');
            $auditoria = \App\Models\Rsv\AuditLog::latest()->paginate(10, ['*'], 'page_auditoria');

            return view('rsv.admin.dashboard', compact('inmuebles', 'reservas', 'finanzas', 'auditoria'))
                ->with('error', 'Ocurrió un error interno.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json(['message' => 'Método no soportado en la API.'], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'capacidad_maxima' => 'sometimes|required|integer|min:1',
            'precio_base_noche' => 'sometimes|required|numeric|min:0',
            'tipo_inmueble_id' => 'sometimes|required|integer',
        ]);

        try {
            $inmueble = CatalogoInmueble::findOrFail($id);

            if ($request->has('active')) {
                $validated['active'] = $request->active ? 1 : 0;
            } else {
                // Si el checkbox no viene en el request (desmarcado en formulario HTML), lo ponemos en 0
                $validated['active'] = 0;
            }

            DB::transaction(function () use ($inmueble, $validated) {
                $inmueble->update($validated);
            });

            // Soporte Dual: Si es API o AJAX, responde con JSON. Si no, redirige.
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inmueble actualizado exitosamente.',
                    'data' => $inmueble->fresh()
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
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al actualizar el inmueble.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el inmueble.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        try {
            $inmueble = CatalogoInmueble::withCount('reservas')->findOrFail($id);

            if ($inmueble->reservas_count > 0) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se puede eliminar el inmueble porque tiene reservas asociadas. Considere desactivarlo.',
                    ], 422);
                }
                return redirect()->back()->with('error', 'No se puede eliminar el inmueble porque tiene reservas asociadas.');
            }

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
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al intentar eliminar el inmueble.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al intentar eliminar el inmueble.');
        }
    }

    /**
     * Cambiar el estado activo/inactivo del inmueble.
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
                    'data' => $inmueble
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
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al cambiar el estado del inmueble.'], 500);
            }
            return redirect()->back()->with('error', 'Ocurrió un error al cambiar el estado del inmueble.');
        }
    }
}
