<?php

namespace App\Http\Controllers\Interacciones;

use App\Http\Controllers\Controller;
use App\Models\Interacciones\IntSeguimiento;
use App\Models\Interacciones\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IntSeguimientoController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validación de datos
        $request->validate([
            'id_interaction' => 'required|exists:interactions,id',
            'outcome' => 'required',
            'next_action_notes' => 'required|string',
            'id_user_asignacion' => 'required|exists:users,id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
            'next_action_date' => 'nullable|date',
        ]);

        try {
            $data = $request->all();
            
            // 2. Manejo del archivo (Soporte)
            if ($request->hasFile('attachment')) {
                // Guardamos en el disco configurado (ej: s3 o local)
                $path = $request->file('attachment')->store('seguimientos/adjuntos', 's3');
                $data['attachment_urls'] = $path;
            }

            // 3. Crear el seguimiento
            $seguimiento = IntSeguimiento::create([
                'id_interaction'     => $request->id_interaction,
                'agent_id'           => auth()->id(),
                'id_user_asignacion' => $request->id_user_asignacion,
                'outcome'            => $request->outcome,
                'next_action_type'   => $request->next_action_type,
                'next_action_date'   => $request->next_action_date,
                'next_action_notes'  => $request->next_action_notes,
                'attachment_urls'    => $data['attachment_urls'] ?? null,
                'interaction_url'    => $request->interaction_url,
            ]);

            /* * OPCIONAL: Actualizar el estado de la interacción padre 
             * para que refleje el último resultado.
             */
            $parent = Interaction::find($request->id_interaction);
            $parent->update([
                'outcome' => $request->outcome,
                'id_user_asignacion' => $request->id_user_asignacion
            ]);

            return redirect()->back()->with('success', '¡Seguimiento registrado correctamente!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}