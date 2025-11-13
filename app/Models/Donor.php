<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $table = 'donors';
    protected $primaryKey = 'id_donor';
    public $incrementing = false; // El campo PK no es autoincremental
    protected $keyType = 'string'; // El campo PK es string (ej: S1, S2)
    public $timestamps = false; // Usamos solo la columna personalizada 'createdate_donor'
    const UPDATED_AT = 'updatedate_donor'; // Para que Laravel sepa qué columna actualizar

    protected $fillable = [
        'id_donor', // Generalmente se excluye si es AI, pero aquí es requerido por el trigger
        'email_donor',
        'title_donor',
        'firstname_donor',
        'lastname_donor',
        'country_donor',
        'zipcode_donor',
        'state_donor',
        'address_donor',
        'unit_donor',
        'city_donor',
        'phone_donor',
        'mobile_donor',
        'idupdater_user_donor',
    ];

    /**
     * Relación: Un donador no registrado ha realizado muchas donaciones.
     * Se usa un scope para filtrar el tipo de donador en la tabla 'donations'.
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'id_donor_donation', 'id_donor')
                    ->where('donor_type_donation', 'S'); // S = Simple/No registrado
    }
}
