<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCiUsersTable extends Migration
{
    public function up()
    {
        Schema::create('ci_users', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('id_cliente')->constrained('clientes'); // Crea una clave foránea a la tabla 'clientes'.
            $table->string('usuario', 25);
            $table->string('password', 255);
            $table->integer('rol');
            $table->date('fecha_fin')->nullable(); // Permite nulo en 'fecha_fin'
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
        Schema::dropIfExists('ci_users');
    }
}
