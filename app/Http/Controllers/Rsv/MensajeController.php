<?php

namespace App\Http\Controllers\Rsv;

use App\Http\Controllers\Controller;
use App\Mail\ReservaInmueble;
use App\Models\Rsv\Mensaje;
use App\Models\Rsv\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
        // El portal envía el texto en "contenido" y lo copia a "mensaje" con JS; se acepta cualquiera
        if (!$request->filled('mensaje') && $request->filled('contenido')) {
            $request->merge(['mensaje' => $request->input('contenido')]);
        }
        $validatedData = $request->validate([
            'id_rsv_reservas' => 'required|exists:rsv_reservas,id',
            'mensaje' => 'required|string|max:1000',
            'url_archivo' => 'nullable|file|max:5120', // Permite adjuntos hasta 5MB
        ]);

        // El remitente es SIEMPRE el usuario autenticado (antes se tomaba del formulario y se
        // podía escribir a nombre de otro). Si escribe el dueño de la reserva es una consulta del
        // asociado (queda "sin responder"); si escribe otro usuario (panel admin) es una respuesta.
        $reserva   = Reserva::find($validatedData['id_rsv_reservas']);
        $uid       = Auth::id();
        $esTitular = (int) $reserva->id_user === (int) $uid;
        if (!$esTitular && !\App\Services\Rsv\RsvAcceso::puede(\App\Services\Rsv\RsvAcceso::SOPORTES_RESPONDER)) {
            return \App\Services\Rsv\RsvAcceso::denegado('Solo puedes escribir en el soporte de tus reservas.');
        }

        // El chat con el asociado es en vivo: sin agente en línea se le ofrece crear un ticket.
        if ($esTitular && !\App\Services\Rsv\RsvSoporteAsociado::hayAgenteEnLinea((int) $uid)) {
            return response()->json([
                'success'    => false,
                'sin_agente' => true,
                'message'    => 'En este momento no hay agentes en línea. Puedes crear un ticket de soporte y te contactaremos.',
            ], 409);
        }

        DB::beginTransaction();

        try {
            if (!$esTitular) {
                // Respuesta del equipo: lo anterior queda atendido
                Mensaje::where('id_rsv_reservas', $reserva->id)->whereNull('leido_en')->update(['leido_en' => now()]);
            }

            // Mapeamos los datos al nombre exacto que tiene el Modelo (contenido)
            $dataInsert = [
                'id_rsv_reservas'   => $reserva->id,
                'id_user_remitente' => $uid,
                'contenido'         => $validatedData['mensaje'],
                'tipo_mensaje'      => 'texto',
                'leido_en'          => $esTitular ? null : now(),
            ];

            // Si subió un archivo por el chat, lo guardamos
            if ($request->hasFile('url_archivo')) {
                $path = $request->file('url_archivo')->store('mensajes_chat', 'public');
                $dataInsert['url_archivo'] = '/storage/' . $path;
                $dataInsert['tipo_mensaje'] = 'archivo';
            }

            $mensaje = Mensaje::create($dataInsert);
            DB::commit();

            if (!$esTitular) {
                $this->notificarRespuestaAsociado($reserva, $mensaje);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado exitosamente.',
                'data' => $mensaje,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
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

    /**
     * Responde directamente en el hilo de una reserva (rsv_mensajes) sin WhatsApp ni llamadas.
     */
    /**
     * Correo al asociado: el equipo respondió su consulta de soporte. Botón directo a la reserva
     * en Mis Reservas. Un fallo del correo solo se registra; el mensaje ya quedó guardado.
     */
    private function notificarRespuestaAsociado(Reserva $reserva, Mensaje $mensaje): void
    {
        try {
            $user = $reserva->user;
            if (!$user || empty($user->email) || (int) $user->id === (int) $mensaje->id_user_remitente) {
                return;
            }
            $texto = 'Tiene una nueva respuesta del equipo de soporte sobre su reserva <strong>' . e($reserva->codigo_reserva) . '</strong>'
                . ' en <strong>' . e(optional($reserva->inmueble)->name ?? 'el inmueble') . '</strong>:<br><br>'
                . '<em>«' . nl2br(e(\Illuminate\Support\Str::limit((string) $mensaje->contenido, 600))) . '»</em><br><br>'
                . ($mensaje->url_archivo ? 'La respuesta incluye un archivo adjunto que puede ver en el portal.<br><br>' : '')
                . 'Puede leer la conversación completa y responder desde el Portal de Reservas. ¡Dios le bendiga!';

            Mail::to($user->email)->send(new ReservaInmueble(
                $user->name,
                $texto,
                'Respuesta de soporte · Reserva ' . $reserva->codigo_reserva,
                false,
                null,
                url(route('rsv.cliente.portal')) . '?tab=mis-reservas&reserva_id=' . $reserva->id,
                'Ver la conversación'
            ));
        } catch (Throwable $e) {
            Log::error('Rsv soporte: no se pudo avisar al asociado (reserva ' . $reserva->id . '): ' . $e->getMessage());
        }
    }

    public function responder(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'respuesta'   => 'required|string',
            'url_archivo' => 'nullable|file|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $reservaId = (int) $id;
            $adminId   = \Illuminate\Support\Facades\Auth::id();

            // Marcar como leídos los mensajes anteriores de esa reserva
            Mensaje::where('id_rsv_reservas', $reservaId)
                ->whereNull('leido_en')
                ->update(['leido_en' => now()]);

            $dataInsert = [
                'id_rsv_reservas'   => $reservaId,
                'id_user_remitente' => $adminId,
                'tipo_mensaje'      => 'texto',
                'contenido'         => $validated['respuesta'],
                'leido_en'          => now(),
            ];

            if ($request->hasFile('url_archivo')) {
                $path = $request->file('url_archivo')->store('mensajes_chat', 'public');
                $dataInsert['url_archivo'] = '/storage/' . $path;
                $dataInsert['tipo_mensaje'] = 'archivo';
            }

            $nuevo = Mensaje::create($dataInsert);
            DB::commit();

            if ($reserva = Reserva::find($reservaId)) {
                $this->notificarRespuestaAsociado($reserva, $nuevo);
            }

            return response()->json([
                'success' => true,
                'message' => 'Respuesta registrada correctamente en rsv_mensajes.',
                'data'    => $nuevo,
            ], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error en MensajeController@responder: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar la respuesta en la base de datos.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Marca todos los mensajes de una reserva como leídos o pendientes de lectura (leido_en).
     */
    public function cambiarEstado(Request $request, string $id): JsonResponse
    {
        try {
            $reservaId = (int) $id;
            $estado    = $request->input('estado', 'respondido');

            if ($estado === 'sin_responder') {
                Mensaje::where('id_rsv_reservas', $reservaId)
                    ->latest('id')
                    ->limit(1)
                    ->update(['leido_en' => null]);
            } else {
                Mensaje::where('id_rsv_reservas', $reservaId)
                    ->whereNull('leido_en')
                    ->update(['leido_en' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado en rsv_mensajes.',
            ], 200);

        } catch (Throwable $e) {
            Log::error('Error en MensajeController@cambiarEstado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado.',
            ], 500);
        }
    }


}
