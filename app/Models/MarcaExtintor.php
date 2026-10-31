<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcaExtintor extends Model
{
    protected $table = 'marca_extintor';
    protected $fillable = [
        'marca','estado'                                                                                  
    ];
    public $timestamps = false; // Desactiva las marcas de tiempo
}
