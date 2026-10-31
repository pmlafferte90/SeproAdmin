<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomenclatura extends Model
{
    protected $table = 'nomenclatura';
    protected $fillable = [
        'descripcion', 'id_tipo', 'abreviatura', 'estado'                                                                                  
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo
    public function tipoNomenclatura()
    {
        return $this->belongsTo(TipoNomenclatura::class, 'id_tipo', 'id');
    }
}
