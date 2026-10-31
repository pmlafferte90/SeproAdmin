<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlExtintorCliTable extends Migration
{
    public function up()
    {
        Schema::create('control_extintor_cli', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('id_cliente_localidad')->constrained('cliente_localidad');
            $table->foreignId('id_subarea_empresa')->constrained('areas_empresa');
            $table->foreignId('id_clase_extintor')->constrained('clase_extintor');
            $table->integer('id_marca_extintor')->constrained('marca_extintor');
            $table->integer('id_tamanio_extintor')->constrained('tamano_extintor');
            $table->integer('mango');
            $table->integer('mano');
            $table->integer('seguro');
            $table->integer('cilindro');
            $table->date('fecha_recarga');
            $table->date('fecha_prox_recarga');
            $table->integer('area_seguridad');
            $table->integer('instructivo_uso');
            $table->integer('rectangulo_instalacion');
            $table->integer('novedad_marbete');
            $table->string('observaciones', 300);
            $table->longText('imagen_observacion')->nullable();

            /* Auditoria */
            $table->integer("id_usuario");
            $table->integer('estado');
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_actualizacion')->nullable();
            $table->integer("id_usuario_auditor")->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('control_extintor_cli');
    }
}

