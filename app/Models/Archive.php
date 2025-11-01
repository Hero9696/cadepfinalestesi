<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $table = 'archives';
    protected $primaryKey = 'id_archive';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_archive';
    const UPDATED_AT = 'updatedate_archive';

    protected $fillable = [
        'id_patient_archive',
        'idcreate_user_archive',
        'idupdater_user_archive',
    ];

    /**
     * Relación: El archivo pertenece a un paciente.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient_archive', 'id_patient');
    }

    /**
     * Relación: Un archivo puede tener muchos reportes de citas.
     */
    public function reports()
    {
        return $this->hasMany(AppointmentReport::class, 'id_archive_appointmentreport', 'id_archive');
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_archive', 'id_user');
    }
}
