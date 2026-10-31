<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TamanoExtintor extends Model
{
    protected $table = 'tamano_extintor';
    protected $fillable = [
        'tamanio','estado'                                                                                  
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo

}
