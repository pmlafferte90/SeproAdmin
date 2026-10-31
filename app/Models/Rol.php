<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'ci_rol';

    protected $fillable = [
        'rol', 
        'estado',
        'fecha_creacion',
        'id_usuario'                                                                                   
    ];
    
    public $timestamps = false; // Desactiva las marcas de tiempo

}
