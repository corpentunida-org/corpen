<?php

namespace App\Actions\Fortify;

use App\Models\Action;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
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
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        if($input['aux'] == 'asociado'){
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
                'password' => Hash::make($input['password']),
            ]);
        }

    }

    public function consultarAsociado( $nid )
    {
        $url = "https://www.siasoftapp.com:7006/api/Pastors"; // URL del endpoint
        $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6ImFkbWluQGdtYWlsLmNvbSIsImp0aSI6IjNjNTZjOTU1LTZiNDktNDhlZi04NjVjLWQ1MzViOTNkMjllMCIsImh0dHA6Ly9zY2hlbWFzLnhtbHNvYXAub3JnL3dzLzIwMDUvMDUvaWRlbnRpdHkvY2xhaW1zL25hbWUiOiJBZG1pbiIsIlVzZXJJZCI6IjEiLCJtYWlsIjoiYWRtaW5AZ21haWwuY29tIiwiVXNlcnJvbGUiOiJBZG1pbiIsImh0dHA6Ly9zY2hlbWFzLm1pY3Jvc29mdC5jb20vd3MvMjAwOC8wNi9pZGVudGl0eS9jbGFpbXMvcm9sZSI6IkFkbWluIiwiZXhwIjoxNzQ2MTkzNzk1LCJpc3MiOiJteWFwcCIsImF1ZCI6Im15YXBwIn0.Cs5UU7RLmFQWsJg444rZTL2QtpRXS4cZEI_8jtzbUSw";

        // Realizar la solicitud GET
        $response = Http::withToken($token)
            ->get($url, ['DocumentId' => 72348832]); // Agregar parámetros a la URL

        // Verificar si la llamada fue exitosa
        if ($response->successful()) {
            // Obtener JSON de respuesta
            $json = $response->json();
            return response()->json([
                'status' => 'success',
                'data' => $json, // JSON de respuesta
            ], 200);
        } else {
            // Manejar el error
            return response()->json([
                'status' => 'error',
                'message' => 'Error al consumir el endpoint',
                'error' => $response->body(), // Mostrar el error recibido
                'code' => $response->status(),
            ], $response->status());
        }
    }

}
