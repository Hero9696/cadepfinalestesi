// app/Models/StructureAppointment.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StructureAppointment extends Model
{
    use HasFactory;

    protected $table = 'structureappointments';
    protected $primaryKey = 'id_structureappointment';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_structureappointment';
    const UPDATED_AT = 'updatedate_structureappointment';

    protected $fillable = [
        'id_appointment_structureappointment',
        'id_user_structureappointment',
        'id_patient_structureappointment',
        'id_schedule_structureappointment',
        'id_week_structureappointment',
        'active_structureappointment',
        'idcreate_user_structureappointment',
        'idupdater_user_structureappointment',
    ];

    protected $casts = [
        'active_structureappointment' => 'boolean',
    ];

    /**
     * Relación: La estructura pertenece a una cita.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'id_appointment_structureappointment', 'id_appointment');
    }

    /**
     * Relación: El usuario (empleado/doctor) que agenda.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_structureappointment', 'id_user');
    }

    /**
     * Relación: El paciente al que se asigna la cita.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient_structureappointment', 'id_patient');
    }

    /**
     * Relación: El horario de la cita.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'id_schedule_structureappointment', 'id_schedule');
    }

    /**
     * Relación: El día de la semana para la cita.
     */
    public function week()
    {
        return $this->belongsTo(Week::class, 'id_week_structureappointment', 'id_week');
    }
}
