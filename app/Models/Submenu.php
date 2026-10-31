<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    use HasFactory;

    protected $table = 'submenu';
    
    protected $fillable = [
        'id_menu',
        'nombre',
        'ruta',
        'estado',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu'); // Cambiado a id_menu
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_submenu', 'id_submenu', 'id_rol');
    }
}
