<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmenuTable extends Migration
{
    public function up()
    {
        Schema::create('submenu', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('id_menu')->constrained()->onDelete('cascade'); // Cambiado a id_menu
            $table->string('nombre', 50);
            $table->string('ruta', 100);
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
        Schema::dropIfExists('submenu');
    }
}
