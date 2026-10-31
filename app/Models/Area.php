<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $fillable = [
        'nombre', 
        'estado', 
        'fecha_creacion', 
        'id_usuario'                                                                                  
    ];
    
    public $timestamps = false; // Desactiva las marcas de tiempo

    public function subAreas()
    {
        return $this->hasMany(SubArea::class, 'id_area');
    }

    public function codAreas()
    {
        return $this->hasMany(CodArea::class, 'id_area');
    }
}
