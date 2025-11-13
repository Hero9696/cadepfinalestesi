<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_role';
    // Sobreescribir las columnas de timestamps para usar las de la base de datos
    const CREATED_AT = 'createdate_role';
    const UPDATED_AT = 'updatedate_role';

    protected $fillable = [
        'name_role',
        'description_role',
        'id_state_role',
        'idcreate_user_role',
        'idupdater_user_role',
    ];

    /**
     * Relación: Un rol tiene un estado.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_role', 'id_state');
    }

    /**
     * Relación: Un rol puede tener muchos usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_role_user', 'id_role');
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        // Se asume que el usuario creador existe en la tabla 'users'
        return $this->belongsTo(User::class, 'idcreate_user_role', 'id');
    }

    /**
     * Relación: Usuario actualizador del registro.
     */
    public function updater()
    {
        // Se asume que el usuario actualizador existe en la tabla 'users'
        return $this->belongsTo(User::class, 'idupdater_user_role', 'id');
    }
}
