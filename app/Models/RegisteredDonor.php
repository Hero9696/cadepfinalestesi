<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredDonor extends Model
{
    use HasFactory;

    protected $table = 'registereddonors';
    protected $primaryKey = 'id_registereddonor';
    public $incrementing = false; // El campo PK no es autoincremental
    protected $keyType = 'string'; // El campo PK es string (ej: R1, R2)
    public $timestamps = false;
    const CREATED_AT = 'createdate_registereddonor';
    const UPDATED_AT = 'updatedate_registereddonor';

    protected $fillable = [
        'id_registereddonor',
        'email_registereddonor',
        'username_registereddonor',
        'password_registereddonor',
        'title_registereddonor',
        'firstname_registereddonor',
        'lastname_registereddonor',
        'country_registereddonor',
        'zipcode_registereddonor',
        'state_registereddonor',
        'address_registereddonor',
        'unit_registereddonor',
        'city_registereddonor',
        'phone_registereddonor',
        'mobile_registereddonor',
        'idcreate_user_registereddonor',
        'idupdater_user_registereddonor',
    ];

    /**
     * Oculta la contraseña.
     */
    protected $hidden = [
        'password_registereddonor',
    ];

    /**
     * Relación: Un donador registrado ha realizado muchas donaciones.
     * Se usa un scope para filtrar el tipo de donador en la tabla 'donations'.
     */
    public function donations()
    {
        return $this->hasMany(Donation::class, 'id_donor_donation', 'id_registereddonor')
                    ->where('donor_type_donation', 'R'); // R = Registrado
    }

    /**
     * Relación: Usuario creador del registro.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'idcreate_user_registereddonor', 'id_user');
    }
}
