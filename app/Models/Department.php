<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'departments';

    // Clave primaria
    protected $primaryKey = 'id_department';

    // Indica que la clave primaria no es autoincremental
    public $incrementing = false;

    // Tipo de dato de la clave primaria
    protected $keyType = 'int';

    // Indica si el modelo debe ser timestamped (created_at y updated_at)
    public $timestamps = false;

    // Columnas que pueden ser asignadas masivamente (Mass Assignment)
    protected $fillable = [
        'id_department',
        'name_department',
    ];

    /**
     * Relación: Un departamento tiene muchos municipios.
     */
    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'id_department_municipality', 'id_department');
    }

    /**
     * Relación: Un departamento puede ser el departamento de nacimiento de muchos pacientes.
     */
    public function birthPatients()
    {
        return $this->hasMany(Patient::class, 'idbirth_department_patient', 'id_department');
    }

    /**
     * Relación: Un departamento es la ubicación de muchos pacientes.
     */
    public function locationPatients()
    {
        return $this->hasMany(Patient::class, 'id_department_patient', 'id_department');
    }
}
