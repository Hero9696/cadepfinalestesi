<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    // Sobreescribe los timestamps de Laravel
    const CREATED_AT = 'createdate_user';
    const UPDATED_AT = 'updatedate_user';

    protected $fillable = [
        'name',
        'email', // Si agregaste email a tu tabla
        'password',
        'id_role_user',
        'id_state_user',
        'idcreate_user_user',
        'idupdater_user_user',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // Usa 'password' ya que renombraste la columna
            'password' => 'hashed',
            'email_verified_at' => 'datetime', // Si usas verificación de email
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // --- Relaciones ---

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role_user', 'id_role');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'id_state_user', 'id_state');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id_user_employee', 'id_user');
    }
}
