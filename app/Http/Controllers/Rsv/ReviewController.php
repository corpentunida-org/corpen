<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class ReviewController extends Controller
{
    /**
     * Resuelve el id de rsv_tipo_receptor para una reseña de CLIENTE.
     * Busca primero un tipo "Cliente/Huésped"; si no hay, toma el primero del catálogo.
     */
    private function resolverTipoReceptorCliente(): ?int
    {
        try {
            if (!class_exists(\App\Models\Rsv\TipoReceptor::class)) {
                return null;
            }

            $col = \Illuminate\Support\Facades\Schema::hasColumn('rsv_tipo_receptor', 'name')
                ? 'name' : 'nombre';

            $tipo = \App\Models\Rsv\TipoReceptor::query()
                ->where(function ($w) use ($col) {
                    $w->where($col, 'like', '%cliente%')
                      ->orWhere($col, 'like', '%huesped%')
                      ->orWhere($col, 'like', '%huésped%');
                })
                ->orderBy('id')
                ->first();

            if (!$tipo) {
                $tipo = \App\Models\Rsv\TipoReceptor::query()->orderBy('id')->first();
            }

            return $tipo?->id;
        } catch (\Throwable $e) {
            Log::warning('resolverTipoReceptorCliente: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     * FIX: filtros con las columnas REALES (id_user_autor · puntuacion).
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Review::query();

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_user')) {
                // Columna real: id_user_autor
                $query->where('id_user_autor', $request->id_user);
            }

            if ($request->has('calificacion')) {
                // Columna real: puntuacion
                $query->where('puntuacion', $request->calificacion);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('comentario', 'like', "%{$search}%");
            }

            $perPage = $request->get('per_page', 15);
            $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de reseñas obtenido exitosamente.',
                    'data' => $reviews,
                ], 200);
            }

            return view('rsv.reviews.index', compact('reviews'));

        } catch (Throwable $e) {
            Log::error('Error en ReviewController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de reseñas.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de reseñas.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    /**
     * Store a newly created resource in storage.
     * FIX 1364: incluye id_rsv_tipo_receptor (columna NOT NULL sin default),
     * resuelto automáticamente como el tipo "Cliente" del catálogo.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'id_user'         => 'required|integer',
            'calificacion'    => 'required|integer|min:1|max:5',
            'comentario'      => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // ── Resolver el tipo de receptor (obligatorio en la tabla) ──
            $idTipoReceptor = $this->resolverTipoReceptorCliente();

            if (!$idTipoReceptor) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede registrar la reseña: el catálogo rsv_tipo_receptor está vacío. Configure al menos un tipo (ej. "Cliente").',
                ], 422);
            }

            // Mapeamos a los nombres de columna correctos del Modelo
            $review = Review::create([
                'id_rsv_reservas'      => $validatedData['id_rsv_reservas'],
                'id_user_autor'        => $validatedData['id_user'],
                'id_rsv_tipo_receptor' => $idTipoReceptor,   // ← FIX 1364
                'puntuacion'           => $validatedData['calificacion'],
                'comentario'           => $validatedData['comentario'] ?? null,
                'tipo_evaluacion'      => 'general',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reseña creada exitosamente.',
                'data'    => $review,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReviewController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la reseña.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reseña solicitada no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reseña obtenida exitosamente.',
                'data'    => $review,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReviewController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reseña.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Método no implementado para API REST.',
        ], 405);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reseña solicitada no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'calificacion' => 'sometimes|required|integer|min:1|max:5',
                'comentario'   => 'nullable|string',
            ]);

            // Mapeo a columnas reales (la vista/controlador usan calificacion)
            $datos = [];
            if (array_key_exists('calificacion', $validatedData)) { $datos['puntuacion'] = $validatedData['calificacion']; }
            if (array_key_exists('comentario', $validatedData))   { $datos['comentario']  = $validatedData['comentario']; }

            DB::transaction(function () use ($review, $datos) {
                $review->update($datos);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reseña actualizada exitosamente.',
                'data'    => $review->fresh(),
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReviewController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reseña.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'La reseña solicitada no existe.',
                ], 404);
            }

            DB::transaction(function () use ($review) {
                $review->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Reseña eliminada exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReviewController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar la reseña.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
