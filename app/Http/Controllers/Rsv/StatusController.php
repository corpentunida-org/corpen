<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Status;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use App\Http\Requests\Rsv\StoreStatusRequest;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     * Soporte Dual (Web/JSON) con filtros de búsqueda y paginación.
     */
    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $query = Status::query();

            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', "%{$request->search}%");
            }

            $perPage = $request->get('per_page', 15);
            $statuses = $query->orderBy('name', 'asc')->paginate($perPage);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Listado de estados obtenido exitosamente.',
                    'data' => $statuses,
                ], 200);
            }

            return view('rsv.statuses.index', compact('statuses'));

        } catch (Throwable $e) {
            Log::error('Error en StatusController@index: ' . $e->getMessage());
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Error al obtener estados.'], 500)
                : back()->with('error', 'Ocurrió un error al obtener el listado de estados.');
        }
    }

    public function create(): JsonResponse { return response()->json(['success' => false], 405); }

    /**
     * Store a newly created resource.
     * Dual: Web → redirect con flash · AJAX → JSON.
     */
    public function store(StoreStatusRequest $request): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());

        DB::beginTransaction();
        try {
            $status = DB::transaction(fn() => Status::create($request->validated()));

            return $esWeb
                ? back()->with('success', 'Estado "' . $status->name . '" creado exitosamente.')
                : response()->json(['success' => true, 'message' => 'Estado creado exitosamente.', 'data' => $status], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@store: ' . $e->getMessage());

            return $esWeb
                ? back()->with('error', 'Ocurrió un error al registrar el estado.')->withInput()
                : response()->json(['success' => false, 'message' => 'Error al registrar el estado.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $rsvStatus): JsonResponse
    {
        try {
            $status = Status::find($rsvStatus);
            if (!$status) return response()->json(['success' => false, 'message' => 'No existe.'], 404);
            return response()->json(['success' => true, 'data' => $status], 200);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener.'], 500);
        }
    }

    public function edit(string $id): JsonResponse { return response()->json(['success' => false], 405); }

    /**
     * Update the specified resource.
     *
     * FIX CRÍTICO: validación INLINE en lugar de UpdateStatusRequest.
     *
     * Antes: UpdateStatusRequest hacía $this->route('rsvStatus') para el
     * unique. Si el nombre del parámetro en web.php no coincidía EXACTAMENTE,
     * llegaba null y la regla "unique:rsv_statuses,name," (sin ID a ignorar)
     * rechazaba el nombre aunque fuera el mismo → validación fallaba en
     * silencio → redirect sin mensaje → "no se aplican los cambios".
     *
     * Ahora: el ID a ignorar viene del propio parámetro del método
     * ($rsvStatus), que Laravel SIEMPRE inyecta correctamente.
     */
    public function update(Request $request, string $rsvStatus): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());

        DB::beginTransaction();
        try {
            $status = Status::find($rsvStatus);
            if (!$status) {
                return $esWeb
                    ? back()->with('error', 'El estado solicitado no existe.')
                    : response()->json(['success' => false, 'message' => 'No existe.'], 404);
            }

            // Validación INLINE: $rsvStatus del método es el ID a ignorar en unique
            $validated = $request->validate([
                'name'      => 'required|string|max:255|unique:rsv_statuses,name,' . $rsvStatus,
                'color_hex' => 'nullable|string|max:7',
            ]);

            DB::transaction(fn() => $status->update($validated));

            return $esWeb
                ? back()->with('success', 'Estado "' . $status->name . '" actualizado exitosamente.')
                : response()->json(['success' => true, 'message' => 'Actualizado exitosamente.', 'data' => $status], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@update: ' . $e->getMessage());

            return $esWeb
                ? back()->with('error', 'Ocurrió un error al actualizar el estado.')->withInput()
                : response()->json(['success' => false, 'message' => 'Error al actualizar.'], 500);
        }
    }

    /**
     * Remove the specified resource.
     *
     * Guard server-side: no eliminar si tiene reservas asociadas.
     * Dual: Web → redirect con flash · AJAX → JSON.
     */
    public function destroy(Request $request, string $rsvStatus): JsonResponse|RedirectResponse
    {
        $esWeb = !($request->wantsJson() || $request->ajax());

        DB::beginTransaction();
        try {
            $status = Status::find($rsvStatus);
            if (!$status) {
                return $esWeb
                    ? back()->with('error', 'El estado solicitado no existe.')
                    : response()->json(['success' => false, 'message' => 'No existe.'], 404);
            }

            // Guard server-side: no eliminar si tiene reservas asociadas
            $conReservas = $status->reservas()->exists();
            if ($conReservas) {
                $n = $status->reservas()->count();
                return $esWeb
                    ? back()->with('error', "No se puede eliminar: el estado tiene {$n} reserva(s) asociada(s).")
                    : response()->json(['success' => false, 'message' => 'No se puede eliminar: tiene reservas asociadas.'], 422);
            }

            $nombre = $status->name;
            DB::transaction(fn() => $status->delete());

            return $esWeb
                ? back()->with('success', 'Estado "' . $nombre . '" eliminado exitosamente.')
                : response()->json(['success' => true, 'message' => 'Eliminado exitosamente.'], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en StatusController@destroy: ' . $e->getMessage());

            return $esWeb
                ? back()->with('error', 'Ocurrió un error al eliminar el estado.')
                : response()->json(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }
}
