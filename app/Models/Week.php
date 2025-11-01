<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;

    protected $table = 'weeks';
    protected $primaryKey = 'id_week';
    public $timestamps = false; // Sin created_at ni updated_at

    protected $fillable = [
        'name_week',
    ];

    /**
     * Relación: Un día de la semana puede tener muchas estructuras de citas asociadas.
     */
    public function structures()
    {
        return $this->hasMany(StructureAppointment::class, 'id_week_structureappointment', 'id_week');
    }
}
