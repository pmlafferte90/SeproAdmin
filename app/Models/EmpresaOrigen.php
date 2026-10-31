<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaOrigen extends Model
{
    use HasFactory;

    // Definir la tabla asociada (opcional, Laravel lo hace automáticamente)
    protected $table = 'empresa_origen';

    // Definir los campos que son asignables
    protected $fillable = ['nombre', 'estado'];
    public $timestamps = false; // Desactiva las marcas de tiempo

}
