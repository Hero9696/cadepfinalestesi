// app/Models/Donation.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $table = 'donations';
    protected $primaryKey = 'id_donation';
    // Sobreescribir las columnas de timestamps
    const CREATED_AT = 'createdate_donation';
    const UPDATED_AT = 'updatedate_donation';

    protected $fillable = [
        'donor_type_donation',
        'id_donor_donation',
        'amount_donation',
        'currency_donation',
        'paymentmethod_donation',
        'transactionid_donation',
        'notes_donation',
        'id_patient_donations',
        'idcreate_user_donation',
        'idupdater_user_donation',
    ];

    protected $casts = [
        'amount_donation' => 'float',
    ];

    /**
     * Relación: La donación está asociada a un paciente (opcional).
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'id_patient_donations', 'id_patient');
    }

    /**
     * Relación polimórfica manual: El donante puede ser un Donor (S) o RegisteredDonor (R).
     */
    public function donor()
    {
        if ($this->donor_type_donation === 'S') {
            return $this->belongsTo(Donor::class, 'id_donor_donation', 'id_donor');
        } elseif ($this->donor_type_donation === 'R') {
            return $this->belongsTo(RegisteredDonor::class, 'id_donor_donation', 'id_registereddonor');
        }
        return null;
    }

    // Nota: Debido a la estructura, una relación `morphTo` nativa de Laravel no aplica directamente.
}
