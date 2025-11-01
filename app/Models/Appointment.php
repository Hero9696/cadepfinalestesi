// app/Models/Appointment.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';
    protected $primaryKey = 'id_appointment';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_appointment';
    const UPDATED_AT = 'updatedate_appointment';

    protected $fillable = [
        'id_employee_appointment',
        'date_appointment',
        'id_state_appointment',
        'idcreate_user_appointment',
        'idupdater_user_appointment',
    ];

    /**
     * Relación: La cita es asignada a un empleado.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_employee_appointment', 'id_employee');
    }

    /**
     * Relación: La cita tiene un estado (ej: pendiente, realizada, cancelada).
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_appointment', 'id_state');
    }

    /**
     * Relación: Una cita tiene un reporte de cita (1:1 o 1:0..1).
     */
    public function report()
    {
        return $this->hasOne(AppointmentReport::class, 'id_appointment_appointmentreport', 'id_appointment');
    }

    /**
     * Relación: La cita tiene una estructura de agendamiento asociada.
     */
    public function structure()
    {
        return $this->hasOne(StructureAppointment::class, 'id_appointment_structureappointment', 'id_appointment');
    }
}
