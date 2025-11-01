// app/Models/Branch.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';
    protected $primaryKey = 'id_branch';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_branch';
    const UPDATED_AT = 'updatedate_branch';

    protected $fillable = [
        'name_branch',
        'phone_branch',
        'id_municipality_branch',
        'id_department_branch',
        'id_state_branch',
        'idcreate_user_branch',
        'idupdater_user_branch',
    ];

    /**
     * Relación: La sucursal pertenece a un municipio.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'id_municipality_branch', 'id_municipality');
    }

    /**
     * Relación: La sucursal pertenece a un departamento.
     */
    public function department()
    {
        // Nota: Aunque ya se relaciona con municipio, tu esquema tiene ambas FK, así que mantenemos la relación directa.
        return $this->belongsTo(Department::class, 'id_department_branch', 'id_department');
    }

    /**
     * Relación: La sucursal tiene un estado.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_branch', 'id_state');
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_branch', 'id_user');
    }

    /**
     * Relación: Usuario actualizador del registro.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'idupdater_user_branch', 'id_user');
    }
}
