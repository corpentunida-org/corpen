<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\InmuebleMultimedia;
use App\Models\Rsv\CatalogoInmueble;
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
 * Maneja subida a S3, reemplazo y transacciones en DB.
 */
class InmuebleMultimediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = InmuebleMultimedia::with(['inmueble:id,name,active,tipo_inmueble_id', 'inmueble.tipoInmueble']);

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

            // Variables globales extraídas de la vista
            $modoGlobal = true;
            $mediaCollection = $multimedia;
            $listaInmueblesOpt = CatalogoInmueble::with('tipoInmueble')->orderBy('name', 'asc')->get();
            $gmUid = 'gm' . substr(md5(uniqid('', true)), 0, 8);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de multimedia obtenido exitosamente.',
                    'data'    => $multimedia,
                ], 200);
            }

            return view('rsv.multimedia.index', compact('multimedia', 'modoGlobal', 'mediaCollection', 'listaInmueblesOpt', 'gmUid'));

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

    public function show(Request $request, string $id): JsonResponse|View
    {
        try {
            $inmueble = CatalogoInmueble::with([
                'multimedia.inmueble:id,name,active,tipo_inmueble_id',
                'multimedia.inmueble.tipoInmueble'
            ])->findOrFail($id);

            $modoGlobal = false;
            $mediaCollection = $inmueble->multimedia->sortBy('orden');
            $listaInmueblesOpt = CatalogoInmueble::with('tipoInmueble')->orderBy('name', 'asc')->get();
            $gmUid = 'gm' . substr(md5(uniqid('', true)), 0, 8);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $mediaCollection], 200);
            }

            return view('rsv.multimedia.show', compact('inmueble', 'modoGlobal', 'mediaCollection', 'listaInmueblesOpt', 'gmUid'));

        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'El recurso no existe.'], 404);
        } catch (Throwable $e) {
            Log::error('Error en InmuebleMultimediaController@show: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno.'], 500);
        }
    }

    public function create(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Método no implementado para API REST.'], 405);
    }

    /**
     * Sube un archivo a AWS S3 y guarda su registro.
     */
    public function store(StoreInmuebleMultimediaRequest $request): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validated();
        $uploadedPath = null;

        try {
            if ($request->hasFile('url_archivo')) {
                $uploadedPath = $request->file('url_archivo')->store('inmuebles/multimedia', 's3');
                $validatedData['url_archivo'] = $uploadedPath;
            }

            $multimedia = DB::transaction(function () use ($validatedData) {
                if (!empty($validatedData['es_portada'])) {
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

            return redirect()->back()
                ->with('success', 'Multimedia registrada exitosamente.');

        } catch (Throwable $e) {
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

            if ($request->hasFile('url_archivo')) {
                $oldPathToDelete = $multimedia->getRawOriginal('url_archivo');
                $newUploadedPath = $request->file('url_archivo')->store('inmuebles/multimedia', 's3');
                $validatedData['url_archivo'] = $newUploadedPath;
            } else {
                unset($validatedData['url_archivo']);
            }

            DB::transaction(function () use ($multimedia, $validatedData) {
                $idInmueble = $validatedData['id_rsv_catalogo_inmueble'] ?? $multimedia->id_rsv_catalogo_inmueble;
                $esPortada  = array_key_exists('es_portada', $validatedData) ? $validatedData['es_portada'] : $multimedia->es_portada;

                if ($esPortada && (!$multimedia->es_portada || $idInmueble != $multimedia->id_rsv_catalogo_inmueble)) {
                    InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $idInmueble)
                        ->where('id', '!=', $multimedia->id)
                        ->update(['es_portada' => false]);
                }

                $multimedia->update($validatedData);
            });

            if ($oldPathToDelete && !filter_var($oldPathToDelete, FILTER_VALIDATE_URL)) {
                Storage::disk('s3')->delete($oldPathToDelete);
            }

            if ($request->wantsJson() || $request->ajax()) {
                $multimedia->load('inmueble:id,name');
                return response()->json(['success' => true, 'message' => 'Actualizado exitosamente.', 'data' => $multimedia], 200);
            }

            return redirect()->back()
                ->with('success', 'Multimedia actualizada exitosamente.');

        } catch (ModelNotFoundException $e) {
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'No existe.'], 404);
            return back()->with('error', 'El recurso no existe.');
        } catch (Throwable $e) {
            if ($newUploadedPath) Storage::disk('s3')->delete($newUploadedPath);

            Log::error('Error en InmuebleMultimediaController@update: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar.');
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
                return redirect()->back()->with('info', $msg);
            }

            DB::transaction(function () use ($multimedia) {
                InmuebleMultimedia::where('id_rsv_catalogo_inmueble', $multimedia->id_rsv_catalogo_inmueble)
                    ->update(['es_portada' => false]);

                $multimedia->update(['es_portada' => true]);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Portada establecida exitosamente.', 'data' => $multimedia], 200);
            }

            return redirect()->back()
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
