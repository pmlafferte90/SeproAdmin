<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteLocalidad extends Model
{
    protected $table = 'cliente_localidad';
    protected $primaryKey = 'id';
    public $timestamps = false; // Desactiva las marcas de tiempo
    protected $fillable = [
        'id_cliente',
        'nombre',
        'direccion',
        'ubicacion',
        'id_usuario',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'id_usuario_auditor'                                                                                     
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }


    public function representantes()
    {
        return $this->belongsToMany(Representante::class, 'representante_localidad', 'id_localidad', 'id_representante');
    }
}
