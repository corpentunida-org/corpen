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

class CorrespondenciaProcesoController extends Controller
{
    /**
     * Listado con Filtros y Búsqueda
     */
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

    /**
     * Formulario de creación
     */
    public function create()
    {
        $correspondencias = Correspondencia::select('id_radicado', 'asunto')->get();
        $procesos_disponibles = Proceso::with('flujo')->get();
        $estados = Estado::all();

        return view('correspondencia.correspondencias_procesos.create', compact('correspondencias', 'procesos_disponibles', 'estados'));
    }

    /**
     * Almacenamiento con Transacción y Sincronización de Estado
     */
    public function store(Request $request)
    {
        // Validación basada en los tipos de tu tabla (BigInt, Varchar, DateTime)
        $validData = $request->validate([
            'id_correspondencia' => 'required|integer|exists:corr_correspondencia,id_radicado',
            'id_proceso' => 'required|integer|exists:corr_procesos,id',
            'observacion' => 'required|string',
            'estado' => 'required|string|max:255',
            'fecha_gestion' => 'required|date',
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        try {
            $idRadicado = $validData['id_correspondencia'];
            DB::transaction(function () use ($request, $validData, $idRadicado) {                
                if ($request->hasFile('documento_arc')) {
                    $file = $request->file('documento_arc');
                    $nombre = 'corpentunida/correspondencia/seg_' . $idRadicado . '_' . time() . '.' . $file->getClientOriginalExtension();
                    Storage::disk('s3')->put($nombre, file_get_contents($file));
                }                

                CorrespondenciaProceso::create([
                    'id_correspondencia' => $idRadicado,
                    'observacion' => $validData['observacion'],
                    'estado' => $validData['estado'],
                    'id_proceso' => $validData['id_proceso'],
                    'notificado_email' => $request->boolean('notificado_email'), // Laravel castea a 1 o 0
                    'fecha_gestion' => $validData['fecha_gestion'],
                    'documento_arc' => $nombre,
                    'fk_usuario' => auth()->id(),
                ]);
                
                $nombreEstado = str_replace('_', ' ', $validData['estado']);
                $estadoMaestro = Estado::where('nombre', 'LIKE', '%' . $nombreEstado . '%')->first();

                if ($estadoMaestro) {
                    Correspondencia::where('id_radicado', $idRadicado)->update(['estado_id' => $estadoMaestro->id]);
                }
            });

            return redirect()->route('correspondencia.correspondencias.show', $idRadicado)->with('success', 'Seguimiento registrado y estado actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error en la operación: ' . $e->getMessage());
        }
    }

    /**
     * Ver detalle
     */
    public function show(CorrespondenciaProceso $correspondenciaProceso)
    {
        $correspondenciaProceso->load(['correspondencia', 'proceso.flujo', 'usuario']);
        return view('correspondencia.correspondencias_procesos.show', compact('correspondenciaProceso'));
    }

    /**
     * Formulario de edición
     */
    public function edit(CorrespondenciaProceso $correspondenciaProceso)
    {
        $estados = Estado::all();
        $procesos_disponibles = Proceso::with('flujo')->get();
        return view('correspondencia.correspondencias_procesos.edit', compact('correspondenciaProceso', 'estados', 'procesos_disponibles'));
    }

    /**
     * Actualización de seguimiento
     */
    public function update(Request $request, CorrespondenciaProceso $correspondenciaProceso)
    {
        $validData = $request->validate([
            'observacion' => 'required|string',
            'estado' => 'required|string|max:255',
            'fecha_gestion' => 'required|date',
            'documento_arc' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        try {
            $updateData = [
                'observacion' => $validData['observacion'],
                'estado' => $validData['estado'],
                'fecha_gestion' => $validData['fecha_gestion'],
                'notificado_email' => $request->boolean('notificado_email'),
            ];

            if ($request->hasFile('documento_arc')) {
                // Eliminar archivo anterior si existe
                if ($correspondenciaProceso->documento_arc) {
                    Storage::disk('s3')->delete($correspondenciaProceso->documento_arc);
                }

                $file = $request->file('documento_arc');                
                $nombre = 'corpentunida/correspondencia/upd_seg_' . $idRadicado . '_' . time() . '.' . $file->getClientOriginalExtension();
                Storage::disk('s3')->put($nombre, file_get_contents($file))
                $updateData['documento_arc'] = $nombre;
            }

            $correspondenciaProceso->update($updateData);

            return redirect()->route('correspondencia.correspondencias.show', $correspondenciaProceso->id_correspondencia)->with('success', 'Gestión actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminación de registro y archivo
     */
    public function destroy(CorrespondenciaProceso $correspondenciaProceso)
    {
        $id_radicado = $correspondenciaProceso->id_correspondencia;

        try {
            if ($correspondenciaProceso->documento_arc) {
                Storage::disk('s3')->delete($correspondenciaProceso->documento_arc);
            }
            $correspondenciaProceso->delete();

            return redirect()->route('correspondencia.correspondencias.show', $id_radicado)->with('success', 'Registro eliminado del historial.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }

    /**
     * API/AJAX: Obtener historial por Radicado
     */
    public function getHistorialByRadicado($id_radicado)
    {
        $historial = CorrespondenciaProceso::with(['usuario', 'proceso'])
            ->where('id_correspondencia', $id_radicado)
            ->orderBy('fecha_gestion', 'desc')
            ->get();

        return response()->json($historial);
    }
}
