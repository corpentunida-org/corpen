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
     * Listar todos los tipos de inmuebles.
     * Soporte Dual (Web/JSON).
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $tiposInmueble = TipoInmueble::orderBy('nombre', 'asc')->get();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data'    => $tiposInmueble
                ], 200);
            }

            // Aquí puedes retornar a una vista si decides administrar esto en una pantalla propia
            return view('rsv.admin.partials.tab-tipos-inmuebles', compact('tiposInmueble'));

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
        // Generalmente 'show' para catálogos pequeños se consume vía AJAX para rellenar modales de edición
        return response()->json([
            'success' => true,
            'data'    => $tipoInmueble
        ], 200);
    }

    /**
     * Actualizar un tipo de inmueble.
     */
    public function update(UpdateTipoInmuebleRequest $request, TipoInmueble $tipoInmueble): JsonResponse|RedirectResponse
    {
        try {
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

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al actualizar.'], 500);
            }
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar.');
        }
    }

    /**
     * Eliminar o desactivar el tipo de inmueble.
     */
    public function destroy(Request $request, TipoInmueble $tipoInmueble): JsonResponse|RedirectResponse
    {
        try {
            // Protección de Integridad Referencial: Verificamos si hay inmuebles usando esta categoría
            $inmueblesAsociados = CatalogoInmueble::where('tipo_inmueble_id', $tipoInmueble->id)->count();

            if ($inmueblesAsociados > 0) {
                $msg = "No se puede eliminar o desactivar este tipo porque tiene {$inmueblesAsociados} inmueble(s) asociado(s).";

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return back()->with('error', $msg);
            }

            // Respetando tu lógica original: desactivar en lugar de eliminar físicamente (Soft-Delete manual)
            DB::transaction(function () use ($tipoInmueble) {
                $tipoInmueble->update(['active' => false]);
                // Si prefieres eliminarlo físicamente de la base de datos, cambia la línea anterior por:
                // $tipoInmueble->delete();
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de inmueble desactivado exitosamente.'
                ], 200);
            }

            return back()->with('success', 'Tipo de inmueble desactivado exitosamente.');

        } catch (Throwable $e) {
            Log::error('Error en TipoInmuebleController@destroy: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Ocurrió un error al intentar desactivar el registro.'], 500);
            }
            return back()->with('error', 'Ocurrió un error al intentar desactivar el registro.');
        }
    }
}
