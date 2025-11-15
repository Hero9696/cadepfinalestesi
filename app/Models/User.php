<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Employee; // <-- ¡IMPORTACIÓN NECESARIA!

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'idcreate_user_user',
        'idupdater_user_user',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ==========================
    // RELACIONES
    // ==========================

    /**
     * Define la relación uno a uno (hasOne) con el modelo Employee.
     * Esto permite saber si el usuario ya está asignado como empleado.
     */
    public function employee()
    {
        // Clave foránea en la tabla 'employees' es 'id_user_employee',
        // que apunta a la clave primaria 'id' de la tabla 'users'.
        return $this->hasOne(Employee::class, 'id_user_employee', 'id');
    }

    // Relación con roles
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role_user', 'id_role');
    }

    // Relación con estados
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_user', 'id_state');
    }

    // Usuario que creó este registro
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_user', 'id');
    }

    // Usuario que actualizó este registro
    public function updater()
    {
        return $this->belongsTo(User::class, 'idupdater_user_user', 'id');
    }
}
