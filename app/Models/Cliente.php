<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id';
    public $timestamps = false; // Desactiva las marcas de tiempo
    protected $fillable = [
        'razon_social',
        'ruc',
        'telefono',
        'correo',
        'emp_origen',
        'id_usuario',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'id_usuario_auditor'
    ];

    public function ciUsers()
    {
        return $this->hasMany(CiUser::class, 'id_cliente');
    }

    public function localidades()
    {
        return $this->hasMany(ClienteLocalidad::class, 'id_cliente');
    }
}
