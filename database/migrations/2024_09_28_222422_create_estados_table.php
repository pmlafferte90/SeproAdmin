<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('estados', function (Blueprint $table) {
        $table->integer('id')->primary();         
        $table->string('descripcion',25); // Campo descripcion de tipo string
    });
     // Insertar los estados
     DB::table('estados')->insert([
        ['id' => 0, 'descripcion' => 'Inactivo'],
        ['id' => 1, 'descripcion' => 'Activo'],
        ['id' => 2, 'descripcion' => 'Pendiente de Aprobacion'],
        ['id' => 3, 'descripcion' => 'Pendiente de Anulacion']
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
