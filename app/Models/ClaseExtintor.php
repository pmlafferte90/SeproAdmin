<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaseExtintor extends Model
{
    protected $table = 'clase_extintor';
    protected $fillable = [
        'clase',
        'estado',
        'id_usuario',
        'fecha_creacion'                                                                                  
    ];
    
    public $timestamps = false; // Desactiva las marcas de tiempo
}
