<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

// Se recomienda usar Pivot para las tablas intermedias si se necesita manejar campos extra
class PatientArea extends Pivot
{
    use HasFactory;

    protected $table = 'patientareas';
    protected $primaryKey = 'id_patientarea';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_patientarea';
    const UPDATED_AT = 'updatedate_patientarea';

    protected $fillable = [
        'id_patient_patientarea',
        'id_area_patientarea',
        'idcreate_user_patientarea',
        'idupdater_user_patientarea',
    ];

    /**
     * Relación: La asignación pertenece a un paciente.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient_patientarea', 'id_patient');
    }

    /**
     * Relación: La asignación pertenece a un área.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area_patientarea', 'id_area');
    }
}
