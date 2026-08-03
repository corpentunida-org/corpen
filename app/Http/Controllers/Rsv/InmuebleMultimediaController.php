<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\InmuebleMultimedia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class InmuebleMultimediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros, ordenamiento y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = InmuebleMultimedia::with('inmueble:id,name,active');

            if ($request->has('id_rsv_catalogo_inmueble')) {
                $query->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            }

            if ($request->has('tipo_multimedia')) {
                $query->where('tipo_multimedia', $request->tipo_multimedia);
            }

            if ($request->has('es_portada')) {
                $query->where('es_portada', filter_var($request->es_portada, FILTER_VALIDATE_BOOLEAN));
            }

            $perPage = $request->get('per_page', 15);
            $multimedia = $query->orderBy('orden', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage);

            // Si es petición por API o AJAX, devolvemos JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de multimedia obtenido exitosamente.',
                    'data' => $multimedia,
                ], 200);
            }

            // Si es navegación web tradicional, renderizamos la vista Blade del módulo
            return view('rsv.multimedia.index', compact('multimedia'));

        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de multimedia.',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al obtener el listado de multimedia.');
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
            'id_rsv_catalogo_inmueble' => 'required|exists:rsv_catalogo_inmueble,id',
            'url_archivo' => 'required|string|max:255',
            'tipo_multimedia' => 'required|string|max:50',
            'orden' => 'required|integer',
            'es_portada' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $esPortada = $validatedData['es_portada'] ?? false;

            // Si este archivo será la portada, asegurar que los demás del mismo inmueble no lo sean.
            if ($esPortada) {
                InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $validatedData['id_rsv_catalogo_inmueble'])
                    ->update(['es_portada' => false]);
            }

            $multimedia = DB::transaction(function () use ($validatedData) {
                return InmuebleMultimedia::create($validatedData);
            });

            $multimedia->load('inmueble:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Recurso multimedia registrado exitosamente.',
                'data' => $multimedia,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en InmuebleMultimediaController@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el recurso multimedia.',
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
            $multimedia = InmuebleMultimedia::with('inmueble')->find($id);

            if (!$multimedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'El recurso multimedia solicitado no existe.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Recurso multimedia obtenido exitosamente.',
                'data' => $multimedia,
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@show: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al obtener el recurso multimedia.',
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
        $validatedData = $request->validate([
            'id_rsv_catalogo_inmueble' => 'sometimes|required|exists:rsv_catalogo_inmueble,id',
            'url_archivo' => 'sometimes|required|string|max:255',
            'tipo_multimedia' => 'sometimes|required|string|max:50',
            'orden' => 'sometimes|required|integer',
            'es_portada' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $multimedia = InmuebleMultimedia::find($id);

            if (!$multimedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'El recurso multimedia solicitado no existe.',
                ], 404);
            }

            $idInmueble = $validatedData['id_rsv_catalogo_inmueble'] ?? $multimedia->id_rsv_catalogo_inmueble;
            $esPortada = array_key_exists('es_portada', $validatedData) ? $validatedData['es_portada'] : $multimedia->es_portada;

            if ($esPortada && (!$multimedia->es_portada || $idInmueble != $multimedia->id_rsv_catalogo_inmueble)) {
                InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $idInmueble)
                    ->where('id', '!=', $multimedia->id)
                    ->update(['es_portada' => false]);
            }

            DB::transaction(function () use ($multimedia, $validatedData) {
                $multimedia->update($validatedData);
            });

            $multimedia->load('inmueble:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Recurso multimedia actualizado exitosamente.',
                'data' => $multimedia,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en InmuebleMultimediaController@update: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el recurso multimedia.',
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
            $multimedia = InmuebleMultimedia::find($id);

            if (!$multimedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'El recurso multimedia solicitado no existe.',
                ], 404);
            }

            DB::transaction(function () use ($multimedia) {
                $multimedia->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Recurso multimedia eliminado exitosamente.',
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en InmuebleMultimediaController@destroy: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el recurso multimedia.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Establecer un recurso como la portada principal del inmueble.
     */
    public function establecerPortada(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $multimedia = InmuebleMultimedia::find($id);

            if (!$multimedia) {
                return response()->json([
                    'success' => false,
                    'message' => 'El recurso multimedia solicitado no existe.',
                ], 404);
            }

            if ($multimedia->es_portada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este recurso ya es la portada del inmueble.',
                ], 422);
            }

            DB::transaction(function () use ($multimedia) {
                InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $multimedia->id_rsv_catalogo_inmueble)
                    ->update(['es_portada' => false]);

                $multimedia->update(['es_portada' => true]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Portada establecida exitosamente.',
                'data' => $multimedia,
            ], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en InmuebleMultimediaController@establecerPortada: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al establecer la portada.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
