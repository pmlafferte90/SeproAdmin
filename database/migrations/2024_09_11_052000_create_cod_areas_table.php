<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodAreasTable extends Migration
{
    public function up()
    {
        Schema::create('cod_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_area')->constrained('areas');
            $table->string('nombre', 5);
            $table->integer('estado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cod_areas');
    }
}

