<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $table = 'tokens';
    protected $primaryKey = 'id_token';
    public $timestamps = false; // Manejamos los timestamps manualmente

    protected $fillable = [
        'id_user_token',
        'token_token',
        'created_token',
        'expires_token',
        'active_token',
        'ip_token',
        'idcreate_user_token',
        'idupdater_user_token',
    ];

    protected $casts = [
        'created_token' => 'datetime',
        'expires_token' => 'datetime',
        'active_token' => 'boolean',
    ];

    /**
     * Relación: El token pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_token', 'id_user');
    }
}
