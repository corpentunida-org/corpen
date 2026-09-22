<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Pantalla obligatoria cuando un admin marcó "forzar cambio de contraseña en el próximo inicio
 * de sesión" desde Gestión de Usuarios (ver App\Http\Middleware\ForzarCambioPassword, que
 * redirige aquí a cualquier usuario con esa marca activa, sea empleado o asociado).
 *
 * No pide la contraseña actual: el usuario ya la usó para autenticarse hace un momento, esa
 * es la prueba de que la conoce — pedirla otra vez aquí sería redundante.
 */
class ForzarCambioPasswordController extends Controller
{
    use PasswordValidationRules;

    public function edit()
    {
        return view('auth.forzar-cambio-password');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'password' => $this->passwordRules(),
        ]);

        $user = Auth::user();
        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'debe_cambiar_password' => false,
        ])->save();

        return redirect()->intended('/dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}
