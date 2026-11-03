<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('representante_emails', function (Blueprint $table) {
        $table->id();
        $table->foreignId('representante_id')
              ->constrained('representante')
              ->cascadeOnDelete();
        $table->string('email', 254);
        $table->boolean('principal')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representante_emails');
    }
};
