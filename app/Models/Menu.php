<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    
    protected $fillable = [
        'nombre',
        'estado',
    ];

    public function submenus()
    {
        return $this->hasMany(Submenu::class, 'id_menu'); // Especificar la clave foránea
    }
}
