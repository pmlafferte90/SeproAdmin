<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol_Submenu extends Model
{
    use HasFactory;

    protected $table = 'rol_submenu';
    
    protected $fillable = [
        'id_rol',
        'id_submenu',
        'estado',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }

    public function submenu()
    {
        return $this->belongsTo(Submenu::class, 'id_submenu');
    }
}
