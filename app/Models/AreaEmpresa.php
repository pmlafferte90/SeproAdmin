<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaEmpresa extends Model
{
    protected $table = 'areas_empresa';

    protected $fillable = [
        'id_cliente_localidad', 
        'id_subarea', 
        'estado',
        'fecha_creacion',
        'id_usuario',
        'fecha_actualizacion',
        'id_usuario_auditor'                                                                                   
    ];

    public $timestamps = false; // Desactiva las marcas de tiempo

    public function subArea()
    {
        return $this->belongsTo(SubArea::class, 'id_subarea');
    }
     // Nueva relación con Cliente
     public function localidad()
     {
        return $this->belongsTo(ClienteLocalidad::class, 'id_cliente_localidad', 'id');
     }
}
