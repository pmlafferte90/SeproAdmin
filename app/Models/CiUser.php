<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CiUser extends Authenticatable
{
    protected $table = 'ci_users';

    protected $fillable = [
        'id',
        'id_cliente',
        'usuario',
        'password',
        'rol',
        'estado',
        'fecha_creacion',
        'fecha_fin',
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo


    // Relación con el modelo Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
    public function roles()
    {
        return $this->belongsToMany(Rol::class);
    }
}
