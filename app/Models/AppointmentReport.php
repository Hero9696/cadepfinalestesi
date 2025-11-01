<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentReport extends Model
{
    use HasFactory;

    protected $table = 'appointmentreports';
    protected $primaryKey = 'id_appointmentreport';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_appointmentreport';
    const UPDATED_AT = 'updatedate_appointmentreport';

    protected $fillable = [
        'id_archive_appointmentreport',
        'id_appointment_appointmentreport',
        'objective_appointmentreport',
        'observations_appointmentreport',
        'height_appointmentreport',
        'weight_appointmentreport',
        'idcreate_user_appointmentreport',
        'idupdater_user_appointmentreport',
    ];

    protected $casts = [
        'height_appointmentreport' => 'float',
        'weight_appointmentreport' => 'float',
    ];

    /**
     * Relación: El reporte pertenece a un archivo/expediente.
     */
    public function archive()
    {
        return $this->belongsTo(Archive::class, 'id_archive_appointmentreport', 'id_archive');
    }

    /**
     * Relación: El reporte está ligado a una cita.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'id_appointment_appointmentreport', 'id_appointment');
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_appointmentreport', 'id_user');
    }
}
