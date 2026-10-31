<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlExtintorCli extends Model
{
    protected $table = 'control_extintor_cli';

    protected $fillable = [
        'id_cliente_localidad', 
        'id_subarea_empresa', 
        'fecha_revision',
        'codigo',
        'clase',
        'marca',
        'peso',
        'unidad',
        'mango',
        'mano',
        'seguro',
        'cilindro',
        'fecha_recarga',
        'fecha_prox_recarga',
        'fecha_fabricacion',
        'fecha_prueba_hidrostatica',
        'area_seguridad',
        'instructivo_uso',
        'instructivo_clasificacion',
        'flecha_ubicacion',
        'silueta',
        'hidrante',
        'dimension_vidrio',
        'rectangulo_instalacion',
        'novedad_marbete',
        'observaciones',
        'imagen',
        'id_usuario',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion',
        'id_usuario_auditor'  

    ];
    public $timestamps = false; // Desactiva las marcas de tiempo


    public function subAreaEmpresa()
    {
        return $this->belongsTo(AreaEmpresa::class, 'id_subarea_empresa');
    }


    public function observacionesNomenclatura()
    {
        return $this->belongsToMany(Nomenclatura::class, 'control_extintor_observaciones', 'control_extintor_id', 'nomenclatura_id');
    }

}
