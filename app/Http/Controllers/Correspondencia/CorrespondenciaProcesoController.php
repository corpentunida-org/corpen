<?php

namespace App\Http\Controllers\Correspondencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correspondencia\CorrespondenciaProceso;
use App\Models\Correspondencia\Correspondencia;
use App\Models\Correspondencia\Proceso;
use App\Models\Correspondencia\Estado;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\CorrespondenciaRemitenteNotificacion;
use App\Mail\FlujoNotificacion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CorrespondenciaProcesoController extends Controller
{
    public function index(Request $request)
    {
        $procesos_maestros = Proceso::all();
        $usuarios = User::whereHas('seguimientosCorrespondencia')->get();

        $query = CorrespondenciaProceso::with(['correspondencia', 'proceso.flujo', 'usuario']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_correspondencia', 'LIKE', "%$search%")->orWhere('observacion', 'LIKE', "%$search%");
            });
        }

        if ($request->filled('proceso_id')) {
            $query->where('id_proceso', $request->proceso_id);
        }

        if ($request->filled('usuario_id')) {
            $query->where('fk_usuario', $request->usuario_id);
        }

        $procesos = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('correspondencia.correspondencias_procesos.index', compact('procesos', 'procesos_maestros', 'usuarios'));
    }

    public function create()
    {
        $correspondencias = Correspondencia::select('id_radicado', 'asunto')->get();
        $procesos_disponibles = Proceso::with('flujo')->get();
        $estados = Estado::all();

        return view('correspondencia.correspondencias_procesos.create', compact('correspondencias', 'procesos_disponibles', 'estados'));
    }

    /**
     * Almacenamiento con Validación Dinámica de Arrays de Archivos y S3
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'id_proceso' => 'required|integer|exists:corr_procesos,id',
            'observacion' => 'required|string',
            'estado_id' => 'required|integer|exists:corr_estados,id',
            'fecha_gestion' => 'required|date',
            'finalizado' => 'nullable|boolean',
        ]);

        $proceso = Proceso::findOrFail($request->id_proceso);

        $numRequeridos = (int) $proceso->numero_archivos;

        if ($numRequeridos > 0) {
            $request->validate(
                [
                    'documento_arc' => 'required|array|size:' . $numRequeridos,
                    'documento_arc.*' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
                ],
                [
                    'documento_arc.required' => "Este proceso exige subir $numRequeridos archivo(s) obligatorio(s).",
                    'documento_arc.size' => "Debe subir exactamente $numRequeridos archivo(s) como lo exige el proceso.",
                    'documento_arc.*.mimes' => 'Solo se permiten archivos: PDF, DOC, DOCX, JPG o PNG.',
                ],
            );
        } else {
            $request->validate([
                'documento_arc' => 'nullable|array',
                'documento_arc.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ]);
        }

        try {
            $idRadicado = $request->id_correspondencia;
            $rutasArchivos = [];

            // Se sube ANTES de abrir la transacción: subir a S3 puede tardar segundos y dentro de
            // ella mantenía abierta la conexión a la base de datos todo ese tiempo.
            if ($request->hasFile('documento_arc')) {
                // Aseguramos de tener el array de nombres de archivos requeridos
                $nombresRequeridos = is_array($proceso->tipos_archivos) ? $proceso->tipos_archivos : json_decode($proceso->tipos_archivos, true) ?? [];

                // Agregamos el $index al foreach para saber qué archivo estamos procesando
                foreach ($request->file('documento_arc') as $index => $file) {
                    // Determinamos el nombre base. Si no existe un nombre requerido (ej. archivo opcional), usamos 'doc-adjunto'
                    $nombreBaseFormateado = isset($nombresRequeridos[$index]) ? Str::slug($nombresRequeridos[$index]) : 'doc-adjunto';

                    // 1. Generamos el nombre del archivo integrando el nombre requerido
                    $nombreArchivo = 'seg_' . $idRadicado . '_' . $nombreBaseFormateado . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // 2. Usamos storeAs a S3 sin forzar visibilidad pública
                    $path = $file->storeAs('corpentunida/correspondencia', $nombreArchivo, 's3');

                    // 3. Verificamos estrictamente que la subida haya sido exitosa
                    if ($path === false) {
                        throw new \Exception("Error al subir el archivo $nombreArchivo a Amazon S3. El bucket rechazó la solicitud.");
                    }

                    $rutasArchivos[] = $path;
                }
            }

            $estadoMaestro = Estado::findOrFail($request->estado_id);

            $seguimiento = DB::transaction(function () use ($request, $idRadicado, $proceso, $rutasArchivos, $estadoMaestro) {
                $seguimiento = CorrespondenciaProceso::create([
                    'id_correspondencia' => $idRadicado,
                    'observacion' => $request->observacion,
                    'estado' => $estadoMaestro->id,
                    'id_proceso' => $proceso->id,
                    // Queda en false hasta que el correo salga de verdad: ver notificarRemitente().
                    'notificado_email' => false,
                    'fecha_gestion' => $request->fecha_gestion,
                    'documento_arc' => $rutasArchivos,
                    'finalizado' => $request->boolean('finalizado'),
                    'fk_usuario' => auth()->id(),
                ]);

                Correspondencia::where('id_radicado', $idRadicado)->update([
                    'estado_id' => $estadoMaestro->id,
                ]);

                return $seguimiento;
            });

            $mensaje = 'Seguimiento registrado y archivos guardados correctamente.';
            if ($request->boolean('notificado_email') && !$this->notificarRemitente($seguimiento, $estadoMaestro)) {
                $mensaje .= ' No se notificó al remitente porque no tiene un correo válido en Maestra de Terceros.';
            }

            if ($request->boolean('finalizado')) {
                $siguienteProceso = Proceso::where('flujo_id', $proceso->flujo_id)
                    ->where('secuencia', $proceso->secuencia + 1)
                    ->first();

                if ($siguienteProceso) {
                    $correos = $siguienteProceso->usuarios()->wherePivot('activo', 1)->pluck('email')->toArray();

                    // Se envía al terminar la respuesta para que el usuario no espere al SMTP. Aquí
                    // la gestión YA quedó guardada: un fallo del correo no debe mostrarse como
                    // error, porque el usuario la registraría de nuevo y quedaría duplicada.
                    if ($correos) {
                        app()->terminating(function () use ($correos, $idRadicado, $siguienteProceso) {
                            try {
                                Mail::to($correos)->send(new FlujoNotificacion($idRadicado, $siguienteProceso->nombre));
                            } catch (\Throwable $e) {
                                Log::error('Error enviando notificación de flujo (radicado ' . $idRadicado . '): ' . $e->getMessage());
                            }
                        });
                    }
                }
            }

            return redirect()->route('correspondencia.correspondencias.show', $idRadicado)->with('success', $mensaje);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error en la operación: ' . $e->getMessage());
        }
    }

    /**
     * Envía al remitente la observación y los adjuntos de la gestión. Devuelve false si no tiene
     * un correo válido. Sale al terminar la respuesta (descargar adjuntos de S3 y hablar con el
     * SMTP puede tardar) y notificado_email solo pasa a true si el envío funcionó, para que el
     * historial no diga "notificado" cuando el correo nunca salió.
     */
    private function notificarRemitente(CorrespondenciaProceso $seguimiento, Estado $estado): bool
    {
        $correspondencia = Correspondencia::with('remitente:cod_ter,nom_ter,email')->find($seguimiento->id_correspondencia);
        $correo = trim((string) $correspondencia?->remitente?->email);

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        app()->terminating(function () use ($seguimiento, $correspondencia, $estado, $correo) {
            try {
                Mail::to($correo)->send(new CorrespondenciaRemitenteNotificacion(
                    $correspondencia->id_radicado,
                    $correspondencia->asunto,
                    trim($correspondencia->remitente->nom_ter),
                    $estado->nombre,
                    $seguimiento->observacion,
                    $seguimiento->documento_arc ?? [],
                ));

                $seguimiento->update(['notificado_email' => true]);
            } catch (\Throwable $e) {
                Log::error('Error notificando al remitente (radicado ' . $correspondencia->id_radicado . '): ' . $e->getMessage());
            }
        });

        return true;
    }

    public function show(CorrespondenciaProceso $correspondenciaProceso)
    {
        $correspondenciaProceso->load(['correspondencia', 'proceso.flujo', 'usuario']);
        return view('correspondencia.correspondencias_procesos.show', compact('correspondenciaProceso'));
    }

    public function edit(CorrespondenciaProceso $correspondenciaProceso)
    {
        $estados = Estado::all();
        $procesos_disponibles = Proceso::with('flujo')->get();
        return view('correspondencia.correspondencias_procesos.edit', compact('correspondenciaProceso', 'estados', 'procesos_disponibles'));
    }

    public function update(Request $request, CorrespondenciaProceso $correspondenciaProceso)
    {
        $validData = $request->validate([
            'observacion' => 'required|string',
            'estado' => 'required|string|max:255',
            'fecha_gestion' => 'required|date',
            'documento_arc' => 'nullable|array',
            'documento_arc.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'finalizado' => 'nullable|boolean',
        ]);

        try {
            $updateData = [
                'observacion' => $validData['observacion'],
                'estado' => $validData['estado'],
                'fecha_gestion' => $validData['fecha_gestion'],
                'notificado_email' => $request->boolean('notificado_email'),
                'finalizado' => $request->boolean('finalizado'),
            ];

            if ($request->hasFile('documento_arc')) {
                // Borrar archivos viejos de S3 asegurando que no intentemos borrar un "false"
                if (is_array($correspondenciaProceso->documento_arc)) {
                    foreach ($correspondenciaProceso->documento_arc as $oldFile) {
                        if ($oldFile && $oldFile !== 'false') {
                            Storage::disk('s3')->delete($oldFile);
                        }
                    }
                } elseif (is_string($correspondenciaProceso->documento_arc) && $correspondenciaProceso->documento_arc !== 'false') {
                    Storage::disk('s3')->delete($correspondenciaProceso->documento_arc);
                }

                $rutasNuevas = [];
                $proceso = $correspondenciaProceso->proceso;
                $nombresRequeridos = is_array($proceso->tipos_archivos) ? $proceso->tipos_archivos : json_decode($proceso->tipos_archivos, true) ?? [];

                foreach ($request->file('documento_arc') as $index => $file) {
                    // Determinamos el nombre base formateado igual que en el método store
                    $nombreBaseFormateado = isset($nombresRequeridos[$index]) ? Str::slug($nombresRequeridos[$index]) : 'doc-adjunto';

                    $nombreArchivo = 'upd_seg_' . $correspondenciaProceso->id_correspondencia . '_' . $nombreBaseFormateado . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Subida a S3 sin forzar permisos públicos
                    $path = $file->storeAs('corpentunida/correspondencia', $nombreArchivo, 's3');

                    // Validación contra fallos silenciosos
                    if ($path === false) {
                        throw new \Exception("Error al actualizar en S3. El archivo $nombreArchivo fue rechazado.");
                    }

                    $rutasNuevas[] = $path;
                }

                $updateData['documento_arc'] = $rutasNuevas;
            }

            $correspondenciaProceso->update($updateData);

            return redirect()->route('correspondencia.correspondencias.show', $correspondenciaProceso->id_correspondencia)->with('success', 'Gestión actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function getHistorialByRadicado($id_radicado)
    {
        $historial = CorrespondenciaProceso::with(['usuario', 'proceso'])
            ->where('id_correspondencia', $id_radicado)
            ->orderBy('fecha_gestion', 'desc')
            ->get();
        return response()->json($historial);
    }
}
