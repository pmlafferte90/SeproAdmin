<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaEmpresaTable extends Migration
{
    public function up()
    {
        Schema::create('areas_empresa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cliente_localidad')->constrained('cliente_localidad');
            $table->foreignId('id_subarea')->constrained('sub_area');

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
        Schema::dropIfExists('areas_empresa');
    }
}

