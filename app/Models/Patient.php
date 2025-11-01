// app/Models/Patient.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'id_patient';
    public $incrementing = false; // El ID se proporciona o gestiona externamente
    protected $keyType = 'int';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_patient';
    const UPDATED_AT = 'updatedate_patient';

    protected $fillable = [
        'id_patient',
        'cui_patient',
        'firstname_patient',
        'middlename_patient',
        'thirdname_patient',
        'lastname_patient',
        'secondlastname_patient',
        'thirdlastname_patient',
        'birthdate_patient',
        'idbirth_department_patient',
        'idbirth_municipality_patient',
        'age_patient',
        'weight_patient',
        'schooling_patient',
        'phone_patient',
        'id_department_patient',
        'id_municipality_patient',
        'address_patient',
        'gender_patient',
        'religion_patient',
        'maritalstatus_patient',
        'dependentfamily_patient',
        'reasonforconsultation_patient',
        'referreddoctor_patient',
        'photo_url_patient',
        'status_patient',
        'reserved_until_patient',
        'idcreate_user_patient',
        'id_branch_patient',
        'id_state_patient',
        'idupdater_user_patient',
    ];

    protected $casts = [
        'birthdate_patient' => 'date',
        'dependentfamily_patient' => 'boolean',
        'reserved_until_patient' => 'datetime',
        'weight_patient' => 'float',
    ];

    /**
     * Relación: Departamento de nacimiento.
     */
    public function birthDepartment()
    {
        return $this->belongsTo(Department::class, 'idbirth_department_patient', 'id_department');
    }

    /**
     * Relación: Municipio de nacimiento.
     */
    public function birthMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'idbirth_municipality_patient', 'id_municipality');
    }

    /**
     * Relación: Departamento de residencia.
     */
    public function locationDepartment()
    {
        return $this->belongsTo(Department::class, 'id_department_patient', 'id_department');
    }

    /**
     * Relación: Municipio de residencia.
     */
    public function locationMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'id_municipality_patient', 'id_municipality');
    }

    /**
     * Relación: Sucursal a la que pertenece el paciente.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch_patient', 'id_branch');
    }

    /**
     * Relación: Estado del paciente.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_patient', 'id_state');
    }

    /**
     * Relación N:M: Áreas asignadas al paciente.
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'patientareas', 'id_patient_patientarea', 'id_area_patientarea');
    }

    /**
     * Relación: El paciente es el principal en las relaciones de parentesco.
     */
    public function principalRelatives()
    {
        return $this->hasMany(Relative::class, 'idprincipal_patient_relative', 'id_patient');
    }

    /**
     * Relación: El paciente es el secundario en las relaciones de parentesco.
     */
    public function secondaryRelatives()
    {
        return $this->hasMany(Relative::class, 'idsecondary_patient_relative', 'id_patient');
    }
}
