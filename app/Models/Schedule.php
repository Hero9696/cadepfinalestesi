// app/Models/Schedule.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'id_schedule';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_schedule';
    const UPDATED_AT = 'updatedate_schedule';

    protected $fillable = [
        'hour_schedule',
        'idcreate_user_schedule',
        'idupdater_user_schedule',
    ];

    /**
     * Relación: Un horario puede tener muchas estructuras de citas asociadas.
     */
    public function structures()
    {
        return $this->hasMany(StructureAppointment::class, 'id_schedule_structureappointment', 'id_schedule');
    }
}
