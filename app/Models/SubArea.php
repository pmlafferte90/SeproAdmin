<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubArea extends Model
{
    protected $table = 'sub_area';

    protected $fillable = [
        'id_area', 
        'nombre', 
        'codigo',
        'clase',
        'marca',
        'peso',
        'unidad',
        'estado',
        'fecha_creacion',
        'id_usuario'                                                                                   
    ];
    
    public $timestamps = false; // Desactiva las marcas de tiempo


    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function areasEmpresa()
    {
        return $this->hasMany(AreaEmpresa::class, 'id_subarea');
    }
}
