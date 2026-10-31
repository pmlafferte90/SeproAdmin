<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    protected $table = 'representante';
    protected $primaryKey = 'id';
    public $timestamps = false; // Desactiva las marcas de tiempo
    protected $fillable = [
        'id_cliente',
        'nombre',
        'apellido',
        'identificacion',
        'telefono',
        'correo',
        'id_usuario',                                                                              
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'id_usuario_auditor',
        'ver_todas_localidades'
    ];
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function localidades()
    {
        return $this->belongsToMany(ClienteLocalidad::class, 'representante_localidad', 'id_representante', 'id_localidad');
    }

}

