<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    protected $primaryKey = 'id_area';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_area';
    const UPDATED_AT = 'updatedate_area';

    protected $fillable = [
        'name_area',
        'description_area',
        'duration_area',
        'id_state_area',
        'idcreate_user_area',
        'idupdater_user_area',
    ];

    /**
     * Relación: El área tiene un estado.
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_area', 'id_state');
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_area', 'id_user');
    }

    /**
     * Relación: Usuario actualizador del registro.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'idupdater_user_area', 'id_user');
    }

    /**
     * Relación: Un área tiene muchos empleados asignados.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_area_employee', 'id_area');
    }

    /**
     * Relación N:M: Un área puede estar asignada a muchos pacientes.
     */
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patientareas', 'id_area_patientarea', 'id_patient_patientarea')
                    ->withPivot(['id_patientarea', 'createdate_patientarea', 'updatedate_patientarea'])
                    ->using(PatientArea::class); // Si creas un modelo para la tabla pivote
    }
}
