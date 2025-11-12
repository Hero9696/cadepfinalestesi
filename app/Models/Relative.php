<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relative extends Model
{
    use HasFactory;

    protected $table = 'relatives';
    protected $primaryKey = 'id_relative';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_relative';
    const UPDATED_AT = 'updatedate_relative';

    protected $fillable = [
        'idprincipal_patient_relative',
        'idsecondary_patient_relative',
        'relationship_relative',
        'idcreate_user_relative',
        'idupdater_user_relative',
    ];

    /**
     * Relación: Paciente principal de la relación.
     */
    public function principalPatient()
    {
        return $this->belongsTo(Patient::class, 'idprincipal_patient_relative', 'id_patient');
    }

    /**
     * Relación: Paciente secundario (el pariente) de la relación.
     */
    public function secondaryPatient()
    {
        return $this->belongsTo(Patient::class, 'idsecondary_patient_relative', 'id_patient');
    }
}
