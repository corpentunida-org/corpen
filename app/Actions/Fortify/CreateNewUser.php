<?php

namespace App\Actions\Fortify;

use App\Models\Action;
use App\Models\User;
use App\Services\Integraciones\PastorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Antes solo el email era único: cualquiera podía registrarse varias veces con la
            // misma cédula usando un correo distinto cada vez (se confirmaron 73 cédulas con
            // hasta 4 cuentas). 'nid' es la cédula real de la persona — no debería poder haber
            // dos cuentas con la misma, sin importar el tipo (asociado o no).
            'nid' => ['required', 'string', 'max:20', 'unique:users,nid'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'nid.unique' => 'Ya existe una cuenta registrada con esta cédula. Si no recuerdas tu acceso, usa la opción de recuperar contraseña en vez de crear una cuenta nueva.',
        ])->validate();

        if($input['aux'] == 'asociado'){
            // Antes esta verificación (cédula + fecha de nacimiento contra SiaSoft) solo
            // ocurría en la pantalla previa (UserController::validarAsociado), que únicamente
            // redirige a este formulario — nunca deja un rastro que el registro real pueda
            // confirmar. Un POST directo a esta acción (saltándose esa pantalla) podía crear un
            // "asociado" con cualquier cédula, real o inventada, sin verificarla nunca. Se repite
            // aquí la misma verificación como defensa en profundidad, no como reemplazo de la
            // pantalla previa (que sigue dando el error temprano, antes de llenar todo el
            // formulario).
            try {
                $pastor = app(PastorService::class)->obtenerPastor($input['nid']);
            } catch (\Throwable $e) {
                // SiaSoft devuelve el mismo tipo de error tanto para "esta cédula no existe"
                // como para una falla real del servicio, así que no se puede distinguir con
                // certeza cuál de las dos pasó — el mensaje cubre ambas posibilidades.
                throw ValidationException::withMessages([
                    'nid' => 'No pudimos verificar tu cédula. Revisa que esté bien escrita; si el problema persiste, intenta de nuevo en unos minutos.',
                ]);
            }

            $fechaApi = $pastor->birthdate !== '' ? Carbon::parse($pastor->birthdate)->format('Y-m-d') : null;
            if ($fechaApi === null || $fechaApi !== $input['fecha']) {
                throw ValidationException::withMessages([
                    'nid' => 'Los datos proporcionados no coinciden con nuestros registros. Por favor, comunícate con soporte técnico.',
                ]);
            }

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'nid' => $input['nid'],
                'fecha_nacimiento' => $input['fecha'],
                'type' => 'ASOCIADO',
            ]);

            $action = New Action();
            $action->user_id = $user->id;
            $action->user_email = $user->email;
            $action->role_id = '13';
            $action->save();

            return $user;
        } else {
            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'nid' => $input['nid'],
                'password' => Hash::make($input['password']),
            ]);
        }

    }

}
