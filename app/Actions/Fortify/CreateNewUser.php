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
                'nid' => $input['nid'],
                'password' => Hash::make($input['password']),
            ]);
        }

    }

}
