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
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Review::query();

            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_user')) {
                $query->where('id_user', $request->id_user);
            }

            if ($request->has('calificacion')) {
                $query->where('calificacion', $request->calificacion);
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('comentario', 'like', "%{$search}%");
            }

            $perPage = $request->get('per_page', 15);
            $reviews = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de reseñas obtenido exitosamente.',
                    'data' => $reviews,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
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
        $validatedData = $request->validate([
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'id_user' => 'required|integer',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $review = DB::transaction(function () use ($validatedData) {
                return Review::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reseña creada exitosamente.',
                'data' => $review,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReviewController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar la reseña.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
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
                'data' => $review,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en ReviewController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener la reseña.',
                'error' => config('app.debug') ? $e->getMessage() : null,
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
     * Update the specified resource in storage.
     */
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
                'comentario' => 'nullable|string',
            ]);

            DB::transaction(function () use ($review, $validatedData) {
                $review->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reseña actualizada exitosamente.',
                'data' => $review,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en ReviewController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la reseña.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
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
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
