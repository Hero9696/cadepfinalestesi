<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $table = 'municipalities';
    protected $primaryKey = 'id_municipality';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_municipality',
        'id_department_municipality',
        'name_municipality',
    ];

    /**
     * Relación: Un municipio pertenece a un departamento.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department_municipality', 'id_department');
    }

    /**
     * Relación: Un municipio puede ser el municipio de nacimiento de muchos pacientes.
     */
    public function birthPatients()
    {
        return $this->hasMany(Patient::class, 'idbirth_municipality_patient', 'id_municipality');
    }

    /**
     * Relación: Un municipio es la ubicación de muchos pacientes.
     */
    public function locationPatients()
    {
        return $this->hasMany(Patient::class, 'id_municipality_patient', 'id_municipality');
    }
}
