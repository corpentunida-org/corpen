<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * "Ver como": permite a soporte reproducir exactamente lo que ve un asociado (para diagnosticar
 * tickets sin adivinar) sin conocer ni cambiar su contraseña. Se restringe a solo ASOCIADOS a
 * propósito — nunca a otro empleado ni admin, para no abrir una vía de escalar privilegios entre
 * cuentas de staff.
 */
class ImpersonarController extends Controller
{
    private function auditoria($accion)
    {
        app(AuditoriaController::class)->create($accion, 'ADMINISTRACIÓN');
    }

    public function iniciar(User $user)
    {
        if ($user->type !== 'ASOCIADO') {
            return back()->with('error', 'Solo se puede "ver como" un asociado, no un empleado.');
        }

        if (session()->has('impersonador_id')) {
            return back()->with('error', 'Ya estás viendo como otro usuario. Vuelve a tu cuenta primero.');
        }

        $admin = auth()->user();

        // Se guarda ANTES de Auth::login() — a partir de esa llamada auth()->user() ya es el
        // asociado, no el admin.
        session([
            'impersonador_id' => $admin->id,
            'impersonador_nombre' => $admin->name,
        ]);

        $this->auditoria("Inició \"ver como\" el asociado {$user->name} #{$user->id} ({$user->email}) para soporte");

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Ahora estás viendo la cuenta de {$user->name}.");
    }

    public function detener()
    {
        $impersonadorId = session('impersonador_id');
        if (!$impersonadorId) {
            return redirect()->route('dashboard');
        }

        $asociado = auth()->user();
        $admin = User::find($impersonadorId);

        session()->forget(['impersonador_id', 'impersonador_nombre']);

        if (!$admin) {
            // El admin original ya no existe (eliminado) — no hay a quién volver; se deja la
            // sesión actual (la del asociado) y se manda al login para evitar quedar en un
            // estado sin dueño real.
            Auth::logout();
            return redirect()->route('login')->with('error', 'No se pudo restaurar tu sesión original.');
        }

        Auth::login($admin);
        $this->auditoria("Terminó \"ver como\" el asociado {$asociado->name} #{$asociado->id}");

        return redirect()->route('admin.users.edit', $asociado->id)->with('success', 'Volviste a tu cuenta.');
    }
}
