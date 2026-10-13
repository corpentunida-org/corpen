<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\InmuebleMultimedia;
use App\Http\Requests\Rsv\StoreInmuebleMultimediaRequest;
use App\Http\Requests\Rsv\UpdateInmuebleMultimediaRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;
use Throwable;

/**
 * Controlador para la gestión de la Galería Multimedia de los Inmuebles.
 * Maneja subida a S3, reemplazo y eliminación segura de archivos junto a registros en DB.
 */
class InmuebleMultimediaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros dinámicos, ordenamiento y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = InmuebleMultimedia::with('inmueble:id,name,active');

            // Filtros limpios usando 'when'
            $query->when($request->filled('id_rsv_catalogo_inmueble'), function ($q) use ($request) {
                $q->where('id_rsv_catalogo_inmueble', $request->id_rsv_catalogo_inmueble);
            })->when($request->filled('tipo_multimedia'), function ($q) use ($request) {
                $q->where('tipo_multimedia', $request->tipo_multimedia);
            })->when($request->has('es_portada'), function ($q) use ($request) {
                $q->where('es_portada', filter_var($request->es_portada, FILTER_VALIDATE_BOOLEAN));
            });

            $perPage = $request->get('per_page', 15);
            $multimedia = $query->orderBy('orden', 'asc')
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de multimedia obtenido exitosamente.',
                    'data'    => $multimedia,
                ], 200);
            }

            return view('rsv.multimedia.index', compact('multimedia'));

        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@index: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al obtener el listado de multimedia.',
                    'error'   => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', 'Ocurrió un error al cargar la galería multimedia.');
        }
    }

    public function create(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Método no implementado para API REST.'], 405);
    }

    /**
     * Sube un archivo a AWS S3 y guarda su registro.
     * Implementa rollback de S3 si la base de datos falla.
     */
    public function store(StoreInmuebleMultimediaRequest $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $uploadedPath = null;

        try {
            // 1. Subir a S3 primero
            if ($request->hasFile('url_archivo')) {
                $uploadedPath = $request->file('url_archivo')->store('inmuebles/multimedia', 's3');
                $validatedData['url_archivo'] = $uploadedPath;
            }

            // 2. Ejecutar lógica de DB en transacción
            $multimedia = DB::transaction(function () use ($validatedData) {
                if ($validatedData['es_portada']) {
                    InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $validatedData['id_rsv_catalogo_inmueble'])
                        ->update(['es_portada' => false]);
                }
                return InmuebleMultimedia::create($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                $multimedia->load('inmueble:id,name');
                return response()->json([
                    'success' => true,
                    'message' => 'Recurso multimedia registrado exitosamente.',
                    'data'    => $multimedia,
                ], 201);
            }

            return redirect()->route('rsv.inmuebles.show', $validatedData['id_rsv_catalogo_inmueble'])
                ->with('success', 'Multimedia registrada exitosamente.');

        } catch (Throwable $e) {
            // Rollback manual de S3: si falla la DB, borramos el archivo huérfano
            if ($uploadedPath) {
                Storage::disk('s3')->delete($uploadedPath);
            }

            Log::error('Error en InmuebleMultimediaController@store: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ocurrió un error al registrar el recurso multimedia.',
                ], 500);
            }

            return back()->withInput()->with('error', 'Ocurrió un error al registrar el recurso multimedia.');
        }
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $multimedia = InmuebleMultimedia::with('inmueble')->findOrFail($id);
            return response()->json(['success' => true, 'data' => $multimedia], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'El recurso no existe.'], 404);
        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@show: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno.'], 500);
        }
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Método no implementado.'], 405);
    }

    /**
     * Actualiza el registro. Si se sube un archivo nuevo, lo reemplaza en S3.
     */
    public function update(UpdateInmuebleMultimediaRequest $request, string $id): JsonResponse|RedirectResponse
    {
        $newUploadedPath = null;
        $oldPathToDelete = null;

        try {
            $multimedia = InmuebleMultimedia::findOrFail($id);
            $validatedData = $request->validated();

            // 1. Manejo del nuevo archivo en S3
            if ($request->hasFile('url_archivo')) {
                $oldPathToDelete = $multimedia->getRawOriginal('url_archivo');

                $newUploadedPath = $request->file('url_archivo')->store('inmuebles/multimedia', 's3');
                $validatedData['url_archivo'] = $newUploadedPath;
            } else {
                // Evitamos sobrescribir la URL actual con null
                unset($validatedData['url_archivo']);
            }

            // 2. Transacción de Base de Datos
            DB::transaction(function () use ($multimedia, $validatedData) {
                $idInmueble = $validatedData['id_rsv_catalogo_inmueble'] ?? $multimedia->id_rsv_catalogo_inmueble;
                $esPortada  = array_key_exists('es_portada', $validatedData) ? $validatedData['es_portada'] : $multimedia->es_portada;

                // Lógica singleton de Portada
                if ($esPortada && (!$multimedia->es_portada || $idInmueble != $multimedia->id_rsv_catalogo_inmueble)) {
                    InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $idInmueble)
                        ->where('id', '!=', $multimedia->id)
                        ->update(['es_portada' => false]);
                }

                $multimedia->update($validatedData);
            });

            // 3. Borrado del archivo anterior de S3 SOLO si la DB se actualizó correctamente
            if ($oldPathToDelete && !filter_var($oldPathToDelete, FILTER_VALIDATE_URL)) {
                Storage::disk('s3')->delete($oldPathToDelete);
            }

            if ($request->wantsJson() || $request->ajax()) {
                $multimedia->load('inmueble:id,name');
                return response()->json(['success' => true, 'message' => 'Actualizado exitosamente.', 'data' => $multimedia], 200);
            }

            return redirect()->route('rsv.inmuebles.show', $multimedia->id_rsv_catalogo_inmueble)
                ->with('success', 'Multimedia actualizada exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'No existe.'], 404);
            return back()->with('error', 'El recurso no existe.');
        } catch (Throwable $e) {
            // Si la DB falla y habíamos subido un archivo nuevo, lo borramos de S3
            if ($newUploadedPath) Storage::disk('s3')->delete($newUploadedPath);

            Log::error('Error en InmuebleMultimediaController@update: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar.');
        }
    }

    /**
     * Borra el registro en DB y el archivo físico de S3.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $multimedia = InmuebleMultimedia::findOrFail($id);
            $inmuebleId = $multimedia->id_rsv_catalogo_inmueble;
            $oldPath = $multimedia->getRawOriginal('url_archivo');

            // 1. Borrar de la BD
            DB::transaction(function () use ($multimedia) {
                $multimedia->delete();
            });

            // 2. Borrar de S3 si la DB fue exitosa
            if ($oldPath && !filter_var($oldPath, FILTER_VALIDATE_URL)) {
                Storage::disk('s3')->delete($oldPath);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Recurso eliminado exitosamente.'], 200);
            }

            return redirect()->route('rsv.inmuebles.show', $inmuebleId)->with('success', 'Multimedia eliminada.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'No existe.'], 404);
            return back()->with('error', 'El recurso no existe.');
        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@destroy: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
            return back()->with('error', 'Ocurrió un error al eliminar.');
        }
    }

    /**
     * Establecer un recurso como la portada principal.
     */
    public function establecerPortada(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $multimedia = InmuebleMultimedia::findOrFail($id);

            if ($multimedia->es_portada) {
                $msg = 'Este recurso ya es la portada del inmueble.';
                if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => $msg], 422);
                return redirect()->route('rsv.inmuebles.show', $multimedia->id_rsv_catalogo_inmueble)->with('info', $msg);
            }

            DB::transaction(function () use ($multimedia) {
                // Quitar portada a las demás
                InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $multimedia->id_rsv_catalogo_inmueble)
                    ->update(['es_portada' => false]);

                // Asignar a la actual
                $multimedia->update(['es_portada' => true]);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Portada establecida exitosamente.', 'data' => $multimedia], 200);
            }

            return redirect()->route('rsv.inmuebles.show', $multimedia->id_rsv_catalogo_inmueble)
                ->with('success', 'Portada establecida exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'No existe.'], 404);
            return back()->with('error', 'El recurso no existe.');
        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@establecerPortada: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'Error al actualizar portada.'], 500);
            return back()->with('error', 'Ocurrió un error al establecer la portada.');
        }
    }
}
