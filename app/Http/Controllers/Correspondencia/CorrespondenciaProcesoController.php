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
     * Almacenamiento con Validación Dinámica de Arrays de Archivos
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'id_correspondencia' => 'required|exists:corr_correspondencia,id_radicado',
            'id_proceso'         => 'required|integer|exists:corr_procesos,id',
            'observacion'        => 'required|string',
            'estado_id'          => 'required|integer|exists:corr_estados,id',
            'fecha_gestion'      => 'required|date',
            'finalizado'         => 'nullable|boolean',
        ]);

        $proceso = Proceso::findOrFail($request->id_proceso);
        $numRequeridos = (int) $proceso->numero_archivos;

        if ($numRequeridos > 0) {
            $request->validate([
                'documento_arc'   => 'required|array|size:' . $numRequeridos,
                'documento_arc.*' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ], [
                'documento_arc.required' => "Este proceso exige subir $numRequeridos archivo(s) obligatorio(s).",
                'documento_arc.size'     => "Debe subir exactamente $numRequeridos archivo(s) como lo exige el proceso.",
                'documento_arc.*.mimes'  => 'Solo se permiten archivos: PDF, DOC, DOCX, JPG o PNG.',
            ]);
        } else {
            $request->validate([
                'documento_arc'   => 'nullable|array',
                'documento_arc.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
            ]);
        }

        try {
            $idRadicado = $request->id_correspondencia;
            
            DB::transaction(function () use ($request, $idRadicado, $proceso) { 
                $estadoMaestro = Estado::findOrFail($request->estado_id);
                $rutasArchivos = [];              
                
                if ($request->hasFile('documento_arc')) {
                    foreach ($request->file('documento_arc') as $file) {
                        // 1. Generamos solo el nombre del archivo
                        $nombreArchivo = 'seg_' . $idRadicado . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        
                        // 2. Usamos storeAs con 'visibility' => 'public' para que Amazon S3 permita abrir el enlace
                        $path = $file->storeAs('corpentunida/correspondencia', $nombreArchivo, [
                            'disk' => 's3',
                            'visibility' => 'public'
                        ]);
                        
                        $rutasArchivos[] = $path;
                    }
                }    

                CorrespondenciaProceso::create([
                    'id_correspondencia' => $idRadicado,
                    'observacion'        => $request->observacion,
                    'estado'             => $estadoMaestro->id,
                    'id_proceso'         => $proceso->id,
                    'notificado_email'   => $request->boolean('notificado_email'),
                    'fecha_gestion'      => $request->fecha_gestion,
                    'documento_arc'      => $rutasArchivos, 
                    'finalizado'         => $request->boolean('finalizado'),
                    'fk_usuario'         => auth()->id(),
                ]);
                
                Correspondencia::where('id_radicado', $idRadicado)->update([
                    'estado_id' => $estadoMaestro->id
                ]);
            });

            return redirect()->route('correspondencia.correspondencias.show', $idRadicado)
                ->with('success', 'Seguimiento registrado y archivos guardados correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error en la operación: ' . $e->getMessage());
        }
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
            'observacion'     => 'required|string',
            'estado'          => 'required|string|max:255',
            'fecha_gestion'   => 'required|date',
            'documento_arc'   => 'nullable|array',
            'documento_arc.*' => 'file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'finalizado'      => 'nullable|boolean',
        ]);

        try {
            $updateData = [
                'observacion'      => $validData['observacion'],
                'estado'           => $validData['estado'],
                'fecha_gestion'    => $validData['fecha_gestion'],
                'notificado_email' => $request->boolean('notificado_email'),
                'finalizado'       => $request->boolean('finalizado'),
            ];

            if ($request->hasFile('documento_arc')) {
                
                // Borrar archivos viejos de S3
                if (is_array($correspondenciaProceso->documento_arc)) {
                    foreach ($correspondenciaProceso->documento_arc as $oldFile) {
                        Storage::disk('s3')->delete($oldFile);
                    }
                } elseif (is_string($correspondenciaProceso->documento_arc)) {
                    Storage::disk('s3')->delete($correspondenciaProceso->documento_arc);
                }

                $rutasNuevas = [];
                foreach ($request->file('documento_arc') as $file) {
                    $nombreArchivo = 'upd_seg_' . $correspondenciaProceso->id_correspondencia . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    // Igual que en el store, lo guardamos con permisos públicos
                    $path = $file->storeAs('corpentunida/correspondencia', $nombreArchivo, [
                        'disk' => 's3',
                        'visibility' => 'public'
                    ]);
                    
                    $rutasNuevas[] = $path;
                }
                
                $updateData['documento_arc'] = $rutasNuevas;
            }

            $correspondenciaProceso->update($updateData);

            return redirect()->route('correspondencia.correspondencias.show', $correspondenciaProceso->id_correspondencia)->with('success', 'Gestión actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
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