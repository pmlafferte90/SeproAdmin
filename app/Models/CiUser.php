<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class CiUser extends Authenticatable
{
    use HasRoles;
    protected $table = 'ci_users';

    protected $fillable = [
        'id',
        'id_cliente',
        'usuario',
        'password',
        'rol',
        'estado',
        'nombre',
        'apellido',
        'email',
        'fecha_creacion',
        'fecha_fin',
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo


    // Relación con el modelo Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function representante()
    {
        return $this->hasOne(Representante::class, 'id_usuario');
    }

}
