<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Soportes\ScpSoporte;
use App\Mail\SoporteEscaladoMail;
use Illuminate\Support\Facades\Mail;

class ScpNotificacionController extends Controller
{
    /**
     * Enviar correo al usuario escalado.
     */
    public function enviarCorreoEscalado($id)
    {
        $soporte = ScpSoporte::with(['usuarioEscalado', 'prioridad'])->findOrFail($id);

        // Validamos que tenga usuario escalado
        if (!$soporte->usuarioEscalado || !$soporte->usuarioEscalado->correo) {
            return response()->json(['error' => 'El soporte no tiene un usuario escalado con correo definido'], 400);
        }

        // Enviar el correo
        Mail::to($soporte->usuarioEscalado->correo)->send(new SoporteEscaladoMail($soporte));

        return response()->json(['success' => 'Correo enviado correctamente al usuario escalado.']);
    }
}
