<?php

namespace App\Http\Controllers\Soportes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Soportes\ScpSoporte;
use App\Mail\SoporteEscaladoMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ScpNotificacionController extends Controller
{
    /**
     * Enviar (o reenviar) el correo al usuario escalado.
     */
    public function enviarCorreoEscalado($id)
    {
        // 'usuario_escalado' guarda un id de scp_usuarios, no de users directamente — scpUsuarioAsignado
        // es la relación correcta; de ahí, UserApp es el usuario real del sistema (con email). Antes
        // usaba una relación (usuarioEscalado) que apuntaba mal a la tabla users con ese mismo id, y
        // además leía '->correo' cuando el campo real en users es 'email' — este endpoint nunca pudo
        // funcionar.
        $soporte = ScpSoporte::with(['scpUsuarioAsignado.UserApp', 'prioridad'])->findOrFail($id);

        $user = Auth::user();
        $puedeGestionar = $soporte->id_users === $user->id
            || optional($soporte->scpUsuarioAsignado)->usuario === $user->id
            || $user->hasDirectPermission('soporte.lista.agente')
            || $user->hasDirectPermission('soporte.lista.todo')
            || $user->hasDirectPermission('soporte.lista.administrador');
        abort_unless($puedeGestionar, 404);

        $agenteEscalado = $soporte->scpUsuarioAsignado;
        if (!$agenteEscalado || !$agenteEscalado->UserApp || empty($agenteEscalado->UserApp->email)) {
            return response()->json(['error' => 'El soporte no tiene un usuario escalado con correo definido'], 400);
        }

        Mail::to($agenteEscalado->UserApp->email)->send(new SoporteEscaladoMail($soporte, 'escalado'));

        return response()->json(['success' => 'Correo enviado correctamente al usuario escalado.']);
    }
}
