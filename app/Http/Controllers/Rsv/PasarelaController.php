<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Models\Rsv\Pasarela;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasarelaController extends Controller
{
    /* ═══════════════ VALIDACIÓN INLINE (sin FormRequests) ═══════════════ */

    /** Reglas compartidas. El nombre es único en rsv_pasarelas (ignora su propio id al editar). */
    private function reglas(?int $idActual = null): array
    {
        $tabla = (new Pasarela)->getTable(); // 'rsv_pasarelas', sin adivinar

        $unico = Rule::unique($tabla, 'nombre');
        if ($idActual) {
            $unico->ignore($idActual);
        }

        return [
            'nombre' => ['required', 'string', 'max:100', $unico],
            'icono'  => ['nullable', 'string', 'max:60'],
            'color'  => ['nullable', 'string', 'max:7'],
        ];
    }

    /** Mensajes en español que el modal pinta bajo cada campo. */
    private function mensajes(): array
    {
        return [
            'nombre.required' => 'El nombre de la pasarela es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 100 caracteres.',
            'nombre.unique'   => 'Ya existe una pasarela con ese nombre.',
            'icono.max'       => 'La clase del ícono no puede superar 60 caracteres.',
            'color.max'       => 'El color debe ser hexadecimal (ej. #3d7a5c).',
        ];
    }

    /* ═══════════════════════ LECTURA ═══════════════════════ */

    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        try {
            $pasarelas = Pasarela::query()
                ->when(
                    $request->filled('search'),
                    fn ($q) => $q->where('nombre', 'like', '%'.trim((string) $request->input('search')).'%')
                )
                ->orderBy('nombre')
                ->paginate(max(1, (int) $request->input('per_page', 15)))
                ->withQueryString();

            if ($this->quiereJson($request)) {
                return $this->ok('Listado de pasarelas de pago obtenido exitosamente.', $pasarelas);
            }

            return view('rsv.pasarelas.index', compact('pasarelas'));

        } catch (Throwable $e) {
            return $this->fallo($request, 'al obtener el listado de pasarelas', $e);
        }
    }

    /* ═══════════════════════ ALTA ═══════════════════════ */

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $datos    = $request->validate($this->reglas(), $this->mensajes());
            $pasarela = DB::transaction(fn () => Pasarela::create($datos));

            if ($this->quiereJson($request)) {
                return $this->ok('Pasarela de pago creada exitosamente.', $pasarela, 201);
            }

            return back()->with('success', 'Pasarela de pago creada exitosamente.');

        } catch (ValidationException $e) {
            throw $e;   /* ⚠️ CLAVE: re-lanzar para que Laravel devuelva 422 y el modal pinte los errores */
        } catch (Throwable $e) {
            return $this->fallo($request, 'al registrar la pasarela de pago', $e);
        }
    }

    /* ═══════════════════════ ACTUALIZACIÓN ═══════════════════════ */

    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $pasarela = Pasarela::findOrFail($id);

            $datos = $request->validate($this->reglas((int) $id), $this->mensajes());

            DB::transaction(fn () => $pasarela->update($datos));

            if ($this->quiereJson($request)) {
                return $this->ok('Pasarela de pago actualizada exitosamente.', $pasarela->fresh());
            }

            return back()->with('success', 'Pasarela de pago actualizada exitosamente.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (ModelNotFoundException) {
            return $this->noEncontrada($request);
        } catch (Throwable $e) {
            return $this->fallo($request, 'al actualizar la pasarela de pago', $e);
        }
    }

    /* ═══════════════════════ BAJA ═══════════════════════ */

    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        try {
            $pasarela = Pasarela::findOrFail($id);

            DB::transaction(fn () => $pasarela->delete());

            if ($this->quiereJson($request)) {
                return $this->ok('Pasarela de pago eliminada exitosamente.');
            }

            return back()->with('success', 'Pasarela de pago eliminada exitosamente.');

        } catch (ModelNotFoundException) {
            return $this->noEncontrada($request);
        } catch (Throwable $e) {
            // FK: la pasarela tiene transacciones asociadas y no puede borrarse
            if ($this->esViolacionDeIntegridad($e)) {
                $msg = 'No se puede eliminar la pasarela porque tiene transacciones asociadas.';

                if ($this->quiereJson($request)) {
                    return response()->json(['success' => false, 'message' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            return $this->fallo($request, 'al eliminar la pasarela de pago', $e);
        }
    }

    /* ═══════════════════════ NO USADOS (compatibilidad de rutas) ═══════════════════════ */

    public function create(): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Método no implementado.'], 405);
    }

    public function edit(string $id): JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Método no implementado.'], 405);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        try {
            return $this->ok('Pasarela obtenida exitosamente.', Pasarela::findOrFail($id));
        } catch (ModelNotFoundException) {
            return $this->noEncontrada($request);
        } catch (Throwable $e) {
            return $this->fallo($request, 'al obtener la pasarela', $e);
        }
    }

    /* ═══════════════════════ HELPERS PRIVADOS ═══════════════════════ */

    private function quiereJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax();
    }

    private function ok(string $mensaje, mixed $data = null, int $codigo = 200): JsonResponse
    {
        $payload = ['success' => true, 'message' => $mensaje];

        if (! is_null($data)) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $codigo);
    }

    private function noEncontrada(Request $request): JsonResponse|RedirectResponse
    {
        $mensaje = 'La pasarela solicitada no existe.';

        if ($this->quiereJson($request)) {
            return response()->json(['success' => false, 'message' => $mensaje], 404);
        }

        return back()->with('error', $mensaje);
    }

    private function fallo(Request $request, string $accion, Throwable $e): JsonResponse|RedirectResponse
    {
        Log::error("Error en PasarelaController {$accion}: {$e->getMessage()}", [
            'trace' => $e->getTraceAsString(),
        ]);

        $mensaje = "Ocurrió un error {$accion}.";

        if ($this->quiereJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $mensaje,
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return back()->withInput()->with('error', $mensaje);
    }

    private function esViolacionDeIntegridad(Throwable $e): bool
    {
        return str_contains($e->getMessage(), 'SQLSTATE[23000]')
            || str_contains($e->getMessage(), 'Integrity constraint violation');
    }
}
