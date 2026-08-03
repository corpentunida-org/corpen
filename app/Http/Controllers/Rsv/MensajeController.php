<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MensajeController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de mensajería y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Mensaje::query();

            // Filtros comunes para sistema de mensajería
            if ($request->has('id_rsv_reservas')) {
                $query->where('id_rsv_reservas', $request->id_rsv_reservas);
            }

            if ($request->has('id_user_remitente')) {
                $query->where('id_user_remitente', $request->id_user_remitente);
            }

            if ($request->has('id_user_destinatario')) {
                $query->where('id_user_destinatario', $request->id_user_destinatario);
            }

            if ($request->has('leido')) {
                $query->where('leido', filter_var($request->leido, FILTER_VALIDATE_BOOLEAN));
            }

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where('mensaje', 'like', "%{$search}%");
            }

            $perPage = $request->get('per_page', 15);
            $mensajes = $query->orderBy('created_at', 'asc')->paginate($perPage);

            // Si la petición es por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de mensajes obtenido exitosamente.',
                    'data' => $mensajes,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.mensajes.index', compact('mensajes'));

        } catch (Throwable $e) {
            Log::error('Error en MensajeController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de mensajes.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de mensajes.');
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
            'id_user_remitente' => 'required|integer',
            'id_user_destinatario' => 'required|integer',
            'mensaje' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $validatedData['leido'] = false; // Por defecto el mensaje no está leído al crearse

            $mensaje = DB::transaction(function () use ($validatedData) {
                return Mensaje::create($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado exitosamente.',
                'data' => $mensaje,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en MensajeController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al enviar el mensaje.',
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
            $mensaje = Mensaje::find($id);

            if (!$mensaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'El mensaje solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mensaje obtenido exitosamente.',
                'data' => $mensaje,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en MensajeController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el mensaje.',
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
            $mensaje = Mensaje::find($id);

            if (!$mensaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'El mensaje solicitado no existe.',
                ], 404);
            }

            $validatedData = $request->validate([
                'mensaje' => 'sometimes|required|string',
                'leido' => 'sometimes|boolean',
            ]);

            DB::transaction(function () use ($mensaje, $validatedData) {
                $mensaje->update($validatedData);
            });

            return response()->json([
                'success' => true,
                'message' => 'Mensaje actualizado exitosamente.',
                'data' => $mensaje,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en MensajeController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el mensaje.',
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
            $mensaje = Mensaje::find($id);

            if (!$mensaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'El mensaje solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($mensaje) {
                $mensaje->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Mensaje eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en MensajeController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el mensaje.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Marca un mensaje específicamente como leído.
     */
    public function marcarComoLeido(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $mensaje = Mensaje::find($id);

            if (!$mensaje) {
                return response()->json([
                    'success' => false,
                    'message' => 'El mensaje solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($mensaje) {
                $mensaje->update(['leido' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Mensaje marcado como leído.',
                'data' => $mensaje,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en MensajeController@marcarComoLeido: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al marcar el mensaje como leído.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
