<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\ReservaHuesped;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ReservaHuespedController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = ReservaHuesped::query();

            // Filtrar por reserva específica
            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            // Filtrar por huésped principal
            if ($request->has('es_principal')) {
                $query->where('es_principal', filter_var($request->es_principal, FILTER_VALIDATE_BOOLEAN));
            }

            // Búsqueda por nombre, apellido o documento
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombres', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%")
                      ->orWhere('numero_documento', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $huespedes = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de huéspedes obtenido exitosamente.',
                    'data' => $huespedes,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.huespedes.index', compact('huespedes'));

        } catch (Throwable $e) {
            Log::error('Error en ReservaHuespedController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de huéspedes.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de huéspedes.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $huesped = ReservaHuesped::find($id);

            if (!$huesped) {
                return response()->json([
                    'success' => false,
                    'message' => 'El huésped solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Huésped obtenido exitosamente.',
                'data' => $huesped,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaHuespedController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el huésped.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_rsv_reservas'  => 'required|exists:rsv_reservas,id',
            'nombres'          => 'required_without:nombre|nullable|string|max:255',
            'nombre'           => 'required_without:nombres|nullable|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'tipo_documento'   => 'nullable|string|max:50',
            'numero_documento' => 'nullable|string|max:50',
            'es_principal'     => 'nullable|boolean',
            'es_titular'       => 'nullable|boolean',
        ]);

        try {
            $huesped = DB::transaction(function () use ($request, $validated) {
                return ReservaHuesped::create([
                    'id_rsv_reservas'     => $validated['id_rsv_reservas'],
                    'id_user_registrador' => Auth::id() ?? $request->user()?->id,
                    'nombre'              => $validated['nombre'] ?? $validated['nombres'],
                    'apellidos'           => $validated['apellidos'],
                    'tipo_documento'      => $validated['tipo_documento'] ?? null,
                    'numero_documento'    => $validated['numero_documento'] ?? null,
                    'es_titular'          => $request->boolean('es_titular', $request->boolean('es_principal', false)),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Huésped registrado exitosamente.',
                'data'    => $huesped,
            ], 201);

        } catch (Throwable $e) {
            Log::error('Error en ReservaHuespedController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el huésped.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $huesped = ReservaHuesped::find($id);

            if (!$huesped) {
                return response()->json([
                    'success' => false,
                    'message' => 'El huésped solicitado no existe.',
                ], 404);
            }

            $validated = $request->validate([
                'nombres'          => 'nullable|string|max:255',
                'nombre'           => 'nullable|string|max:255',
                'apellidos'        => 'sometimes|required|string|max:255',
                'tipo_documento'   => 'nullable|string|max:50',
                'numero_documento' => 'nullable|string|max:50',
                'es_principal'     => 'nullable|boolean',
                'es_titular'       => 'nullable|boolean',
            ]);

            $datosActualizar = [];

            if (array_key_exists('nombre', $validated) || array_key_exists('nombres', $validated)) {
                $datosActualizar['nombre'] = $validated['nombre'] ?? $validated['nombres'];
            }
            if (array_key_exists('apellidos', $validated)) {
                $datosActualizar['apellidos'] = $validated['apellidos'];
            }
            if (array_key_exists('tipo_documento', $validated)) {
                $datosActualizar['tipo_documento'] = $validated['tipo_documento'];
            }
            if (array_key_exists('numero_documento', $validated)) {
                $datosActualizar['numero_documento'] = $validated['numero_documento'];
            }
            if ($request->has('es_titular') || $request->has('es_principal')) {
                $datosActualizar['es_titular'] = $request->boolean('es_titular', $request->boolean('es_principal'));
            }

            DB::transaction(function () use ($huesped, $datosActualizar) {
                $huesped->update($datosActualizar);
            });

            return response()->json([
                'success' => true,
                'message' => 'Datos del huésped actualizados exitosamente.',
                'data'    => $huesped->fresh(),
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReservaHuespedController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar los datos del huésped.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $huesped = ReservaHuesped::find($id);

            if (!$huesped) {
                return response()->json([
                    'success' => false,
                    'message' => 'El huésped solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($huesped) {
                $huesped->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Huésped eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReservaHuespedController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el huésped.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
