// app/Models/State.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states';
    protected $primaryKey = 'id_state';
    // La clave primaria es AUTO_INCREMENT, por lo que las configuraciones por defecto de Laravel son correctas
    // protected $keyType = 'bigint'; // Laravel ya lo infiere
    public $timestamps = false; // No tiene created_at ni updated_at

    protected $fillable = [
        'name_state',
    ];

    /**
     * Relación: Un estado puede estar asociado a muchos roles.
     */
    public function roles()
    {
        return $this->hasMany(Role::class, 'id_state_role', 'id_state');
    }

    /**
     * Relación: Un estado puede estar asociado a muchos usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_state_user', 'id_state');
    }

    /**
     * Relación: Un estado puede estar asociado a muchas sucursales.
     */
    public function branches()
    {
        return $this->hasMany(Branch::class, 'id_state_branch', 'id_state');
    }
}
