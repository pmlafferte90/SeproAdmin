<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaseExtintorsTable extends Migration
{
    public function up()
    {
        Schema::create('clase_extintors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clase', 10)->unique();

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
        Schema::dropIfExists('clase_extintors');
    }
}

