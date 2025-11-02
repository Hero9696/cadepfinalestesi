<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return \App\Models\User
     */
    public function create(array $input): User
    {
        // 1. VALIDACIONES AGREGADAS para campos personalizados
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            
            // CAMPOS REQUERIDOS POR LA BASE DE DATOS
            'id_role_user' => ['required', 'integer', 'exists:roles,id_role'],
            'id_state_user' => ['required', 'integer', 'exists:states,id_state'],
            
        ])->validate();

        // 2. CREACIÓN DEL USUARIO con asignación de IDs de auditoría
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            
            // CAMPOS DEL FORMULARIO
            'id_role_user' => $input['id_role_user'],
            'id_state_user' => $input['id_state_user'],

            // CAMPOS DE AUDITORÍA (NECESARIOS EN EL REGISTRO INICIAL)
            // Asignamos el ID 1 temporalmente. Esto asume que la base de datos
            // tiene un usuario con ID=1 (Super Admin) o permite NULL para el primer registro.
            // Si tu columna idcreate_user_user no acepta NULL, este valor es necesario.
            'idcreate_user_user' => 1, 
            'idupdater_user_user' => 1,
        ]);
    }
}