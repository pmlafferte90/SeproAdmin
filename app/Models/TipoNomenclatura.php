<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoNomenclatura extends Model
{
    protected $table = 'tipo';
    protected $fillable = [
        'descripcion','estado'                                                                                  
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo
    public function nomenclaturas()
    {
        return $this->hasMany(Nomenclatura::class, 'id_tipo', 'id');
    }
}
