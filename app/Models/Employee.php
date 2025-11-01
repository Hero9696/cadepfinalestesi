<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'id_employee';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_employee';
    const UPDATED_AT = 'updatedate_employee';

    protected $fillable = [
        'cui_employee',
        'firstname_employee',
        'middlename_employee',
        'thirdname_employee',
        'lastname_employee',
        'secondlastname_employee',
        'thirdlastname_employee',
        'profession_employee',
        'phone_employee',
        'id_user_employee',
        'birthdate_employee',
        'idbirth_department_employee',
        'idbirth_municipality_employee',
        'age_employee',
        'id_department_employee',
        'id_municipality_employee',
        'address_employee',
        'gender_employee',
        'maritalstatus_employee',
        'id_area_employee',
        'id_branch_employee',
        'id_state_employee',
        'idcreate_user_employee',
        'idupdater_user_employee',
    ];

    protected $casts = [
        'birthdate_employee' => 'date',
    ];

    /**
     * Relación: Un empleado está asociado a una cuenta de usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_employee', 'id_user');
    }

    /**
     * Relación: Departamento de nacimiento.
     */
    public function birthDepartment()
    {
        return $this->belongsTo(Department::class, 'idbirth_department_employee', 'id_department');
    }

    /**
     * Relación: Municipio de nacimiento.
     */
    public function birthMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'idbirth_municipality_employee', 'id_municipality');
    }

    /**
     * Relación: Departamento de residencia.
     */
    public function locationDepartment()
    {
        return $this->belongsTo(Department::class, 'id_department_employee', 'id_department');
    }

    /**
     * Relación: Municipio de residencia.
     */
    public function locationMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'id_municipality_employee', 'id_municipality');
    }

    /**
     * Relación: Área de especialización del empleado.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area_employee', 'id_area');
    }

    /**
     * Relación: Sucursal a la que pertenece el empleado.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_employee', 'id_branch');
    }

    /**
     * Relación: Estado del empleado.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_employee', 'id_state');
    }
}
