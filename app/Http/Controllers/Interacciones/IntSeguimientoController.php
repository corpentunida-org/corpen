<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interacciones\Concerns\GuardaSoporteInteraccion;
use App\Models\Interacciones\IntOutcome;
use App\Models\Interacciones\IntSeguimiento;
use App\Models\Interacciones\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class IntSeguimientoController extends Controller
{
    use GuardaSoporteInteraccion;

    public function store(Request $request)
    {
        // 1. Validación de datos
        $request->validate([
            'id_interaction' => 'required|exists:interactions,id',
            'outcome' => 'required',
            // Solo obligatorio cuando el resultado elegido es específicamente "No Efectivo" (no
            // alcanza con estado=0: "Reasignado a otro Operador" también lo es y no necesita
            // motivo). Se compara por nombre y no por ID fijo, por si el catálogo cambia.
            'motivo_no_efectivo_id' => [
                Rule::requiredIf(fn () => strtolower(trim(optional(IntOutcome::find($request->outcome))->name ?? '')) === 'no efectivo'),
                'nullable',
                'exists:int_motivos_no_efectivo,id',
            ],
            'next_action_notes' => 'required|string',
            'id_user_asignacion' => 'required|exists:users,id',
            'attachment' => $this->reglaValidacionSoporte(),
            'next_action_date' => 'nullable|date',
        ]);

        // 2. Subir el soporte ANTES de la transacción: si S3 falla, no hay nada que revertir en
        // la base de datos. Si en cambio la transacción de abajo falla DESPUÉS de subir el
        // archivo, sí quedaría huérfano en S3 — por eso el catch lo borra explícitamente.
        $rutaArchivo = null;
        $tamanoArchivo = null;
        if ($request->hasFile('attachment')) {
            $tamanoArchivo = $request->file('attachment')->getSize();
            $rutaArchivo = $this->guardarSoporte($request->file('attachment'), (int) $request->id_interaction);
        }

        try {
            DB::transaction(function () use ($request, $rutaArchivo, $tamanoArchivo) {
                IntSeguimiento::create([
                    'id_interaction'     => $request->id_interaction,
                    'agent_id'           => auth()->id(),
                    'id_user_asignacion' => $request->id_user_asignacion,
                    'outcome'            => $request->outcome,
                    'motivo_no_efectivo_id' => $request->motivo_no_efectivo_id,
                    'next_action_type'   => $request->next_action_type,
                    'next_action_date'   => $request->next_action_date,
                    'next_action_notes'  => $request->next_action_notes,
                    'attachment_urls'    => $rutaArchivo,
                    'attachment_size'    => $tamanoArchivo,
                    'interaction_url'    => $request->interaction_url,
                ]);

                // Refleja en la interacción el último resultado registrado.
                Interaction::whereKey($request->id_interaction)->update([
                    'outcome' => $request->outcome,
                    'motivo_no_efectivo_id' => $request->motivo_no_efectivo_id,
                    'id_user_asignacion' => $request->id_user_asignacion,
                ]);
            });

            return redirect()->back()->with('success', '¡Seguimiento registrado correctamente!');
        } catch (\Exception $e) {
            if ($rutaArchivo) {
                Storage::disk('s3')->delete($rutaArchivo);
            }
            Log::error('Error al registrar seguimiento (interacción '.$request->id_interaction.'): '.$e->getMessage());

            return redirect()->back()->with('error', 'Error al guardar: '.$e->getMessage());
        }
    }
}
