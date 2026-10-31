<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolSubmenuTable extends Migration
{
    public function up()
    {
        Schema::create('rol_submenu', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('id_rol')->constrained('roles')->onDelete('cascade');
            $table->foreignId('id_submenu')->constrained('submenus')->onDelete('cascade');
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
        Schema::dropIfExists('rol_submenu');
    }
}
