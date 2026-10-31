<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteLocalidadTable extends Migration
{
    public function up()
    {
        Schema::create('cliente_localidad', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_cliente')->constrained('cliente');
            $table->string('nombre', 150);
            $table->string('direccion', 150);
            $table->string('ubicacion', 250)->nullable();

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
        Schema::dropIfExists('clientes');
    }
}
