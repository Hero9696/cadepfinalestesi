<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relative extends Model
{
    use HasFactory;

    protected $table = 'relatives';
    protected $primaryKey = 'id_relative';
    
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
     * RELACIÓN: Obtiene el paciente principal.
     * Esta es la función que sigue la línea de la imagen.
     */
    public function principalPatient()
    {
        return $this->belongsTo(Patient::class, 'idprincipal_patient_relative', 'id_patient');
    }

    /**
     * RELACIÓN: Obtiene el paciente secundario (relacionado).
     * Esta es la función que sigue la segunda línea de la imagen.
     */
    public function secondaryPatient()
    {
        return $this->belongsTo(Patient::class, 'idsecondary_patient_relative', 'id_patient');
    }
}