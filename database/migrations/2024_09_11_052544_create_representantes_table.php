<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentantesTable extends Migration
{
    public function up()
    {
        Schema::create('representante', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cliente')->constrained('cliente');
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('identificacion', 13);
            $table->string('telefono', 15);
            $table->string('correo', 25);
        
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
        Schema::dropIfExists('representantes');
    }
}
