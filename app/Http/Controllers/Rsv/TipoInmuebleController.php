<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\TipoInmueble;
use App\Models\Rsv\CatalogoInmueble;
use App\Http\Requests\Rsv\StoreTipoInmuebleRequest;
use App\Http\Requests\Rsv\UpdateTipoInmuebleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TipoInmuebleController extends Controller
{
    /**
     * Listar todos los tipos de inmuebles con soporte de contexto global o por inmueble.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            // 1. Resolver inmueble objetivo (modo individual vs global)
            $inmuebleId = $request->input('inmueble_id');
            $tiInm = null;

            if (!empty($inmuebleId) && is_numeric($inmuebleId)) {
                $tiInm = CatalogoInmueble::find($inmuebleId);
            }

            $modoGlobal = !$tiInm;

            // 2. Resolver tipo asignado si estamos en modo individual
            $tipoAsignadoId = null;
            $tipoAsignado = null;

            if (!$modoGlobal && $tiInm) {
                $tipoAsignadoId = $tiInm->tipo_inmueble_id ?? null;
            }

            // 3. Obtener colección de tipos con conteo de inmuebles
            $tiposCollection = TipoInmueble::withCount('catalogoInmuebles')->orderBy('nombre', 'asc')->get();

            // 4. Si hay tipo asignado, ordenarlo primero (visible sin scroll)
            if (!$modoGlobal && $tipoAsignadoId) {
                $tipoAsignado = $tiposCollection->firstWhere('id', $tipoAsignadoId);

                $tiposCollection = $tiposCollection
                    ->sortByDesc(fn ($t) => $t->id == $tipoAsignadoId ? 1 : 0)
                    ->values();
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $tiposCollection,
                    'context' => [
                        'modoGlobal'     => $modoGlobal,
                        'tipoAsignadoId' => $tipoAsignadoId,
                    ]
                ], 200);
            }

            return view('rsv.admin.partials.tab-tipos-inmuebles', compact(
                'tiposCollection',
                'modoGlobal',
                'tiInm',
                'tipoAsignado',
                'tipoAsignadoId'
            ));

        } catch (Throwable $e) {
            Log::error('Error en TipoInmuebleController@index: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al obtener los tipos de inmueble.'], 500);
            }
            return back()->with('error', 'Ocurrió un error al cargar los tipos de inmuebles.');
        }
    }

    /**
     * Guardar un nuevo tipo de inmueble.
     */
    public function store(StoreTipoInmuebleRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $validatedData = $request->validated();

            $tipoInmueble = DB::transaction(function () use ($validatedData) {
                return TipoInmueble::create($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de inmueble creado exitosamente.',
                    'data'    => $tipoInmueble
                ], 201);
            }

            return back()->with('success', 'Tipo de inmueble creado exitosamente.');

        } catch (Throwable $e) {
            Log::error('Error en TipoInmuebleController@store: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al registrar el tipo de inmueble.'], 500);
            }
            return back()->withInput()->with('error', 'Ocurrió un error al registrar el tipo de inmueble.');
        }
    }

    /**
     * Mostrar la información de un tipo de inmueble específico.
     */
    public function show(Request $request, TipoInmueble $tipoInmueble): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $tipoInmueble
        ], 200);
    }

    /**
     * Actualizar un tipo de inmueble.
     */
    public function update(UpdateTipoInmuebleRequest $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $tipoInmueble = TipoInmueble::findOrFail($id);
            $validatedData = $request->validated();

            DB::transaction(function () use ($tipoInmueble, $validatedData) {
                $tipoInmueble->update($validatedData);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de inmueble actualizado exitosamente.',
                    'data'    => $tipoInmueble->fresh()
                ], 200);
            }

            return back()->with('success', 'Tipo de inmueble actualizado exitosamente.');

        } catch (Throwable $e) {
            Log::error('Error en TipoInmuebleController@update: ' . $e->getMessage());
            if ($request->wantsJson() || $request->ajax()) return response()->json(['success' => false, 'message' => 'Ocurrió un error al actualizar.'], 500);
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar.');
        }
    }
}
